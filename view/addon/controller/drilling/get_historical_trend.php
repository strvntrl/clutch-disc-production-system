<?php

header('Content-Type: application/json');
include(__DIR__ . "/../../../../database.php");
date_default_timezone_set('Asia/Jakarta');

const DAILY_TARGET_PCS = 3000;

$type          = $_GET['type']  ?? 'now';
$month         = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
$year          = isset($_GET['year'])  ? (int) $_GET['year']  : (int) date('Y');
$history_date  = $_GET['date'] ?? date('Y-m-d');

$machines = array_column(db_select(
    "SELECT m.machine_code
     FROM machine m
     JOIN process_stage s ON s.stage_id = m.stage_id
     WHERE s.stage_code = 'DRILLING'
     ORDER BY m.machine_code"
), 'machine_code');

$data = [];
$total_all_data = 0;

if ($type === 'yearly') {
    for ($i = 1; $i <= 12; $i++) {
        $row = ['time' => date("F", mktime(0, 0, 0, $i, 10)), 'target' => 0];
        foreach ($machines as $mc) $row[$mc] = 0;
        $data[$i] = $row;
    }

    $start_date = "$year-01-01 07:30:00";
    $end_date   = ($year + 1) . "-01-01 07:30:00";

    $rows = db_select(
        "SELECT m.machine_code AS mc,
                EXTRACT(MONTH FROM (l.event_time - INTERVAL '7 hours'))::int AS bulan,
                COUNT(*) AS actual
         FROM machine_process_log l
         JOIN machine m ON m.machine_id = l.machine_id
         JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'DRILLING'
         WHERE l.event_time >= :start AND l.event_time < :end
         GROUP BY m.machine_code, EXTRACT(MONTH FROM (l.event_time - INTERVAL '7 hours'))",
        ['start' => $start_date, 'end' => $end_date]
    );

    foreach ($rows as $r) {
        $bulan = (int) $r['bulan'];
        if (isset($data[$bulan][$r['mc']])) {
            $data[$bulan][$r['mc']] = (int) $r['actual'];
            $total_all_data += (int) $r['actual'];
        }
    }
} else {
    if ($type === 'now') {
        $month = (int) date('m');
        $year  = (int) date('Y');
    } elseif ($type === 'history') {
        $time_history = strtotime($history_date);
        $month = (int) date('m', $time_history);
        $year  = (int) date('Y', $time_history);
    }

    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for ($i = 1; $i <= $days_in_month; $i++) {
        $row = ['time' => (string) $i, 'target' => DAILY_TARGET_PCS];
        foreach ($machines as $mc) $row[$mc] = 0;
        $data[$i] = $row;
    }

    $start_date = date('Y-m-d H:i:s', mktime(7, 30, 0, $month, 1, $year));
    $next_month = $month == 12 ? 1 : $month + 1;
    $next_year  = $month == 12 ? $year + 1 : $year;
    $end_date   = date('Y-m-d H:i:s', mktime(7, 30, 0, $next_month, 1, $next_year));

    if ($type === 'now') {
        $today_start = date('Y-m-d') . ' 07:30:00';
        if (strtotime($today_start) < strtotime($end_date)) {
            $end_date = $today_start;
        }
    }

    $rows = db_select(
        "SELECT m.machine_code AS mc,
                EXTRACT(DAY FROM (l.event_time - INTERVAL '7 hours'))::int AS tanggal,
                COUNT(*) AS actual
         FROM machine_process_log l
         JOIN machine m ON m.machine_id = l.machine_id
         JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'DRILLING'
         WHERE l.event_time >= :start AND l.event_time < :end
         GROUP BY m.machine_code, EXTRACT(DAY FROM (l.event_time - INTERVAL '7 hours'))",
        ['start' => $start_date, 'end' => $end_date]
    );

    foreach ($rows as $r) {
        $tanggal = (int) $r['tanggal'];
        if (isset($data[$tanggal][$r['mc']])) {
            $data[$tanggal][$r['mc']] = (int) $r['actual'];
            $total_all_data += (int) $r['actual'];
        }
    }
}

$final_data = array_values($data);

echo json_encode([
    'status'     => 'success',
    'type'       => $type,
    'month_name' => date("F", mktime(0, 0, 0, $month, 10)),
    'year'       => $year,
    'total_data' => $total_all_data,
    'data'       => $final_data,
]);