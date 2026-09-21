<?php

header('Content-Type: application/json');
include(__DIR__ . "/../../../../database.php");
date_default_timezone_set('Asia/Jakarta');

const HOURLY_TARGET_PCS = 125;
const DEFAULT_CYCLE_SEC = 30;

// ------------------------------------------------------------------
// 1. INPUT + DATA SHIFT
// ------------------------------------------------------------------
$reqDate   = $_GET['date']  ?? date('Y-m-d');
$reqShift  = $_GET['shift'] ?? 'Shift-1';
$shiftCode = strtoupper($reqShift);

$shiftRow = db_select(
    "SELECT shift_id, shift_code, start_time, end_time, is_overnight
     FROM shift WHERE shift_code = :code LIMIT 1",
    ['code' => $shiftCode]
);
if (empty($shiftRow)) {
    http_response_code(400);
    echo json_encode(["error" => "Shift tidak dikenali: $shiftCode"]);
    exit;
}
$shift = $shiftRow[0];
$shiftId = $shift['shift_id'];

$shiftStart = new DateTime("$reqDate {$shift['start_time']}");
$shiftEnd   = new DateTime("$reqDate {$shift['end_time']}");
if ($shift['is_overnight'] === 't' || $shift['is_overnight'] === true) {
    $shiftEnd->modify('+1 day');
}
$startTimeStr = $shiftStart->format('Y-m-d H:i:s');
$endTimeStr   = $shiftEnd->format('Y-m-d H:i:s');

// ------------------------------------------------------------------
// 2. AMBIL SEMUA LOG PRODUKSI DRILLING DALAM RENTANG SHIFT
// ------------------------------------------------------------------
$logs = db_select(
    "SELECT l.machine_id, m.machine_code, l.event_time,
            p.part_id, p.part_number, p.standard_cycle_sec,
            o.operator_id, o.operator_name
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'DRILLING'
     LEFT JOIN part p ON p.part_id = l.part_id
     LEFT JOIN operator o ON o.operator_id = l.operator_id
     WHERE l.event_time >= :start AND l.event_time < :end
     ORDER BY m.machine_code, l.event_time",
    ['start' => $startTimeStr, 'end' => $endTimeStr]
);

$machines = array_column(db_select(
    "SELECT m.machine_code
     FROM machine m
     JOIN process_stage s ON s.stage_id = m.stage_id
     WHERE s.stage_code = 'DRILLING'
     ORDER BY m.machine_code"
), 'machine_code');

// ------------------------------------------------------------------
// 3. GRID (jam x mesin): target flat 125/jam, di-nol-kan kalau jam
// ------------------------------------------------------------------
$now = time();
$numBuckets = (int) ceil(($shiftEnd->getTimestamp() - $shiftStart->getTimestamp()) / 3600);

// hitung actual per (bucket,machine) dari $logs
$actualGrid = []; // [bucketIdx][machine_code] = count
foreach ($logs as $r) {
    $delta = strtotime($r['event_time']) - $shiftStart->getTimestamp();
    $idx = (int) floor($delta / 3600);
    if ($idx >= 0 && $idx < $numBuckets) {
        $actualGrid[$idx][$r['machine_code']] = ($actualGrid[$idx][$r['machine_code']] ?? 0) + 1;
    }
}

$chartData = [];
for ($i = 0; $i < $numBuckets; $i++) {
    $bucketStart = (clone $shiftStart)->modify("+{$i} hour");
    $jamLabel = $bucketStart->format('H:i');
    $target = ($bucketStart->getTimestamp() > $now) ? 0 : HOURLY_TARGET_PCS;
    foreach ($machines as $mc) {
        $chartData[] = [
            'jam'    => $jamLabel,
            'mc'     => $mc,
            'target' => $target,
            'actual' => $actualGrid[$i][$mc] ?? 0,
        ];
    }
}

