<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\LessonService;
use App\Services\LessonGuardService;
use App\Middlewares\AuthMiddleware;
use Exception;

class LessonController
{
    private LessonService $lessonService;
    private LessonGuardService $guardService;

    public function __construct()
    {
        $this->lessonService = new LessonService();
        $this->guardService  = new LessonGuardService();
    }

    /**
     * GET /api/courses/:id/curriculum
     * Public với guest, nhưng truyền userId nếu có JWT để trả về progress
     */
    public function curriculum(Request $request, Response $response, string $courseId)
    {
        try {
            $userRole = 'guest';
            $userId   = 0;

            $authHeader = $request->getHeader('Authorization');
            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $payload = \App\Core\JWTHandler::decode($matches[1]);
                if ($payload) {
                    $userRole = $payload->role ?? 'guest';
                    $userId   = (int)($payload->sub ?? 0);
                }
            }

            $curriculum = $this->lessonService->getCourseCurriculum((int)$courseId, $userRole, $userId);
            $response->success("Giáo trình khóa học", $curriculum);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    /**
     * GET /api/lessons/:id
     * Bảo vệ chống skip bài: nếu học viên cố truy cập bài bị khoá → 403 + redirect_lesson_id
     */
    public function show(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userRole = $request->user->role ?? 'guest';
            $userId   = (int)($request->user->sub ?? 0);

            // Sequential lock guard — chỉ áp dụng cho student
            if ($userRole === 'student' && $userId > 0) {
                $lid = (int)$lessonId;
                if (!$this->guardService->canAccessLesson($userId, $lid)) {
                    // Tìm course_id để cung cấp redirect_lesson_id chính xác
                    $db = \App\Core\Database::connect();
                    $s  = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                    $s->execute([$lid]);
                    $courseId = (int)$s->fetchColumn();
                    $redirectId = $courseId ? $this->guardService->getCurrentAllowedLesson($userId, $courseId) : 0;

                    $response->json([
                        'success'          => false,
                        'message'          => 'Bạn phải hoàn thành bài học trước đó trước khi truy cập bài này.',
                        'redirect_lesson_id' => $redirectId,
                    ], 403);
                    return;
                }
            }

            $lesson = $this->lessonService->getLessonDetail((int)$lessonId, $userRole, $userId);
            $response->success("Chi tiết bài giảng", $lesson);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    /**
     * POST /api/lessons/:id/complete
     * Đánh dấu bài hoàn thành thủ công (nút "Đánh dấu Đã Học")
     */
    public function complete(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userRole = $request->user->role ?? 'guest';
            if ($userRole !== 'student') {
                $response->error("Chỉ học viên mới có thể đánh dấu hoàn thành bài học.", 403);
                return;
            }

            $userId = (int)$request->user->sub;
            $lid    = (int)$lessonId;

            // Sequential lock guard
            if (!$this->guardService->canAccessLesson($userId, $lid)) {
                $db = \App\Core\Database::connect();
                $s  = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                $s->execute([$lid]);
                $courseId   = (int)$s->fetchColumn();
                $redirectId = $courseId ? $this->guardService->getCurrentAllowedLesson($userId, $courseId) : 0;

                $response->json([
                    'success'            => false,
                    'message'            => 'Bạn phải hoàn thành bài học trước đó trước khi đánh dấu bài này.',
                    'redirect_lesson_id' => $redirectId,
                ], 403);
                return;
            }

            $result = $this->lessonService->markLessonCompleted($userId, $lid);
            $response->success("Đã lưu tiến độ hoàn thành bài học thành công.", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * POST /api/lessons/:id/progress
     * Cập nhật video_progress / text_progress liên tục từ client
     * Body: { video_progress: int, text_progress: int }
     */
    public function updateProgress(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userRole = $request->user->role ?? 'guest';
            if ($userRole !== 'student') {
                $response->error("Chỉ học viên mới có thể cập nhật tiến độ.", 403);
                return;
            }

            $userId = (int)$request->user->sub;
            $lid    = (int)$lessonId;

            // Sequential lock guard
            if (!$this->guardService->canAccessLesson($userId, $lid)) {
                $db = \App\Core\Database::connect();
                $s  = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                $s->execute([$lid]);
                $courseId   = (int)$s->fetchColumn();
                $redirectId = $courseId ? $this->guardService->getCurrentAllowedLesson($userId, $courseId) : 0;

                $response->json([
                    'success'            => false,
                    'message'            => 'Bài học này chưa được mở khoá.',
                    'redirect_lesson_id' => $redirectId,
                ], 403);
                return;
            }

            $videoProgress = (int)($request->input('video_progress') ?? 0);
            $textProgress  = (int)($request->input('text_progress') ?? 0);

            $result = $this->lessonService->updateProgress($userId, $lid, $videoProgress, $textProgress);
            $response->success("Đã cập nhật tiến độ.", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
