<?php
session_start();
include(__DIR__ . "/../../../database.php");

date_default_timezone_set('Asia/Jakarta');

// ================= ERROR HANDLER =================
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    while (ob_get_level() > 0) ob_end_clean();
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

// ================= KONVERSI SHIFT =================
function convertShift($s)
{
    if ($s === 'Shift-1') return 1;
    if ($s === 'Shift-2') return 2;
    return 3;
}

// ================= INPUT =================
$shiftText = $_POST['shift']   ?? 'Shift-1';
$tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
$shift     = convertShift($shiftText);
$seksi     = 'HOTPRESS';
$kode_form = 'HOTPRESS';

$targetPost = (isset($_POST['target']) && $_POST['target'] !== '')
    ? intval($_POST['target'])
    : null;

$userRole      = $_SESSION['role'] ?? '';
$canEditLocked = ($userRole === 'supervisor' || $userRole === 'ass_manager');
$canEditTarget = $canEditLocked;

// ================= APPROVE =================
$approveArr        = $_POST['approve']  ?? [];
$approveForeman    = isset($approveArr[0]) ? intval($approveArr[0]) : null;
$approveSupervisor = isset($approveArr[1]) ? intval($approveArr[1]) : null;
$approveAssmanager = isset($approveArr[2]) ? intval($approveArr[2]) : null;

// ================= ROWS =================
$rowIds      = $_POST['row_id']      ?? [];
$noWosList   = $_POST['no_wos']      ?? [];
$mcNoList    = $_POST['mc_no']       ?? [];
$typeList    = $_POST['type']        ?? [];
$cfNoList    = $_POST['cf_no']       ?? [];
$custList    = $_POST['customer']    ?? [];
$beratList   = $_POST['berat_pcs']   ?? [];
$hasilKgList = $_POST['hasil_kg']    ?? [];
$baikList    = $_POST['baik']        ?? [];
$rusakList   = $_POST['rusak']       ?? [];
$totalList   = $_POST['total_hasil'] ?? [];
$reffList    = $_POST['reff_no']     ?? [];
$startList   = $_POST['start']       ?? [];
$finishList  = $_POST['finish']      ?? [];
$opList      = $_POST['operator']    ?? [];
$ketList     = $_POST['keterangan']  ?? [];
$valList     = $_POST['validasi']    ?? [];

// ================= CEK / BUAT FORM =================
$form_id = null;

$checkFormSQL = "
    SELECT id FROM pmc_form
    WHERE CONVERT(date, tanggal) = '$tanggal'
    AND   shift = $shift
    AND   seksi = '$seksi'
";
$checkForm = odbc_exec($con, $checkFormSQL);

if ($checkForm && odbc_fetch_row($checkForm)) {
    $form_id = odbc_result($checkForm, 'id');
} else {
    $insertFormSQL = "
        INSERT INTO pmc_form (kode_form, tanggal, shift, seksi, created_at, status)
        VALUES ('$kode_form', '$tanggal', $shift, '$seksi', GETDATE(), 0)
    ";
    odbc_exec($con, $insertFormSQL);

    $getId = odbc_exec($con, "SELECT @@IDENTITY AS id");
    odbc_fetch_row($getId);
    $form_id = odbc_result($getId, 'id');

    if (empty($form_id)) {
        die(json_encode(['status' => 'error', 'message' => 'Gagal membuat form_id baru']));
    }
}

// ================= SIMPAN APPROVE =================
if ($approveForeman !== null || $approveSupervisor !== null || $approveAssmanager !== null) {
    $setClauses = [];
    if ($approveForeman    !== null) $setClauses[] = "approve_foreman    = $approveForeman";
    if ($approveSupervisor !== null) $setClauses[] = "approve_supervisor = $approveSupervisor";
    if ($approveAssmanager !== null) $setClauses[] = "approve_assmanager = $approveAssmanager";

    if (!empty($setClauses)) {
        $approveSQL = "UPDATE pmc_form SET " . implode(', ', $setClauses) . " WHERE id = $form_id";
        odbc_exec($con, $approveSQL);
    }
}

// ================= SIMPAN TARGET =================
if ($canEditTarget && $targetPost !== null) {
    odbc_exec($con, "UPDATE pmc_form SET target = $targetPost WHERE id = $form_id");
    odbc_exec($con, "UPDATE pmc_hotpress SET target = $targetPost WHERE form_id = $form_id");
}

