<?php

/**
 * pmc/view/addon/proses/save_drilling.php
 *
 * Simpan (upsert) data form PMC Drilling.
 * Header: pmc_entry (1 per tanggal+shift+stage)
 * Detail: pmc_entry_detail (per baris/row_no, upsert via ON CONFLICT)
 */

session_start();
$GLOBALS['AUTH_GUARD_DEPTH'] = 3; // proses/ -> addon/ -> view/ -> pmc/ (3x naik ke auth/)
include(__DIR__ . "/../../../auth/auth_guard.php");
include(__DIR__ . "/../../../../database.php");
header('Content-Type: application/json');

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
});

$shiftName = $_POST['shift']   ?? 'Shift-1';
$tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
$shiftCode = strtoupper($shiftName);

$shiftRow = db_select("SELECT shift_id FROM shift WHERE shift_code = :c LIMIT 1", ['c' => $shiftCode]);
if (empty($shiftRow)) {
    echo json_encode(['status' => 'error', 'message' => 'Shift tidak dikenali']);
    exit;
}
$shiftId = $shiftRow[0]['shift_id'];

$stageRow = db_select("SELECT stage_id FROM process_stage WHERE stage_code = 'DRILLING' LIMIT 1");
$stageId  = $stageRow[0]['stage_id'];

// ================= UPSERT HEADER =================
$existingEntry = db_select(
    "SELECT entry_id FROM pmc_entry WHERE entry_date = :d AND shift_id = :s AND stage_id = :g",
    ['d' => $tanggal, 's' => $shiftId, 'g' => $stageId]
);

if (!empty($existingEntry)) {
    $entryId = $existingEntry[0]['entry_id'];
} else {
    $ins = db_select(
        "INSERT INTO pmc_entry (entry_date, shift_id, stage_id, status)
         VALUES (:d, :s, :g, 'DRAFT') RETURNING entry_id",
        ['d' => $tanggal, 's' => $shiftId, 'g' => $stageId]
    );
    $entryId = $ins[0]['entry_id'];
}

// ================= SIMPAN APPROVE =================
$approve = $_POST['approve'] ?? [];
$approveForeman    = isset($approve[0]) ? (int) $approve[0] : null;
$approveSupervisor = isset($approve[1]) ? (int) $approve[1] : null;
$approveManager    = isset($approve[2]) ? (int) $approve[2] : null;

if ($approveForeman !== null || $approveSupervisor !== null || $approveManager !== null) {
    db_execute(
        "UPDATE pmc_entry
         SET approve_foreman = COALESCE(:af, approve_foreman),
             approve_supervisor = COALESCE(:as_, approve_supervisor),
             approve_manager = COALESCE(:am, approve_manager),
             updated_at = now()
         WHERE entry_id = :id",
        [
            'af' => $approveForeman === null ? null : ($approveForeman === 1),
            'as_' => $approveSupervisor === null ? null : ($approveSupervisor === 1),
            'am' => $approveManager === null ? null : ($approveManager === 1),
            'id' => $entryId,
        ]
    );
}

// ================= SIMPAN DETAIL =================
$rowIds     = $_POST['row_id']     ?? [];
$mesinCodes = $_POST['mesin']      ?? [];
$partNos    = $_POST['cf_no']      ?? [];
$mataBor    = $_POST['mata_bor']   ?? [];
$ukuranBor  = $_POST['ukuran_bor'] ?? [];
$jam        = $_POST['jam']        ?? [];
$target     = $_POST['target_row'] ?? [];
$actual     = $_POST['actual']     ?? [];
$ng         = $_POST['ng']         ?? [];
$operator   = $_POST['operator']   ?? [];
$keterangan = $_POST['keterangan'] ?? [];
$validasi   = $_POST['validasi']   ?? [];

$userRole      = $_SESSION['role'] ?? '';
$canEditLocked = in_array($userRole, ['supervisor', 'manager'], true);

// cache machine_id lookup
$machineCache = [];
function resolveMachineId($code, &$cache)
{
    global $pdo;
    if (isset($cache[$code])) return $cache[$code];
    $r = db_select("SELECT machine_id FROM machine WHERE machine_code = :c LIMIT 1", ['c' => $code]);
    $cache[$code] = $r[0]['machine_id'] ?? null;
    return $cache[$code];
}

// cache operator_id lookup (cari berdasarkan nama, create kalau belum ada)
function resolveOperatorId($name)
{
    $name = trim($name);
    if ($name === '') return null;
    $r = db_select("SELECT operator_id FROM operator WHERE operator_name ILIKE :n LIMIT 1", ['n' => $name]);
    if (!empty($r)) return $r[0]['operator_id'];
    $code = 'OP-' . strtoupper(substr(md5($name . microtime()), 0, 6));
    $ins = db_select(
        "INSERT INTO operator (operator_code, operator_name) VALUES (:c, :n) RETURNING operator_id",
        ['c' => $code, 'n' => $name]
    );
    return $ins[0]['operator_id'] ?? null;
}

