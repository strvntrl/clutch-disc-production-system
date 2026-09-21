<?php

ob_start();
include(__DIR__ . "/../../../../database.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

const TARGET_PER_SIDE = 200;

$date      = isset($_GET['date'])  ? preg_replace('/[^0-9\-]/', '', $_GET['date']) : date('Y-m-d');
$shiftName = isset($_GET['shift']) ? preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['shift']) : 'Shift-1';
$shiftCode = strtoupper($shiftName);

$shiftRow = db_select(
    "SELECT shift_id, start_time, end_time, is_overnight
     FROM shift WHERE shift_code = :code LIMIT 1",
    ['code' => $shiftCode]
);
if (empty($shiftRow)) {
    ob_clean();
    die(json_encode(["status" => false, "error" => "Shift tidak dikenali: $shiftCode"]));
}
$shift = $shiftRow[0];

$startDT = new DateTime("$date {$shift['start_time']}");
$endDT   = new DateTime("$date {$shift['end_time']}");
if ($shift['is_overnight'] === 't' || $shift['is_overnight'] === true) {
    $endDT->modify('+1 day');
}
$start_time = $startDT->format('Y-m-d H:i:s');
$end_time   = $endDT->format('Y-m-d H:i:s');

// ------------------------------------------------------------------
// 1. SIAPKAN DAFTAR MESIN PREFORMING (default OFF, target 200/sisi)
// ------------------------------------------------------------------
$machineRows = db_select(
    "SELECT m.machine_id, m.machine_code
     FROM machine m
     JOIN process_stage s ON s.stage_id = m.stage_id
     WHERE s.stage_code = 'PREFORMING'
     ORDER BY m.machine_code"
);

$machines = [];
foreach ($machineRows as $m) {
    $machines[$m['machine_id']] = [
        'mc' => $m['machine_code'],
        'status' => 'OFF',
        'A' => ['target' => TARGET_PER_SIDE, 'actual' => 0, 'ach' => 0],
        'B' => ['target' => TARGET_PER_SIDE, 'actual' => 0, 'ach' => 0],
    ];
}

// ------------------------------------------------------------------
// 2. AGREGASI LOG PER MESIN + STATION_NO (SISI)
// ------------------------------------------------------------------
$rows = db_select(
    "SELECT l.machine_id, l.station_no,
            MIN(l.event_time) AS first_dt, MAX(l.event_time) AS last_dt,
            COUNT(*) AS actual
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'PREFORMING'
     WHERE l.event_time >= :start AND l.event_time < :end
     GROUP BY l.machine_id, l.station_no",
    ['start' => $start_time, 'end' => $end_time]
);

$total_actual = 0;
$total_plan = 0;
$global_start = null;
$global_last = null;
$highest_out = ['mc' => '-', 'val' => 0];

foreach ($rows as $r) {
    $mid = $r['machine_id'];
    $side = ((int) $r['station_no'] === 2) ? 'B' : 'A'; // default ke A kalau NULL/tidak dikenali
    if (!isset($machines[$mid])) continue;

    $machines[$mid]['status'] = 'RUNNING';
    $actual = (int) $r['actual'];
    $machines[$mid][$side]['actual'] = $actual;
    $machines[$mid][$side]['ach'] = round(($actual / TARGET_PER_SIDE) * 100, 1);

    $total_actual += $actual;
    $total_plan += TARGET_PER_SIDE;

    $fDt = strtotime($r['first_dt']);
    $lDt = strtotime($r['last_dt']);
    if ($global_start === null || $fDt < $global_start) $global_start = $fDt;
    if ($global_last === null || $lDt > $global_last) $global_last = $lDt;

    if ($actual > $highest_out['val']) {
        $highest_out = ['mc' => $machines[$mid]['mc'] . '-' . $side, 'val' => $actual];
    }
}

// ------------------------------------------------------------------
// 3. RINGKASAN
// ------------------------------------------------------------------
$running_mc = 0;
$off_mc = 0;
foreach ($machines as $m) {
    if ($m['status'] === 'RUNNING') $running_mc++;
    else $off_mc++;
}

$ach_rate = $total_plan > 0 ? round(($total_actual / $total_plan) * 100, 1) : 0;
if ($ach_rate > 100) $ach_rate = 100;

// ------------------------------------------------------------------
// 4. RESPONSE
// ------------------------------------------------------------------
if (ob_get_length()) ob_clean();
echo json_encode([
    'punya_data' => ($total_actual > 0),
    'kpi' => [
        'plan' => $total_plan,
        'actual' => $total_actual,
        'gap' => max(0, $total_plan - $total_actual),
        'ach' => $ach_rate,
        'waktu_mulai' => $global_start ? date('H:i:s', $global_start) : '--:--:--',
        'waktu_selesai' => $global_last ? date('H:i:s', $global_last) : '--:--:--',
    ],
    'status' => ['run' => $running_mc, 'off' => $off_mc],
    'ticker_data' => [
        "This shift has the highest output: {$highest_out['mc']} REACHES {$highest_out['val']} PCS",
    ],
    'machines' => array_values($machines),
]);