/**
 * AI Study Hub LMS - UI Application Core
 * Manages Toast Notifications, Sidebar toggle, and general DOM events.
 */

const App = {
    /**
     * Display a floating UI notification
     */
    showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container') || this._createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Colors base on type
        const bg = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#4f46e5');
        
        toast.style.cssText = `
            background: ${bg};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateX(120%);
            transition: transform 0.3s ease;
            font-weight: 500;
            z-index: 9999;
        `;
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => toast.style.transform = 'translateX(0)', 10);

        // Auto remove
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    _createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
        `;
        document.body.appendChild(container);
        return container;
    },

    /**
     * Prevent guest users from seeing protected pages
     */
    requireAuth(allowedRoles = []) {
        const user = window.api.getUser();
        if (!user) {
            window.location.href = '/login.html';
            return;
        }

        if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
            alert('Bạn không có quyền truy cập trang này.');
            window.history.back();
        }
        return user;
    },

    /**
     * Build User Profile Avatar block on Navbar
     */
    renderUserNav() {
        const user = window.api.getUser();
        const userMenu = document.getElementById('user-menu');
        if (!userMenu) return;

        const currentLang = localStorage.getItem('lang') || 'vi';
        
        let loginText = currentLang === 'en' ? 'Login' : 'Đăng nhập';
        let registerText = currentLang === 'en' ? 'Register Now' : 'Đăng ký ngay';
        let helloText = currentLang === 'en' ? 'Hello' : 'Xin chào';

        const langToggleBtn = `
            <div style="display: flex; gap: 0.5rem; margin-right: 1.5rem; border-right: 1px solid rgba(255,255,255,0.1); padding-right: 1.5rem; align-items: center;">
                <button class="lang-switch-btn" data-lang="vi" onclick="window.I18n && window.I18n.setLocale('vi')" style="background:none; border:none; cursor:pointer; font-weight: ${currentLang==='vi'?'bold':'normal'}; color: var(--${currentLang==='vi'?'primary':'text-secondary'});">VI</button>
                <span style="color:var(--text-secondary)">|</span>
                <button class="lang-switch-btn" data-lang="en" onclick="window.I18n && window.I18n.setLocale('en')" style="background:none; border:none; cursor:pointer; font-weight: ${currentLang==='en'?'bold':'normal'}; color: var(--${currentLang==='en'?'primary':'text-secondary'});">EN</button>
            </div>
        `;

        if (user) {
            const username = user.username || user.email.split('@')[0];
            let avatarHtml = '';
            if (user.avatar) {
                avatarHtml = `<img src="${user.avatar}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">`;
            } else {
                avatarHtml = `<div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 2px solid rgba(255,255,255,0.1);">${username.charAt(0).toUpperCase()}</div>`;
            }

            userMenu.innerHTML = langToggleBtn + `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-weight: 500; color: var(--text-primary); display:flex; align-items:center; gap:0.5rem;">${username} ${avatarHtml}</span>
                    <button class="btn btn-primary" onclick="App.toggleOffcanvasSidebar()" style="padding: 0.5rem; border-radius: 8px; font-size: 1.2rem; line-height: 1; display:flex; align-items:center;">
                        ☰
                    </button>
                </div>
            `;
            this.renderOffcanvasSidebar(user);
        } else {
            userMenu.innerHTML = langToggleBtn + `
                <a href="/login.html" class="nav-link" data-i18n="btn_login">${loginText}</a>
                <a href="/register.html" class="btn btn-primary" data-i18n="btn_register">${registerText}</a>
            `;
        }
    },

    toggleOffcanvasSidebar() {
        const sidebar = document.getElementById('global-offcanvas');
        const backdrop = document.getElementById('offcanvas-backdrop');
        if (sidebar) sidebar.classList.toggle('open');
        if (backdrop) backdrop.classList.toggle('show');
    },

    renderOffcanvasSidebar(user) {
        if (document.getElementById('global-offcanvas')) return;

        const sidebar = document.createElement('div');
        sidebar.id = 'global-offcanvas';
        sidebar.style.cssText = `
            position: fixed;
            top: 0;
            right: -320px;
            width: 320px;
            height: 100vh;
            background: var(--bg-surface);
            border-left: 1px solid var(--primary-glow);
            z-index: 10000;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 30px rgba(0,0,0,0.8);
        `;

        const style = document.createElement('style');
        style.innerHTML = `
            #global-offcanvas.open { right: 0 !important; }
            #offcanvas-backdrop {
                position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
            }
            #offcanvas-backdrop.show { opacity: 1; pointer-events: all; }
            .offcanvas-nav { list-style: none; padding: 0; margin: 0; flex: 1; overflow-y: auto; }
            .offcanvas-nav li a {
                display: block; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); color: var(--text-primary); transition: all 0.2s; text-decoration: none; font-weight: 500;
            }
            .offcanvas-nav li a:hover { background: rgba(79, 70, 229, 0.15); color: var(--primary); padding-left: 2rem; }
        `;
        document.head.appendChild(style);

        const backdrop = document.createElement('div');
        backdrop.id = 'offcanvas-backdrop';
        backdrop.onclick = () => this.toggleOffcanvasSidebar();
        document.body.appendChild(backdrop);

        let menuItems = '';
        if (user.role === 'admin') {
            menuItems = `<li><a href="/admin/dashboard.html">⚙️ Bảng Điều Khiển Admin</a></li>`;
        } else if (user.role === 'teacher') {
            menuItems = `<li><a href="/teacher/dashboard.html">🧑‍🏫 Quản lý Khóa học</a></li>`;
        } else {
            menuItems = `
                <li><a href="/student/dashboard.html">📊 Tổng quan học tập</a></li>
                <li><a href="/student/dashboard.html#my-courses">📚 Khóa học của tôi</a></li>
                <li><a href="/student/chat.html">💬 Cửa sổ Chat (Hội nhóm)</a></li>
                <li><a href="/#courses">✨ Khám phá Khóa học mới</a></li>
            `;
        }

        sidebar.innerHTML = `
            <div style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2);">
                <h3 style="margin:0;">Hub <span class="text-gradient">Menu</span></h3>
                <button onclick="App.toggleOffcanvasSidebar()" style="background:none; border:none; color:var(--text-muted); font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <ul class="offcanvas-nav">
                <li><a href="/profile.html" style="color: var(--warning);">👤 Cá Nhân (Edit Profile)</a></li>
                ${menuItems}
            </ul>
            <div style="padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05);">
                <button onclick="App.logout()" class="btn btn-outline" style="width: 100%;" data-i18n="btn_logout">${localStorage.getItem('lang')==='en'?'Logout from system':'Đăng xuất khỏi hệ thống'}</button>
            </div>
        `;
        document.body.appendChild(sidebar);
    },

    logout() {
        window.api.clearSession();
        window.location.href = '/login.html';
    }
};

window.App = App;

// Auto setup on load
document.addEventListener('DOMContentLoaded', () => {
    // Tự động load thư viện i18n.js nếu chưa có
    if (typeof window.I18n === 'undefined') {
        const script = document.createElement('script');
        script.src = '/assets/js/i18n.js';
        script.onload = () => {
            if (window.I18n) window.I18n.render(); 
        };
        document.head.appendChild(script);
    }

    App.renderUserNav();
});
