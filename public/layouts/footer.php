<?php
$actor = $actor ?? 'guest';
$extraScripts = $extraScripts ?? '';
?>

<?php if (in_array($actor, ['student', 'teacher', 'admin']) && !($noSidebar ?? false)): ?>
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

                <p class="footer-description" data-i18n="footer_desc">
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
                    <span class="footer-label" data-i18n="footer_explore">Khám phá</span>

                    <a href="/" data-i18n="nav_home">Trang chủ</a>
                    <a href="/courses" data-i18n="nav_courses">Khóa học</a>
                    <a href="/about" data-i18n="nav_about">Giới thiệu</a>
                </div>

                <div class="footer-links">
                    <span class="footer-label" data-i18n="footer_support">Hỗ trợ</span>

                    <a href="/login" data-i18n="nav_student_ai">AI Tutor</a>
                    <a href="/student/chat" data-i18n="nav_student_chat">Tin nhắn</a>
                    <a href="/terms" data-i18n="footer_terms">Điều khoản</a>
                </div>

            </div>

        </div>

    </footer>
<?php endif; ?>

<!-- Scripts -->
<script src="/assets/js/api.js?v=<?= time() ?>"></script>
<script src="/assets/js/app.js?v=<?= time() ?>"></script>
<script src="/assets/js/i18n.js?v=<?= time() ?>"></script>
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