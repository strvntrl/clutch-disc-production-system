<?php
session_start();
include(__DIR__ . "/../../database.php"); // ../../ -> naik dari pmc/auth ke root project

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.php?error=1");
    exit;
}

$rows = db_select(
    "SELECT user_id, username, password_hash, full_name, role, is_active
     FROM app_user WHERE username = :u LIMIT 1",
    ['u' => $username]
);

if (empty($rows)) {
    header("Location: login.php?error=1");
    exit;
}

$user = $rows[0];

if (!password_verify($password, $user['password_hash'])) {
    header("Location: login.php?error=1");
    exit;
}

if ($user['is_active'] === 'f' || $user['is_active'] === false) {
    header("Location: login.php?error=2");
    exit;
}

// Login sukses
$_SESSION['user_id']   = $user['user_id'];
$_SESSION['username']  = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role']      = $user['role'];

header("Location: ../index.php");
exit;