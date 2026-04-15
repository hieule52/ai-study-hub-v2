<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\CourseService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class CourseController
{
    private CourseService $courseService;

    public function __construct()
    {
        $this->courseService = new CourseService();
    }

    public function index(Request $request, Response $response)
    {
        try {
            $page = (int)$request->input('page', 1);
            $limit = (int)$request->input('limit', 20);
            
            $courses = $this->courseService->getAllCourses($page, $limit);
            $response->success("Thành công", $courses);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function show(Request $request, Response $response, string $id)
    {
        try {
            // Require login before viewing specific courses
            AuthMiddleware::handle($request, $response);

            $course = $this->courseService->getCourseDetail((int)$id);
            $response->success("Chi tiết khóa học", (array)$course);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 404);
        }
    }

    public function store(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['admin', 'teacher']);

            $data = $request->all();
            $teacherId = $request->user->sub; // Lấy từ token subject
            
            $course = $this->courseService->createCourse($data, $teacherId);
            $response->success("Tạo khóa học thành công", (array)$course, 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }
}
