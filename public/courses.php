<?php
$pageTitle = 'Thư viện Khóa học — AI Study Hub®';
$actor     = 'guest';
$extraHead = '<link rel="stylesheet" href="/assets/css/pages/home.css?v=6">';
require __DIR__ . '/layouts/header.php';
?>

<style>
    .search-container {
        position: relative;
        max-width: 600px;
        margin: 3rem auto 0;
    }
    .search-input {
        width: 100%;
        padding: 1.25rem 2rem 1.25rem 3.5rem;
        border-radius: 100px;
        border: 1px solid rgba(0,0,0,0.08);
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .search-input:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255,255,255,0.1);
        box-shadow: 0 15px 40px rgba(99, 102, 241, 0.1);
    }
    .search-icon {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.3;
    }
</style>

<div class="cinematic-bg">
    <video autoplay muted loop playsinline class="cinematic-video">
        <source src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4" type="video/mp4">
    </video>
    <div class="cinematic-overlay"></div>
</div>

<section class="cinematic-section container" style="padding-top: 10rem;">
    <div class="text-center">
        <span class="editorial-label fade-up" data-i18n="nav_courses">Khóa học</span>
        <h1 class="editorial-title fade-up" data-i18n="courses_title">Thư viện Khóa học</h1>
        <p class="editorial-text mx-auto fade-up" data-i18n="courses_subtitle">Khám phá kho tàng kiến thức AI chuyên sâu.</p>
        
        <div class="search-container fade-up">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="course-search" class="search-input" data-i18n="courses_search_placeholder" placeholder="Tìm kiếm khóa học...">
        </div>
    </div>
</section>

<section class="cinematic-section container" style="padding-top: 2rem;">
    <div class="course-gallery-grid" id="all-courses-list" style="margin-top: 0;">
        <!-- Loading state -->
        <div class="card p-12 text-center" style="grid-column: 1 / -1; background: rgba(0,0,0,0.02); border: 1px dashed rgba(0,0,0,0.1); border-radius: 32px;">
            <p class="text-muted" data-i18n="home_loading">Đang kết nối dữ liệu...</p>
        </div>
    </div>
</section>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        let allCourses = [];
        let enrolledIds = [];
        const isGuest = !window.api.getToken();

        try {
            if (!isGuest) {
                const er = await window.api.get('/student/courses');
                if (er && er.data) enrolledIds = er.data.map(c => c.id);
            }

            const res = await window.api.get('/courses');
            allCourses = res.data?.items ?? res.data ?? [];
            if (!Array.isArray(allCourses)) allCourses = [];

            renderCourses(allCourses);

            // Search logic
            document.getElementById('course-search').addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const filtered = allCourses.filter(c => 
                    c.title.toLowerCase().includes(query) || 
                    (c.description && c.description.toLowerCase().includes(query))
                );
                renderCourses(filtered);
            });

        } catch(e) {
            console.error('Course Page Error:', e);
            document.getElementById('all-courses-list').innerHTML = `<p class="text-muted" style="grid-column: 1 / -1; text-align:center;">Lỗi kết nối dữ liệu.</p>`;
        }

        function renderCourses(courses) {
            const el = document.getElementById('all-courses-list');
            if (!courses.length) {
                el.innerHTML = `<p class="text-muted" style="grid-column: 1 / -1; text-align:center; padding: 4rem;" data-i18n="courses_no_results">${I18n.get('courses_no_results')}</p>`;
                return;
            }

            el.innerHTML = courses.map((c) => {
                const enrolled  = enrolledIds.includes(c.id);
                const isPremium = c.is_premium == 1 || c.price > 0;
                
                let btn = '';
                if (enrolled) {
                    btn = `<button onclick="window.location.href='/student/learning/${c.id}'" class="btn btn-outline" style="width:100%; border-radius:100px;" data-i18n="std_btn_continue">${I18n.get('std_btn_continue')}</button>`;
                } else if (isPremium) {
                    const link = !isGuest ? `/student/payment/${c.id}` : '/login';
                    btn = `<button onclick="window.location.href='${link}'" class="btn btn-primary" style="width:100%; border-radius:100px;" data-i18n="home_btn_buy">${I18n.get('home_btn_buy')}</button>`;
                } else {
                    btn = !isGuest
                        ? `<button onclick="window.api.post('/courses/${c.id}/enroll', {}).then(()=>window.location.href='/student/learning/${c.id}')" class="btn btn-primary" style="width:100%; border-radius:100px;" data-i18n="std_btn_free">${I18n.get('std_btn_free')}</button>`
                        : `<button onclick="window.location.href='/login'" class="btn btn-outline" style="width:100%; border-radius:100px;" data-i18n="home_btn_login_learn">${I18n.get('home_btn_login_learn')}</button>`;
                }

                const price = c.price > 0
                    ? `<span style="font-weight:700; color: var(--text-primary);">${new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(c.price)}</span>`
                    : `<span class="badge badge-free" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);" data-i18n="home_free">${I18n.get('home_free')}</span>`;

                return `
                <div class="course-card-grid-item">
                    <div class="card liquid-glass" style="display:flex; flex-direction:column; border-radius: 24px; overflow: hidden; height: 100%; transition: all 0.6s cubic-bezier(0.16,1,0.3,1);">
                        <div class="card-img-placeholder" style="cursor:pointer; position:relative; aspect-ratio: 16 / 9; background: rgba(255,255,255,0.02);" onclick="window.location.href='/course/${c.id}'">
                            ${c.thumbnail ? `<img src="${c.thumbnail}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">` : '<span style="position:relative; z-index:1; font-size:2rem;">📚</span>'}
                            ${isPremium ? '<span class="badge badge-premium" style="position:absolute; top:15px; right:15px; z-index:2; background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); font-size: 0.6rem;">PREMIUM</span>' : ''}
                        </div>
                        <div class="card-body" style="flex:1; display:flex; flex-direction:column; padding: 1.5rem;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; cursor:pointer;" onclick="window.location.href='/course/${c.id}'">${c.title}</h3>
                            <p class="text-xs opacity-60 mb-6" style="line-height: 1.6; flex:1;">${c.description ? c.description.substring(0, 80)+'...' : I18n.get('std_no_desc')}</p>
                            <div class="flex justify-between items-center mb-6">${price.replace('color: var(--text-primary)', 'color: #fff')}</div>
                            ${btn}
                        </div>
                    </div>
                </div>`;
            }).join('');

            if (window.I18n) window.I18n.render();
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>
