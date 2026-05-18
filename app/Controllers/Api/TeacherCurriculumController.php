<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\ChapterRepository;
use App\Repositories\LessonRepository;
use App\Repositories\QuizRepository;
use App\Repositories\CourseRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

/**
 * TeacherCurriculumController
 * CRUD đầy đủ cho quản lý giáo trình: Chapter, Lesson, Quiz.
 * Bao gồm: Create, Update, Delete, Reorder.
 */
class TeacherCurriculumController
{
    private ChapterRepository $chapterRepo;
    private LessonRepository $lessonRepo;
    private QuizRepository $quizRepo;
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->chapterRepo = new ChapterRepository();
        $this->lessonRepo = new LessonRepository();
        $this->quizRepo = new QuizRepository();
        $this->courseRepo = new CourseRepository();
    }

    // ==========================================
    // CHAPTER CRUD
    // ==========================================

    /**
     * Tạo chương mới
     * POST /api/teacher/chapters
     */
    public function createChapter(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title']) || empty($data['course_id'])) {
                throw new Exception("Thông tin thiếu");
            }

            $chapter = $this->chapterRepo->create($data); if ($chapter) $this->courseRepo->requireReapproval((int)$data['course_id']);
            if ($chapter) {
                $response->success("Tạo chương thành công", $chapter, 201);
            } else {
                $response->error("Tạo thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Cập nhật chương
     * PUT /api/teacher/chapters/:id
     */
    public function updateChapter(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title'])) {
                throw new Exception("Tiêu đề chương không được để trống.");
            }

            $success = $this->chapterRepo->update((int)$id, $data); if ($success) { $chapter = $this->chapterRepo->findById((int)$id); if ($chapter) $this->courseRepo->requireReapproval((int)$chapter['course_id']); }
            if ($success) {
                $response->success("Cập nhật chương thành công");
            } else {
                $response->error("Cập nhật thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Xóa chương (soft delete)
     * DELETE /api/teacher/chapters/:id
     */
    public function deleteChapter(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $chapter = $this->chapterRepo->findById((int)$id);
            if (!$chapter) {
                throw new Exception("Không tìm thấy chương.");
            }

            $this->chapterRepo->delete((int)$id); $this->courseRepo->requireReapproval((int)$chapter['course_id']);

            // Auto-update total lessons
            $this->courseRepo->updateTotalLessons($chapter['course_id']);

            $response->success("Đã xóa chương thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Sắp xếp lại thứ tự chương
     * PUT /api/teacher/chapters/reorder
     * Body: { course_id: int, orders: [{ id: int, order_index: int }, ...] }
     */
    public function reorderChapters(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['course_id']) || empty($data['orders'])) {
                throw new Exception("Thiếu thông tin sắp xếp.");
            }

            $this->chapterRepo->reorder((int)$data['course_id'], $data['orders']); $this->courseRepo->requireReapproval((int)$data['course_id']);
            $response->success("Đã sắp xếp lại thứ tự chương");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    // ==========================================
    // LESSON CRUD
    // ==========================================

    /**
     * Tạo bài học mới
     * POST /api/teacher/lessons
     * Body: { chapter_id, title, content_type, video_url?, video_filename?, content?, order_index?, is_free? }
     */
    public function createLesson(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title']) || empty($data['chapter_id'])) {
                throw new Exception("Thông tin thiếu");
            }
            
            $contentType = $data['content_type'] ?? 'video';
            if ($contentType !== 'quiz') {
                $contentStr = trim($data['content'] ?? '');
                if (empty($contentStr) || $contentStr === '<p><br></p>') {
                    throw new Exception("Vui lòng nhập nội dung chi tiết cho bài học.");
                }
            }

            // Tự động hóa trích xuất metadata và sinh nội dung bằng AI
            \App\Services\AiAutomationService::processLessonMetadata($data);

            $lesson = $this->lessonRepo->create($data); if ($lesson) $this->requireReapprovalByChapter((int)$data['chapter_id']);
            if ($lesson) {
                // Auto-update total lessons count
                $chapter = $this->chapterRepo->findById((int)$data['chapter_id']);
                if ($chapter) {
                    $this->courseRepo->updateTotalLessons($chapter['course_id']);
                }

                $response->success("Tạo bài học thành công", $lesson, 201);
            } else {
                $response->error("Tạo thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Cập nhật bài học
     * PUT /api/teacher/lessons/:id
     */
    public function updateLesson(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title'])) {
                throw new Exception("Tiêu đề bài học không được để trống.");
            }

            $contentType = $data['content_type'] ?? 'video';
            if ($contentType !== 'quiz') {
                $contentStr = trim($data['content'] ?? '');
                if (empty($contentStr) || $contentStr === '<p><br></p>') {
                    throw new Exception("Vui lòng nhập nội dung chi tiết cho bài học.");
                }
            }

            // Tự động hóa trích xuất metadata và sinh nội dung bằng AI
            \App\Services\AiAutomationService::processLessonMetadata($data);

            $oldLesson = $this->lessonRepo->findLessonById((int)$id);
            $this->requireReapprovalByLesson((int)$id); 
            
            $success = $this->lessonRepo->update((int)$id, $data);
            if ($success) {
                // Nếu cập nhật thành công, kiểm tra xem video đã bị thay đổi để xóa file cũ giải phóng bộ nhớ
                if ($oldLesson) {
                    $oldVideo = $oldLesson['video_filename'] ?? '';
                    $newVideo = $data['video_filename'] ?? '';
                    if (!empty($oldVideo) && $oldVideo !== $newVideo) {
                        $videoService = new \App\Services\VideoService();
                        $videoService->deleteVideoFile($oldVideo);
                    }
                }
                
                $response->success("Cập nhật bài học thành công");
            } else {
                $response->error("Cập nhật thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Xóa bài học (soft delete)
     * DELETE /api/teacher/lessons/:id
     */
    public function deleteLesson(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $lesson = $this->lessonRepo->findLessonById((int)$id);
            if (!$lesson) {
                throw new Exception("Không tìm thấy bài học.");
            }

            $this->lessonRepo->delete((int)$id); $this->requireReapprovalByChapter((int)$lesson['chapter_id']);

            // Xóa video file vật lý trên đĩa nếu bài học bị xóa để giải phóng dung lượng
            if (!empty($lesson['video_filename'])) {
                $videoService = new \App\Services\VideoService();
                $videoService->deleteVideoFile($lesson['video_filename']);
            }

            // Auto-update total lessons
            $chapter = $this->chapterRepo->findById($lesson['chapter_id']);
            if ($chapter) {
                $this->courseRepo->updateTotalLessons($chapter['course_id']);
            }

            $response->success("Đã xóa bài học thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Sắp xếp lại thứ tự bài học
     * PUT /api/teacher/lessons/reorder
     * Body: { chapter_id: int, orders: [{ id: int, order_index: int }, ...] }
     */
    public function reorderLessons(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['chapter_id']) || empty($data['orders'])) {
                throw new Exception("Thiếu thông tin sắp xếp.");
            }

            $this->lessonRepo->reorder((int)$data['chapter_id'], $data['orders']); $this->requireReapprovalByChapter((int)$data['chapter_id']);
            $response->success("Đã sắp xếp lại thứ tự bài học");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    // ==========================================
    // QUIZ CRUD
    // ==========================================

    /**
     * Tạo bài kiểm tra
     * POST /api/teacher/quizzes
     */
    public function createQuiz(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title']) || empty($data['lesson_id'])) {
                throw new Exception("Thông tin thiếu");
            }

            $quiz = $this->quizRepo->create($data); if ($quiz) $this->requireReapprovalByLesson((int)$data['lesson_id']);
            if ($quiz) {
                $response->success("Tạo bài kiểm tra thành công", $quiz, 201);
            } else {
                $response->error("Tạo thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Lấy toàn bộ quiz (câu hỏi + đáp án) theo lesson_id — dùng cho Quiz Builder
     * GET /api/teacher/lessons/:id/quiz
     */
    public function getFullQuiz(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $quiz = $this->quizRepo->getFullQuiz((int)$id);
            $response->success("OK", $quiz ?? []); // null nếu chưa có
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Lưu toàn bộ quiz + câu hỏi + đáp án theo lesson — dùng cho Quiz Builder
     * POST /api/teacher/lessons/:id/quiz
     * Body: { title: string, questions: [{ question, answers: [{ text, is_correct }] }] }
     */
    public function saveFullQuiz(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title'])) {
                throw new Exception("Tiêu đề bài kiểm tra không được để trống.");
            }
            if (empty($data['questions']) || !is_array($data['questions'])) {
                throw new Exception("Cần ít nhất 1 câu hỏi.");
            }

            $result = $this->quizRepo->saveFullQuiz(
                (int)$id, 
                $data['title'], 
                $data['questions'],
                $data['explanations'] ?? null,
                $data['hints'] ?? null,
                $data['ai_tags'] ?? null
            ); $this->requireReapprovalByLesson((int)$id);
            $response->success("Đã lưu bài kiểm tra thành công!", $result);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Cập nhật bài kiểm tra
     * PUT /api/teacher/quizzes/:id
     */
    public function updateQuiz(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $data = $request->all();
            if (empty($data['title'])) {
                throw new Exception("Tiêu đề bài kiểm tra không được để trống.");
            }

            $success = $this->quizRepo->update((int)$id, $data); if ($success) { $quiz = $this->quizRepo->findById((int)$id); if ($quiz) $this->requireReapprovalByLesson((int)$quiz['lesson_id']); }
            if ($success) {
                $response->success("Cập nhật bài kiểm tra thành công");
            } else {
                $response->error("Cập nhật thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    /**
     * Xóa bài kiểm tra (soft delete)
     * DELETE /api/teacher/quizzes/:id
     */
    public function deleteQuiz(Request $request, Response $response, string $id)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $quiz = $this->quizRepo->findById((int)$id); $this->quizRepo->delete((int)$id); if ($quiz) $this->requireReapprovalByLesson((int)$quiz['lesson_id']);
            $response->success("Đã xóa bài kiểm tra thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    private function requireReapprovalByChapter(int $chapterId) {
        $chapter = $this->chapterRepo->findById($chapterId);
        if ($chapter) {
            $this->courseRepo->requireReapproval((int)$chapter['course_id']);
        }
    }

    private function requireReapprovalByLesson(int $lessonId) {
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if ($lesson) {
            $this->requireReapprovalByChapter((int)$lesson['chapter_id']);
        }
    }
}