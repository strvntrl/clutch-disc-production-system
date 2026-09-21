<?php
session_start();
include(__DIR__ . "/../database.php");

$id = $_POST['id'];
$status = $_POST['status'];
$nama = $_SESSION['nama'];

if ($status == 1) {

    mysqli_query($con, "
        UPDATE produksi
        SET validasi=1,
        checked_by='$nama',
        checked_at=NOW()
        WHERE id='$id'
    ");

    $ambil = mysqli_query($con, "
        SELECT checked_by, checked_at 
        FROM produksi 
        WHERE id='$id'
    ");

    $data = mysqli_fetch_assoc($ambil);

    echo json_encode([
        "nama" => $data['checked_by'],
        "waktu" => date("d-m-Y H:i", strtotime($data['checked_at']))
    ]);

} else {

    mysqli_query($con, "
        UPDATE produksi
        SET validasi=0,
        checked_by=NULL,
        checked_at=NULL
        WHERE id='$id'
    ");

    echo json_encode([
        "nama" => null,
        "waktu" => null
    ]);
}