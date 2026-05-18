<?php
$pageTitle = 'Đăng nhập — AI Study Hub®';
$actor = 'auth';
require __DIR__ . '/layouts/header.php';
?>

<link rel="stylesheet" href="/assets/css/pages/auth.css">

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

    <div class="auth-container">
        <div class="auth-card fade-up visible">
            <div class="auth-header">
                <a href="/" style="display:inline-block; margin-bottom:1.5rem;">
                    <div style="width:40px; height:40px; background:var(--text-primary); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; font-weight:800;">A</div>
                </a>
                <h1 class="auth-title" data-i18n="title_login">Chào mừng quay lại</h1>
                <p class="auth-subtitle" data-i18n="subtitle_platform">Tiếp tục hành trình làm chủ AI của bạn</p>
            </div>

            <form id="login-form">
                <div class="form-group">
                    <label class="form-label" data-i18n="label_email">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" data-i18n="label_password">Mật khẩu</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="auth-btn" id="btn-login" data-i18n="btn_login">Đăng nhập ngay</button>
            </form>

            <div class="auth-footer">
                <span data-i18n="text_no_account">Bạn mới đến đây?</span> 
                <a href="/register" data-i18n="link_register_now">Đăng ký thành viên</a>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btn-login');
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            btn.disabled = true;
            btn.textContent = I18n.get('btn_loading');
            
            const res = await window.api.post('/auth/login', { email, password });
            
            // Fix: Check res.data.token instead of res.token
            if (res.data && res.data.token) {
                window.api.setToken(res.data.token, res.data.user);
                App.showToast(I18n.get('toast_login_success'), 'success');
                
                setTimeout(() => {
                    const roles = { 
                        'admin': '/admin/dashboard', 
                        'teacher': '/teacher/dashboard', 
                        'student': '/student/dashboard' 
                    };
                    window.location.href = roles[res.data.user.role] || '/';
                }, 1000);
            } else {
                throw new Error("Login succeeded but no session token received.");
            }
        } catch (err) {
            console.error('Login Error:', err);
            App.showToast(err.message || I18n.get('toast_error'), 'error');
            btn.disabled = false;
            btn.textContent = I18n.get('btn_login');
        }
    });
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>