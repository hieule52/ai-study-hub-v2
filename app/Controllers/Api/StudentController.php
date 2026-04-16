<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\EnrollmentRepository;
use App\Middlewares\AuthMiddleware;
use Exception;

class StudentController
{
    private EnrollmentRepository $enrollRepo;

    public function __construct()
    {
        $this->enrollRepo = new EnrollmentRepository();
    }

    public function getEnrolledCourses(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $courses = $this->enrollRepo->findEnrolledCoursesByUser($userId);
            
            $response->success("Danh sách khóa học đang theo học", $courses);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function enrollCourse(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $success = $this->enrollRepo->enroll($userId, (int)$courseId);
            if ($success) {
                $response->success("Đăng ký khóa học thành công!", null, 201);
            } else {
                $response->error("Bạn đã tham gia khóa học này rồi.", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
