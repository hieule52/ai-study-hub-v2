<?php

namespace App\Services;

use App\Repositories\CertificateRepository;
use App\Repositories\NotificationRepository;
use App\Core\Database;
use PDO;

/**
 * CertificateService
 * Tự động tạo chứng chỉ khi học viên hoàn thành 100% khóa học và đã đánh giá.
 */
class CertificateService
{
    private CertificateRepository $certRepo;
    private NotificationRepository $notifRepo;
    private PDO $db;

    public function __construct()
    {
        $this->certRepo  = new CertificateRepository();
        $this->notifRepo = new NotificationRepository();
        $this->db        = Database::connect();
    }

    /**
     * Sinh chứng chỉ cho học viên (idempotent).
     * Tính điểm trung bình từ quiz_results của toàn khóa học.
     */
    public function generate(int $userId, int $courseId): ?array
    {
        // Idempotent: Trả về ngay nếu đã có
        $existing = $this->certRepo->findByUserAndCourse($userId, $courseId);
        if ($existing) {
            return $existing;
        }

        // Tính điểm quiz trung bình
        $avgScore = $this->calcAverageScore($userId, $courseId);

        // Cấp chứng chỉ
        $cert = $this->certRepo->issue($userId, $courseId, $avgScore);

        // Gửi thông báo
        try {
            $this->notifRepo->create(
                $userId,
                'certificate',
                '🎓 Chứng chỉ đã được cấp!',
                'Chúc mừng! Bạn đã nhận được chứng chỉ hoàn thành khóa học. Xem chứng chỉ tại trang Chứng chỉ của bạn.'
            );
        } catch (\Exception $e) {
            // Non-critical
        }

        return $cert;
    }

    /**
     * Tính điểm quiz trung bình của học viên trong toàn bộ khóa học.
     */
    private function calcAverageScore(int $userId, int $courseId): float
    {
        $stmt = $this->db->prepare("
            SELECT AVG(qr.score) as avg_score
            FROM quiz_results qr
            JOIN quizzes q       ON qr.quiz_id  = q.id
            JOIN lessons l       ON q.lesson_id = l.id
            JOIN chapters c      ON l.chapter_id = c.id
            WHERE qr.user_id = :uid
              AND c.course_id = :cid
              AND q.deleted_at IS NULL
              AND l.deleted_at IS NULL
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['avg_score'] !== null ? round((float)$row['avg_score'], 2) : 100.0;
    }
}
