/**
 * AI Study Hub LMS — UI Application Core
 * Bright Modern Edition
 */

const App = {
    /**
     * Display a floating UI notification
     */
    showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container') || this._createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const bg = type === 'success' ? '#fff' : 'rgba(252,165,165,0.1)';
        const border = type === 'success' ? 'rgba(0,0,0,0.1)' : 'rgba(252,165,165,0.3)';
        const color = type === 'success' ? '#0f172a' : '#ef4444';

        toast.style.cssText = `
            background: ${bg};
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid ${border};
            color: ${color};
            padding: 1rem 1.5rem;
            border-radius: 100px;
            margin-bottom: 0.75rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            transform: translateX(120%);
            transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
            font-weight: 500;
            font-size: 0.85rem;
            z-index: 99999;
            min-width: 280px;
            letter-spacing: 0.02em;
        `;
        toast.textContent = message;

        toastContainer.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 10);
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 600);
        }, 4000);
    },

    _createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 100px;
            right: 40px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
        `;
        document.body.appendChild(container);
        return container;
    },

    requireAuth(allowedRoles = []) {
        const user = window.api.getUser();
        if (!user) { window.location.href = '/login.php'; return; }
        if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
            const dashboards = { 'admin': '/admin/dashboard.php', 'teacher': '/teacher/dashboard.php', 'student': '/student/dashboard.php' };
            window.location.replace(dashboards[user.role] || '/');
            return null;
        }
        return user;
    },

    renderUserNav() {
        const user = window.api.getUser();
        const userMenu = document.getElementById('user-menu');
        if (!userMenu) return;

        const currentLang = localStorage.getItem('lang') || 'vi';
        const loginText    = currentLang === 'en' ? 'Login' : 'Đăng nhập';
        const startText    = currentLang === 'en' ? 'Get Started' : 'Bắt đầu học';

        const langToggleBtn = `
            <div style="display:flex; gap:0.5rem; margin-right:1.5rem; align-items:center;">
                <button class="lang-switch-btn" data-lang="vi" onclick="window.I18n&&window.I18n.setLocale('vi')"
                    style="background:none; border:none; cursor:pointer; font-size:0.75rem; font-weight:${currentLang==='vi'?'700':'500'}; color:var(--text-primary); padding:0.2rem; opacity:${currentLang==='vi'?'1':'0.4'}; transition: opacity 0.3s;">VI</button>
                <span style="color:var(--text-muted); font-size:0.6rem; opacity: 0.3;">|</span>
                <button class="lang-switch-btn" data-lang="en" onclick="window.I18n&&window.I18n.setLocale('en')"
                    style="background:none; border:none; cursor:pointer; font-size:0.75rem; font-weight:${currentLang==='en'?'700':'500'}; color:var(--text-primary); padding:0.2rem; opacity:${currentLang==='en'?'1':'0.4'}; transition: opacity 0.3s;">EN</button>
            </div>
        `;

        if (user) {
            const username = user.username || user.email.split('@')[0];
            const avatarHtml = user.avatar
                ? `<img src="${user.avatar}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,0.1);">`
                : `<div style="width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem;">${username.charAt(0).toUpperCase()}</div>`;

            userMenu.innerHTML = langToggleBtn + `
                <div style="display:flex; align-items:center; gap:0.75rem; cursor: pointer;" onclick="App.toggleOffcanvasSidebar()">
                    <span style="font-size:0.85rem; font-weight: 600; color: var(--text-primary);">${username}</span>
                    ${avatarHtml}
                </div>
            `;
            this.renderOffcanvasSidebar(user);
        } else {
            userMenu.innerHTML = langToggleBtn + `
                <a href="/login.php" class="nav-link" style="font-size: 0.85rem; font-weight: 600; margin-right: 0.5rem;">${loginText}</a>
                <a href="/register.php" class="btn btn-primary" style="border-radius: 100px; padding: 0.6rem 1.5rem; font-size: 0.8rem;">${startText}</a>
            `;
        }
    },

    toggleOffcanvasSidebar() {
        const sidebar  = document.getElementById('global-offcanvas');
        const backdrop = document.getElementById('offcanvas-backdrop');
        if (sidebar)  sidebar.classList.toggle('open');
        if (backdrop) backdrop.classList.toggle('show');
    },

    renderOffcanvasSidebar(user) {
        if (document.getElementById('global-offcanvas')) return;

        const sidebar = document.createElement('div');
        sidebar.id = 'global-offcanvas';
        sidebar.style.cssText = `
            position: fixed; top: 0; right: -320px; width: 320px; height: 100vh;
            background: rgba(13, 15, 23, 0.88);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 10000;
            transition: right 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex; flex-direction: column;
            box-shadow: -20px 0 60px rgba(0,0,0,0.4);
        `;

        const style = document.createElement('style');
        style.innerHTML = `
            #global-offcanvas.open { right: 0 !important; }
            #offcanvas-backdrop { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:9999;opacity:0;pointer-events:none;transition:opacity 0.6s ease; }
            #offcanvas-backdrop.show { opacity:1;pointer-events:all; }
            .offcanvas-nav { list-style:none;padding:1.5rem;margin:0;flex:1;overflow-y:auto; }
            .offcanvas-nav li a { display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1.25rem;border-radius:16px;margin-bottom:4px;color:rgba(255,255,255,0.55);transition:all 0.4s cubic-bezier(0.16, 1, 0.3, 1);text-decoration:none;font-weight:500;font-size:0.9rem; }
            .offcanvas-nav li a:hover { background:rgba(255,255,255,0.05); color:#fff; transform: translateX(8px); }
        `;
        document.head.appendChild(style);

        const backdrop = document.createElement('div');
        backdrop.id = 'offcanvas-backdrop';
        backdrop.onclick = () => this.toggleOffcanvasSidebar();
        document.body.appendChild(backdrop);

        const lang = localStorage.getItem('lang') || 'vi';
        const items = user.role === 'admin' ? [
            { icon: '⚙️', text: lang==='en'?'Admin Dashboard':'Bảng điều khiển Admin', link: '/admin/dashboard.php' },
            { icon: '👥', text: lang==='en'?'Manage Users':'Quản lý Người dùng', link: '/admin/users.php' }
        ] : user.role === 'teacher' ? [
            { icon: '🧑‍🏫', text: lang==='en'?'Teacher Dashboard':'Bảng điều khiển Giảng viên', link: '/teacher/dashboard.php' },
            { icon: '📚', text: lang==='en'?'Course Manager':'Quản lý Khóa học', link: '/teacher/dashboard.php#courses-container' }
        ] : [
            { icon: '📊', text: lang==='en'?'My Learning':'Tổng quan học tập', link: '/student/dashboard.php' },
            { icon: '📚', text: lang==='en'?'My Courses':'Khóa học của tôi', link: '/student/my-courses.php' },
            { icon: '🎓', text: lang==='en'?'Certificates':'Chứng chỉ của tôi', link: '/student/certificates.php' },
            { icon: '🤖', text: lang==='en'?'AI Tutor':'Gia sư AI', link: '/student/ai-chat.php' }
        ];

        sidebar.innerHTML = `
            <div style="padding:2.5rem 2rem; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:36px; height:36px; background:var(--primary); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1rem; font-weight:800; box-shadow: 0 8px 16px rgba(99,102,241,0.2);">A</div>
                    <span style="font-weight:700; font-size:1.1rem; letter-spacing:-0.02em; color: #fff;">AI Study Hub</span>
                </div>
            </div>
            <ul class="offcanvas-nav">
                <li><a href="/profile.php">👤 ${lang==='en'?'My Profile':'Hồ sơ cá nhân'}</a></li>
                ${items.map(i => `<li><a href="${i.link}">${i.icon} ${i.text}</a></li>`).join('')}
                <li><a href="/">🏠 ${lang==='en'?'Back to Home':'Trang chủ'}</a></li>
            </ul>
            <div style="padding:2rem; border-top:1px solid rgba(255,255,255,0.06);">
                <button onclick="App.logout()" class="btn btn-outline" style="width:100%; border-radius:100px; font-size:0.85rem; padding: 0.8rem; border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">${lang==='en'?'Logout':'Đăng xuất'}</button>
            </div>
        `;
        document.body.appendChild(sidebar);
    },

    logout() {
        window.api.clearSession();
        window.location.href = '/login.php';
    },

    /**
     * Smart navigation: Check if user is logged in before going to a page
     */
    checkAuthAndGo(targetUrl, message = 'Vui lòng đăng nhập để sử dụng tính năng này') {
        const user = window.api.getUser();
        if (user) {
            window.location.href = targetUrl;
        } else {
            this.showToast(message, 'info');
            setTimeout(() => {
                window.location.href = '/login.php';
            }, 1200);
        }
    },

    // ── Notifications ───────────────────────────────────────────
    async initNotifications() {
        const user = window.api.getUser();
        if (!user) return;
        
        try {
            const res = await window.api.get('/notifications');
            if (res && res.data) {
                this.updateNotifBadge(res.data.unread);
                this.renderNotifs(res.data.items);
            }
        } catch (e) { console.error('Notif error:', e); }
    },

    updateNotifBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    },

    toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('show');
        if (dropdown.classList.contains('show')) {
            this.initNotifications(); // Refresh when open
        }
    },

    renderNotifs(items) {
        const list = document.getElementById('notifList');
        if (!list) return;
        if (!items || items.length === 0) {
            list.innerHTML = `<div style="padding:2rem; text-align:center; opacity:0.4; font-size:0.8rem;">Chưa có thông báo mới</div>`;
            return;
        }

        list.innerHTML = items.map(item => {
            const time = new Date(item.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const unreadClass = item.is_read ? '' : 'unread';
            const icon = item.icon || 'ℹ️';
            
            // Custom click logic for chat
            let clickAttr = '';
            if (item.type === 'chat' && item.data && item.data.sender_id) {
                const url = window.api.getUser().role === 'teacher' ? '/teacher/chat.php' : '/student/chat.php';
                clickAttr = `onclick="window.location.href='${url}?user_id=${item.data.sender_id}'"`;
            }

            return `
                <div class="notif-item ${unreadClass}" ${clickAttr} style="cursor:pointer;">
                    <div class="notif-icon">${icon}</div>
                    <div class="notif-content">
                        <div class="notif-title">${item.title}</div>
                        <div class="notif-message">${item.message}</div>
                        <div class="notif-time">${time}</div>
                    </div>
                </div>
            `;
        }).join('');
    },

    async markAllNotifRead() {
        try {
            await window.api.put('/notifications/read-all');
            this.updateNotifBadge(0);
            this.initNotifications();
        } catch (e) { console.error(e); }
    }
};

window.App = App;

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.I18n === 'undefined') {
        const script = document.createElement('script');
        script.src = '/assets/js/i18n.js';
        script.onload = () => { if (window.I18n) window.I18n.render(); };
        document.head.appendChild(script);
    }
    App.renderUserNav();
    App.initNotifications();
    
    // Auto-refresh notifications every 30 seconds
    setInterval(() => App.initNotifications(), 30000);
    
    // Global handlers for navbar
    window.toggleNotifDropdown = () => App.toggleNotifDropdown();
    window.markAllNotifRead = () => App.markAllNotifRead();

    // Close dropdown on click outside
    document.addEventListener('click', e => {
        if (!e.target.closest('#notifBell') && !e.target.closest('#notifDropdown')) {
            document.getElementById('notifDropdown')?.classList.remove('show');
        }
    });
});
