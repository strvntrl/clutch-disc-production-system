<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri === '/' || $requestUri === '') {
    require __DIR__ . '/../index.php';
    exit;
}

http_response_code(404);
echo "404 - Page not found";