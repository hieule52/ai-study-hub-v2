<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class VipPaymentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function createTransaction(int $userId, float $amount): int
    {
        $stmt = $this->db->prepare("INSERT INTO vip_payments (user_id, amount, status) VALUES (:uid, :amount, 'pending')");
        $stmt->execute([
            'uid' => $userId,
            'amount' => $amount
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    public function findTransaction(int $txnId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM vip_payments WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $txnId]);
        $data = $stmt->fetch();
        return $data ? (array)$data : null;
    }

    public function completeTransaction(int $txnId, int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE vip_payments SET status = 'completed' WHERE id = :id AND user_id = :uid");
            $success = $stmt->execute(['id' => $txnId, 'uid' => $userId]);

            if ($success) {
                // Đánh dấu user is_vip = 1
                $updateUser = $this->db->prepare("UPDATE users SET is_vip = 1 WHERE id = :uid");
                $updateUser->execute(['uid' => $userId]);
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
