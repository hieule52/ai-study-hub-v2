<?php
$pageTitle = 'Thông báo - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">
              <script src="/assets/js/teacher/notifications.js?v=' . time() . '" defer></script>
              <style>
                  .notif-page-card {
                      background: var(--glass-bg);
                      backdrop-filter: blur(20px);
                      -webkit-backdrop-filter: blur(20px);
                      border: 1px solid var(--glass-border);
                      border-radius: var(--radius-lg);
                      padding: 2.5rem;
                      margin-top: 1.5rem;
                      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                  }
                  .filter-group {
                      display: flex;
                      gap: 0.75rem;
                      margin-bottom: 2rem;
                      border-bottom: 1px solid var(--glass-border);
                      padding-bottom: 1.25rem;
                  }
                  .filter-chip {
                      padding: 0.6rem 1.25rem;
                      border-radius: 100px;
                      border: 1px solid var(--glass-border);
                      background: rgba(255,255,255,0.02);
                      color: var(--text-secondary);
                      cursor: pointer;
                      font-weight: 600;
                      font-size: 0.85rem;
                      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                  }
                  .filter-chip:hover {
                      background: rgba(255,255,255,0.06);
                      color: var(--text-primary);
                      border-color: rgba(255,255,255,0.2);
                  }
                  .filter-chip.active {
                      background: var(--primary);
                      border-color: var(--primary);
                      color: #fff;
                      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
                  }
                  .notif-page-list {
                      display: flex;
                      flex-direction: column;
                      gap: 0.75rem;
                  }
                  .notif-page-item {
                      padding: 1.5rem;
                      background: rgba(255, 255, 255, 0.01);
                      border: 1px solid var(--glass-border);
                      border-radius: var(--radius-md);
                      display: flex;
                      gap: 1.25rem;
                      align-items: flex-start;
                      transition: all 0.3s ease;
                      cursor: pointer;
                      position: relative;
                  }
                  .notif-page-item:hover {
                      background: rgba(255, 255, 255, 0.03);
                      border-color: var(--glass-border-hi);
                      transform: translateX(4px);
                  }
                  .notif-page-item.unread {
                      background: rgba(99, 102, 241, 0.03);
                      border-left: 4px solid var(--primary);
                      border-color: rgba(99, 102, 241, 0.15);
                  }
                  .notif-page-icon {
                      font-size: 1.5rem;
                      width: 48px;
                      height: 48px;
                      border-radius: 50%;
                      background: rgba(255, 255, 255, 0.03);
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      flex-shrink: 0;
                      border: 1px solid var(--glass-border);
                  }
                  .notif-page-item.unread .notif-page-icon {
                      background: rgba(99, 102, 241, 0.1);
                      border-color: rgba(99, 102, 241, 0.2);
                  }
                  .notif-page-content {
                      flex: 1;
                  }
                  .notif-page-title {
                      font-weight: 700;
                      font-size: 1rem;
                      color: var(--text-primary);
                      margin-bottom: 0.35rem;
                  }
                  .notif-page-msg {
                      font-size: 0.9rem;
                      color: var(--text-secondary);
                      line-height: 1.5;
                  }
                  .notif-page-time {
                      font-size: 0.75rem;
                      opacity: 0.4;
                      margin-top: 0.75rem;
                      display: flex;
                      align-items: center;
                      gap: 4px;
                  }
                  .btn-action-read {
                      background: none;
                      border: none;
                      color: var(--text-muted);
                      cursor: pointer;
                      opacity: 0.4;
                      transition: 0.2s;
                      font-size: 0.9rem;
                      padding: 6px;
                  }
                  .btn-action-read:hover {
                      opacity: 1;
                      color: var(--primary);
                  }
              </style>';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="teacher-content">
        <header class="dash-header" style="margin-bottom: 2rem;">
            <div class="dash-title-group">
                <h1 data-i18n="nav_hub_menu">Thông báo</h1>
                <p>Xem toàn bộ thông báo và cập nhật mới từ hệ thống</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div style="position: relative; display: none;">
                    <button class="notif-bell-btn" id="teacherNotifBell" onclick="toggleTeacherNotif(event)">
                        <i class="fas fa-bell"></i>
                        <span id="teacherNotifBadge" class="notif-badge" style="display: none;">0</span>
                    </button>
                    <div id="teacherNotifDropdown" class="teacher-notif-dropdown" style="display: none;">
                        <div class="notif-dropdown-header">
                            <span data-i18n="nav_hub_menu">Thông báo</span>
                            <button onclick="markTeacherNotifRead(event)" class="btn-mark-read" data-i18n="nav_hub_mark_read">Xác nhận tất cả</button>
                        </div>
                        <div id="teacherNotifList" class="notif-dropdown-list"></div>
                    </div>
                </div>

                <button onclick="markAllAsReadPage()" class="btn btn-outline" style="border-radius: 100px; padding: 0.8rem 1.8rem;">
                    <i class="fas fa-check-double" style="margin-right: 6px;"></i> Đánh dấu tất cả đã đọc
                </button>
            </div>
        </header>

        <div class="notif-page-card">
            <div class="filter-group">
                <button class="filter-chip active" onclick="setNotifFilter('all', this)">Tất cả</button>
                <button class="filter-chip" onclick="setNotifFilter('unread', this)">Chưa đọc</button>
                <button class="filter-chip" onclick="setNotifFilter('read', this)">Đã đọc</button>
            </div>

            <div id="notifPageList" class="notif-page-list">
                <div style="text-align: center; padding: 3rem; opacity: 0.5;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 1rem;"></i>
                    <p>Đang tải thông báo...</p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
