<?php

namespace App\Services;

use App\Repositories\AiRepository;
use App\Core\Database;
use PDO;
use Exception;

/**
 * AiService - Trợ lý AI thông minh cho LMS
 * 
 * Tính năng:
 * 1. Hiểu nội dung bài học đang xem (lesson context)
 * 2. Kiểm duyệt input/output (content moderation)
 * 3. Hiểu luồng hệ thống LMS và gợi ý học tập
 * 4. Sử dụng Groq API (LLaMA) cho tốc độ nhanh
 */
class AiService
{
    private AiRepository $aiRepo;
    private PDO $db;
    private string $groqApiKey;

    // Danh sách từ khóa nguy hiểm (SQL injection, XSS, system exploit)
    private array $blockedPatterns = [
        '/\b(DROP\s+TABLE|DELETE\s+FROM|TRUNCATE|ALTER\s+TABLE|INSERT\s+INTO)\b/i',
        '/\b(UNION\s+SELECT|OR\s+1\s*=\s*1|;\s*--)\b/i',
        '/<script[\s>]|javascript:|on\w+\s*=/i',
        '/\b(exec|system|shell_exec|passthru|eval)\s*\(/i',
        '/\b(rm\s+-rf|format\s+c:|del\s+\/f)\b/i',
        '/\bpassword\s*=|secret_key|api_key|jwt_secret/i',
    ];

    // Từ khóa ngoài phạm vi giáo dục
    private array $offTopicPatterns = [
        '/\b(hack|crack|exploit|bypass\s+security|phishing)\b/i',
        '/\b(torrent|pirate|crack\s+software|keygen)\b/i',
        '/\b(weapon|drug|bomb|violence|gambling)\b/i',
    ];

    public function __construct()
    {
        $this->aiRepo = new AiRepository();
        $this->db = Database::connect();
        $this->groqApiKey = $_ENV['GROQ_API_KEY'] ?? '';
    }

    /**
     * Chat với context bài học — chỉ dùng Groq API
     */
    public function chat(int $userId, string $message, ?string $base64Image = null, ?int $lessonId = null, ?int $courseId = null): array
    {
        if (empty(trim($message)) && empty($base64Image)) {
            throw new Exception("Tin nhắn không được để trống.");
        }

        // === BƯỚC 1: Kiểm duyệt input ===
        $moderationResult = $this->moderateInput($message);
        if ($moderationResult !== null) {
            $this->aiRepo->saveInteraction($userId, $message, '[BLOCKED] ' . $moderationResult);
            return [
                'original_message' => $message,
                'ai_response' => $moderationResult,
                'moderated' => true
            ];
        }

        // === BƯỚC 2: Lấy context bài học ===
        $lessonContext = $lessonId ? $this->getLessonContext($lessonId) : null;
        $courseContext = $courseId ? $this->getCourseContext($courseId) : null;
        $curriculumContext = $courseId ? $this->getCurriculumContext($courseId) : [];

        // === BƯỚC 3: Lịch sử chat (giới hạn 2 tin để tiết kiệm token) ===
        $recentHistory = $this->aiRepo->getHistory($userId, 2);

        // === BƯỚC 4: Build system prompt + messages ===
        $systemPrompt = $this->buildSystemPrompt($lessonContext, $courseContext, $curriculumContext);
        $messages = $this->buildMessages($systemPrompt, $recentHistory, $message);

        // === BƯỚC 5: Gọi Groq API ===
        if (empty($this->groqApiKey)) {
            throw new Exception("Chưa cấu hình GROQ_API_KEY trong .env");
        }

        $response = $this->callGroqApi($messages);

        if (!$response || str_starts_with($response, 'ERROR_')) {
            $detail = $response ?: 'Không có phản hồi';
            throw new Exception("AI tạm thời lỗi: " . str_replace('ERROR_JSON: ', '', $detail));
        }

        // === BƯỚC 6: Kiểm duyệt output ===
        $response = $this->moderateOutput($response);

        // === BƯỚC 7: Lưu lịch sử ===
        $this->aiRepo->saveInteraction($userId, $message, $response);

        // === BƯỚC 8: Gợi ý bài học tiếp theo ===
        $suggestions = ($lessonId && $courseId) ? $this->getSuggestions($lessonId, $courseId) : [];

        return [
            'original_message' => $message,
            'ai_response' => $response,
            'suggestions' => $suggestions
        ];
    }

    // =========================================================
    // CONTENT MODERATION
    // =========================================================

