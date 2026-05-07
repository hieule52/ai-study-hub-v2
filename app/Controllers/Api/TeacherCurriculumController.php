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

            $chapter = $this->chapterRepo->create($data);
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

            $success = $this->chapterRepo->update((int)$id, $data);
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

            $this->chapterRepo->delete((int)$id);

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

            $this->chapterRepo->reorder((int)$data['course_id'], $data['orders']);
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

            $lesson = $this->lessonRepo->create($data);
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

            $success = $this->lessonRepo->update((int)$id, $data);
            if ($success) {
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

            $this->lessonRepo->delete((int)$id);

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

            $this->lessonRepo->reorder((int)$data['chapter_id'], $data['orders']);
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

            $quiz = $this->quizRepo->create($data);
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

            $success = $this->quizRepo->update((int)$id, $data);
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

            $this->quizRepo->delete((int)$id);
            $response->success("Đã xóa bài kiểm tra thành công");
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