// ================= AMBIL TARGET AKTIF =================
function getActiveTarget($con, $form_id, $overrideTarget = null)
{
    if ($overrideTarget !== null) return $overrideTarget;
    $sql = "SELECT TOP 1 target FROM pmc_hotpress WHERE form_id = $form_id AND target > 0";
    $res = odbc_exec($con, $sql);
    if ($res && odbc_fetch_row($res)) return intval(odbc_result($res, 'target'));
    return 0;
}
$activeTarget = getActiveTarget($con, $form_id, $targetPost);

// ================= LOOP SIMPAN DETAIL =================
for ($i = 0; $i < count($rowIds); $i++) {
    $rowId = intval($rowIds[$i]);

    $val_no_wos   = addslashes(trim($noWosList[$i]   ?? ''));
    $val_mc_no    = addslashes(trim($mcNoList[$i]    ?? ''));
    $val_type     = addslashes(trim($typeList[$i]    ?? ''));
    $val_cf_no    = addslashes(trim($cfNoList[$i]    ?? ''));
    $val_customer = addslashes(trim($custList[$i]    ?? ''));
    $val_berat    = trim($beratList[$i]   ?? '');
    $val_hasil_kg = trim($hasilKgList[$i] ?? '');
    $val_baik     = intval($baikList[$i]  ?? 0);
    $val_rusak    = intval($rusakList[$i] ?? 0);
    $val_total    = intval($totalList[$i] ?? 0);
    $val_reff     = addslashes(trim($reffList[$i]   ?? ''));
    $val_start    = addslashes(trim($startList[$i]  ?? ''));
    $val_finish   = addslashes(trim($finishList[$i] ?? ''));
    $val_operator = addslashes(trim($opList[$i]     ?? ''));
    $val_ket      = addslashes(trim($ketList[$i]    ?? ''));
    $isChecked    = isset($valList[$rowId]) && intval($valList[$rowId]) === 1;

    $hasData = (
        $val_cf_no    !== '' ||
        $val_no_wos   !== '' ||
        $val_operator !== '' ||
        $val_baik      > 0   ||
        $val_rusak     > 0   ||
        $isChecked
    );

    // Cek apakah row sudah ada di DB
    $checkRowSQL = "
        SELECT * FROM pmc_hotpress
        WHERE form_id = $form_id AND row_id = $rowId
    ";
    $resRow  = odbc_exec($con, $checkRowSQL);
    $isExist = false;
    $db      = [];

    if ($resRow && ($dbRow = odbc_fetch_array($resRow))) {
        $isExist = true;
        $db      = $dbRow;
    }

    if (!$hasData && !$isExist) continue;
    if ($isExist && intval($db['validasi']) === 1 && !$canEditLocked) continue;

    // Nilai validasi
    if ($isChecked) {
        $val_validasi = 1;
    } elseif ($isExist) {
        $val_validasi = intval($db['validasi']);
    } else {
        $val_validasi = 0;
    }

    // Target per row
    $val_target = $activeTarget > 0
        ? $activeTarget
        : ($isExist ? intval($db['target'] ?? 0) : 0);

    // Handle nullable
    $beratVal   = ($val_berat   !== '' && floatval($val_berat)    > 0) ? floatval($val_berat)    : 'NULL';
    $hasilKgVal = ($val_hasil_kg !== '' && floatval($val_hasil_kg) > 0) ? floatval($val_hasil_kg) : 'NULL';
    $startVal   = ($val_start  !== '') ? "'$val_start'"  : 'NULL';
    $finishVal  = ($val_finish !== '') ? "'$val_finish'" : 'NULL';

    if ($isExist) {
        $sql = "
            UPDATE pmc_hotpress SET
                no_wos        = '$val_no_wos',
                mc_no         = '$val_mc_no',
                type          = '$val_type',
                cf_no         = '$val_cf_no',
                customer      = '$val_customer',
                berat_pcs     = $beratVal,
                hasil_kg      = $hasilKgVal,
                baik          = $val_baik,
                rusak         = $val_rusak,
                total_hasil   = $val_total,
                reff_no       = '$val_reff',
                start_proses  = $startVal,
                finish_proses = $finishVal,
                operator      = '$val_operator',
                keterangan    = '$val_ket',
                validasi      = $val_validasi,
                target        = $val_target
            WHERE form_id = $form_id AND row_id = $rowId
        ";
    } else {
        $sql = "
            INSERT INTO pmc_hotpress (
                form_id, row_id, shift,
                no_wos, mc_no, type, cf_no, customer,
                berat_pcs, hasil_kg, baik, rusak, total_hasil,
                reff_no, start_proses, finish_proses,
                operator, keterangan, validasi, target, created_at
            ) VALUES (
                $form_id, $rowId, $shift,
                '$val_no_wos', '$val_mc_no', '$val_type', '$val_cf_no', '$val_customer',
                $beratVal, $hasilKgVal, $val_baik, $val_rusak, $val_total,
                '$val_reff', $startVal, $finishVal,
                '$val_operator', '$val_ket', $val_validasi, $val_target, GETDATE()
            )
        ";
    }

    $exec = odbc_exec($con, $sql);
    if (!$exec) {
        error_log("HOTPRESS SQL ERROR row_id=$rowId: " . odbc_errormsg($con));
        continue;
    }

    // ================= SYNC KE dbo.php =================
    if ($exec && $val_validasi === 1 && $val_cf_no !== '') {

        $cf_safe = $val_cf_no;

        $cfSQL = "
            SELECT TOP 1
                [ITEM ID] AS item_id,
                [CUST ID] AS cust_id,
                [WEIGHT]  AS weight
            FROM cf
            WHERE LTRIM(RTRIM(PARTNO)) = '$cf_safe'
        ";
        $cfRes = odbc_exec($con, $cfSQL);

        $item_id   = '';
        $cust_id   = '';
        $cf_weight = 0;

        if ($cfRes && odbc_fetch_row($cfRes)) {
            $item_id   = addslashes(odbc_result($cfRes, 'item_id') ?? '');
            $cust_id   = addslashes(odbc_result($cfRes, 'cust_id') ?? '');
            $cf_weight = floatval(odbc_result($cfRes, 'weight') ?? 0);
        }

        $weight_ok     = $val_baik  * $cf_weight;
        $weight_reject = $val_rusak * $cf_weight;
        $wo_number     = $form_id . '-' . $rowId;
        $mesin_num     = $val_mc_no !== '' ? $val_mc_no : ($rowId + 1);
        $opr_safe      = $val_operator;
        $item_safe     = $item_id;
        $cust_safe     = $cust_id;
        $now           = date('Y-m-d H:i:s');

        $checkPhpSQL = "
            SELECT TOP 1 ID
            FROM sync_produksi
            WHERE WO_NUMBER = '$wo_number'
            AND   SECTION   = 'HOTPRESS'
            AND   SHIFT     = $shift
        ";
        $checkPhpRes = odbc_exec($con, $checkPhpSQL);
        $phpExists   = ($checkPhpRes && odbc_fetch_row($checkPhpRes));

        if ($phpExists) {
            $phpSQL = "
                UPDATE sync_produksi SET
                    PHP            = NULL,
                    SECTION        = 'HOTPRESS',
                    MESIN          = '$mesin_num',
                    [DATE]         = '$tanggal',
                    [SHIFT]        = $shift,
                    PARTNUMBER     = '$cf_safe',
                    [ITEM ID]      = '$item_safe',
                    [CUST ID]      = '$cust_safe',
                    OK             = $val_baik,
                    REJECT         = $val_rusak,
                    WEIGHT_OK      = $weight_ok,
                    WEIGHT_REJECT  = $weight_reject,
                    OPR_NAME       = '$opr_safe',
                    [CREATED BY]   = '$opr_safe',
                    [LAST UPDATED] = '$now',
                    [UPDATED BY]   = '$opr_safe',
                    [STATUS]       = 'VALIDATED'
                WHERE WO_NUMBER = '$wo_number'
                AND   SECTION   = 'HOTPRESS'
                AND   SHIFT     = $shift
            ";
        } else {
            $phpSQL = "
                INSERT INTO sync_produksi (
                    PHP, SECTION, MESIN, [DATE], [SHIFT],
                    PARTNUMBER, [ITEM ID], [CUST ID],
                    OK, REJECT,
                    WEIGHT_OK, WEIGHT_REJECT,
                    OPR_NAME, [CREATED BY], [CREATED DATE],
                    [LAST UPDATED], [UPDATED BY],
                    WO_NUMBER, [STATUS]
                ) VALUES (
                    NULL, 'HOTPRESS', '$mesin_num', '$tanggal', $shift,
                    '$cf_safe', '$item_safe', '$cust_safe',
                    $val_baik, $val_rusak,
                    $weight_ok, $weight_reject,
                    '$opr_safe', '$opr_safe', '$now',
                    '$now', '$opr_safe',
                    '$wo_number', 'VALIDATED'
                )
            ";
        }

        $phpExec = odbc_exec($con, $phpSQL);
        if (!$phpExec) {
            error_log("HOTPRESS SYNC PHP ERROR row_id=$rowId: " . odbc_errormsg($con));
        }
    }
}

echo "SUCCESS";