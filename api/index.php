<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri === '/' || $requestUri === '') {
    require __DIR__ . '/../index.php';
    exit;
}

$routes = [
    '/view/drilling.php' => __DIR__ . '/../view/drilling.php',
    '/view/hotpress.php' => __DIR__ . '/../view/hotpress.php',
    '/view/preforming.php' => __DIR__ . '/../view/preforming.php',

    '/pmc_system/index.php' => __DIR__ . '/../pmc_system/index.php',

    '/production_report/index.php' => __DIR__ . '/../production_report/index.php',
];

if (isset($routes[$requestUri])) {
    require $routes[$requestUri];
    exit;
}

http_response_code(404);
echo '404 - Halaman tidak ditemukan';