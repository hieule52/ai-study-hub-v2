<?php
$pageTitle = 'AI Study Hub - Learning Management System';
$actor = 'guest';
require __DIR__ . '/layouts/header.php';
?>

<!-- Hero Section -->
<section class="container hero-section">
    <div class="hero-bg-glow"></div>
    <h1 class="hero-title" data-i18n="home_hero_title">Đánh Thức Tiềm Năng Học Tập Của Bạn Với <br><span
            class="text-gradient">Trợ Lý AI Thông
            Minh</span></h1>
    <p class="hero-subtitle" data-i18n="home_hero_subtitle">
        Học tập không giới hạn cùng nền tảng LMS cao cấp kết hợp trò chuyện thông minh qua AI. Giải phóng 100% năng
        suất.
    </p>

    <div class="flex justify-center gap-4">
        <a href="/register.php" class="btn btn-primary" id="btn-start-learning" data-i18n="home_btn_start">Bắt Đầu Học
            Ngay</a>
        <a href="#courses" class="btn btn-outline" data-i18n="home_btn_explore">Khám Phá Khóa Học</a>
    </div>
</section>

<!-- Popular Courses -->
<section id="courses" class="container" style="padding-bottom: 6rem;">
    <div class="flex items-center justify-between mb-8">
        <h2 data-i18n="home_popular_courses">Khóa Học <span class="text-gradient">Nổi Bật</span></h2>
        <div style="width: 300px;">
            <input type="text" id="search-course-input" class="form-control" placeholder="🔍 Tìm khóa học..." style="border-radius: 50px;"
                data-i18n="home_search_placeholder">
        </div>
    </div>

    <!-- Render list by JS -->
    <div class="grid-cols-3" id="course-list">
        <div class="card p-4 text-center">
            <p data-i18n="home_loading">Đang tải dữ liệu...</p>
        </div>
    </div>
</section>

