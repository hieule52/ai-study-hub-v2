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
        $prompt = "BẠN LÀ: Một giảng viên đại học cao cấp, người cố vấn học thuật chuyên nghiệp (Expert Educational Mentor).\n";
        $prompt .= "TÍNH CÁCH: Kiên nhẫn, thông thái, lịch thiệp và luôn khuyến khích tư duy.\n";
        $prompt .= "NGÔN NGỮ: Tiếng Việt (hoặc ngôn ngữ của người dùng), chuyên nghiệp nhưng dễ hiểu.\n\n";

        $prompt .= "CHỈ THỊ HÀNH VI:\n";
        $prompt .= "1. Giải thích các khái niệm theo từng bước (step-by-step).\n";
        $prompt .= "2. Nếu là lập trình: Giải thích logic code, giải thuật và các lỗi thường gặp. Sử dụng Markdown để trình bày code đẹp mắt.\n";
        $prompt .= "3. KHÔNG tiết lộ đáp án ngay lập tức cho các câu hỏi bài tập. Hãy gợi ý và dẫn dắt để học viên tự tìm ra lời giải.\n";
        $prompt .= "4. Tuyệt đối từ chối các yêu cầu không liên quan đến giáo dục, giải trí hoặc các hành vi vi phạm đạo đức/bảo mật.\n\n";

        if ($course) {
            $prompt .= "KHÓA HỌC: \"{$course['title']}\" (Trình độ: {$course['level']}).\n";
        }

        if ($lesson) {
            $prompt .= "BÀI HỌC HIỆN TẠI: \"{$lesson['title']}\"\n";
            $prompt .= "--- NỘI DUNG NGỮ CẢNH (DÙNG ĐỂ TRẢ LỜI) ---\n";
            
            if (!empty($lesson['ai_summary'])) $prompt .= "[Tóm tắt bài]: {$lesson['ai_summary']}\n";
            if (!empty($lesson['video_transcript'])) $prompt .= "[Bản dịch Video]: {$lesson['video_transcript']}\n";
            if (!empty($lesson['lesson_context'])) $prompt .= "[Bối cảnh bổ sung]: {$lesson['lesson_context']}\n";
            
            if (!empty($lesson['content'])) {
                $clean = mb_substr(strip_tags($lesson['content']), 0, 4000);
                $prompt .= "[Nội dung chi tiết]: $clean\n";
            }

            if (!empty($lesson['quiz_explanations'])) {
                $prompt .= "[Giải thích bài tập]: {$lesson['quiz_explanations']}\n";
            }
            $prompt .= "-------------------------------------------\n";
        }

        return $prompt;
    }

    private function getDeepLessonContext(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, q.explanations as quiz_explanations, q.hints as quiz_hints
            FROM lessons l
            LEFT JOIN quizzes q ON l.id = q.lesson_id
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
