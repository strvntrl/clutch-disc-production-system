<?php

ob_start();
include(__DIR__ . "/../../../../database.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

const DEFAULT_TARGET_CYCLE = 82;
const DEFAULT_CVT = 1;
const BREAKDOWN_GAP_SEC = 600;

$date      = isset($_GET['date'])  ? preg_replace('/[^0-9\-]/', '', $_GET['date']) : date('Y-m-d');
$shiftName = isset($_GET['shift']) ? preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['shift']) : 'Shift-1';
$shiftCode = strtoupper($shiftName);

$shiftRow = db_select(
    "SELECT shift_id, shift_code, start_time, end_time, is_overnight
     FROM shift WHERE shift_code = :code LIMIT 1",
    ['code' => $shiftCode]
);
if (empty($shiftRow)) {
    ob_clean();
    die(json_encode(["status" => false, "error" => "Shift tidak dikenali: $shiftCode"]));
}
$shift = $shiftRow[0];
$shiftId = $shift['shift_id'];

$startDT = new DateTime("$date {$shift['start_time']}");
$endDT   = new DateTime("$date {$shift['end_time']}");
if ($shift['is_overnight'] === 't' || $shift['is_overnight'] === true) {
    $endDT->modify('+1 day');
}
$start_time = $startDT->format('Y-m-d H:i:s');
$end_time   = $endDT->format('Y-m-d H:i:s');

$now = new DateTime();
$is_live = ($now >= $startDT && $now < $endDT);

// ------------------------------------------------------------------
// 1. SYNC: agregasi machine_process_log -> upsert press_station_cycle
// ------------------------------------------------------------------
$syncRows = db_select(
    "SELECT l.machine_id, m.machine_code, l.part_id, l.operator_id,
            MIN(l.event_time) AS first_dt, MAX(l.event_time) AS last_dt,
            COUNT(*) AS jml_cycle
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'HOTPRESS'
     WHERE l.event_time >= :start AND l.event_time < :end
     GROUP BY l.machine_id, m.machine_code, l.part_id, l.operator_id",
    ['start' => $start_time, 'end' => $end_time]
);

foreach ($syncRows as $row) {
    $machineId = $row['machine_id'];
    $partId    = $row['part_id'];
    $operatorId = $row['operator_id'];
    $cycle     = (int) $row['jml_cycle'];
    $lastDt    = $row['last_dt'];

    $existing = db_select(
        "SELECT cycle_id, target_cycle
         FROM press_station_cycle
         WHERE machine_id = :mid AND shift_id = :sid
           AND part_id IS NOT DISTINCT FROM :pid
           AND operator_id IS NOT DISTINCT FROM :oid
           AND start_time >= :start AND start_time < :end
         LIMIT 1",
        ['mid' => $machineId, 'sid' => $shiftId, 'pid' => $partId, 'oid' => $operatorId, 'start' => $start_time, 'end' => $end_time]
    );

    if (!empty($existing)) {
        $tc = (int) ($existing[0]['target_cycle'] ?: DEFAULT_TARGET_CYCLE);
        $pcs = $cycle * DEFAULT_CVT;
        $params = ['cycle' => $cycle, 'pcs' => $pcs, 'id' => $existing[0]['cycle_id']];
        $lastSql = '';
        if ($now < $endDT) {
            $lastSql = ', last_time = :last';
            $params['last'] = $lastDt;
        }
        db_execute(
            "UPDATE press_station_cycle
             SET actual_cycle = :cycle, actual_pcs = :pcs $lastSql
             WHERE cycle_id = :id",
            $params
        );
    } else {
        $maxShafRow = db_select(
            "SELECT MAX(station_no) AS max_shaf
             FROM press_station_cycle
             WHERE machine_id = :mid AND shift_id = :sid
               AND start_time >= :start AND start_time < :end",
            ['mid' => $machineId, 'sid' => $shiftId, 'start' => $start_time, 'end' => $end_time]
        );
        $maxShaf = (int) ($maxShafRow[0]['max_shaf'] ?? 0);
        $nextShaf = ($maxShaf >= 1 && $maxShaf < 4) ? $maxShaf + 1 : 1;

        $tc = DEFAULT_TARGET_CYCLE;
        $pcs = $cycle * DEFAULT_CVT;
        db_execute(
            "INSERT INTO press_station_cycle
             (machine_id, station_no, part_id, operator_id, shift_id, start_time, last_time,
              target_cycle, actual_cycle, target_pcs, actual_pcs, status)
             VALUES (:mid, :station, :pid, :oid, :sid, :start, :last, :tc, :cycle, :tpcs, :pcs, 'RUNNING')",
            [
                'mid' => $machineId, 'station' => $nextShaf, 'pid' => $partId, 'oid' => $operatorId,
                'sid' => $shiftId, 'start' => $row['first_dt'], 'last' => $lastDt,
                'tc' => $tc, 'cycle' => $cycle, 'tpcs' => $tc * DEFAULT_CVT, 'pcs' => $pcs,
            ]
        );
    }
}

