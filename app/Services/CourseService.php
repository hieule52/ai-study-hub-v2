<?php

namespace App\Services;

use App\Core\Database;
use App\Repositories\CourseRepository;
use PDO;
use Exception;

/**
 * CourseService
 * Xử lý nghiệp vụ hoàn thành khóa học và cấp chứng chỉ.
 */
class CourseService
{
    private PDO $db;
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->courseRepo = new CourseRepository();
    }

    public function getAllCourses(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        return $this->courseRepo->findAll($limit, $offset);
    }

    public function getCourseDetail(int $id)
    {
        $course = $this->courseRepo->findById($id);
        if (!$course) {
            throw new Exception("Không tìm thấy khóa học.");
        }
        return $course;
    }

    public function createCourse(array $data, int $teacherId)
    {
        if (empty($data['title'])) {
            throw new Exception("Tiêu đề khóa học không được để trống.");
        }

        $price = isset($data['price']) ? (float)$data['price'] : 0;
        $is_premium = $price > 0 ? 1 : 0;

        $newCourse = $this->courseRepo->create([
            'teacher_id' => $teacherId,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $price,
            'is_premium' => $is_premium,
            'category_id' => $data['category_id'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'estimated_duration' => $data['estimated_duration'] ?? 0,
            'ai_course_summary' => $data['ai_course_summary'] ?? null,
            'ai_keywords' => $data['ai_keywords'] ?? null,
            'ai_focus' => $data['ai_focus'] ?? null
        ]);

        if (!$newCourse) {
            throw new Exception("Lỗi hệ thống khi tạo khóa học.");
        }

        return $newCourse;
    }

    /**
     * Kiểm tra và xử lý hoàn thành khóa học khi học viên hoàn thành bài học hoặc nộp quiz.
     * Trả về array với keys:
     *   'progress_percent', 'course_completed', 'certificate_issued', 'needs_review'
     */
    public function checkAndProcessCourseCompletion(int $userId, int $courseId): array
    {
        $totalLessons     = $this->countTotalLessons($courseId);
        $completedLessons = $this->countCompletedLessons($userId, $courseId);

        if ($totalLessons === 0) {
            return ['progress_percent' => 0, 'course_completed' => false, 'certificate_issued' => false, 'needs_review' => false];
        }

        $percent = (int) round(($completedLessons / $totalLessons) * 100);

        // Cập nhật enrollments.progress_percent
        $this->updateEnrollmentProgress($userId, $courseId, $percent);

        if ($percent < 100) {
            return ['progress_percent' => $percent, 'course_completed' => false, 'certificate_issued' => false, 'needs_review' => false];
        }

        // Kiểm tra tất cả quiz bắt buộc đã pass chưa
        if (!$this->allQuizzesPassed($userId, $courseId)) {
            return ['progress_percent' => $percent, 'course_completed' => false, 'certificate_issued' => false, 'needs_review' => false];
        }

        // Đánh dấu khóa học hoàn thành
        $this->markCourseCompleted($userId, $courseId);

        // Tạo chứng chỉ ngay khi hoàn thành khóa học (Không bắt buộc phải đánh giá mới có chứng chỉ)
        $certService = new CertificateService();
        $cert = $certService->generate($userId, $courseId);
        $certIssued = ($cert !== null);

        // Kiểm tra đã đánh giá chưa
        $hasReview = $this->hasReview($userId, $courseId);

        return [
            'progress_percent'  => 100,
            'course_completed'  => true,
            'certificate_issued'=> $certIssued,
            'needs_review'      => !$hasReview,
        ];
    }

    // ─── Private helpers ──────────────────────────────────────

    private function countTotalLessons(int $courseId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(l.id)
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE c.course_id = :cid AND l.deleted_at IS NULL
        ");
        $stmt->execute(['cid' => $courseId]);
        return (int) $stmt->fetchColumn();
    }

    private function countCompletedLessons(int $userId, int $courseId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(lp.lesson_id)
            FROM lesson_progress lp
            JOIN lessons l  ON lp.lesson_id  = l.id
            JOIN chapters c ON l.chapter_id   = c.id
            WHERE lp.user_id = :uid
              AND c.course_id = :cid
              AND lp.is_completed = 1
              AND l.deleted_at IS NULL
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        return (int) $stmt->fetchColumn();
    }

    private function updateEnrollmentProgress(int $userId, int $courseId, int $percent): void
    {
        $this->db->prepare("
            UPDATE enrollments
            SET progress_percent = :pct
            WHERE user_id = :uid AND course_id = :cid
        ")->execute(['pct' => $percent, 'uid' => $userId, 'cid' => $courseId]);
    }

    /**
     * Kiểm tra tất cả quiz trong khóa đã được pass (score >= passing_score).
     * Nếu khóa chưa có quiz nào → return true (không bắt buộc).
     */
    private function allQuizzesPassed(int $userId, int $courseId): bool
    {
        // Lấy tất cả quiz trong khóa học
        $stmt = $this->db->prepare("
            SELECT q.id, COALESCE(q.passing_score, 80) as passing_score
            FROM quizzes q
            JOIN lessons l  ON q.lesson_id  = l.id
            JOIN chapters c ON l.chapter_id  = c.id
            WHERE c.course_id = :cid
              AND q.deleted_at IS NULL
              AND l.deleted_at IS NULL
        ");
        $stmt->execute(['cid' => $courseId]);
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($quizzes)) {
            return true; // Không có quiz → không bắt buộc
        }

        foreach ($quizzes as $quiz) {
            $stmt2 = $this->db->prepare("
                SELECT score FROM quiz_results
                WHERE user_id = :uid AND quiz_id = :qid
                LIMIT 1
            ");
            $stmt2->execute(['uid' => $userId, 'qid' => $quiz['id']]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);

            if (!$row || (int)$row['score'] < (int)$quiz['passing_score']) {
                return false;
            }
        }
        return true;
    }

    private function markCourseCompleted(int $userId, int $courseId): void
    {
        $this->db->prepare("
            UPDATE enrollments
            SET progress_percent = 100, course_status = 'completed'
            WHERE user_id = :uid AND course_id = :cid
              AND course_status != 'completed'
        ")->execute(['uid' => $userId, 'cid' => $courseId]);
    }

    private function hasReview(int $userId, int $courseId): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM course_reviews
            WHERE user_id = :uid AND course_id = :cid
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Dùng cho StudentController sau khi submit review:
     * Nếu khóa đã completed, tạo chứng chỉ ngay.
     */
    public function tryIssueCertificateAfterReview(int $userId, int $courseId): bool
    {
        $stmt = $this->db->prepare("
            SELECT course_status FROM enrollments
            WHERE user_id = :uid AND course_id = :cid LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['course_status'] === 'completed') {
            $certService = new CertificateService();
            return $certService->generate($userId, $courseId) !== null;
        }
        return false;
    }
}
