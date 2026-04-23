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
        return $this->courseRepo->update($courseId, $data);
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
