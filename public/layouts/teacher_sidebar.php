<?php
$currentPath = $_SERVER['REQUEST_URI'];
$isActive = function(string $path) use ($currentPath) {
    if ($path === '/teacher/courses') {
        return (strpos($currentPath, '/teacher/courses') !== false || strpos($currentPath, '/teacher/course-builder') !== false || strpos($currentPath, '/teacher/create-course') !== false) ? 'active' : '';
    }
    return (strpos($currentPath, $path) !== false) ? 'active' : '';
};
?>
<aside class="teacher-sidebar">
    <div class="sidebar-brand" style="padding: 1rem 1.25rem; margin-bottom: 2rem;">
        <div style="font-weight: 800; font-size: 1.25rem; color: var(--text-primary); display: flex; align-items: center; gap: 8px; white-space: nowrap;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="url(#logo-sparkle-sidebar)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                <defs>
                    <linearGradient id="logo-sparkle-sidebar" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fff" />
                        <stop offset="100%" stop-color="#818cf8" />
                    </linearGradient>
                </defs>
                <path d="M12 3l1.912 5.813a2 2 0 001.275 1.275L21 12l-5.813 1.912a2 2 0 00-1.275 1.275L12 21l-1.912-5.813a2 2 0 00-1.275-1.275L3 12l5.813-1.912a2 2 0 001.275-1.275L12 3z"/>
            </svg>
            AI Study Hub
        </div>
    </div>

    <a href="/teacher/dashboard" class="sidebar-nav-item <?= $isActive('/teacher/dashboard') ?>">
        <i class="fas fa-th-large"></i>
        <span data-i18n="tc_dash_title">Bảng điều khiển</span>
    </a>
    <a href="/teacher/courses" class="sidebar-nav-item <?= $isActive('/teacher/courses') ?>">
        <i class="fas fa-book"></i>
        <span data-i18n="tc_dash_list_title">Khóa học của tôi</span>
    </a>
    <a href="/teacher/students" class="sidebar-nav-item <?= $isActive('/teacher/students') ?>">
        <i class="fas fa-user-graduate"></i>
        <span data-i18n="nav_teacher_students">Học viên</span>
    </a>
    <a href="/teacher/chat" class="sidebar-nav-item <?= $isActive('/teacher/chat') ?>">
        <i class="fas fa-comments"></i>
        <span data-i18n="nav_teacher_chat">Tin nhắn</span>
    </a>
    <a href="/teacher/notifications" class="sidebar-nav-item <?= $isActive('/teacher/notifications') ?>">
        <i class="fas fa-bell"></i>
        <span data-i18n="nav_teacher_notifications">Thông báo</span>
    </a>
    <a href="/teacher/profile" class="sidebar-nav-item <?= $isActive('/teacher/profile') ?>">
        <i class="fas fa-user-cog"></i>
        <span data-i18n="nav_teacher_profile">Hồ sơ cá nhân</span>
    </a>
    
    <div style="margin-top: auto; padding: 1.5rem 1rem;">
        <button onclick="App.logout()" class="teacher-logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span data-i18n="nav_logout">Đăng xuất</span>
        </button>
    </div>
</aside>

<style>
.teacher-logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 0.8rem;
    background: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #f87171;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.teacher-logout-btn:hover {
    background: rgba(239, 68, 68, 0.15);
    border-color: #ef4444;
    color: #fff;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
    transform: translateY(-2px);
}
</style>
