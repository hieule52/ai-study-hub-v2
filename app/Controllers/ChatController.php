<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\MessageModel;

class ChatController extends \App\Core\Controller
{
    public function chatWithFriend()
    {
        $userId = $_SESSION['user_id'];
        $friend_id = $_GET['friend_id'] ?? null;
        if (!$friend_id) {
            header("Location: /friends");
            exit;
        }

        $friendModel = new UserModel();
        $friend = $friendModel->getById($friend_id);

        if (!$friend) {
            header("Location: /friends");
            exit;
        }

        $messageModel = new MessageModel();
        $messages = $messageModel->getMessagesBetween($userId, $friend_id);

        $this->view('chat/chat', [
            'friend' => $friend,
            'messages' => $messages
        ]);
    }


    public function sendMessage()
    {
        $senderId = $_SESSION['user_id'];
        $receiverId = $_POST['receiver_id'];
        $content = trim($_POST['content']);

        if ($content) {
            $messageModel = new MessageModel();
            $messageModel->sendMessage($senderId, $receiverId, $content);
        }

        header("Location: /chat?friend_id=$receiverId");
        exit;
    }
    public function fetchMessages()
    {
        $friend_id = $_GET['friend_id'];
        $userId = $_SESSION['user_id'];

        $messageModel = new MessageModel();
        $messages = $messageModel->getMessagesBetween($userId, $friend_id);

        header('Content-Type: application/json');
        echo json_encode(['messages' => $messages]);
    }
}
