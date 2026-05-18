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

<!-- Custom Premium Glass Confirmation Modal -->
<div id="custom-confirm-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(15px); z-index:9999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease;">
    <div id="confirm-modal-card" style="background:linear-gradient(135deg, #0f172a, #020617); border:1px solid rgba(255,255,255,0.08); border-radius:24px; padding:2.5rem; width:100%; max-width:440px; box-shadow:0 30px 60px rgba(0,0,0,0.8), 0 0 50px rgba(99,102,241,0.1); transform:scale(0.9); transition:transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); text-align:center; position:relative;">
        <div id="confirm-modal-icon-container" style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem auto; font-size:2rem; box-shadow:0 0 20px rgba(255,255,255,0.05);">
            <!-- Icon will be injected here -->
        </div>
        <h3 id="confirm-modal-title" style="font-size:1.4rem; font-weight:800; color:#fff; margin-bottom:0.75rem;">Xác nhận hành động</h3>
        <p id="confirm-modal-message" style="font-size:0.9rem; color:#94a3b8; line-height:1.6; margin-bottom:2rem; padding:0 0.5rem;">Thông báo...</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <button id="confirm-modal-cancel" class="admin-btn admin-btn-ghost" style="width:100%; padding:0.875rem; border-radius:12px; font-weight:700; border:1px solid rgba(255,255,255,0.08); cursor:pointer;">Hủy bỏ</button>
            <button id="confirm-modal-ok" class="admin-btn" style="width:100%; padding:0.875rem; border-radius:12px; font-weight:700; color:#fff; border:none; box-shadow:0 10px 20px rgba(0,0,0,0.2); cursor:pointer;">Đồng ý</button>
        </div>
    </div>
</div>

<script>
let confirmCallback = null;

function showConfirmModal({ title, message, iconClass, iconBg, iconColor, confirmText, confirmBtnClass, onConfirm }) {
    const modal = document.getElementById('custom-confirm-modal');
    const card = document.getElementById('confirm-modal-card');
    
    document.getElementById('confirm-modal-title').innerText = title;
    document.getElementById('confirm-modal-message').innerText = message;
    
    const iconContainer = document.getElementById('confirm-modal-icon-container');
    iconContainer.innerHTML = `<i class="${iconClass}"></i>`;
    iconContainer.style.background = iconBg;
    iconContainer.style.color = iconColor;
    iconContainer.style.boxShadow = `0 0 20px ${iconBg}`;
    
    const okBtn = document.getElementById('confirm-modal-ok');
    okBtn.innerText = confirmText || 'Đồng ý';
    
    okBtn.className = `admin-btn ${confirmBtnClass || 'admin-btn-success'}`;
    if (confirmBtnClass === 'admin-btn-success') {
        okBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
    } else if (confirmBtnClass === 'admin-btn-danger') {
        okBtn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
    } else {
        okBtn.style.background = 'linear-gradient(135deg, #6366f1, #4f46e5)';
    }
    
    confirmCallback = onConfirm;
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
        card.style.transform = 'scale(1)';
    }, 10);
}

function hideConfirmModal() {
    const modal = document.getElementById('custom-confirm-modal');
    const card = document.getElementById('confirm-modal-card');
    
    modal.style.opacity = '0';
    card.style.transform = 'scale(0.9)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['admin']);
    if (!user) return;

    // Gán sự kiện cho các nút trong Modal Confirm tùy chỉnh
    document.getElementById('confirm-modal-cancel').addEventListener('click', hideConfirmModal);
    document.getElementById('confirm-modal-ok').addEventListener('click', () => {
        if (confirmCallback) confirmCallback();
        hideConfirmModal();
    });

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
    showConfirmModal({
        title: 'Phê duyệt Khóa học',
        message: 'Bạn có chắc chắn muốn PHÊ DUYỆT và xuất bản khóa học này lên hệ thống?',
        iconClass: 'fas fa-check-circle',
        iconBg: 'rgba(16, 185, 129, 0.1)',
        iconColor: '#10b981',
        confirmText: 'Phê duyệt ngay',
        confirmBtnClass: 'admin-btn-success',
        onConfirm: async () => {
            try { 
                await window.api.put(`/admin/courses/${id}/approve`); 
                App.showToast('Duyệt thành công!', 'success'); 
                loadPendingCourses(); 
            } catch(e) { App.showToast(e.message, 'error'); }
        }
    });
}

async function rejectCourse(id) {
    showConfirmModal({
        title: 'Từ chối Khóa học',
        message: 'Bạn có chắc chắn muốn TỪ CHỐI phê duyệt khóa học này?',
        iconClass: 'fas fa-times-circle',
        iconBg: 'rgba(239, 68, 68, 0.1)',
        iconColor: '#ef4444',
        confirmText: 'Xác nhận từ chối',
        confirmBtnClass: 'admin-btn-danger',
        onConfirm: async () => {
            try { 
                await window.api.put(`/admin/courses/${id}/reject`); 
                App.showToast('Đã từ chối.', 'success'); 
                loadPendingCourses(); 
            } catch(e) { App.showToast(e.message, 'error'); }
        }
    });
}

async function hideCourse(id) {
    showConfirmModal({
        title: 'Ẩn Khóa học',
        message: 'Bạn có chắc chắn muốn ẨN khóa học này khỏi danh mục hiển thị cho học viên?',
        iconClass: 'fas fa-eye-slash',
        iconBg: 'rgba(245, 158, 11, 0.1)',
        iconColor: '#f59e0b',
        confirmText: 'Xác nhận ẩn',
        confirmBtnClass: 'admin-btn-warning',
        onConfirm: async () => {
            try { 
                await window.api.put(`/admin/courses/${id}/hide`); 
                App.showToast('Đã ẩn.', 'success'); 
                loadPendingCourses(); 
            } catch(e) { App.showToast(e.message, 'error'); }
        }
    });
}

async function showCourse(id) {
    try { 
        await window.api.put(`/admin/courses/${id}/show`); 
        App.showToast('Đã hiện.', 'success'); 
        loadPendingCourses(); 
    } catch(e) { App.showToast(e.message, 'error'); }
}

async function deleteCourse(id) {
    showConfirmModal({
        title: 'Xóa vĩnh viễn Khóa học',
        message: 'CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN khóa học này? Toàn bộ chương, bài học và dữ liệu đi kèm sẽ bị mất và không thể khôi phục!',
        iconClass: 'fas fa-trash-alt',
        iconBg: 'rgba(239, 68, 68, 0.15)',
        iconColor: '#ef4444',
        confirmText: 'Xóa vĩnh viễn',
        confirmBtnClass: 'admin-btn-danger',
        onConfirm: async () => {
            try { 
                await window.api.delete(`/admin/courses/${id}`); 
                App.showToast('Đã xóa.', 'success'); 
                loadPendingCourses(); 
            } catch(e) { App.showToast(e.message, 'error'); }
        }
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
