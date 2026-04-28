<?php
$pageTitle = 'Quản lý Users - Admin AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        
        /* Premium Modal Styles */
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; 
            width: 100%; height: 100%; 
            background-color: rgba(0, 0, 0, 0.4); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal.active { display: flex; opacity: 1; }
        .modal-content {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem; width: 450px; max-width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255,255,255,0.1);
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .modal.active .modal-content {
            transform: scale(1) translateY(0);
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;}
        .modal-header h3 { display: flex; align-items: center; gap: 10px; margin: 0; font-size: 1.25rem; color: #fff;}
        .modal-close { 
            cursor: pointer; color: var(--text-secondary); font-size: 1.5rem; 
            width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; background: rgba(255,255,255,0.05); transition: all 0.2s;
        }
        .modal-close:hover { background: rgba(255,255,255,0.1); color: #fff; transform: rotate(90deg);}
        
        .premium-input {
            background: rgba(0,0,0,0.2) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 10px !important;
            padding: 0.75rem 1rem !important;
            color: #fff !important;
            transition: all 0.3s ease !important;
        }
        .premium-input:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }
        .btn-premium-save {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-premium-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
        }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;">👥 Quản lý Users Toàn Hệ Thống</h1>
                    <p class="text-secondary mt-2">Toàn quyền kiểm soát tài khoản, phân quyền, và chặn truy cập.</p>
                </div>
            </div>

            <!-- Manage Users -->
            <div class="card glass-panel" style="padding: 1.5rem;" id="users">
                <div class="flex justify-between items-center mb-4">
                    <h3>Danh sách tài khoản</h3>
                    <input type="text" id="user-search" class="form-control" style="width: 250px; padding: 0.5rem;" placeholder="🔍 Tìm email/tên/role..." onkeyup="filterUsers()">
                </div>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>EMAIL / TÊN</th>
                                <th>PHÂN QUYỀN</th>
                                <th>TRẠNG THÁI</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody id="user-table">
                            <tr><td colspan="5" style="text-align: center; padding: 2rem;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

<?php ob_start(); ?>
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
                allUsers = res.data;
                renderUsers(allUsers);
            } catch(e) {
                console.error("Lỗi tải user", e);
            }
        }

        function renderUsers(users) {
            const tbody = document.getElementById('user-table');
            if(users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Không tìm thấy user.</td></tr>';
                return;
            }

            tbody.innerHTML = users.map(u => {
                return `
                    <tr id="u-row-${u.id}">
                        <td>#${u.id}</td>
                        <td>
                            <div style="font-weight: 500;">${u.email}</div>
                            <div style="font-size: 0.8rem; color:var(--text-secondary);">${u.username}</div>
                        </td>
                        <td>
                            <span style="text-transform: capitalize; font-weight: bold; color: ${u.role==='admin'?'var(--danger)':(u.role==='teacher'?'var(--success)':'var(--primary)')}">
                                ${u.role}
                            </span>
                            ${u.is_vip == 1 ? '<span style="font-size: 0.75rem; color: var(--warning); margin-left:5px;">[VIP]</span>' : ''}
                        </td>
                        <td>
                            <span style="color: ${u.status === 'active' ? 'var(--success)' : 'var(--danger)'}">
                                • ${u.status === 'active' ? 'Hoạt động' : 'Bị Khóa'}
                            </span>
                        </td>
                        <td>
                            <button onclick='openEditModal(${JSON.stringify(u).replace(/'/g, "&#39;")})' class="btn btn-outline" style="padding: 0.4rem; font-size: 0.8rem; margin-right: 5px;">Sửa</button>
                            <button onclick="deleteUser(${u.id})" class="btn btn-outline" style="padding: 0.4rem; font-size: 0.8rem; border-color: var(--danger); color: var(--danger);">Xóa</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function filterUsers() {
            const val = document.getElementById('user-search').value.toLowerCase();
            const filtered = allUsers.filter(u => 
                u.email.toLowerCase().includes(val) || 
                u.username.toLowerCase().includes(val) || 
                u.role.toLowerCase().includes(val)
            );
            renderUsers(filtered);
        }

        // Modal Logic
        const modal = document.getElementById('editModal');
        function openEditModal(u) {
            document.getElementById('edit_id').value = u.id;
            document.getElementById('edit_email').value = u.email;
            document.getElementById('edit_username').value = u.username;
            document.getElementById('edit_role').value = u.role;
            document.getElementById('edit_vip').value = u.is_vip;
            document.getElementById('edit_status').value = u.status;
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        async function saveUser(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const data = {
                username: document.getElementById('edit_username').value,
                role: document.getElementById('edit_role').value,
                is_vip: document.getElementById('edit_vip').value,
                status: document.getElementById('edit_status').value
            };

            try {
                await window.api.put(`/admin/users/${id}`, data);
                App.showToast("Cập nhật thành công!", "success");
                closeModal();
                loadUsers();
            } catch(error) {
                App.showToast(error.message, "error");
            }
        }

        async function deleteUser(id) {
            if(!confirm("CẢNH BÁO: Xóa tài khoản này (Soft Delete)? Người dùng sẽ mất quyền đăng nhập.")) return;
            try {
                await window.api.delete(`/admin/users/${id}`);
                App.showToast("Đã xóa người dùng thành công.", "success");
                loadUsers();
            } catch(e) {
                App.showToast(e.message, "error");
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
