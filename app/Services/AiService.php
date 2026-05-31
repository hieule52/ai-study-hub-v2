<?php

namespace App\Services;

use App\Repositories\AiRepository;
use App\Core\Database;
use PDO;
use Exception;

/**
 * AiService - Hệ thống AI kép cho AI Study Hub LMS
 *
 * AI số 1: chatAssistant() — Gia sư AI tổng quát (Guest/Student/Teacher)
 *   • Trả lời kiến thức rộng, hỏi khóa học, lộ trình học
 *   • Cảnh báo khi trả lời từ kiến thức bên ngoài LMS
 *
 * AI số 2: chatTutor() — AI Tutor (chỉ Student, trong trang học bài)
 *   • Nghiêm ngặt 100% theo nội dung bài học
 *   • Từ chối mọi câu hỏi ngoài phạm vi bài
 */
class AiService
{
    private AiRepository $aiRepo;
    private PDO $db;
    private string $groqApiKey;

    // Security: Block patterns for system safety
    private array $blockedPatterns = [
        '/\b(DROP\s+TABLE|DELETE\s+FROM|TRUNCATE|ALTER\s+TABLE|INSERT\s+INTO)\b/i',
        '/\b(UNION\s+SELECT|OR\s+1\s*=\s*1|;\s*--)\b/i',
        '/<script[\s>]|javascript:|on\w+\s*=/i',
        '/\b(exec|system|shell_exec|passthru|eval)\s*\(/i',
        '/\bpassword\s*=|secret_key|api_key|jwt_secret/i',
    ];

    public function __construct()
    {
        $this->aiRepo = new AiRepository();
        $this->db = Database::connect();
        $this->groqApiKey = $_ENV['GROQ_API_KEY'] ?? '';
    }

    // ═══════════════════════════════════════════════════════════
    // AI SỐ 1: GIA SƯ AI (ASSISTANT) — Cho Guest/Student/Teacher
    // ═══════════════════════════════════════════════════════════

