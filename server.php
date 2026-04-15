<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;
use App\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

// Load biến môi trường cho Database
Env::load(__DIR__ . '/.env');

$port = $_ENV['WS_PORT'] ?? 8080;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    $port
);

echo "AI Study Hub WebSocket Live Chat đang chạy ở cổng {$port}...\n";

$server->run();
