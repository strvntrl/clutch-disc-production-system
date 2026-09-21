<?php

/**
 * pmc/auth/auth_guard.php
 *
 * Include file ini di baris paling atas tiap halaman PMC yang wajib
 * login. Kalau belum ada session, redirect ke halaman login.
 *
 * Cara pakai (dari pmc/index.php atau pmc/view/xxx.php):
 *   session_start();
 *   include __DIR__ . '/../auth/auth_guard.php';   // sesuaikan path
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Hitung path relatif ke auth/login.php dari lokasi file yang meng-include ini.
    // Default: asumsikan dipanggil dari pmc/index.php atau pmc/view/*.php
    $depth = isset($GLOBALS['AUTH_GUARD_DEPTH']) ? $GLOBALS['AUTH_GUARD_DEPTH'] : 0;
    $prefix = str_repeat('../', $depth);
    header("Location: {$prefix}auth/login.php");
    exit;
}

/**
 * Helper: batasi akses berdasarkan role.
 * Contoh: require_role(['supervisor', 'manager']);
 */
function require_role(array $allowedRoles): void
{
    if (!in_array($_SESSION['role'] ?? '', $allowedRoles, true)) {
        http_response_code(403);
        die('Akses ditolak: role Anda (' . htmlspecialchars($_SESSION['role'] ?? '-') . ') tidak berwenang untuk halaman ini.');
    }
}