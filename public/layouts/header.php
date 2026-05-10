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
    <style>
        /* ── Notification Bell ── */
        .notif-bell {
            position: relative;
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.2s;
            padding: 0.25rem;
        }
        .notif-bell:hover { color: var(--text-primary); }
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            line-height: 1;
        }
        /* ── Notification Dropdown ── */
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 340px;
            max-height: 420px;
            background: var(--bg-surface);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            z-index: 9999;
            overflow: hidden;
            display: none;
            flex-direction: column;
        }
        .notif-dropdown.open { display: flex; }
        .notif-dropdown-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .notif-list { overflow-y: auto; flex: 1; max-height: 320px; }
        .notif-item {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .notif-item:hover { background: rgba(79,70,229,0.08); }
        .notif-item.unread { background: rgba(79,70,229,0.05); border-left: 3px solid var(--primary); }
        .notif-item-icon { font-size: 1.4rem; flex-shrink: 0; margin-top: 2px; }
        .notif-item-body { flex: 1; min-width: 0; }
        .notif-item-title { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
        .notif-item-msg { font-size: 0.78rem; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .notif-item-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 3px; }
        /* ── Sidebar active link ── */
        .sidebar-link.active {
            background: rgba(79,70,229,0.15);
            color: var(--primary);
            border-left: 3px solid var(--primary);
        }
    </style>
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

                    <div class="flex items-center gap-4">
                        <!-- Notification Bell (chỉ hiển thị khi đã login) -->
                        <?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
                        <div style="position: relative;">
                            <div class="notif-bell" id="notifBell" onclick="toggleNotifDropdown()" title="Thông báo">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                            </div>
                            <!-- Notification Dropdown -->
                            <div class="notif-dropdown" id="notifDropdown">
                                <div class="notif-dropdown-header">
                                    <span>🔔 Thông báo</span>
                                    <button onclick="markAllNotifRead()" style="background:none; border:none; color:var(--primary); cursor:pointer; font-size:0.8rem;">Đánh dấu đọc</button>
                                </div>
                                <div class="notif-list" id="notifList">
                                    <div style="text-align:center; padding:2rem; color:var(--text-muted); font-size:0.85rem;">Đang tải...</div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div id="user-menu" class="flex items-center gap-4">
                            <!-- Data sẽ được Inject bằng app.js dựa vào auth token -->
                        </div>
                    </div>
                </div>
            </nav>

            <?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
                <div class="dashboard-layout">
                    <!-- Sidebar -->
                    <aside class="sidebar">
                        <ul class="sidebar-nav">
                            <?php
                            $currentPath = $_SERVER['REQUEST_URI'];
                            $isActive = fn(string $path) => (strpos($currentPath, $path) !== false) ? 'active' : '';
                            ?>
                            <?php if ($actor === 'student'): ?>
                                <li><a href="/student/dashboard.php" class="sidebar-link <?= $isActive('/student/dashboard') ?>" data-i18n="nav_student_dashboard">📊 Tổng quan học tập</a></li>
                                <li><a href="/student/dashboard.php#enrolled-course-container" class="sidebar-link" data-i18n="nav_student_courses">📚 Khóa học của tôi</a></li>
                                <li><a href="/student/ai-chat.php" class="sidebar-link <?= $isActive('/student/ai-chat') ?>" data-i18n="nav_student_ai">🤖 Gia Sư AI (AI Tutor)</a></li>
                                <li><a href="/student/chat.php" class="sidebar-link <?= $isActive('/student/chat') ?>" data-i18n="nav_student_chat">💬 Chat với Giảng viên</a></li>
                                <li><a href="/student/certificates.php" class="sidebar-link <?= $isActive('/student/certificates') ?>">🎓 Chứng chỉ của tôi</a></li>
                            <?php elseif ($actor === 'teacher'): ?>
                                <li><a href="/teacher/dashboard.php" class="sidebar-link <?= $isActive('/teacher/dashboard') ?>" data-i18n="nav_teacher_dashboard">📊 Tổng quan Giảng dạy</a></li>
                                <li><a href="/teacher/dashboard.php#courses-container" class="sidebar-link" data-i18n="nav_teacher_courses">📚 Quản lý Khóa học</a></li>
                                <li><a href="/teacher/students.php" class="sidebar-link <?= $isActive('/teacher/students') ?>" data-i18n="nav_teacher_students">👨‍🎓 Học viên của tôi</a></li>
                                <li><a href="/teacher/chat.php" class="sidebar-link <?= $isActive('/teacher/chat') ?>" data-i18n="nav_teacher_chat">💬 Hỗ trợ học viên</a></li>
                            <?php elseif ($actor === 'admin'): ?>
                                <li><a href="/admin/dashboard.php" class="sidebar-link <?= $isActive('/admin/dashboard') ?>" data-i18n="nav_admin_dashboard">📊 Tổng quan Hệ thống</a></li>
                                <li><a href="/admin/users.php" class="sidebar-link <?= $isActive('/admin/users') ?>" data-i18n="nav_admin_users">👥 Quản lý Người dùng</a></li>
                                <li><a href="/admin/courses.php" class="sidebar-link <?= $isActive('/admin/courses') ?>" data-i18n="nav_teacher_courses">📚 Quản lý Khóa học</a></li>
                                <li><a href="/admin/vip.php" class="sidebar-link <?= $isActive('/admin/vip') ?>" data-i18n="nav_admin_vip">💎 Quản lý Ghi danh</a></li>
                                <li><a href="/admin/logs.php" class="sidebar-link <?= $isActive('/admin/logs') ?>" data-i18n="nav_admin_logs">📝 Audit Logs</a></li>
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

<!-- Notification JS (chỉ load khi đã đăng nhập) -->
<?php if (in_array($actor, ['student', 'teacher', 'admin'])): ?>
<script>
(function() {
    let notifOpen = false;
    let notifLoaded = false;

    window.toggleNotifDropdown = function() {
        const dropdown = document.getElementById('notifDropdown');
        notifOpen = !notifOpen;
        dropdown.classList.toggle('open', notifOpen);
        if (notifOpen && !notifLoaded) {
            loadNotifications();
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        loadNotifications();
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        if (notifOpen && bell && !bell.contains(e.target) && !dropdown.contains(e.target)) {
            notifOpen = false;
            dropdown.classList.remove('open');
        }
    });

    async function loadNotifications() {
        if (!window.api) return;
        try {
            const res = await window.api.get('/notifications?limit=10');
            const { unread, items } = res.data;
            notifLoaded = true;

            // Update badge
            const badge = document.getElementById('notifBadge');
            if (unread > 0) {
                badge.style.display = 'flex';
                badge.textContent = unread > 9 ? '9+' : unread;
            } else {
                badge.style.display = 'none';
            }

            // Render list
            const list = document.getElementById('notifList');
            if (!items || items.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.85rem;">Không có thông báo nào.</div>';
                return;
            }

            const iconMap = {
                'certificate': '🎓',
                'success': '✅',
                'warning': '⚠️',
                'course_approved': '📚',
                'info': 'ℹ️'
            };

            list.innerHTML = items.map(n => {
                const icon = iconMap[n.type] || 'ℹ️';
                const timeAgo = formatTimeAgo(n.created_at);
                return `<div class="notif-item ${n.is_read == 0 ? 'unread' : ''}">
                    <div class="notif-item-icon">${icon}</div>
                    <div class="notif-item-body">
                        <div class="notif-item-title">${escapeHtmlNotif(n.title)}</div>
                        <div class="notif-item-msg">${escapeHtmlNotif(n.message)}</div>
                        <div class="notif-item-time">${timeAgo}</div>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            // Silently fail — notification is non-critical
        }
    }

    window.markAllNotifRead = async function() {
        try {
            await window.api.put('/notifications/read-all', {});
            document.getElementById('notifBadge').style.display = 'none';
            document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        } catch (e) {}
    };

    function formatTimeAgo(dateStr) {
        const now = new Date();
        const d = new Date(dateStr);
        const diff = Math.floor((now - d) / 1000);
        if (diff < 60) return 'Vừa xong';
        if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
        if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
        return Math.floor(diff / 86400) + ' ngày trước';
    }

    function escapeHtmlNotif(text) {
        if (!text) return '';
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    // Auto-load badge count after api.js initializes
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(async function() {
            if (!window.api || !window.api.getToken()) return;
            try {
                const res = await window.api.get('/notifications?limit=1');
                const unread = res?.data?.unread || 0;
                const badge = document.getElementById('notifBadge');
                if (unread > 0 && badge) {
                    badge.style.display = 'flex';
                    badge.textContent = unread > 9 ? '9+' : unread;
                }
            } catch (e) {}
        }, 500);
    });
})();
</script>
<?php endif; ?>