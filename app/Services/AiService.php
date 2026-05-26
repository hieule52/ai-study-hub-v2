<?php

namespace App\Services;

use App\Repositories\AiRepository;
use App\Core\Database;
use PDO;
use Exception;

/**
 * AiService - Advanced AI Tutor for AI Study Hub LMS
 * Refined for Production-Ready Cinematic Experience
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

    /**
     * Advanced Contextual Chat with Lesson Awareness
     */
    public function chat(int $userId, string $message, ?string $base64Image = null, ?int $lessonId = null, ?int $courseId = null): array
    {
        if (empty(trim($message)) && empty($base64Image)) {
            throw new Exception("Vui lòng nhập nội dung câu hỏi.");
        }

        // 1. Content Moderation
        $moderation = $this->moderateInput($message);
        if ($moderation) return ['ai_response' => $moderation, 'moderated' => true];

        // 2. Fetch Deep Context
        $lesson = $lessonId ? $this->getDeepLessonContext($lessonId) : null;
        $course = $courseId ? $this->getCourseContext($courseId) : null;

        // 3. Conversation Management
        $conversationId = $lessonId ? $this->aiRepo->getOrCreateConversation($userId, $lessonId) : null;
        $history = $conversationId ? $this->aiRepo->getHistory($conversationId, 6) : [];

        // 4. Build Professional Persona Prompt
        $systemPrompt = $this->buildAdvancedSystemPrompt($lesson, $course);
        $messages = $this->buildMessages($systemPrompt, $history, $message);

        // 5. Execute AI Request (Groq LLaMA 3.1)
        if (empty($this->groqApiKey)) throw new Exception("AI Service configuration missing.");
        
        $response = $this->callGroqApi($messages);
        if (!$response) throw new Exception("AI không thể phản hồi lúc này.");

        // 6. Persist Message
        if ($conversationId) {
            $this->aiRepo->saveMessage($conversationId, 'user', $message);
            $this->aiRepo->saveMessage($conversationId, 'assistant', $response);
        }

        return [
            'original_message' => $message,
            'ai_response' => $response,
            'lesson_id' => $lessonId
        ];
    }

    private function buildAdvancedSystemPrompt(?array $lesson, ?array $course): string
    {
        // Xác định chế độ hoạt động
        $strictMode = !empty($lesson['strict_ai_mode']) && (int)$lesson['strict_ai_mode'] === 1;

        // ── STRICT MODE PROMPT ──
        if ($strictMode && $lesson) {
            $prompt = "BẠN LÀ: Trợ giảng AI nghiêm ngặt (Strict Mode) của hệ thống AI Study Hub LMS.\n";
            $prompt .= "CHẾ ĐỘ: NGHIÊM NGẶT — Chỉ được phép trả lời dựa trên nội dung bài học bên dưới.\n\n";
            $prompt .= "QUY TẮC BẮT BUỘC (KHÔNG ĐƯỢC VI PHẠM):\n";
            $prompt .= "1. TUYỆT ĐỐI CHỈ được trả lời dựa trên các thông tin có trong phần NGỮ CẢNH BÀI HỌC bên dưới.\n";
            $prompt .= "2. Nếu câu hỏi của học viên KHÔNG liên quan hoặc KHÔNG CÓ trong nội dung bài học, trả lời CHÍNH XÁC:\n";
            $prompt .= "   \"⚠️ Thông tin này không có trong bài học hiện tại. Vui lòng tham khảo tài liệu bổ sung hoặc hỏi giảng viên.\"\n";
            $prompt .= "3. KHÔNG BAO GIỜ bịa đặt, suy luận ngoài phạm vi, hoặc bổ sung kiến thức từ bên ngoài.\n";
            $prompt .= "4. Nếu là câu hỏi Quiz/Bài tập: KHÔNG trực tiếp đưa ra đáp án. Gợi ý bằng logic và dẫn dắt.\n";
            $prompt .= "5. Ngôn ngữ: Tiếng Việt, học thuật, lịch sự, có tính hệ thống.\n\n";
        } else {
            // ── NORMAL MODE PROMPT ──
            $prompt = "BẠN LÀ: Một giảng viên đại học cao cấp, người cố vấn học thuật chuyên nghiệp (Expert Educational Mentor) từ AI Study Hub.\n";
            $prompt .= "TÍNH CÁCH: Kiên nhẫn, thông thái, truyền cảm hứng và luôn khuyến khích học viên tự suy nghĩ.\n";
            $prompt .= "NGÔN NGỮ: Tiếng Việt (hoặc ngôn ngữ của học viên), khoa học, lịch sự nhưng gần gũi.\n\n";

            $prompt .= "CHỈ THỊ CỐT LÕI:\n";
            $prompt .= "1. Giải thích kiến thức theo từng bước (step-by-step) khoa học, liên hệ thực tế.\n";
            $prompt .= "2. Nếu học viên hỏi về câu hỏi trong Quiz/Bài tập:\n";
            $prompt .= "   - TUYỆT ĐỐI KHÔNG trực tiếp đưa ra đáp án đúng (ví dụ: Không nói 'Đáp án đúng là A').\n";
            $prompt .= "   - Hãy sử dụng gợi ý (hints) và giải thích logic để dẫn dắt học viên tự tìm ra đáp án sai của mình và sửa lại cho đúng.\n";
            $prompt .= "   - Định hướng học viên bằng câu hỏi gợi mở liên quan đến khái niệm bài học.\n";
            $prompt .= "3. Nếu là code: Giải thích logic, giải thuật, và các lỗi bug thường gặp. Dùng Markdown code blocks.\n";
            $prompt .= "4. Từ chối lịch sự mọi yêu cầu ngoài học thuật hoặc không phù hợp.\n";
            $prompt .= "5. HIỂU BIẾT VỀ HỆ THỐNG & KHÓA HỌC:\n";
            $prompt .= "   - Bạn là trợ lý chính thức của hệ thống giáo dục trực tuyến **AI Study Hub LMS**.\n";
            $prompt .= "   - Khi học viên hỏi về các khóa học, giới thiệu khóa học, khuyên học khóa học nào, hoặc hỏi hệ thống có những khóa học gì:\n";
            $prompt .= "     + Bạn CẦN sử dụng danh sách các khóa học thực tế đang hoạt động trong hệ thống dưới đây để giới thiệu chi tiết (tên khóa học, giảng viên, trình độ, học phí, mô tả khái quát).\n";
            $prompt .= "     + Hãy chèn đường dẫn xem chi tiết dạng Markdown liên kết (ví dụ: [Tên Khóa Học](/course/X)) để học viên có thể click vào học hoặc đăng ký ngay.\n";
            $prompt .= "     + Khuyến khích học viên đăng ký hoặc tìm hiểu thêm tại trang **Khóa học** (đường dẫn: `/courses`).\n";
            $prompt .= "     + Tuyệt đối không bịa đặt hoặc tự vẽ ra các khóa học không có thực trong danh sách này.\n\n";
        }

        // ── Gắn danh sách khóa học thực tế (chỉ ở Normal Mode) ──
        if (!$strictMode) {
            $systemCourses = $this->getSystemCourses();
            if (!empty($systemCourses)) {
                $prompt .= "--- DANH SÁCH KHÓA HỌC THỰC TẾ TRONG HỆ THỐNG AI STUDY HUB ---\n";
                foreach ($systemCourses as $idx => $c) {
                    $num = $idx + 1;
                    $priceStr = ((float)$c['price'] <= 0) ? "Miễn phí" : number_format($c['price'], 0, ',', '.') . " VNĐ";
                    $prompt .= "Khóa học {$num}:\n";
                    $prompt .= "  - ID Khóa học: {$c['id']}\n";
                    $prompt .= "  - Tên khóa học: \"{$c['title']}\"\n";
                    $prompt .= "  - Giảng viên: {$c['teacher_name']}\n";
                    $prompt .= "  - Trình độ: {$c['level']}\n";
                    $prompt .= "  - Học phí: {$priceStr}\n";
                    $prompt .= "  - Mô tả: " . strip_tags($c['description']) . "\n";
                    $prompt .= "  - Đường dẫn xem chi tiết: /course/{$c['id']}\n\n";
                }
                $prompt .= "-------------------------------------------------------------\n\n";
            }
        }

        if ($course) {
            $prompt .= "KHÓA HỌC HIỆN TẠI: \"{$course['title']}\" (Trình độ: {$course['level']}).\n";
        }

        // ── NGỮ CẢNH BÀI HỌC — THEO THỨ TỰ ƯU TIÊN ──
        if ($lesson) {
            $prompt .= "BÀI HỌC HIỆN TẠI: \"{$lesson['title']}\"\n";
            $prompt .= "--- NGỮ CẢNH BÀI HỌC (DÙNG ĐỂ GIẢNG DẠY & TRẢ LỜI) ---\n";

            // Ưu tiên 1: Ghi chú của giảng viên (Teacher Notes) — cao nhất
            if (!empty($lesson['teacher_notes'])) {
                $prompt .= "[🔴 GHI CHÚ CỦA GIẢNG VIÊN — ƯU TIÊN CAO NHẤT]: {$lesson['teacher_notes']}\n";
                $prompt .= "→ Lưu ý: Nội dung ghi chú của giảng viên phải được ưu tiên tuyệt đối trong mọi câu trả lời.\n";
            }

            // Ưu tiên 2: Bản dịch đã xác minh bởi giảng viên
            $transcriptStatus = $lesson['transcript_status'] ?? 'empty';
            if (!empty($lesson['video_transcript'])) {
                $statusLabel = match($transcriptStatus) {
                    'teacher_verified' => '✅ ĐÃ XÁC MINH BỞI GIẢNG VIÊN',
                    'auto_generated' => '🤖 TẠO TỰ ĐỘNG BỞI AI',
                    default => '📝 CHƯA XÁC MINH'
                };
                $prompt .= "[Bản dịch Video ({$statusLabel})]: {$lesson['video_transcript']}\n";
            }

            // Ưu tiên 3: Tóm tắt AI
            if (!empty($lesson['ai_summary'])) $prompt .= "[Tóm tắt bài]: {$lesson['ai_summary']}\n";

            // Ưu tiên 4: Bối cảnh bổ sung
            if (!empty($lesson['lesson_context'])) $prompt .= "[Bối cảnh bổ sung]: {$lesson['lesson_context']}\n";
            
            // Ưu tiên 5: Nội dung chi tiết
            if (!empty($lesson['content'])) {
                $clean = mb_substr(strip_tags($lesson['content']), 0, 4000);
                $prompt .= "[Nội dung chi tiết]: $clean\n";
            }

            // Ưu tiên 6: Giải thích bài tập
            if (!empty($lesson['quiz_explanations'])) {
                $prompt .= "[Giải thích bài tập chung]: {$lesson['quiz_explanations']}\n";
            }

            // Gắn ngữ cảnh câu hỏi trắc nghiệm chi tiết của bài học
            $quizQuestionsContext = $this->getQuizQuestionsContext($lesson['id']);
            if (!empty($quizQuestionsContext)) {
                $prompt .= $quizQuestionsContext;
            }
            
            $prompt .= "-------------------------------------------\n";
        }

        return $prompt;
    }

    /**
     * Lấy ngữ cảnh chi tiết toàn bộ câu hỏi và đáp án của bài học để AI Tutor trợ giúp học viên
     */
    private function getQuizQuestionsContext(int $lessonId): string
    {
        try {
            $stmt = $this->db->prepare("SELECT id, title FROM quizzes WHERE lesson_id = :lid AND deleted_at IS NULL LIMIT 1");
            $stmt->execute(['lid' => $lessonId]);
            $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$quiz) return "";

            $stmtQuestions = $this->db->prepare("SELECT id, question, explanation, hint, difficulty_level FROM questions WHERE quiz_id = :qid AND deleted_at IS NULL ORDER BY id ASC");
            $stmtQuestions->execute(['qid' => $quiz['id']]);
            $questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

            if (empty($questions)) return "";

            $contextStr = "\n--- CÂU HỎI TRONG BÀI KIỂM TRA (QUIZ: \"{$quiz['title']}\") ---\n";
            $i = 1;
            foreach ($questions as $q) {
                $contextStr .= "Câu hỏi {$i}: \"{$q['question']}\"\n";
                $contextStr .= "- Mức độ: {$q['difficulty_level']}\n";
                
                $stmtAnswers = $this->db->prepare("SELECT answer_text, is_correct FROM answers WHERE question_id = :qid AND deleted_at IS NULL ORDER BY id ASC");
                $stmtAnswers->execute(['qid' => $q['id']]);
                $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($answers)) {
                    $contextStr .= "- Các đáp án lựa chọn:\n";
                    $j = 65; // ASCII for 'A'
                    foreach ($answers as $a) {
                        $char = chr($j++);
                        $correct = $a['is_correct'] ? "ĐÚNG" : "SAI";
                        $contextStr .= "  + {$char}: \"{$a['answer_text']}\" (Đây là đáp án {$correct})\n";
                    }
                }
                if (!empty($q['hint'])) {
                    $contextStr .= "- Gợi ý giải: \"{$q['hint']}\"\n";
                }
                if (!empty($q['explanation'])) {
                    $contextStr .= "- Giải thích chi tiết: \"{$q['explanation']}\"\n";
                }
                $contextStr .= "\n";
                $i++;
            }
            $contextStr .= "--------------------------------------------------------\n";
            return $contextStr;
        } catch (\Exception $e) {
            return ""; // Tránh crash luồng chat nếu lỗi DB
        }
    }

    /**
     * Lấy toàn bộ danh sách khóa học đang hoạt động trong hệ thống
     */
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    private function callGroqApi(array $messages): ?string
    {
        $data = [
            'model' => 'llama-3.1-8b-instant',
            'messages' => $messages,
            'temperature' => 0.6,
            'max_tokens' => 1200
        ];

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->groqApiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? null;
    }
}
