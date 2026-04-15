<?php

namespace App\Services;

use App\Repositories\LessonRepository;
use Exception;

class LessonService
{
    private LessonRepository $lessonRepo;

    public function __construct()
    {
        $this->lessonRepo = new LessonRepository();
    }

    public function getCourseCurriculum(int $courseId): array
    {
        $chapters = $this->lessonRepo->findChaptersByCourse($courseId);
        
        foreach ($chapters as &$chapter) {
            $chapter['lessons'] = $this->lessonRepo->findLessonsByChapter($chapter['id']);
        }

        return $chapters;
    }

    public function getLessonDetail(int $lessonId)
    {
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Không tìm thấy bài học này.");
        }
        return $lesson;
    }

    public function markLessonCompleted(int $userId, int $lessonId)
    {
        // Phải check xem lesson này có tồn tại không
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Lesson không tồn tại.");
        }

        $success = $this->lessonRepo->markProgress($userId, $lessonId);
        if (!$success) {
            throw new Exception("Không thể cập nhật tiến trình bài học.");
        }
        return true;
    }
}
