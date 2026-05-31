<?php
$pageTitle  = 'AI Study Hub® — Cinematic AI Learning';
$actor      = 'guest';
$extraHead  = '<link rel="stylesheet" href="/assets/css/pages/home.css?v=6">';
require __DIR__ . '/layouts/header.php';
?>
<main class="home-page">


    <!-- ── CINEMATIC BG VIDEO ── -->
    <div class="cinematic-bg">
        <video autoplay muted loop playsinline class="cinematic-video">
            <source
                src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4"
                type="video/mp4">
        </video>
        <div class="cinematic-overlay"></div>
    </div>

    <!-- ── SECTION 1: CINEMATIC HERO ── -->
    <section class="hero-section">

        <div class="hero-content flex flex-column items-center">
            <span class="editorial-label hero-label fade-up" data-i18n="home_label_academic">Đào tạo xuất sắc</span>
            <h1 class="editorial-title hero-heading fade-up" data-i18n="home_hero_title" style="text-align: center;">
                Đánh thức<br>
                <em>tiềm năng</em><br>
                qua học tập thông minh.
            </h1>
            <p class="editorial-text hero-subtext fade-up" data-i18n="home_hero_subtitle" style="text-align: center;">
                Nền tảng LMS tích hợp AI Tutor giúp bạn học tập tập trung,
                gia sư thông minh, hỗ trợ giảng viên thời gian thực và phát triển học thuật bền vững.
            </p>
            <div class="flex gap-6 fade-up justify-center">
                <a href="javascript:void(0)" onclick="App.checkAuthAndGo('/student/courses')" class="btn btn-primary" id="hero-primary-btn" data-i18n="home_btn_start"
                    style="border-radius:100px; padding: 1.15rem 3.2rem; font-weight:600; font-size: 0.95rem; letter-spacing: 0.02em;">Bắt
                    đầu ngay</a>
                <a href="#courses" class="btn btn-outline" data-i18n="home_btn_explore"
                    style="border-radius:100px; padding: 1.15rem 3.2rem; font-weight:500; font-size: 0.9rem; letter-spacing: 0.02em;">Khám
                    phá</a>
            </div>
        </div>
    </section>

    <!-- ── SECTION 2: AI LEARNING MANIFESTO ── -->
    <section class="cinematic-section" style="background: rgba(99, 102, 241, 0.03);">
        <div class="container">
            <div class="text-center flex flex-column items-center">
                <p class="editorial-label fade-up" data-i18n="home_manifesto_label">Tầm nhìn của chúng tôi</p>
                <h2 class="editorial-title fade-up" data-i18n="home_manifesto_title"
                    style="font-size: clamp(2.2rem, 5vw, 4.5rem); text-align: center;">
                    Chúng tôi tin rằng giáo dục cần <br>
                    <em>chiều sâu, sự tập trung, trí tuệ,</em> <br>
                    và tính nhân văn sâu sắc.
                </h2>
            </div>
        </div>
    </section>

    <!-- ── SECTION 3: FEATURED COURSES ── -->
    <section id="courses" class="cinematic-section">
        <div class="container text-center">
            <span class="editorial-label fade-up" data-i18n="home_courses_label">Chương trình đào tạo</span>
            <h2 class="editorial-title fade-up" data-i18n="home_courses_title" style="text-align: center;">Khóa học
                <em>Nổi bật</em></h2>

            <div class="course-showcase-grid" id="course-list">
                <div class="text-center"
                    style="grid-column: 1 / -1; padding: 4rem; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.06); border-radius: 24px;">
                    <p class="text-secondary" data-i18n="home_loading">Đang kết nối dữ liệu...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SECTION 4: AI TUTOR EXPERIENCE ── -->
    <section id="ai-tutor" class="cinematic-section" style="background: rgba(15, 23, 42, 0.4);">
        <div class="container asymmetric-grid">
            <div class="col-left-6 fade-up">
                <span class="editorial-label" data-i18n="home_ai_label">Trợ lý học tập thông minh</span>
                <h2 class="editorial-title" data-i18n="home_ai_title">Gia sư AI <em>Hiểu ngữ cảnh</em> <br>của riêng
                    bạn.</h2>
                <p class="editorial-text" data-i18n="home_ai_text">
                    Đặt câu hỏi bất cứ lúc nào. AI của chúng tôi không chỉ trả lời; nó hiểu nội dung bài học bạn đang
                    học,
                    giải thích các đoạn code phức tạp và hỗ trợ học thuật cá nhân hóa 24/7.
                </p>
                <div class="mt-8">
                    <a href="/ai-chat" class="btn btn-outline"
                        style="border-radius: 100px; padding: 0.9rem 2.5rem; font-weight: 500; border-color: rgba(255,255,255,0.12);"
                        data-i18n="nav_student_ai">Gia sư AI</a>
                </div>
            </div>
            <div class="col-right-5 fade-up">
                <div
                    style="width: 100%; height: 380px; background: url('https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=1000&auto=format&fit=crop') center; background-size: cover; border-radius: 32px; box-shadow: 0 24px 64px rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.06);">
                </div>
            </div>
        </div>
    </section>

    <!-- ── SECTION 5: STUDENT EXPERIENCE ── -->
    <section class="cinematic-section">
        <div class="container">
            <div class="asymmetric-grid">
                <div class="col-left-6 order-2 lg:order-1 fade-up" style="position: relative;">
                    <div id="stats-container" class="liquid-glass p-8"
                        style="border-radius: 28px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex justify-between mb-4">
                            <span class="text-xs opacity-40 uppercase" data-i18n="home_student_progress"
                                style="letter-spacing: 0.1em;">TIẾN ĐỘ HỌC TẬP</span>
                            <span class="text-xs font-bold" style="color: var(--accent);"><span
                                    data-i18n="home_student_complete">HOÀN THÀNH</span> <span id="stats-progress-percent">0%</span></span>
                        </div>
                        <div class="progress-bar-track mb-6" style="background: rgba(255,255,255,0.05); height: 6px;">
                            <div id="stats-progress-bar" class="progress-bar-fill"
                                style="width: 0%; background: var(--accent); box-shadow: 0 0 15px rgba(168, 85, 247, 0.4);">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1 p-5 rounded-2xl text-center"
                                style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                <span id="stats-streak" class="block text-3xl font-bold" style="color: var(--text-primary);">0</span>
                                <span class="text-xxs opacity-40 uppercase" data-i18n="home_student_streak">Ngày liên
                                    tiếp</span>
                            </div>
                            <div class="flex-1 p-5 rounded-2xl text-center"
                                style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                <span id="stats-certs" class="block text-3xl font-bold" style="color: var(--text-primary);">0</span>
                                <span class="text-xxs opacity-40 uppercase" data-i18n="home_student_certs">Chứng
                                    chỉ</span>
                            </div>
                        </div>
                    </div>
                    <div id="guest-stats-overlay" class="guest-tip-overlay"
                        style="display:none; background: rgba(11, 15, 26, 0.6); backdrop-filter: blur(8px); border-radius: 28px;">
                        <a href="/register" class="btn btn-primary btn-sm" style="border-radius:100px;"
                            data-i18n="home_student_guest_tip">Đăng ký để theo dõi tiến độ</a>
                    </div>
                </div>
                <div class="col-right-5 order-1 lg:order-2 fade-up">
                    <span class="editorial-label" data-i18n="home_student_label">Trải nghiệm học viên</span>
                    <h2 class="editorial-title" data-i18n="home_student_title">Làm chủ kiến thức <em>là một hành
                            trình.</em></h2>
                    <p class="editorial-text" data-i18n="home_student_text" style="color: rgba(240, 240, 244, 0.7);">
                        Theo dõi sự phát triển của bạn với các phân tích trực quan. Mỗi bài học hoàn thành là một bước
                        tiến gần hơn đến mục tiêu của bạn.
                        Duy trì động lực với chuỗi ngày học tập và tiến độ thời gian thực.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SECTION 6: CERTIFICATE SHOWCASE ── -->
    <section id="certificates" class="cinematic-section" style="background: rgba(15, 23, 42, 0.3);">
        <div class="container text-center">
            <span class="editorial-label fade-up" data-i18n="home_cert_label">Công nhận thành tích</span>
            <h2 class="editorial-title fade-up" data-i18n="home_cert_title">Hành trình học tập của bạn <br>xứng đáng
                được <em>ghi nhận.</em></h2>
            <div class="flex justify-center mt-12 fade-up" style="position: relative;">
                <!-- REDESIGNED CERTIFICATE (DARK) -->
                <div id="cert-showcase" class="liquid-glass p-0"
                    style="width: 100%; max-width: 800px; aspect-ratio: 1.414 / 1; border-radius: 12px; border: 12px solid rgba(255,255,255,0.05); background: #0f172a; box-shadow: 0 40px 100px rgba(0,0,0,0.5); overflow: hidden; position: relative;">
                    <div style="position: absolute; inset: 0; border: 2px solid rgba(99, 102, 241, 0.2); margin: 20px;">
                    </div>
                    
                    <!-- Earned Certificate State (Default hidden) -->
                    <div id="cert-earned-content" style="display:none; padding: 4rem; text-align: center; height: 100%; flex-direction: column; justify-content: center;">
                        <div style="font-size: 3.5rem; margin-bottom: 2rem; filter: drop-shadow(0 0 15px rgba(168, 85, 247, 0.4));">🎓</div>
                        <h4 style="font-family: var(--font-heading); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 1rem; opacity: 0.6; color: var(--accent);" data-i18n="cert_title_card">Certificate of Completion</h4>
                        <h3 class="editorial-title" id="cert-user-name" style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #fff;">Lê Diên Hiếu</h3>
                        <p style="font-size: 1rem; opacity: 0.5; margin-bottom: 1.5rem; color: rgba(255,255,255,0.7);" data-i18n="cert_subtitle">has successfully mastered the curriculum of</p>
                        <h3 id="cert-course-title" style="font-family: var(--font-heading); font-size: 2.2rem; color: var(--accent); font-weight: 800; line-height: 1.2;">Mastering Cinematic UI Design</h3>
                        
                        <div id="cert-multi-indicator" style="display:none; margin-top: 2rem;">
                            <a href="/student/certificates" class="btn btn-outline" style="border-radius: 100px; font-size: 0.8rem; padding: 0.5rem 1.5rem; border-color: rgba(255,255,255,0.1);">
                                <span data-i18n="cert_view_more">Xem các chứng chỉ khác</span> (<span id="cert-remaining-count">0</span>)
                            </a>
                        </div>

                        <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; padding: 0 2rem;">
                            <div style="text-align: left;">
                                <div style="width: 140px; border-bottom: 2px solid rgba(255,255,255,0.2); margin-bottom: 0.75rem;"></div>
                                <span style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.4; letter-spacing: 0.1em;" data-i18n="cert_director">Program Director</span>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #fff; letter-spacing: 0.05em;">AI STUDY HUB®</div>
                                <span style="font-size: 0.75rem; text-transform: uppercase; opacity: 0.4; letter-spacing: 0.1em;" data-i18n="cert_official">Official Academic Record</span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty/Motivation State -->
                    <div id="cert-empty-content" style="padding: 4rem; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <div style="font-size: 4rem; margin-bottom: 2rem; opacity: 0.2;">📜</div>
                        <h3 style="font-family: var(--font-heading); font-size: 2rem; color: #fff; margin-bottom: 1rem;" data-i18n="home_cert_empty_title">Chứng chỉ đầu tiên đang chờ bạn</h3>
                        <p style="max-width: 400px; color: rgba(255,255,255,0.5); line-height: 1.6; margin-bottom: 2.5rem;" data-i18n="home_cert_empty_text">Hoàn thành 100% khóa học để ghi nhận thành tựu và nhận chứng chỉ chính thức từ AI Study Hub.</p>
                        <a href="/student/courses" class="btn btn-primary" style="border-radius: 100px; padding: 0.8rem 2.5rem;" data-i18n="nav_my_courses">Vào khóa học của tôi</a>
                    </div>
                </div>
                <div id="guest-cert-overlay" class="guest-tip-overlay"
                    style="display:none; background: rgba(11, 15, 26, 0.7); backdrop-filter: blur(10px);">
                    <div class="flex flex-column items-center gap-4">
                        <div
                            style="background: rgba(255,255,255,0.05); padding: 1.5rem 2.5rem; border-radius: 100px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);">
                            <span data-i18n="home_cert_guest_tip"
                                style="font-weight: 700; color: #fff; letter-spacing: 0.02em;">Hoàn thành khóa học để
                                nhận chứng chỉ</span>
                        </div>
                        <a href="/login" class="btn btn-primary"
                            style="border-radius:100px; padding: 0.8rem 2.5rem;">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SECTION 7: FINAL CTA ── -->
    <section class="cinematic-section text-center"
        style="padding: 14rem 0; background: linear-gradient(to bottom, transparent, rgba(99, 102, 241, 0.03));">
        <div class="container flex flex-column items-center">
            <h2 class="editorial-title fade-up" data-i18n="home_cta_title" style="text-align: center;">Bắt đầu hành
                trình học tập <br>thông minh <em>ngay hôm nay.</em></h2>
            <div class="mt-12 fade-up">
                <a href="/register" class="btn btn-primary" id="final-cta-btn" data-i18n="home_cta_btn"
                    style="border-radius:100px; padding: 1.2rem 4rem; font-size: 1rem; font-weight: 600; letter-spacing: 0.01em;">Tham
                    gia miễn phí</a>
            </div>
        </div>
    </section>

