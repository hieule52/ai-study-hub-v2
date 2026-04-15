<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Course;
use PDO;

class CourseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE deleted_at IS NULL AND status = 'approved' ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $courses = [];
        while ($row = $stmt->fetch()) {
            $courses[] = new Course((array)$row);
        }
        return $courses;
    }

    public function findById(int $id): ?Course
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Course((array)$data);
        }
        return null;
    }

    public function create(array $data): ?Course
    {
        $sql = "INSERT INTO courses (teacher_id, title, description, price, is_premium) 
                VALUES (:teacher_id, :title, :description, :price, :is_premium)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'teacher_id' => $data['teacher_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
            'is_premium' => $data['is_premium'] ?? 0
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }
}
