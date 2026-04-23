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
        $sql = "INSERT INTO courses (teacher_id, title, description, thumbnail, price, is_premium, status) 
                VALUES (:teacher_id, :title, :description, :thumbnail, :price, :is_premium, :status)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'teacher_id' => $data['teacher_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $data['price'] ?? 0,
            'is_premium' => $data['is_premium'] ?? 0,
            'status' => $data['status'] ?? 'pending'
        ]);

        if ($success) {
            return $this->findById($this->db->lastInsertId());
        }
        return null;
    }

    public function findTeacherCourses(int $teacherId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as total_students 
            FROM courses c 
            WHERE c.teacher_id = :teacher_id AND c.deleted_at IS NULL 
            ORDER BY c.id DESC
        ");
        $stmt->execute(['teacher_id' => $teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherStats(int $teacherId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(c.id) as total_courses,
                COALESCE((SELECT COUNT(e.id) FROM enrollments e JOIN courses c2 ON e.course_id = c2.id WHERE c2.teacher_id = :t1 AND c2.deleted_at IS NULL), 0) as total_students
            FROM courses c
            WHERE c.teacher_id = :t2 AND c.deleted_at IS NULL
        ");
        $stmt->execute(['t1' => $teacherId, 't2' => $teacherId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$stats) {
            $stats = ['total_courses' => 0, 'total_students' => 0];
        }
        $stats['avg_rating'] = "4.8"; // Thay thế bằng bảng review nếu có
        return $stats;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE courses SET title = :title, description = :description, thumbnail = COALESCE(:thumbnail, thumbnail), price = :price, is_premium = :is_premium WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $data['price'] ?? 0,
            'is_premium' => $data['is_premium'] ?? 0,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE courses SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getPendingCourses(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, u.username as teacher_name, u.email as teacher_email
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            WHERE c.status = 'pending' AND c.deleted_at IS NULL
            ORDER BY c.id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPendingCourses(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM courses WHERE status = 'pending' AND deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE courses SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getTotalRevenue(): float
    {
        // Tính tổng doanh thu từ VIP payments đã hoàn thành
        $stmt = $this->db->query("SELECT SUM(amount) FROM vip_payments WHERE status = 'completed'");
        $total = $stmt->fetchColumn();
        return $total ? (float)$total : 0.0;
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        // Sử dụng DATE_FORMAT để nhóm theo 'YYYY-MM'
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as year_month,
                SUM(amount) as revenue
            FROM vip_payments
            WHERE status = 'completed' AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL :months MONTH)
            GROUP BY year_month
            ORDER BY year_month ASC
        ";
        $stmt = $this->db->prepare($sql);
        // Từ -5 đến 0 = 6 tháng
        $stmt->bindValue(':months', $months - 1, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        
        // Đúc và điền 0 cho những tháng không có doanh thu
        for ($i = $months - 1; $i >= 0; $i--) {
            $time = strtotime("-$i months");
            $yearMonth = date('Y-m', $time);
            $label = 'Tháng ' . date('n', $time);
            
            $revenue = 0;
            foreach ($results as $row) {
                if ($row['year_month'] === $yearMonth) {
                    $revenue = (float)$row['revenue'];
                    break;
                }
            }
            $labels[] = $label;
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
