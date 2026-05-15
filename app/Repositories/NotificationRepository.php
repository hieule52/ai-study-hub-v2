<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * NotificationRepository
 *
 * Upgraded DB schema (aistudyhublms.notifications):
 *   id, user_id, type, title, content, data (JSON), is_read, created_at
 */
class NotificationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Tạo thông báo mới
     */
    public function create(int $userId, string $type, string $title, string $message, ?array $data = null): bool
    {
        $dataJson = $data ? json_encode($data) : null;

        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, content, data, is_read)
            VALUES (:uid, :type, :title, :content, :data, 0)
        ");
        return $stmt->execute([
            'uid'     => $userId,
            'type'    => $type,
            'title'   => $title,
            'content' => mb_substr($message, 0, 255),
            'data'    => $dataJson
        ]);
    }

    /**
     * Lấy thông báo của user (có pagination)
     */
    public function findByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, type, title, content as message, data, is_read, created_at
            FROM notifications
            WHERE user_id = :uid
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':uid',    $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            if ($row['data']) {
                $row['data'] = json_decode($row['data'], true);
            }
            
            // Map type to icon for frontend legacy support
            $icons = [
                'certificate'    => '🎓',
                'course_approved'=> '📚',
                'chat'           => '💬',
                'success'        => '✅',
                'warning'        => '⚠️',
                'info'           => 'ℹ️',
            ];
            $row['icon'] = $icons[$row['type']] ?? 'ℹ️';
            
            return $row;
        }, $rows);
    }

    /**
     * Đếm số thông báo chưa đọc
     */
    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0"
        );
        $stmt->execute(['uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Đánh dấu đã đọc theo id
     */
    public function markRead(int $notifId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :uid"
        );
        return $stmt->execute(['id' => $notifId, 'uid' => $userId]);
    }

    /**
     * Đánh dấu tất cả đã đọc
     */
    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1 WHERE user_id = :uid AND is_read = 0"
        );
        return $stmt->execute(['uid' => $userId]);
    }
}
