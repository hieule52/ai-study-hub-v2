<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * CertificateRepository
 *
 * Actual DB schema (aistudyhublms.certificates):
 *   id, user_id, course_id, certificate_code VARCHAR(50), completion_date, score, created_at
 *   UNIQUE(certificate_code), UNIQUE(user_id, course_id)
 *   NO uuid column, NO deleted_at column
 */
class CertificateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Kiểm tra học viên đã có chứng chỉ khóa học này chưa
     */
    public function findByUserAndCourse(int $userId, int $courseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   u.username, u.email,
                   co.title as course_title, co.teacher_id,
                   t.username as teacher_name
            FROM certificates c
            JOIN users u    ON c.user_id    = u.id
            JOIN courses co ON c.course_id  = co.id
            JOIN users t    ON co.teacher_id = t.id
            WHERE c.user_id = :uid AND c.course_id = :cid
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Lấy chứng chỉ theo certificate_code (để verify public)
     * Note: DB dùng `certificate_code` NOT `uuid`
     */
    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   u.username, u.email,
                   co.title as course_title,
                   t.username as teacher_name
            FROM certificates c
            JOIN users u    ON c.user_id    = u.id
            JOIN courses co ON c.course_id  = co.id
            JOIN users t    ON co.teacher_id = t.id
            WHERE c.certificate_code = :code
            LIMIT 1
        ");
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Alias để tương thích với code cũ dùng findByUuid()
     */
    public function findByUuid(string $uuid): ?array
    {
        return $this->findByCode($uuid);
    }

    /**
     * Lấy tất cả chứng chỉ của một học viên
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   co.title as course_title, co.thumbnail,
                   t.username as teacher_name
            FROM certificates c
            JOIN courses co ON c.course_id  = co.id
            JOIN users t    ON co.teacher_id = t.id
            WHERE c.user_id = :uid
            ORDER BY c.completion_date DESC
        ");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cấp chứng chỉ mới — sử dụng certificate_code (UUID format)
     */
    public function issue(int $userId, int $courseId, ?float $score = null): array
    {
        // Trả về ngay nếu đã có
        $existing = $this->findByUserAndCourse($userId, $courseId);
        if ($existing) {
            return $existing;
        }

        $code = $this->generateCode();

        $stmt = $this->db->prepare("
            INSERT INTO certificates (certificate_code, user_id, course_id, score)
            VALUES (:code, :uid, :cid, :score)
        ");
        $stmt->execute([
            'code'  => $code,
            'uid'   => $userId,
            'cid'   => $courseId,
            'score' => $score,
        ]);

        return $this->findByCode($code);
    }

    /**
     * Đếm số chứng chỉ của user
     */
    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM certificates WHERE user_id = :uid"
        );
        $stmt->execute(['uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Generate unique certificate code (UUID v4 format)
     */
    private function generateCode(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant RFC 4122
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
