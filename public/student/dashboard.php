<?php
$pageTitle = 'Student Dashboard - AI Study Hub';
$actor = 'student';
$footerMode = 'mini';
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/assets/css/student/dashboard.css?v=' . time() . '">
';
require __DIR__ . '/../layouts/header.php';
?>

<!-- ── Page Header ── -->
<div class="dashboard-header-flex">
    <div>
        <p class="section-label" data-i18n="nav_student_dashboard">Hệ thống học tập</p>
        <h1 class="dashboard-title">
            <span data-i18n="std_welcome">Xin chào, </span>
            <span id="student-name" class="text-gradient">...</span> 👋
        </h1>
        <p class="text-secondary mt-2" style="font-size:1.05rem; opacity: 0.7;" data-i18n="std_subtitle">Sẵn sàng để bứt phá giới hạn kiến thức hôm nay?</p>
    </div>
    <a href="/courses" class="btn btn-primary dashboard-btn-explore">
        <span data-i18n="home_btn_explore">🔍 Khám phá khóa học</span>
    </a>
</div>

<!-- ── Stats Grid ── -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-orb" style="background:var(--primary);"></div>
        <p class="stat-label" data-i18n="std_learning">Đang học</p>
        <div class="stat-value">
            <span id="stat_learning">0</span><span class="stat-unit" data-i18n="std_courses_unit">khóa</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-orb" style="background:var(--success);"></div>
        <p class="stat-label" data-i18n="std_completed">Hoàn thành</p>
        <div class="stat-value" style="color:var(--success);">
            <span id="stat_completed">0</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-orb" style="background:var(--warning);"></div>
        <p class="stat-label" data-i18n="std_certs">Chứng chỉ</p>
        <div class="stat-value" style="color:var(--warning);">
            <span id="stat_certs">0</span>
        </div>
    </div>
</div>

<!-- ── Chart ── -->
<div class="chart-container">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="section-label" data-i18n="std_chart_title">Hiệu suất học tập</p>
            <h3 class="chart-header-title">Tiến độ hàng tuần</h3>
        </div>
        <div class="badge" style="background: rgba(255,255,255,0.05);">Real-time Stats</div>
    </div>
    <canvas id="learningChart" height="80"></canvas>
</div>

<!-- ── My Enrolled Courses ── -->
<div class="section-header">
    <div>
        <p class="section-label" data-i18n="nav_student_courses">Hành trình của bạn</p>
        <h2 style="font-size:1.6rem;font-weight:800;letter-spacing:-0.03em;" data-i18n="std_learning">🚀 Tiếp tục học tập</h2>
    </div>
    <a href="/student/courses" class="btn btn-ghost" style="font-size: 0.85rem;" data-i18n="std_view_all">Xem tất cả &rarr;</a>
</div>
<div class="scroller-container mb-12" id="enrolled-course-container">
    <div class="text-secondary loading-msg" data-i18n="home_loading">Đang tải dữ liệu...</div>
</div>

<!-- ── Divider ── -->
<div class="cinematic-divider"></div>

<!-- ── Explore New Courses ── -->
<div class="dashboard-header-flex">
    <div>
        <p class="section-label" data-i18n="home_courses_label">Cơ hội mới</p>
        <h2 style="font-size:1.6rem;font-weight:800;letter-spacing:-0.03em;" data-i18n="std_explore_new">
            Khóa Học <span class="text-gradient">Nổi Bật</span> 🌟
        </h2>
    </div>
    <div class="search-input-wrapper">
        <input type="text" id="courseSearchInput" class="form-control dashboard-search"
            placeholder="Tìm khóa học..."
            data-i18n="home_search_placeholder">
        <span class="search-input-icon">🔍</span>
    </div>
