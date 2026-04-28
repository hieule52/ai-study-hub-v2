<?php
$pageTitle = 'Góc Học Tập - Dashboard';
$actor = 'student';
ob_start();
?>
<!-- Chart.js for stats -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            background: var(--bg-surface-glass);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            margin-bottom: 2rem;
        }

        /* Scroller for enrolled courses */
        .scroller-container {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            scroll-snap-type: x mandatory;
        }

        .scroller-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroller-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
    </style>

<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2.5rem; letter-spacing: -1px;"><span data-i18n="std_welcome">Chào mừng quay lại, </span><span id="student-name"
                            class="text-gradient">...</span> 👋</h1>
                    <p class="text-secondary mt-2 text-lg" data-i18n="std_subtitle">Hôm nay bạn muốn học thêm điều gì mới?</p>
                </div>
            </div>

            <!-- Stats & Chart Area -->
            <div class="grid-cols-3 mb-8">
                <div class="card p-4 glass-panel"
                    style="display: flex; flex-direction: column; justify-content: center;">
                    <p class="text-secondary font-bold"
                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 1px;">Đang học</p>
                    <h2 style="font-size: 2.5rem; color: var(--text-primary); margin-top: 0.5rem;" id="stat_learning">0
                        <span style="font-size: 1.2rem; font-weight: normal; color: var(--text-muted);">khóa</span></h2>
                </div>
                <div class="card p-4 glass-panel"
                    style="display: flex; flex-direction: column; justify-content: center;">
                    <p class="text-secondary font-bold"
                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 1px;">Hoàn thành</p>
                    <h2 style="font-size: 2.5rem; color: var(--success); margin-top: 0.5rem;" id="stat_completed">0
                        <span style="font-size: 1.2rem; font-weight: normal; color: var(--text-muted);">khóa</span></h2>
                </div>
                <div class="card p-4 glass-panel" style="position: relative; overflow: hidden;">
                    <div
                        style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: var(--warning); filter: blur(50px); opacity: 0.3; border-radius: 50%;">
                    </div>
                    <p class="text-secondary font-bold"
                        style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 1px;">Chứng chỉ</p>
                    <h2 style="font-size: 2.5rem; color: var(--warning); margin-top: 0.5rem;" id="stat_certs">0</h2>
                </div>
            </div>

            <!-- Chart.js integration -->
            <div class="chart-container mb-8">
                <h3 class="mb-4" data-i18n="std_chart_title">Biểu đồ tiến độ học tập trong tuần</h3>
                <canvas id="learningChart" height="80"></canvas>
            </div>

            <!-- My Courses (Enrolled) -->
            <h2 class="mb-4 text-xl" data-i18n="std_my_courses">🚀 Khóa Học Của Bạn</h2>
            <div class="scroller-container mb-8" id="enrolled-course-container">
                <div class="text-secondary col-span-2" data-i18n="home_loading">Đang tải khóa học...</div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.05); margin: 3rem 0;">

            <!-- All Courses Advertisement (Like Guest Page) -->
            <div class="flex items-center justify-between mb-6">
                <h2 style="font-size: 2rem;" data-i18n="std_explore_new">Khám Phá <span class="text-gradient">Khóa Học Mới</span> 🌟</h2>
                <div style="width: 250px;">
                    <input type="text" class="form-control" placeholder="🔍 Tìm khóa học..."
                        style="border-radius: 20px;" data-i18n="home_search_placeholder">
                </div>
            </div>

            <!-- Render all courses list here -->
            <div class="grid-cols-3" id="all-course-list">
                <div class="card p-4 text-center col-span-3">
                    <p data-i18n="home_loading">Đang lấy dữ liệu khóa học mới...</p>
                </div>
            </div>

