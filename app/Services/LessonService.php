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
    public function getCourseCurriculum(int $courseId, string $userRole = 'student'): array
    {
        $chapters = $this->lessonRepo->findChaptersByCourse($courseId);
        if (empty($chapters)) return [];

        // Check if course is in pending_reapproval status
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("SELECT status FROM courses WHERE id = ?");
        $stmtStatus->execute([$courseId]);
        $courseStatus = $stmtStatus->fetchColumn();

        // Lấy tất cả lessons của toàn bộ chapters trong 1 query
        $chapterIds = array_column($chapters, 'id');
        $allLessons = $this->lessonRepo->findLessonsByChapterIds($chapterIds);

        if ($courseStatus === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            // Fetch unapproved change logs to reconstruct approved version for student
            $stmtLogs = $db->prepare("SELECT * FROM course_change_logs WHERE course_id = ? ORDER BY id ASC");
            $stmtLogs->execute([$courseId]);
            $logs = $stmtLogs->fetchAll(\PDO::FETCH_ASSOC);

            $pendingChapters = [];
            $pendingLessons = [];

            foreach ($logs as $log) {
                $etype = $log['entity_type'];
                $eid = (int)$log['entity_id'];
                $atype = $log['action_type'];
                $oldSnap = $log['old_snapshot'] ? json_decode($log['old_snapshot'], true) : null;

                if ($etype === 'chapter') {
                    if (!isset($pendingChapters[$eid])) {
                        $pendingChapters[$eid] = ['action' => $atype, 'snapshot' => $oldSnap];
                    }
                } elseif ($etype === 'lesson') {
                    if (!isset($pendingLessons[$eid])) {
                        $pendingLessons[$eid] = ['action' => $atype, 'snapshot' => $oldSnap];
                    }
                }
            }

            // 1. Reconstruct Chapters
            $activeChapters = [];
            foreach ($chapters as $ch) {
                $chId = (int)$ch['id'];
                if (isset($pendingChapters[$chId])) {
                    $info = $pendingChapters[$chId];
                    if ($info['action'] === 'create') {
                        continue; // Hide unapproved chapter
                    } elseif ($info['action'] === 'update' && $info['snapshot']) {
                        foreach ($info['snapshot'] as $k => $v) {
                            $ch[$k] = $v;
                        }
                    }
                }
                $activeChapters[] = $ch;
            }
            // Restore deleted chapters
            foreach ($pendingChapters as $chId => $info) {
                if ($info['action'] === 'delete' && $info['snapshot']) {
                    $activeChapters[] = $info['snapshot'];
                }
            }
            usort($activeChapters, function($a, $b) {
                return ($a['order_index'] ?? $a['sort_order'] ?? 0) - ($b['order_index'] ?? $b['sort_order'] ?? 0);
            });
            $chapters = $activeChapters;

            // 2. Reconstruct Lessons
            $activeLessons = [];
            foreach ($allLessons as $lesson) {
                $lesId = (int)$lesson['id'];
                if (isset($pendingLessons[$lesId])) {
                    $info = $pendingLessons[$lesId];
                    if ($info['action'] === 'create') {
                        continue; // Hide unapproved lesson
                    } elseif ($info['action'] === 'update' && $info['snapshot']) {
                        foreach ($info['snapshot'] as $k => $v) {
                            $lesson[$k] = $v;
                        }
                    }
                }
                $activeLessons[] = $lesson;
            }
            // Restore deleted lessons
            foreach ($pendingLessons as $lesId => $info) {
                if ($info['action'] === 'delete' && $info['snapshot']) {
                    $activeLessons[] = $info['snapshot'];
                }
            }
            usort($activeLessons, function($a, $b) {
                return ($a['order_index'] ?? 0) - ($b['order_index'] ?? 0);
            });
            $allLessons = $activeLessons;
        }

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

    public function getLessonDetail(int $lessonId, string $userRole = 'student')
    {
        $lesson = $this->lessonRepo->findLessonById($lessonId);
        if (!$lesson) {
            throw new Exception("Không tìm thấy bài học này.");
        }

        // Fetch course status
        $db = \App\Core\Database::connect();
        $stmtStatus = $db->prepare("
            SELECT c.status, c.id as course_id 
            FROM courses c
            JOIN chapters ch ON c.id = ch.course_id
            WHERE ch.id = ?
        ");
        $stmtStatus->execute([$lesson['chapter_id']]);
        $courseInfo = $stmtStatus->fetch(\PDO::FETCH_ASSOC);

        if ($courseInfo && $courseInfo['status'] === 'pending_reapproval' && $userRole !== 'teacher' && $userRole !== 'admin') {
            // Find oldest update/delete log for this lesson
            $stmtLog = $db->prepare("
                SELECT * FROM course_change_logs 
                WHERE course_id = ? AND entity_type = 'lesson' AND entity_id = ? AND action_type = 'update'
                ORDER BY id ASC LIMIT 1
            ");
            $stmtLog->execute([$courseInfo['course_id'], $lessonId]);
            $log = $stmtLog->fetch(\PDO::FETCH_ASSOC);
            if ($log && $log['old_snapshot']) {
                $oldSnap = json_decode($log['old_snapshot'], true);
                foreach ($oldSnap as $k => $v) {
                    $lesson[$k] = $v;
                }
            }
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
