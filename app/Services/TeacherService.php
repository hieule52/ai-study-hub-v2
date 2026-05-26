<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\ChapterRepository;
use App\Repositories\LessonRepository;
use App\Repositories\QuizRepository;
use Exception;

class TeacherService
{
    private CourseRepository $courseRepo;
    private EnrollmentRepository $enrollRepo;
    private ChapterRepository $chapterRepo;
    private LessonRepository $lessonRepo;
    private QuizRepository $quizRepo;

    public function __construct()
    {
        $this->courseRepo = new CourseRepository();
        $this->enrollRepo = new EnrollmentRepository();
        $this->chapterRepo = new ChapterRepository();
        $this->lessonRepo = new LessonRepository();
        $this->quizRepo = new QuizRepository();
    }

    public function getDashboardStats(int $teacherId): array
    {
        return $this->courseRepo->getTeacherStats($teacherId);
    }

    public function getTeacherCourses(int $teacherId): array
    {
        return $this->courseRepo->findTeacherCourses($teacherId);
    }

    public function updateCourse(int $teacherId, int $courseId, array $data): bool
    {
        $course = $this->courseRepo->findById($courseId);
        if (!$course || $course->teacher_id != $teacherId) {
            throw new Exception("Không tìm thấy khóa học hoặc bạn không có quyền sửa.");
        }
        
        $oldState = [
            'title' => $course->title,
            'description' => $course->description,
            'thumbnail' => $course->thumbnail,
            'price' => $course->price,
            'is_premium' => $course->is_premium,
            'category_id' => $course->category_id,
            'level' => $course->level,
            'estimated_duration' => $course->estimated_duration,
            'ai_course_summary' => $course->ai_course_summary,
            'ai_keywords' => $course->ai_keywords,
            'ai_focus' => $course->ai_focus
        ];

        $success = $this->courseRepo->update($courseId, $data);
        if ($success) {
            $newCourse = $this->courseRepo->findById($courseId);
            if ($newCourse) {
                $newState = [
                    'title' => $newCourse->title,
                    'description' => $newCourse->description,
                    'thumbnail' => $newCourse->thumbnail,
                    'price' => $newCourse->price,
                    'is_premium' => $newCourse->is_premium,
                    'category_id' => $newCourse->category_id,
                    'level' => $newCourse->level,
                    'estimated_duration' => $newCourse->estimated_duration,
                    'ai_course_summary' => $newCourse->ai_course_summary,
                    'ai_keywords' => $newCourse->ai_keywords,
                    'ai_focus' => $newCourse->ai_focus
                ];

                \App\Services\ChangeTrackingService::trackChange(
                    $courseId,
                    'course',
                    $courseId,
                    'update',
                    $oldState,
                    $newState,
                    $teacherId
                );
            }
        }
        return $success;
    }

    public function deleteCourse(int $teacherId, int $courseId): bool
    {
        $course = $this->courseRepo->findById($courseId);
        if (!$course || $course->teacher_id != $teacherId) {
            throw new Exception("Không tìm thấy khóa học hoặc không có quyền.");
        }
        return $this->courseRepo->delete($courseId);
    }

    public function getTeacherStudents(int $teacherId): array
    {
        return $this->enrollRepo->findStudentsByTeacher($teacherId);
    }
}
