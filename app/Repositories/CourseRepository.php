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
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, cc.slug as category_slug, u.username as teacher_name
            FROM courses c
            LEFT JOIN course_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE c.status = 'approved' AND c.deleted_at IS NULL
            ORDER BY c.id DESC LIMIT :limit OFFSET :offset
        ");
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
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, cc.slug as category_slug, u.username as teacher_name, u.email as teacher_email
            FROM courses c
            LEFT JOIN course_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE c.id = :id AND c.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Course((array)$data);
        }
        return null;
    }

    public function create(array $data): ?Course
    {
        $sql = "INSERT INTO courses (teacher_id, category_id, level, estimated_duration, title, description, thumbnail, price, is_premium, status, ai_course_summary, ai_keywords, ai_focus) 
                VALUES (:teacher_id, :category_id, :level, :estimated_duration, :title, :description, :thumbnail, :price, :is_premium, :status, :ai_summary, :ai_keywords, :ai_focus)";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'teacher_id' => $data['teacher_id'],
            'category_id' => $data['category_id'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'estimated_duration' => $data['estimated_duration'] ?? 0,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $data['price'] ?? 0,
            'is_premium' => $data['is_premium'] ?? 0,
            'status' => $data['status'] ?? 'pending',
            'ai_summary' => $data['ai_course_summary'] ?? null,
            'ai_keywords' => $data['ai_keywords'] ?? null,
            'ai_focus' => $data['ai_focus'] ?? null
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
                COALESCE((SELECT COUNT(e.id) FROM enrollments e JOIN courses c2 ON e.course_id = c2.id WHERE c2.teacher_id = :t1), 0) as total_students
            FROM courses c
            WHERE c.teacher_id = :t2 AND c.deleted_at IS NULL
        ");
        $stmt->execute(['t1' => $teacherId, 't2' => $teacherId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$stats) {
            $stats = ['total_courses' => 0, 'total_students' => 0];
        }
        $stmtAvg = $this->db->prepare("
            SELECT AVG(cr.rating) 
            FROM course_reviews cr 
            JOIN courses c ON cr.course_id = c.id 
            WHERE c.teacher_id = :teacher_id AND c.deleted_at IS NULL
        ");
        $stmtAvg->execute(['teacher_id' => $teacherId]);
        $avg = $stmtAvg->fetchColumn();
        $stats['avg_rating'] = $avg ? number_format($avg, 1) : "0.0";
        
        return $stats;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE courses SET title = :title, description = :description, 
                thumbnail = COALESCE(:thumbnail, thumbnail), price = :price, 
                is_premium = :is_premium, category_id = :category_id,
                level = :level, estimated_duration = :estimated_duration,
                ai_course_summary = :ai_summary, ai_keywords = :ai_keywords, ai_focus = :ai_focus
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $data['price'] ?? 0,
            'is_premium' => $data['is_premium'] ?? 0,
            'category_id' => $data['category_id'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'estimated_duration' => $data['estimated_duration'] ?? 0,
            'ai_summary' => $data['ai_course_summary'] ?? null,
            'ai_keywords' => $data['ai_keywords'] ?? null,
            'ai_focus' => $data['ai_focus'] ?? null
        ]);
    }

    /**
     * Tìm kiếm khóa học theo từ khóa
     */
    public function search(string $keyword, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, u.username as teacher_name
            FROM courses c
            LEFT JOIN course_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE c.status = 'approved' AND c.deleted_at IS NULL
              AND (c.title LIKE :kw1 OR c.description LIKE :kw2)
            ORDER BY c.id DESC LIMIT :limit
        ");
        $kw = '%' . $keyword . '%';
        $stmt->bindValue(':kw1', $kw);
        $stmt->bindValue(':kw2', $kw);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm khóa học theo danh mục
     */
    public function findByCategory(int $categoryId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, u.username as teacher_name
            FROM courses c
            LEFT JOIN course_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE c.category_id = :cat_id AND c.status = 'approved'
            ORDER BY c.id DESC LIMIT :limit
        ");
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Auto-cập nhật total_lessons cho khóa học
     * Đếm tổng lessons active trong tất cả chapters
     */
    public function updateTotalLessons(int $courseId): bool
    {
        $sql = "UPDATE courses SET total_lessons = (
                    SELECT COUNT(*) FROM lessons l
                    JOIN chapters ch ON l.chapter_id = ch.id
                    WHERE ch.course_id = :cid AND l.deleted_at IS NULL AND ch.deleted_at IS NULL
                ) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['cid' => $courseId, 'id' => $courseId]);
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
            WHERE c.status IN ('pending', 'pending_reapproval') AND c.deleted_at IS NULL
            ORDER BY c.id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAdminCourses(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, u.username as teacher_name, u.email as teacher_email
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            WHERE c.deleted_at IS NULL
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPendingCourses(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM courses WHERE status IN ('pending', 'pending_reapproval') AND deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Count published (approved) courses for pagination
     */
    public function countApproved(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM courses WHERE status = 'approved' AND deleted_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE courses SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Mark course as pending_reapproval if it was approved.
     * 
     * LƯU Ý: Phương thức này CHỈ thay đổi status.
     * Notification + change log được xử lý bởi ChangeTrackingService::trackChange().
     * KHÔNG gọi phương thức này trực tiếp từ controllers — để ChangeTrackingService quản lý.
     */
    public function requireReapproval(int $courseId): bool
    {
        $stmt = $this->db->prepare("UPDATE courses SET status = 'pending_reapproval' WHERE id = :id AND status = 'approved'");
        return $stmt->execute(['id' => $courseId]);
    }

    public function getTotalRevenue(): float
    {
        // Calculate total revenue from enrollments (sum of course prices)
        $stmt = $this->db->query("
            SELECT SUM(c.price) 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id
        ");
        $total = $stmt->fetchColumn();
        return $total ? (float)$total : 0.0;
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        $sql = "
            SELECT 
                DATE_FORMAT(e.enrolled_at, '%Y-%m') as year_month,
                SUM(c.price) as revenue
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.enrolled_at >= DATE_SUB(CURRENT_DATE(), INTERVAL :months MONTH)
            GROUP BY year_month
            ORDER BY year_month ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':months', $months - 1, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        
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