// ------------------------------------------------------------------
// 4. KPI: actual = total event, target = dari production_plan
// ------------------------------------------------------------------
$totalPcs = count($logs);

$planRow = db_select(
    "SELECT COALESCE(SUM(target_qty), 0) AS total_target
     FROM production_plan
     WHERE status = 'ACTIVE' AND shift_id = :shift_id AND plan_date = :plan_date",
    ['shift_id' => $shiftId, 'plan_date' => $reqDate]
);
$kpiTarget = (int) ($planRow[0]['total_target'] ?? 0);
$kpiActual = $totalPcs;

// start_time_val / last_update dari log aktual
$startTimeVal = '--:--:--';
$lastUpdate   = '--:--:--';
if (!empty($logs)) {
    $times = array_map(fn($r) => strtotime($r['event_time']), $logs);
    $startTimeVal = date('H:i:s', min($times));
    $lastUpdate   = date('H:i:s', max($times));
}

// ------------------------------------------------------------------
// 5. TABLE DATA: kelompokkan per mesin -> per sesi (part_id + operator_id)
//    target = durasi sesi aktual / cycle time part (bukan durasi shift penuh)
// ------------------------------------------------------------------
$byMachine = [];
foreach ($machines as $mc) $byMachine[$mc] = [];
foreach ($logs as $r) $byMachine[$r['machine_code']][] = $r;

$tableData = [];
foreach ($byMachine as $mc => $rows) {
    if (empty($rows)) continue;

    $sessions = [];
    foreach ($rows as $r) {
        $key = ($r['part_id'] ?? '0') . '-' . ($r['operator_id'] ?? '0');
        if (!isset($sessions[$key])) {
            $sessions[$key] = [
                'part_number'        => $r['part_number'] ?? '-',
                'operator_name'      => $r['operator_name'] ?? '-',
                'standard_cycle_sec' => $r['standard_cycle_sec'] ?: DEFAULT_CYCLE_SEC,
                'events'             => [],
            ];
        }
        $sessions[$key]['events'][] = strtotime($r['event_time']);
    }

    foreach ($sessions as $s) {
        sort($s['events']);
        $firstT = $s['events'][0];
        $lastT  = end($s['events']);
        $cycleSec = max(1, (int) $s['standard_cycle_sec']);
        $sessionElapsed = $lastT - $firstT;
        $target = ($sessionElapsed < $cycleSec) ? 1 : (int) floor($sessionElapsed / $cycleSec);
        $actual = count($s['events']);

        $tableData[] = [
            'mc'     => $mc,
            'pn'     => $s['part_number'],
            'opr'    => $s['operator_name'],
            'start'  => date('H:i:s', $firstT),
            'last'   => date('H:i:s', $lastT),
            'target' => $target,
            'actual' => $actual,
        ];
    }
}

// ------------------------------------------------------------------
// 6. INSIGHT BULANAN
// ------------------------------------------------------------------
$monthRows = db_select(
    "SELECT m.machine_code, sh.shift_code, l.event_time,
            (DATE(l.event_time - INTERVAL '7 hours')) AS shift_date
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'DRILLING'
     JOIN shift sh ON sh.shift_id = l.shift_id
     WHERE date_trunc('month', l.event_time) = date_trunc('month', :ref_date::date)",
    ['ref_date' => $reqDate]
);

