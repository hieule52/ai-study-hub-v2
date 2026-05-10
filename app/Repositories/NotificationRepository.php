<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * NotificationRepository
 *
 * Actual DB schema (aistudyhublms.notifications):
 *   id, user_id, content VARCHAR(255), is_read TINYINT(1), created_at
 *   NO type, NO title, NO message, NO data columns
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
     * Note: DB chỉ có cột `content`, không có title/type/message riêng
     * Format: "[icon] title: message"
     */
    public function create(int $userId, string $type, string $title, string $message, ?array $data = null): bool
    {
        // Map type → icon
        $icons = [
            'certificate'    => '🎓',
            'course_approved'=> '📚',
            'success'        => '✅',
            'warning'        => '⚠️',
            'info'           => 'ℹ️',
        ];
        $icon = $icons[$type] ?? 'ℹ️';

        // Gộp thành 1 chuỗi content vì DB chỉ có cột `content`
        $content = "{$icon} {$title}: {$message}";

        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, content, is_read)
            VALUES (:uid, :content, 0)
        ");
        return $stmt->execute([
            'uid'     => $userId,
            'content' => mb_substr($content, 0, 255), // giới hạn VARCHAR(255)
        ]);
    }

    /**
     * Lấy thông báo của user (có pagination)
     * Trả về items theo format chuẩn để header bell hiểu được
     */
    public function findByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, content, is_read, created_at
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

        // Normalize để header bell JS có thể hiểu title/message/type
        return array_map(function($row) {
            $content = $row['content'] ?? '';
            // Parse icon và nội dung
            $type = 'info';
            if (str_contains($content, '🎓')) $type = 'certificate';
            elseif (str_contains($content, '📚')) $type = 'course_approved';
            elseif (str_contains($content, '✅')) $type = 'success';
            elseif (str_contains($content, '⚠️')) $type = 'warning';

            // Split title: message nếu có dấu ":"
            $colonPos = strpos($content, ': ', 3);
            if ($colonPos !== false) {
                $row['title']   = trim(substr($content, 0, $colonPos));
                $row['message'] = trim(substr($content, $colonPos + 2));
            } else {
                $row['title']   = $content;
                $row['message'] = '';
            }
            $row['type'] = $type;
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
