<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$userId = (int)($_SESSION['user_id'] ?? 0);
// $username = (string)($_SESSION['username'] ?? '');

if ($userId <= 0) {
  header("Location: /login");
  exit;
}

// ================== CONFIG ==================
$AMOUNT = 5000;                 // giá VIP
$BANK_ID = "MB";                // VietQR dùng "MB"
$ACCOUNT_NO = "0328821260";
$ACCOUNT_NAME = "LE DIEN HIEU";

// ĐỔI secret này thành chuỗi random dài (GIỐNG y chang trong webhook.php)
define('VIP_SECRET', 'CHANGE_ME_TO_RANDOM_LONG_SECRET');
// ============================================

// sanitize username cho gọn
// $customerId = preg_replace('/[^A-Za-z0-9_-]/', '', $username);
// if ($customerId === '') $customerId = 'USER';

$ts  = time();
$data = $userId . '|' . $AMOUNT . '|' . $ts;

$VIP_SECRET = $_ENV['VIP_SECRET'] ?? '';
if ($VIP_SECRET === '') {
    die('VIP_SECRET not set');
}

$sig = substr(
    hash_hmac('sha256', $data, $VIP_SECRET),
    0,
    8
);

// 🔥 NỘI DUNG CHUYỂN KHOẢN NGẮN
$code = "VIP{$userId}-{$ts}-{$sig}";

$qr = "https://img.vietqr.io/image/{$BANK_ID}-{$ACCOUNT_NO}-compact2.png"
    . "?amount={$AMOUNT}"
    . "&addInfo=" . urlencode($code)
    . "&accountName=" . urlencode($ACCOUNT_NAME);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Nâng cấp VIP</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .vip-card{max-width:520px;margin:40px auto;padding:20px;border-radius:18px;background:#ffffff10;border:1px solid #ffffff1f}
    .vip-actions{display:flex;gap:10px;justify-content:center;margin-top:14px;flex-wrap:wrap}
    .vip-btn{padding:10px 16px;border-radius:999px;border:1px solid #ffffff33;background:#ffffff10;color:#fff;text-decoration:none;cursor:pointer}
    .vip-btn-primary{background:#7c5cff;border:none}
    .vip-toast{display:none;margin-top:12px;padding:10px 12px;border-radius:12px;background:#00c48c1a;border:1px solid #00c48c55}
    .vip-toast.error{background:#ff4d4f1a;border-color:#ff4d4f55}
  </style>
</head>
<body style="padding:24px;">
  <div class="vip-card">
    <h2 style="margin:0 0 10px">🌟 Nâng cấp VIP</h2>

    <p>Số tiền: <b><?= number_format($AMOUNT) ?>đ</b></p>
    <!-- <p>ID khách hàng: <b><?= htmlspecialchars($customerId) ?></b></p> -->

    <p>
      Nội dung chuyển khoản (bắt buộc đúng):<br>
      <b style="color:#ffcf66;word-break:break-all"><?= htmlspecialchars($code) ?></b>
    </p>

    <img src="<?= htmlspecialchars($qr) ?>" alt="QR thanh toán"
         style="max-width:100%;border-radius:16px;margin:12px 0;background:#fff;padding:8px">

    <p style="opacity:.85;margin:0 0 8px">
      ✔ Chuyển khoản đúng <b>số tiền</b> + <b>nội dung</b> → hệ thống tự mở VIP.
    </p>

    <div class="vip-actions">
      <a href="/games" class="vip-btn">← Quay lại Games</a>
      <button id="btnCheck" type="button" class="vip-btn vip-btn-primary">Tôi đã thanh toán (Kiểm tra)</button>
    </div>

    <div id="toastOk" class="vip-toast">
      ✅ Thanh toán thành công! Đang mở game Samurai...
    </div>
    <div id="toastErr" class="vip-toast error">
      ⏳ Chưa thấy giao dịch đúng. Vui lòng chờ 10–30 giây rồi bấm kiểm tra lại.
    </div>

    <p id="payStatus" style="margin-top:10px;opacity:.85">
      ⏳ Nếu bạn vừa thanh toán xong, hãy bấm “Kiểm tra” hoặc chờ hệ thống tự cập nhật.
    </p>
  </div>
    <script>
        const CODE = <?= json_encode($code) ?>;
        const AMOUNT = <?= (int)$AMOUNT ?>;

        function norm(str){
        return String(str || '')
            .normalize('NFD')                 // tách dấu
            .replace(/[\u0300-\u036f]/g, '')  // xoá dấu
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '');
        }

        function pickValue(row, candidates){
        const keys = Object.keys(row || {});
        const map = {};
        keys.forEach(k => map[norm(k)] = k);

        for (const c of candidates){
            const ck = norm(c);
            for (const nk in map){
            if (nk.includes(ck) || ck.includes(nk)) return row[map[nk]];
            }
        }
        return undefined;
        }

        function toNumberMoney(v){
        return Number(String(v ?? '0').replace(/[^\d]/g, '')) || 0;
        }

        async function checkPaid(){
        const statusEl = document.getElementById("payStatus");
        const toastOk = document.getElementById("toastOk");
        const toastErr = document.getElementById("toastErr");

        if (toastOk) toastOk.style.display = "none";
        if (toastErr) toastErr.style.display = "none";
        if (statusEl) statusEl.textContent = "⏳ Đang kiểm tra giao dịch...";

        try {
            const res = await fetch("/vip/sheet-proxy", { cache: "no-store" });
            if (!res.ok) throw new Error("proxy_http_" + res.status);

            const json = await res.json();
            const rows = Array.isArray(json.data) ? json.data : [];

            const codeNorm = norm(CODE);

            // debug nếu cần:
            // console.log("keys:", Object.keys(rows[0] || {}));

            const found = rows.slice(-50).reverse().find(row => {
            const desc = pickValue(row, ["Mô tả", "Mo ta", "Noi dung", "Description", "memo", "content"]) || "";
            const amountRaw = pickValue(row, ["Giá trị", "Gia tri", "Amount", "So tien", "Value"]) || 0;

            const amount = toNumberMoney(amountRaw);

            // ✅ chứa mã + tiền sheet >= tiền yêu cầu
            return amount >= AMOUNT && norm(desc).includes(codeNorm);
            });

            if (!found) {
            if (toastErr) toastErr.style.display = "block";
            if (statusEl) statusEl.textContent = "❌ Chưa thấy giao dịch đúng (nội dung chứa mã + số tiền đủ). Chờ 10–30s rồi thử lại.";
            return;
            }

            // thấy giao dịch -> gọi server mở VIP
            const r2 = await fetch("/vip/confirm", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ code: CODE })
            });

            const j2 = await r2.json();

            if (j2.ok) {
            if (toastOk) toastOk.style.display = "block";
            if (statusEl) statusEl.textContent = "✅ Thanh toán thành công! Đang chuyển vào Samurai...";
            setTimeout(() => window.location.href = "/games/samurai", 800);
            } else {
            if (statusEl) statusEl.textContent = "⚠️ Đã thấy giao dịch nhưng server chưa xác nhận VIP: " + (j2.message || "unknown");
            }

        } catch (e) {
            console.error(e);
            if (statusEl) statusEl.textContent = "⚠️ Lỗi khi kiểm tra giao dịch (proxy/JSON).";
        }
        }

        document.getElementById("btnCheck")?.addEventListener("click", checkPaid);
    </script>

</body>
</html>
