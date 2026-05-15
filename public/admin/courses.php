<?php
$pageTitle = 'Quản lý Khóa học — Admin AI Study Hub';
$actor = 'admin';
$noSidebar = true;
$extraHead = '
    <link rel="stylesheet" href="/assets/css/admin/layout.css?v=' . time() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-body">
    <?php require __DIR__ . '/../layouts/admin_sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <h1 class="admin-page-title">Quản lý khóa học</h1>
                <p class="admin-page-subtitle">Duyệt, ẩn và quản lý toàn bộ khóa học trên nền tảng</p>
            </div>
            <div class="admin-topbar-right">
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2); border-radius:100px;">
                    <i class="fas fa-clock" style="color:#f59e0b; font-size:0.8rem;"></i>
                    <span id="pendingCountBadge" style="font-size:0.85rem; font-weight:700; color:#f59e0b;">0 chờ duyệt</span>
                </div>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fas fa-book-open" style="color:#f59e0b;"></i>
                        Danh sách tất cả khóa học
                    </div>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>KHÓA HỌC</th>
                            <th>GIẢNG VIÊN</th>
                            <th>HỌC PHÍ</th>
                            <th>TRẠNG THÁI</th>
                            <th>THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody id="pending-courses-table">
                        <tr><td colspan="5" style="text-align:center;padding:3rem;opacity:0.4;">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['admin']);
    if (!user) return;
    await loadPendingCourses();
});

async function loadPendingCourses() {
    try {
        const res = await window.api.get('/admin/courses');
        const tbody = document.getElementById('pending-courses-table');
        const courses = res.data;

        const pending = courses.filter(c => c.status === 'pending').length;
        document.getElementById('pendingCountBadge').innerText = `${pending} chờ duyệt`;

        if (courses.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:3rem;opacity:0.4;">Chưa có khóa học nào.</td></tr>';
            return;
        }

        tbody.innerHTML = courses.map(c => {
            const statusClass = c.status === 'approved' ? 'badge-approved'
                : c.status === 'pending' ? 'badge-pending'
                : c.status === 'hidden'  ? 'badge-draft'
                : 'badge-draft';

            const statusLabel = c.status === 'approved' ? 'Đã duyệt'
                : c.status === 'pending' ? 'Chờ duyệt'
                : c.status === 'hidden'  ? 'Đã ẩn'
                : 'Bản nháp';

            const priceLabel = c.price > 0
                ? new Intl.NumberFormat('vi-VN', {style:'currency',currency:'VND'}).format(c.price)
                : '<span style="opacity:0.5;">Miễn phí</span>';

            const actions = c.status === 'pending'
                ? `<button onclick="approveCourse(${c.id})" class="admin-btn admin-btn-success"><i class="fas fa-check"></i> Duyệt</button>
                   <button onclick="rejectCourse(${c.id})" class="admin-btn admin-btn-danger"><i class="fas fa-times"></i> Từ chối</button>`
                : c.status === 'approved'
                ? `<button onclick="hideCourse(${c.id})" class="admin-btn admin-btn-ghost"><i class="fas fa-eye-slash"></i> Ẩn</button>`
                : c.status === 'hidden'
                ? `<button onclick="showCourse(${c.id})" class="admin-btn admin-btn-success"><i class="fas fa-eye"></i> Hiện</button>`
                : '';

            return `
                <tr id="course-row-${c.id}">
                    <td>
                        <div style="font-weight:600; color:#fff;">${c.title}</div>
                        <a href="/admin/preview-course.php?course_id=${c.id}" target="_blank"
                           style="font-size:0.75rem; color:#818cf8; text-decoration:none; margin-top:4px; display:inline-block;">
                            <i class="fas fa-external-link-alt"></i> Xem trước
                        </a>
                    </td>
                    <td>
                        <div style="font-weight:500;">${c.teacher_name}</div>
                        <div style="font-size:0.78rem; opacity:0.4;">${c.teacher_email}</div>
                    </td>
                    <td style="font-weight:600; color:#f59e0b;">${priceLabel}</td>
                    <td><span class="admin-badge ${statusClass}">${statusLabel}</span></td>
                    <td>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            ${actions}
                            <button onclick="deleteCourse(${c.id})" class="admin-btn admin-btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } catch(e) { console.error(e); }
}

async function approveCourse(id) {
    if (!confirm('Xác nhận duyệt khóa học này?')) return;
    try { await window.api.put(`/admin/courses/${id}/approve`); App.showToast('Duyệt thành công!','success'); loadPendingCourses(); }
    catch(e) { App.showToast(e.message,'error'); }
}

async function rejectCourse(id) {
    if (!confirm('Từ chối khóa học này?')) return;
    try { await window.api.put(`/admin/courses/${id}/reject`); App.showToast('Đã từ chối.','success'); loadPendingCourses(); }
    catch(e) { App.showToast(e.message,'error'); }
}

async function hideCourse(id) {
    if (!confirm('Ẩn khóa học này?')) return;
    try { await window.api.put(`/admin/courses/${id}/hide`); App.showToast('Đã ẩn.','success'); loadPendingCourses(); }
    catch(e) { App.showToast(e.message,'error'); }
}

async function showCourse(id) {
    try { await window.api.put(`/admin/courses/${id}/show`); App.showToast('Đã hiện.','success'); loadPendingCourses(); }
    catch(e) { App.showToast(e.message,'error'); }
}

async function deleteCourse(id) {
    if (!confirm('Xóa vĩnh viễn khóa học này?')) return;
    try { await window.api.delete(`/admin/courses/${id}`); App.showToast('Đã xóa.','success'); loadPendingCourses(); }
    catch(e) { App.showToast(e.message,'error'); }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
