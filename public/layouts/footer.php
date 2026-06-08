<?php
$actor = $actor ?? 'guest';
$extraScripts = $extraScripts ?? '';

// 1. Dynamic layout fallbacks for $footerMode
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$normalizedPath = rtrim(parse_url($requestUri, PHP_URL_PATH), '/');

if (!isset($footerMode)) {
    if ($actor === 'teacher' || $actor === 'admin') {
        $footerMode = 'none';
    } elseif ($actor === 'student') {
        // Learning/internal pages: learning, quiz, lesson, ai-tutor, chat, ai-chat, payment, course-completed
        $isLearningPage = (
            strpos($normalizedPath, '/student/learning') === 0 ||
            strpos($normalizedPath, '/student/lesson') === 0 ||
            strpos($normalizedPath, '/student/quiz') === 0 ||
            strpos($normalizedPath, '/student/ai-tutor') === 0 ||
            strpos($normalizedPath, '/student/chat') === 0 ||
            strpos($normalizedPath, '/student/ai-chat') === 0 ||
            strpos($normalizedPath, '/student/payment') === 0 ||
            strpos($normalizedPath, '/student/course-completed') === 0
        );
        $footerMode = $isLearningPage ? 'none' : 'mini';
    } else {
        $footerMode = 'full';
    }
}

// Map the old flags for backwards compatibility
if (isset($noFooter) && $noFooter) {
    $footerMode = 'none';
} elseif (isset($miniFooter) && $miniFooter) {
    $footerMode = 'mini';
}
?>

<?php
// 2. Layout Closing Tags (for structural containers opened in header.php)
if (in_array($actor, ['student', 'teacher', 'admin']) && !($noSidebar ?? false)) {
    echo '</main> <!-- End main-content -->';
    echo '</div> <!-- End dashboard-layout -->';
} elseif ($actor === 'auth') {
    echo '</div> <!-- End auth-wrapper -->';
}
?>

<?php
// 3. Footer Rendering
if ($footerMode === 'mini'): ?>
    <style>
        .mini-footer {
            height: 60px;
            max-height: 60px;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            width: 100%;
            z-index: 10;
        }
        .mini-footer-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.4);
        }
        .mini-footer-brand {
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
        }
        .mini-footer-version {
            font-family: monospace;
            opacity: 0.7;
        }
        @media (max-width: 768px) {
            .mini-footer {
                height: auto;
                max-height: none;
                padding: 1rem 0;
            }
            .mini-footer-container {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>
    <footer class="mini-footer">
        <div class="mini-footer-container">
            <span class="mini-footer-brand">✦ AI Study Hub LMS<sup>®</sup></span>
            <span class="mini-footer-copyright">© 2026 AI Study Hub LMS — Lê Diên Hiếu.</span>
            <span class="mini-footer-version">v2.1.0</span>
        </div>
    </footer>
<?php elseif ($footerMode === 'full'): ?>
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