// ------------------------------------------------------------------
// 2. AMBIL MESIN HOTPRESS
// ------------------------------------------------------------------
$machines = db_select(
    "SELECT m.machine_id, m.machine_code
     FROM machine m
     JOIN process_stage s ON s.stage_id = m.stage_id
     WHERE s.stage_code = 'HOTPRESS'
     ORDER BY m.machine_code"
);

// ------------------------------------------------------------------
// 3. AMBIL LOG (buat graph & deteksi breakdown) + DETAIL STASIUN
// ------------------------------------------------------------------
$logRows = db_select(
    "SELECT l.machine_id, l.event_time, l.cycle_time_sec
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'HOTPRESS'
     WHERE l.event_time >= :start AND l.event_time < :end
     ORDER BY l.machine_id, l.event_time",
    ['start' => $start_time, 'end' => $end_time]
);
$logsByMachine = [];
foreach ($logRows as $r) $logsByMachine[$r['machine_id']][] = $r;

$stationRows = db_select(
    "SELECT psc.machine_id, psc.station_no, psc.target_cycle, psc.actual_cycle,
            psc.target_pcs, psc.actual_pcs, psc.start_time, psc.last_time,
            p.part_number, o.operator_name
     FROM press_station_cycle psc
     LEFT JOIN part p ON p.part_id = psc.part_id
     LEFT JOIN operator o ON o.operator_id = psc.operator_id
     WHERE psc.shift_id = :sid AND psc.start_time >= :start AND psc.start_time < :end",
    ['sid' => $shiftId, 'start' => $start_time, 'end' => $end_time]
);
$stationsByMachine = [];
foreach ($stationRows as $r) $stationsByMachine[$r['machine_id']][(int) $r['station_no']] = $r;

// ------------------------------------------------------------------
// 4. BANGUN DETAIL PER MESIN
// ------------------------------------------------------------------
$shift_outputs = [];
$shift_cycles  = [];
$running = 0; $breakdown = 0; $off = 0;
$machine_details = [];
$global_start = null; $global_last = null;
$total_kpi_actual_cycle = 0;
$total_kpi_plan = 0;

$limitTs = $is_live ? time() : $endDT->getTimestamp();

