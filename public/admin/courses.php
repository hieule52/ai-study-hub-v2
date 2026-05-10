<?php
$pageTitle = 'Quản lý Khóa học - Admin AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
    .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
    .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
</style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 style="font-size: 2rem;">📚 Quản Lý Khóa Học</h1>
        <p class="text-secondary mt-2">Quản lý toàn bộ khóa học trên hệ thống, bao gồm xét duyệt khóa học mới.</p>
    </div>
</div>

<div class="card glass-panel" style="padding: 1.5rem; border-color: rgba(239, 68, 68, 0.2);" id="courses">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>KHÓA HỌC</th>
                    <th>GIẢNG VIÊN</th>
                    <th>PHÍ THAM GIA</th>
                    <th>THAO TÁC</th>
                </tr>
            </thead>
            <tbody id="pending-courses-table">
                <tr><td colspan="5" style="text-align: center; padding: 2rem;">Đang tải dữ liệu...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php ob_start(); ?>
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
            if(res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">Hệ thống chưa có khóa học nào.</td></tr>';
                return;
            }

            tbody.innerHTML = res.data.map(c => `
                <tr id="course-row-${c.id}">
                    <td>#${c.id}</td>
                    <td style="font-weight: 500; max-width: 200px;">
                        ${c.title}
                        <div style="font-size: 0.8rem; color:var(--text-secondary); margin-top: 4px;">
                        <a href="/admin/preview-course.php?course_id=${c.id}" target="_blank" style="color:var(--primary)">🔍 Xem trước</a>
                        </div>
                    </td>
                    <td>${c.teacher_name}<br><small class="text-secondary">${c.teacher_email}</small></td>
                    <td style="color:var(--warning); font-weight:bold;">${c.price > 0 ? new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(c.price) : 'Miễn phí'}</td>
                    <td>
                        <div style="margin-bottom: 8px;">
                            ${c.status === 'pending' ? 
                                `<span style="color:var(--warning); font-weight:600; font-size:0.85rem;">⏳ Đang chờ duyệt</span>`
                            : c.status === 'approved' ? 
                                `<span style="color:var(--success); font-weight:600; font-size:0.85rem;">✅ Đã duyệt</span>`
                            : c.status === 'hidden' ? 
                                `<span style="color:var(--text-secondary); font-weight:600; font-size:0.85rem;">🚫 Đã ẩn</span>`
                            : `<span style="color:var(--text-secondary); font-weight:600; font-size:0.85rem;">📝 Bản nháp</span>`
                            }
                        </div>
                        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                            ${c.status === 'pending' ? 
                                `<button onclick="approveCourse(${c.id})" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border: none; background: var(--success);">Duyệt</button>
                                 <button onclick="rejectCourse(${c.id})" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--danger); color: var(--danger);">Từ chối</button>`
                            : c.status === 'approved' ? 
                                `<button onclick="hideCourse(${c.id})" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--warning); color: var(--warning);">Ẩn</button>`
                            : c.status === 'hidden' ? 
                                `<button onclick="showCourse(${c.id})" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--success); color: var(--success);">Hiện</button>`
                            : ''
                            }
                            <button onclick="deleteCourse(${c.id})" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-color: var(--danger); color: var(--danger);">Xóa</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch(e) {
            console.error("Lỗi tải khóa học", e);
        }
    }

    async function approveCourse(id) {
        if(!confirm('Xác nhận duyệt khóa học này?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/approve`);
            App.showToast("Duyệt khóa học thành công!", "success");
            loadPendingCourses();
        } catch(e) {
            App.showToast(e.message, "error");
        }
    }

    async function rejectCourse(id) {
        if(!confirm('Từ chối khóa học này? Trạng thái sẽ bị đổi thành Bản Nháp.')) return;
        try {
            await window.api.put(`/admin/courses/${id}/reject`);
            App.showToast("Đã chuyển khóa học về Bản nháp.", "success");
            loadPendingCourses();
        } catch(e) {
            App.showToast(e.message, "error");
        }
    }

    async function hideCourse(id) {
        if(!confirm('Bạn có chắc chắn muốn ẩn khóa học này khỏi hệ thống?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/hide`);
            App.showToast("Đã ẩn khóa học.", "success");
            loadPendingCourses();
        } catch(e) {
            App.showToast(e.message, "error");
        }
    }

    async function showCourse(id) {
        if(!confirm('Hiển thị lại khóa học này trên hệ thống?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/show`);
            App.showToast("Đã hiển thị khóa học.", "success");
            loadPendingCourses();
        } catch(e) {
            App.showToast(e.message, "error");
        }
    }

    async function deleteCourse(id) {
        if(!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn khóa học này?')) return;
        try {
            await window.api.delete(`/admin/courses/${id}`);
            App.showToast("Đã xóa khóa học thành công.", "success");
            loadPendingCourses();
        } catch(e) {
            App.showToast(e.message, "error");
        }
    }
</script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
