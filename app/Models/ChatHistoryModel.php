<?php

namespace App\Models;

use App\Core\Model;

class ChatHistoryModel extends Model
{
    protected $table = "ai_messages";

    /**
     * Lưu lịch sử theo loại (chat, homework, summarizer, quiz)
     * 
     * @param int $userId - ID của user
     * @param string $type - Loại: 'chat', 'homework', 'summarizer', 'quiz'
     * @param string $userMessage - Tin nhắn của user
     * @param string $aiResponse - Phản hồi của AI
     * @return bool
     */
    public function saveByType($userId, $type, $userMessage, $aiResponse)
    {
        $sql = "INSERT INTO {$this->table} (user_id, type, input_text, output_text, created_at) 
                VALUES (?, ?, ?, ?, NOW())";

        $this->query($sql, [$userId, $type, $userMessage, $aiResponse]);
        return true;
    }

    /**
     * Lấy lịch sử theo loại
     * 
     * @param int $userId - ID của user
     * @param string $type - Loại: 'chat', 'homework', 'summarizer', 'quiz'
     * @param int $limit - Số lượng tin nhắn tối đa
     * @return array
     */
    public function getHistoryByType($userId, $type, $limit = 50)
    {
        $limit = (int)$limit;

        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND type = ?
                ORDER BY created_at ASC 
                LIMIT {$limit}";

        $stmt = $this->query($sql, [$userId, $type]);
        return $stmt->fetchAll();
    }
}
