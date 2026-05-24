<?php
$pageTitle = 'Hồ Sơ Cá Nhân - AI Study Hub';
$actor = 'guest'; 
$extraHead = '
    <link rel="stylesheet" href="/assets/css/pages/profile.css?v=' . time() . '">
';
require __DIR__ . '/layouts/header.php';
?>

    <div class="profile-container">
        
        <div class="profile-header">
            <a href="javascript:window.history.back()" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
                <span data-i18n="lrn_back_home">Trở về</span>
            </a>
            <h1 class="profile-title" data-i18n="profile_title">Cài đặt Tài khoản</h1>
            <p class="text-secondary" data-i18n="profile_subtitle">Quản lý định danh và cài đặt bảo mật gốc.</p>
        </div>

        <!-- Identity Section -->
        <div class="card glass-panel avatar-section">
            <div style="position: relative; cursor: pointer;" onclick="document.getElementById('avatarUpload').click()" id="avatar-container">
                <div class="avatar-circle" id="user-avatar-char" style="overflow: hidden;">
                    <span id="avatar-letter">?</span>
                </div>
                <div class="avatar-upload-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
            </div>
            <input type="file" id="avatarUpload" style="display:none;" accept="image/png, image/jpeg, image/webp" onchange="handleAvatarUpload(event)">
            
            <div>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.2rem;" id="ui-username">...</h3>
                <div style="color: var(--text-secondary); font-size: 0.95rem; opacity: 0.7;" id="ui-email">...</div>
                <div style="margin-top: 1rem;">
                    <span id="ui-role" style="font-size: 0.7rem; padding: 0.3rem 0.8rem; background: rgba(99,102,241,0.15); color: var(--primary); border: 1px solid rgba(99,102,241,0.3); border-radius: 100px; font-weight: 800; letter-spacing: 0.05em;">ROLE</span>
                </div>
            </div>
        </div>

        <!-- Update Info Form -->
        <div class="card glass-panel profile-form-card">
            <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; font-weight: 800;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span data-i18n="profile_personal">Thông tin Cá nhân</span>
            </h3>
            
            <form id="profileForm" onsubmit="updateProfile(event)">
                <div class="form-group mb-6">
                    <label class="form-label" data-i18n="profile_email_label">Email</label>
                    <input type="email" id="email" class="form-control premium-input" disabled>
                </div>
                
                <div class="form-group mb-8">
                    <label class="form-label" data-i18n="profile_name_label">Tên hiển thị</label>
                    <input type="text" id="username" class="form-control premium-input" required placeholder="Nhập tên mới..." data-i18n-placeholder="profile_name_placeholder">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-profile-save" id="btnSaveInfo">
                        <span data-i18n="profile_btn_save">Lưu thay đổi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="card glass-panel profile-form-card" style="border-color: rgba(239, 68, 68, 0.15);">
            <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; font-weight: 800;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--danger);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span data-i18n="profile_security">Bảo mật Đăng nhập</span>
            </h3>
            
            <form id="passwordForm" onsubmit="changePassword(event)">
                <div class="form-group mb-6">
                    <label class="form-label" data-i18n="profile_pass_old">Mật khẩu Hiện tại</label>
                    <input type="password" id="old_password" class="form-control premium-input" required placeholder="••••••••">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label" data-i18n="profile_pass_new">Mật khẩu Mới</label>
                        <input type="password" id="new_password" class="form-control premium-input" required minlength="6" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="profile_pass_confirm">Xác nhận Mật khẩu Mới</label>
                        <input type="password" id="confirm_password" class="form-control premium-input" required minlength="6" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-outline" style="border-radius: 100px; padding: 0.75rem 2rem; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);" id="btnSavePass">
                        <span data-i18n="profile_btn_pass">Đổi Mật Khẩu</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

