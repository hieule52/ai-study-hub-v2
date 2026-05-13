<?php
$actor = $actor ?? 'guest';
$extraScripts = $extraScripts ?? '';
?>

<?php if (in_array($actor, ['student', 'teacher', 'admin']) && !isset($noSidebar)): ?>
    </main> <!-- End main-content -->
    </div> <!-- End dashboard-layout -->
<?php elseif ($actor === 'auth'): ?>
    </div> <!-- End auth-wrapper -->
<?php else: ?>
    <!-- Cinematic Dark Footer -->
    <!-- Cinematic Editorial Footer -->
    <footer class="cinematic-footer">

        <div class="footer-container">

            <!-- Left -->
            <div class="footer-brand">
                <div class="footer-logo">
                    ✦ AI Study Hub<sup>®</sup>
                </div>

                <p class="footer-description">
                    Nền tảng học tập AI thế hệ mới, mang đến trải nghiệm giáo dục
                    cá nhân hóa và chiều sâu học thuật cho mọi học viên.
                </p>

                <div class="footer-copyright">
                    © 2026 AI Study Hub LMS — Lê Diên Hiếu.
                </div>
            </div>

            <!-- Right -->
            <div class="footer-links-wrapper">

                <div class="footer-links">
                    <span class="footer-label">Khám phá</span>

                    <a href="/">Trang chủ</a>
                    <a href="/courses.php">Khóa học</a>
                    <a href="/about.php">Giới thiệu</a>
                </div>

                <div class="footer-links">
                    <span class="footer-label">Hỗ trợ</span>

                    <a href="/login.php">AI Tutor</a>
                    <a href="/student/chat.php">Tin nhắn</a>
                    <a href="/terms.php">Điều khoản</a>
                </div>

            </div>

        </div>

    </footer>
<?php endif; ?>

<!-- Scripts -->
<script src="/assets/js/api.js"></script>
<script src="/assets/js/app.js"></script>
<?= $extraScripts ?>

<script>
    // Global scroll listener for navbar
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            nav?.classList.add('scrolled');
        } else {
            nav?.classList.remove('scrolled');
        }
    });
</script>

</body>

</html>