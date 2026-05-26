<?php
$pageTitle = 'Thông báo kiểm duyệt — Admin AI Study Hub';
$actor = 'admin';
$noSidebar = true;
$extraHead = '
    <link rel="stylesheet" href="/assets/css/admin/layout.css?v=' . time() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notif-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .filter-tabs {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 1rem;
        }
        .filter-btn {
            padding: 0.6rem 1.25rem;
            border-radius: 100px;
            border: 1px solid var(--glass-border);
            background: rgba(255,255,255,0.02);
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .filter-btn:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
        }
        .notif-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .notif-item {
            padding: 1.25rem 1.5rem;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        .notif-item:hover {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
        }
        .notif-item.unread {
            background: rgba(99, 102, 241, 0.03);
            border-left: 4px solid var(--primary);
            border-color: rgba(99, 102, 241, 0.15) rgba(99, 102, 241, 0.15) rgba(99, 102, 241, 0.15) var(--primary);
        }
        .notif-item.high-priority {
            background: rgba(239, 68, 68, 0.03);
            border-left: 4px solid #ef4444;
            border-color: rgba(239, 68, 68, 0.15) rgba(239, 68, 68, 0.15) rgba(239, 68, 68, 0.15) #ef4444;
        }
        .notif-icon {
            font-size: 1.4rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid var(--glass-border);
        }
        .notif-item.unread .notif-icon {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.2);
        }
        .notif-item.high-priority .notif-icon {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
        }
        .notif-content {
            flex: 1;
        }
        .notif-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .notif-badge-priority {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .notif-badge-priority.high {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .notif-badge-priority.low {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .notif-message {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.5;
        }
        .notif-meta-info {
            font-size: 0.75rem;
            opacity: 0.4;
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-read-single {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.85rem;
            padding: 6px;
        }
        .btn-read-single:hover {
            color: var(--primary);
        }
    </style>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-body">
    <?php require __DIR__ . '/../layouts/admin_sidebar.php'; ?>

    <div class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <h1 class="admin-page-title">Thông báo kiểm duyệt</h1>
                <p class="admin-page-subtitle">Nhật ký thay đổi và kiểm duyệt chất lượng giáo trình khóa học</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button onclick="markAllAsRead()" class="admin-btn admin-btn-ghost" style="border-radius: 100px;">
                    <i class="fas fa-check-double" style="margin-right: 6px;"></i> Đánh dấu tất cả đã đọc
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
            <div class="notif-card">
                <div class="filter-tabs">
                    <button class="filter-btn active" onclick="setFilter('all', this)">Tất cả</button>
                    <button class="filter-btn" onclick="setFilter('unread', this)">Chưa đọc</button>
                    <button class="filter-btn" onclick="setFilter('high', this)">Độ ưu tiên cao (HIGH)</button>
                    <button class="filter-btn" onclick="setFilter('critical', this)">Cập nhật bài học/Quiz</button>
                </div>

                <div id="notifListContainer" class="notif-list">
                    <div style="text-align: center; padding: 3rem; opacity: 0.5;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 1rem;"></i>
                        <p>Đang tải danh sách thông báo...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allNotifications = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['admin']);
    if (!user) return;
    await loadNotifications();
});

async function loadNotifications() {
    try {
        const res = await window.api.get('/admin/notifications');
        allNotifications = res.data || [];
        renderNotifications();
    } catch (err) {
        console.error(err);
        document.getElementById('notifListContainer').innerHTML = `
            <div style="text-align:center; padding:3rem; color:#f87171;">
                <i class="fas fa-exclamation-circle" style="font-size:2rem; margin-bottom:1rem;"></i>
                <p>Lỗi tải thông báo: ${err.message}</p>
            </div>
        `;
    }
}

function setFilter(filter, el) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');
    renderNotifications();
}

function renderNotifications() {
    const container = document.getElementById('notifListContainer');
    if (!container) return;

    let filtered = allNotifications;
    if (currentFilter === 'unread') {
        filtered = allNotifications.filter(n => !n.is_read);
    } else if (currentFilter === 'high') {
        filtered = allNotifications.filter(n => n.priority === 'high');
    } else if (currentFilter === 'critical') {
        filtered = allNotifications.filter(n => n.type === 'critical_update');
    }

    if (filtered.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 4rem 2rem; opacity: 0.3;">
                <i class="far fa-bell-slash" style="font-size: 3rem; margin-bottom: 1.5rem;"></i>
                <p style="font-size: 1rem; font-weight: 500;">Không có thông báo nào phù hợp.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(n => {
        const isUnread = !n.is_read;
        const isHigh = n.priority === 'high';
        const itemClass = (isUnread ? 'unread' : '') + (isHigh ? ' high-priority' : '');
        const timeStr = new Date(n.created_at).toLocaleString('vi-VN');
        const icon = isHigh ? '⚠️' : 'ℹ️';

        const priorityBadge = isHigh 
            ? `<span class="notif-badge-priority high">Khẩn cấp</span>`
            : `<span class="notif-badge-priority low">Thông thường</span>`;

        return `
            <div class="notif-item ${itemClass}" onclick="handleNotifClick(${n.id}, ${n.course_id})">
                <div class="notif-icon">${icon}</div>
                <div class="notif-content">
                    <div class="notif-title">
                        <span>${escapeHtml(n.title)}</span>
                        ${priorityBadge}
                    </div>
                    <div class="notif-message">${escapeHtml(n.message)}</div>
                    <div class="notif-meta-info">
                        <span><i class="fas fa-book"></i> Khóa học: <strong>${escapeHtml(n.course_title)}</strong></span>
                        <span><i class="fas fa-user-tie"></i> Giảng viên: ${escapeHtml(n.teacher_name || 'Hệ thống')}</span>
                        <span><i class="far fa-clock"></i> ${timeStr}</span>
                    </div>
                </div>
                ${isUnread ? `
                    <button class="btn-read-single" onclick="event.stopPropagation(); markSingleRead(${n.id})" title="Đánh dấu đã đọc">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
            </div>
        `;
    }).join('');
}

async function markSingleRead(id) {
    try {
        await window.api.put(`/admin/notifications/${id}/read`);
        allNotifications = allNotifications.map(n => n.id === id ? { ...n, is_read: 1 } : n);
        renderNotifications();
        // Sync sidebar badge
        if (window.loadNotifications) window.loadNotifications();
    } catch(err) { console.error(err); }
}

async function handleNotifClick(id, courseId) {
    // Mark read
    const notif = allNotifications.find(n => n.id === id);
    if (notif && !notif.is_read) {
        await markSingleRead(id);
    }
    // Redirect to course preview with auto-opening change log review mode
    window.location.href = `/admin/preview/${courseId}?review=1`;
}

async function markAllAsRead() {
    try {
        await window.api.put('/admin/notifications/read-all');
        App.showToast('Đã đánh dấu tất cả thông báo là đã đọc', 'success');
        await loadNotifications();
        // Sync sidebar badge
        if (window.loadNotifications) window.loadNotifications();
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
