<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class VipController extends Controller
{
    // ====== CONFIG (ĐỒNG BỘ VỚI upgrade.php) ======
    private const DEFAULT_VIP_AMOUNT = 5000;
    private const DEFAULT_VIP_SECRET = 'CHANGE_ME_TO_RANDOM_LONG_SECRET'; // đổi thành chuỗi random dài
    private const GAS_URL = "https://script.google.com/macros/s/AKfycbykhtgco3MvHkM8TJBrI02TSTHMWeT_NBP5suKS_myqszeQzArZuf2F03dRXhZ8QyRlmQ/exec";
    // ==============================================

    private function vipAmount(): int
    {
        return (int)(getenv('VIP_AMOUNT') ?: self::DEFAULT_VIP_AMOUNT);
    }

    private function vipSecret(): string
    {
        return (string)(getenv('VIP_SECRET') ?: self::DEFAULT_VIP_SECRET);
    }

    public static function isVip(int $userId): bool
    {
        if ($userId <= 0) return false;

        $db = Database::connect();
        $stmt = $db->prepare("SELECT is_vip FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn() === 1;
    }

    public static function requireVip(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            header("Location: /login");
            exit;
        }

        // tránh loop khi đang ở /vip/upgrade
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if (!self::isVip($userId)) {
            if ($path !== '/vip/upgrade') {
                header("Location: /vip/upgrade");
            }
            exit;
        }
    }

    // GET /vip/check
    public function check(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['vip' => false, 'logged_in' => false]);
            return;
        }

        $vip = self::isVip($userId);
        $_SESSION['is_vip'] = $vip ? 1 : 0;

        echo json_encode(['vip' => $vip, 'logged_in' => true]);
    }

    // GET /vip/upgrade
    public function upgrade()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // chưa login → login
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            header('Location: /login');
            exit;
        }

        // đồng bộ session theo DB để tránh “session cũ”
        $isVip = self::isVip($userId);
        $_SESSION['is_vip'] = $isVip ? 1 : 0;

        // đã VIP → về trang chủ
        if ($isVip) {
            header('Location: /');
            exit;
        }

        return $this->view('vip/upgrade');
    }

    // GET /vip/sheet-proxy
    public function sheetProxy(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $ctx = stream_context_create([
            "http" => [
                "method"  => "GET",
                "timeout" => 15,
                "header"  => "User-Agent: Mozilla/5.0\r\n"
            ]
        ]);

        $raw = @file_get_contents(self::GAS_URL, false, $ctx);
        if ($raw === false) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'cannot_fetch_gas']);
            return;
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'gas_not_json',
                'raw_head' => substr($raw, 0, 300),
            ]);
            return;
        }

        // trả đúng raw JSON từ GAS
        echo $raw;
    }

    // POST /vip/confirm
    public function confirm(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['ok' => false, 'message' => 'not_login']);
            return;
        }

        $raw  = file_get_contents("php://input");
        $body = json_decode($raw, true);
        $code = (string)($body['code'] ?? '');

        $VIP_AMOUNT = $this->vipAmount();
        $VIP_SECRET = $this->vipSecret();

        // 1) VIP-UID{uid}-CID{cid}-{ts}-{sig}
       if (!preg_match('/^VIP(\d+)-(\d{10})-([a-fA-F0-9]{8})$/', $code, $m)) {
            echo json_encode(['ok' => false, 'message' => 'bad_code']);
            return;
        }

        $uid = (int)$m[1];
        $ts  = (int)$m[2];
        $sig = strtolower($m[3]);

        // chỉ cho phép mở VIP đúng user đang đăng nhập
        if ($uid !== $userId) {
            echo json_encode(['ok' => false, 'message' => 'uid_mismatch']);
            return;
        }

        // chống replay
        if ($ts < time() - 86400 || $ts > time() + 300) {
            echo json_encode(['ok' => false, 'message' => 'expired']);
            return;
        }

        // verify chữ ký
        $data   = $uid . '|' . $VIP_AMOUNT . '|' . $ts;
        $expect = substr(hash_hmac('sha256', $data, $VIP_SECRET), 0, 8);

        if (strtolower($expect) !== $sig) {
            echo json_encode([
                'ok' => false,
                'message' => 'bad_sig',
                'debug' => [
                    'expect' => strtolower($expect),
                    'got'    => strtolower($sig),
                ]
            ]);
            return;
        }

        // OK -> mở VIP
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET is_vip = 1 WHERE id = ?");
        $stmt->execute([$uid]);

        $_SESSION['is_vip'] = 1;

        echo json_encode(['ok' => true]);
    }
}
