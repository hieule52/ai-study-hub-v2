<?php
// Default values
$pageTitle = $pageTitle ?? 'AI Study Hub - Learning Management System';
$actor = $actor ?? 'guest'; // guest, auth, student, teacher, admin
$extraHead = $extraHead ?? '';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Phải load /_url để pass .htaccess rewrite rule cho thư mục public -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <?= $extraHead ?>
</head>

<body>

    <?php if ($actor === 'auth'): ?>
        <!-- Layout dành cho trang Auth -->
        <div class="auth-wrapper">
        <?php else: ?>

            <!-- Header Navbar -->
            <nav class="navbar" <?php if (in_array($actor, ['student', 'teacher', 'admin']))
                echo 'style="z-index: 100;"'; ?>>
                <div class="container navbar-container">
                    <a href="/" class="nav-brand">AI <span class="text-gradient">Study Hub</span></a>

                    <?php if ($actor === 'guest'): ?>
                        <ul class="nav-menu">
                            <li><a href="/" class="nav-link" data-i18n="nav_home">Trang chủ</a></li>
                            <li><a href="/#courses" class="nav-link" data-i18n="nav_courses">Các Khóa học</a></li>
                            <li><a href="/#about" class="nav-link" data-i18n="nav_about">Giới thiệu</a></li>
                        </ul>
                    <?php endif; ?>

                    <div id="user-menu" class="flex items-center gap-4">
                        <!-- Data sẽ được Inject bằng app.js dựa vào auth token -->
                    </div>
                </div>
            </nav>

            <?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
                <div class="dashboard-layout">
                    <!-- Sidebar -->
                    <aside class="sidebar">
                        <ul class="sidebar-nav">
                            <?php if ($actor === 'student'): ?>
                                <li><a href="/student/dashboard.php" class="sidebar-link" data-i18n="nav_student_dashboard">📊 Tổng quan học tập</a></li>
                                <li><a href="/student/dashboard.php#enrolled-course-container" class="sidebar-link" data-i18n="nav_student_courses">📚 Khóa học của
                                        tôi</a></li>
                                <li><a href="/student/ai-chat.php" class="sidebar-link" data-i18n="nav_student_ai">🤖 Gia Sư AI (AI Tutor)</a></li>
                                <li><a href="/student/chat.php" class="sidebar-link" data-i18n="nav_student_chat">💬 Cửa sổ Chat (Hội nhóm)</a></li>
                            <?php elseif ($actor === 'teacher'): ?>
                                <li><a href="/teacher/dashboard.php" class="sidebar-link" data-i18n="nav_teacher_dashboard">📊 Tổng quan Giảng dạy</a></li>
                                <li><a href="/teacher/dashboard.php#courses-container" class="sidebar-link" data-i18n="nav_teacher_courses">📚 Quản lý Khóa học</a>
                                </li>
                                <li><a href="/teacher/students.php" class="sidebar-link" data-i18n="nav_teacher_students">👨‍🎓 Học viên của tôi</a></li>
                                <li><a href="/teacher/chat.php" class="sidebar-link" data-i18n="nav_teacher_chat">💬 Hỗ trợ học viên</a></li>
                            <?php elseif ($actor === 'admin'): ?>
                                <li><a href="/admin/dashboard.php" class="sidebar-link" data-i18n="nav_admin_dashboard">📊 Tổng quan Hệ thống</a></li>
                                <li><a href="/admin/users.php" class="sidebar-link" data-i18n="nav_admin_users">👥 Quản lý Người dùng</a></li>
                                <li><a href="/admin/courses.php" class="sidebar-link" data-i18n="nav_teacher_courses">📚 Quản lý Khóa học</a></li>
                                <li><a href="/admin/vip.php" class="sidebar-link" data-i18n="nav_admin_vip">💎 Giao dịch VIP</a></li>
                                <li><a href="/admin/logs.php" class="sidebar-link" data-i18n="nav_admin_logs">📝 Audit Logs</a></li>
                            <?php endif; ?>
                            <?php if ($actor === 'student'): ?>
                                <li><a href="/" class="sidebar-link" data-i18n="nav_back_home">🚪 Về trang chủ</a></li>
                            <?php endif; ?>
                        </ul>
                    </aside>

                    <!-- Main Content -->
                    <main class="main-content">
                    <?php endif; ?>

                <?php endif; ?>