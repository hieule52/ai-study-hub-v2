<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\ChapterRepository;
use App\Repositories\LessonRepository;
use App\Repositories\QuizRepository;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class TeacherCurriculumController
{
    private ChapterRepository $chapterRepo;
    private LessonRepository $lessonRepo;
    private QuizRepository $quizRepo;

    public function __construct()
    {
        $this->chapterRepo = new ChapterRepository();
        $this->lessonRepo = new LessonRepository();
        $this->quizRepo = new QuizRepository();
    }

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
                $response->success("Tạo bài học thành công", $lesson, 201);
            } else {
                $response->error("Tạo thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

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
}
