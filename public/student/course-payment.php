<?php
$pageTitle = 'Thanh Toán An Toàn - AI Study Hub';
$actor = 'guest';
ob_start();
?>
<style>
    body {
        background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.15), transparent 50%),
            radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.1), transparent 50%),
            var(--bg-main);
        min-height: 100vh;
    }

    .payment-wrapper {
        max-width: 900px;
        margin: 4rem auto;
        position: relative;
    }

    .payment-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.3), rgba(236, 72, 153, 0.3));
        filter: blur(80px);
        z-index: -1;
        border-radius: 50%;
    }

    .payment-container {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-xl);
        padding: 3rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 4rem;
    }

    .qr-section {
        text-align: center;
        border-right: 1px dashed rgba(255, 255, 255, 0.1);
        padding-right: 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .qr-box {
        position: relative;
        background: #fff;
        padding: 15px;
        border-radius: 20px;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
        margin: 2rem 0;
        display: inline-block;
    }

    .qr-box::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, var(--primary), var(--secondary), var(--warning));
        z-index: -1;
        border-radius: 22px;
        animation: gradient-spin 3s linear infinite;
    }

    @keyframes gradient-spin {
        0% {
            filter: hue-rotate(0deg);
        }

        100% {
            filter: hue-rotate(360deg);
        }
    }

    .qr-image {
        width: 220px;
        height: 220px;
        border-radius: 10px;
        display: block;
    }

    .info-section {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-row {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .info-label {
        color: var(--text-secondary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .highlight-text {
        color: var(--warning);
        font-size: 1.5rem;
    }

    .sync-status {
        margin-top: 2rem;
        padding: 1.25rem;
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid var(--success);
        border-radius: var(--radius-md);
        text-align: center;
        font-size: 0.95rem;
        color: var(--success);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
    }

    .pulse-dot {
        width: 12px;
        height: 12px;
        background: var(--success);
        border-radius: 50%;
        animation: pulse-dot 1.5s infinite;
    }

    @keyframes pulse-dot {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .content-box {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 1rem;
        border-radius: var(--radius-sm);
        font-family: monospace;
        font-size: 1.4rem !important;
        letter-spacing: 2px;
        text-align: center;
        color: var(--warning);
        text-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }
</style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="payment-wrapper">
        <div class="payment-glow"></div>

        <a href="javascript:history.back()"
            style="display:inline-block; margin-bottom: 1.5rem; color: var(--text-secondary); text-decoration: none;"
            data-i18n="pay_cancel">&larr;
            Hủy giao dịch</a>

        <div class="payment-container">
            <!-- QR Part -->
            <div class="qr-section">
                <h2 style="font-size: 2rem;"><span data-i18n="pay_title_1">Thanh toán </span><span class="text-gradient"
                        data-i18n="pay_title_2">Tự Động</span></h2>
                <p class="text-secondary mt-2" data-i18n="pay_subtitle">Sử dụng App ngân hàng để quét mã VietQR</p>

                <div class="qr-box">
                    <img id="qr-img" class="qr-image" src="" alt="Loading QR code...">
                </div>

                <div class="flex items-center gap-2 text-secondary" style="font-size: 0.85rem;" data-i18n="pay_note">
                    <span style="font-size: 1.2rem;">⚡</span> Hệ thống gạch nợ tự động trong 5-10 giây.
                </div>
            </div>

            <!-- Detail Part -->
            <div class="info-section">
                <h3 style="margin-bottom: 2rem; font-size: 1.5rem; color: var(--text-primary);">Chi tiết giao dịch
                </h3>

                <div>
                    <div class="info-row" style="display: flex; justify-content: space-between;">
                        <div class="info-label">Ngân hàng</div>
                        <div class="info-value">Viettin Bank</div>
                    </div>
                    <div class="info-row" style="display: flex; justify-content: space-between;">
                        <div class="info-label">Chủ tài khoản</div>
                        <div class="info-value">LE DIEN HIEU</div>
                    </div>
                    <div class="info-row" style="display: flex; justify-content: space-between;">
                        <div class="info-label">Số tài khoản</div>
                        <div class="info-value">101875375750</div>
                    </div>
                    <div class="info-row"
                        style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                        <div class="info-label">Số tiền</div>
                        <div class="info-value highlight-text" style="font-size: 2rem;" id="display-amount">...
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <div class="info-label" style="margin-bottom: 0.8rem;">Lời nhắn chuyển khoản (Ghi chính xác)
                        </div>
                        <div class="info-value content-box" id="display-content">...</div>
                    </div>
                </div>

                <div class="sync-status" id="sync-status">
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
        const courseId = urlParams.get('course_id');
        const price = urlParams.get('price');

        if (!courseId || !price) {
            const errMsg = window.I18n ? window.I18n.get('pay_err_invalid') : 'Thông tin hóa đơn không hợp lệ.';
            App.showToast(errMsg, 'error');
            setTimeout(() => window.location.href = '/student/dashboard.php', 2000);
            return;
        }

        // Configs
        const BANK_BIN = 'VIETINBANK';
        const ACCOUNT_NO = '101875375750';
        const ACCOUNT_NAME = 'LE DIEN HIEU';
        const AMOUNT = Math.round(parseFloat(price));
        const TRANSFER_CONTENT = `MUA ${courseId} ${user.id}`;

        // Setup Details
        document.getElementById('display-amount').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(AMOUNT);
        document.getElementById('display-content').innerText = TRANSFER_CONTENT;

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

        async function handleSuccess(cid) {
            try {
                await window.api.post(`/courses/${cid}/verify-purchase`, {});
                const toastSuccess = window.I18n ? window.I18n.get('pay_toast_success') : 'Giao dịch xác nhận thành công!';
                App.showToast(toastSuccess, 'success');
                setTimeout(() => {
                    window.location.href = `/student/learning.php?course_id=${cid}`;
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