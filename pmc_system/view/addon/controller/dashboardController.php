<?php
include(__DIR__ . "/../../../database.php");

$query = "SELECT id_seksi, nama_seksi FROM dbo.seksi";
$result = odbc_exec($con, $query);

$seksiList = [];

$selectedSeksiName = '';

if (!empty($_GET['seksi'])) {
    $id = $_GET['seksi'];

    $query2 = "SELECT nama_seksi FROM dbo.seksi WHERE id_seksi = '$id'";
    $result2 = odbc_exec($con, $query2);

    if ($row = odbc_fetch_array($result2)) {
        $selectedSeksiName = strtoupper($row['nama_seksi']);
    }
}

while ($row = odbc_fetch_array($result)) {
    $seksiList[] = $row;
}

$selectedSeksi = $_GET['seksi'] ?? '';
