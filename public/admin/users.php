<?php
$pageTitle = 'Quản lý Người dùng — Admin AI Study Hub';
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
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <h1 class="admin-page-title">Quản lý người dùng</h1>
                <p class="admin-page-subtitle">Toàn quyền kiểm soát tài khoản, phân quyền và truy cập</p>
            </div>
            <div class="admin-topbar-right">
                <div class="admin-search-wrap" style="width: 300px;">
                    <i class="fas fa-search" style="opacity: 0.3; font-size: 0.875rem;"></i>
                    <input type="text" id="user-search" placeholder="Tìm email, tên, phân quyền..." onkeyup="filterUsers()">
                </div>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fas fa-users" style="color: #818cf8;"></i>
                        Danh sách tài khoản
                    </div>
                    <span id="userCount" style="font-size:0.8rem; opacity:0.4;"></span>
                </div>
                <table class="admin-table" id="userTableEl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NGƯỜI DÙNG</th>
                            <th>PHÂN QUYỀN</th>
                            <th>TRẠNG THÁI</th>
                            <th>THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody id="user-table">
                        <tr><td colspan="5" style="text-align:center; padding:3rem; opacity:0.4;">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.65); backdrop-filter:blur(12px); align-items:center; justify-content:center;" onclick="if(event.target===this) closeModal()">
    <div style="background:linear-gradient(135deg,#0d1525,#111827); border:1px solid rgba(255,255,255,0.08); border-radius:24px; width:100%; max-width:480px; margin:1rem; box-shadow:0 40px 80px rgba(0,0,0,0.6); animation: modalSlideIn 0.35s cubic-bezier(0.16,1,0.3,1);">
        <div style="padding:2rem 2rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;font-size:1.1rem;"><i class="fas fa-user-edit" style="color:#818cf8;"></i></div>
                <div>
                    <div style="font-weight:800; font-size:1.1rem; color:#fff;">Chỉnh sửa tài khoản</div>
                    <div style="font-size:0.78rem; opacity:0.4; margin-top:2px;" id="editModalEmail">—</div>
                </div>
            </div>
            <button onclick="closeModal()" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.5);width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>

        <form id="editForm" onsubmit="saveUser(event)" style="padding:1.5rem 2rem;">
            <input type="hidden" id="edit_id">

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.35);margin-bottom:0.5rem;">Tên hiển thị</label>
                <input type="text" id="edit_username" required
                    style="width:100%;padding:0.875rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-size:0.9rem;font-family:inherit;outline:none;box-sizing:border-box;transition:all 0.3s;"
                    onfocus="this.style.borderColor='rgba(99,102,241,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.35);margin-bottom:0.5rem;">Phân quyền</label>
                    <select id="edit_role"
                        style="width:100%;padding:0.875rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-size:0.9rem;font-family:inherit;outline:none;box-sizing:border-box;">
                        <option value="student">Học viên</option>
                        <option value="teacher">Giảng viên</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.35);margin-bottom:0.5rem;">Tài khoản VIP</label>
                    <select id="edit_vip"
                        style="width:100%;padding:0.875rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-size:0.9rem;font-family:inherit;outline:none;box-sizing:border-box;">
                        <option value="0">Thường</option>
                        <option value="1">VIP Premium</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.35);margin-bottom:0.5rem;">Trạng thái tài khoản</label>
                <select id="edit_status"
                    style="width:100%;padding:0.875rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-size:0.9rem;font-family:inherit;outline:none;box-sizing:border-box;">
                    <option value="active">Hoạt động</option>
                    <option value="banned">Bị khóa</option>
                </select>
            </div>

            <div style="margin-bottom:1.75rem;">
                <label style="display:block;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.35);margin-bottom:0.5rem;">Mật khẩu mới <span style="opacity:0.4;">(để trống nếu không đổi)</span></label>
                <input type="password" id="edit_password" placeholder="••••••••"
                    style="width:100%;padding:0.875rem 1rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:#fff;font-size:0.9rem;font-family:inherit;outline:none;box-sizing:border-box;transition:all 0.3s;"
                    onfocus="this.style.borderColor='rgba(99,102,241,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="display:flex; gap:0.75rem;">
                <button type="submit" style="flex:1;padding:0.875rem;background:linear-gradient(135deg,var(--primary),#818cf8);border:none;border-radius:12px;color:#fff;font-size:0.9rem;font-weight:700;cursor:pointer;font-family:inherit;">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <button type="button" onclick="closeModal()" style="padding:0.875rem 1.25rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;color:rgba(255,255,255,0.6);font-size:0.9rem;cursor:pointer;font-family:inherit;">Hủy</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalSlideIn {
    from { opacity:0; transform:translateY(20px) scale(0.97); }
    to { opacity:1; transform:translateY(0) scale(1); }
}
select option { background: #0d1525; color: #fff; }
.admin-btn-warning {
    background: rgba(245,158,11,0.12) !important;
    color: #f59e0b !important;
    border: 1px solid rgba(245,158,11,0.25) !important;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.2s;
}
.admin-btn-warning:hover {
    background: rgba(245,158,11,0.25) !important;
    transform: translateY(-1px);
}
.admin-btn-success {
    background: rgba(16,185,129,0.12) !important;
    color: #10b981 !important;
    border: 1px solid rgba(16,185,129,0.25) !important;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.2s;
}
.admin-btn-success:hover {
    background: rgba(16,185,129,0.25) !important;
    transform: translateY(-1px);
}
</style>

<script>
let allUsers = [];

document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['admin']);
    if (!user) return;
    await loadUsers();
});

