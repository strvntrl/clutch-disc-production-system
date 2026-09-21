<?php

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$requestPath = rtrim($requestPath, '/') ?: '/';

// Daftar route yang diizinkan
$routes = [
    '/' => __DIR__ . '/../index.php',

    '/pmc_system' => __DIR__ . '/../pmc_system/index.php',

    '/pmc_system/auth/login.php' =>
        __DIR__ . '/../pmc_system/auth/login.php',

    '/pmc_system/auth/process_login.php' =>
        __DIR__ . '/../pmc_system/auth/process_login.php',

    '/pmc_system/view/hotpress.php' =>
        __DIR__ . '/../pmc_system/view/hotpress.php',

    '/pmc_system/view/drilling.php' =>
        __DIR__ . '/../pmc_system/view/drilling.php',

    '/pmc_system/view/preforming.php' =>
        __DIR__ . '/../pmc_system/view/preforming.php',

    '/view/hotpress.php' =>
        __DIR__ . '/../view/hotpress.php',

    '/view/drilling.php' =>
        __DIR__ . '/../view/drilling.php',

    '/view/preforming.php' =>
        __DIR__ . '/../view/preforming.php',

    '/production_report' =>
        __DIR__ . '/../production_report/index.php',
];

if (isset($routes[$requestPath])) {
    $file = $routes[$requestPath];

    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Halaman tidak ditemukan
http_response_code(404);
echo "404 - Halaman tidak ditemukan";