<?php ob_start(); ?>
<script>
        // Global variables for enrolled logic
        let enrolledCourseIds = [];

        window.enrollAndLearn = async function (courseId) {
            try {
                const btn = document.getElementById(`btn-enroll-${courseId}`);
                if (btn) { btn.innerHTML = 'Đang xử lý...'; btn.disabled = true; }
                await window.api.post(`/courses/${courseId}/enroll`, {});
            } catch (e) {
                console.log("Error or already enrolled", e.message);
            }
            window.location.href = `/student/learning.php?course_id=${courseId}`;
        };

        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['student', 'admin', 'teacher']);
            if (!user) return;

            document.getElementById('student-name').innerText = user.username || user.email.split('@')[0];

            try {
                // 1. Load enrolled courses & stats
                const enrolledRes = await window.api.get('/student/courses');
                const enrolledCourses = enrolledRes.data || [];
                enrolledCourseIds = enrolledCourses.map(c => c.id);

                let completed = 0;
                let learning = 0;

                const enrolledContainer = document.getElementById('enrolled-course-container');
                enrolledContainer.innerHTML = '';

                if (enrolledCourses.length === 0) {
                    enrolledContainer.innerHTML = '<div class="card p-4 text-center text-secondary w-full" style="flex: 1;" data-i18n="std_no_enrolled">Bạn chưa tham gia khóa học nào. Hãy khám phá bên dưới nhé! 👇</div>';
                } else {
                    enrolledCourses.forEach(c => {
                        let prog = parseInt(c.progress_percent) || 0;
                        if (prog >= 100) completed++;
                        else learning++;

                        const div = document.createElement('div');
                        div.className = 'card glass-panel scroller-item';
                        div.style.cssText = 'padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; border-top: 3px solid var(--primary);';
                        const icon = c.thumbnail ? `<img src="${c.thumbnail}" style="border-radius:var(--radius-sm); height: 100px; width:100%; object-fit: cover;">` : `<div style="height: 100px; display:flex; align-items:center; justify-content:center; background: rgba(0,0,0,0.2); font-size: 2.5rem; border-radius: var(--radius-sm);">🚀</div>`;

                        div.innerHTML = `
                            ${icon}
                            <div>
                                <h4 style="margin-bottom: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${c.title}">${c.title}</h4>
                                <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-bottom: 0.5rem; overflow: hidden;">
                                    <div style="width: ${prog}%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 4px; box-shadow: 0 0 10px var(--primary);"></div>
                                </div>
                                <p class="text-secondary flex justify-between" style="font-size: 0.75rem;">
                                    <span data-i18n="std_progress">Tiến độ học</span> 
                                    <span style="color: ${prog >= 100 ? 'var(--success)' : 'var(--text-primary)'}; font-weight: bold;">${prog}%</span>
                                </p>
                                <a href="/student/learning.php?course_id=${c.id}" class="btn btn-primary mt-4" style="width: 100%; padding: 0.5rem 1rem;" data-i18n="${prog > 0 ? 'std_btn_continue' : 'std_btn_start'}">${prog > 0 ? 'Tiếp tục học' : 'Vào học ngay'}</a>
                            </div>
                        `;
                        enrolledContainer.appendChild(div);
                    });
                }

                document.getElementById('stat_learning').innerHTML = `${learning} <span style="font-size: 1.2rem; font-weight: normal; color: var(--text-muted);" data-i18n="std_courses_unit">khóa</span>`;
                document.getElementById('stat_completed').innerHTML = `${completed} <span style="font-size: 1.2rem; font-weight: normal; color: var(--text-muted);" data-i18n="std_courses_unit">khóa</span>`;
                document.getElementById('stat_certs').innerText = completed;

                // Thống kê thực tế học tập
                const statsRes = await window.api.get('/student/stats');
                const realStats = statsRes.data;
                const dateLabels = Object.keys(realStats).map(d => {
                    const parts = d.split('-');
                    return `${parts[2]}/${parts[1]}`; // Hiển thị DD/MM
                });
                const chartData = Object.values(realStats);

                // INIT CHART.JS
                const chartLabelText = window.I18n ? window.I18n.get('std_chart_label') : 'Bài học hoàn thành';
                const ctx = document.getElementById('learningChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dateLabels,
                        datasets: [{
                            label: chartLabelText,
                            data: chartData,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#ec4899',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#ec4899',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' ' + chartLabelText;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 5,
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#94a3b8', stepSize: 1 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8' }
                            }
                        }
                    }
                });


                // 2. Load ALL Courses to showcase (Like Guest Page)
                const allCoursesRes = await window.api.get('/courses');
                const courses = allCoursesRes.data;
                const container = document.getElementById('all-course-list');

                if (courses.length === 0) {
                    container.innerHTML = '<p class="text-muted col-span-3" data-i18n="home_no_courses">Chưa có khóa học nào trên hệ thống.</p>';
                    if (window.I18n) window.I18n.render();
                    return;
                }

                container.innerHTML = courses.map(c => {
                    const isEnrolled = enrolledCourseIds.includes(c.id);
                    let buttonHtml = '';

                    if (isEnrolled) {
                        buttonHtml = `<button onclick="window.location.href='/student/learning.php?course_id=${c.id}'" class="btn btn-outline" style="width: 100%; border-color: var(--success); color: var(--success); justify-content: center; pointer-events: none;" data-i18n="std_owned">Đã sở hữu ✅</button>`;
                    } else if (c.is_premium == 1 || c.price > 0) {
                        buttonHtml = `<button onclick="window.location.href='/student/course-payment.php?course_id=${c.id}&price=${c.price}'" class="btn" style="background: var(--warning); color: #000; font-weight: bold; width: 100%; justify-content: center; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);" data-i18n="std_btn_buy">💳 Mua khóa học</button>`;
                    } else {
                        buttonHtml = `<button id="btn-enroll-${c.id}" onclick="window.enrollAndLearn(${c.id})" class="btn btn-primary" style="width: 100%; justify-content: center;" data-i18n="std_btn_free">Đăng ký Miễn Phí</button>`;
                    }

                    return `
                    <div class="card glass-panel" style="display: flex; flex-direction: column;">
                        <div class="card-img-placeholder" style="position: relative; height: 160px; font-size: 3rem;">
                            📚
                            ${(c.is_premium == 1 || c.price > 0) ? '<span style="position: absolute; top: 15px; right: 15px; background: var(--warning); color: #000; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.5);">PREMIUM 💎</span>' : '<span style="position: absolute; top: 15px; right: 15px; background: var(--success); color: #fff; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px;" data-i18n="home_free">FREE</span>'}
                        </div>
                        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                            <h3 class="card-title" style="margin-bottom: 0.5rem;">${c.title}</h3>
                            <p class="text-secondary" style="font-size: 0.875rem; flex: 1; margin-bottom: 1.5rem;" ${!c.description ? 'data-i18n="std_no_desc"' : ''}>
                                ${c.description ? c.description.substring(0, 90) + '...' : 'Chưa có mô tả chi tiết từ giảng viên.'}
                            </p>
                            
                            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; margin-bottom: 1rem;">
                                <span style="color: var(--text-primary); font-weight: 800; font-size: 1.5rem;">
                                    ${c.price > 0 ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(c.price) : '0 ₫'}
                                </span>
                            </div>
                            ${buttonHtml}
                        </div>
                    </div>`;
                }).join('');
                
                if (window.I18n) window.I18n.render();

            } catch (error) {
                console.error(error);
                document.getElementById('enrolled-course-container').innerHTML = `<div class="text-danger" data-i18n="std_conn_error">Lỗi kết nối.</div>`;
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
