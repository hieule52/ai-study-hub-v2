<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Friendship;
use App\Models\FriendshipModel;
use App\Models\UserModel;

class FriendshipController extends Controller
{
    // Trang kết nối bạn bè
    public function connect()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login"); // Hoặc route login của bạn
            exit;
        }
        $currentUser = $_SESSION['user_id'];

        $friendModel = new FriendshipModel();
        $userModel = new UserModel();

        $friends = $friendModel->getFriends($currentUser);
        $suggestions = $friendModel->getSuggestions($currentUser);
        $requests = $friendModel->getPendingRequests($currentUser);
        

        return $this->view("friends/connect", [
            "friends" => $friends,
            "suggestions" => $suggestions,
            "requests" => $requests
        ]);
    }

    // Gửi lời mời
    public function send()
    {
        $currentUser = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;

        if ($friendId) {
            $model = new FriendshipModel();
            $model->sendRequest($currentUser, $friendId);
        }

        header("Location: /friends");
        exit;
    }

    // Chấp nhận lời mời
    public function accept()
    {
        $currentUser = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;

        if ($friendId) {
            $model = new FriendshipModel();
            $model->acceptRequest($friendId, $currentUser);
        }

        header("Location: /friends");
        exit;
    }

    // Từ chối
    public function decline()
    {
        $currentUser = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;

        if ($friendId) {
            $model = new FriendshipModel();
            $model->deleteRequest($friendId, $currentUser);
        }

        header("Location: /friends");
        exit;
    }

    // Hủy bạn bè
    public function remove()
    {
        $currentUser = $_SESSION['user_id'];
        $friendId = $_POST['friend_id'] ?? null;

        if ($friendId) {
            $model = new FriendshipModel();
            $model->removeFriend($currentUser, $friendId);
        }

        header("Location: /friends");
        exit;
    }
}
