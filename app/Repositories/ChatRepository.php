<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ChatRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function saveMessage(int $senderId, int $receiverId, string $content): bool
    {
        $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (:sender, :receiver, :content)");
        return $stmt->execute([
            'sender' => $senderId,
            'receiver' => $receiverId,
            'content' => $content
        ]);
    }
}
