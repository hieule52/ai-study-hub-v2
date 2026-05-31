<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\QuizService;
use App\Services\LessonGuardService;
use App\Middlewares\AuthMiddleware;
use Exception;

class QuizController
{
    private QuizService $quizService;
    private LessonGuardService $guardService;

    public function __construct()
    {
        $this->quizService   = new QuizService();
        $this->guardService  = new LessonGuardService();
    }

    /**
     * GET /api/lessons/:id/quiz
     * Guard: học viên chỉ lấy được quiz nếu bài học đã được mở khoá
     */
    public function showByLesson(Request $request, Response $response, string $lessonId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userRole = $request->user->role ?? 'guest';
            $userId   = (int)($request->user->sub ?? 0);

            // Sequential lock guard
            if ($userRole === 'student' && $userId > 0) {
                $lid = (int)$lessonId;
                if (!$this->guardService->canAccessLesson($userId, $lid)) {
                    $db = \App\Core\Database::connect();
                    $s  = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                    $s->execute([$lid]);
                    $courseId   = (int)$s->fetchColumn();
                    $redirectId = $courseId ? $this->guardService->getCurrentAllowedLesson($userId, $courseId) : 0;

                    $response->json([
                        'success'            => false,
                        'message'            => 'Bài học này chưa được mở khoá. Hãy hoàn thành bài học trước.',
                        'redirect_lesson_id' => $redirectId,
                    ], 403);
                    return;
                }
            }

            $quiz = $this->quizService->getQuizForStudent((int)$lessonId, $userRole, $userId);
            $response->success("Tải bộ câu hỏi thành công", $quiz);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    /**
     * POST /api/quizzes/:id/submit
     * Guard: kiểm tra bài học gắn với quiz có bị khoá không
     */
    public function submit(Request $request, Response $response, string $quizId)
    {
        try {
            AuthMiddleware::handle($request, $response);

            $userId   = $request->user->sub;
            $userRole = $request->user->role ?? 'guest';

            // Sequential lock guard — lấy lesson_id từ quiz để kiểm tra
            if ($userRole === 'student' && $userId > 0) {
                $db = \App\Core\Database::connect();
                $s  = $db->prepare("SELECT lesson_id FROM quizzes WHERE id = ? AND deleted_at IS NULL LIMIT 1");
                $s->execute([(int)$quizId]);
                $quizLessonId = (int)$s->fetchColumn();

                if ($quizLessonId && !$this->guardService->canAccessLesson((int)$userId, $quizLessonId)) {
                    $s2 = $db->prepare("SELECT c.course_id FROM chapters c JOIN lessons l ON l.chapter_id = c.id WHERE l.id = ? LIMIT 1");
                    $s2->execute([$quizLessonId]);
                    $courseId   = (int)$s2->fetchColumn();
                    $redirectId = $courseId ? $this->guardService->getCurrentAllowedLesson((int)$userId, $courseId) : 0;

                    $response->json([
                        'success'            => false,
                        'message'            => 'Bài kiểm tra này chưa được mở khoá.',
                        'redirect_lesson_id' => $redirectId,
                    ], 403);
                    return;
                }
            }

            $studentAnswers = $request->input('answers', []); // ['12' => 45, '13' => 48]

            $result = $this->quizService->submitAndCalculateScore($userId, (int)$quizId, (array)$studentAnswers, $userRole);
            $response->success("Nộp bài thi thành công", $result, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * GET /api/quizzes/:id/history
     * Lịch sử làm bài của học viên
     */
    public function history(Request $request, Response $response, string $quizId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = (int)$request->user->sub;

            $history = $this->quizService->getHistory($userId, (int)$quizId);
            $response->success("Lịch sử làm bài", $history);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
