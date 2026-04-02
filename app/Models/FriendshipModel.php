<?php

namespace App\Models;

use App\Core\Model;

class FriendshipModel extends Model
{
    protected $table = 'friendships';

    // Lấy danh sách bạn bè
    public function getFriends($userId)
    {
        $sql = "SELECT users.* FROM friendships
                JOIN users ON users.id = friendships.friend_id
                WHERE friendships.user_id = ? AND friendships.status = 'accepted'
                
                UNION

                SELECT users.* FROM friendships
                JOIN users ON users.id = friendships.user_id
                WHERE friendships.friend_id = ? AND friendships.status = 'accepted'";

        return $this->query($sql, [$userId, $userId])->fetchAll();
    }

    // Gợi ý kết bạn
    public function getSuggestions($userId)
    {
        $sql = "SELECT * FROM users 
                WHERE id != ?
                AND id NOT IN (
                    SELECT friend_id FROM friendships WHERE user_id = ?
                )
                AND id NOT IN (
                    SELECT user_id FROM friendships WHERE friend_id = ?
                )";

        return $this->query($sql, [$userId, $userId, $userId])->fetchAll();
    }

    // Các yêu cầu kết bạn gửi đến mình
    public function getPendingRequests($userId)
    {
        $sql = "SELECT users.*, friendships.user_id AS from_id
                FROM friendships
                JOIN users ON users.id = friendships.user_id
                WHERE friendships.friend_id = ?
                AND friendships.status = 'pending'";

        return $this->query($sql, [$userId])->fetchAll();
    }

    // Gửi lời mời
    public function sendRequest($userId, $friendId)
    {
        $sql = "INSERT INTO friendships (user_id, friend_id, status)
                VALUES (?, ?, 'pending')";

        return $this->query($sql, [$userId, $friendId]);
    }

    // Chấp nhận lời mời
    public function acceptRequest($from, $to)
    {
        $sql = "UPDATE friendships 
                SET status = 'accepted'
                WHERE user_id = ? AND friend_id = ?";

        return $this->query($sql, [$from, $to]);
    }

    // Xóa lời mời (từ chối)
    public function deleteRequest($from, $to)
    {
        $sql = "DELETE FROM friendships 
                WHERE user_id = ? AND friend_id = ?";

        return $this->query($sql, [$from, $to]);
    }

    // Hủy kết bạn
    public function removeFriend($userId, $friendId)
    {
        $sql = "DELETE FROM friendships 
                WHERE (user_id = ? AND friend_id = ?)
                   OR (user_id = ? AND friend_id = ?)";

        return $this->query($sql, [$userId, $friendId, $friendId, $userId]);
    }
}
