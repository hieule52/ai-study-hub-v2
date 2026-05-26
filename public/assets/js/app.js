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
        
        if (!document.querySelector('link[href*="font-awesome"]')) {
            const faLink = document.createElement('link');
            faLink.rel = 'stylesheet';
            faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
            document.head.appendChild(faLink);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        let iconHtml = '';
        let borderGlow = '';
        let glowColor = '';
        let iconColor = '';
        
        if (type === 'success') {
            iconHtml = '<i class="fas fa-check-circle"></i>';
            borderGlow = 'rgba(16, 185, 129, 0.25)';
            glowColor = 'rgba(16, 185, 129, 0.12)';
            iconColor = '#10b981';
        } else if (type === 'error') {
            iconHtml = '<i class="fas fa-exclamation-circle"></i>';
            borderGlow = 'rgba(239, 68, 68, 0.25)';
            glowColor = 'rgba(239, 68, 68, 0.12)';
            iconColor = '#ef4444';
        } else if (type === 'warning') {
            iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
            borderGlow = 'rgba(245, 158, 11, 0.25)';
            glowColor = 'rgba(245, 158, 11, 0.12)';
            iconColor = '#f59e0b';
        } else {
            iconHtml = '<i class="fas fa-info-circle"></i>';
            borderGlow = 'rgba(99, 102, 241, 0.25)';
            glowColor = 'rgba(99, 102, 241, 0.12)';
            iconColor = '#818cf8';
        }

        toast.style.cssText = `
            background: rgba(13, 19, 35, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid ${borderGlow};
            border-left: 4px solid ${iconColor};
            color: #fff;
            padding: 0.85rem 1.25rem;
            border-radius: 14px;
            margin-bottom: 0.75rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 0 15px ${glowColor};
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.5s ease;
            font-weight: 500;
            font-size: 0.85rem;
            z-index: 999999;
            min-width: 300px;
            max-width: 400px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-sizing: border-box;
            opacity: 0;
        `;
        
        toast.innerHTML = `
            <span style="color: ${iconColor}; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                ${iconHtml}
            </span>
            <span style="flex: 1; line-height: 1.4; word-break: break-word;">${message}</span>
        `;

        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 20);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    },

    confirm({ title = 'Xác nhận', message = '', type = 'danger', confirmText = 'Đồng ý', cancelText = 'Hủy bỏ' }) {
        return new Promise((resolve) => {
            if (!document.querySelector('link[href*="font-awesome"]')) {
                const faLink = document.createElement('link');
                faLink.rel = 'stylesheet';
                faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
                document.head.appendChild(faLink);
            }

            let modal = document.getElementById('global-confirm-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'global-confirm-modal';
                modal.style.cssText = `
                    position: fixed;
                    inset: 0;
                    background: rgba(4, 7, 16, 0.8);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 100000;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;
                document.body.appendChild(modal);
            }
            
            let iconHtml = '';
            let confirmBtnColor = '';
            let glowColor = '';
            
            if (type === 'danger') {
                iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                confirmBtnColor = 'linear-gradient(135deg, #ef4444, #dc2626)';
                glowColor = 'rgba(239, 68, 68, 0.15)';
            } else if (type === 'success') {
                iconHtml = '<i class="fas fa-check-circle"></i>';
                confirmBtnColor = 'linear-gradient(135deg, #10b981, #059669)';
                glowColor = 'rgba(16, 185, 129, 0.15)';
            } else if (type === 'warning') {
                iconHtml = '<i class="fas fa-exclamation-circle"></i>';
                confirmBtnColor = 'linear-gradient(135deg, #f59e0b, #d97706)';
                glowColor = 'rgba(245, 158, 11, 0.15)';
            } else {
                iconHtml = '<i class="fas fa-info-circle"></i>';
                confirmBtnColor = 'linear-gradient(135deg, #6366f1, #4f46e5)';
                glowColor = 'rgba(99, 102, 241, 0.15)';
            }
            
            modal.innerHTML = `
                <div class="confirm-card" style="
                    background: linear-gradient(135deg, #0d1525, #070a13);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 24px;
                    padding: 2.5rem 2rem;
                    width: 100%;
                    max-width: 420px;
                    margin: 1.5rem;
                    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.6), 0 0 50px ${glowColor};
                    text-align: center;
                    transform: scale(0.9);
                    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                    font-family: 'Inter', system-ui, sans-serif;
                ">
                    <div class="confirm-icon" style="
                        width: 64px;
                        height: 64px;
                        border-radius: 20px;
                        background: ${type === 'danger' ? 'rgba(239, 68, 68, 0.1)' : type === 'success' ? 'rgba(16, 185, 129, 0.1)' : type === 'warning' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(99, 102, 241, 0.1)'};
                        color: ${type === 'danger' ? '#ef4444' : type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#818cf8'};
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 1.5rem;
                        font-size: 1.75rem;
                        box-shadow: 0 8px 24px ${glowColor};
                    ">
                        ${iconHtml}
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem; letter-spacing: -0.02em;">${title}</h3>
                    <p style="font-size: 0.9rem; color: #94a3b8; line-height: 1.6; margin: 0 0 2rem; padding: 0 0.5rem;">${message}</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;">
                        <button class="confirm-cancel-btn" style="
                            padding: 0.85rem;
                            border-radius: 12px;
                            font-weight: 700;
                            font-family: inherit;
                            font-size: 0.875rem;
                            background: rgba(255, 255, 255, 0.04);
                            color: rgba(255, 255, 255, 0.6);
                            border: 1px solid rgba(255, 255, 255, 0.08);
                            cursor: pointer;
                            transition: all 0.2s;
                            outline: none;
                        ">${cancelText}</button>
                        <button class="confirm-ok-btn" style="
                            padding: 0.85rem;
                            border-radius: 12px;
                            font-weight: 700;
                            font-family: inherit;
                            font-size: 0.875rem;
                            background: ${confirmBtnColor};
                            color: #fff;
                            border: none;
                            cursor: pointer;
                            box-shadow: 0 8px 20px ${glowColor};
                            transition: all 0.2s;
                            outline: none;
                        ">${confirmText}</button>
                    </div>
                </div>
            `;
            
            const card = modal.querySelector('.confirm-card');
            const cancelBtn = modal.querySelector('.confirm-cancel-btn');
            const okBtn = modal.querySelector('.confirm-ok-btn');
            
            const close = (result) => {
                modal.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    modal.style.display = 'none';
                    resolve(result);
                }, 300);
            };
            
            cancelBtn.onclick = () => close(false);
            okBtn.onclick = () => close(true);
            
            cancelBtn.onmouseover = () => {
                cancelBtn.style.background = 'rgba(255, 255, 255, 0.08)';
                cancelBtn.style.color = '#fff';
                cancelBtn.style.borderColor = 'rgba(255, 255, 255, 0.15)';
            };
            cancelBtn.onmouseout = () => {
                cancelBtn.style.background = 'rgba(255, 255, 255, 0.04)';
                cancelBtn.style.color = 'rgba(255, 255, 255, 0.6)';
                cancelBtn.style.borderColor = 'rgba(255, 255, 255, 0.08)';
            };
            
            okBtn.onmouseover = () => {
                okBtn.style.transform = 'translateY(-1px)';
                okBtn.style.boxShadow = `0 12px 24px ${glowColor}`;
            };
            okBtn.onmouseout = () => {
                okBtn.style.transform = 'translateY(0)';
                okBtn.style.boxShadow = `0 8px 20px ${glowColor}`;
            };
            
            modal.onclick = (e) => {
                if (e.target === modal) close(false);
            };
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                card.style.transform = 'scale(1)';
                okBtn.focus();
            }, 10);
        });
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

    formatVND(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    },

    requireAuth(allowedRoles = []) {
        const user = window.api.getUser();
        if (!user) { window.location.href = '/login'; return; }
        if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
            const dashboards = { 'admin': '/admin/dashboard', 'teacher': '/teacher/dashboard', 'student': '/student/dashboard' };
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
            const fallbackHtml = `<div style="width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem;">${username.charAt(0).toUpperCase()}</div>`.replace(/\s+/g, ' ').trim();
            const avatarHtml = user.avatar
                ? `<img src="${user.avatar}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,0.1);" onerror="this.onerror=null; this.outerHTML=decodeURIComponent('${encodeURIComponent(fallbackHtml)}');">`
                : fallbackHtml;

            userMenu.innerHTML = langToggleBtn + `
                <div style="display:flex; align-items:center; gap:0.75rem; cursor: pointer;" onclick="App.toggleOffcanvasSidebar()">
                    <span style="font-size:0.85rem; font-weight: 600; color: var(--text-primary);">${username}</span>
                    ${avatarHtml}
                </div>
            `;
            this.renderOffcanvasSidebar(user);
        } else {
            userMenu.innerHTML = langToggleBtn + `
                <a href="/login" class="nav-link" style="font-size: 0.85rem; font-weight: 600; margin-right: 0.5rem;">${loginText}</a>
                <a href="/register" class="btn btn-primary" style="border-radius: 100px; padding: 0.6rem 1.5rem; font-size: 0.8rem;">${startText}</a>
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
            { icon: '⚙️', text: lang==='en'?'Admin Dashboard':'Bảng điều khiển Admin', link: '/admin/dashboard' },
            { icon: '👥', text: lang==='en'?'Manage Users':'Quản lý Người dùng', link: '/admin/users' }
        ] : user.role === 'teacher' ? [
            { icon: '🧑‍🏫', text: lang==='en'?'Teacher Dashboard':'Bảng điều khiển Giảng viên', link: '/teacher/dashboard' },
            { icon: '📚', text: lang==='en'?'Course Manager':'Quản lý Khóa học', link: '/teacher/dashboard#courses-container' }
        ] : [
            { icon: '📊', text: lang==='en'?'My Learning':'Tổng quan học tập', link: '/student/dashboard' },
            { icon: '📚', text: lang==='en'?'My Courses':'Khóa học của tôi', link: '/student/courses' },
            { icon: '🎓', text: lang==='en'?'Certificates':'Chứng chỉ của tôi', link: '/student/certificates' },
            { icon: '🤖', text: lang==='en'?'AI Tutor':'Gia sư AI', link: '/student/ai-chat' }
        ];

        sidebar.innerHTML = `
            <div style="padding:2.5rem 2rem; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:36px; height:36px; background:var(--primary); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1rem; font-weight:800; box-shadow: 0 8px 16px rgba(99,102,241,0.2);">A</div>
                    <span style="font-weight:700; font-size:1.1rem; letter-spacing:-0.02em; color: #fff;">AI Study Hub</span>
                </div>
            </div>
            <ul class="offcanvas-nav">
                <li><a href="${user.role === 'teacher' ? '/teacher/profile' : '/profile'}">👤 ${lang==='en'?'My Profile':'Hồ sơ cá nhân'}</a></li>
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
        window.location.href = '/login';
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
                window.location.href = '/login';
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
                const url = window.api.getUser().role === 'teacher' ? '/teacher/chat' : '/student/chat';
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
