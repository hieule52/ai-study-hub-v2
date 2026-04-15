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

        if (user) {
            userMenu.innerHTML = `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-weight: 500; color: var(--text-primary)">Hi, ${user.email.split('@')[0]}</span>
                    <button class="btn btn-outline" onclick="App.logout()" style="padding: 0.5rem 1rem;">Đăng xuất</button>
                    ${user.role === 'admin' ? '<a href="/admin/dashboard.html" class="btn btn-primary" style="padding: 0.5rem 1rem;">Admin Panel</a>' : ''}
                    ${user.role === 'teacher' ? '<a href="/teacher/dashboard.html" class="btn btn-primary" style="padding: 0.5rem 1rem;">Teacher Panel</a>' : ''}
                    ${user.role === 'student' ? '<a href="/student/dashboard.html" class="btn btn-primary" style="padding: 0.5rem 1rem;">Góc học tập</a>' : ''}
                </div>
            `;
        } else {
            userMenu.innerHTML = `
                <a href="/login.html" class="nav-link">Đăng nhập</a>
                <a href="/register.html" class="btn btn-primary">Đăng ký ngay</a>
            `;
        }
    },

    logout() {
        window.api.clearSession();
        window.location.href = '/login.html';
    }
};

window.App = App;

// Auto setup on load
document.addEventListener('DOMContentLoaded', () => {
    App.renderUserNav();
});
