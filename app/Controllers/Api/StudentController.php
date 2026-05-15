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
                $response->success("Đăng ký khóa học thành công!", [], 201);
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
                $response->success("Xác nhận giao dịch và mở khóa thành công!", [], 201);
            } else {
                $response->error("Khóa học này đã được kích hoạt trước đó.", 400);
            }
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function getStats(Request $request, Response $response)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $stats = $this->enrollRepo->getLearningStats((int)$userId);
            
            $response->success("Learning stats retrieved", $stats);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 400);
        }
    }

    public function getMyReview(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $stmt = \App\Core\Database::connect()->prepare("
                SELECT rating, comment, created_at 
                FROM course_reviews 
                WHERE user_id = ? AND course_id = ?
            ");
            $stmt->execute([$userId, $courseId]);
            $review = $stmt->fetch(\PDO::FETCH_ASSOC);

            $response->success("Thông tin đánh giá của bạn", $review ?: []);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function submitReview(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;
            
            $rating = (int)$request->input('rating');
            $comment = $request->input('comment', '');

            if ($rating < 1 || $rating > 5) {
                $response->error("Đánh giá phải từ 1 đến 5 sao.", 400);
                return;
            }

            // Check if 100% completed
            $stmt = \App\Core\Database::connect()->prepare("SELECT progress_percent FROM enrollments WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$userId, $courseId]);
            $progress = $stmt->fetchColumn();

            if ($progress === false || $progress < 100) {
                $response->error("Bạn cần hoàn thành 100% khóa học mới có thể đánh giá.", 400);
                return;
            }

            $stmt = \App\Core\Database::connect()->prepare("
                INSERT INTO course_reviews (course_id, user_id, rating, comment) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
            ");
            $stmt->execute([$courseId, $userId, $rating, $comment]);

            $response->success("Cảm ơn bạn đã đánh giá khóa học!", [], 201);
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