    /**
     * Chat với AI Assistant tổng quát.
     * - Trả lời kiến thức rộng, thông tin khóa học, lộ trình học.
     * - Kèm disclaimer khi trả lời từ kiến thức bên ngoài LMS.
     */
    public function chatAssistant(int $userId, string $message, string $userRole = 'guest', ?int $courseId = null): array
    {
        if (empty(trim($message))) {
            throw new Exception("Vui lòng nhập nội dung câu hỏi.");
        }

        $moderation = $this->moderateInput($message);
        if ($moderation) return ['ai_response' => $moderation, 'moderated' => true, 'from_external' => false, 'is_external' => false];

        // Step 1: Perform keyword searches in the database for relevant course descriptions, lesson contents, objectives, quiz explanations, and learning paths.
        $lmsContext = $this->searchLmsKnowledge($message);
        $fromExternal = ($lmsContext === null);

        // Build context
        $course = $courseId ? $this->getCourseContext($courseId) : null;

        // Conversation history (không gắn với lesson cụ thể)
        $conversationId = ($userId > 0) ? $this->aiRepo->getOrCreateAssistantConversation($userId) : null;
        $history = $conversationId ? $this->aiRepo->getHistory($conversationId, 8) : [];

        $systemPrompt = $this->buildAssistantPrompt($course, $userRole, $lmsContext);
        $messages = $this->buildMessages($systemPrompt, $history, $message);

        if (empty($this->groqApiKey)) throw new Exception("AI Service configuration missing.");

        $response = $this->callGroqApi($messages, 0.7, 1500);
        if (!$response) throw new Exception("AI không thể phản hồi lúc này.");

        if ($conversationId) {
            $this->aiRepo->saveMessage($conversationId, 'user', $message);
            $this->aiRepo->saveMessage($conversationId, 'assistant', $response);
        }

        return [
            'ai_response'    => $response,
            'is_external'    => $fromExternal,
            'from_external'  => $fromExternal,
            'disclaimer'     => $fromExternal
                ? '⚠️ Nội dung dưới đây được tạo từ nguồn kiến thức bên ngoài hệ thống AI Study Hub LMS. Vui lòng kiểm chứng lại thông tin trước khi áp dụng.'
                : null,
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // AI SỐ 2: AI TUTOR — Chỉ Student, trong trang học bài
    // ═══════════════════════════════════════════════════════════

    /**
     * Chat với AI Tutor nghiêm ngặt — chỉ dựa vào nội dung bài học.
     * Từ chối trả lời mọi câu hỏi ngoài phạm vi bài.
     */
    public function chatTutor(int $userId, string $message, int $lessonId, ?int $courseId = null): array
    {
        if (empty(trim($message))) {
            throw new Exception("Vui lòng nhập nội dung câu hỏi.");
        }

        $moderation = $this->moderateInput($message);
        if ($moderation) return ['ai_response' => $moderation, 'moderated' => true];

        // Kiểm tra học viên có được học bài này không (enrolled)
        $this->assertStudentAccessToLesson($userId, $lessonId);

        $lesson = $this->getDeepLessonContext($lessonId);
        if (!$lesson) throw new Exception("Không tìm thấy bài học.");

        $course = $courseId ? $this->getCourseContext($courseId) : null;

        $conversationId = $this->aiRepo->getOrCreateConversation($userId, $lessonId);
        $history = $this->aiRepo->getHistory($conversationId, 6);

        $systemPrompt = $this->buildTutorPrompt($lesson, $course);
        $messages = $this->buildMessages($systemPrompt, $history, $message);

        if (empty($this->groqApiKey)) throw new Exception("AI Service configuration missing.");

        $response = $this->callGroqApi($messages, 0.4, 1200);
        if (!$response) throw new Exception("AI không thể phản hồi lúc này.");

        $this->aiRepo->saveMessage($conversationId, 'user', $message);
        $this->aiRepo->saveMessage($conversationId, 'assistant', $response);

        return [
            'ai_response' => $response,
            'lesson_id'   => $lessonId,
            'mode'        => 'tutor',
        ];
    }

    /**
     * Legacy: backward compat cho code cũ
     */
    public function chat(int $userId, string $message, ?string $base64Image = null, ?int $lessonId = null, ?int $courseId = null): array
    {
        if ($lessonId) {
            return $this->chatTutor($userId, $message, $lessonId, $courseId);
        }
        return $this->chatAssistant($userId, $message, 'student', $courseId);
    }

    // ─── System Prompt Builders ──────────────────────────────────

    private function buildAssistantPrompt(?array $course, string $userRole, ?string $lmsContext = null): string
    {
        $roleLabel = match($userRole) {
            'teacher' => 'Giảng viên',
            'admin'   => 'Quản trị viên',
            default   => 'Học viên/Khách',
        };

        $p  = "BẠN LÀ: Gia sư AI (AI Assistant) chuyên nghiệp của hệ thống AI Study Hub LMS.\n";
        $p .= "ĐỐI TƯỢNG: {$roleLabel}\n";
        $p .= "TÍNH CÁCH: Thông thái, kiên nhẫn, truyền cảm hứng, khuyến khích tự học.\n";
        $p .= "NGÔN NGỮ: Tiếng Việt, khoa học, lịch sự, gần gũi.\n\n";

        $p .= "QUY TẮC:\n";
        $p .= "1. Trả lời mọi câu hỏi học thuật và kiến thức liên quan đến AI, công nghệ, khoa học.\n";
        $p .= "2. Khi hỏi về khóa học: Dùng danh sách khóa học thực tế bên dưới để giới thiệu chi tiết. LUÔN tạo link Markdown có thể click được dạng [Tên khóa học](/course/id).\n";
        $p .= "3. Khi hỏi về lộ trình học: Đề xuất lộ trình phù hợp với mục tiêu và trình độ của học viên.\n";
        $p .= "4. Khi phải dùng kiến thức bên ngoài LMS: Bắt đầu câu trả lời bằng [EXTERNAL_KNOWLEDGE].\n";
        $p .= "5. KHÔNG trực tiếp đưa đáp án quiz. Gợi ý bằng hints và dẫn dắt.\n";
        $p .= "6. Từ chối lịch sự nội dung không phù hợp với môi trường giáo dục.\n";
        $p .= "7. Khi giới thiệu khóa học, PHẢI dùng định dạng link Markdown: [Tên khóa học](/course/id) để người dùng có thể click vào xem chi tiết.\n";

        if ($userRole === 'teacher') {
            $p .= "7. Hỗ trợ giảng viên: Gợi ý cấu trúc bài giảng, câu hỏi quiz, tài liệu tham khảo.\n";
        }

        $p .= "\n";

        // Danh sách khóa học thực tế
        $courses = $this->getSystemCourses();
        if (!empty($courses)) {
            $p .= "--- DANH SÁCH KHÓA HỌC TRONG HỆ THỐNG AI STUDY HUB ---\n";
            foreach ($courses as $idx => $c) {
                $priceStr = ((float)$c['price'] <= 0) ? "Miễn phí" : number_format($c['price'], 0, ',', '.') . " VNĐ";
                $p .= ($idx + 1) . ". **[{$c['title']}](/course/{$c['id']})** — GV: {$c['teacher_name']} | Trình độ: {$c['level']} | Học phí: {$priceStr}\n";
            }
            $p .= "\n📚 Xem tất cả khóa học: [/courses](/courses)\n\n";
        }

        if ($course) {
            $p .= "KHÓA HỌC ĐANG HỎI: \"{$course['title']}\" (Trình độ: {$course['level']})\n";
            if (!empty($course['description'])) {
                $p .= "Mô tả: " . mb_substr(strip_tags($course['description']), 0, 500) . "\n";
            }
            $p .= "\n";
        }

        if ($lmsContext) {
            $p .= "--- TÀI LIỆU/BỐI CẢNH TỪ HỆ THỐNG LMS (Hãy sử dụng thông tin này để trả lời ưu tiên): ---\n";
            $p .= $lmsContext . "\n\n";
        }

        return $p;
    }

    private function buildTutorPrompt(array $lesson, ?array $course): string
    {
        $p  = "BẠN LÀ: AI Tutor nghiêm ngặt của hệ thống AI Study Hub LMS.\n";
        $p .= "CHẾ ĐỘ: STRICT — Chỉ trả lời dựa trên nội dung bài học bên dưới.\n\n";

        $p .= "QUY TẮC BẮT BUỘC:\n";
        $p .= "1. CHỈ trả lời dựa trên NGỮ CẢNH BÀI HỌC bên dưới.\n";
        $p .= "2. Nếu câu hỏi KHÔNG có trong bài học, trả lời CHÍNH XÁC:\n";
        $p .= "   \"Câu hỏi này nằm ngoài phạm vi bài học hiện tại. Vui lòng sử dụng Gia sư AI để được hỗ trợ kiến thức tổng quát.\"\n";
        $p .= "3. KHÔNG bịa đặt, suy luận ngoài phạm vi, bổ sung kiến thức bên ngoài.\n";
        $p .= "4. Câu hỏi Quiz: KHÔNG đưa đáp án trực tiếp. Dùng gợi ý và dẫn dắt.\n";
        $p .= "5. Ngôn ngữ: Tiếng Việt, học thuật, lịch sự.\n\n";

        if ($course) {
            $p .= "KHÓA HỌC: \"{$course['title']}\" (Trình độ: {$course['level']})\n";
        }

        $p .= "BÀI HỌC: \"{$lesson['title']}\"\n";
        $p .= "--- NGỮ CẢNH BÀI HỌC ---\n";

        if (!empty($lesson['teacher_notes'])) {
            $p .= "[🔴 GHI CHÚ GIẢNG VIÊN — ƯU TIÊN CAO]: {$lesson['teacher_notes']}\n";
        }

        $transcriptStatus = $lesson['transcript_status'] ?? 'empty';
        if (!empty($lesson['video_transcript'])) {
            $label = match($transcriptStatus) {
                'teacher_verified' => '✅ ĐÃ XÁC MINH',
                'auto_generated'   => '🤖 TỰ ĐỘNG',
                default            => '📝 CHƯA XÁC MINH',
            };
            $p .= "[Transcript Video ({$label})]: {$lesson['video_transcript']}\n";
        }

        if (!empty($lesson['ai_summary']))    $p .= "[Tóm tắt]: {$lesson['ai_summary']}\n";
        if (!empty($lesson['lesson_context'])) $p .= "[Bối cảnh]: {$lesson['lesson_context']}\n";

        if (!empty($lesson['content'])) {
            $p .= "[Nội dung]: " . mb_substr(strip_tags($lesson['content']), 0, 4000) . "\n";
        }

        // Câu hỏi quiz trong bài
        $quizCtx = $this->getQuizQuestionsContext($lesson['id']);
        if ($quizCtx) $p .= $quizCtx;

        $p .= "-------------------------------------------\n";
        return $p;
    }

    // ─── Private helpers ─────────────────────────────────────────

    /**
     * Phát hiện heuristic xem AI có dùng kiến thức bên ngoài không
     */
    private function detectExternalKnowledge(string $question, string $answer): bool
    {
        return str_contains($answer, '[EXTERNAL_KNOWLEDGE]');
    }

    private function assertStudentAccessToLesson(int $userId, int $lessonId): void
    {
        $stmt = $this->db->prepare("
            SELECT e.id
            FROM enrollments e
            JOIN chapters c  ON c.course_id  = e.course_id
            JOIN lessons l   ON l.chapter_id = c.id
            WHERE e.user_id  = :uid
              AND l.id       = :lid
              AND l.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        if (!$stmt->fetchColumn()) {
            throw new Exception("Bạn chưa đăng ký khóa học chứa bài học này.");
        }
    }

    private function getQuizQuestionsContext(int $lessonId): string
    {
        try {
            $stmt = $this->db->prepare("SELECT id, title FROM quizzes WHERE lesson_id = :lid AND deleted_at IS NULL LIMIT 1");
            $stmt->execute(['lid' => $lessonId]);
            $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$quiz) return "";

            $stmtQ = $this->db->prepare("SELECT id, question, explanation, hint, difficulty_level FROM questions WHERE quiz_id = :qid AND deleted_at IS NULL ORDER BY id ASC");
            $stmtQ->execute(['qid' => $quiz['id']]);
            $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);
            if (empty($questions)) return "";

            $ctx = "\n--- QUIZ: \"{$quiz['title']}\" ---\n";
            foreach ($questions as $i => $q) {
                $ctx .= "Câu " . ($i + 1) . ": \"{$q['question']}\" (Mức: {$q['difficulty_level']})\n";
                $stmtA = $this->db->prepare("SELECT answer_text, is_correct FROM answers WHERE question_id = :qid AND deleted_at IS NULL ORDER BY id ASC");
                $stmtA->execute(['qid' => $q['id']]);
                $answers = $stmtA->fetchAll(PDO::FETCH_ASSOC);
                foreach ($answers as $j => $a) {
                    $ctx .= "  " . chr(65 + $j) . ": \"{$a['answer_text']}\" [" . ($a['is_correct'] ? "ĐÚNG" : "SAI") . "]\n";
                }
                if (!empty($q['hint']))        $ctx .= "Gợi ý: {$q['hint']}\n";
                if (!empty($q['explanation'])) $ctx .= "Giải thích: {$q['explanation']}\n";
                $ctx .= "\n";
            }
            $ctx .= "---\n";
            return $ctx;
        } catch (\Exception $e) {
            return "";
        }
    }

    private function getSystemCourses(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT c.id, c.title, c.level, c.price, c.description, u.username as teacher_name
                FROM courses c
                LEFT JOIN users u ON c.teacher_id = u.id
                WHERE c.status = 'approved' AND c.deleted_at IS NULL
                ORDER BY c.id DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getDeepLessonContext(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, q.explanations as quiz_explanations, q.hints as quiz_hints
            FROM lessons l
            LEFT JOIN quizzes q ON l.id = q.lesson_id AND q.deleted_at IS NULL
            WHERE l.id = :id AND l.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getCourseContext(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT title, level, description FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function buildMessages(string $system, array $history, string $userMsg): array
    {
        $msgs = [['role' => 'system', 'content' => $system]];
        foreach ($history as $h) {
            $msgs[] = ['role' => $h['role'], 'content' => $h['content']];
        }
        $msgs[] = ['role' => 'user', 'content' => $userMsg];
        return $msgs;
    }

    private function moderateInput(string $msg): ?string
    {
        foreach ($this->blockedPatterns as $p) {
            if (preg_match($p, $msg)) return "⚠️ Yêu cầu của bạn chứa nội dung không phù hợp với môi trường giáo dục.";
        }
        return null;
    }

    private function callGroqApi(array $messages, float $temperature = 0.6, int $maxTokens = 1200): ?string
    {
        $data = [
            'model'       => 'llama-3.1-8b-instant',
            'messages'    => $messages,
            'temperature' => $temperature,
            'max_tokens'  => $maxTokens,
        ];

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->groqApiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT    => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? null;
    }

    /**
     * Tìm kiếm thông tin liên quan trong cơ sở dữ liệu LMS.
     */
    private function searchLmsKnowledge(string $message): ?string
    {
        $cleanedMsg = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $message);
        $words = array_filter(explode(' ', $cleanedMsg), function($w) {
            return mb_strlen(trim($w)) >= 3;
        });

        if (empty($words)) {
            $words = [trim($message)];
        }

        $words = array_slice($words, 0, 4);
        $contextParts = [];

        // Search courses
        $courseQueries = [];
        $params = [];
        foreach ($words as $i => $word) {
            $word = trim($word);
            if (empty($word)) continue;
            $courseQueries[] = "(title LIKE :c_title_$i OR description LIKE :c_desc_$i)";
            $params["c_title_$i"] = "%$word%";
            $params["c_desc_$i"] = "%$word%";
        }
        if (!empty($courseQueries)) {
            $sql = "SELECT title, description, level FROM courses WHERE (" . implode(" OR ", $courseQueries) . ") AND status = 'approved' AND deleted_at IS NULL LIMIT 2";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($courses as $c) {
                $contextParts[] = "[Khóa học: {$c['title']} ({$c['level']})]\nMô tả: " . strip_tags($c['description']);
            }
        }

        // Search lessons
        $lessonQueries = [];
        $lessonParams = [];
        foreach ($words as $i => $word) {
            $word = trim($word);
            if (empty($word)) continue;
            $lessonQueries[] = "(title LIKE :l_title_$i OR content LIKE :l_content_$i OR lesson_context LIKE :l_ctx_$i OR video_transcript LIKE :l_trans_$i)";
            $lessonParams["l_title_$i"] = "%$word%";
            $lessonParams["l_content_$i"] = "%$word%";
            $lessonParams["l_ctx_$i"] = "%$word%";
            $lessonParams["l_trans_$i"] = "%$word%";
        }
        if (!empty($lessonQueries)) {
            $sql = "SELECT title, content, lesson_context, video_transcript FROM lessons WHERE (" . implode(" OR ", $lessonQueries) . ") AND deleted_at IS NULL LIMIT 2";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($lessonParams);
            $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($lessons as $l) {
                $contentSnippet = mb_substr(strip_tags($l['content'] ?? ''), 0, 400);
                $transSnippet = mb_substr(strip_tags($l['video_transcript'] ?? ''), 0, 400);
                $contextParts[] = "[Bài học: {$l['title']}]\nNội dung: {$contentSnippet}\nVideo: {$transSnippet}";
            }
        }

        // Search learning paths
        $lpQueries = [];
        $lpParams = [];
        foreach ($words as $i => $word) {
            $word = trim($word);
            if (empty($word)) continue;
            $lpQueries[] = "(title LIKE :lp_title_$i OR description LIKE :lp_desc_$i)";
            $lpParams["lp_title_$i"] = "%$word%";
            $lpParams["lp_desc_$i"] = "%$word%";
        }
        if (!empty($lpQueries)) {
            $sql = "SELECT title, description FROM learning_paths WHERE (" . implode(" OR ", $lpQueries) . ") LIMIT 2";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($lpParams);
            $lps = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($lps as $lp) {
                $contextParts[] = "[Lộ trình học: {$lp['title']}]\nMô tả: " . strip_tags($lp['description']);
            }
        }

        return !empty($contextParts) ? implode("\n\n", $contextParts) : null;
    }
}