let allNotifications = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['teacher', 'admin']);
    if (!user) return;
    if (window.I18n) window.I18n.render();

    await loadNotificationsPage();
});

async function loadNotificationsPage() {
    try {
        const res = await window.api.get('/notifications');
        allNotifications = res.data.items || [];
        renderNotificationsPage();
    } catch (err) {
        console.error(err);
        document.getElementById('notifPageList').innerHTML = `<div style="text-align:center; padding:3rem; color:var(--danger);"><i class="fas fa-exclamation-circle" style="font-size:2rem; margin-bottom:1rem;"></i><p>Lỗi tải thông báo: ${err.message}</p></div>`;
    }
}

function setNotifFilter(filter, el) {
    currentFilter = filter;
    document.querySelectorAll('.filter-chip').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');
    renderNotificationsPage();
}

function renderNotificationsPage() {
    const listContainer = document.getElementById('notifPageList');
    if (!listContainer) return;

    let filtered = allNotifications;
    if (currentFilter === 'unread') {
        filtered = allNotifications.filter(n => !n.is_read);
    } else if (currentFilter === 'read') {
        filtered = allNotifications.filter(n => n.is_read);
    }

    if (filtered.length === 0) {
        let emptyMsg = 'Bạn không có thông báo nào.';
        if (currentFilter === 'unread') emptyMsg = 'Bạn không có thông báo chưa đọc nào.';
        if (currentFilter === 'read') emptyMsg = 'Bạn không có thông báo đã đọc nào.';

        listContainer.innerHTML = `
            <div style="text-align: center; padding: 4rem 2rem; opacity: 0.3;">
                <i class="far fa-bell-slash" style="font-size: 3rem; margin-bottom: 1.5rem;"></i>
                <p style="font-size: 1rem; font-weight: 500;">${emptyMsg}</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = filtered.map(n => {
        const unreadClass = n.is_read ? '' : 'unread';
        const formattedTime = new Date(n.created_at).toLocaleString('vi-VN');
        const icon = n.icon || 'ℹ️';

        let clickAttr = '';
        if (n.type === 'chat' && n.data && n.data.sender_id) {
            clickAttr = `onclick="handleNotifClick(${n.id}, '/teacher/chat?user_id=${n.data.sender_id}')"`;
        } else {
            clickAttr = `onclick="handleNotifClick(${n.id}, null)"`;
        }

        return `
            <div class="notif-page-item ${unreadClass}" ${clickAttr}>
                <div class="notif-page-icon">${icon}</div>
                <div class="notif-page-content">
                    <div class="notif-page-title">${escapeHtml(n.title || 'Thông báo')}</div>
                    <div class="notif-page-msg">${escapeHtml(n.message)}</div>
                    <div class="notif-page-time">
                        <i class="far fa-clock"></i> ${formattedTime}
                    </div>
                </div>
                ${!n.is_read ? `
                    <button class="btn-action-read" onclick="event.stopPropagation(); markSingleRead(${n.id})" title="Đánh dấu đã đọc">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
            </div>
        `;
    }).join('');
}

async function markSingleRead(id) {
    try {
        await window.api.put(`/notifications/${id}/read`);
        // Update local state
        allNotifications = allNotifications.map(n => n.id === id ? { ...n, is_read: 1 } : n);
        renderNotificationsPage();
        if (window.loadNotifications) window.loadNotifications(); // Sync small dropdown if on page
    } catch(err) { console.error(err); }
}

async function handleNotifClick(id, redirectUrl) {
    // Mark as read
    const notif = allNotifications.find(n => n.id === id);
    if (notif && !notif.is_read) {
        await markSingleRead(id);
    }
    if (redirectUrl) {
        window.location.href = redirectUrl;
    }
}

async function markAllAsReadPage() {
    try {
        await window.api.put('/notifications/read-all');
        App.showToast('Đã đánh dấu tất cả thông báo là đã đọc', 'success');
        await loadNotificationsPage();
        if (window.loadNotifications) window.loadNotifications(); // Sync small dropdown
    } catch(err) {
        App.showToast(err.message, 'error');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
