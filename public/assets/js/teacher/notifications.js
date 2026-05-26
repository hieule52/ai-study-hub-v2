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

        const dropdown = document.getElementById('teacherNotifDropdown');
        if (dropdown) {
            let footer = dropdown.querySelector('.notif-dropdown-footer');
            if (!footer) {
                footer = document.createElement('div');
                footer.className = 'notif-dropdown-footer';
                footer.style.cssText = 'padding: 1rem 1.5rem; text-align: center; border-top: 1px solid var(--glass-border); background: rgba(255,255,255,0.01);';
                footer.innerHTML = '<a href="/teacher/notifications" style="font-size: 0.85rem; color: var(--primary); font-weight: 700; text-decoration: none; display: block; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">Xem tất cả thông báo</a>';
                dropdown.appendChild(footer);
            }
        }

        if (notifs.length === 0) {
            list.innerHTML = '<div class="p-8 text-center opacity-30" style="font-size:0.8rem;">Không có thông báo mới.</div>';
            return;
        }

        list.innerHTML = notifs.map(n => {
            const redirectUrl = (n.type === 'chat' && n.data && n.data.sender_id) ? `/teacher/chat?user_id=${n.data.sender_id}` : null;
            const clickHandler = `onclick="handleDropdownNotifClick(event, ${n.id}, ${redirectUrl ? `'${redirectUrl}'` : 'null'})"`;
            const hoverStyles = `onmouseover="this.style.background='rgba(255, 255, 255, 0.03)'" onmouseout="this.style.background='${n.is_read ? 'transparent' : 'rgba(99, 102, 241, 0.05)'}'"`;

            return `
                <div class="notif-item ${n.is_read ? '' : 'unread'}" ${clickHandler} ${hoverStyles}
                     style="padding: 1.25rem; border-bottom: 1px solid var(--glass-border); text-align: left; cursor: pointer; transition: background 0.3s ease; ${n.is_read ? '' : 'background: rgba(99, 102, 241, 0.05);'}">
                    <div style="display: flex; gap: 12px;">
                        <span style="font-size: 1.2rem; margin-top: 2px;">${n.icon || 'ℹ️'}</span>
                        <div style="flex: 1;">
                            <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">${n.title || ''}</div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; font-weight: 400;">${n.message}</div>
                            <div style="font-size: 0.7rem; opacity: 0.4; margin-top: 8px;">${new Date(n.created_at).toLocaleString()}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (err) { console.error('Lỗi tải thông báo:', err); }
}

async function handleDropdownNotifClick(event, id, redirectUrl) {
    if (event) event.stopPropagation();
    try {
        await window.api.put(`/notifications/${id}/read`);
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else {
            loadNotifications();
        }
    } catch (err) {
        console.error('Lỗi khi đọc thông báo:', err);
    }
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
