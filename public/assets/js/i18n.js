const I18n = {
    locale: localStorage.getItem('lang') || 'vi',
    translations: {
        vi: {
            "title_register": "Đăng Ký Học Viên",
            "subtitle_platform": "AI Study Hub LMS Platform",
            "label_display_name": "Tên hiển thị",
            "label_email": "Email",
            "label_password": "Mật khẩu",
            "btn_create_account": "Tạo Tài Khoản",
            "text_already_have_account": "Đã có tài khoản?",
            "link_login_now": "Đăng nhập ngay",
            "toast_register_success": "Tạo tài khoản thành công! Đang tự đăng nhập...",
            "btn_loading": "Hệ thống đang lưu...",
            "placeholder_username": "Nguyen Van A",
            "placeholder_password": "Ít nhất 6 ký tự",
            "title_login": "Đăng Nhập",
            "btn_login": "Đăng Nhập",
            "text_no_account": "Chưa có tài khoản?",
            "link_register_now": "Đăng ký ngay",
            "toast_login_success": "Đăng nhập thành công!",
            "toast_error": "Có lỗi xảy ra",

            // Backend Messages
            "Vui lòng cung cấp đủ username, email và password.": "Vui lòng cung cấp đủ username, email và password.",
            "Email đã tồn tại trong hệ thống.": "Email đã tồn tại trong hệ thống.",
            "Có lỗi xảy ra khi tạo tài khoản.": "Có lỗi xảy ra khi tạo tài khoản.",
            "Email và password không được để trống.": "Email và password không được để trống.",
            "Thông tin đăng nhập không chính xác.": "Thông tin đăng nhập không chính xác.",
            "Tài khoản của bạn đã bị khóa.": "Tài khoản của bạn đã bị khóa.",
            "Provided key is too short": "Cấu hình Server: Khóa bí mật JWT quá ngắn.",
            // Thêm các từ khoá chung
            "btn_register": "Đăng ký ngay",
            "btn_logout": "Đăng xuất khỏi hệ thống",
            "text_hello": "Xin chào"
        },
        en: {
            "title_register": "Student Registration",
            "subtitle_platform": "AI Study Hub LMS Platform",
            "label_display_name": "Display Name",
            "label_email": "Email",
            "label_password": "Password",
            "btn_create_account": "Create Account",
            "text_already_have_account": "Already have an account?",
            "link_login_now": "Login now",
            "toast_register_success": "Account created successfully! Logging in...",
            "btn_loading": "System is saving...",
            "placeholder_username": "John Doe",
            "placeholder_password": "At least 6 characters",
            "title_login": "Login",
            "btn_login": "Login",
            "text_no_account": "Don't have an account?",
            "link_register_now": "Register now",
            "toast_login_success": "Login successful!",
            "toast_error": "An error occurred",

            // Backend Messages
            "Vui lòng cung cấp đủ username, email và password.": "Please provide username, email, and password.",
            "Email đã tồn tại trong hệ thống.": "Email already exists in the system.",
            "Có lỗi xảy ra khi tạo tài khoản.": "An error occurred while creating the account.",
            "Email và password không được để trống.": "Email and password cannot be empty.",
            "Thông tin đăng nhập không chính xác.": "Incorrect login information.",
            "Tài khoản của bạn đã bị khóa.": "Your account has been banned.",
            "Provided key is too short": "Server Configuration: Provided key is too short.",
            // General keywords
            "btn_register": "Register now",
            "btn_logout": "Logout from system",
            "text_hello": "Hello"
        }
    },

    setLocale(lang) {
        this.locale = lang;
        localStorage.setItem('lang', lang);
        window.location.reload();
    },

    get(key) {
        return this.translations[this.locale]?.[key] || key;
    },

    render() {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (el.tagName === 'INPUT') {
                el.placeholder = this.get(key);
            } else {
                el.innerHTML = this.get(key);
            }
        });

        // Cập nhật trạng thái nút chuyển đổi ngôn ngữ
        document.querySelectorAll('.lang-switch-btn').forEach(btn => {
            if (btn.getAttribute('data-lang') === this.locale) {
                btn.style.fontWeight = 'bold';
                btn.style.color = 'var(--primary)';
            } else {
                btn.style.fontWeight = 'normal';
                btn.style.color = 'var(--text-secondary)';
            }
        });
    }
};

window.I18n = I18n;

document.addEventListener('DOMContentLoaded', () => {
    I18n.render();
});
