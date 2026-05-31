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
                $response->success("Ghi danh khóa học thành công!", [], 201);
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

            $rating  = (int)$request->input('rating');
            $comment = $request->input('comment', '');

            if ($rating < 1 || $rating > 5) {
                $response->error("Đánh giá phải từ 1 đến 5 sao.", 400);
                return;
            }

            // Check if 100% completed
            $db   = \App\Core\Database::connect();
            $stmt = $db->prepare("SELECT progress_percent, course_status FROM enrollments WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$userId, $courseId]);
            $enrollment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$enrollment || (int)$enrollment['progress_percent'] < 100) {
                $response->error("Bạn cần hoàn thành 100% khóa học mới có thể đánh giá.", 400);
                return;
            }

            // Lưu đánh giá
            $stmt = $db->prepare("
                INSERT INTO course_reviews (course_id, user_id, rating, comment)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
            ");
            $stmt->execute([$courseId, $userId, $rating, $comment]);

            // Tự động cấp chứng chỉ nếu khoá học đã hoàn thành
            $certIssued = false;
            try {
                $courseService = new \App\Services\CourseService();
                $certIssued    = $courseService->tryIssueCertificateAfterReview((int)$userId, (int)$courseId);
            } catch (\Exception $ce) {
                // Non-critical
            }

            $response->success(
                $certIssued
                    ? "🎓 Cảm ơn bạn đã đánh giá! Chứng chỉ của bạn đã được cấp."
                    : "Cảm ơn bạn đã đánh giá khóa học!",
                ['cert_issued' => $certIssued],
                201
            );
        } catch (Exception $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function resetProgress(Request $request, Response $response, string $courseId)
    {
        try {
            AuthMiddleware::handle($request, $response);
            $userId = $request->user->sub;

            $db = \App\Core\Database::connect();
            $db->beginTransaction();

            // 1. Lấy danh sách lesson_id của khóa học này để xóa trong bảng lesson_progress
            $stmtLessons = $db->prepare("
                SELECT l.id FROM lessons l
                JOIN chapters c ON l.chapter_id = c.id
                WHERE c.course_id = ?
            ");
            $stmtLessons->execute([$courseId]);
            $lessonIds = $stmtLessons->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($lessonIds)) {
                $placeholders = implode(',', array_fill(0, count($lessonIds), '?'));
                // Xóa tiến độ học của user này đối với các bài học thuộc khóa học này
                $stmtDelProg = $db->prepare("
                    DELETE FROM lesson_progress 
                    WHERE user_id = ? AND lesson_id IN ($placeholders)
                ");
                $stmtDelProg->execute(array_merge([$userId], $lessonIds));
            }

            // 2. Reset progress_percent, completed_at, course_status trong bảng enrollments
            $stmtResetEnroll = $db->prepare("
                UPDATE enrollments 
                SET progress_percent = 0, completed_at = NULL, course_status = 'learning'
                WHERE user_id = ? AND course_id = ?
            ");
            $stmtResetEnroll->execute([$userId, $courseId]);

            // 3. Xóa chứng chỉ đã cấp nếu có (để học viên học lại có thể nhận lại)
            $stmtDelCert = $db->prepare("
                DELETE FROM certificates 
                WHERE student_id = ? AND course_id = ?
            ");
            $stmtDelCert->execute([$userId, $courseId]);

            // 4. Xóa đánh giá đã gửi nếu có
            $stmtDelReview = $db->prepare("
                DELETE FROM course_reviews 
                WHERE user_id = ? AND course_id = ?
            ");
            $stmtDelReview->execute([$userId, $courseId]);

            $db->commit();
            $response->success("Đã cài đặt lại toàn bộ tiến độ học tập. Chúc bạn học lại vui vẻ!", []);
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $response->error($e->getMessage(), 500);
        }
    }
}

