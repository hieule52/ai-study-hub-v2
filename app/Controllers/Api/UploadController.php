<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class UploadController
{
    public function upload(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin', 'teacher']);

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Không có file nào được tải lên hoặc file quá lớn.");
            }

            $file = $_FILES['file'];
            $uploadDirName = '/uploads/courses/' . date('Y_m');
            $uploadPath = __DIR__ . '/../../../public' . $uploadDirName;
            
            // create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('file_') . '_' . time() . '.' . $extension;
            $targetPath = $uploadPath . '/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response->success("Upload thành công", [
                    'url' => $uploadDirName . '/' . $fileName
                ]);
            } else {
                throw new Exception("Lỗi khi lưu file vào hệ thống.");
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
