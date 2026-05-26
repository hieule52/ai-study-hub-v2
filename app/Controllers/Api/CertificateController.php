<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\CertificateRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\NotificationRepository;
use App\Middlewares\AuthMiddleware;
use Exception;

class CertificateController
{
    private CertificateRepository  $certRepo;
    private EnrollmentRepository   $enrollRepo;
    private NotificationRepository $notifRepo;

    public function __construct()
    {
        $this->certRepo   = new CertificateRepository();
        $this->enrollRepo = new EnrollmentRepository();
        $this->notifRepo  = new NotificationRepository();
    }

    /**
     * GET /api/certificates/my
     * Lấy tất cả chứng chỉ của học viên đang đăng nhập
     */
    public function myCertificates(Request $request, Response $response): void
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;

            $certs = $this->certRepo->findByUser($userId);

            // Normalize field names for frontend (completion_date → issued_at alias)
            $certs = array_map(fn($c) => array_merge($c, [
                'uuid'      => $c['certificate_code'], // alias for frontend
                'issued_at' => $c['completion_date'],  // alias for frontend
            ]), $certs);

            $response->success('Danh sách chứng chỉ', $certs);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * POST /api/certificates/claim/:courseId
     * Học viên tự claim chứng chỉ khi hoàn thành 100%
     */
    public function claim(Request $request, Response $response, string $courseId): void
    {
        try {
            AuthMiddleware::handle($request, $response);

            // Role guard: chỉ student mới được claim
            if (($request->user->role ?? '') !== 'student') {
                $response->error('Chỉ học viên mới có thể nhận chứng chỉ.', 403);
                return;
            }

            $userId   = (int)$request->user->sub;
            $courseId = (int)$courseId;

            // Kiểm tra enrollment
            $enrolled = $this->enrollRepo->checkEnrollment($userId, $courseId);
            if (!$enrolled) {
                $response->error('Bạn chưa đăng ký khóa học này.', 403);
                return;
            }

            // Kiểm tra tiến độ 100%
            $progress = $this->enrollRepo->getProgressPercent($userId, $courseId);
            if ($progress < 100) {
                $response->error("Bạn chưa hoàn thành 100% khóa học (hiện tại: {$progress}%).", 400);
                return;
            }

            // Cấp chứng chỉ (idempotent — trả về nếu đã có)
            $cert = $this->certRepo->issue($userId, $courseId);

            // Tạo thông báo (silently fail nếu lỗi)
            try {
                $this->notifRepo->create(
                    $userId,
                    'certificate',
                    '🎓 Chứng chỉ đã được cấp!',
                    'Chúc mừng! Bạn đã nhận được chứng chỉ hoàn thành khóa học.'
                );
            } catch (Exception $notifEx) {
                // Non-critical — không throw
            }

            $certCode = $cert['certificate_code'];

            $response->success('Chứng chỉ đã được cấp thành công!', [
                'uuid'        => $certCode,                         // frontend expects 'uuid'
                'issued_at'   => $cert['completion_date'],          // frontend expects 'issued_at'
                'verify_url'  => "/certificate/verify.php?uuid={$certCode}",
            ], 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * GET /api/certificates/verify/:code
     * Public verification — ai cũng có thể verify
     */
    public function verify(Request $request, Response $response, string $uuid): void
    {
        try {
            // Support cả query param: /api/certificates/verify?uuid=xxx
            $code = $uuid ?: $request->query('uuid', '');
            $cert = $this->certRepo->findByCode($code);

            if (!$cert) {
                $response->error('Chứng chỉ không tồn tại hoặc đã bị thu hồi.', 404);
                return;
            }

            $response->success('Chứng chỉ hợp lệ', [
                'uuid'         => $cert['certificate_code'],
                'student_name' => $cert['username'],
                'course_title' => $cert['course_title'],
                'teacher_name' => $cert['teacher_name'],
                'issued_at'    => $cert['completion_date'],
                'score'        => $cert['score'],
                'valid'        => true,
            ]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/notifications
     */
    public function getNotifications(Request $request, Response $response): void
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;
            $limit  = min(50, max(1, (int)($request->query('limit') ?? 20)));
            $offset = max(0, (int)($request->query('offset') ?? 0));

            $notifs = $this->notifRepo->findByUser($userId, $limit, $offset);
            $unread = $this->notifRepo->countUnread($userId);

            $response->success('Thông báo', ['unread' => $unread, 'items' => $notifs]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/notifications/read-all
     */
    public function markAllRead(Request $request, Response $response): void
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;
            $this->notifRepo->markAllRead($userId);
            $response->success('Đã đánh dấu tất cả thông báo là đã đọc');
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/notifications/:id/read
     */
    public function markRead(Request $request, Response $response, string $id): void
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;
            $success = $this->notifRepo->markRead((int)$id, $userId);
            if ($success) {
                $response->success('Đã đánh dấu thông báo là đã đọc');
            } else {
                $response->error('Đánh dấu thất bại', 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
