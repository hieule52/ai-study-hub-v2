<?php
$pageTitle = 'My Students - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">
              <link rel="stylesheet" href="/assets/css/teacher/students.css?v=' . time() . '">
              <script src="/assets/js/teacher/notifications.js?v=' . time() . '" defer></script>';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <!-- Sidebar -->
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="teacher-content">
        <header class="dash-header">
            <div class="dash-title-group">
                <h1 data-i18n="nav_teacher_students">Học viên</h1>
                <p data-i18n="tc_dash_subtitle">Theo dõi tiến độ và hỗ trợ học viên của bạn</p>
            </div>
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <div style="position: relative;">
                    <button class="notif-bell-btn" id="teacherNotifBell" onclick="toggleTeacherNotif(event)">
                        <i class="fas fa-bell"></i>
                        <span id="teacherNotifBadge" class="notif-badge" style="display: none;">0</span>
                    </button>
                    
                    <div id="teacherNotifDropdown" class="teacher-notif-dropdown" style="display: none;">
                        <div class="notif-dropdown-header">
                            <span data-i18n="nav_hub_menu">Thông báo</span>
                            <button onclick="markTeacherNotifRead(event)" class="btn-mark-read" data-i18n="nav_hub_mark_read">Xác nhận tất cả</button>
                        </div>
                        <div id="teacherNotifList" class="notif-dropdown-list"></div>
                    </div>
                </div>

                <div class="chat-input-wrap" style="width: 320px; padding: 0.5rem 1.25rem;">
                    <i class="fas fa-search" style="opacity: 0.3; font-size: 0.9rem;"></i>
                    <input type="text" id="studentSearch" class="chat-input" data-i18n="tc_stud_search" placeholder="Tìm tên học viên..." style="padding: 0.5rem 0.5rem; margin-left: 8px;">
                </div>
            </div>
        </header>

        <div class="student-grid" id="studentGrid">
            <!-- Populated via JS -->
            <div class="p-10 text-center opacity-50 col-span-full">Đang tải danh sách học viên...</div>
        </div>
    </main>
</div>

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['teacher', 'admin']);
    if (!user) return;
    if (window.I18n) window.I18n.render();

    try {
        const res = await window.api.get('/teacher/students');
        const students = res.data || [];
        
        const grid = document.getElementById('studentGrid');
        if (students.length === 0) {
            grid.innerHTML = '<div class="p-10 text-center opacity-50 col-span-full">Bạn chưa có học viên nào tham gia khóa học.</div>';
            return;
        }

        const renderStudents = (list) => {
            grid.innerHTML = list.map(s => `
                <div class="student-card">
                    <div class="student-avatar-lg-wrap">
                        ${getAvatarHtml(s.username, s.avatar, 80)}
                    </div>
                    <h3 class="student-name">${escapeHtml(s.username)}</h3>
                    <p class="student-email">${escapeHtml(s.email)}</p>
                    
                    <div class="student-course-info">
                        <span class="course-label">Đang học</span>
                        <div class="course-title">${escapeHtml(s.course_title)}</div>
                    </div>

                    <div class="progress-container">
                        <div class="progress-label-row">
                            <span>Tiến độ hoàn thành</span>
                            <span>${s.progress_percent}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: ${s.progress_percent}%"></div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="/teacher/chat.php?user_id=${s.user_id}" class="btn btn-primary">
                            <i class="fas fa-comment"></i> Nhắn tin
                        </a>
                    </div>
                </div>
            `).join('');
        };

        renderStudents(students);

        document.getElementById('studentSearch').addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const filtered = students.filter(s => 
                s.username.toLowerCase().includes(term) || 
                s.email.toLowerCase().includes(term) ||
                s.course_title.toLowerCase().includes(term)
            );
            renderStudents(filtered);
        });
    } catch (err) {
        console.error(err);
    }
});

function getAvatarHtml(name, avatarUrl, size = 40) {
    if (avatarUrl) {
        return `<img src="${avatarUrl}" style="width:${size}px; height:${size}px; border-radius: 50%; object-fit:cover; border:2px solid var(--glass-border);">`;
    }
    const initial = name ? name.charAt(0).toUpperCase() : '?';
    return `
        <div style="width:${size}px; height:${size}px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #818cf8); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:${size/2.2}px; border:2px solid rgba(255,255,255,0.1); box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
            ${initial}
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
