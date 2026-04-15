<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Core\JWTHandler;
use App\Repositories\ChatRepository;
use Exception;

class Chat implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;
    protected array $usersConnectionMap = [];
    private ChatRepository $chatRepo;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->chatRepo = new ChatRepository();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Phân tích connection string URL để lấy tham số token 
        // VD: ws://localhost:8080?token=ey...
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryArray);

        if (!isset($queryArray['token'])) {
            $conn->send(json_encode(['error' => 'Vui lòng truyền token JWT để xác thực']));
            $conn->close();
            return;
        }

        $tokenPayload = JWTHandler::decode($queryArray['token']);
        
        if (!$tokenPayload) {
            $conn->send(json_encode(['error' => 'Lỗi xác thực Token, kết nối bị từ chối']));
            $conn->close();
            return;
        }

        $userId = $tokenPayload['sub'];

        $this->clients->attach($conn);
        $this->usersConnectionMap[$conn->resourceId] = $userId;

        echo "User ID {$userId} đã kết nối mảng Socket (({$conn->resourceId}))\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $senderId = $this->usersConnectionMap[$from->resourceId];
        $data = json_decode($msg, true);

        if (!isset($data['receiver_id']) || !isset($data['content'])) {
            $from->send(json_encode(['error' => 'Thiếu receiver_id hoặc content']));
            return;
        }

        $receiverId = (int)$data['receiver_id'];
        $content = strip_tags($data['content']);

        // Lưu log database
        $this->chatRepo->saveMessage($senderId, $receiverId, $content);

        // Gửi mess tới người nhận Realtime
        foreach ($this->clients as $client) {
            if ($this->usersConnectionMap[$client->resourceId] === $receiverId) {
                // Send JSON stream format
                $client->send(json_encode([
                    'sender_id' => $senderId,
                    'content' => $content,
                    'time' => date('Y-m-d H:i:s')
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        if (isset($this->usersConnectionMap[$conn->resourceId])) {
            $userId = $this->usersConnectionMap[$conn->resourceId];
            unset($this->usersConnectionMap[$conn->resourceId]);
            echo "Kết nối {$conn->resourceId} (User $userId) đã ngắt.\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Cảnh báo lỗi Socket: {$e->getMessage()}\n";
        $conn->close();
    }
}
