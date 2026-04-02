<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\MessageModel;

class ChatSocket implements MessageComponentInterface
{
    protected $clients = [];
    protected $userConnections = [];
    protected $messageModel;


    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if ($data["type"] == "register") {
            $userId = $data["user_id"];
            $this->userConnections[$userId] = $from;
            return;
        }

        if ($data["type"] == "message") {
            $sender = $data["sender_id"];
            $receiver = $data["receiver_id"];
            $content = $data["content"];

            // Lưu DB
            $this->messageModel->sendMessage($sender, $receiver, $content);

            // Gửi lại cho chính sender để hiển thị ngay
            $from->send(json_encode([
                "type" => "message",
                "sender_id" => $sender,
                "receiver_id" => $receiver,
                "content" => $content
            ]));

            // Nếu receiver đang online → send real-time
            if (isset($this->userConnections[$receiver])) {
                $this->userConnections[$receiver]->send(json_encode([
                    "type" => "message",
                    "sender_id" => $sender,
                    "receiver_id" => $receiver,
                    "content" => $content
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
        foreach ($this->userConnections as $userId => $client) {
            if ($client === $conn) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