</div>
<div class="grid-cols-3 mb-12" id="all-course-list">
    <div class="card p-10 text-center" style="grid-column:span 3; background: rgba(255,255,255,0.02);">
        <p class="text-muted" data-i18n="home_loading">Đang lấy dữ liệu từ hệ thống...</p>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let enrolledCourseIds = [];

    window.enrollAndLearn = async function(courseId) {
        try {
            const btn = document.getElementById(`btn-enroll-${courseId}`);
            if (btn) { btn.textContent = 'Đang xử lý...'; btn.disabled = true; }
            await window.api.post(`/courses/${courseId}/enroll`, {});
        } catch(e) { console.log(e.message); }
        window.location.href = `/student/learning/${courseId}`;
    };

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
        if (user.role === 'admin')   { window.location.replace('/admin/dashboard'); return; }
        if (user.role === 'teacher') { window.location.replace('/teacher/dashboard'); return; }

        document.getElementById('student-name').textContent = user.username || user.email.split('@')[0];

        try {
            // ── Enrolled courses ──────────────────────────────────
            const [enrolledRes, certRes] = await Promise.all([
                window.api.get('/student/courses'),
                window.api.get('/certificates/my').catch(() => ({ data: [] }))
            ]);

            const enrolledCourses = enrolledRes.data || [];
            const certsList       = certRes.data || [];
            const certCourseIds   = certsList.map(c => c.course_id);
            enrolledCourseIds     = enrolledCourses.map(c => c.id);

            let completed = 0, learning = 0;
            const ec = document.getElementById('enrolled-course-container');
            ec.innerHTML = '';

            if (!enrolledCourses.length) {
                ec.innerHTML = `<div class="card p-5 text-center text-secondary" style="min-width:280px;" data-i18n="std_no_enrolled">Bạn chưa tham gia khóa học nào. Hãy khám phá bên dưới! 👇</div>`;
            } else {
                enrolledCourses.forEach(c => {
                    const prog    = parseInt(c.progress_percent) || 0;
                    const isDone  = prog >= 100;
                    const hasCert = certCourseIds.includes(c.id);
                    if (isDone) completed++; else learning++;

                    let certBtn = '';
                    if (isDone) {
                        if (hasCert) {
                            certBtn = `<button onclick="window.location.href='/student/certificates'" class="btn btn-outline" style="width:100%;border-radius:100px;font-size:0.8rem;color:var(--success);border-color:rgba(110,231,183,0.3);">🎓 Xem Chứng Chỉ</button>`;
                        } else {
                            certBtn = `<button onclick="claimCert(${c.id})" class="btn btn-primary" style="width:100%;border-radius:100px;font-size:0.8rem;background:rgba(251,191,36,0.15);color:var(--warning);border:1px solid rgba(251,191,36,0.3);">🏆 Nhận Chứng Chỉ</button>`;
                        }
                    }

                    const thumb = c.thumbnail
                        ? `<img src="${c.thumbnail}" class="scroller-card-img">`
                        : `<div class="scroller-card-placeholder">📚</div>`;

                    const card = document.createElement('div');
                    card.className = 'course-scroll-card';
                    card.innerHTML = `
                        ${thumb}
                        <div>
                            <h4 style="font-size:0.9rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:0.75rem;" title="${c.title}">${c.title}</h4>
                            <div class="progress-row">
                                <span data-i18n="std_progress">Tiến độ học</span>
                                <span style="color:${isDone?'var(--success)':'rgba(240,240,244,0.8)'};font-weight:600;">${prog}%</span>
                            </div>
                            <div class="progress-track mb-4">
                                <div class="progress-fill ${isDone?'done':''}" style="width:${prog}%;"></div>
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:0.5rem;">
                            <a href="/student/learning/${c.id}" class="btn btn-primary" style="width:100%;border-radius:100px;font-size:0.82rem;padding:0.55rem;" data-i18n="${prog>0?'std_btn_continue':'std_btn_start'}">${prog>0?'Tiếp tục học':'Vào học ngay'}</a>
                            ${certBtn}
                        </div>
                    `;
                    ec.appendChild(card);
                });
            }

            document.getElementById('stat_learning').textContent   = learning;
            document.getElementById('stat_completed').textContent  = completed;
            document.getElementById('stat_certs').textContent      = certsList.length;
            
            // Re-render i18n for dynamic content
            if (window.I18n) window.I18n.render();

            // ── Chart ─────────────────────────────────────────────
            const statsRes = await window.api.get('/student/stats');
            const realStats = statsRes.data.chart || statsRes.data; // fallback for safety
            const labels    = Object.keys(realStats).map(d => { const p=d.split('-'); return `${p[2]}/${p[1]}`; });
            const data      = Object.values(realStats);
            const chartLbl  = window.I18n ? window.I18n.get('std_chart_label') : 'Bài học hoàn thành';

            new Chart(document.getElementById('learningChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: chartLbl,
                        data,
                        borderColor: 'rgba(255,255,255,0.6)',
                        backgroundColor: 'rgba(255,255,255,0.05)',
                        tension: 0.4, fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(255,255,255,0.3)',
                        pointRadius: 4, borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' ' + chartLbl } }
                    },
                    scales: {
                        y: { beginAtZero: true, suggestedMax: 5, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: 'rgba(240,240,244,0.35)', stepSize: 1 } },
                        x: { grid: { display: false }, ticks: { color: 'rgba(240,240,244,0.35)' } }
                    }
                }
            });

            // ── All courses ────────────────────────────────────────
            let currentCourses = [];

            function renderCourseList(courses) {
                const el = document.getElementById('all-course-list');
                if (!courses.length) {
                    el.innerHTML = '<p class="text-muted" style="grid-column:span 3;text-align:center;" data-i18n="home_no_courses">Chưa có khóa học nào.</p>';
                    if (window.I18n) window.I18n.render(); return;
                }
                el.innerHTML = courses.map(c => {
                    const isEnrolled = enrolledCourseIds.includes(c.id);
                    const isPremium  = c.is_premium==1 || c.price>0;
                    let btn = '';
                    if (isEnrolled) {
                        btn = `<button onclick="window.location.href='/student/learning/${c.id}'" class="btn btn-outline" style="width:100%;border-radius:100px;font-size:0.82rem;color:var(--success);border-color:rgba(110,231,183,0.3);" data-i18n="std_owned">Đã sở hữu ✅</button>`;
                    } else if (isPremium) {
                        btn = `<button onclick="window.location.href='/student/payment/${c.id}?price=${c.price}'" class="btn btn-primary" style="width:100%;border-radius:100px;font-size:0.82rem;" data-i18n="std_btn_buy">🎓 Ghi danh khóa học</button>`;
                    } else {
                        btn = `<button id="btn-enroll-${c.id}" onclick="window.enrollAndLearn(${c.id})" class="btn btn-primary" style="width:100%;border-radius:100px;font-size:0.82rem;" data-i18n="std_btn_free">🚀 Tham gia ngay</button>`;
                    }
                    const priceHtml = c.price>0
                        ? `<span style="font-weight:700;">${new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(c.price)}</span>`
                        : `<span class="badge badge-free" data-i18n="home_free">Miễn phí</span>`;
                    const descHtml = c.description
                        ? c.description.substring(0, 90) + '...'
                        : `<span data-i18n="std_no_desc">Chưa có mô tả.</span>`;
                    return `
                    <div class="card liquid-glass" style="display:flex;flex-direction:column;">
                        <div class="card-img-placeholder" style="position:relative;height:180px;cursor:pointer;" onclick="window.location.href='/course/${c.id}'">
                            ${c.thumbnail?`<img src="${c.thumbnail}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">`:'<span style="position:relative;z-index:1;font-size:2.5rem;">📚</span>'}
                            ${isPremium?'<span class="badge badge-premium" style="position:absolute;top:10px;right:10px;z-index:2;">PREMIUM</span>':''}
                        </div>
                        <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                            <h3 class="card-title" style="cursor:pointer;" onclick="window.location.href='/course/${c.id}'">${c.title}</h3>
                            <p class="text-secondary" style="font-size:0.82rem;flex:1;margin-bottom:1rem;line-height:1.6;">${descHtml}</p>
                            <div class="flex items-center justify-between mb-3">${priceHtml}</div>
                            ${btn}
                        </div>
                    </div>`;
                }).join('');
                if (window.I18n) window.I18n.render();
            }

            const allRes = await window.api.get('/courses');
            currentCourses = allRes.data.items ?? allRes.data ?? [];
            renderCourseList(currentCourses);

            const searchInput = document.getElementById('courseSearchInput');
            let debounce = null;
            searchInput?.addEventListener('input', e => {
                clearTimeout(debounce);
                debounce = setTimeout(async () => {
                    const q = e.target.value.trim();
                    if (!q) { renderCourseList(currentCourses); return; }
                    try {
                        const sr = await window.api.get(`/courses/search?q=${encodeURIComponent(q)}`);
                        renderCourseList(sr.data.items ?? sr.data ?? []);
                    } catch(err) {}
                }, 400);
            });

        } catch(err) {
            console.error(err);
            document.getElementById('enrolled-course-container').innerHTML = `<div class="text-danger text-sm" data-i18n="std_conn_error">Lỗi kết nối.</div>`;
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>
