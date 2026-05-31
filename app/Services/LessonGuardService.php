<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class LessonGuardService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Kiểm tra học viên có được phép truy cập bài học (phải học tuần tự) hay không.
     * Trả về true nếu được phép truy cập, ngược lại trả về false.
     */
    public function canAccessLesson(int $userId, int $lessonId): bool
    {
        // 1. Tìm course_id của bài học này
        $stmt = $this->db->prepare("
            SELECT c.course_id 
            FROM chapters c 
            JOIN lessons l ON l.chapter_id = c.id 
            WHERE l.id = :lid AND l.deleted_at IS NULL AND c.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['lid' => $lessonId]);
        $courseId = $stmt->fetchColumn();
        if (!$courseId) {
            return false; // Không tìm thấy bài học hoặc chương học tương ứng
        }

        // 2. Lấy danh sách tất cả các bài học trong khóa học sắp xếp theo đúng thứ tự học tập tuần tự
        $stmt = $this->db->prepare("
            SELECT l.id
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE c.course_id = :cid 
              AND l.deleted_at IS NULL 
              AND c.deleted_at IS NULL
            ORDER BY c.order_index ASC, l.order_index ASC, l.id ASC
        ");
        $stmt->execute(['cid' => $courseId]);
        $orderedLessonIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($orderedLessonIds)) {
            return false;
        }

        // 3. Tìm vị trí của bài học hiện tại trong danh sách tuần tự
        $index = array_search($lessonId, $orderedLessonIds);
        if ($index === false) {
            return false; // Bài học không nằm trong khóa học này
        }

        // Nếu là bài học đầu tiên trong khóa học, mặc định luôn được phép mở khóa
        if ($index === 0) {
            return true;
        }

        // 4. Tìm bài học ngay phía trước bài hiện tại
        $prevLessonId = (int)$orderedLessonIds[$index - 1];

        // 5. Kiểm tra xem bài học trước đó đã hoàn thành hay chưa
        $stmtProg = $this->db->prepare("
            SELECT is_completed 
            FROM lesson_progress 
            WHERE user_id = :uid AND lesson_id = :lid
            LIMIT 1
        ");
        $stmtProg->execute(['uid' => $userId, 'lid' => $prevLessonId]);
        $isCompleted = $stmtProg->fetchColumn();

        return (int)$isCompleted === 1;
    }

    /**
     * Lấy bài học cao nhất mà học viên đang được phép học hiện tại (bài học chưa hoàn thành đầu tiên).
     */
    public function getCurrentAllowedLesson(int $userId, int $courseId): int
    {
        $stmt = $this->db->prepare("
            SELECT l.id
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE c.course_id = :cid 
              AND l.deleted_at IS NULL 
              AND c.deleted_at IS NULL
            ORDER BY c.order_index ASC, l.order_index ASC, l.id ASC
        ");
        $stmt->execute(['cid' => $courseId]);
        $orderedLessonIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($orderedLessonIds)) {
            return 0;
        }

        foreach ($orderedLessonIds as $lid) {
            $lid = (int)$lid;
            $stmtProg = $this->db->prepare("
                SELECT is_completed 
                FROM lesson_progress 
                WHERE user_id = :uid AND lesson_id = :lid
                LIMIT 1
            ");
            $stmtProg->execute(['uid' => $userId, 'lid' => $lid]);
            $isCompleted = $stmtProg->fetchColumn();

            if ((int)$isCompleted !== 1) {
                return $lid; // Trả về bài học đầu tiên chưa học xong
            }
        }

        // Nếu tất cả bài học đã hoàn thành xong, trả về bài học cuối cùng
        return (int)end($orderedLessonIds);
    }
}
