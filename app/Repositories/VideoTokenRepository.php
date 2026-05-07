<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * VideoTokenRepository
 * Quản lý signed tokens cho video streaming bảo mật.
 * Token có thời hạn, dùng 1 lần cho mỗi session xem video.
 */
class VideoTokenRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Tạo token mới cho user xem video bài học
     */
    public function createToken(int $userId, int $lessonId, string $token, string $expiresAt): bool
    {
        // Xóa token cũ của user cho lesson này (chỉ giữ 1 token active)
        $this->revokeUserLessonTokens($userId, $lessonId);

        $sql = "INSERT INTO video_tokens (token, user_id, lesson_id, expires_at) 
                VALUES (:token, :user_id, :lesson_id, :expires_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Tìm token và kiểm tra còn hiệu lực
     * @return array|null Token data hoặc null nếu không hợp lệ/hết hạn
     */
    public function findValidToken(string $token): ?array
    {
        $sql = "SELECT vt.*, l.video_filename, l.video_url, l.content_type, l.title as lesson_title
                FROM video_tokens vt
                JOIN lessons l ON vt.lesson_id = l.id
                WHERE vt.token = :token AND vt.expires_at > NOW()
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Xóa token cũ của user cho lesson cụ thể
     */
    public function revokeUserLessonTokens(int $userId, int $lessonId): bool
    {
        $sql = "DELETE FROM video_tokens WHERE user_id = :user_id AND lesson_id = :lesson_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
    }

    /**
     * Cleanup: Xóa tất cả token đã hết hạn
     * Nên chạy định kỳ (cron job) hoặc mỗi khi tạo token mới
     */
    public function deleteExpiredTokens(): int
    {
        $sql = "DELETE FROM video_tokens WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Đếm số token active của user (để rate limit nếu cần)
     */
    public function countActiveTokens(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM video_tokens WHERE user_id = :user_id AND expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
