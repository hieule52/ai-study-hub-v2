<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AiRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Lấy hoặc tạo một cuộc hội thoại cho User tại bài học cụ thể
     */
    public function getOrCreateConversation(int $userId, int $lessonId): int
    {
        $stmt = $this->db->prepare("SELECT id FROM ai_conversations WHERE user_id = :uid AND lesson_id = :lid LIMIT 1");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        $id = $stmt->fetchColumn();

        if ($id) return (int)$id;

        $stmt = $this->db->prepare("INSERT INTO ai_conversations (user_id, lesson_id) VALUES (:uid, :lid)");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Lưu tin nhắn vào hội thoại
     */
    public function saveMessage(int $conversationId, string $role, string $content, int $tokens = 0): bool
    {
        $stmt = $this->db->prepare("INSERT INTO ai_messages (conversation_id, role, content, token_usage) VALUES (:cid, :role, :content, :tokens)");
        return $stmt->execute([
            'cid'     => $conversationId,
            'role'    => $role,
            'content' => $content,
            'tokens'  => $tokens
        ]);
    }

    /**
     * Lấy lịch sử hội thoại
     */
    public function getHistory(int $conversationId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT role, content 
            FROM ai_messages 
            WHERE conversation_id = :cid 
            ORDER BY id DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Lấy hoặc tạo conversation cho AI Assistant tổng quát (không gắn bài học cụ thể)
     * Dùng lesson_id = NULL làm định danh
     */
    public function getOrCreateAssistantConversation(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT id FROM ai_conversations WHERE user_id = :uid AND lesson_id IS NULL LIMIT 1");
        $stmt->execute(['uid' => $userId]);
        $id = $stmt->fetchColumn();

        if ($id) return (int)$id;

        $stmt = $this->db->prepare("INSERT INTO ai_conversations (user_id, lesson_id) VALUES (:uid, NULL)");
        $stmt->execute(['uid' => $userId]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Xóa lịch sử hội thoại của bài học
     */
    public function clearHistory(int $userId, int $lessonId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ai_conversations WHERE user_id = :uid AND lesson_id = :lid");
        return $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
    }

    /**
     * Xóa toàn bộ lịch sử AI Assistant của user
     */
    public function clearAssistantHistory(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM ai_conversations WHERE user_id = :uid AND lesson_id IS NULL");
        return $stmt->execute(['uid' => $userId]);
    }
}
