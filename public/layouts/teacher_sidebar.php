<?php
$currentPath = $_SERVER['REQUEST_URI'];
$isActive = fn(string $path) => (strpos($currentPath, $path) !== false) ? 'active' : '';
?>
<aside class="teacher-sidebar">
    <div class="sidebar-brand" style="padding: 1rem 1.25rem; margin-bottom: 2rem;">
        <div style="font-weight: 800; font-size: 1.2rem; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
            <span style="color: var(--primary);">✦</span> AI Hub
        </div>
    </div>

    <a href="/teacher/dashboard.php" class="sidebar-nav-item <?= $isActive('/teacher/dashboard.php') ?>">
        <i class="fas fa-th-large"></i>
        <span data-i18n="tc_dash_title">Bảng điều khiển</span>
    </a>
    <a href="/teacher/courses.php" class="sidebar-nav-item <?= $isActive('/teacher/courses.php') ?>">
        <i class="fas fa-book"></i>
        <span data-i18n="tc_dash_list_title">Khóa học của tôi</span>
    </a>
    <a href="/teacher/students.php" class="sidebar-nav-item <?= $isActive('/teacher/students.php') ?>">
        <i class="fas fa-user-graduate"></i>
        <span data-i18n="nav_teacher_students">Học viên</span>
    </a>
    <a href="/teacher/chat.php" class="sidebar-nav-item <?= $isActive('/teacher/chat.php') ?>">
        <i class="fas fa-comments"></i>
        <span data-i18n="nav_teacher_chat">Tin nhắn</span>
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
