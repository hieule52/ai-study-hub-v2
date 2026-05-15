/**
 * AI Study Hub LMS — Shared Teacher Notification Logic
 */

function toggleTeacherNotif(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('teacherNotifDropdown');
    if (!dropdown) return;
    const isHidden = dropdown.style.display === 'none' || dropdown.style.display === '';
    dropdown.style.display = isHidden ? 'block' : 'none';
}

async function loadNotifications() {
    try {
        const badge = document.getElementById('teacherNotifBadge');
        const list = document.getElementById('teacherNotifList');
        if (!badge || !list) return;

        const res = await window.api.get('/notifications');
        const data = res.data; 
        const notifs = data.items || [];

        const unreadCount = data.unread || 0;
        if (unreadCount > 0) {
            badge.innerText = unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }

        if (notifs.length === 0) {
            list.innerHTML = '<div class="p-8 text-center opacity-30" style="font-size:0.8rem;">Không có thông báo mới.</div>';
            return;
        }

        list.innerHTML = notifs.map(n => `
            <div class="notif-item ${n.is_read ? '' : 'unread'}" 
                 style="padding: 1.25rem; border-bottom: 1px solid var(--glass-border); text-align: left; cursor: default; transition: background 0.3s ease; ${n.is_read ? '' : 'background: rgba(99, 102, 241, 0.05);'}">
                <div style="display: flex; gap: 12px;">
                    <span style="font-size: 1.2rem; margin-top: 2px;">${n.icon || 'ℹ️'}</span>
                    <div style="flex: 1;">
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">${n.title || ''}</div>
                        <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; font-weight: 400;">${n.message}</div>
                        <div style="font-size: 0.7rem; opacity: 0.4; margin-top: 8px;">${new Date(n.created_at).toLocaleString()}</div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (err) { console.error('Lỗi tải thông báo:', err); }
}

async function markTeacherNotifRead(event) {
    if (event) event.stopPropagation();
    try {
        await window.api.put('/notifications/read-all');
        loadNotifications();
    } catch (err) { console.error(err); }
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    setInterval(loadNotifications, 15000);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('teacherNotifDropdown');
        const bell = document.getElementById('teacherNotifBell');
        if (dropdown && bell && !bell.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
