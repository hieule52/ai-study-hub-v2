<?php

// 1️⃣ Bắt buộc require autoload trước
require __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\ChatSocket;

// 2️⃣ Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3️⃣ Tạo server WebSocket
$port = $_ENV['WEBSOCKET_PORT'] ?? 9090;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatSocket()
        )
    ),
    $port
);

echo "WebSocket server running on port {$port}...\n";
$server->run();
