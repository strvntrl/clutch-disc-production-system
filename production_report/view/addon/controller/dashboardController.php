<?php

include(__DIR__ . "/../../../database.php");

$seksiList = db_select(
    "SELECT stage_id AS id_seksi, stage_name AS nama_seksi
     FROM process_stage
     WHERE stage_code IN ('DRILLING', 'HOTPRESS', 'PREFORMING')
     ORDER BY sequence_order"
);

if (!isset($selected_seksi)) {
    $selected_seksi = isset($_GET['seksi']) && is_array($_GET['seksi'])
        ? $_GET['seksi']
        : [];
}

if (!is_array($selected_seksi)) {
    $selected_seksi = $selected_seksi !== '' ? [$selected_seksi] : [];
}

$selected_seksi = array_filter($selected_seksi, fn($v) => is_numeric($v) && (int) $v > 0);
$selected_seksi = array_values($selected_seksi);

$selectedSeksiName = '';
if (!empty($selected_seksi)) {
    $id = (int) $selected_seksi[0];
    foreach ($seksiList as $s) {
        if ((int) $s['id_seksi'] === $id) {
            $selectedSeksiName = strtoupper($s['nama_seksi']);
            break;
        }
    }
}