<?php ob_start(); ?>
<script>
    // Function to enroll and redirect
    window.enrollAndLearn = async function (courseId) {
        try {
            // Ignore errors like "already enrolled"
            await window.api.post(`/courses/${courseId}/enroll`, {});
        } catch (e) {
            console.log(e);
        }
        window.location.href = `/student/learning.php?course_id=${courseId}`;
    };

    // Fetch courses for guest
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            let enrolledCourseIds = [];
            // Nếu User đã đăng nhập, gọi thêm API lấy danh sách khóa học của họ
            if (window.api.getToken()) {
                const user = window.api.getUser();
                if (user) {
                    if (user.role === 'admin') {
                        window.location.href = '/admin/dashboard.php';
                        return;
                    }
                    if (user.role === 'teacher') {
                        window.location.href = '/teacher/dashboard.php';
                        return;
                    }
                }

                const btnStart = document.getElementById('btn-start-learning');
                if (btnStart) btnStart.style.display = 'none';

                try {
                    const enrolledRes = await window.api.get('/student/courses');
                    enrolledCourseIds = enrolledRes.data.map(c => c.id);
                } catch (e) {
                    console.warn("Không lấy được tiến độ hoặc chưa có đăng ký.");
                }
            }

            function renderCourses(courses) {
                const container = document.getElementById('course-list');
                if (!courses || courses.length === 0) {
                    container.innerHTML = '<p class="text-muted" style="grid-column: span 3; text-align: center;" data-i18n="home_no_courses">Không tìm thấy khóa học nào phù hợp.</p>';
                    if (window.I18n) window.I18n.render();
                    return;
                }

                container.innerHTML = courses.map(c => {
                    let buttonHtml = '';
                    const isEnrolled = enrolledCourseIds.includes(c.id);

                    if (isEnrolled) {
                        buttonHtml = `<button onclick="window.enrollAndLearn(${c.id})" class="btn btn-primary" style="padding: 0.5rem 1rem; cursor: pointer; border: none;" data-i18n="home_btn_join">Tham gia học</button>`;
                    } else if (c.is_premium == 1 || c.price > 0) {
                        if (window.api.getToken()) {
                            buttonHtml = `<button onclick="window.location.href='/student/course-payment.php?course_id=${c.id}&price=${c.price}'" class="btn" style="background: var(--warning); color: #000; font-weight: bold; padding: 0.5rem 1rem; cursor: pointer; border: none;" data-i18n="home_btn_buy">Mua khóa học</button>`;
                        } else {
                            buttonHtml = `<button onclick="window.location.href='/login.php'" class="btn" style="background: var(--warning); color: #000; font-weight: bold; padding: 0.5rem 1rem; cursor: pointer; border: none;" data-i18n="home_btn_buy">Mua khóa học</button>`;
                        }
                    } else {
                        if (window.api.getToken()) {
                            buttonHtml = `<button onclick="window.enrollAndLearn(${c.id})" class="btn btn-primary" style="padding: 0.5rem 1rem; cursor: pointer; border: none;" data-i18n="home_btn_join">Tham gia học</button>`;
                        } else {
                            buttonHtml = `<button onclick="window.location.href='/login.php'" class="btn btn-primary" style="padding: 0.5rem 1rem; cursor: pointer; border: none;" data-i18n="home_btn_login_learn">Đăng nhập để Học</button>`;
                        }
                    }

                    return `
                        <div class="card glass-panel" style="backdrop-filter: blur(4px);">
                            <div class="card-img-placeholder" style="position: relative; cursor: pointer; padding: 0; overflow: hidden;" onclick="window.location.href='/course-detail.php?id=${c.id}'">
                                ${c.thumbnail ? `<img src="${c.thumbnail}" style="width: 100%; height: 100%; object-fit: cover;">` : '<span style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; font-size: 3rem;">📚</span>'}
                                ${(c.is_premium == 1 || c.price > 0) ? '<span style="position: absolute; top: 10px; right: 10px; background: var(--warning); color: #000; font-size: 0.7rem; font-weight: bold; padding: 2px 8px; border-radius: 10px; z-index: 2;">PREMIUM</span>' : ''}
                            </div>
                            <div class="card-body">
                                <h3 class="card-title" style="cursor: pointer;" onclick="window.location.href='/course-detail.php?id=${c.id}'">${c.title}</h3>
                                <p class="text-secondary" style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    ${c.description ? c.description.substring(0, 100) + '...' : 'Chưa có mô tả'}
                                </p>
                                
                                <div class="flex justify-between items-center mt-4">
                                    <div>
                                        <span style="color: var(--warning); font-weight: bold; font-size: 1.25rem;">
                                            ${c.price > 0 ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(c.price) : '<span data-i18n="home_free">Miễn phí</span>'}
                                        </span>
                                    </div>
                                    ${buttonHtml}
                                </div>
                            </div>
                        </div>`;
                }).join('');

                if (window.I18n) window.I18n.render();
            }

            // Initial load
            const res = await window.api.get('/courses');
            const initialCourses = res.data.items ? res.data.items : (res.data || []);
            renderCourses(initialCourses);

            // Search functionality
            const searchInput = document.getElementById('search-course-input');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const keyword = e.target.value.trim();
                    if (searchTimeout) clearTimeout(searchTimeout);

                    searchTimeout = setTimeout(async () => {
                        try {
                            let url = '/courses';
                            if (keyword) {
                                url = `/courses/search?q=${encodeURIComponent(keyword)}`;
                            }
                            const searchRes = await window.api.get(url);
                            const searchCourses = searchRes.data.items ? searchRes.data.items : (searchRes.data || []);
                            renderCourses(searchCourses);
                        } catch (err) {
                            console.error("Lỗi tìm kiếm khóa học", err);
                        }
                    }, 500); // Debounce 500ms
                });
            }

        } catch (e) {
            document.getElementById('course-list').innerHTML = '<p class="text-danger" style="grid-column: span 3; text-align: center;">Lỗi kết nối cơ sở dữ liệu khóa học.</p>';
            console.error(e);
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>