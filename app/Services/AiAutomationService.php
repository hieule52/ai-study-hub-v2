<?php

namespace App\Services;

use App\Core\Gemini;
use App\Core\Groq;
use Exception;

class AiAutomationService
{
    /**
     * Tự động hóa trích xuất metadata video và sinh nội dung bằng AI cho bài học
     * 
     * @param array $data Dữ liệu đầu vào từ form (tham chiếu)
     */
    public static function processLessonMetadata(array &$data): void
    {
        $contentType = $data['content_type'] ?? 'video';
        
        // 1. Xử lý Video Metadata
        if ($contentType === 'video') {
            $videoUrl = $data['video_url'] ?? '';
            $videoFilename = $data['video_filename'] ?? '';

            if (!empty($videoUrl) && self::isYouTubeUrl($videoUrl)) {
                // --- CASE 1: YouTube Video ---
                $youtubeId = self::extractYouTubeId($videoUrl);
                if ($youtubeId) {
                    $data['video_type'] = 'youtube';
                    $data['storage_driver'] = 'youtube';
                    $data['video_url'] = "https://www.youtube.com/embed/" . $youtubeId;
                    $data['video_thumbnail'] = "https://img.youtube.com/vi/" . $youtubeId . "/maxresdefault.jpg";
                    // Fallback thumbnail nếu không có maxres
                    if (empty($data['video_thumbnail'])) {
                        $data['video_thumbnail'] = "https://img.youtube.com/vi/" . $youtubeId . "/hqdefault.jpg";
                    }
                }
            } elseif (!empty($videoFilename)) {
                // --- CASE 2: Secure Uploaded Video ---
                $data['video_type'] = 'upload';
                $data['storage_driver'] = 'local';
                
                if (empty($data['secure_token'])) {
                    $data['secure_token'] = bin2hex(random_bytes(32));
                }

                // Nếu chưa có thumbnail, đặt ảnh mặc định
                if (empty($data['video_thumbnail'])) {
                    $data['video_thumbnail'] = '/uploads/courses/video_default_thumbnail.png';
                }
            }
        }

        // 2. Tự động sinh AI Transcript, Summary, Keywords, Context
        // Chỉ chạy AI nếu tiêu đề bài học không trống và các trường AI chưa được điền
        // Tôn trọng các toggle của giảng viên
        $autoSummary = (int)($data['enable_auto_summary'] ?? 1);
        $autoKeywords = (int)($data['enable_auto_keywords'] ?? 1);
        $autoContext = (int)($data['enable_auto_context'] ?? 1);

        $needsAi = false;
        if (!empty($data['title'])) {
            if ($autoSummary && empty($data['ai_summary'])) $needsAi = true;
            if ($autoKeywords && (empty($data['lesson_keywords']) && empty($data['key_topics']))) $needsAi = true;
            if ($autoContext && empty($data['lesson_context'])) $needsAi = true;
            if (empty($data['video_transcript'])) $needsAi = true;
        }

        if ($needsAi) {
            self::generateAiContent($data, $autoSummary, $autoKeywords, $autoContext);
        }
    }

    /**
     * Kiểm tra xem có phải link YouTube không
     */
    private static function isYouTubeUrl(string $url): bool
    {
        return preg_match('/(youtube\.com|youtu\.be|youtube-nocookie\.com)/i', $url) === 1;
    }

