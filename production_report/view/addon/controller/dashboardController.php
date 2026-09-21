<?php
include(__DIR__ . "/../../../database.php");

if (!isset($con) || !$con) {
    die("Koneksi database tidak tersedia di dashboardController.");
}

$query = "SELECT id_seksi, nama_seksi FROM dbo.seksi ORDER BY nama_seksi ASC";
$result = odbc_exec($con, $query);

if (!$result) {
    die("Query seksi gagal: " . odbc_errormsg($con));
}

$seksiList = [];
while ($row = odbc_fetch_array($result)) {
    $seksiList[] = $row;
}

if (!isset($selected_seksi)) {
    $selected_seksi = isset($_GET['seksi']) && is_array($_GET['seksi'])
        ? $_GET['seksi']
        : [];
}

if (!is_array($selected_seksi)) {
    $selected_seksi = $selected_seksi !== '' ? [$selected_seksi] : [];
}

$selected_seksi = array_filter($selected_seksi, fn($v) => is_numeric($v) && (int)$v > 0);
$selected_seksi = array_values($selected_seksi);

$selectedSeksiName = '';
if (!empty($selected_seksi)) {
    $id = (int)$selected_seksi[0];
    foreach ($seksiList as $s) {
        if ((int)$s['id_seksi'] === $id) {
            $selectedSeksiName = strtoupper($s['nama_seksi']);
            break;
        }
    }
}
