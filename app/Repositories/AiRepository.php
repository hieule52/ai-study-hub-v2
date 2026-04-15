<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AiRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function saveInteraction(int $userId, string $message, string $response): bool
    {
        $stmt = $this->db->prepare("INSERT INTO ai_messages (user_id, message, response) VALUES (:uid, :msg, :res)");
        return $stmt->execute([
            'uid' => $userId,
            'msg' => $message,
            'res' => $response
        ]);
    }

    public function getHistory(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("SELECT * FROM ai_messages WHERE user_id = :uid ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        // Đảo ngược lại để chat render từ trên xuống dưới
        $results = $stmt->fetchAll();
        return array_reverse($results);
    }
}
