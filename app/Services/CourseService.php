<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Exception;

class CourseService
{
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->courseRepo = new CourseRepository();
    }

    public function getAllCourses(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        return $this->courseRepo->findAll($limit, $offset);
    }

    public function getCourseDetail(int $id)
    {
        $course = $this->courseRepo->findById($id);
        if (!$course) {
            throw new Exception("Không tìm thấy khóa học.");
        }
        return $course;
    }

    public function createCourse(array $data, int $teacherId)
    {
        if (empty($data['title'])) {
            throw new Exception("Tiêu đề khóa học không được để trống.");
        }

        $price = isset($data['price']) ? (float)$data['price'] : 0;
        $is_premium = $price > 0 ? 1 : 0;

        $newCourse = $this->courseRepo->create([
            'teacher_id' => $teacherId,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'thumbnail' => $data['thumbnail'] ?? null,
            'price' => $price,
            'is_premium' => $is_premium,
            'category_id' => $data['category_id'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'estimated_duration' => $data['estimated_duration'] ?? 0,
            'ai_course_summary' => $data['ai_course_summary'] ?? null,
            'ai_keywords' => $data['ai_keywords'] ?? null,
            'ai_focus' => $data['ai_focus'] ?? null
        ]);

        if (!$newCourse) {
            throw new Exception("Lỗi hệ thống khi tạo khóa học.");
        }

        return $newCourse;
    }
}
