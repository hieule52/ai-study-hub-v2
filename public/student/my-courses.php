<?php
$pageTitle = 'Khóa học của tôi - AI Study Hub';
$actor = 'student';
$extraHead = '
    <link rel="stylesheet" href="/assets/css/student/courses.css?v=' . time() . '">
';
require __DIR__ . '/../layouts/header.php';
?>

<!-- ── Page Header ── -->
<div class="flex items-center justify-between mb-10" style="flex-wrap:wrap;gap:1.5rem;">
    <div>
        <p class="section-label" data-i18n="nav_student_dashboard">Student Portal</p>
        <h1 style="font-size:clamp(2rem,4vw,2.5rem);font-weight:900;letter-spacing:-0.05em;" data-i18n="nav_student_courses">
            📚 Khóa học của tôi
        </h1>
        <p class="text-secondary mt-2" style="font-size:1.05rem; opacity: 0.7;" data-i18n="std_my_courses_subtitle">
            Theo dõi và tiếp tục hành trình chinh phục tri thức của bạn
        </p>
    </div>
    <a href="/courses.php" class="btn btn-primary" style="border-radius:100px; padding: 0.8rem 1.5rem; font-weight: 700;" data-i18n="home_btn_explore">✨ Khám phá thêm</a>
</div>

<!-- ── Stats Pills ── -->
<div class="flex gap-4 mb-10" style="flex-wrap:wrap;">
    <div class="stat-pill">
        <span>📊</span> <span data-i18n="std_total">Tổng</span>: <strong id="count_total" style="color: #fff;">–</strong>
    </div>
    <div class="stat-pill" style="border-color: rgba(16, 185, 129, 0.2); color: var(--success);">
        <span>✅</span> <span data-i18n="std_completed">Hoàn thành</span>: <strong id="count_done">–</strong>
    </div>
    <div class="stat-pill" style="border-color: rgba(99, 102, 241, 0.2); color: var(--primary);">
        <span>⏳</span> <span data-i18n="std_learning">Đang học</span>: <strong id="count_learning">–</strong>
    </div>
</div>

<!-- ── Course Grid ── -->
<div class="course-grid mb-12" id="enrolled-course-container">
    <div class="text-secondary text-center" style="grid-column:span 3; padding: 4rem; background: rgba(255,255,255,0.02); border-radius: var(--radius-xl);" data-i18n="home_loading">
        Đang đồng bộ hóa dữ liệu khóa học...
    </div>
</div>

<?php ob_start(); ?>
<script>
    window.claimCert = async function(courseId) {
        try {
            await window.api.post(`/certificates/claim/${courseId}`, {});
            App.showToast('🎓 Chứng chỉ đã được cấp thành công!', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } catch(e) { App.showToast(e.message, 'error'); }
    };

    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['student']);
        if (!user) return;

        try {
            const [enrolledRes, certRes] = await Promise.all([
                window.api.get('/student/courses'),
                window.api.get('/certificates/my').catch(() => ({ data: [] }))
            ]);

            const courses       = enrolledRes.data || [];
            const certsList     = certRes.data || [];
            const certCourseIds = certsList.map(c => c.course_id);

            let total = courses.length, done = 0, learning = 0;

            const container = document.getElementById('enrolled-course-container');
            container.innerHTML = '';

            if (!courses.length) {
                container.style.gridTemplateColumns = '1fr';
                container.innerHTML = `
                    <div class="card p-12 text-center" style="max-width:520px;margin:4rem auto; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                        <div style="font-size:4rem;margin-bottom:1.5rem;">📭</div>
                        <h2 style="margin-bottom:0.75rem; font-weight: 800;" data-i18n="std_no_enrolled">Hành trình chưa bắt đầu</h2>
                        <p class="text-secondary mb-8" style="font-size: 1.1rem; opacity: 0.7;">Bạn chưa tham gia khóa học nào. Hãy để AI Study Hub giúp bạn tìm thấy đam mê mới!</p>
                        <a href="/courses.php" class="btn btn-primary" style="border-radius:100px; padding: 1rem 2rem; font-weight: 700;" data-i18n="std_explore_new">✨ Khám phá khóa học ngay</a>
                    </div>`;
            } else {
                courses.forEach(c => {
                    const prog    = parseInt(c.progress_percent) || 0;
                    const isDone  = prog >= 100;
                    const hasCert = certCourseIds.includes(c.id);
                    if (isDone) done++; else learning++;

                    let certBtn = '';
                    if (isDone) {
                        certBtn = hasCert
                            ? `<a href="/student/certificates.php" class="btn btn-outline" style="width:100%;border-radius:100px;font-size:0.85rem;color:var(--success);border-color:rgba(16,185,129,0.3);">🎓 Xem Chứng Chỉ</a>`
                            : `<button onclick="claimCert(${c.id})" class="btn" style="width:100%;border-radius:100px;font-size:0.85rem;background:rgba(251,191,36,0.1);color:var(--warning);border:1px solid rgba(251,191,36,0.25);">🏆 Nhận Chứng Chỉ</button>`;
                    }

                    const thumb = c.thumbnail
                        ? `<img src="${c.thumbnail}" alt="${c.title}">`
                        : `<div style="height:100%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.03);font-size:4rem;color:rgba(255,255,255,0.1);">📚</div>`;

                    const badge = isDone
                        ? `<span class="status-badge" style="background:rgba(16,185,129,0.2); color:var(--success); border-color:rgba(16,185,129,0.3);">Hoàn thành</span>`
                        : (prog > 0 ? `<span class="status-badge" style="background:rgba(99,102,241,0.2); color:var(--primary); border-color:rgba(99,102,241,0.3);">Đang học</span>` : '');

                    const card = document.createElement('div');
                    card.className = 'my-course-card';
                    card.innerHTML = `
                        <div class="card-thumb-area">
                            ${thumb}
                            ${badge}
                        </div>
                        <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
                            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${c.title}">${c.title}</h3>
                            <div style="flex:1;"></div>
                            <div class="progress-row">
                                <span data-i18n="std_progress">Tiến độ hiện tại</span>
                                <span style="color:${isDone?'var(--success)':'var(--text-primary)'};">${prog}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill ${isDone?'done':''}" style="width:${prog}%;"></div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                                <a href="/student/learning.php?course_id=${c.id}" class="btn btn-primary" style="width:100%;border-radius:100px;font-size:0.85rem;padding:0.75rem; font-weight:700;" data-i18n="${prog>0?'std_btn_continue':'std_btn_start'}">${prog>0?'Tiếp tục học':'Vào học ngay'}</a>
                                ${certBtn}
                            </div>
                        </div>`;
                    container.appendChild(card);
                });
            }

            document.getElementById('count_total').textContent    = total;
            document.getElementById('count_done').textContent     = done;
            document.getElementById('count_learning').textContent = learning;

            if (window.I18n) window.I18n.render();

        } catch(err) {
            console.error(err);
            document.getElementById('enrolled-course-container').innerHTML =
                `<div class="text-danger text-sm" data-i18n="std_conn_error">Lỗi kết nối.</div>`;
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>
