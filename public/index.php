<?php
require_once __DIR__ . '/../config/database.php';

$requestUri = $_SERVER['REQUEST_URI'];
$path = rtrim(parse_url($requestUri, PHP_URL_PATH), '/') ?: '/';

if (!empty($_SERVER['QUERY_STRING'])) {
    header('Location: ' . $path);
    exit;
}

if (strpos($path, '/api/') === 0) {
    require __DIR__ . '/../api/index.php';
    exit;
}

if ($path === '/') {
    header('Location: /login');
    exit;
}

if ($path === '/login') {
    require __DIR__ . '/views/login.php';
} elseif ($path === '/register') {
    require __DIR__ . '/views/register.php';
} elseif ($path === '/home') {
    require __DIR__ . '/views/home.php';
} elseif ($path === '/logout') {
    session_destroy();
    header('Location: /login');
    exit;
} else {
    http_response_code(404);
    echo '404 Not Found';
}