<?php ob_start(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(); // Require any logged in role
            if (!user) return;
            
            if (user.role === 'teacher') {
                window.location.replace('/teacher/profile');
                return;
            }
            
            await fetchProfile();
        });

        async function fetchProfile() {
            try {
                const res = await window.api.get('/user/profile');
                const user = res.data;
                
                // Binding View
                document.getElementById('email').value = user.email;
                document.getElementById('username').value = user.username;
                
                // Avatar Area binding
                document.getElementById('ui-username').innerText = user.username;
                document.getElementById('ui-email').innerText = user.email;
                document.getElementById('ui-role').innerText = user.role.toUpperCase();
                
                const avatarCircle = document.getElementById('user-avatar-char');
                const fallbackHtml = `<span id="avatar-letter">${user.username.charAt(0).toUpperCase()}</span>`.replace(/\s+/g, ' ').trim();
                if (user.avatar) {
                    avatarCircle.innerHTML = `<img src="${user.avatar}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML=decodeURIComponent('${encodeURIComponent(fallbackHtml)}');">`;
                } else {
                    avatarCircle.innerHTML = fallbackHtml;
                }

            } catch(e) {
                App.showToast("Không thể tải thông tin user: " + e.message, "error");
            }
        }

        async function updateProfile(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveInfo');
            btn.disabled = true;
            btn.innerText = "⏳ Đang lưu...";

            try {
                const username = document.getElementById('username').value;
                await window.api.put('/user/profile', { username });
                
                const successMsg = window.I18n ? window.I18n.get('profile_toast_success') : "Cập nhật thông tin thành công!";
                App.showToast(successMsg, "success");
                
                // Cập nhật lại UI Avatar
                document.getElementById('ui-username').innerText = username;
                
                // Nếu chưa có ảnh thì đổi logo chữ cái
                const avatarLetter = document.getElementById('avatar-letter');
                if(avatarLetter) avatarLetter.innerText = username.charAt(0).toUpperCase();

                // Cập nhật Local Storage object (để navbar không bị cũ)
                const currSession = JSON.parse(localStorage.getItem('auth_user'));
                if(currSession) {
                    currSession.username = username;
                    localStorage.setItem('auth_user', JSON.stringify(currSession));
                    App.renderUserNav(); // Update Right corner
                }

            } catch(e) {
                App.showToast(e.message, "error");
            } finally {
                btn.disabled = false;
                btn.innerText = window.I18n ? window.I18n.get('profile_btn_save') : "Lưu thay đổi";
            }
        }

        async function changePassword(e) {
            e.preventDefault();
            
            const oldPass = document.getElementById('old_password').value;
            const newPass = document.getElementById('new_password').value;
            const confPass = document.getElementById('confirm_password').value;

            if (newPass !== confPass) {
                return App.showToast("Mật khẩu xác nhận không khớp!", "error");
            }

            const btn = document.getElementById('btnSavePass');
            btn.disabled = true;
            btn.innerText = "⏳ Đang đổi...";

            try {
                await window.api.put('/user/change-password', {
                    old_password: oldPass,
                    new_password: newPass
                });
                
                const successMsg = window.I18n ? window.I18n.get('profile_toast_pass_success') : "Thay đổi mật khẩu thành công!";
                App.showToast(successMsg, "success");
                
                // Reset form
                document.getElementById('passwordForm').reset();
                
            } catch(e) {
                App.showToast(e.message, "error");
            } finally {
                btn.disabled = false;
                btn.innerText = window.I18n ? window.I18n.get('profile_btn_pass') : "Đổi Mật Khẩu";
            }
        }

        async function handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Kiểm tra sơ bộ ở Frontend (2MB) để tiết kiệm băng thông gửi lên
            if (file.size > 2 * 1024 * 1024) {
                App.showToast("Dung lượng ảnh vượt quá 2MB, vui lòng chọn ảnh khác.", "error");
                event.target.value = ''; // Reset input
                return;
            }

            App.showToast("Đang tải ảnh lên...", "success");

            try {
                // Tạo FromData multipart
                const formData = new FormData();
                formData.append('avatar', file);

                // Api utility không bọc header content-type cho FormData để trình duyệt tự nhận dạng Boundary
                const token = window.api.getToken();
                
                const response = await fetch(`${window.api.baseUrl}/user/avatar`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Lỗi khi upload ảnh.');
                }

                const newAvatarUrl = data.data.avatar;
                
                // Cập nhật UI Profile ngay lập tức
                document.getElementById('user-avatar-char').innerHTML = `<img src="${newAvatarUrl}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">`;
                
                // Cập nhật LocalStorage
                const currSession = JSON.parse(localStorage.getItem('auth_user'));
                if (currSession) {
                    currSession.avatar = newAvatarUrl;
                    localStorage.setItem('auth_user', JSON.stringify(currSession));
                    App.renderUserNav(); // Cập nhật hình nhỏ góc phải
                }

                const successMsg = window.I18n ? window.I18n.get('profile_toast_avatar_success') : "Đổi ảnh đại diện thành công!";
                App.showToast(successMsg, "success");

            } catch (err) {
                App.showToast(err.message, "error");
            } finally {
                event.target.value = ''; // Reset form reset để up 2 lần 1 file không bị khựng
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>
