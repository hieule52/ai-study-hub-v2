<?php

namespace App\Services;

use App\Repositories\VideoTokenRepository;
use App\Repositories\LessonRepository;
use App\Repositories\EnrollmentRepository;
use Exception;

/**
 * VideoService
 * Xử lý upload, stream, và bảo mật video.
 * 
 * Cơ chế bảo mật:
 * 1. Video lưu ngoài webroot (storage/videos/) → không truy cập trực tiếp
 * 2. Signed Token (HMAC-SHA256) có thời hạn 4h
 * 3. Kiểm tra enrollment trước khi cấp token
 * 4. PHP stream video với HTTP Range support (seek)
 */
class VideoService
{
    private VideoTokenRepository $tokenRepo;
    private LessonRepository $lessonRepo;
    private EnrollmentRepository $enrollRepo;

    private string $storagePath;
    private string $tokenSecret;
    private int $tokenExpiry;  // seconds
    private int $maxSizeMB;

    // Allowed video MIME types
    private array $allowedMimes = [
        'video/mp4',
        'video/webm',
        'video/quicktime',  // .mov
        'video/x-msvideo',  // .avi
    ];

    // Allowed extensions
    private array $allowedExtensions = ['mp4', 'webm', 'mov', 'avi'];

    public function __construct()
    {
        $this->tokenRepo = new VideoTokenRepository();
        $this->lessonRepo = new LessonRepository();
        $this->enrollRepo = new EnrollmentRepository();

        // Config từ .env
        $configPath = $_ENV['VIDEO_STORAGE_PATH'] ?? 'storage/videos';
        
        // Nếu đường dẫn tuyệt đối (C:/ hoặc /) → dùng trực tiếp
        if (preg_match('/^[A-Za-z]:[\\/\\\\]/', $configPath) || str_starts_with($configPath, '/')) {
            $this->storagePath = rtrim($configPath, '/\\');
        } else {
            // Đường dẫn tương đối → ghép với project root
            $basePath = realpath(__DIR__ . '/../../') ?: __DIR__ . '/../..';
            $this->storagePath = $basePath . '/' . $configPath;
        }
        
        $this->tokenSecret = $_ENV['VIDEO_TOKEN_SECRET'] ?? 'default_video_secret_key';
        $this->tokenExpiry = (int)($_ENV['VIDEO_TOKEN_EXPIRY'] ?? 14400); // 4 giờ
        $this->maxSizeMB = (int)($_ENV['VIDEO_MAX_SIZE_MB'] ?? 500);
    }

    /**
     * Lấy đường dẫn thư mục lưu video
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * Upload video cho bài học
     * Lưu file vào: storage/videos/{courseId}/{chapterId}/{uuid}.{ext}
     * 
     * @param array  $file      $_FILES['video']
     * @param int    $courseId   ID khóa học
     * @param int    $chapterId ID chương
     * @return array Thông tin file đã upload
     */
    public function uploadVideo(array $file, int $courseId, int $chapterId): array
    {
        // 1. Validate file
        $this->validateVideoFile($file);

        // 2. Tạo thư mục lưu trữ
        $targetDir = $this->storagePath . '/' . $courseId . '/' . $chapterId;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // 3. Sinh UUID filename (bảo mật - không dùng tên gốc)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uuid = bin2hex(random_bytes(16)); // 32 char hex
        $filename = $uuid . '.' . $extension;
        $targetPath = $targetDir . '/' . $filename;

        // 4. Move file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Lỗi hệ thống khi lưu video.");
        }

