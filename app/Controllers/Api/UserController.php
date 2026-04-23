<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class UserController
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function profile(Request $request, Response $response)
    {
        try {
            // Ai đăng nhập cũng xem được profile của mình
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['student', 'teacher', 'admin']);
            $userId = $request->user->sub;

            $userInfo = $this->userRepo->findById((int)$userId);
            if (!$userInfo) {
                $response->error("Không tìm thấy người dùng.", 404);
                return;
            }

            // Convert to array and hide sensitive data
            $userData = (array)$userInfo;
            unset($userData['password_hash']);

            $response->success("Thông tin cá nhân", $userData);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 401);
        }
    }

    public function updateProfile(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['student', 'teacher', 'admin']);
            $userId = $request->user->sub;
            $body = $request->all();

            if (empty($body['username'])) {
                $response->error("Tên hiển thị không được để trống.", 400);
                return;
            }

            $success = $this->userRepo->updateProfile((int)$userId, $body['username']);
            
            if ($success) {
                $response->success("Cập nhật thông tin thành công!");
            } else {
                $response->error("Không thể cập nhật thông tin.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 401);
        }
    }

    public function changePassword(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['student', 'teacher', 'admin']);
            $userId = $request->user->sub;
            $body = $request->all();

            if (empty($body['old_password']) || empty($body['new_password'])) {
                $response->error("Vui lòng nhập đầy đủ mật khẩu cũ và mới.", 400);
                return;
            }

            $userInfo = $this->userRepo->findById((int)$userId);
            
            // Xác thực mật khẩu cũ
            if (!password_verify($body['old_password'], $userInfo->password_hash)) {
                $response->error("Mật khẩu cũ không chính xác.", 400);
                return;
            }

            // Mã hóa mật khẩu mới
            $hashedPassword = password_hash($body['new_password'], PASSWORD_BCRYPT);
            
            $success = $this->userRepo->updatePassword((int)$userId, $hashedPassword);
            
            if ($success) {
                $response->success("Đổi mật khẩu thành công!");
            } else {
                $response->error("Không thể đổi mật khẩu, vui lòng thử lại.", 500);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 401);
        }
    }

    public function uploadAvatar(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['student', 'teacher', 'admin']);
            $userId = $request->user->sub;

            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Không có file nào được tải lên hoặc quá trình tải bị lỗi.");
            }

            $file = $_FILES['avatar'];

            // 1. Kiểm tra dung lượng (Max 2MB)
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($file['size'] > $maxSize) {
                throw new Exception("Dung lượng file vượt quá giới hạn 2MB.");
            }

            // 2. Kiểm tra định dạng phân loại (MIME Type)
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileMimeType = mime_content_type($file['tmp_name']);
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                throw new Exception("Chỉ hỗ trợ định dạng ảnh (.jpg, .png, .webp).");
            }

            // 3. Chuẩn bị thư mục
            $uploadDirName = '/uploads/avatars';
            $uploadPath = __DIR__ . '/../../../public' . $uploadDirName;
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // 4. Định danh file mới và di chuyển
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!$extension) {
                // FALLBACK an toàn nếu file không có đuôi mở rộng rõ ràng
                $extension = str_replace('image/', '', $fileMimeType);
            }
            
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $targetPath = $uploadPath . '/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $avatarUrl = $uploadDirName . '/' . $fileName;
                
                // Cập nhật CSDL
                $success = $this->userRepo->updateAvatar((int)$userId, $avatarUrl);
                
                if ($success) {
                    $response->success("Tải lên ảnh kích thước tối ưu thành công!", [
                        'avatar' => $avatarUrl
                    ]);
                } else {
                    throw new Exception("Lỗi khi lưu Database ảnh vào hệ thống.");
                }
            } else {
                throw new Exception("Lỗi khi lưu File vật lý vào đĩa chủ.");
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
