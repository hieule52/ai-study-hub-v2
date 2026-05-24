<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Core\JWTHandler;
use App\Repositories\ChatRepository;
use App\Repositories\NotificationRepository;
use App\Core\Database;
use Exception;

class Chat implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;
    // resourceId => ['user_id' => int, 'role' => string, 'username' => string]
    protected array $connMeta = [];
    private ChatRepository $chatRepo;
    private NotificationRepository $notifRepo;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->chatRepo = new ChatRepository();
        $this->notifRepo = new NotificationRepository();
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

        $userId = (int)$tokenPayload->sub;
        $role   = $tokenPayload->role ?? 'student';

        // === SECURITY: Only allow student and teacher roles in chat ===
        if (!in_array($role, ['student', 'teacher'])) {
            $conn->send(json_encode(['error' => 'Admin không sử dụng phòng chat này']));
            $conn->close();
            return;
        }

        // Lấy username và avatar từ DB
        $userInfo = $this->getUserInfoFromDb($userId);

        // Cập nhật last_seen trong DB khi user kết nối WS
        try {
            $db = Database::connect();
            $db->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?")->execute([$userId]);
        } catch (\Exception $e) {
            echo "[WS] Lỗi cập nhật last_seen khi connect: " . $e->getMessage() . "\n";
        }

        $this->clients->attach($conn);
        $this->connMeta[$conn->resourceId] = [
            'user_id'  => $userId,
            'role'     => $role,
            'username' => $userInfo['username'],
            'avatar'   => $userInfo['avatar'],
        ];

        $conn->send(json_encode([
            'type'    => 'connected',
            'message' => "Chào {$userInfo['username']}! Bạn đã kết nối thành công.",
            'user_id' => $userId,
        ]));

        echo "[WS] User #{$userId} ({$role}: {$userInfo['username']}) kết nối (#{$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $senderMeta = $this->connMeta[$from->resourceId] ?? null;
        if (!$senderMeta) {
            $from->send(json_encode(['error' => 'Phiên kết nối không hợp lệ']));
            return;
        }

        $senderId   = $senderMeta['user_id'];
        $senderRole = $senderMeta['role'];
        $data       = json_decode($msg, true);

        if (!isset($data['receiver_id']) || !isset($data['content'])) {
            $from->send(json_encode(['error' => 'Thiếu receiver_id hoặc content']));
            return;
        }

        $receiverId = (int)$data['receiver_id'];
        $content    = strip_tags(trim($data['content']));

        if (empty($content)) return;
        if (mb_strlen($content) > 2000) {
            $from->send(json_encode(['error' => 'Tin nhắn quá dài (tối đa 2000 ký tự)']));
            return;
        }

        // === SECURITY: Enforce Student ↔ Teacher only chat ===
        $receiverRole = $this->getRoleById($receiverId);

        if ($senderRole === 'student' && $receiverRole !== 'teacher') {
            $from->send(json_encode(['error' => 'Học viên chỉ có thể nhắn tin cho Giảng viên']));
            return;
        }

        if ($senderRole === 'teacher' && $receiverRole !== 'student') {
            $from->send(json_encode(['error' => 'Giảng viên chỉ có thể nhắn tin cho Học viên']));
            return;
        }

        // Lưu vào database
        $this->chatRepo->saveMessage($senderId, $receiverId, $content);

        // Cập nhật last_seen trong DB khi user gửi tin nhắn qua WS
        try {
            $db = Database::connect();
            $db->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?")->execute([$senderId]);
        } catch (\Exception $e) {
            echo "[WS] Lỗi cập nhật last_seen khi gửi tin nhắn: " . $e->getMessage() . "\n";
        }

        // Tạo thông báo mới cho người nhận
        try {
            $this->notifRepo->create($receiverId, 'chat', $senderMeta['username'], $content, [
                'sender_id' => $senderId
            ]);
        } catch (Exception $notifEx) {
            echo "[WS] Không thể tạo thông báo: " . $notifEx->getMessage() . "\n";
        }

        $payload = json_encode([
            'type'          => 'message',
            'sender_id'     => $senderId,
            'sender_name'   => $senderMeta['username'],
            'sender_avatar' => $senderMeta['avatar'] ?? null,
            'content'       => $content,
            'time'          => date('Y-m-d H:i:s'),
        ]);

        // Gửi realtime tới receiver nếu đang online
        $delivered = false;
        foreach ($this->clients as $client) {
            $clientMeta = $this->connMeta[$client->resourceId] ?? null;
            if ($clientMeta && $clientMeta['user_id'] === $receiverId) {
                $client->send($payload);
                $delivered = true;
            }
        }

        // Xác nhận đã gửi cho người gửi
        $from->send(json_encode([
            'type'      => 'sent',
            'content'   => $content,
            'delivered' => $delivered,
            'time'      => date('Y-m-d H:i:s'),
        ]));
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        if (isset($this->connMeta[$conn->resourceId])) {
            $meta = $this->connMeta[$conn->resourceId];
            unset($this->connMeta[$conn->resourceId]);
            echo "[WS] User #{$meta['user_id']} ({$meta['username']}) đã ngắt kết nối (#{$conn->resourceId})\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "[WS] Lỗi: {$e->getMessage()}\n";
        $conn->close();
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    private function getUserInfoFromDb(int $userId): array
    {
        try {
            $db   = Database::connect();
            $stmt = $db->prepare("SELECT username, avatar FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return [
                'username' => $row['username'] ?? "User #{$userId}",
                'avatar'   => $row['avatar'] ?? null,
            ];
        } catch (\Exception $e) {
            return ['username' => "User #{$userId}", 'avatar' => null];
        }
    }

    private function getRoleById(int $userId): string
    {
        // Check in-memory connections first (fast path)
        foreach ($this->connMeta as $meta) {
            if ($meta['user_id'] === $userId) {
                return $meta['role'];
            }
        }

        // Fallback to DB
        try {
            $db   = Database::connect();
            $stmt = $db->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['role'] ?? 'unknown';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
}
