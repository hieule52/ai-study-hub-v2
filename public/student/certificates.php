<?php
$pageTitle = 'Chứng Chỉ Của Tôi - AI Study Hub';
$actor = 'student';
$extraHead = '
    <link rel="stylesheet" href="/assets/css/student/certificates.css?v=' . time() . '">
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-container">
    <div class="certs-header">
        <h1 style="font-size: 2.5rem; letter-spacing: -1px;">
            🎓 Chứng Chỉ <span class="text-gradient">Của Tôi</span>
        </h1>
        <p class="text-secondary mt-2">Các chứng chỉ bạn đã đạt được sau khi hoàn thành 100% khóa học.</p>
    </div>

    <!-- Stats bar -->
    <div class="flex items-center gap-4 mb-8" id="cert-stats">
        <div class="card p-4 glass-panel" style="min-width: 160px;">
            <p class="text-secondary" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Tổng Chứng
                Chỉ</p>
            <h2 style="font-size: 2.5rem; color: var(--success);" id="total-count">0</h2>
        </div>
    </div>

    <!-- Certificate Grid -->
    <div class="cert-grid" id="cert-grid">
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
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
                        <h3 style="margin-bottom: 0.5rem;">Chưa có chứng chỉ nào</h3>
                        <p class="text-secondary">Hoàn thành 100% một khóa học và nhận chứng chỉ ngay!</p>
                        <a href="/student/dashboard" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-flex;">
                            📚 Đến khóa học của tôi
                        </a>
                    </div>`;
                return;
            }

            grid.innerHTML = certs.map(c => {
                const issueDate = new Date(c.issued_at).toLocaleDateString('vi-VN', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                const verifyUrl = `/certificate/verify?uuid=${encodeURIComponent(c.uuid)}`;
                const score = c.score !== null ? `<div class="cert-meta-row">⭐ Điểm: <strong>${c.score}/100</strong></div>` : '';

                return `
                <div class="cert-card">
                    <div class="cert-badge">✅ ĐÃ HOÀN THÀNH</div>
                    <span class="cert-icon">🎓</span>
                    <div class="cert-course-title">${escapeHtml(c.course_title)}</div>
                    <div class="cert-teacher">👨‍🏫 Giảng viên: ${escapeHtml(c.teacher_name)}</div>
                    <div class="cert-meta">
                        <div class="cert-meta-row">📅 Ngày cấp: <strong>${issueDate}</strong></div>
                        ${score}
                        <div class="cert-meta-row">🔑 UUID: <code style="font-size:0.7rem;color:var(--text-muted);">${c.uuid.substring(0, 18)}...</code></div>
                    </div>
                    <div class="cert-actions">
                        <a href="${verifyUrl}" target="_blank" class="btn btn-outline" style="flex:1; justify-content:center; border-color: var(--success); color: var(--success);">
                            🔍 Xác Minh
                        </a>
                        <button onclick="copyCertLink('${verifyUrl}')" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);" title="Sao chép link xác minh">
                            📋 Chia sẻ
                        </button>
                    </div>
                </div>`;
            }).join('');

        } catch (e) {
            document.getElementById('cert-grid').innerHTML = `
                <div style="grid-column:1/-1; text-align:center; color:var(--danger);">
                    Lỗi tải chứng chỉ: ${escapeHtml(e.message)}
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
        navigator.clipboard.writeText(fullUrl).then(() => {
            App.showToast('Đã sao chép link xác minh chứng chỉ!', 'success');
        }).catch(() => {
            prompt('Sao chép link này:', fullUrl);
        });
    };
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>