async function loadUsers() {
    try {
        const res = await window.api.get('/admin/users');
        allUsers = res.data.items || res.data;
        renderUsers(allUsers);
    } catch(e) { console.error(e); }
}

function getInitial(name) {
    return name ? name.charAt(0).toUpperCase() : '?';
}

function getAvatarColor(role) {
    return role === 'admin' ? 'linear-gradient(135deg,#ec4899,#f43f5e)'
         : role === 'teacher' ? 'linear-gradient(135deg,#10b981,#059669)'
         : 'linear-gradient(135deg,var(--primary),#818cf8)';
}

function renderUsers(users) {
    const tbody = document.getElementById('user-table');
    document.getElementById('userCount').innerText = `${users.length} người dùng`;

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:3rem;opacity:0.4;">Không tìm thấy kết quả</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(u => {
        const roleClass = u.role === 'admin' ? 'badge-admin' : u.role === 'teacher' ? 'badge-teacher' : 'badge-student';
        const statusClass = u.status === 'active' ? 'badge-active' : 'badge-draft';
        const statusLabel = u.status === 'active' ? 'Hoạt động' : 'Bị khóa';

        // Lock/Unlock button
        const isActive = u.status === 'active';
        const lockBtnClass = isActive ? 'admin-btn-warning' : 'admin-btn-success';
        const lockIcon = isActive ? 'fa-lock' : 'fa-lock-open';
        const lockTitle = isActive ? 'Khóa' : 'Mở khóa';

        return `
            <tr id="u-row-${u.id}">
                <td style="opacity:0.4; font-size:0.8rem;">#${u.id}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:0.875rem;">
                        <div class="admin-avatar" style="background:${getAvatarColor(u.role)};">${getInitial(u.username)}</div>
                        <div>
                            <div style="font-weight:600; color:#fff;">${u.username}</div>
                            <div style="font-size:0.78rem; opacity:0.4; margin-top:2px;">${u.email}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="admin-badge ${roleClass}">${u.role}</span>
                </td>
                <td><span class="admin-badge ${statusClass}">${statusLabel}</span></td>
                <td>
                    <div style="display:flex; gap:0.5rem;">
                        <button onclick="toggleUserStatus(${u.id}, '${u.status}')" class="admin-btn ${lockBtnClass}" title="${lockTitle}">
                            <i class="fas ${lockIcon}"></i>
                        </button>
                        <button onclick='openEditModal(${JSON.stringify(u).replace(/'/g, "&#39;")})' class="admin-btn admin-btn-ghost">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button onclick="deleteUser(${u.id})" class="admin-btn admin-btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function filterUsers() {
    const val = document.getElementById('user-search').value.toLowerCase();
    renderUsers(allUsers.filter(u =>
        u.email.toLowerCase().includes(val) ||
        u.username.toLowerCase().includes(val) ||
        u.role.toLowerCase().includes(val)
    ));
}

function openEditModal(u) {
    document.getElementById('edit_id').value    = u.id;
    document.getElementById('edit_username').value = u.username;
    document.getElementById('edit_role').value  = u.role;
    document.getElementById('edit_vip').value   = u.is_vip;
    document.getElementById('edit_status').value = u.status;
    document.getElementById('edit_password').value = '';
    document.getElementById('editModalEmail').innerText = u.email;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

async function saveUser(e) {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const data = {
        username: document.getElementById('edit_username').value,
        role:     document.getElementById('edit_role').value,
        is_vip:   document.getElementById('edit_vip').value,
        status:   document.getElementById('edit_status').value
    };
    const pwd = document.getElementById('edit_password').value;
    if (pwd) data.password = pwd;

    try {
        await window.api.put(`/admin/users/${id}`, data);
        App.showToast('Cập nhật tài khoản thành công!', 'success');
        closeModal();
        loadUsers();
    } catch(err) { App.showToast(err.message, 'error'); }
}

async function deleteUser(id) {
    const confirmed = await App.confirm({
        title: 'Xóa tài khoản',
        message: 'Bạn có chắc chắn muốn xóa tài khoản này? Người dùng sẽ mất quyền truy cập hệ thống ngay lập tức.',
        type: 'danger',
        confirmText: 'Xóa tài khoản',
        cancelText: 'Hủy bỏ'
    });
    if (!confirmed) return;
    try {
        await window.api.delete(`/admin/users/${id}`);
        App.showToast('Đã xóa người dùng thành công.', 'success');
        loadUsers();
    } catch(e) { App.showToast(e.message, 'error'); }
}

async function toggleUserStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'banned' : 'active';
    const actionLabel = newStatus === 'banned' ? 'Khóa tài khoản' : 'Mở khóa tài khoản';
    const msg = newStatus === 'banned'
        ? 'Tài khoản sẽ bị khóa và không thể truy cập hệ thống. Bạn có chắc chắn?'
        : 'Tài khoản sẽ được mở khóa và có thể truy cập lại hệ thống. Bạn có chắc chắn?';

    const confirmed = await App.confirm({
        title: actionLabel,
        message: msg,
        type: newStatus === 'banned' ? 'warning' : 'info',
        confirmText: actionLabel,
        cancelText: 'Hủy bỏ'
    });
    if (!confirmed) return;

    try {
        await window.api.put(`/admin/users/${id}/status`, { status: newStatus });
        App.showToast(`Đã ${newStatus === 'banned' ? 'khóa' : 'mở khóa'} tài khoản thành công!`, 'success');
        loadUsers();
    } catch(e) {
        App.showToast(e.message, 'error');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
