<?php

include(__DIR__ . "/../../../database.php");

// ================= UPDATE TARGET =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_target') {

    $row_id = (int) ($_POST['row_id'] ?? 0);
    $target = (int) ($_POST['target'] ?? 0);

    header('Content-Type: application/json');

    if ($row_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Row ID tidak valid']);
        exit;
    }

    try {
        // update target HANYA pada baris detail yang bersangkutan (per row, bukan per form)
        db_execute(
            "UPDATE pmc_entry_detail SET target_qty = :t, updated_at = now() WHERE detail_id = :id",
            ['t' => $target, 'id' => $row_id]
        );
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    exit;
}

function safeInt($val): int
{
    return (int) $val;
}

function isValidDate(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// ================= GRANULARITAS =================
function getPeriodeExpr(string $granularitas, string $dateColumn = 'pe.entry_date'): string
{
    switch (strtolower($granularitas)) {
        case 'bulanan':
            return "to_char($dateColumn, 'YYYY-MM')";
        case 'tahunan':
            return "to_char($dateColumn, 'YYYY')";
        case 'harian':
        default:
            return "to_char($dateColumn, 'YYYY-MM-DD')";
    }
}

// ================= PARAMETER =================
$granularitas   = $granularitas   ?? 'harian';
$date_from      = $date_from      ?? date('Y-m-01');
$date_to        = $date_to        ?? date('Y-m-d');
$selected_seksi = $selected_seksi ?? [];
$seksiList      = $seksiList      ?? [];

$reportBySeksi = [];
$kpiBySeksi    = [];

// ================= VALIDASI =================
if (
    empty($selected_seksi) ||
    !isValidDate($date_from) ||
    !isValidDate($date_to)
) {
    return;
}
if ($date_from > $date_to) {
    return;
}

// ================= SEKSI =================
$selectedNames = []; 
$selectedIds   = []; 
foreach ($selected_seksi as $sid) {
    $sid = safeInt($sid);
    foreach ($seksiList as $s) {
        if ((int) $s['id_seksi'] === $sid) {
            $selectedNames[$sid] = $s['nama_seksi'];
            $selectedIds[] = $sid;
            break;
        }
    }
}
if (empty($selectedNames)) {
    return;
}

$periodeExpr = getPeriodeExpr($granularitas);

$stagePlaceholders = [];
$stageParams = [];
foreach (array_values($selectedIds) as $idx => $sid) {
    $ph = ":sid{$idx}";
    $stagePlaceholders[] = $ph;
    $stageParams[substr($ph, 1)] = $sid;
}
$stageInClause = implode(',', $stagePlaceholders);

// ================= QUERY UTAMA =================
$query = "
    SELECT
        pe.entry_id   AS form_id,
        ped.detail_id AS row_id,
        ps.stage_name AS seksi,
        $periodeExpr  AS periode,
        m.machine_code AS mesin,
        sh.shift_code AS shift,
        COALESCE(iot.total_iot, 0) AS iot,
        ped.actual_qty AS actual,
        ped.reject_qty AS ng,
        ped.target_qty AS target
    FROM pmc_entry pe
    JOIN pmc_entry_detail ped ON ped.entry_id = pe.entry_id
    JOIN process_stage ps ON ps.stage_id = pe.stage_id
    JOIN shift sh ON sh.shift_id = pe.shift_id
    LEFT JOIN machine m ON m.machine_id = ped.machine_id
    LEFT JOIN (
        SELECT
            machine_id, shift_id,
            DATE(event_time - INTERVAL '7 hours') AS shift_date,
            COUNT(*) AS total_iot
        FROM machine_process_log
        GROUP BY machine_id, shift_id, DATE(event_time - INTERVAL '7 hours')
    ) iot ON iot.machine_id = ped.machine_id
         AND iot.shift_id = pe.shift_id
         AND iot.shift_date = pe.entry_date
    WHERE
        pe.entry_date >= :date_from
        AND pe.entry_date <= :date_to
        AND pe.stage_id IN ($stageInClause)
        AND ped.is_checked = TRUE
    ORDER BY periode DESC, mesin ASC, shift ASC
";

$queryParams = array_merge(
    ['date_from' => $date_from, 'date_to' => $date_to],
    $stageParams
);

$rows = db_select($query, $queryParams);

// ================= QUERY BREAKDOWN KETERANGAN =================
$breakdownBySeksi = [];

foreach ($selectedNames as $sid => $nama) {
    $breakdownRows = db_select(
        "SELECT m.machine_code AS mesin, ped.keterangan
         FROM pmc_entry_detail ped
         JOIN pmc_entry pe ON pe.entry_id = ped.entry_id
         LEFT JOIN machine m ON m.machine_id = ped.machine_id
         WHERE pe.entry_date >= :date_from
           AND pe.entry_date <= :date_to
           AND pe.stage_id = :stage_id
           AND ped.keterangan IS NOT NULL
           AND ped.keterangan <> ''
           AND ped.is_checked = TRUE",
        ['date_from' => $date_from, 'date_to' => $date_to, 'stage_id' => $sid]
    );

    $akarTemp  = [];
    $mesinTemp = [];

    foreach ($breakdownRows as $brow) {
        $mesin = trim($brow['mesin'] ?? '-');
        $ket   = trim($brow['keterangan'] ?? '');
        if ($ket === '') continue;

        $ketArr = explode(',', $ket);
        foreach ($ketArr as $k) {
            $k = trim($k);
            if ($k === '') continue;

            $akarTemp[$k] = ($akarTemp[$k] ?? 0) + 1;
            $mesinTemp[$mesin] = ($mesinTemp[$mesin] ?? 0) + 1;
        }
    }

    arsort($akarTemp);
    arsort($mesinTemp);

    $akarMasalah = [];
    foreach ($akarTemp as $label => $count) {
        $akarMasalah[] = ['label' => $label, 'count' => $count];
    }
    $mesinBreakdown = [];
    foreach ($mesinTemp as $mesin => $count) {
        $mesinBreakdown[] = ['mesin' => $mesin, 'count' => $count];
    }

    $breakdownBySeksi[$sid] = [
        'akar_masalah'    => $akarMasalah,
        'mesin_breakdown' => $mesinBreakdown,
    ];
}

// ================= OLAH HASIL QUERY UTAMA =================
foreach ($selectedNames as $sid => $nama) {
    $reportBySeksi[$sid] = [];
    $kpiBySeksi[$sid] = [
        'total_iot'    => 0,
        'total_actual' => 0,
        'total_ng'     => 0,
        'total_target' => 0,
        'achievement'  => '0.0',
    ];
}

foreach ($rows as $row) {
    $seksiNama = $row['seksi'];
    $seksiId = array_search($seksiNama, $selectedNames);
    if ($seksiId === false) continue;

    $actual = (int) ($row['actual'] ?? 0);
    $ng     = (int) ($row['ng'] ?? 0);
    $iot    = (int) ($row['iot'] ?? 0);

    if ($iot == 0 && $actual == 0 && $ng == 0) continue;

    $reportBySeksi[$seksiId][] = [
        'form_id' => (int) $row['form_id'],
        'row_id'  => (int) $row['row_id'],
        'periode' => $row['periode'],
        'mesin'   => $row['mesin'],
        'shift'   => $row['shift'],
        'iot'     => $iot,
        'actual'  => $actual,
        'ng'      => $ng,
        'target'  => (int) $row['target'],
    ];

    $kpiBySeksi[$seksiId]['total_iot']    += $iot;
    $kpiBySeksi[$seksiId]['total_actual'] += $actual;
    $kpiBySeksi[$seksiId]['total_ng']     += $ng;
    $kpiBySeksi[$seksiId]['total_target'] += (int) $row['target'];
}

// ================= ACHIEVEMENT =================
foreach ($kpiBySeksi as $sid => $kpi) {
    $actual = $kpi['total_actual'];
    $target = $kpi['total_target'];

    $achievement = $target > 0
        ? number_format(($actual / $target) * 100, 1, '.', '')
        : '0.0';

    $kpiBySeksi[$sid]['achievement'] = $achievement;
}