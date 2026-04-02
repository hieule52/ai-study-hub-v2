<?php

use App\Core\Env;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env
Env::load(__DIR__ . '/../.env');

session_start();

// Tạo router
$router = new Router();

// Load routes
require_once __DIR__ . '/../routes/web.php';

// Run router
$router->run();
