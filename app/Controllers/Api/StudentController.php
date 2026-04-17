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

            // Secure: Check if course is premium
            $courseRepo = new \App\Repositories\CourseRepository();
            $course = $courseRepo->findById((int)$courseId);
            
            if ($course && $course->is_premium == 1) {
                $response->error("Khóa học này là khóa học trả phí. Vui lòng thanh toán để mở khóa.", 403);
                return;
            }

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

    public function verifyPurchase(Request $request, Response $response, string $courseId)
    {
        // This endpoint is hit after JS successfully verifies the JSON from Apps Script
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $success = $this->enrollRepo->enroll($userId, (int)$courseId);
            if ($success) {
                $response->success("Xác nhận giao dịch và mở khóa thành công!", null, 201);
            } else {
                $response->error("Khóa học này đã được kích hoạt trước đó.", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
