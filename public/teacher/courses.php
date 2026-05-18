<?php
$pageTitle = 'My Courses - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">
              <script src="/assets/js/teacher/notifications.js?v=' . time() . '" defer></script>';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="teacher-content">
        <header class="dash-header">
            <div class="dash-title-group">
                <h1 data-i18n="tc_dash_list_title">My Courses</h1>
                <p data-i18n="tc_dash_subtitle">Manage and update your academic content</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
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

                <a href="/teacher/create-course" class="btn btn-primary" style="padding: 1rem 2rem; border-radius: 100px;">
                    <span data-i18n="tc_dash_btn_create">+ Tạo khóa học mới</span>
                </a>
            </div>
        </header>

        <section>
            <div class="course-table-wrap">
                <table class="course-table">
                    <thead>
                        <tr>
                            <th data-i18n="tc_dash_col_name">Khóa học</th>
                            <th data-i18n="tc_dash_col_price">Học phí</th>
                            <th data-i18n="tc_dash_col_students">Học viên</th>
                            <th data-i18n="tc_dash_col_status">Trạng thái</th>
                            <th data-i18n="tc_dash_col_action">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="course-list">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </section>
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
        const res = await window.api.get('/teacher/dashboard');
        const { courses } = res.data;

        const courseList = document.getElementById('course-list');
        if (courses.length === 0) {
            courseList.innerHTML = `<tr><td colspan="5" class="text-center p-10 opacity-50">${I18n.get('tc_dash_no_courses')}</td></tr>`;
            return;
        }

        courseList.innerHTML = courses.map(c => {
            let statusClass = 'status-draft';
            let statusLabel = 'tc_dash_draft';
            
            if (c.status === 'active' || c.status === 'approved') { 
                statusClass = 'status-active'; 
                statusLabel = 'tc_dash_active'; 
            } else if (c.status === 'pending') { 
                statusClass = 'status-pending'; 
                statusLabel = 'tc_dash_pending'; 
            }

            const priceLabel = c.is_premium ? App.formatVND(c.price) : I18n.get('tc_dash_free');

            return `
                <tr>
                    <td>
                        <div class="course-name-cell">
                            <img src="${c.thumbnail || '/assets/images/course-default.jpg'}" class="course-thumb" alt="">
                            <span class="course-title">${c.title}</span>
                        </div>
                    </td>
                    <td>${priceLabel}</td>
                    <td>${c.student_count || 0} <span style="font-size: 0.8rem; opacity: 0.5;">${I18n.get('tc_dash_unit_student')}</span></td>
                    <td>
                        <span class="status-badge ${statusClass}" data-i18n="${statusLabel}">${I18n.get(statusLabel)}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="/teacher/course-builder.php?course_id=${c.id}" class="btn btn-outline btn-sm" style="border-radius:100px;">
                                ${I18n.get('tc_dash_btn_build')}
                            </a>
                            <button onclick="deleteCourse(${c.id})" class="btn btn-ghost btn-sm" style="color: var(--danger);">
                                ${I18n.get('tc_dash_btn_delete')}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

    } catch (err) {
        console.error(err);
    }
});

async function deleteCourse(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa khóa học này?')) return;
    try {
        await window.api.delete(`/teacher/courses/${id}`);
        App.showToast('Đã xóa khóa học thành công.', 'success');
        location.reload();
    } catch (err) {
        App.showToast(err.message, 'error');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
