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
    public function chatAssistant(int $userId, string $message, string $userRole = 'guest', ?int $courseId = null, string $lang = 'vi'): array
    {
        if (empty(trim($message))) {
            throw new Exception("Vui lòng nhập nội dung câu hỏi.");
        }

        $moderation = $this->moderateInput($message);
        if ($moderation) return ['ai_response' => $moderation, 'moderated' => true, 'from_external' => false, 'is_external' => false];

        // Step 1: Perform keyword searches in the database for relevant course descriptions, lesson contents, objectives, quiz explanations, and learning paths.
        $lmsContext = $this->searchLmsKnowledge($message);

        // Build context
        $course = $courseId ? $this->getCourseContext($courseId) : null;

        // Conversation history (không gắn với lesson cụ thể)
        $conversationId = ($userId > 0) ? $this->aiRepo->getOrCreateAssistantConversation($userId) : null;
        $history = $conversationId ? $this->aiRepo->getHistory($conversationId, 8) : [];

        $systemPrompt = $this->buildAssistantPrompt($course, $userRole, $lmsContext, $lang);
        $messages = $this->buildMessages($systemPrompt, $history, $message);

        if (empty($this->groqApiKey)) throw new Exception("AI Service configuration missing.");

        $response = $this->callGroqApi($messages, 0.7, 1500);
        if (!$response) throw new Exception("AI không thể phản hồi lúc này.");

        $hasExternalMarker = false;
        if (preg_match('/\[?EXTERNAL_KNOWLEDGE\]?:?/i', $response)) {
            $hasExternalMarker = true;
            $response = preg_replace('/\[?EXTERNAL_KNOWLEDGE\]?:?/i', '', $response);
            $response = trim($response);
        }

        $cleanedResponse = $this->validateAndCleanCourseLinks($response);
        $linkStripped = ($cleanedResponse !== $response);
        $response = $cleanedResponse;

        if ($conversationId) {
            $this->aiRepo->saveMessage($conversationId, 'user', $message);
            $this->aiRepo->saveMessage($conversationId, 'assistant', $response);
        }

        $fromExternal = ($lmsContext === null) || $hasExternalMarker || $linkStripped;

        return [
            'ai_response'    => $response,
            'is_external'    => $fromExternal,
            'from_external'  => $fromExternal,
            'disclaimer'     => $fromExternal
                ? ($lang === 'en'
                    ? '⚠️ This content is generated from external sources outside the AI Study Hub LMS. Please verify the information.'
                    : '⚠️ Nội dung dưới đây được tạo từ nguồn kiến thức bên ngoài hệ thống AI Study Hub LMS. Vui lòng kiểm chứng lại thông tin trước khi áp dụng.')
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
    public function chatTutor(int $userId, string $message, int $lessonId, ?int $courseId = null, string $lang = 'vi'): array
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

        // Không lưu lịch sử chat AI Tutor vào DB để tiết kiệm dung lượng
        // Mỗi request là stateless, chỉ dựa vào nội dung bài học
        $systemPrompt = $this->buildTutorPrompt($lesson, $course, $lang);
        $messages = $this->buildMessages($systemPrompt, [], $message);

        if (empty($this->groqApiKey)) throw new Exception("AI Service configuration missing.");

        $response = $this->callGroqApi($messages, 0.4, 1200);
        if (!$response) throw new Exception("AI không thể phản hồi lúc này.");

        return [
            'ai_response' => $response,
            'lesson_id'   => $lessonId,
            'mode'        => 'tutor',
        ];
    }

    /**
     * Legacy: backward compat cho code cũ
     */
    public function chat(int $userId, string $message, ?string $base64Image = null, ?int $lessonId = null, ?int $courseId = null, string $lang = 'vi'): array
    {
        if ($lessonId) {
            return $this->chatTutor($userId, $message, $lessonId, $courseId, $lang);
        }
        return $this->chatAssistant($userId, $message, 'student', $courseId, $lang);
    }
    // ─── System Prompt Builders ──────────────────────────────────────────────

    private function buildAssistantPrompt(?array $course, string $userRole, ?string $lmsContext = null, string $lang = 'vi'): string
    {
        $roleLabel = match($userRole) {
            'teacher' => 'Giảng viên',
            'admin'   => 'Quản trị viên',
            default   => 'Học viên/Khách',
        };

        $p  = "BẠN LÀ: Gia sư AI (AI Assistant) chuyên nghiệp của hệ thống AI Study Hub LMS.\n";
        $p .= "ĐỐI TƯỢNG: {$roleLabel}\n";
        $p .= "TÍNH CÁCH: Thông thái, kiên nhẫn, truyền cảm hứng, khuyến khích tự học.\n";
        $p .= "NGÔN NGỮ: " . ($lang === 'en' ? "Tiếng Anh (English). Hãy trả lời hoàn toàn bằng Tiếng Anh." : "Tiếng Việt (Vietnamese).") . "\n\n";

        $p .= "QUY TẮC:\n";
        $p .= "1. Trả lời chi tiết, đầy đủ và trực tiếp mọi câu hỏi học thuật, kiến thức liên quan đến AI, lập trình/công nghệ (viết code, debug, giải thích), toán học, khoa học. TUYỆT ĐỐI KHÔNG được từ chối viết code hay giải toán khi người dùng yêu cầu.\n";
        $p .= "2. Khi người dùng chỉ hỏi kiến thức chung, nhờ viết code, giải toán, giải thích khái niệm (KHÔNG chủ động hỏi tìm khóa học của hệ thống): TUYỆT ĐỐI KHÔNG ĐƯỢC phép giới thiệu, nhắc tên, gợi ý hoặc chèn link đến bất kỳ khóa học nào trong hệ thống. Hãy trả lời câu hỏi trực tiếp và toàn bộ câu trả lời phải bắt đầu bằng [EXTERNAL_KNOWLEDGE].\n";
        $p .= "3. Chỉ được phép giới thiệu khóa học khi người dùng chủ động hỏi tìm khóa học hoặc lộ trình học của hệ thống. Khi đó:\n";
        $p .= "   - Chỉ gợi ý khóa học có chủ đề thực sự trùng khớp hoặc liên quan trực tiếp đến yêu cầu của người dùng. TUYỆT ĐỐI KHÔNG được cố tình gượng ép, suy diễn hoặc bịa đặt sự liên quan để giới thiệu một khóa học không đúng chủ đề (ví dụ: người dùng hỏi học lập trình nhưng hệ thống chỉ có khóa AI và Kinh doanh Online thì KHÔNG được giới thiệu khóa AI hay Kinh doanh dưới danh nghĩa dạy lập trình, mà phải báo hệ thống chưa có khóa học về chủ đề này).\n";
        $p .= "   - Phải dùng danh sách khóa học thực tế bên dưới. Chỉ được giới thiệu các khóa học đúng như trong danh sách đó. TUYỆT ĐỐI phải dùng link Markdown: [Tên khóa học](/course/id) và dùng CHÍNH XÁC tên khóa học từ danh sách.\n";
        $p .= "4. Khi câu trả lời chứa kiến thức ngoài hệ thống LMS (như viết code, giải toán, giải thích học thuật): Bắt đầu bằng [EXTERNAL_KNOWLEDGE]. Hãy viết câu trả lời đầy đủ, chi tiết, hữu ích nhất có thể.\n";
        $p .= "5. KHÔNG trực tiếp đưa đáp án quiz. Gợi ý bằng hints và dẫn dắt.\n";
        $p .= "6. Từ chối lịch sự nội dung không phù hợp với môi trường giáo dục.\n";
        $p .= "7. TUYỆT ĐỐI KHÔNG được mô tả, liệt kê, hay nhắc đến bất kỳ khóa học nào KHÔNG có trong danh sách bên dưới — dưới bất kỳ hình thức nào. Việc bịa đặt tên khóa học, tên giảng viên, giá, hay trình độ cho khóa học không tồn tại là NGHIÊM CẤM.\n";
        $p .= "7a. TUYỆT ĐỐI KHÔNG tự tạo hay bịa đặt bất kỳ liên kết (link) nào đến bài học (ví dụ: /lesson/1, /lesson/2...), chương học, hay tài liệu khác. Chỉ được dùng liên kết khóa học /course/id đúng như trong danh sách.\n";
        $p .= "8. TUYỆT ĐỐI KHÔNG đổi tên, dịch, hay paraphrase tên khóa học khi tạo link Markdown. Dùng CHÍNH XÁC tên trong danh sách.\n";
        $p .= "9. Cách xử lý khi không có khóa học phù hợp:\n";
        $p .= "   - Nếu người dùng hỏi tìm khóa học hoặc lộ trình học mà hệ thống không có khóa học phù hợp: Hãy nói rõ 'Hiện tại hệ thống chưa có khóa học về chủ đề này' và cung cấp kiến thức chung với tiền tố [EXTERNAL_KNOWLEDGE].\n";
        $p .= "   - Nếu người dùng chỉ hỏi kiến thức chung, nhờ viết code, giải toán, giải thích khái niệm (không hỏi tìm khóa học): Trả lời trực tiếp và đầy đủ câu hỏi của họ với tiền tố [EXTERNAL_KNOWLEDGE] (không đưa câu từ chối hay câu 'chưa có khóa học về chủ đề này').\n";

        if ($userRole === 'teacher') {
            $p .= "10. Hỗ trợ giảng viên: Gợi ý cấu trúc bài giảng, câu hỏi quiz, tài liệu tham khảo.\n";
        }

        $p .= "\n";

        // Danh sách khóa học thực tế
        $courses = $this->getSystemCourses();
        $courseCount = count($courses);
        if (!empty($courses)) {
            $p .= "--- DANH SÁCH ĐẦY ĐỦ VÀ DUY NHẤT KHÓA HỌC TRONG HỆ THỐNG (tổng cộng {$courseCount} khóa học) ---\n";
            $p .= "QUAN TRỌNG: Đây là TẤT CẢ các khóa học hiện có. Không có khóa học nào khác ngoài danh sách này.\n";
            foreach ($courses as $idx => $c) {
                $priceStr = ((float)$c['price'] <= 0) ? "Miễn phí" : number_format($c['price'], 0, ',', '.') . " VNĐ";
                $categoryStr = !empty($c['category_name']) ? $c['category_name'] : 'Khác';
                $p .= ($idx + 1) . ". **[{$c['title']}](/course/{$c['id']})** — Danh mục: {$categoryStr} | GV: {$c['teacher_name']} | Trình độ: {$c['level']} | Học phí: {$priceStr}\n";
            }
            $p .= "\n📚 Xem tất cả khóa học: [/courses](/courses)\n\n";
        } else {
            $p .= "--- HỆ THỐNG HIỆN CHƯA CÓ KHÓA HỌC NÀO ---\n";
            $p .= "Hệ thống chưa có khóa học nào. KHÔNG được bịa đặt bất kỳ khóa học nào. Hãy cung cấp kiến thức chung với [EXTERNAL_KNOWLEDGE].\n\n";
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

    private function buildTutorPrompt(array $lesson, ?array $course, string $lang = 'vi'): string
    {
        $courseTitle = $course ? $course['title'] : 'Không rõ';
        $p  = "BẠN LÀ: AI Tutor chuyên nghiệp và tận tâm của hệ thống AI Study Hub LMS cho bài học \"{$lesson['title']}\" (Khóa học: \"{$courseTitle}\").\n";
        $p .= "CHẾ ĐỘ: LESSON_FOCUS — Tập trung hoàn toàn vào nội dung và các chủ đề liên quan của bài học này.\n\n";

        $p .= "QUY TẮC BẮT BUỘC:\n";
        $p .= "1. Tập trung trả lời xoay quanh bài học \"{$lesson['title']}\". Bạn được phép giải thích chi tiết, cung cấp ví dụ thực tế hoặc viết mã nguồn/giải toán để làm rõ các khái niệm, kiến thức và transcript được nhắc đến trong bài học này.\n";
        $p .= "2. Nếu câu hỏi của người dùng HOÀN TOÀN không liên quan đến bài học hoặc bất kỳ chủ đề/khái niệm nào trong bài học này, bạn phải từ chối và trả lời CHÍNH XÁC câu sau:\n";
        $p .= "   \"" . ($lang === 'en' 
               ? "This question is out of scope of the current lesson. Please use the AI Assistant for general knowledge support."
               : "Câu hỏi này nằm ngoài phạm vi bài học hiện tại. Vui lòng sử dụng Gia sư AI để được hỗ trợ kiến thức tổng quát.") . "\"\n";
        $p .= "3. Câu hỏi Quiz: KHÔNG đưa đáp án trực tiếp. Dùng gợi ý và dẫn dắt để học viên tự tìm câu trả lời.\n";
        $p .= "4. Ngôn ngữ: " . ($lang === 'en' ? "Tiếng Anh (English). Hãy trả lời hoàn toàn bằng Tiếng Anh." : "Tiếng Việt (Vietnamese).") . "\n\n";

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
            JOIN chapters ch ON ch.course_id  = e.course_id
            JOIN lessons l   ON l.chapter_id = ch.id
            JOIN courses co  ON ch.course_id  = co.id
            WHERE e.user_id  = :uid
              AND l.id       = :lid
              AND l.deleted_at IS NULL
              AND ch.deleted_at IS NULL
              AND co.status = 'approved'
              AND co.deleted_at IS NULL
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
                SELECT c.id, c.title, c.level, c.price, c.description, u.username as teacher_name,
                       cc.name as category_name
                FROM courses c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN course_categories cc ON c.category_id = cc.id
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
            JOIN chapters ch ON l.chapter_id = ch.id
            JOIN courses c ON ch.course_id = c.id
            LEFT JOIN quizzes q ON l.id = q.lesson_id AND q.deleted_at IS NULL
            WHERE l.id = :id 
              AND l.deleted_at IS NULL 
              AND ch.deleted_at IS NULL 
              AND c.status = 'approved' 
              AND c.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getCourseContext(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT title, level, description FROM courses WHERE id = ? AND status = 'approved' AND deleted_at IS NULL");
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
        $stopWords = ['học', 'tôi', 'muốn', 'cho', 'của', 'các', 'những', 'một', 'được', 'này', 'làm', 'trên', 'trong', 'dưới', 'biết', 'thấy'];
        $words = array_filter(explode(' ', $cleanedMsg), function($w) use ($stopWords) {
            $w = trim(mb_strtolower($w));
            return mb_strlen($w) >= 3 && !in_array($w, $stopWords);
        });

        if (empty($words)) {
            $fallbackWords = array_filter(explode(' ', $cleanedMsg), function($w) {
                return mb_strlen(trim($w)) >= 3;
            });
            $words = !empty($fallbackWords) ? $fallbackWords : [trim($message)];
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
            $lessonQueries[] = "(l.title LIKE :l_title_$i OR l.content LIKE :l_content_$i OR l.lesson_context LIKE :l_ctx_$i OR l.video_transcript LIKE :l_trans_$i)";
            $lessonParams["l_title_$i"] = "%$word%";
            $lessonParams["l_content_$i"] = "%$word%";
            $lessonParams["l_ctx_$i"] = "%$word%";
            $lessonParams["l_trans_$i"] = "%$word%";
        }
        if (!empty($lessonQueries)) {
            $sql = "SELECT l.title, l.content, l.lesson_context, l.video_transcript 
                    FROM lessons l
                    JOIN chapters ch ON l.chapter_id = ch.id
                    JOIN courses c ON ch.course_id = c.id
                    WHERE (" . implode(" OR ", $lessonQueries) . ") 
                      AND l.deleted_at IS NULL 
                      AND ch.deleted_at IS NULL 
                      AND c.status = 'approved' 
                      AND c.deleted_at IS NULL 
                    LIMIT 2";
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

    /**
     * Kiểm tra các link khóa học /course/id trong câu trả lời của AI.
     * Nếu khóa học không tồn tại, không được phê duyệt hoặc đã bị xóa, chuyển link thành văn bản in đậm.
     */
    private function validateAndCleanCourseLinks(string $response): string
    {
        $lines = explode("\n", $response);
        $cleanedLines = [];

        foreach ($lines as $line) {
            $hasInvalidLink = false;

            // Find all markdown links [Text](URL) in this line
            $line = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function($matches) use (&$hasInvalidLink) {
                $text = $matches[1];
                $url = trim($matches[2]);

                // Case 1: Specific course link /course/ID
                if (preg_match('/^\/course\/(\d+)$/', $url, $subMatches)) {
                    $courseId = (int)$subMatches[1];
                    $stmt = $this->db->prepare("SELECT id, title FROM courses WHERE id = ? AND status = 'approved' AND deleted_at IS NULL");
                    $stmt->execute([$courseId]);
                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                    if ($row) {
                        return "[{$row['title']}](/course/{$courseId})";
                    } else {
                        $hasInvalidLink = true; // Mark as invalid
                        return "**" . $text . "**";
                    }
                }

                // Case 2: Malformed specific course link like /course/id or /course/xyz
                if (str_contains($url, '/course/')) {
                    $hasInvalidLink = true; // Mark as invalid
                    return "**" . $text . "**";
                }

                // Case 3: Link to general courses page /courses
                if (str_contains($url, '/courses')) {
                    $cleanText = mb_strtolower(trim($text));
                    if (str_contains($cleanText, 'xem tất cả') || str_contains($cleanText, 'danh sách') || str_contains($cleanText, 'tất cả khóa học')) {
                        return "[{$text}](/courses)";
                    } else {
                        // Hallucinated course name linked to /courses
                        $hasInvalidLink = true;
                        return "**" . $text . "**";
                    }
                }

                // Case 4: Other internal links (lesson, chapter, etc.)
                if (preg_match('/^\/(lesson|chapter|user|profile|admin)/i', $url)) {
                    $hasInvalidLink = true; // Mark as invalid since AI assistant shouldn't generate these
                    return "**" . $text . "**";
                }

                return "[{$text}]({$url})";
            }, $line);

            if ($hasInvalidLink) {
                // Discard this line entirely to remove the fabricated course recommendation
                continue;
            }

            $cleanedLines[] = $line;
        }

        return implode("\n", $cleanedLines);
    }
}
