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
        <div class="admin-nav-label">Tổng quan</div>

        <a href="/admin/dashboard" class="admin-nav-item <?= $isActive('/admin/dashboard') ?>">
            <i class="fas fa-chart-line"></i>
            <span>Bảng điều khiển</span>
        </a>

        <div class="admin-nav-label">Quản lý</div>

        <a href="/admin/courses" class="admin-nav-item <?= $isActive('/admin/courses') ?>">
            <i class="fas fa-book-open"></i>
            <span>Khóa học</span>
            <span class="admin-nav-badge" id="sidebarPendingBadge" style="display:none;">0</span>
        </a>

        <a href="/admin/users" class="admin-nav-item <?= $isActive('/admin/users') ?>">
            <i class="fas fa-users"></i>
            <span>Người dùng</span>
        </a>

        <a href="/admin/vip" class="admin-nav-item <?= $isActive('/admin/vip') ?>">
            <i class="fas fa-receipt"></i>
            <span>Doanh thu ghi danh</span>
        </a>

        <div class="admin-nav-label">Hệ thống</div>

        <a href="/admin/logs" class="admin-nav-item <?= $isActive('/admin/logs') ?>">
            <i class="fas fa-terminal"></i>
            <span>Nhật ký hệ thống</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="admin-sidebar-footer">
        <button onclick="App.logout()" class="admin-logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
        </button>
    </div>
</aside>

<script>
// Load pending course badge
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await window.api.get('/admin/stats');
        const pending = res.data?.pending_courses || 0;
        const badge = document.getElementById('sidebarPendingBadge');
        if (badge && pending > 0) {
            badge.innerText = pending;
            badge.style.display = 'inline-flex';
        }
    } catch(e) {}
});
</script>
