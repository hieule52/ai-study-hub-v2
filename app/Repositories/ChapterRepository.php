<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ChapterRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): ?array
    {
        $sql = "INSERT INTO chapters (course_id, title, order_index) VALUES (:course_id, :title, :order_index)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'order_index' => $data['order_index'] ?? 0
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM chapters WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $data : null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE chapters SET title = :title, order_index = :order_index WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'order_index' => $data['order_index'] ?? 0
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE chapters SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
