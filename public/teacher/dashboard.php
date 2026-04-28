<?php
$pageTitle = 'Teacher Panel - AI Study Hub';
$actor = 'teacher';
ob_start();
?>
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-draft { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-active { background: rgba(16, 185, 129, 0.1); color: var(--success); }
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-draft { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-active { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;" data-i18n="tc_dash_title">Khóa học giảng dạy</h1>
                    <p class="text-secondary mt-2" data-i18n="tc_dash_subtitle">Đăng tải và quản lý các video/bài trắc nghiệm của bạn.</p>
                </div>
                <button onclick="window.location.href='/teacher/create-course.php'" class="btn btn-primary" data-i18n="tc_dash_btn_create">+ Tạo Khóa Học</button>
            </div>

            <!-- Stats -->
            <div class="grid-cols-3 mb-8" id="teacher-stats">
                <div class="card p-4 text-center text-muted" data-i18n="tc_dash_loading_stats">Đang tải thống kê...</div>
            </div>

            <div class="card glass-panel" style="padding: 1.5rem;">
                <h3 class="mb-4" data-i18n="tc_dash_list_title">Danh sách khóa học</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th data-i18n="tc_dash_col_name">TÊN KHÓA HỌC</th>
                            <th data-i18n="tc_dash_col_price">GIÁ BÁN</th>
                            <th data-i18n="tc_dash_col_students">HỌC VIÊN</th>
                            <th data-i18n="tc_dash_col_status">TRẠNG THÁI</th>
                            <th data-i18n="tc_dash_col_action">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody id="course-table">
                        <tr><td colspan="5" style="text-align: center;" data-i18n="tc_dash_loading_courses">Đang tải dữ liệu khóa học...</td></tr>
                    </tbody>
                </table>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['teacher', 'admin']);
            if (!user) return;

            try {
                const res = await window.api.get('/teacher/dashboard');
                const data = res.data;
                const stats = data.stats;
                
                document.getElementById('teacher-stats').innerHTML = `
                    <div class="card p-4">
                        <p class="text-secondary" style="font-size: 0.875rem;" data-i18n="tc_dash_stat_courses">Tổng Khóa Học</p>
                        <h2 style="font-size: 2rem; color: var(--text-primary);">${stats.total_courses || 0}</h2>
                    </div>
                    <div class="card p-4">
                        <p class="text-secondary" style="font-size: 0.875rem;" data-i18n="tc_dash_stat_students">Tổng Học Viên (Enrolled)</p>
                        <h2 style="font-size: 2rem; color: var(--text-primary);">${stats.total_students || 0}</h2>
                    </div>
                    <div class="card p-4">
                        <p class="text-secondary" style="font-size: 0.875rem;" data-i18n="tc_dash_stat_rating">Đánh giá trung bình</p>
                        <h2 style="font-size: 2rem; color: var(--success);">${stats.avg_rating || 'N/A'} <span style="font-size: 1rem;">⭐</span></h2>
                    </div>
                `;

                const tbody = document.getElementById('course-table');
                const courses = data.courses;

                if (courses.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;" data-i18n="tc_dash_no_course">Bạn chưa có khóa học nào.</td></tr>`;
                    if (window.I18n) window.I18n.render();
                    return;
                }

                const freeText = window.I18n ? window.I18n.get('tc_dash_free') : 'Miễn phí';
                const stuUnit = window.I18n ? window.I18n.get('tc_dash_unit_student') : 'hv';
                const buildBtnText = window.I18n ? window.I18n.get('tc_dash_btn_build') : 'Hành trang lộ trình';
                const delBtnText = window.I18n ? window.I18n.get('tc_dash_btn_delete') : 'Xóa';

                tbody.innerHTML = courses.map(c => `
                    <tr>
                        <td style="font-weight: 500;">${c.title}</td>
                        <td>${c.price > 0 ? new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(c.price) : freeText}</td>
                        <td>${c.total_students || 0} <span class="text-muted" style="font-size:0.8rem">${stuUnit}</span></td>
                        <td>
                            <span class="status-badge ${c.status === 'approved' ? 'status-active' : 'status-draft'}" data-i18n="${c.status === 'approved' ? 'tc_dash_active' : 'tc_dash_pending'}">
                                ${c.status === 'approved' ? 'Hoạt động' : 'Chờ duyệt'}
                            </span>
                        </td>
                        <td>
                            <button onclick="window.location.href='/teacher/course-builder.php?course_id=${c.id}'" class="btn btn-outline" style="padding: 0.5rem; color: var(--primary); border-color: var(--primary);">${buildBtnText}</button>
                            <button onclick="deleteCourse(${c.id})" class="btn btn-outline" style="padding: 0.5rem; color: var(--danger); border-color: var(--danger);">${delBtnText}</button>
                        </td>
                    </tr>
                `).join('');
                if (window.I18n) window.I18n.render();

            } catch (err) {
                console.error(err);
                const errMsg = window.I18n ? window.I18n.get('tc_dash_err_load') : 'Lỗi tải dữ liệu Dashboard';
                App.showToast(errMsg, 'error');
            }
        });

        window.deleteCourse = async function(id) {
            const confirmMsg = window.I18n ? window.I18n.get('tc_dash_confirm_delete') : "Bạn có chắc chắn muốn xóa khóa học này?";
            if(!confirm(confirmMsg)) return;
            try {
                await window.api.delete('/teacher/courses/' + id);
                const succMsg = window.I18n ? window.I18n.get('tc_dash_delete_success') : 'Đã xóa khóa học thành công';
                App.showToast(succMsg);
                setTimeout(() => window.location.reload(), 1000);
            } catch(e) {
                App.showToast(e.message, 'error');
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
