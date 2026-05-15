<?php
$pageTitle = 'Xác Minh Chứng Chỉ - AI Study Hub';
$actor = 'guest';
ob_start();
?>
<style>
    .verify-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .cert-card {
        background: linear-gradient(145deg, rgba(16, 185, 129, 0.08), rgba(79, 70, 229, 0.05));
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: var(--radius-xl);
        padding: 3rem;
        max-width: 600px;
        width: 100%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 40px rgba(16, 185, 129, 0.1);
    }
    .cert-seal {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--success), #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        animation: pulse-glow 2s infinite;
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 10px 50px rgba(16, 185, 129, 0.7); }
    }
    .cert-invalid {
        background: linear-gradient(145deg, rgba(239, 68, 68, 0.08), rgba(239, 68, 68, 0.03));
        border-color: rgba(239, 68, 68, 0.3);
    }
    .cert-invalid .cert-seal {
        background: linear-gradient(135deg, var(--danger), #dc2626);
        box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
        animation: none;
    }
    .cert-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: rgba(255,255,255,0.03);
        border-radius: var(--radius-md);
        margin-bottom: 0.75rem;
        text-align: left;
    }
    .cert-field label { color: var(--text-secondary); font-size: 0.85rem; }
    .cert-field span { font-weight: 600; color: var(--text-primary); }
    .verify-uuid-input {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }
</style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="verify-wrapper">
    <div style="width: 100%; max-width: 650px;">
        <h1 style="text-align:center; margin-bottom: 2rem;" data-i18n="cert_verify_title">🔍 Xác Minh Chứng Chỉ</h1>

        <!-- Input UUID -->
        <div class="verify-uuid-input">
            <input type="text" id="uuidInput" class="form-control" 
                   placeholder="Nhập UUID chứng chỉ (VD: a1b2c3d4-...)"
                   style="flex:1; font-family: monospace; font-size: 0.9rem;"
                   data-i18n="cert_verify_uuid_placeholder">
            <button onclick="verifyCert()" class="btn btn-primary" id="verifyBtn" data-i18n="cert_verify_btn">Xác Minh</button>
        </div>

        <!-- Result -->
        <div id="certResult" style="display:none;"></div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    // Auto-verify from URL if ?uuid= param present
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const uuid = params.get('uuid');
        if (uuid) {
            document.getElementById('uuidInput').value = uuid;
            verifyCert();
        }
    });

    async function verifyCert() {
        const uuid = document.getElementById('uuidInput').value.trim();
        if (!uuid) { App.showToast('Vui lòng nhập UUID chứng chỉ', 'error'); return; }

        const btn = document.getElementById('verifyBtn');
        const loadingText = window.I18n ? window.I18n.get('btn_loading') : '⏳ Đang kiểm tra...';
        btn.innerHTML = loadingText;
        btn.disabled = true;

        const resultEl = document.getElementById('certResult');
        resultEl.style.display = 'block';
        resultEl.innerHTML = '<div class="cert-card"><p>Đang xác minh...</p></div>';

        try {
            const res = await fetch(`/api/certificates/verify/${encodeURIComponent(uuid)}`);
            const data = await res.json();

            if (!res.ok || !data.data?.valid) {
                const invalidTitle = window.I18n ? window.I18n.get('cert_verify_invalid') : 'Chứng chỉ không hợp lệ';
                resultEl.innerHTML = `
                    <div class="cert-card cert-invalid">
                        <div class="cert-seal">❌</div>
                        <h2 style="color: var(--danger); margin-bottom: 1rem;">${invalidTitle}</h2>
                        <p class="text-secondary">${data.message || 'Chứng chỉ này không tồn tại hoặc đã bị thu hồi.'}</p>
                    </div>`;
                return;
            }

            const c = data.data;
            const issuedDate = new Date(c.issued_at).toLocaleDateString('vi-VN', {
                year: 'numeric', month: 'long', day: 'numeric'
            });

            const validTitle = window.I18n ? window.I18n.get('cert_verify_valid') : 'Chứng Chỉ Hợp Lệ ✓';
            const studentLbl = window.I18n ? window.I18n.get('cert_verify_student') : 'Học viên';
            const courseLbl = window.I18n ? window.I18n.get('cert_verify_course') : 'Khóa học';
            const teacherLbl = window.I18n ? window.I18n.get('cert_verify_teacher') : 'Giảng viên';
            const dateLbl = window.I18n ? window.I18n.get('cert_verify_date') : 'Ngày cấp';
            const scoreLbl = window.I18n ? window.I18n.get('cert_verify_score') : 'Điểm cuối';

            resultEl.innerHTML = `
                <div class="cert-card">
                    <div class="cert-seal">🎓</div>
                    <h2 style="color: var(--success); margin-bottom: 0.5rem;">${validTitle}</h2>
                    <p class="text-secondary" style="margin-bottom: 2rem; font-size: 0.9rem;">Chứng chỉ này đã được xác minh bởi AI Study Hub LMS</p>
                    
                    <div style="text-align: left;">
                        <div class="cert-field">
                            <label>${studentLbl}</label>
                            <span>👤 ${escapeHtml(c.student_name)}</span>
                        </div>
                        <div class="cert-field">
                            <label>${courseLbl}</label>
                            <span>📚 ${escapeHtml(c.course_title)}</span>
                        </div>
                        <div class="cert-field">
                            <label>${teacherLbl}</label>
                            <span>👨‍🏫 ${escapeHtml(c.teacher_name)}</span>
                        </div>
                        <div class="cert-field">
                            <label>${dateLbl}</label>
                            <span>📅 ${issuedDate}</span>
                        </div>
                        ${c.score !== null ? `<div class="cert-field"><label>${scoreLbl}</label><span>⭐ ${c.score}/100</span></div>` : ''}
                        <div class="cert-field">
                            <label>UUID</label>
                            <span style="font-family:monospace; font-size:0.75rem; color:var(--text-secondary);">${escapeHtml(c.uuid)}</span>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background: rgba(16,185,129,0.05); border-radius: var(--radius-md); border: 1px solid rgba(16,185,129,0.2);">
                        <p style="font-size: 0.8rem; color: var(--success); margin: 0;">
                            ✅ Chứng chỉ này xác nhận học viên đã hoàn thành 100% chương trình học của khóa học trên nền tảng AI Study Hub LMS.
                        </p>
                    </div>
                </div>`;
        } catch (e) {
            resultEl.innerHTML = `<div class="cert-card cert-invalid">
                <div class="cert-seal">⚠️</div>
                <p class="text-secondary">Lỗi kết nối. Vui lòng thử lại.</p>
            </div>`;
        } finally {
            btn.innerHTML = 'Xác Minh';
            btn.disabled = false;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>
