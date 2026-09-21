<?php
include(__DIR__ . "/../../../database.php");

date_default_timezone_set('Asia/Jakarta');

// ================= PARAMETER =================
$date       = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$shift_name = isset($_GET['shift']) ? $_GET['shift'] : 'Shift-1';
$shift_id   = ($shift_name == 'Shift-1') ? 1 : (($shift_name == 'Shift-2') ? 2 : 3);

// ================= CEK HARI =================
$day_of_week = date('N', strtotime($date)); 

// ================= HITUNG START END =================
if ($day_of_week == 6) {
    // ===== SABTU =====
    if ($shift_name == 'Shift-1') {
        $start_time = $date . ' 07:30:00';
        $end_time   = $date . ' 12:30:00';
    } elseif ($shift_name == 'Shift-2') {
        $start_time = $date . ' 12:30:00';
        $end_time   = $date . ' 17:30:00';
    } else {
        $start_time = $date . ' 17:30:00';
        $end_time   = $date . ' 22:30:00';
    }
} elseif ($day_of_week == 7) {
    // ===== MINGGU - tidak ada shift =====
    echo json_encode([
        'last_update' => date('H:i:s'),
        'date'        => $date,
        'shift'       => $shift_name,
        'data'        => []
    ]);
    exit;
} else {
    // ===== SENIN - JUMAT =====
    if ($shift_name == 'Shift-1') {
        $start_time = $date . ' 07:30:00';
        $end_time   = $date . ' 15:30:00';
    } elseif ($shift_name == 'Shift-2') {
        $start_time = $date . ' 15:30:00';
        $end_time   = $date . ' 23:30:00';
    } else {
        $prev_date  = date('Y-m-d', strtotime($date . ' -1 day'));
        $start_time = $prev_date . ' 23:30:00';
        $end_time   = $date . ' 07:30:00';
    }
}

// ================= TIME SLOTS BERDASARKAN SHIFT & HARI =================
if ($day_of_week == 6) {
    // Sabtu 
    $saturday_slots = [
        'Shift-1' => [
            "SELECT 0 idx, '07:30' jam",
            "SELECT 1, '08:30'",
            "SELECT 2, '09:30'",
            "SELECT 3, '10:30'",
            "SELECT 4, '11:30'",
            "SELECT 5, '12:30'",
        ],
        'Shift-2' => [
            "SELECT 0 idx, '12:30' jam",
            "SELECT 1, '13:30'",
            "SELECT 2, '14:30'",
            "SELECT 3, '15:30'",
            "SELECT 4, '16:30'",
            "SELECT 5, '17:30'",
        ],
        'Shift-3' => [
            "SELECT 0 idx, '17:30' jam",
            "SELECT 1, '18:30'",
            "SELECT 2, '19:30'",
            "SELECT 3, '20:30'",
            "SELECT 4, '21:30'",
            "SELECT 5, '22:30'",
        ],
    ];
    $slot_union = implode(' UNION ALL ', $saturday_slots[$shift_name]);
    $time_slots_sql = "
        WITH time_slots AS (
            SELECT * FROM (
                $slot_union
            ) a
        )
    ";
} else {
    // Senin - Jumat 
    $time_slots_sql = "
        WITH time_slots AS (
            SELECT * FROM (
                SELECT 0 idx, '07:30' jam, 'Shift-1' shift_name UNION ALL
                SELECT 1, '08:30', 'Shift-1' UNION ALL
                SELECT 2, '09:30', 'Shift-1' UNION ALL
                SELECT 3, '10:30', 'Shift-1' UNION ALL
                SELECT 4, '11:30', 'Shift-1' UNION ALL
                SELECT 5, '12:30', 'Shift-1' UNION ALL
                SELECT 6, '13:30', 'Shift-1' UNION ALL
                SELECT 7, '14:30', 'Shift-1' UNION ALL
                SELECT 8, '15:30', 'Shift-1' UNION ALL

                SELECT 0, '15:30', 'Shift-2' UNION ALL
                SELECT 1, '16:30', 'Shift-2' UNION ALL
                SELECT 2, '17:30', 'Shift-2' UNION ALL
                SELECT 3, '18:30', 'Shift-2' UNION ALL
                SELECT 4, '19:30', 'Shift-2' UNION ALL
                SELECT 5, '20:30', 'Shift-2' UNION ALL
                SELECT 6, '21:30', 'Shift-2' UNION ALL
                SELECT 7, '22:30', 'Shift-2' UNION ALL
                SELECT 8, '23:30', 'Shift-2' UNION ALL

                SELECT 0, '23:30', 'Shift-3' UNION ALL
                SELECT 1, '00:30', 'Shift-3' UNION ALL
                SELECT 2, '01:30', 'Shift-3' UNION ALL
                SELECT 3, '02:30', 'Shift-3' UNION ALL
                SELECT 4, '03:30', 'Shift-3' UNION ALL
                SELECT 5, '04:30', 'Shift-3' UNION ALL
                SELECT 6, '05:30', 'Shift-3' UNION ALL
                SELECT 7, '06:30', 'Shift-3' UNION ALL
                SELECT 8, '07:30', 'Shift-3'
            ) a
            WHERE shift_name = '$shift_name'
        )
    ";
}

// ================= QUERY =================
$query = "
    $time_slots_sql,

    machines AS (
        SELECT 'DR01' mc UNION ALL
        SELECT 'DR02' UNION ALL
        SELECT 'DR03' UNION ALL
        SELECT 'DR04' UNION ALL
        SELECT 'DR05'
    ),

    base AS (
        SELECT
            RIGHT('0' + CAST(DATEPART(HOUR, DATEADD(MINUTE, -30, s.[datetime])) AS VARCHAR), 2) + ':30' AS jam,
            REPLACE(REPLACE(UPPER(s.mc), ' ', ''), '-', '') AS mc,
            CASE WHEN s.status = 'OK' THEN 1 ELSE 0 END AS ok_val,
            CASE WHEN s.status = 'NG' THEN 1 ELSE 0 END AS ng_val,
            1 AS total_val
        FROM sfc s
        WHERE
            s.[datetime] >= '$start_time'
            AND s.[datetime] < '$end_time'
    ),

    data_grouped AS (
        SELECT
            jam,
            mc,
            SUM(ok_val)    AS actual,
            SUM(ng_val)    AS ng,
            SUM(total_val) AS iot
        FROM base
        GROUP BY jam, mc
    )

    SELECT
        ts.jam,
        m.mc,
        ISNULL(d.iot,    0) AS iot,
        ISNULL(d.actual, 0) AS actual,
        ISNULL(d.ng,     0) AS ng
    FROM time_slots ts
    CROSS JOIN machines m
    LEFT JOIN data_grouped d
        ON d.jam = ts.jam
        AND d.mc = m.mc
    ORDER BY ts.idx, m.mc
";

$result = odbc_exec($con, $query);

if (!$result) {
    die(json_encode([
        'error' => odbc_errormsg($con),
        'data'  => []
    ]));
}

$data = [];
while ($row = odbc_fetch_array($result)) {
    $data[] = [
        'jam'    => $row['jam'],
        'mc'     => $row['mc'],
        'iot'    => (int)$row['iot'],
        'actual' => (int)$row['actual'],
        'ng'     => (int)$row['ng']
    ];
}

echo json_encode([
    'last_update' => date('H:i:s'),
    'date'        => $date,
    'shift'       => $shift_name,
    'data'        => $data
]);