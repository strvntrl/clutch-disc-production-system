<?php
include(__DIR__ . "/../../../database.php");

// ================= UPDATE TARGET =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_target') {

    $row_id = (int)($_POST['row_id'] ?? 0);
    $target = (int)($_POST['target'] ?? 0);

    if ($row_id <= 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Row ID tidak valid'
        ]);
        exit;
    }

    // update target HANYA pada baris drilling yang bersangkutan (per row, bukan per form)
    $sql = "
        UPDATE dbo.pmc_drilling
        SET target = $target
        WHERE id = $row_id
    ";

    $ok = odbc_exec($con, $sql);

    if ($ok) {
        echo json_encode([
            'status' => 'success'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => odbc_errormsg($con)
        ]);
    }

    exit;
}

if (!$con) {
    die("Koneksi database gagal");
}

function safeStr($val)
{
    return str_replace("'", "''", trim($val));
}

function safeInt($val)
{
    return (int)$val;
}

function isValidDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// ================= GRANULARITAS =================
function getPeriodeExpr($granularitas, $dateColumn = 'f.tanggal')
{
    switch (strtolower($granularitas)) {
        case 'bulanan':
            return "LEFT(CONVERT(varchar(10), $dateColumn, 120), 7)";
        case 'tahunan':
            return "LEFT(CONVERT(varchar(10), $dateColumn, 120), 4)";
        case 'harian':
        default:
            return "CONVERT(varchar(10), $dateColumn, 120)";
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
foreach ($selected_seksi as $sid) {
    $sid = safeInt($sid);
    foreach ($seksiList as $s) {
        if ((int)$s['id_seksi'] === $sid) {
            $selectedNames[$sid] = $s['nama_seksi'];
            break;
        }
    }
}
if (empty($selectedNames)) {
    return;
}

$dateFromSafe = safeStr($date_from);
$dateToSafe   = safeStr($date_to);

$periodeExpr = getPeriodeExpr($granularitas);

$seksiSql = [];
foreach ($selectedNames as $nama) {
    $seksiSql[] = "'" . safeStr($nama) . "'";
}
$seksiIn = implode(",", $seksiSql);

// ================= QUERY =================
$query = "
WITH form_data AS (
    SELECT
        f.id,
        f.seksi,
        f.shift,
        f.tanggal,
        $periodeExpr AS periode
    FROM dbo.pmc_form f
    WHERE
        f.tanggal >= '$dateFromSafe'
        AND f.tanggal < DATEADD(DAY, 1, '$dateToSafe')
        AND f.seksi IN ($seksiIn)
),

detail_data AS (
    SELECT
        d.id AS row_id,
        d.form_id,
        d.mesin,
        d.shift,
        ISNULL(d.actual,0) AS actual,
        ISNULL(d.ng,0) AS ng,
        ISNULL(d.target,0) AS target
    FROM dbo.pmc_drilling d
    WHERE d.validasi = 1
),

sfc_summary AS (
    SELECT
        CASE
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '07:30:00'
             AND CONVERT(varchar(8), s.[datetime], 108) < '15:30:00'
            THEN 1
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '15:30:00'
             AND CONVERT(varchar(8), s.[datetime], 108) < '23:30:00'
            THEN 2
            ELSE 3
        END AS shift,
        CASE
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '23:30:00'
            THEN CONVERT(varchar(10), DATEADD(DAY,1,s.[datetime]), 120)
            ELSE CONVERT(varchar(10), s.[datetime], 120)
        END AS tanggal,
        s.mc AS mesin,
        COUNT(*) AS iot
    FROM dbo.sfc s
    WHERE
        s.[datetime] >= DATEADD(HOUR,-8,'$dateFromSafe')
        AND s.[datetime] < DATEADD(HOUR,16,'$dateToSafe')
    GROUP BY
        CASE
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '07:30:00'
             AND CONVERT(varchar(8), s.[datetime], 108) < '15:30:00'
            THEN 1
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '15:30:00'
             AND CONVERT(varchar(8), s.[datetime], 108) < '23:30:00'
            THEN 2
            ELSE 3
        END,
        CASE
            WHEN CONVERT(varchar(8), s.[datetime], 108) >= '23:30:00'
            THEN CONVERT(varchar(10), DATEADD(DAY,1,s.[datetime]), 120)
            ELSE CONVERT(varchar(10), s.[datetime], 120)
        END,
        s.mc
)

SELECT
    f.id AS form_id,
    d.row_id,
    f.seksi,
    f.periode,
    d.mesin,
    d.shift,
    SUM(ISNULL(s.iot,0)) AS iot,
    d.actual,
    d.ng,
    d.target
FROM form_data f
INNER JOIN detail_data d
    ON d.form_id = f.id
LEFT JOIN sfc_summary s
    ON s.tanggal = CONVERT(varchar(10), f.tanggal, 120)
    AND s.shift  = CAST(f.shift AS INT)
    AND s.mesin  = d.mesin
GROUP BY
    f.id,
    d.row_id,
    f.seksi,
    f.periode,
    d.mesin,
    d.shift,
    d.actual,
    d.ng,
    d.target
ORDER BY
    f.periode DESC,
    d.mesin ASC,
    d.shift ASC
";

// ================= QUERY BREAKDOWN KETERANGAN =================
$breakdownBySeksi = [];

foreach ($selectedNames as $sid => $nama) {
    $seksiNameSafe = safeStr($nama);

    $breakdownSQL = "
        SELECT
            mesin,
            keterangan,
            COUNT(*) AS frekuensi
        FROM (
            SELECT
                d.mesin,
                CAST(d.keterangan AS varchar(500)) AS keterangan
            FROM dbo.pmc_drilling d
            INNER JOIN dbo.pmc_form f ON f.id = d.form_id
            WHERE
                f.tanggal >= '$dateFromSafe'
                AND f.tanggal < DATEADD(DAY, 1, '$dateToSafe')
                AND f.seksi = '$seksiNameSafe'
                AND d.keterangan IS NOT NULL
                AND d.validasi = 1
        ) AS sub
        WHERE keterangan <> ''
        GROUP BY
            mesin,
            keterangan
        ORDER BY
            frekuensi DESC
    ";

    $breakdownRes = odbc_exec($con, $breakdownSQL);

    $akarMasalah = [];
    $mesinBreakdown = [];
    $akarTemp  = [];
    $mesinTemp = [];

    if ($breakdownRes) {
        while ($brow = odbc_fetch_array($breakdownRes)) {
            $mesin = trim($brow['mesin'] ?? '-');
            $ket   = trim($brow['keterangan'] ?? '');
            $freq  = (int)$brow['frekuensi'];

            if ($ket === '') continue;

            $ketArr = explode(',', $ket);
            foreach ($ketArr as $k) {
                $k = trim($k);
                if ($k === '') continue;

                // Akar masalah aggregate
                if (!isset($akarTemp[$k])) $akarTemp[$k] = 0;
                $akarTemp[$k] += $freq;

                // Mesin aggregate
                if (!isset($mesinTemp[$mesin])) $mesinTemp[$mesin] = 0;
                $mesinTemp[$mesin] += $freq;
            }
        }
    }

    // Sort descending
    arsort($akarTemp);
    arsort($mesinTemp);

    foreach ($akarTemp as $label => $count) {
        $akarMasalah[] = ['label' => $label, 'count' => $count];
    }
    foreach ($mesinTemp as $mesin => $count) {
        $mesinBreakdown[] = ['mesin' => $mesin, 'count' => $count];
    }

    $breakdownBySeksi[$sid] = [
        'akar_masalah'   => $akarMasalah,
        'mesin_breakdown' => $mesinBreakdown,
    ];
}

$result = odbc_exec($con, $query);
if (!$result) {
    die("
        SQL ERROR:
        <br><br>
        " . odbc_errormsg($con) . "
    ");
}

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

while ($row = odbc_fetch_array($result)) {
    $seksiNama = $row['seksi'];
    $seksiId = array_search($seksiNama, $selectedNames);
    if (!$seksiId) {
        continue;
    }

    $actual = (int)($row['actual'] ?? 0);
    $ng     = (int)($row['ng'] ?? 0);
    $iot    = (int)($row['iot'] ?? 0);

    if (
        $iot == 0 &&
        $actual == 0 &&
        $ng == 0
    ) {
        continue;
    }

    $reportBySeksi[$seksiId][] = [
        'form_id' => (int)$row['form_id'],
        'row_id'  => (int)$row['row_id'],
        'periode' => $row['periode'],
        'mesin'   => $row['mesin'],
        'shift'   => $row['shift'],
        'iot'     => $iot,
        'actual'  => $actual,
        'ng'      => $ng,
        'target'  => (int)$row['target'],
    ];

    $kpiBySeksi[$seksiId]['total_iot'] += $iot;
    $kpiBySeksi[$seksiId]['total_actual'] += $actual;
    $kpiBySeksi[$seksiId]['total_ng'] += $ng;
    $kpiBySeksi[$seksiId]['total_target'] += (int)$row['target'];
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
