<?php
$pageTitle = 'Tham gia ngay — AI Study Hub®';
$actor = 'auth';
require __DIR__ . '/layouts/header.php';
?>

<link rel="stylesheet" href="/assets/css/pages/auth.css">
<style>
    .role-selector {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .role-option {
        flex: 1;
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.05);
        color: var(--text-secondary);
    }
    .role-option i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .role-option.active {
        border-color: var(--accent-primary);
        background: rgba(99, 102, 241, 0.1);
        color: var(--accent-primary);
    }
    .role-option:hover:not(.active) {
        border-color: var(--text-secondary);
    }
</style>

<div class="auth-page">
    <!-- Back to Home Button -->
    <a href="/" class="back-home-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span data-i18n="nav_home">Trang chủ</span>
    </a>

    <video autoplay muted loop playsinline class="auth-bg-video">
        <source src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4" type="video/mp4">
    </video>

    <div class="auth-container" style="max-width: 500px;">
        <div class="auth-card fade-up visible">
            <div class="auth-header">
                <a href="/" style="display:inline-block; margin-bottom:1.5rem;">
                    <div style="width:40px; height:40px; background:var(--text-primary); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; font-weight:800;">A</div>
                </a>
                <h1 class="auth-title" data-i18n="title_register">Tham gia AI Study Hub®</h1>
                <p class="auth-subtitle" data-i18n="subtitle_platform">Bắt đầu hành trình làm chủ công nghệ cùng cộng đồng AI</p>
            </div>

            <form id="register-form">
                <label class="form-label" data-i18n="label_role">Bạn là...</label>
                <div class="role-selector">
                    <div class="role-option active" data-role="student">
                        <i class="fas fa-user-graduate"></i>
                        <span data-i18n="role_student">Học sinh</span>
                    </div>
                    <div class="role-option" data-role="teacher">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span data-i18n="role_teacher">Giảng viên</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" data-i18n="label_display_name">Tên hiển thị</label>
                    <input type="text" id="username" class="form-control" placeholder="Nguyen Van A" required>
                </div>
                <div class="form-group">
                    <label class="form-label" data-i18n="label_email">Địa chỉ Email</label>
                    <input type="email" id="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" data-i18n="label_password">Mật khẩu bảo mật</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="auth-btn" id="btn-register" data-i18n="btn_create_account">Tạo tài khoản miễn phí</button>
            </form>

            <div class="auth-footer">
                <span data-i18n="text_already_have_account">Đã có tài khoản?</span> 
                <a href="/login" data-i18n="link_login_now">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    // Role selection logic
    const roleOptions = document.querySelectorAll('.role-option');
    let selectedRole = 'student';

    roleOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            roleOptions.forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            selectedRole = opt.dataset.role;
        });
    });

    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btn-register');
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            btn.disabled = true;
            btn.textContent = I18n.get('btn_loading');
            
            // API call includes the selected role
            const res = await window.api.post('/auth/register', { 
                username, 
                email, 
                password,
                role: selectedRole
            });
            
            // Fix: Check res.data.token instead of res.token
            if (res.data && res.data.token) {
                window.api.setToken(res.data.token, res.data.user);
                App.showToast(I18n.get('toast_register_success'), 'success');
                
                setTimeout(() => {
                    const dashboard = selectedRole === 'teacher' ? '/teacher/dashboard' : '/student/dashboard';
                    window.location.href = dashboard;
                }, 1500);
            } else {
                console.error('API Response missing data.token:', res);
                throw new Error("Registration succeeded but no session token received.");
            }
        } catch (err) {
            console.error('Registration Error:', err);
            App.showToast(err.message || I18n.get('toast_error'), 'error');
            btn.disabled = false;
            btn.textContent = I18n.get('btn_create_account');
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>