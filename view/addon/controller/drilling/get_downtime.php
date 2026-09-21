<?php

header('Content-Type: application/json');
include(__DIR__ . "/../../../../database.php");
date_default_timezone_set('Asia/Jakarta');

// ------------------------------------------------------------------
// 1. INPUT + DATA SHIFT
// ------------------------------------------------------------------
$reqDate   = $_GET['date']  ?? date('Y-m-d');
$reqShift  = $_GET['shift'] ?? 'Shift-1';
$shiftCode = strtoupper($reqShift); // "Shift-1" -> "SHIFT-1"

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

$start_time = "$reqDate {$shift['start_time']}";
$endDate    = $reqDate;
if ($shift['is_overnight'] === 't' || $shift['is_overnight'] === true) {
    $endDate = date('Y-m-d', strtotime("$reqDate +1 day"));
}
$end_time = "$endDate {$shift['end_time']}";

// ------------------------------------------------------------------
// 2. AMBIL MESIN DRILLING + LOG PRODUKSI DALAM RENTANG SHIFT
// ------------------------------------------------------------------
$machines = array_column(db_select(
    "SELECT m.machine_code
     FROM machine m
     JOIN process_stage s ON s.stage_id = m.stage_id
     WHERE s.stage_code = 'DRILLING'
     ORDER BY m.machine_code"
), 'machine_code');

$logRows = db_select(
    "SELECT m.machine_code, l.event_time
     FROM machine_process_log l
     JOIN machine m ON m.machine_id = l.machine_id
     JOIN process_stage s ON s.stage_id = m.stage_id AND s.stage_code = 'DRILLING'
     WHERE l.event_time >= :start AND l.event_time < :end
     ORDER BY m.machine_code, l.event_time",
    ['start' => $start_time, 'end' => $end_time]
);

$machine_records = [];
foreach ($machines as $mc) $machine_records[$mc] = [];
foreach ($logRows as $row) $machine_records[$row['machine_code']][] = $row['event_time'];

// ------------------------------------------------------------------
// 3. TENTUKAN BATAS WAKTU YANG DIPROSES (real-time aware)
// ------------------------------------------------------------------
$now_timestamp = time();
$end_timestamp = strtotime($end_time);
$limit_time = min($now_timestamp, $end_timestamp);
if (strtotime($start_time) > $now_timestamp) {
    $limit_time = strtotime($start_time); 
}

// ------------------------------------------------------------------
// 4. BANGUN BLOK UPTIME/DOWNTIME PER MESIN (gap > 60 detik = DOWNTIME)
// ------------------------------------------------------------------
$final_data = [];
$summary_data = [];

foreach ($machines as $mc) {
    $last_time = strtotime($start_time);
    $current_status = null;
    $current_block_start = $last_time;
    $current_block_pcs = 0;
    $mc_start_uptime = null;
    $mc_last_uptime = null;
    $mc_total_uptime_sec = 0;
    $mc_total_pcs = 0;

    foreach ($machine_records[$mc] as $time_str) {
        $time_val = strtotime($time_str);
        if ($time_val > $limit_time) break;

        $diff = $time_val - $last_time;
        $status = ($diff <= 60) ? 'UPTIME' : 'DOWNTIME';

        if ($current_status === null) {
            $current_status = $status;
        } elseif ($current_status !== $status) {
            $duration = $last_time - $current_block_start;
            $final_data[] = [
                'machine' => $mc,
                'start' => date('Y-m-d H:i:s', $current_block_start),
                'end' => date('Y-m-d H:i:s', $last_time),
                'status' => $current_status,
                'color' => ($current_status == 'UPTIME') ? '#80EF80' : '#FF746C',
                'pcs' => $current_block_pcs,
                'duration' => $duration,
            ];
            if ($current_status == 'UPTIME') {
                $mc_total_uptime_sec += $duration;
                $mc_total_pcs += $current_block_pcs;
            }
            $current_block_start = $last_time;
            $current_status = $status;
            $current_block_pcs = 0;
        }
        $current_block_pcs++;
        $last_time = $time_val;
        if ($mc_start_uptime === null) $mc_start_uptime = $time_val;
        $mc_last_uptime = $time_val;
    }

    if ($last_time < $limit_time) {
        $diff = $limit_time - $last_time;
        $status = ($diff <= 60) ? 'UPTIME' : 'DOWNTIME';
        if ($current_status !== $status) {
            if ($current_status !== null && $current_block_start != $last_time) {
                $duration = $last_time - $current_block_start;
                $final_data[] = [
                    'machine' => $mc,
                    'start' => date('Y-m-d H:i:s', $current_block_start),
                    'end' => date('Y-m-d H:i:s', $last_time),
                    'status' => $current_status,
                    'color' => ($current_status == 'UPTIME') ? '#80EF80' : '#FF746C',
                    'pcs' => $current_block_pcs,
                    'duration' => $duration,
                ];
                if ($current_status == 'UPTIME') {
                    $mc_total_uptime_sec += $duration;
                    $mc_total_pcs += $current_block_pcs;
                }
            }
            $current_block_start = $last_time;
            $current_status = $status;
            $current_block_pcs = 0;
        }
        $last_time = $limit_time;
    }

    if ($current_status !== null && $current_block_start < $last_time) {
        $duration = $last_time - $current_block_start;
        $final_data[] = [
            'machine' => $mc,
            'start' => date('Y-m-d H:i:s', $current_block_start),
            'end' => date('Y-m-d H:i:s', $last_time),
            'status' => $current_status,
            'color' => ($current_status == 'UPTIME') ? '#80EF80' : '#FF746C',
            'pcs' => $current_block_pcs,
            'duration' => $duration,
        ];
        if ($current_status == 'UPTIME') {
            $mc_total_uptime_sec += $duration;
            $mc_total_pcs += $current_block_pcs;
        }
    } elseif ($current_status === null) {
        if ($limit_time > $current_block_start) {
            $final_data[] = [
                'machine' => $mc,
                'start' => date('Y-m-d H:i:s', $current_block_start),
                'end' => date('Y-m-d H:i:s', $limit_time),
                'status' => 'DOWNTIME',
                'color' => '#ef4444',
                'pcs' => 0,
                'duration' => $limit_time - $current_block_start,
            ];
        }
    }

    $summary_data[$mc] = [
        'start_uptime' => $mc_start_uptime ? date('H:i:s', $mc_start_uptime) : '-',
        'last_uptime'  => $mc_last_uptime ? date('H:i:s', $mc_last_uptime) : '-',
        'avg_time'     => ($mc_total_pcs > 0) ? round($mc_total_uptime_sec / $mc_total_pcs, 1) : 0,
    ];
}

// ------------------------------------------------------------------
// 5. RESPONSE
// ------------------------------------------------------------------
echo json_encode([
    'date' => $reqDate,
    'shift' => $reqShift,
    'start_shift' => $start_time,
    'end_shift' => $end_time,
    'data' => $final_data,
    'summary' => $summary_data,
]);