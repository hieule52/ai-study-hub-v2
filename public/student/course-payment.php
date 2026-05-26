<?php
$pageTitle = 'Thanh Toán An Toàn - AI Study Hub';
$actor = 'student';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/pages/payment.css?v=' . time() . '">';
require __DIR__ . '/../layouts/header.php';
?>

<div class="cinematic-bg-mini"></div>

<div class="container">
    <div class="payment-wrapper">
        
        <a href="javascript:history.back()"
            style="display:inline-flex; align-items:center; gap:8px; margin-bottom: 2rem; color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.3s;"
            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            <span data-i18n="pay_cancel">Hủy giao dịch</span>
        </a>

        <div class="payment-container fade-up visible">
            <!-- QR Part -->
            <div class="qr-section">
                <h2 style="font-size: 2.2rem; font-family: var(--font-heading); letter-spacing: -0.02em;">
                    <span data-i18n="pay_title_1">Thanh toán </span>
                    <span style="color: var(--accent);" data-i18n="pay_title_2">Tự Động</span>
                </h2>
                <p class="text-secondary mt-2" data-i18n="pay_subtitle" style="opacity: 0.5;">Sử dụng App ngân hàng để quét mã VietQR</p>

                <div class="qr-box">
                    <img id="qr-img" class="qr-image" src="" alt="Loading QR code...">
                </div>

                <div class="flex items-center gap-3 text-secondary" style="font-size: 0.85rem; opacity: 0.4;" data-i18n="pay_note">
                    <span>✦</span> Hệ thống gạch nợ tự động trong 5-10 giây.
                </div>
            </div>

            <!-- Detail Part -->
            <div class="info-section">
                <h3 style="margin-bottom: 2.5rem; font-size: 1.4rem; font-family: var(--font-heading); color: #fff;" data-i18n="pay_details_title">Chi tiết giao dịch</h3>

                <div class="flex flex-column gap-1">
                    <div class="info-row flex justify-between items-center">
                        <div class="info-label" data-i18n="pay_bank">Ngân hàng</div>
                        <div class="info-value">Viettin Bank</div>
                    </div>
                    <div class="info-row flex justify-between items-center">
                        <div class="info-label" data-i18n="pay_account_holder">Chủ tài khoản</div>
                        <div class="info-value">LE DIEN HIEU</div>
                    </div>
                    <div class="info-row flex justify-between items-center">
                        <div class="info-label" data-i18n="pay_account_number">Số tài khoản</div>
                        <div class="info-value" style="letter-spacing: 0.05em;">101875375750</div>
                    </div>
                    <div class="info-row flex justify-between items-center" style="border-bottom: none; padding-bottom: 0;">
                        <div class="info-label" data-i18n="pay_amount">Số tiền</div>
                        <div class="info-value highlight-text" style="font-size: 2.2rem;" id="display-amount">...</div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <div class="info-label" style="margin-bottom: 1rem;" data-i18n="pay_message">Lời nhắn chuyển khoản (Ghi chính xác)</div>
                        <div class="info-value content-box" id="display-content">...</div>
                    </div>
                </div>

                <div class="sync-status" id="sync-status" data-i18n="pay_waiting">
                    <div class="pulse-dot"></div>
                    Đang chờ thanh toán... Hệ thống đang dò tìm giao dịch.
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['student', 'admin', 'teacher']);
        if (!user) return;

        const urlParams = new URLSearchParams(window.location.search);
        // Support clean URL (/student/payment/8?price=...) and legacy (?course_id=8)
        const courseId = <?= json_encode($_GET['course_id'] ?? null) ?> ?? urlParams.get('course_id');

        if (!courseId) {
            const errMsg = window.I18n ? window.I18n.get('pay_err_invalid') : 'Thông tin hóa đơn không hợp lệ.';
            App.showToast(errMsg, 'error');
            setTimeout(() => window.location.href = '/student/dashboard', 2000);
            return;
        }

        let AMOUNT = 0;
        let TRANSFER_CONTENT = "";

        try {
            // Fetch authentic course details from API securely
            const courseRes = await window.api.get(`/courses/${courseId}`);
            const course = courseRes.data;

            if (!course || course.price === undefined) {
                throw new Error("Không thể lấy thông tin giá khóa học.");
            }

            AMOUNT = Math.round(parseFloat(course.price));
            TRANSFER_CONTENT = `MUA ${courseId} ${user.id}`;

            // Setup Details in UI
            document.getElementById('display-amount').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(AMOUNT);
            document.getElementById('display-content').innerText = TRANSFER_CONTENT;

            // Configs
            const BANK_BIN = 'VIETINBANK';
            const ACCOUNT_NO = '101875375750';
            const ACCOUNT_NAME = 'LE DIEN HIEU';

            // Generate VietQR Link
            const qrUrl = `https://img.vietqr.io/image/${BANK_BIN}-${ACCOUNT_NO}-compact2.png?amount=${AMOUNT}&addInfo=${encodeURIComponent(TRANSFER_CONTENT)}&accountName=${encodeURIComponent(ACCOUNT_NAME)}`;
            document.getElementById('qr-img').src = qrUrl;

            // Start Polling JSON from Apps Script
            const APPS_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbzGPUNNyERsT-7sweQuTvvOJJ8z7RUS4YOzPthw6kYCRMH6GcBMnyY22DiS9bDsemkA/exec";

            let pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(APPS_SCRIPT_URL);
                    const json = await response.json();

                    const data = json.data || json;

                    const targetContent = TRANSFER_CONTENT.toLowerCase().replace(/\s+/g, '');

                    let foundMatch = data.find(tx => {
                        const description = tx['Mô tả'] || tx.description;
                        const txAmount = tx['Giá trị'] || tx.amount;
                        if (!description) return false;
                        const desc = String(description).toLowerCase().replace(/\s+/g, '');
                        return desc.includes(targetContent) && txAmount >= AMOUNT;
                    });

                    if (foundMatch) {
                        clearInterval(pollInterval);

                        const statusBox = document.getElementById('sync-status');
                        statusBox.innerHTML = window.I18n ? window.I18n.get('pay_success_msg') : '✅ Lệ phí đã được thanh toán! Chuẩn bị vào lớp...';
                        statusBox.style.background = 'rgba(79, 70, 229, 0.2)';
                        statusBox.style.borderColor = 'var(--primary)';
                        statusBox.style.color = 'var(--primary)';

                        await handleSuccess(courseId);
                    }
                } catch (err) {
                    console.log('Fetching bank sync...', err.message);
                }
            }, 5000);

        } catch (e) {
            console.error(e);
            const errMsg = window.I18n ? window.I18n.get('pay_err_invalid') : 'Thông tin hóa đơn không hợp lệ.';
            App.showToast(errMsg + " (" + e.message + ")", 'error');
            setTimeout(() => window.location.href = '/student/dashboard', 3000);
            return;
        }

        // Render I18n
        if (window.I18n) window.I18n.render();

        async function handleSuccess(cid) {
            try {
                await window.api.post(`/courses/${cid}/verify-purchase`, {});
                const toastSuccess = window.I18n ? window.I18n.get('pay_toast_success') : 'Giao dịch xác nhận thành công!';
                App.showToast(toastSuccess, 'success');
                setTimeout(() => {
                    window.location.href = `/student/learning/${cid}`;
                }, 2500);
            } catch (e) {
                const toastErr = window.I18n ? window.I18n.get('pay_toast_err') : 'Lỗi khi mở khóa: ';
                App.showToast(toastErr + e.message, 'error');
            }
        }

    });
</script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>