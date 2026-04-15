<?php

use App\Core\Env;
use App\Core\Router;
use App\Core\Request;
use App\Core\Response;

// Bật CORS cho toàn bộ App
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require_once __DIR__ . '/../vendor/autoload.php';

// Load biến môi trường
if (file_exists(__DIR__ . '/../.env')) {
    Env::load(__DIR__ . '/../.env');
}

// Fix cho PHP Built-in Server: Nếu là file vật lý thì trả về file đó
$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . $uriPath)) {
    return false;
}

// Nếu người dùng truy cập trang gốc '/', tự động đọc file giao diện HTML
if ($uriPath === '/' || $uriPath === '') {
    readfile(__DIR__ . '/index.html');
    exit;
}

// Khởi tạo Core objects cho Backend API
$request = new Request();
$response = new Response();
$router = new Router();

// Nạp các khai báo routes từ api.php
$apiRoutesPath = __DIR__ . '/../routes/api.php';
if (file_exists($apiRoutesPath)) {
    require_once $apiRoutesPath;
} else {
    $response->error('Missing api routes configuration', 500);
}

// Điều hướng request
$router->dispatch($request, $response);