    /**
     * Trích xuất YouTube Video ID từ link
     */
    private static function extractYouTubeId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Gọi LLM (Gemini hoặc Groq) để sinh nội dung học tập thông minh
     * Tôn trọng các toggle của giảng viên
     */
    private static function generateAiContent(array &$data, int $autoSummary = 1, int $autoKeywords = 1, int $autoContext = 1): void
    {
        $title = $data['title'];
        $content = strip_tags($data['content'] ?? '');
        $objectives = $data['objectives'] ?? 'Nắm bắt kiến thức cốt lõi bài học.';
        $contentType = $data['content_type'] ?? 'video';

        $prompt = "Bạn là một giáo sư, chuyên gia AI giáo dục hàng đầu học viện AI Study Hub.
Hãy tạo nội dung học tập chuyên sâu bằng Tiếng Việt cho bài giảng sau:
- Tiêu đề bài giảng: \"{$title}\"
- Loại bài học: {$contentType}
- Tóm tắt/Mô tả sơ lược: \"{$content}\"
- Mục tiêu bài giảng: \"{$objectives}\"

Hãy trả về duy nhất một đối tượng JSON hợp lệ (không chứa từ nào khác ngoài JSON, không nằm trong tag markdown nếu có thể, hoặc dùng định dạng chuẩn) với các key sau:
1. \"video_transcript\": Nếu loại bài học là video, hãy soạn thảo một kịch bản lời thoại/bài giảng chi tiết, chuyên nghiệp và có độ sâu học thuật cực cao, mô phỏng video bài giảng thật dài khoảng 5-10 phút. Bắt đầu bằng các mốc thời gian e.g. `[00:00] Xin chào các bạn học viên... [01:30] Phân tích chủ đề...`. Nếu không phải video, hãy biên soạn một bài tóm tắt giảng giải chuyên sâu.
2. \"ai_summary\": Một bản tóm tắt học thuật tinh gọn, trình bày đẹp bằng Markdown, có bullet points rõ ràng các kiến thức quan trọng nhất.
3. \"lesson_context\": Ngữ cảnh và vai trò của bài học này trong chuỗi tư duy phát triển kiến thức khoa học máy tính/công nghệ của học viên, các bài viết liên quan hoặc lời khuyên từ AI Tutor.
4. \"lesson_keywords\": Các từ khóa chuyên ngành cốt lõi của bài học (khoảng 5-8 từ khóa, phân cách bằng dấu phẩy).

Mẫu JSON cần trả về:
{
  \"video_transcript\": \"nội dung...\",
  \"ai_summary\": \"nội dung...\",
  \"lesson_context\": \"nội dung...\",
  \"lesson_keywords\": \"từ khóa 1, từ khóa 2, ...\"
}";

        try {
            // Sử dụng Gemini trước
            $aiResponse = Gemini::ask($prompt);
            
            // Nếu Gemini báo lỗi hoặc trống, thử Groq
            if (empty($aiResponse) || str_starts_with($aiResponse, '❌')) {
                $aiResponse = Groq::ask($prompt);
            }

            if (!empty($aiResponse) && !str_starts_with($aiResponse, '❌')) {
                $parsed = self::parseJsonFromAi($aiResponse);
                if ($parsed) {
                    if (empty($data['video_transcript'])) {
                        $data['video_transcript'] = $parsed['video_transcript'] ?? null;
                        // Đánh dấu transcript được tạo tự động bởi AI
                        if (!empty($data['video_transcript'])) {
                            $data['transcript_status'] = 'auto_generated';
                        }
                    }
                    if ($autoSummary && empty($data['ai_summary'])) {
                        $data['ai_summary'] = $parsed['ai_summary'] ?? null;
                    }
                    if ($autoContext && empty($data['lesson_context'])) {
                        $data['lesson_context'] = $parsed['lesson_context'] ?? null;
                    }
                    if ($autoKeywords && (empty($data['lesson_keywords']) || empty($data['key_topics']))) {
                        $keywords = $parsed['lesson_keywords'] ?? null;
                        $data['lesson_keywords'] = $keywords;
                        $data['key_topics'] = $keywords;
                    }
                    return;
                }
            }
        } catch (Exception $e) {
            // Không chặn tiến trình lưu nếu AI lỗi
        }

        // --- FALLBACK SYSTEM ---
        // Nếu AI lỗi hoặc trả về không đúng cấu trúc, điền nội dung học thuật mẫu cực chuẩn
        if (empty($data['video_transcript'])) {
            $data['video_transcript'] = "[00:00] Xin chào toàn thể học viên của AI Study Hub. Trong bài học \"" . $title . "\" hôm nay, chúng ta sẽ cùng nghiên cứu sâu sắc về các khái niệm nền tảng của chủ đề.\n[02:00] Đi sâu vào phân tích kiến trúc thực tế...\n[05:00] Tóm lược các nội dung cốt lõi và hướng dẫn thực hành.";
            $data['transcript_status'] = 'auto_generated';
        }
        if ($autoSummary && empty($data['ai_summary'])) {
            $data['ai_summary'] = "### Điểm cốt lõi bài học:\n- **Khái niệm chính**: Nghiên cứu sâu về " . $title . ".\n- **Ứng dụng**: Cách triển khai thực tiễn trong công việc và học tập.\n- **Lưu ý quan trọng**: Đọc kỹ tài liệu đính kèm và làm bài kiểm tra đầy đủ.";
        }
        if ($autoContext && empty($data['lesson_context'])) {
            $data['lesson_context'] = "Bài học này nằm trong chương trình đào tạo chuyên sâu của khóa học, đóng vai trò bản lề giúp định hình tư duy thiết kế và tối ưu hóa giải pháp công nghệ.";
        }
        if ($autoKeywords && empty($data['lesson_keywords'])) {
            $data['lesson_keywords'] = "ai study hub, " . strtolower($title);
            $data['key_topics'] = $data['lesson_keywords'];
        }
    }

    /**
     * Parse JSON từ text phản hồi của AI (bỏ markdown code blocks ```json ... ```)
     */
    private static function parseJsonFromAi(string $text): ?array
    {
        // Loại bỏ ```json và ``` nếu có
        $clean = preg_replace('/^```(?:json)?\s+|\s+```$/i', '', trim($text));
        
        // Tìm vị trí của dấu { đầu tiên và dấu } cuối cùng phòng trường hợp AI trả kèm text giải thích
        $start = strpos($clean, '{');
        $end = strrpos($clean, '}');
        
        if ($start !== false && $end !== false) {
            $clean = substr($clean, $start, $end - $start + 1);
        }

        $decoded = json_decode($clean, true);
        return is_array($decoded) ? $decoded : null;
    }
}
