<?php
// Default values
$pageTitle = $pageTitle ?? 'AI Study Hub® — Nền tảng học tập AI';
$actor = $actor ?? 'guest'; // guest, auth, student, teacher, admin
$extraHead = $extraHead ?? '';
$noSidebar = $noSidebar ?? false;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Core design system (always loaded) -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= time() ?>">
    <!-- Dashboard layout CSS (only for authenticated roles if sidebar is shown) -->
    <?php if (in_array($actor, ['student', 'teacher', 'admin']) && !($noSidebar ?? false)): ?>
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css?v=<?= time() ?>">
    <?php endif; ?>
    <!-- Per-page extra head (CSS, meta, etc.) -->
    <?= $extraHead ?>
    <style>
        .sidebar-link.active {
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-primary);
            border-left: 2px solid var(--text-primary);
            font-weight: 600;
        }
    </style>
</head>

<body class="cinematic-theme <?= ($noSidebar ?? false) ? 'no-sidebar-mode' : '' ?>">

    <?php if ($actor === 'auth'): ?>
        <div class="auth-wrapper">
        <?php else: ?>

            <?php if (!($noSidebar ?? false)): ?>
            <nav class="navbar" id="mainNavbar">
                <div class="navbar-container">
                    <a href="/" class="nav-brand" style="display: flex; align-items: center; gap: 6px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="url(#logo-sparkle)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <defs>
                                <linearGradient id="logo-sparkle" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#fff" />
                                    <stop offset="100%" stop-color="#818cf8" />
                                </linearGradient>
                            </defs>
                            <path d="M12 3l1.912 5.813a2 2 0 001.275 1.275L21 12l-5.813 1.912a2 2 0 00-1.275 1.275L12 21l-1.912-5.813a2 2 0 00-1.275-1.275L3 12l5.813-1.912a2 2 0 001.275-1.275L12 3z"/>
                        </svg>
                        AI Study Hub
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: -8px;">
                            <path d="M12 3l1.912 5.813a2 2 0 001.275 1.275L21 12l-5.813 1.912a2 2 0 00-1.275 1.275L12 21l-1.912-5.813a2 2 0 00-1.275-1.275L3 12l5.813-1.912a2 2 0 001.275-1.275L12 3z"/>
                        </svg>
                    </a>

                    <ul class="nav-menu">
                        <li><a href="/" class="nav-link" data-i18n="nav_home">Trang chủ</a></li>
                        <li><a href="/courses.php" class="nav-link" data-i18n="nav_courses">Khóa học</a></li>
                        <li><a href="/about.php" class="nav-link" data-i18n="nav_about">Giới thiệu</a></li>
                        <li><a href="javascript:void(0)" class="nav-link" data-i18n="nav_student_ai"
                                onclick="App.checkAuthAndGo('/student/ai-chat.php', 'Vui lòng đăng nhập để sử dụng Gia sư AI')">Gia sư AI</a></li>
                        <li><a href="javascript:void(0)" class="nav-link" data-i18n="nav_student_certificates"
                                onclick="App.checkAuthAndGo('/student/certificates.php', 'Vui lòng đăng nhập để xem chứng chỉ')">Chứng chỉ</a></li>
                    </ul>

                    <div class="flex items-center gap-6" id="nav-right">
                        <div id="user-menu" class="flex items-center gap-6">
                            <?php if ($actor === 'guest'): ?>
                                <a href="/login.php" class="nav-link" data-i18n="nav_login"
                                    style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Đăng nhập</a>
                                <a href="/register.php" class="btn btn-primary" data-i18n="nav_start"
                                    style="border-radius: 100px; padding: 0.6rem 1.5rem; font-size: 0.8rem; font-weight: 700;">Bắt
                                    đầu học</a>
                            <?php endif; ?>
                        </div>

                        <?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
                            <div style="position: relative;">
                                <div class="notif-bell" id="notifBell" onclick="toggleNotifDropdown()">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                    </svg>
                                    <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                                </div>
                                <div class="notif-dropdown" id="notifDropdown">
                                    <div class="notif-dropdown-header">
                                        <span style="color: var(--text-primary);" data-i18n="nav_hub_menu">Thông báo</span>
                                        <button onclick="markAllNotifRead()"
                                            style="background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size:0.75rem; opacity: 0.6;">Mark
                                            all read</button>
                                    </div>
                                    <div class="notif-list" id="notifList"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
            <?php endif; ?>

            <?php 
            $showDashboardLayout = in_array($actor, ['student', 'teacher', 'admin']) && !($noSidebar ?? false);
            if ($showDashboardLayout): 
            ?>
                <div class="dashboard-layout">
                    <aside class="sidebar">
                        <ul class="sidebar-nav">
                            <?php
                            $currentPath = $_SERVER['REQUEST_URI'];
                            $isActive = fn(string $path) => (strpos($currentPath, $path) !== false) ? 'active' : '';
                            ?>
                            <li>
                                <a href="/student/dashboard.php" class="sidebar-link <?= $isActive('/student/dashboard') ?>"
                                    data-i18n="nav_student_dashboard">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span>Tổng quan</span>
                                </a>
                            </li>
                            <li>
                                <a href="/student/my-courses.php" class="sidebar-link <?= $isActive('/student/my-courses') ?>"
                                    data-i18n="nav_student_courses">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a4 4 0 0 0-4-4H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a4 4 0 0 1 4-4h6z"></path>
                                    </svg>
                                    <span>Khóa học của tôi</span>
                                </a>
                            </li>
                            <li>
                                <a href="/student/ai-chat.php" class="sidebar-link <?= $isActive('/student/ai-chat') ?>"
                                    data-i18n="nav_student_ai">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z">
                                        </path>
                                        <path
                                            d="M12 6a1 1 0 1 0 1 1 1 1 0 0 0-1-1zm0 4a1 1 0 1 0 1 1 1 1 0 0 0-1-1zm0 4a1 1 0 1 0 1 1 1 1 0 0 0-1-1z">
                                        </path>
                                    </svg>
                                    <span>Gia sư AI</span>
                                </a>
                            </li>
                            <li>
                                <a href="/student/chat.php" class="sidebar-link <?= $isActive('/student/chat') ?>"
                                    data-i18n="nav_student_chat">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    <span>Tin nhắn</span>
                                </a>
                            </li>
                            <li>
                                <a href="/student/certificates.php"
                                    class="sidebar-link <?= $isActive('/student/certificates') ?>"
                                    data-i18n="nav_student_certificates">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                    <span>Chứng chỉ</span>
                                </a>
                            </li>
                        <?php elseif ($actor === 'teacher'): ?>
                            <li>
                                <a href="/teacher/dashboard.php" class="sidebar-link <?= $isActive('/teacher/dashboard') ?>"
                                    data-i18n="nav_teacher_dashboard">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span>Tổng quan</span>
                                </a>
                            </li>
                            <li>
                                <a href="/teacher/dashboard.php#courses-container" class="sidebar-link"
                                    data-i18n="nav_teacher_courses">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <span>Quản lý khóa học</span>
                                </a>
                            </li>
                            <li>
                                <a href="/teacher/students.php" class="sidebar-link <?= $isActive('/teacher/students') ?>"
                                    data-i18n="nav_teacher_students">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span>Học viên</span>
                                </a>
                            </li>
                            <li>
                                <a href="/teacher/chat.php" class="sidebar-link <?= $isActive('/teacher/chat') ?>"
                                    data-i18n="nav_teacher_chat">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    <span>Hỗ trợ</span>
                                </a>
                            </li>
                        <?php elseif ($actor === 'admin'): ?>
                            <li>
                                <a href="/admin/dashboard.php" class="sidebar-link <?= $isActive('/admin/dashboard') ?>"
                                    data-i18n="nav_admin_dashboard">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path
                                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                        </path>
                                    </svg>
                                    <span>Hệ thống</span>
                                </a>
                            </li>
                            <li>
                                <a href="/admin/users.php" class="sidebar-link <?= $isActive('/admin/users') ?>"
                                    data-i18n="nav_admin_users">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span>Người dùng</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </aside>
                <main class="main-content">
                <?php endif; ?>