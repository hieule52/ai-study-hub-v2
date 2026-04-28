<?php
$pageTitle = 'Đăng nhập - AI Study Hub';
$actor = 'auth';
require __DIR__ . '/layouts/header.php';
?>

    <div class="card glass-panel auth-card">
            <div class="text-center mb-8">
                <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">AI <span class="text-gradient">Study Hub</span></h2>
                <p class="text-secondary">Rất vui được gặp lại bạn!</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Email / Tài khoản</label>
                    <input type="email" id="email" class="form-control" placeholder="admin@aistudyhub.com" required>
                </div>

                <div class="form-group mb-8">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                    Đăng Nhập
                </button>
            </form>

            <div class="text-center mt-8">
                <p class="text-secondary">Chưa có tài khoản? <a href="/register.php" class="text-gradient"
                        style="font-weight: 600; text-decoration: none;">Đăng ký miễn phí</a></p>
                <a href="/" class="text-muted" style="display: block; margin-top: 1rem; font-size: 0.875rem;">&larr; Về
                    trang chủ</a>
            </div>
        </div>

<?php ob_start(); ?>
    <script>
        // Check if already logged in
        if (window.api.getToken()) {
            const u = window.api.getUser();
            if (u) {
                if (u.role === 'admin') window.location.href = '/admin/dashboard.php';
                else if (u.role === 'teacher') window.location.href = '/teacher/dashboard.php';
                else window.location.href = '/'; // Redirect student to new homepage
            }
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Đang xử lý...';
            btn.disabled = true;

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await window.api.post('/auth/login', { email, password });

                // Save session
                window.api.setToken(res.data.token, res.data.user);

                App.showToast('Đăng nhập thành công', 'success');

                setTimeout(() => {
                    if (res.data.user.role === 'admin') window.location.href = '/admin/dashboard.php';
                    else if (res.data.user.role === 'teacher') window.location.href = '/teacher/dashboard.php';
                    else window.location.href = '/'; // Student goes to guest-like homepage
                }, 1000);

            } catch (error) {
                App.showToast(error.message, 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>