foreach ($machines as $m) {
    $mid = $m['machine_id'];
    $mc  = $m['machine_code'];
    $logs = $logsByMachine[$mid] ?? [];
    $stations = $stationsByMachine[$mid] ?? [];

    $totalLogRows = count($logs);
    $has_breakdown = false;
    $bd_history = [];
    $graph_data = [];
    $total_mc_time = 0;
    $total_cycle_time = 0;

    if ($totalLogRows > 0) {
        $firstTs = strtotime($logs[0]['event_time']);
        $lastTs  = strtotime($logs[$totalLogRows - 1]['event_time']);
        if ($global_start === null || $firstTs < $global_start) $global_start = $firstTs;
        if ($global_last === null || $lastTs > $global_last) $global_last = $lastTs;

        $prevTs = $startDT->getTimestamp();
        foreach ($logs as $idx => $rec) {
            $ts = strtotime($rec['event_time']);
            if (($ts - $prevTs) > BREAKDOWN_GAP_SEC) {
                $has_breakdown = true;
                $bd_history[] = date('H:i', $prevTs) . ' - ' . date('H:i', $ts);
            }
            $machineTime = ($idx === 0) ? (float) $rec['cycle_time_sec'] : ($ts - $prevTs);
            $cycleTime   = (float) $rec['cycle_time_sec'];
            $total_mc_time += $machineTime;
            $total_cycle_time += $cycleTime;
            $graph_data[] = ['pcs' => (string) ($idx + 1), 'machine_time' => round($machineTime, 1), 'cycle_time' => round($cycleTime, 1)];
            $prevTs = $ts;
        }
        if (($limitTs - $prevTs) > BREAKDOWN_GAP_SEC) {
            $has_breakdown = true;
            $bd_history[] = date('H:i', $prevTs) . ' - ' . date('H:i', $limitTs);
        }
    }

    $avg_mc_time = $totalLogRows > 0 ? round($total_mc_time / $totalLogRows, 1) : 0;
    $avg_cycle_time = $totalLogRows > 0 ? round($total_cycle_time / $totalLogRows, 1) : 0;

    $shaft_data = [];
    $current_mc_actual_pcs = 0;
    for ($s = 1; $s <= 4; $s++) {
        $partno = '-'; $operator = '-'; $target_pcs = 0; $actual_pcs = 0; $ach = 0;
        $s_start = '-'; $s_last = '-';

        if (isset($stations[$s])) {
            $row = $stations[$s];
            $partno = $row['part_number'] ?: '-';
            $operator = $row['operator_name'] ?: '-';
            $target_pcs = (int) $row['target_pcs'];
            $actual_pcs = (int) $row['actual_pcs'];
            $s_start = $row['start_time'] ? date('H:i:s', strtotime($row['start_time'])) : '-';
            $s_last  = $row['last_time'] ? date('H:i:s', strtotime($row['last_time'])) : '-';
            if ($target_pcs > 0) {
                $ach = round(($actual_pcs / $target_pcs) * 100, 1);
                if ($ach > 100) $ach = 100;
            }
            $total_kpi_actual_cycle += (int) $row['actual_cycle'];
            $total_kpi_plan += (int) $row['target_cycle'];
        }

        $shaft_data[$s] = [
            'partno' => $partno, 'operator' => $operator, 'target' => $target_pcs,
            'actual_pcs' => $actual_pcs, 'ach' => $ach, 'start' => $s_start, 'last' => $s_last,
        ];
        $current_mc_actual_pcs += $actual_pcs;
    }

    if ($totalLogRows > 0 || count($stations) > 0) {
        $shift_outputs[$mc] = $current_mc_actual_pcs;
        $shift_cycles[$mc] = $avg_cycle_time;
    }

    $lastEventTs = $totalLogRows > 0 ? strtotime($logs[$totalLogRows - 1]['event_time']) : $startDT->getTimestamp();
    $is_currently_bd = (($limitTs - $lastEventTs) > BREAKDOWN_GAP_SEC);
    $is_currently_off = ($totalLogRows === 0 && count($stations) === 0);

    if ($is_currently_off) $off++;
    elseif ($is_live) {
        if ($is_currently_bd) $breakdown++; else $running++;
    } else {
        $running++;
        if ($has_breakdown) $breakdown++;
    }

    $mc_status = 'OFF';
    if (!$is_currently_off) {
        $mc_status = $is_live ? ($is_currently_bd ? 'RUNNING_BD' : 'RUNNING') : ($has_breakdown ? 'RUNNING_BD' : 'RUNNING');
    }

    $machine_details[] = [
        'mc' => $mc,
        'status' => $mc_status,
        'shafts' => $shaft_data,
        'bd_history' => $bd_history,
        'graph_data' => $graph_data,
        'avg_machine_time' => $avg_mc_time,
        'avg_cycle_time' => $avg_cycle_time,
    ];
}

usort($machine_details, fn($a, $b) => strcmp($a['mc'], $b['mc']));

$ach_rate = $total_kpi_plan > 0 ? round(($total_kpi_actual_cycle / $total_kpi_plan) * 100, 1) : 0;
if ($ach_rate > 100) $ach_rate = 100;

$high_out_val = !empty($shift_outputs) ? max($shift_outputs) : 0;
$nonZeroOutputs = array_filter($shift_outputs);
$low_out_val = !empty($nonZeroOutputs) ? min($nonZeroOutputs) : 0;
$nonZeroCycles = array_filter($shift_cycles);
$fast_cyc_val = !empty($nonZeroCycles) ? min($nonZeroCycles) : 0;
$slow_cyc_val = !empty($shift_cycles) ? max($shift_cycles) : 0;

$high_out_mc = []; $low_out_mc = []; $fast_cyc_mc = []; $slow_cyc_mc = [];
foreach ($shift_outputs as $mc => $v) {
    if ($v == $high_out_val && $v > 0) $high_out_mc[] = $mc;
    if ($v == $low_out_val && $v > 0) $low_out_mc[] = $mc;
}
foreach ($shift_cycles as $mc => $v) {
    if ($v == $fast_cyc_val && $v > 0) $fast_cyc_mc[] = $mc;
    if ($v == $slow_cyc_val && $v > 0) $slow_cyc_mc[] = $mc;
}