</main> <!-- End home-page main wrapper -->

<?php ob_start(); ?>
<script>
    const observerOptions = { threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, observerOptions);

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    window.enrollAndLearn = async function (courseId) {
        try { await window.api.post(`/courses/${courseId}/enroll`, {}); } catch (e) { }
        window.location.href = `/student/learning/${courseId}`;
    };

    document.addEventListener('DOMContentLoaded', async () => {
        const token = window.api.getToken();
        const user = window.api.getUser();

        // Tự động chuyển hướng về Dashboard tương ứng nếu đã đăng nhập
        if (token && user) {
            let dashboardUrl = '/student/dashboard';
            if (user.role === 'teacher') dashboardUrl = '/teacher/dashboard';
            else if (user.role === 'admin') dashboardUrl = '/admin/dashboard';
            
            window.location.href = dashboardUrl;
            return;
        }

        const isGuest = !token;
        const heroBtn = document.getElementById('hero-primary-btn');
        const finalBtn = document.getElementById('final-cta-btn');

        // 1. Handle Auth UI State
        if (isGuest) {
            document.getElementById('stats-container').classList.add('guest-mask');
            document.getElementById('guest-stats-overlay').style.display = 'flex';
        } else {
            const user = window.api.getUser();
            let dashboardUrl = '/student/dashboard';
            let dashboardTextKey = 'nav_student_dashboard';

            if (user.role === 'teacher') {
                dashboardUrl = '/teacher/dashboard';
                dashboardTextKey = 'tc_dash_title';
            } else if (user.role === 'admin') {
                dashboardUrl = '/admin/dashboard';
                dashboardTextKey = 'nav_admin_dashboard';
            }

            const dashboardText = (window.I18n && I18n.get(dashboardTextKey)) || 'Vào bảng điều khiển';
            
            if (heroBtn) {
                heroBtn.href = dashboardUrl;
                heroBtn.textContent = dashboardText;
                heroBtn.removeAttribute('data-i18n');
            }
            if (finalBtn) {
                finalBtn.href = dashboardUrl;
                finalBtn.textContent = dashboardText;
                finalBtn.removeAttribute('data-i18n');
            }

            // Fetch real progress data
            try {
                const statsRes = await window.api.get('/student/stats');
                if (statsRes && statsRes.data) {
                    const stats = statsRes.data;
                    const percent = stats.completion_rate || 0;
                    document.getElementById('stats-progress-percent').textContent = percent + '%';
                    document.getElementById('stats-progress-bar').style.width = percent + '%';
                    document.getElementById('stats-streak').textContent = stats.streak || 0;
                    document.getElementById('stats-certs').textContent = stats.certificates_count || 0;
                }

                // Update Certificate Info
                document.getElementById('cert-user-name').textContent = user.username || user.email.split('@')[0];
                
                // Check real certificates
                const certRes = await window.api.get('/certificates/my');
                const earnedContent = document.getElementById('cert-earned-content');
                const emptyContent = document.getElementById('cert-empty-content');
                
                if (certRes && certRes.data && certRes.data.length > 0) {
                    const latestCert = certRes.data[0];
                    document.getElementById('cert-course-title').textContent = latestCert.course_title;
                    earnedContent.style.display = 'flex';
                    emptyContent.style.display = 'none';

                    // If multiple certs, show indicator
                    if (certRes.data.length > 1) {
                        document.getElementById('cert-multi-indicator').style.display = 'block';
                        document.getElementById('cert-remaining-count').textContent = certRes.data.length - 1;
                    }
                } else {
                    earnedContent.style.display = 'none';
                    emptyContent.style.display = 'flex';
                }
            } catch (err) { console.error('Stats/Cert Loading Error:', err); }
        }

        // 2. Fetch and Render Courses
        try {
            let enrolledIds = [];
            if (!isGuest) {
                try {
                    const enrolledRes = await window.api.get('/student/courses');
                    if (enrolledRes && enrolledRes.data) {
                        enrolledIds = enrolledRes.data.map(c => c.id);
                    }
                } catch (e) { }
            }

            function renderCourses(courses) {
                const el = document.getElementById('course-list');
                if (!courses || !courses.length) {
                    el.innerHTML = `<p class="text-muted" style="grid-column: 1 / -1; text-align:center;" data-i18n="home_no_courses">Chưa có khóa học nào khả dụng.</p>`;
                    return;
                }

                el.innerHTML = courses.slice(0, 4).map((c, index) => {
                    const enrolled = enrolledIds.includes(c.id);
                    const isPremium = c.is_premium == 1 || c.price > 0;
                    const isLarge = index % 3 === 0;

                    let btn = '';
                    if (enrolled) {
                        btn = `<button onclick="window.enrollAndLearn(${c.id})" class="btn btn-outline" style="width:100%; border-radius:100px; font-weight:500; font-size: 0.85rem;" data-i18n="std_btn_continue">Tiếp tục học</button>`;
                    } else if (isPremium) {
                        const link = !isGuest ? `/student/payment/${c.id}` : '/login';
                        btn = `<button onclick="window.location.href='${link}'" class="btn btn-primary" style="width:100%; border-radius:100px; font-weight:600; font-size: 0.85rem;" data-i18n="cd_btn_buy">🎓 Ghi danh khóa học</button>`;
                    } else {
                        btn = !isGuest
                            ? `<button id="btn-enroll-${c.id}" onclick="window.enrollAndLearn(${c.id})" class="btn btn-primary" style="width:100%; border-radius:100px; font-weight:600; font-size: 0.85rem;" data-i18n="std_btn_free">Ghi danh miễn phí</button>`
                            : `<button onclick="window.location.href='/login'" class="btn btn-outline" style="width:100%; border-radius:100px; font-weight:500; font-size: 0.85rem;" data-i18n="home_btn_login_learn">Đăng nhập để học</button>`;
                    }

                    const price = c.price > 0
                        ? `<span style="font-weight:800; font-size:1.1rem; color: #fff;">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(c.price)}</span>`
                        : `<span class="badge badge-free" data-i18n="home_free">Miễn phí</span>`;

                    return `
                    <div class="${isLarge ? 'course-card-large' : 'course-card-small'} fade-up">
                        <div class="card" style="display:flex; flex-direction:column; border-radius: 24px; overflow: hidden; transition: all 0.6s cubic-bezier(0.16,1,0.3,1);">
                            <div style="cursor:pointer; position:relative; aspect-ratio: 16/9; background: rgba(255,255,255,0.02); overflow: hidden;" onclick="window.location.href='/course/${c.id}'">
                                ${c.thumbnail ? `<img src="${c.thumbnail}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition: transform 0.7s cubic-bezier(0.16,1,0.3,1);" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">` : '<span style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:2.5rem; opacity:0.15;">📚</span>'}
                                ${isPremium ? '<span class="badge badge-premium" style="position:absolute; top:20px; right:20px; z-index:2; padding: 0.4rem 1rem; border-radius: 100px; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em;">PREMIUM</span>' : ''}
                            </div>
                            <div class="card-body" style="flex:1; display:flex; flex-direction:column; padding: 2rem 2.5rem 2.5rem;">
                                <h3 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 600; letter-spacing: -0.02em; margin-bottom: 0.75rem; cursor:pointer; line-height:1.2; color: #fff;" onclick="window.location.href='/course/${c.id}'">${c.title}</h3>
                                <p style="font-size: 0.85rem; color: rgba(248,250,252,0.35); line-height:1.7; margin-bottom: 1.5rem;">${c.description ? c.description.substring(0, 120) + '...' : 'Chưa có mô tả cho khóa học này.'}</p>
                                <div style="margin-top: auto;">
                                    <div style="margin-bottom: 1.25rem;">${price}</div>
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    </div>`;
                }).join('');

                if (window.I18n) window.I18n.render();
                document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
            }

            const courseRes = await window.api.get('/courses');
            const courses = courseRes.data?.items ?? courseRes.data ?? courseRes;
            renderCourses(Array.isArray(courses) ? courses : []);

        } catch (e) {
            console.error('Home Data Loading Error:', e);
            document.getElementById('course-list').innerHTML = `<p class="text-muted" style="grid-column: 1 / -1; text-align:center;">Lỗi kết nối dữ liệu. Vui lòng thử lại sau.</p>`;
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>