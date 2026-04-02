<?php

namespace App\Models;

use App\Core\Model;

class MessageModel extends Model
{
    protected $table = 'messages';

    public function getMessagesBetween($user1, $user2)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = :u1 AND receiver_id = :u2)
               OR (sender_id = :u2 AND receiver_id = :u1)
            ORDER BY created_at ASC
        ");
        $stmt->execute([':u1' => $user1, ':u2' => $user2]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function sendMessage($sender, $receiver, $content)
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, content) 
            VALUES (:sender, :receiver, :content)
        ");
        $stmt->execute([':sender' => $sender, ':receiver' => $receiver, ':content' => $content]);
    }
}
