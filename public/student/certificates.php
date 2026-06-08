<?php
$pageTitle = 'Chứng Chỉ Của Tôi - AI Study Hub';
$actor = 'student';
$footerMode = 'mini';
$extraHead = '
    <link rel="stylesheet" href="/assets/css/student/certificates.css?v=' . time() . '">
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <div class="certs-header">
        <h1 style="font-size: 2.5rem; letter-spacing: -1px;">
            🎓 <span data-i18n="cert_title">Chứng Chỉ Của Tôi</span>
        </h1>
        <p class="text-secondary mt-2" data-i18n="cert_desc">Các chứng chỉ bạn đã đạt được sau khi hoàn thành 100% khóa học.</p>
    </div>

    <!-- Stats bar -->
    <div class="flex items-center gap-4 mb-8" id="cert-stats">
        <div class="card p-4 glass-panel" style="min-width: 160px;">
            <p class="text-secondary" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;" data-i18n="cert_total_label">Tổng Chứng Chỉ</p>
            <h2 style="font-size: 2.5rem; color: var(--success);" id="total-count">0</h2>
        </div>
    </div>

    <!-- Certificate Grid -->
    <div class="cert-grid" id="cert-grid">
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);" data-i18n="cert_loading">
            Đang tải chứng chỉ...
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['student']);
        if (!user) return;

        try {
            const res = await window.api.get('/certificates/my');
            const certs = res.data || [];
            const grid = document.getElementById('cert-grid');
            document.getElementById('total-count').textContent = certs.length;

            if (certs.length === 0) {
                grid.innerHTML = `
                    <div class="empty-certs" style="grid-column: 1/-1;">
                        <span class="emoji">🎯</span>
                        <h3 style="margin-bottom: 0.5rem;" data-i18n="cert_empty_title">Chưa có chứng chỉ nào</h3>
                        <p class="text-secondary" data-i18n="cert_empty_desc">Hoàn thành 100% một khóa học và nhận chứng chỉ ngay!</p>
                        <a href="/student/dashboard" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-flex;">
                            📚 <span data-i18n="cert_go_courses">Đến khóa học của tôi</span>
                        </a>
                    </div>`;
                if (window.I18n) window.I18n.render();
                return;
            }

            grid.innerHTML = certs.map(c => {
                const lang = window.I18n ? window.I18n.locale : 'vi';
                const issueDate = new Date(c.issued_at).toLocaleDateString(lang === 'vi' ? 'vi-VN' : 'en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                const verifyUrl = `/certificate/verify?uuid=${encodeURIComponent(c.uuid)}`;
                
                const scoreLabel = window.I18n ? window.I18n.get('cert_verify_score') : 'Điểm';
                const score = c.score !== null ? `<div class="cert-meta-row">⭐ ${scoreLabel}: <strong>${c.score}/100</strong></div>` : '';

                const teacherLbl = window.I18n ? window.I18n.get('cert_teacher_label') : 'Giảng viên';
                const dateLbl = window.I18n ? window.I18n.get('cert_date_label') : 'Ngày cấp';
                const completedBadge = window.I18n ? window.I18n.get('cert_completed_badge') : 'ĐÃ HOÀN THÀNH';
                const verifyBtnTxt = window.I18n ? window.I18n.get('cert_btn_verify') : 'Xác Minh';
                const shareBtnTxt = window.I18n ? window.I18n.get('cert_btn_share') : 'Chia sẻ';

                return `
                <div class="cert-card">
                    <div class="cert-badge">✅ ${completedBadge}</div>
                    <span class="cert-icon">🎓</span>
                    <div class="cert-course-title">${escapeHtml(c.course_title)}</div>
                    <div class="cert-teacher">👨‍🏫 ${teacherLbl}: ${escapeHtml(c.teacher_name)}</div>
                    <div class="cert-meta">
                        <div class="cert-meta-row">📅 ${dateLbl}: <strong>${issueDate}</strong></div>
                        ${score}
                        <div class="cert-meta-row">🔑 UUID: <code style="font-size:0.7rem;color:var(--text-muted);">${c.uuid.substring(0, 18)}...</code></div>
                    </div>
                    <div class="cert-actions">
                        <a href="${verifyUrl}" target="_blank" class="btn btn-outline" style="flex:1; justify-content:center; border-color: var(--success); color: var(--success);">
                            🔍 ${verifyBtnTxt}
                        </a>
                        <button onclick="copyCertLink('${verifyUrl}')" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);" title="Sao chép link xác minh">
                            📋 ${shareBtnTxt}
                        </button>
                    </div>
                </div>`;
            }).join('');
            if (window.I18n) window.I18n.render();

        } catch (e) {
            const errLbl = window.I18n ? window.I18n.get('std_conn_error') : 'Lỗi kết nối';
            document.getElementById('cert-grid').innerHTML = `
                <div style="grid-column:1/-1; text-align:center; color:var(--danger);">
                    ${errLbl}: ${escapeHtml(e.message)}
                </div>`;
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    window.copyCertLink = function (url) {
        const fullUrl = window.location.origin + url;
        const msg = window.I18n ? window.I18n.get('cert_copied_toast') : 'Đã sao chép link xác minh chứng chỉ!';
        navigator.clipboard.writeText(fullUrl).then(() => {
            App.showToast(msg, 'success');
        }).catch(() => {
            prompt('Copy link:', fullUrl);
        });
    };
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>