$monthlyData = null;
if (!empty($monthRows)) {
    $groups = [];
    foreach ($monthRows as $r) {
        $key = $r['machine_code'] . '|' . $r['shift_code'] . '|' . $r['shift_date'];
        $groups[$key] ??= ['mc' => $r['machine_code'], 'shift' => $r['shift_code'], 'date' => $r['shift_date'], 'count' => 0];
        $groups[$key]['count']++;
    }

    $topMc = null; $lowMc = null;
    foreach ($groups as $g) {
        if ($topMc === null || $g['count'] > $topMc['count']) $topMc = $g;
        if ($g['count'] > 0 && ($lowMc === null || $g['count'] < $lowMc['count'])) $lowMc = $g;
    }

    $perDate = [];
    foreach ($groups as $g) {
        $d = $g['date'];
        $perDate[$d] ??= ['s1' => 0, 's2' => 0, 's3' => 0, 'total' => 0];
        $slot = ['SHIFT-1' => 's1', 'SHIFT-2' => 's2', 'SHIFT-3' => 's3'][$g['shift']] ?? null;
        if ($slot) $perDate[$d][$slot] += $g['count'];
        $perDate[$d]['total'] += $g['count'];
    }
    $highCumDate = null; $lowCumDate = null;
    foreach ($perDate as $d => $v) {
        if ($highCumDate === null || $v['total'] > $perDate[$highCumDate]['total']) $highCumDate = $d;
        if ($v['total'] > 0 && ($lowCumDate === null || $v['total'] < $perDate[$lowCumDate]['total'])) $lowCumDate = $d;
    }

    $shiftTotals = ['SHIFT-1' => 0, 'SHIFT-2' => 0, 'SHIFT-3' => 0];
    $shiftDays   = ['SHIFT-1' => [], 'SHIFT-2' => [], 'SHIFT-3' => []];
    foreach ($groups as $g) {
        $shiftTotals[$g['shift']] += $g['count'];
        $shiftDays[$g['shift']][$g['date']] = true;
    }
    $shiftAvg = [];
    foreach ($shiftTotals as $sc => $total) {
        $days = count($shiftDays[$sc]);
        $shiftAvg[$sc] = $days > 0 ? round($total / $days) : 0;
    }
    arsort($shiftAvg);
    $shiftNames = array_keys($shiftAvg);
    $topShiftCode = $shiftNames[0] ?? 'SHIFT-1';
    $lowShiftCode = end($shiftNames) ?: 'SHIFT-1';

    $totalMonth = array_sum(array_column($groups, 'count'));

    $monthlyData = [
        'top_mc'    => ['mc' => $topMc['mc'], 'val' => $topMc['count'], 'date' => $topMc['date'], 'shift' => $topMc['shift']],
        'low_mc'    => ['mc' => $lowMc['mc'] ?? '-', 'val' => $lowMc['count'] ?? 0, 'date' => $lowMc['date'] ?? '-', 'shift' => $lowMc['shift'] ?? '-'],
        'high_cum'  => ['date' => $highCumDate, 'total' => $perDate[$highCumDate]['total'], 's1' => $perDate[$highCumDate]['s1'], 's2' => $perDate[$highCumDate]['s2'], 's3' => $perDate[$highCumDate]['s3']],
        'low_cum'   => ['date' => $lowCumDate ?? '-', 'total' => $lowCumDate ? $perDate[$lowCumDate]['total'] : 0, 's1' => $lowCumDate ? $perDate[$lowCumDate]['s1'] : 0, 's2' => $lowCumDate ? $perDate[$lowCumDate]['s2'] : 0, 's3' => $lowCumDate ? $perDate[$lowCumDate]['s3'] : 0],
        'top_shift' => ['name' => $topShiftCode, 'avg' => $shiftAvg[$topShiftCode] ?? 0],
        'low_shift' => ['name' => $lowShiftCode, 'avg' => $shiftAvg[$lowShiftCode] ?? 0],
        'total_month' => $totalMonth,
    ];
}

// ------------------------------------------------------------------
// 7. RESPONSE
// ------------------------------------------------------------------
echo json_encode([
    'date'            => $reqDate,
    'shift'           => $reqShift,
    'start_time_val'  => $startTimeVal,
    'last_update'     => $lastUpdate,
    'total_pcs'       => $totalPcs,
    'data'            => $chartData,
    'monthly_data'    => $monthlyData,
    'table_data'      => $tableData,
    'kpi_target'      => $kpiTarget,
    'kpi_actual'      => $kpiActual,
]);