        // 5. Lấy thông tin file
        $fileSize = filesize($targetPath);

        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'size' => $fileSize,
            'path' => $courseId . '/' . $chapterId . '/' . $filename,
            'extension' => $extension,
            'mime_type' => $file['type']
        ];
    }

    /**
     * Validate file video upload
     */
    private function validateVideoFile(array $file): void
    {
        // Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File vượt quá giới hạn cho phép của server.',
                UPLOAD_ERR_FORM_SIZE => 'File vượt quá giới hạn cho phép.',
                UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần.',
                UPLOAD_ERR_NO_FILE => 'Không có file nào được upload.',
                UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm.',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file.',
            ];
            throw new Exception($errorMessages[$file['error']] ?? 'Lỗi upload không xác định.');
        }

        // Check size (max 200MB default)
        $maxBytes = $this->maxSizeMB * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new Exception("Video vượt quá giới hạn {$this->maxSizeMB}MB.");
        }

        // Check extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception("Định dạng video không hỗ trợ. Chấp nhận: " . implode(', ', $this->allowedExtensions));
        }

        // Check MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file['tmp_name']);
        if (!in_array($detectedMime, $this->allowedMimes)) {
            throw new Exception("Loại file không hợp lệ. Chỉ chấp nhận video.");
        }
    }

    /**
     * Tạo signed token để stream video
     * 
     * @param int $userId    ID người dùng
     * @param int $lessonId  ID bài học
     * @param int $courseId  ID khóa học (để kiểm tra enrollment)
     * @return array Token info
     */
    public function generateStreamToken(int $userId, int $lessonId, int $courseId, string $userRole = 'student'): array
    {
        // 1. Kiểm tra bài học tồn tại
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Bài học không tồn tại.");
        }

        // 2. Kiểm tra quyền xem video
        // Admin → luôn cho phép
        // Teacher → cho phép nếu là chủ khóa
        // Student → phải enrolled hoặc bài học miễn phí
        if ($userRole !== 'admin') {
            if (!$lesson['is_free']) {
                // Teacher: kiểm tra có phải chủ khóa không
                if ($userRole === 'teacher') {
                    $db = \App\Core\Database::connect();
                    $stmt = $db->prepare("SELECT teacher_id FROM courses WHERE id = :id");
                    $stmt->execute(['id' => $courseId]);
                    $course = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if (!$course || $course['teacher_id'] != $userId) {
                        // Teacher không phải chủ khóa → phải enrolled
                        $isEnrolled = $this->enrollRepo->checkEnrollment($userId, $courseId);
                        if (!$isEnrolled) {
                            throw new Exception("Bạn chưa đăng ký khóa học này.");
                        }
                    }
                } else {
                    // Student: phải enrolled
                    $isEnrolled = $this->enrollRepo->checkEnrollment($userId, $courseId);
                    if (!$isEnrolled) {
                        throw new Exception("Bạn chưa đăng ký khóa học này.");
                    }
                }
            }
        }

        // 3. Kiểm tra bài học có video
        if ($lesson['content_type'] !== 'video' || empty($lesson['video_filename'])) {
            throw new Exception("Bài học này không có video.");
        }

        // 4. Tạo signed token (HMAC-SHA256)
        $rawToken = bin2hex(random_bytes(32)); // 64 char
        
        // Dùng MySQL NOW() để tránh lệch timezone giữa PHP và MySQL
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT DATE_ADD(NOW(), INTERVAL :seconds SECOND) as expires_at");
        $stmt->execute(['seconds' => $this->tokenExpiry]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $expiresAt = $row['expires_at'];

        // Lấy IP và User-Agent của client hiện tại khi cấp token
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // 5. Lưu token vào database
        $success = $this->tokenRepo->createToken($userId, $lessonId, $rawToken, $expiresAt, $ipAddress, $userAgent);
        if (!$success) {
            throw new Exception("Không thể tạo token xem video.");
        }

        // 6. Cleanup expired tokens (opportunistic)
        $this->tokenRepo->deleteExpiredTokens();

        return [
            'token' => $rawToken,
            'expires_at' => $expiresAt,
            'stream_url' => '/api/video/stream?token=' . $rawToken,
            'lesson_title' => $lesson['title'],
            'duration' => $lesson['duration'] ?? 0
        ];
    }

    /**
     * Stream video qua PHP (HTTP Range support cho seek)
     * 
     * @param string $token Signed token
     */
    public function streamVideo(string $token): void
    {
        // 1. Validate token
        $tokenData = $this->tokenRepo->findValidToken($token);
        if (!$tokenData) {
            http_response_code(403);
            echo json_encode(['error' => 'Token không hợp lệ hoặc đã hết hạn.']);
            exit;
        }

        // 2. Kiểm tra tác nhân tải xuống (Downloader User-Agent Blacklist & Sec-Fetch-Dest)
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $clientUa = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Danh sách đen các User-Agent của công cụ tải xuống phổ biến
        $downloaderAgents = [
            'idman', 'internet download manager', 'fdm', 'free download manager', 
            'aria2', 'wget', 'curl', 'python', 'postman', 'insomnia', 
            'download', 'downloader', 'youtube-dl', 'streamlink'
        ];
        foreach ($downloaderAgents as $agent) {
            if (stripos($clientUa, $agent) !== false) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Công cụ tải xuống bị chặn trên hệ thống.']);
                exit;
            }
        }

        // Kiểm tra khớp User-Agent để đảm bảo chính trình duyệt của user đang yêu cầu stream
        if (!empty($tokenData['user_agent']) && $tokenData['user_agent'] !== $clientUa) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Thiết bị yêu cầu không khớp với phiên làm việc.']);
            exit;
        }

        // Kiểm tra Sec-Fetch-Dest (Ngăn chặn IDM và các cuộc gọi tải trực tiếp)
        // Trình duyệt luôn gửi Sec-Fetch-Dest: video khi chạy thẻ <video>
        // IDM hoặc các công cụ tải độc lập không gửi hoặc gửi 'empty' / 'document'
        $secFetchDest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
        $isModernBrowser = (stripos($clientUa, 'Chrome') !== false || 
                            stripos($clientUa, 'Safari') !== false || 
                            stripos($clientUa, 'Firefox') !== false || 
                            stripos($clientUa, 'Edge') !== false);
                            
        if ($isModernBrowser) {
            if (empty($secFetchDest) || !in_array(strtolower($secFetchDest), ['video', 'audio'])) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Yêu cầu không hợp lệ. Trực tiếp tải video không được phép.']);
                exit;
            }
        }

        // 3. Tìm file video trên disk
        $lesson = $this->lessonRepo->findLessonById($tokenData['lesson_id']);
        if (!$lesson || empty($lesson['video_filename'])) {
            http_response_code(404);
            echo json_encode(['error' => 'Video không tồn tại.']);
            exit;
        }

        // Tìm file video - tìm trong thư mục course/chapter
        $videoPath = $this->findVideoFile($lesson);
        if (!$videoPath || !file_exists($videoPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'File video không tìm thấy trên hệ thống.']);
            exit;
        }

        // 4. Stream video với HTTP Range support
        $this->sendVideoStream($videoPath);
    }

    /**
     * Tìm đường dẫn file video trên disk
     */
    private function findVideoFile(array $lesson): ?string
    {
        $filename = $lesson['video_filename'];
        
        // Nếu filename chứa path đầy đủ (courseId/chapterId/file.mp4)
        $fullPath = $this->storagePath . '/' . $filename;
        if (file_exists($fullPath)) {
            return $fullPath;
        }

        // Tìm theo chapter → course
        $chapterId = $lesson['chapter_id'];
        
        // Lấy course_id từ chapter
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT course_id FROM chapters WHERE id = :id");
        $stmt->execute(['id' => $chapterId]);
        $chapter = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($chapter) {
            $searchPath = $this->storagePath . '/' . $chapter['course_id'] . '/' . $chapterId . '/' . $filename;
            if (file_exists($searchPath)) {
                return $searchPath;
            }
        }

        return null;
    }

    /**
     * PHP Video Streaming với HTTP Range support
     * Hỗ trợ: seek, partial content, resume
     */
    private function sendVideoStream(string $filePath): void
    {
        $fileSize = filesize($filePath);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // MIME types
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        // Tắt output buffering trước khi gửi headers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Headers cho video streaming
        header('Content-Type: ' . $mimeType);
        header('Accept-Ranges: bytes');
        // Cho phép browser cache video (giảm re-request khi quay lại)
        header('Cache-Control: private, max-age=3600');
        header('ETag: "' . md5($filePath . $fileSize) . '"');
        // Ngăn download dialog
        header('Content-Disposition: inline');

        // Xử lý ETag cache (304 Not Modified)
        $etag = '"' . md5($filePath . $fileSize) . '"';
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            exit;
        }

        $start = 0;
        $end = $fileSize - 1;
        $length = $fileSize;

        // HTTP Range Request (cho seek/partial loading)
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            
            if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
                $start = $matches[1] !== '' ? intval($matches[1]) : 0;
                $end = $matches[2] !== '' ? intval($matches[2]) : $fileSize - 1;

                // Validate range
                if ($start > $end || $start >= $fileSize) {
                    http_response_code(416); // Range Not Satisfiable
                    header("Content-Range: bytes */$fileSize");
                    exit;
                }

                $length = $end - $start + 1;
                http_response_code(206); // Partial Content
                header("Content-Range: bytes $start-$end/$fileSize");
            }
        }

        header("Content-Length: $length");

        // Cho phép script dừng khi client disconnect
        ignore_user_abort(false);
        set_time_limit(0);

        // Stream file
        $fp = fopen($filePath, 'rb');
        if ($fp === false) {
            http_response_code(500);
            exit;
        }

        // Seek to start position
        if ($start > 0) {
            fseek($fp, $start);
        }

        // Buffer lớn hơn (512KB) → ít syscall hơn → nhanh hơn nhiều
        $bufferSize = 524288; // 512KB thay vì 8KB
        $bytesRemaining = $length;

        while (!feof($fp) && $bytesRemaining > 0 && !connection_aborted()) {
            $readSize = min($bufferSize, $bytesRemaining);
            $data = fread($fp, $readSize);
            
            if ($data === false) {
                break;
            }
            
            echo $data;
            flush();
            $bytesRemaining -= strlen($data);
        }

        fclose($fp);
        exit;
    }

    /**
     * Xóa video file khỏi disk
     */
    public function deleteVideoFile(string $filename): bool
    {
        $filePath = $this->storagePath . '/' . $filename;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }


    /**
     * Format file size (bytes → human readable)
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
