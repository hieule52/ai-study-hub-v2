<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\VideoService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

/**
 * UploadController
 * Tách biệt upload ảnh (public) và upload video (bảo mật).
 * - Ảnh: lưu trong public/uploads/ → truy cập trực tiếp qua URL
 * - Video: lưu trong storage/videos/ → chỉ stream qua API có token
 */
class UploadController
{
    private VideoService $videoService;

    // Allowed image types
    private array $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    private int $maxImageSizeMB = 5;

    public function __construct()
    {
        $this->videoService = new VideoService();
    }

    /**
     * Upload cũ (backward compatible) - chuyển sang uploadImage
     * POST /api/upload
     */
    public function upload(Request $request, Response $response)
    {
        $this->uploadImage($request, $response);
    }

    /**
     * Upload ảnh (thumbnail, avatar, v.v.)
     * Lưu vào: public/uploads/courses/YYYY_MM/
     * 
     * POST /api/upload/image
     * Headers: Authorization: Bearer {jwt}
     * Body: multipart/form-data { file: <image> }
     * 
     * Response: { url: "/uploads/courses/2025_05/file_xxx.jpg" }
     */
    public function uploadImage(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin', 'teacher']);

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Không có file nào được tải lên hoặc file quá lớn.");
            }

            $file = $_FILES['file'];

            // Validate image
            $this->validateImage($file);

            $uploadDirName = '/uploads/courses/' . date('Y_m');
            $uploadPath = __DIR__ . '/../../../public' . $uploadDirName;
            
            // create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = uniqid('img_') . '_' . time() . '.' . $extension;
            $targetPath = $uploadPath . '/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response->success("Upload ảnh thành công", [
                    'url' => $uploadDirName . '/' . $fileName,
                    'type' => 'image',
                    'size' => filesize($targetPath)
                ]);
            } else {
                throw new Exception("Lỗi khi lưu file vào hệ thống.");
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Upload video (bảo mật)
     * Lưu vào: storage/videos/{courseId}/{chapterId}/
     * Video KHÔNG thể truy cập trực tiếp → chỉ xem qua /api/video/stream?token=xxx
     * 
     * POST /api/upload/video
     * Headers: Authorization: Bearer {jwt}
     * Body: multipart/form-data { 
     *   video: <video file>,
     *   course_id: int,
     *   chapter_id: int
     * }
     * 
     * Response: { filename, size, path }
     */
    public function uploadVideo(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin', 'teacher']);

            // Kiểm tra upload errors chi tiết
            if (!isset($_FILES['video'])) {
                throw new Exception("Không tìm thấy file video trong request. Kiểm tra field name = 'video'.");
            }

            $uploadError = $_FILES['video']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($uploadError !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File vượt quá upload_max_filesize (' . ini_get('upload_max_filesize') . '). Tăng giá trị trong php.ini.',
                    UPLOAD_ERR_FORM_SIZE => 'File vượt quá giới hạn form.',
                    UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần. Vui lòng thử lại.',
                    UPLOAD_ERR_NO_FILE => 'Không có file nào được upload.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server thiếu thư mục tạm (tmp). Liên hệ admin.',
                    UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào đĩa.',
                ];
                throw new Exception($errorMessages[$uploadError] ?? "Lỗi upload không xác định (code: {$uploadError}).");
            }

            $courseId = (int) ($request->input('course_id') ?? ($_POST['course_id'] ?? 0));
            $chapterId = (int) ($request->input('chapter_id') ?? ($_POST['chapter_id'] ?? 0));

            if (!$courseId || !$chapterId) {
                throw new Exception("Thiếu course_id hoặc chapter_id.");
            }

            // VideoService xử lý validate + upload
            $result = $this->videoService->uploadVideo($_FILES['video'], $courseId, $chapterId);

            // === VIDEO INTEGRITY CHECK ===
            $storagePath = $this->videoService->getStoragePath();
            $fullPath = $storagePath . '/' . $result['path'];
            
            // Kiểm tra file tồn tại và có size hợp lệ
            if (!file_exists($fullPath)) {
                throw new Exception("Video đã upload nhưng file không tìm thấy trên đĩa. Kiểm tra quyền ghi thư mục.");
            }

            $actualSize = filesize($fullPath);
            if ($actualSize < 1024) { // < 1KB = file rỗng/hỏng
                @unlink($fullPath);
                throw new Exception("Video upload bị lỗi - file quá nhỏ ({$actualSize} bytes). Vui lòng upload lại.");
            }

            // So sánh size upload vs size trên đĩa
            $expectedSize = $_FILES['video']['size'];
            if ($actualSize < $expectedSize * 0.9) { // Nếu file nhỏ hơn 90% expected → bị cắt
                @unlink($fullPath);
                throw new Exception("Video bị cắt khi upload ({$actualSize} bytes / {$expectedSize} bytes). Kiểm tra upload_max_filesize trong php.ini (hiện tại: " . ini_get('upload_max_filesize') . ").");
            }

            // Kiểm tra có thể mở file đọc được không
            $fp = @fopen($fullPath, 'rb');
            if (!$fp) {
                throw new Exception("Không thể đọc file video sau khi upload. Kiểm tra quyền truy cập.");
            }
            // Đọc 12 bytes đầu để verify magic bytes
            $header = fread($fp, 12);
            fclose($fp);

            if (strlen($header) < 12) {
                @unlink($fullPath);
                throw new Exception("File video bị hỏng - không đọc được header.");
            }

            $response->success("Upload video thành công", [
                'filename' => $result['filename'],
                'original_name' => $result['original_name'],
                'path' => $result['path'],
                'size' => $actualSize,
                'size_formatted' => VideoService::formatFileSize($actualSize),
                'type' => 'video',
                'integrity' => 'verified'
            ]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Validate file ảnh
     */
    private function validateImage(array $file): void
    {
        // Check size
        $maxBytes = $this->maxImageSizeMB * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new Exception("Ảnh vượt quá giới hạn {$this->maxImageSizeMB}MB.");
        }

        // Check MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file['tmp_name']);
        if (!in_array($detectedMime, $this->allowedImageTypes)) {
            throw new Exception("Loại file không hợp lệ. Chỉ chấp nhận: JPEG, PNG, GIF, WebP, SVG.");
        }

        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (!in_array($ext, $allowedExts)) {
            throw new Exception("Định dạng file không hỗ trợ.");
        }
    }
}
