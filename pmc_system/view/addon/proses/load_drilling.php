<?php

/**
 * pmc/view/addon/proses/load_drilling.php
 *
 * Endpoint: GET ?tanggal=YYYY-MM-DD&shift=Shift-1
 * Balikin data form PMC Drilling yang sudah tersimpan (kalau ada),
 * plus status approve 3 tingkat.
 */

session_start();
$GLOBALS['AUTH_GUARD_DEPTH'] = 3;
include(__DIR__ . "/../../../auth/auth_guard.php");
include(__DIR__ . "/../../../../database.php");
header('Content-Type: application/json');

$tanggal   = $_GET['tanggal'] ?? date('Y-m-d');
$shiftName = $_GET['shift']   ?? 'Shift-1';
$shiftCode = strtoupper($shiftName);

$shiftRow = db_select("SELECT shift_id FROM shift WHERE shift_code = :c LIMIT 1", ['c' => $shiftCode]);
if (empty($shiftRow)) {
    echo json_encode(['data' => [], 'approve' => [0, 0, 0], 'status' => 0]);
    exit;
}
$shiftId = $shiftRow[0]['shift_id'];

$stageRow = db_select("SELECT stage_id FROM process_stage WHERE stage_code = 'DRILLING' LIMIT 1");
$stageId  = $stageRow[0]['stage_id'];

$entryRow = db_select(
    "SELECT entry_id, status, approve_foreman, approve_supervisor, approve_manager
     FROM pmc_entry WHERE entry_date = :d AND shift_id = :s AND stage_id = :g",
    ['d' => $tanggal, 's' => $shiftId, 'g' => $stageId]
);

if (empty($entryRow)) {
    echo json_encode(['data' => [], 'approve' => [0, 0, 0], 'status' => 0]);
    exit;
}
$entry = $entryRow[0];

$detailRows = db_select(
    "SELECT d.row_no, m.machine_code, p.part_number, c.customer_name, p.product_type,
            d.jumlah_mata_bor, d.ukuran_bor, d.jam_operasional,
            d.target_qty, d.actual_qty, d.reject_qty,
            o.operator_name, d.keterangan, d.is_checked
     FROM pmc_entry_detail d
     LEFT JOIN machine  m ON m.machine_id  = d.machine_id
     LEFT JOIN part     p ON p.part_id     = d.part_id
     LEFT JOIN customer c ON c.customer_id = p.customer_id
     LEFT JOIN operator o ON o.operator_id = d.operator_id
     WHERE d.entry_id = :e
     ORDER BY d.row_no",
    ['e' => $entry['entry_id']]
);

$data = array_map(function ($r) {
    return [
        'row_id'          => (string) $r['row_no'],
        'mesin'           => $r['machine_code'],
        'cf_no'           => $r['part_number'],
        'customer'        => $r['customer_name'],
        'diameter_cf'     => $r['product_type'], // slot "SIZE CF" diisi tipe produk (bukan diameter mata bor)
        'jumlah_mata_bor' => $r['jumlah_mata_bor'],
        'ukuran_bor'      => $r['ukuran_bor'],
        'jam'             => $r['jam_operasional'],
        'target'          => $r['target_qty'],
        'actual'          => $r['actual_qty'],
        'ng'              => $r['reject_qty'],
        'operator'        => $r['operator_name'],
        'keterangan'      => $r['keterangan'],
        'validasi'        => ($r['is_checked'] === 't' || $r['is_checked'] === true) ? 1 : 0,
    ];
}, $detailRows);

echo json_encode([
    'data'   => $data,
    'approve' => [
        ($entry['approve_foreman'] === 't' || $entry['approve_foreman'] === true) ? 1 : 0,
        ($entry['approve_supervisor'] === 't' || $entry['approve_supervisor'] === true) ? 1 : 0,
        ($entry['approve_manager'] === 't' || $entry['approve_manager'] === true) ? 1 : 0,
    ],
    'status' => $entry['status'] === 'SUBMITTED' ? 1 : 0,
]);