// ------------------------------------------------------------------
// 5. INSIGHT BULANAN 
// ------------------------------------------------------------------
$monthRows = db_select(
    "SELECT m.machine_code AS mc,
            (DATE(psc.start_time - INTERVAL '7 hours')) AS prod_date,
            sh.shift_code, SUM(psc.actual_pcs) AS actual
     FROM press_station_cycle psc
     JOIN machine m ON m.machine_id = psc.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'HOTPRESS'
     JOIN shift sh ON sh.shift_id = psc.shift_id
     WHERE date_trunc('month', psc.start_time) = date_trunc('month', :ref_date::date)
     GROUP BY m.machine_code, (DATE(psc.start_time - INTERVAL '7 hours')), sh.shift_code",
    ['ref_date' => $date]
);

$highest_month_record = ['mc' => '-', 'val' => 0, 'date' => '-', 'shift' => '-'];
$lowest_month_record  = ['mc' => '-', 'val' => 999999, 'date' => '-', 'shift' => '-'];
$total_actual_month = 0;
$shift_totals = []; $shift_days = [];

foreach ($monthRows as $rm) {
    $val = (int) $rm['actual'];
    $total_actual_month += $val;
    $dateFmt = date('d M Y', strtotime($rm['prod_date']));
    if ($val > $highest_month_record['val']) {
        $highest_month_record = ['mc' => $rm['mc'], 'val' => $val, 'date' => $dateFmt, 'shift' => $rm['shift_code']];
    }
    if ($val > 0 && $val < $lowest_month_record['val']) {
        $lowest_month_record = ['mc' => $rm['mc'], 'val' => $val, 'date' => $dateFmt, 'shift' => $rm['shift_code']];
    }
    $shift_totals[$rm['shift_code']] = ($shift_totals[$rm['shift_code']] ?? 0) + $val;
    $shift_days[$rm['shift_code']][$rm['prod_date']] = true;
}
if ($lowest_month_record['val'] == 999999) $lowest_month_record['val'] = 0;

$shift_averages = [];
foreach ($shift_totals as $sc => $total) {
    $days = count($shift_days[$sc] ?? []);
    if ($days > 0) $shift_averages[$sc] = round($total / $days);
}
$most_prod_shift = ['shift' => '-', 'avg' => 0];
$least_prod_shift = ['shift' => '-', 'avg' => 999999];
foreach ($shift_averages as $sc => $avg) {
    if ($avg > $most_prod_shift['avg']) $most_prod_shift = ['shift' => $sc, 'avg' => $avg];
    if ($avg < $least_prod_shift['avg']) $least_prod_shift = ['shift' => $sc, 'avg' => $avg];
}
if ($least_prod_shift['avg'] == 999999) $least_prod_shift['avg'] = 0;

// ------------------------------------------------------------------
// 6. RESPONSE
// ------------------------------------------------------------------
if (ob_get_length()) ob_clean();
echo json_encode([
    'punya_data' => ($total_kpi_actual_cycle > 0),
    'kpi' => [
        'plan' => $total_kpi_plan,
        'actual' => $total_kpi_actual_cycle,
        'gap' => max(0, $total_kpi_plan - $total_kpi_actual_cycle),
        'ach' => $ach_rate,
        'waktu_mulai' => $global_start ? date('H:i:s', $global_start) : '--:--:--',
        'waktu_selesai' => $global_last ? date('H:i:s', $global_last) : '--:--:--',
    ],
    'status' => ['run' => $running, 'bd' => $breakdown, 'off' => $off],
    'ticker_data' => [
        'high_out_shift' => ['mc' => implode(', ', $high_out_mc), 'val' => $high_out_val],
        'low_out_shift' => ['mc' => implode(', ', $low_out_mc), 'val' => $low_out_val],
        'fast_cyc_shift' => ['mc' => implode(', ', $fast_cyc_mc), 'val' => $fast_cyc_val],
        'slow_cyc_shift' => ['mc' => implode(', ', $slow_cyc_mc), 'val' => $slow_cyc_val],
        'highest_month_record' => $highest_month_record,
        'lowest_month_record' => $lowest_month_record,
        'most_prod_shift' => $most_prod_shift,
        'least_prod_shift' => $least_prod_shift,
        'total_month' => $total_actual_month,
    ],
    'machines' => $machine_details,
]);