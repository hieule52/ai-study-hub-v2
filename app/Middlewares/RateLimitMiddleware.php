<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;

/**
 * Rate Limiting Middleware
 * Uses APCu (if available) or file-based counter as fallback.
 * Protects sensitive endpoints from brute-force & abuse.
 */
class RateLimitMiddleware
{
    /**
     * Handle rate limiting for a request.
     *
     * @param Request  $request
     * @param Response $response
     * @param int      $maxRequests  Max requests per window
     * @param int      $windowSeconds Time window in seconds
     * @param string   $group        Identifier group (e.g. 'auth', 'ai')
     */
    public static function handle(
        Request $request,
        Response $response,
        int $maxRequests = 30,
        int $windowSeconds = 60,
        string $group = 'default'
    ): void {
        // Lấy IP client (hỗ trợ proxy)
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_X_REAL_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';

        // Chỉ giữ IP đầu tiên nếu có chuỗi
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);

        $key = "ratelimit_{$group}_{$ip}";

        if (function_exists('apcu_fetch')) {
            // APCu fast path
            $data = apcu_fetch($key, $success);
            if (!$success) {
                apcu_store($key, ['count' => 1, 'reset_at' => time() + $windowSeconds], $windowSeconds);
                return;
            }

            if (time() > $data['reset_at']) {
                apcu_store($key, ['count' => 1, 'reset_at' => time() + $windowSeconds], $windowSeconds);
                return;
            }

            $data['count']++;
            if ($data['count'] > $maxRequests) {
                $retryAfter = $data['reset_at'] - time();
                header("Retry-After: {$retryAfter}");
                $response->error("Quá nhiều yêu cầu. Vui lòng thử lại sau {$retryAfter} giây.", 429);
            }
            apcu_store($key, $data, $windowSeconds);
        } else {
            // File-based fallback
            self::fileBasedRateLimit($key, $maxRequests, $windowSeconds, $response);
        }
    }

    private static function fileBasedRateLimit(
        string $key,
        int $maxRequests,
        int $windowSeconds,
        Response $response
    ): void {
        $dir = sys_get_temp_dir() . '/lms_ratelimit';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Sanitize key for filename
        $file = $dir . '/' . md5($key) . '.json';
        $now  = time();

        $data = ['count' => 0, 'reset_at' => $now + $windowSeconds];
        if (file_exists($file)) {
            $raw = file_get_contents($file);
            if ($raw) {
                $decoded = json_decode($raw, true);
                if ($decoded && $now <= $decoded['reset_at']) {
                    $data = $decoded;
                }
            }
        }

        $data['count']++;
        file_put_contents($file, json_encode($data), LOCK_EX);

        if ($data['count'] > $maxRequests) {
            $retryAfter = $data['reset_at'] - $now;
            header("Retry-After: {$retryAfter}");
            $response->error("Quá nhiều yêu cầu. Vui lòng thử lại sau {$retryAfter} giây.", 429);
        }
    }
}