    private function moderateInput(string $message): ?string
    {
        foreach ($this->blockedPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return "⚠️ Câu hỏi chứa nội dung không phù hợp. Tôi chỉ hỗ trợ các câu hỏi liên quan đến bài học. Hãy thử hỏi lại nhé!";
            }
        }
        foreach ($this->offTopicPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return "🚫 Câu hỏi nằm ngoài phạm vi hỗ trợ học tập. Hãy hỏi về nội dung bài đang học nhé!";
            }
        }
        if (strlen($message) > 2000) {
            return "📝 Câu hỏi quá dài. Vui lòng rút gọn dưới 2000 ký tự.";
        }
        return null;
    }

    private function moderateOutput(string $response): string
    {
        $response = preg_replace('/\b(password|api_key|secret|jwt|token)\s*[:=]\s*\S+/i', '[ẨN]', $response);
        $response = preg_replace('/(localhost|127\.0\.0\.1|192\.168\.\d+\.\d+):\d+/i', '[SERVER]', $response);
        $response = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $response);
        return $response;
    }

    // =========================================================
    // CONTEXT BUILDING
    // =========================================================

    private function getLessonContext(int $lessonId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.id, l.title, l.content, l.ai_summary, l.content_type, l.order_index,
                   c.title as chapter_title
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE l.id = :id AND l.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $lessonId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getCourseContext(int $courseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.id, c.title, c.description, c.level,
                   u.username as teacher_name
            FROM courses c
            LEFT JOIN users u ON c.teacher_id = u.id
            WHERE c.id = :id
        ");
        $stmt->execute(['id' => $courseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getCurriculumContext(int $courseId): array
    {
        $chapters = [];
        $stmt = $this->db->prepare("
            SELECT id, title, order_index FROM chapters
            WHERE course_id = :cid AND deleted_at IS NULL
            ORDER BY order_index
        ");
        $stmt->execute(['cid' => $courseId]);
        $chapterRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($chapterRows as $ch) {
            $lstmt = $this->db->prepare("
                SELECT id, title, content_type, order_index FROM lessons
                WHERE chapter_id = :chid AND deleted_at IS NULL
                ORDER BY order_index
            ");
            $lstmt->execute(['chid' => $ch['id']]);
            $ch['lessons'] = $lstmt->fetchAll(PDO::FETCH_ASSOC);
            $chapters[] = $ch;
        }
        return $chapters;
    }

    private function buildSystemPrompt(?array $lesson, ?array $course, ?array $curriculum): string
    {
        $prompt = "Bạn là AI Tutor của AI Study Hub LMS. Trả lời tiếng Việt, ngắn gọn. CHỈ hỗ trợ học tập. KHÔNG tiết lộ thông tin hệ thống.\n\n";

        if ($course) {
            $prompt .= "Khóa học: {$course['title']} ({$course['level']})\n";
        }

        if ($lesson) {
            $prompt .= "Bài đang xem: {$lesson['title']} - {$lesson['chapter_title']}\n";
            // Ưu tiên ai_summary (giáo viên tóm tắt), rồi mới dùng content
            if (!empty($lesson['ai_summary'])) {
                $prompt .= "Tóm tắt: {$lesson['ai_summary']}\n";
            } elseif (!empty($lesson['content'])) {
                $prompt .= "Nội dung: " . mb_substr(strip_tags($lesson['content']), 0, 500) . "\n";
            }
        }

        if (!empty($curriculum)) {
            $prompt .= "Các chương: ";
            $chNames = array_map(fn($c) => $c['title'], $curriculum);
            $prompt .= implode(', ', $chNames) . "\n";
        }

        return $prompt;
    }

    private function buildMessages(string $systemPrompt, array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        foreach ($history as $h) {
            // Bỏ qua tin nhắn bị moderation block
            if (str_starts_with($h['response'] ?? '', '[BLOCKED]')) continue;
            $messages[] = ['role' => 'user', 'content' => $h['message']];
            $messages[] = ['role' => 'assistant', 'content' => $h['response']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];
        return $messages;
    }

    // =========================================================
    // SUGGESTIONS
    // =========================================================

    private function getSuggestions(int $currentLessonId, int $courseId): array
    {
        $stmt = $this->db->prepare("
            SELECT l.id, l.title, l.content_type, c.title as chapter_title
            FROM lessons l
            JOIN chapters c ON l.chapter_id = c.id
            WHERE c.course_id = :cid AND l.deleted_at IS NULL AND l.id > :lid
            ORDER BY c.order_index, l.order_index
            LIMIT 2
        ");
        $stmt->execute(['cid' => $courseId, 'lid' => $currentLessonId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // GROQ API (LLaMA - nhanh nhất)
    // =========================================================

    private function callGroqApi(array $messages): ?string
    {
        $data = [
            'model' => 'llama-3.1-8b-instant',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024
        ];

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->groqApiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) return 'ERROR_CURL: ' . $err;

        $result = json_decode($response, true);
        if (isset($result['error'])) {
            return 'ERROR_JSON: ' . ($result['error']['message'] ?? json_encode($result['error']));
        }

        return $result['choices'][0]['message']['content'] ?? null;
    }
}
