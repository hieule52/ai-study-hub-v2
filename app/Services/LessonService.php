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

    /**
     * Fix N+1 query: load tất cả chapters + tất cả lessons trong 2 query
     * thay vì 1 + N queries (1 per chapter)
     */
    public function getCourseCurriculum(int $courseId): array
    {
        $chapters = $this->lessonRepo->findChaptersByCourse($courseId);
        if (empty($chapters)) return [];

        // Lấy tất cả lessons của toàn bộ chapters trong 1 query
        $chapterIds = array_column($chapters, 'id');
        $allLessons = $this->lessonRepo->findLessonsByChapterIds($chapterIds);

        // Group lessons by chapter_id (in memory) — O(n) không phải O(n²)
        $lessonsByChapter = [];
        foreach ($allLessons as $lesson) {
            $lessonsByChapter[$lesson['chapter_id']][] = $lesson;
        }

        // Gắn lessons vào chapter tương ứng
        foreach ($chapters as &$chapter) {
            $chapter['lessons'] = $lessonsByChapter[$chapter['id']] ?? [];
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

        // Retrieve course_id to update course progress
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT course_id FROM chapters WHERE id = :id");
        $stmt->execute(['id' => $lesson['chapter_id']]);
        $courseId = (int)$stmt->fetchColumn();

        if ($courseId) {
            $enrollRepo = new \App\Repositories\EnrollmentRepository();
            $percent = $enrollRepo->updateProgress($userId, $courseId);

            // Auto issue certificate if 100%
            if ($percent == 100) {
                $certRepo = new \App\Repositories\CertificateRepository();
                if (!$certRepo->findByUserAndCourse($userId, $courseId)) {
                    $certRepo->issue($userId, $courseId, 100.0);
                }
            }
            return $percent;
        }

        return 0;
    }
}
