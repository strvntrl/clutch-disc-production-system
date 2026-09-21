<?php
include(__DIR__ . "/../../../database.php");

date_default_timezone_set('Asia/Jakarta');

// ================= ERROR HANDLER =================
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'error',
        'message' => $errstr,
        'file'    => basename($errfile),
        'line'    => $errline
    ]);
    exit;
});

set_exception_handler(function ($e) {
    http_response_code(500);
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage(),
        'file'    => basename($e->getFile()),
        'line'    => $e->getLine()
    ]);
    exit;
});

ini_set('display_errors', 0);
error_reporting(E_ALL);

while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/json');

// ================= PARAMETER =================
$tanggal  = $_GET['tanggal'] ?? date('Y-m-d');
$shiftStr = $_GET['shift']   ?? 'Shift-1';
$seksi    = 'HOTPRESS';

function convertShift($s)
{
    if ($s === 'Shift-1') return 1;
    if ($s === 'Shift-2') return 2;
    return 3;
}
$shift = convertShift($shiftStr);

// ================= CARI HEADER =================
$headerSQL = "
    SELECT id, target, approve_foreman, approve_supervisor, approve_assmanager
    FROM pmc_form
    WHERE CONVERT(date, tanggal) = '$tanggal'
    AND   shift = $shift
    AND   seksi = '$seksi'
";
$headerRes = odbc_exec($con, $headerSQL);

if (!$headerRes || !odbc_fetch_row($headerRes)) {
    echo json_encode([
        'data'    => [],
        'target'  => 0,
        'approve' => [0 => 0, 1 => 0, 2 => 0]
    ]);
    exit;
}

$headerId  = odbc_result($headerRes, 'id');
$target    = intval(odbc_result($headerRes, 'target') ?? 0);
$approve0  = intval(odbc_result($headerRes, 'approve_foreman')    ?? 0);
$approve1  = intval(odbc_result($headerRes, 'approve_supervisor') ?? 0);
$approve2  = intval(odbc_result($headerRes, 'approve_assmanager') ?? 0);

// ================= AMBIL DETAIL =================
$detailSQL = "
    SELECT
        row_id, no_wos, mc_no, type, cf_no, customer,
        berat_pcs, hasil_kg, baik, rusak, total_hasil,
        reff_no, start_proses, finish_proses,
        operator, keterangan, validasi
    FROM pmc_hotpress
    WHERE form_id = $headerId
    ORDER BY row_id ASC
";
$detailRes = odbc_exec($con, $detailSQL);

$data = [];
while ($row = odbc_fetch_array($detailRes)) {
    $data[] = [
        'row_id'       => intval($row['row_id']),
        'no_wos'       => $row['no_wos']       ?? '',
        'mc_no'        => $row['mc_no']        ?? '',
        'type'         => $row['type']         ?? '',
        'cf_no'        => $row['cf_no']        ?? '',
        'customer'     => $row['customer']     ?? '',
        'berat_pcs'    => $row['berat_pcs']    ?? '',
        'hasil_kg'     => $row['hasil_kg']     ?? '',
        'baik'         => intval($row['baik']  ?? 0),
        'rusak'        => intval($row['rusak'] ?? 0),
        'total_hasil'  => intval($row['total_hasil'] ?? 0),
        'reff_no'      => $row['reff_no']      ?? '',
        'start_proses' => $row['start_proses'] ?? '',
        'finish_proses' => $row['finish_proses'] ?? '',
        'operator'     => $row['operator']     ?? '',
        'keterangan'   => $row['keterangan']   ?? '',
        'validasi'     => intval($row['validasi'] ?? 0),
    ];
}

echo json_encode([
    'data'    => $data,
    'target'  => $target,
    'approve' => [
        0 => $approve0,
        1 => $approve1,
        2 => $approve2,
    ]
]);
