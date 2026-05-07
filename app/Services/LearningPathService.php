<?php

namespace App\Services;

use App\Repositories\LearningPathRepository;
use App\Repositories\EnrollmentRepository;
use Exception;

/**
 * LearningPathService
 * Business logic cho hệ thống lộ trình học.
 * Tính tiến độ tổng dựa trên enrollment + completion các khóa con.
 */
class LearningPathService
{
    private LearningPathRepository $pathRepo;
    private EnrollmentRepository $enrollRepo;

    public function __construct()
    {
        $this->pathRepo = new LearningPathRepository();
        $this->enrollRepo = new EnrollmentRepository();
    }

    /**
     * Lấy tất cả lộ trình published (cho student)
     */
    public function getAllPublished(): array
    {
        return $this->pathRepo->findAllPublished();
    }

    /**
     * Lấy tất cả lộ trình (cho admin/teacher)
     */
    public function getAll(): array
    {
        return $this->pathRepo->findAll();
    }

    /**
     * Chi tiết lộ trình kèm danh sách khóa học
     */
    public function getPathDetail(int $id): array
    {
        $path = $this->pathRepo->findByIdWithCourses($id);
        if (!$path) {
            throw new Exception("Không tìm thấy lộ trình học.");
        }
        return $path;
    }

    /**
     * Chi tiết lộ trình kèm tiến độ của user
     */
    public function getPathDetailWithProgress(int $pathId, int $userId): array
    {
        $path = $this->getPathDetail($pathId);

        // Kiểm tra enrollment
        $enrollment = $this->pathRepo->getUserEnrollment($userId, $pathId);
        $path['is_enrolled'] = $enrollment !== null;
        $path['enrollment'] = $enrollment;

        // Đánh dấu khóa nào đã enrolled
        if (!empty($path['courses'])) {
            foreach ($path['courses'] as &$course) {
                $course['is_enrolled'] = $this->enrollRepo->checkEnrollment($userId, $course['course_id']);
            }
        }

        return $path;
    }

    /**
     * Tạo lộ trình mới (Teacher/Admin)
     */
    public function createPath(array $data, int $creatorId): array
    {
        if (empty($data['title'])) {
            throw new Exception("Tiêu đề lộ trình không được để trống.");
        }

        $data['created_by'] = $creatorId;
        $path = $this->pathRepo->create($data);
        
        if (!$path) {
            throw new Exception("Lỗi hệ thống khi tạo lộ trình.");
        }

        return $path;
    }

    /**
     * Cập nhật lộ trình
     */
    public function updatePath(int $id, array $data): bool
    {
        $path = $this->pathRepo->findById($id);
        if (!$path) {
            throw new Exception("Không tìm thấy lộ trình.");
        }

        return $this->pathRepo->update($id, $data);
    }

    /**
     * Thêm khóa học vào lộ trình
     */
    public function addCourseToPath(int $pathId, int $courseId, int $order = 0, bool $required = true): bool
    {
        $path = $this->pathRepo->findById($pathId);
        if (!$path) {
            throw new Exception("Không tìm thấy lộ trình.");
        }

        return $this->pathRepo->addCourse($pathId, $courseId, $order, $required);
    }

    /**
     * Xóa khóa học khỏi lộ trình
     */
    public function removeCourseFromPath(int $pathId, int $courseId): bool
    {
        return $this->pathRepo->removeCourse($pathId, $courseId);
    }

    /**
     * Student đăng ký lộ trình
     * Tự động enroll vào tất cả khóa học miễn phí trong lộ trình
     */
    public function enrollInPath(int $userId, int $pathId): array
    {
        $path = $this->pathRepo->findByIdWithCourses($pathId);
        if (!$path) {
            throw new Exception("Không tìm thấy lộ trình.");
        }

        if (!$path['is_published']) {
            throw new Exception("Lộ trình chưa được công bố.");
        }

        // Enroll vào lộ trình
        $enrolled = $this->pathRepo->enrollUser($userId, $pathId);
        
        // Tự động enroll vào các khóa miễn phí
        $autoEnrolled = [];
        if (!empty($path['courses'])) {
            foreach ($path['courses'] as $course) {
                if (!$course['is_premium'] && $course['status'] === 'approved') {
                    $result = $this->enrollRepo->enroll($userId, $course['course_id']);
                    if ($result) {
                        $autoEnrolled[] = $course['title'];
                    }
                }
            }
        }

        return [
            'path_enrolled' => $enrolled,
            'auto_enrolled_courses' => $autoEnrolled,
            'message' => $enrolled 
                ? 'Đăng ký lộ trình thành công!' 
                : 'Bạn đã đăng ký lộ trình này trước đó.'
        ];
    }

    /**
     * Tính toán tiến độ tổng của user trong lộ trình
     * Dựa trên số khóa bắt buộc đã hoàn thành (progress >= 100%)
     */
    public function calculateProgress(int $userId, int $pathId): array
    {
        $path = $this->pathRepo->findByIdWithCourses($pathId);
        if (!$path) {
            throw new Exception("Không tìm thấy lộ trình.");
        }

        $totalRequired = 0;
        $completedRequired = 0;
        $courseProgresses = [];

        if (!empty($path['courses'])) {
            foreach ($path['courses'] as $course) {
                $enrolled = $this->enrollRepo->checkEnrollment($userId, $course['course_id']);

                // Lấy tiến độ enrollment
                $progress = 0;
                if ($enrolled) {
                    $enrollData = $this->getEnrollmentProgress($userId, $course['course_id']);
                    $progress = $enrollData['progress_percent'] ?? 0;
                }

                $courseProgresses[] = [
                    'course_id' => $course['course_id'],
                    'title' => $course['title'],
                    'is_required' => (bool) $course['is_required'],
                    'is_enrolled' => $enrolled,
                    'progress_percent' => $progress,
                    'is_completed' => $progress >= 100
                ];

                if ($course['is_required']) {
                    $totalRequired++;
                    if ($progress >= 100) {
                        $completedRequired++;
                    }
                }
            }
        }

        $overallPercent = $totalRequired > 0 
            ? round(($completedRequired / $totalRequired) * 100) 
            : 0;

        // Cập nhật tiến độ vào database
        $this->pathRepo->updateProgress($userId, $pathId, $overallPercent);

        return [
            'path_id' => $pathId,
            'overall_percent' => $overallPercent,
            'completed_required' => $completedRequired,
            'total_required' => $totalRequired,
            'courses' => $courseProgresses
        ];
    }

    /**
     * Helper: Lấy enrollment progress cho 1 khóa
     */
    private function getEnrollmentProgress(int $userId, int $courseId): array
    {
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT progress_percent FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ?: ['progress_percent' => 0];
    }

    /**
     * Lấy tất cả lộ trình user đã enrolled
     */
    public function getUserPaths(int $userId): array
    {
        return $this->pathRepo->getUserEnrolledPaths($userId);
    }
}
