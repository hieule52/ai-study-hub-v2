<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\TeacherService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;
use Exception;

class TeacherController
{
    private TeacherService $teacherService;

    public function __construct()
    {
        $this->teacherService = new TeacherService();
    }

    public function dashboard(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $teacherId = $request->user->sub;
            $stats = $this->teacherService->getDashboardStats($teacherId);
            $courses = $this->teacherService->getTeacherCourses($teacherId);

            $response->success("Dashboard data", [
                'stats' => $stats,
                'courses' => $courses
            ]);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function updateCourse(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $teacherId = $request->user->sub;
            $data = $request->all();
            
            $success = $this->teacherService->updateCourse($teacherId, (int)$courseId, $data);
            if ($success) {
                $response->success("Cập nhật khóa học thành công");
            } else {
                $response->error("Cập nhật thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function deleteCourse(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $teacherId = $request->user->sub;
            $success = $this->teacherService->deleteCourse($teacherId, (int)$courseId);
            if ($success) {
                $response->success("Xóa khóa học thành công");
            } else {
                $response->error("Xóa thất bại", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function students(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            RoleMiddleware::handle($request, $response, ['teacher', 'admin']);

            $teacherId = $request->user->sub;
            $students = $this->teacherService->getTeacherStudents($teacherId);
            $response->success("Danh sách học viên", $students);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
