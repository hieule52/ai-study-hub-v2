<?php
// ── Admin Layout Shared Sidebar ──
// Usage: require __DIR__ . '/../../layouts/admin_sidebar.php';
$currentPath = $_SERVER['REQUEST_URI'];
$isActive = fn(string $path) => (strpos($currentPath, $path) !== false) ? 'active' : '';
$adminUser = null;
try {
    // Try to get user from localStorage via header — will be resolved client-side
} catch (\Throwable $e) {}
?>

<aside class="admin-sidebar">
    <!-- Brand -->
    <a href="/admin/dashboard" class="admin-brand">
        <div class="admin-brand-icon">A</div>
        <div class="admin-brand-text">
            <div class="admin-brand-title">AI Study Hub</div>
            <div class="admin-brand-badge">ADMIN</div>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-label" data-i18n="adm_nav_label_overview">Tổng quan</div>

        <a href="/admin/dashboard" class="admin-nav-item <?= $isActive('/admin/dashboard') ?>">
            <i class="fas fa-chart-line"></i>
            <span data-i18n="adm_nav_dashboard">Bảng điều khiển</span>
        </a>

        <div class="admin-nav-label" data-i18n="adm_nav_label_manage">Quản lý</div>

        <a href="/admin/courses" class="admin-nav-item <?= $isActive('/admin/courses') ?>">
            <i class="fas fa-book-open"></i>
            <span data-i18n="adm_nav_courses">Khóa học</span>
            <span class="admin-nav-badge" id="sidebarPendingBadge" style="display:none;">0</span>
        </a>

        <a href="/admin/users" class="admin-nav-item <?= $isActive('/admin/users') ?>">
            <i class="fas fa-users"></i>
            <span data-i18n="adm_nav_users">Người dùng</span>
        </a>

        <a href="/admin/vip" class="admin-nav-item <?= $isActive('/admin/vip') ?>">
            <i class="fas fa-receipt"></i>
            <span data-i18n="adm_nav_vip">Doanh thu ghi danh</span>
        </a>

        <a href="/admin/notifications" class="admin-nav-item <?= $isActive('/admin/notifications') ?>">
            <i class="fas fa-bell"></i>
            <span data-i18n="adm_nav_notifications">Thông báo kiểm duyệt</span>
            <span class="admin-nav-badge" id="sidebarNotifBadge" style="display:none; background: #ef4444; box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);">0</span>
        </a>

        <div class="admin-nav-label" data-i18n="adm_nav_label_system">Hệ thống</div>

        <a href="/admin/logs" class="admin-nav-item <?= $isActive('/admin/logs') ?>">
            <i class="fas fa-terminal"></i>
            <span data-i18n="adm_nav_logs">Nhật ký hệ thống</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="admin-sidebar-footer">
        <button onclick="App.logout()" class="admin-logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span data-i18n="nav_logout">Đăng xuất</span>
        </button>
    </div>
</aside>

<script>
// Load pending course badge and moderation notifications
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const resStats = await window.api.get('/admin/stats');
        const pending = resStats.data?.pending_courses || 0;
        const badgePending = document.getElementById('sidebarPendingBadge');
        if (badgePending && pending > 0) {
            badgePending.innerText = pending;
            badgePending.style.display = 'inline-flex';
        }
    } catch(e) {}

    try {
        const resNotifs = await window.api.get('/admin/notifications');
        const unreadCount = resNotifs.data?.filter(n => !n.is_read).length || 0;
        const badgeNotif = document.getElementById('sidebarNotifBadge');
        if (badgeNotif && unreadCount > 0) {
            badgeNotif.innerText = unreadCount;
            badgeNotif.style.display = 'inline-flex';
        }
    } catch(e) {}
});
</script>