// cache part_id lookup berdasarkan part_number
function resolvePartId($partno)
{
    $partno = trim($partno);
    if ($partno === '') return null;
    $r = db_select("SELECT part_id FROM part WHERE part_number = :p LIMIT 1", ['p' => $partno]);
    return $r[0]['part_id'] ?? null;
}

for ($i = 0; $i < count($rowIds); $i++) {
    $rowNo = (int) ($rowIds[$i] ?? $i);

    $existing = db_select(
        "SELECT detail_id, is_checked FROM pmc_entry_detail WHERE entry_id = :e AND row_no = :r",
        ['e' => $entryId, 'r' => $rowNo]
    );
    $isExist = !empty($existing);
    $wasChecked = $isExist && ($existing[0]['is_checked'] === 't' || $existing[0]['is_checked'] === true);

    // Kalau row sudah checked dan user bukan supervisor/manager, skip (tidak boleh edit)
    if ($wasChecked && !$canEditLocked) continue;

    $valPart     = trim($partNos[$i]    ?? '');
    $valActual   = trim($actual[$i]     ?? '');
    $valNg       = trim($ng[$i]         ?? '');
    $valOperator = trim($operator[$i]   ?? '');
    $valKet      = trim($keterangan[$i] ?? '');
    $isChecked   = isset($validasi[$rowNo]) && (int) $validasi[$rowNo] === 1;

    $hasData = ($valPart !== '' || $valActual !== '' || $valNg !== '' || $valOperator !== '' || $valKet !== '' || $isChecked);
    if (!$hasData && !$isExist) continue;

    $machineId  = resolveMachineId($mesinCodes[$i] ?? '', $machineCache);
    $partId     = resolvePartId($valPart);
    $operatorId = resolveOperatorId($valOperator);

    $checkedBy = null;
    $checkedAt = null;
    if ($isChecked) {
        $checkedBy = $_SESSION['user_id'];
        $checkedAt = date('Y-m-d H:i:s');
    }

    db_execute(
        "INSERT INTO pmc_entry_detail
            (entry_id, row_no, machine_id, part_id, operator_id, target_qty, actual_qty, reject_qty,
             jumlah_mata_bor, ukuran_bor, keterangan, jam_operasional,
             is_checked, checked_by, checked_at, updated_at)
         VALUES
            (:entry_id, :row_no, :machine_id, :part_id, :operator_id, :target_qty, :actual_qty, :reject_qty,
             :jumlah_mata_bor, :ukuran_bor, :keterangan, :jam_operasional,
             :is_checked, :checked_by, :checked_at, now())
         ON CONFLICT (entry_id, row_no) DO UPDATE SET
            machine_id       = EXCLUDED.machine_id,
            part_id          = EXCLUDED.part_id,
            operator_id      = EXCLUDED.operator_id,
            target_qty       = EXCLUDED.target_qty,
            actual_qty       = EXCLUDED.actual_qty,
            reject_qty       = EXCLUDED.reject_qty,
            jumlah_mata_bor  = EXCLUDED.jumlah_mata_bor,
            ukuran_bor       = EXCLUDED.ukuran_bor,
            keterangan       = EXCLUDED.keterangan,
            jam_operasional  = EXCLUDED.jam_operasional,
            is_checked       = EXCLUDED.is_checked,
            checked_by       = COALESCE(EXCLUDED.checked_by, pmc_entry_detail.checked_by),
            checked_at       = COALESCE(EXCLUDED.checked_at, pmc_entry_detail.checked_at),
            updated_at       = now()",
        [
            'entry_id'        => $entryId,
            'row_no'          => $rowNo,
            'machine_id'      => $machineId,
            'part_id'         => $partId,
            'operator_id'     => $operatorId,
            'target_qty'      => (int) ($target[$i] ?: 0),
            'actual_qty'      => (int) ($valActual ?: 0),
            'reject_qty'      => (int) ($valNg ?: 0),
            'jumlah_mata_bor' => $mataBor[$i] ?? '',
            'ukuran_bor'      => $ukuranBor[$i] ?? '',
            'keterangan'      => $valKet,
            'jam_operasional' => $jam[$i] ?? '',
            'is_checked'      => $isChecked,
            'checked_by'      => $checkedBy,
            'checked_at'      => $checkedAt,
        ]
    );
}

echo json_encode(['status' => 'success']);
