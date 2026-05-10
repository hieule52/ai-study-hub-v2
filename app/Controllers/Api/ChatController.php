<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Middlewares\AuthMiddleware;
use Exception;
use PDO;

class ChatController
{
    public function history(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;
            
            $otherId = (int)$request->query('user_id');
            if (!$otherId) {
                throw new Exception("Missing user_id");
            }

            $db = Database::connect();
            $stmt = $db->prepare("
                SELECT m.*, u.username as sender_name, u.avatar as sender_avatar 
                FROM messages m
                LEFT JOIN users u ON m.sender_id = u.id
                WHERE (m.sender_id = :uid1 AND m.receiver_id = :uid2)
                   OR (m.sender_id = :uid2 AND m.receiver_id = :uid1)
                ORDER BY m.created_at ASC
                LIMIT 100
            ");
            
            $stmt->execute([
                'uid1' => $userId,
                'uid2' => $otherId
            ]);
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->success("Thành công", $messages);
            
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
