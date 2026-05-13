const I18n = {
    locale: localStorage.getItem('lang') || 'vi',
    translations: {
        vi: {
            "title_register": "Tham gia AI Study Hub®",
            "subtitle_platform": "Bắt đầu hành trình học tập AI của bạn",
            "label_role": "Bạn là...",
            "role_student": "Học sinh",
            "role_teacher": "Giảng viên",
            "label_display_name": "Tên hiển thị",
            "label_email": "Địa chỉ Email",
            "label_password": "Mật khẩu bảo mật",
            "btn_create_account": "Tạo tài khoản miễn phí",
            "text_already_have_account": "Bạn đã có tài khoản?",
            "link_login_now": "Đăng nhập tại đây",
            "title_login": "Chào mừng quay lại",
            "btn_login": "Đăng nhập ngay",
            "text_no_account": "Bạn mới đến đây?",
            "link_register_now": "Đăng ký thành viên",
            "btn_loading": "Đang xử lý...",
            "btn_register": "Đăng ký ngay",
            "toast_login_success": "Chào mừng bạn quay lại!",
            "toast_register_success": "Đăng ký tài khoản thành công!",
            "toast_error": "Đã xảy ra lỗi, vui lòng thử lại",

            // Nav
            "nav_home": "Trang chủ",
            "nav_courses": "Khóa học",
            "nav_about": "Giới thiệu",
            "nav_login": "Đăng nhập",
            "nav_start": "Bắt đầu học",
            "nav_student_dashboard": "Tổng quan",
            "nav_student_courses": "Khóa học của tôi",
            "nav_student_ai": "Gia sư AI",
            "nav_student_chat": "Tin nhắn",
            "nav_student_certificates": "Chứng chỉ",
            "nav_back_home": "Về trang chủ",

            // Home
            "home_label_academic": "Đào tạo xuất sắc",
            "home_hero_title": "Đánh thức<br><em>tiềm năng</em><br>qua học tập thông minh.",
            "home_hero_subtitle": "Nền tảng LMS tích hợp AI Tutor giúp bạn học tập tập trung, gia sư thông minh, hỗ trợ giảng viên thời gian thực và phát triển học thuật bền vững.",
            "home_btn_start": "Bắt đầu ngay",
            "home_btn_explore": "Khám phá",
            "home_manifesto_label": "Tầm nhìn của chúng tôi",
            "home_manifesto_title": "Chúng tôi tin rằng giáo dục cần <br><em>chiều sâu, sự tập trung, trí tuệ,</em> <br>và tính nhân văn sâu sắc.",
            "home_courses_label": "Chương trình đào tạo",
            "home_courses_title": "Khóa học <em>Nổi bật</em>",
            "home_loading": "Đang kết nối dữ liệu...",
            "home_no_courses": "Hiện chưa có khóa học nào được đăng tải.",
            "home_ai_label": "Trợ lý học tập thông minh",
            "home_ai_title": "Gia sư AI <em>Hiểu ngữ cảnh</em> <br>của riêng bạn.",
            "home_ai_text": "Đặt câu hỏi bất cứ lúc nào. AI của chúng tôi không chỉ trả lời; nó hiểu nội dung bài học bạn đang học, giải thích các đoạn code phức tạp và hỗ trợ học thuật cá nhân hóa 24/7.",
            "home_ai_status": "AI TUTOR ĐANG TRỰC",
            "home_student_label": "Trải nghiệm học viên",
            "home_student_title": "Làm chủ kiến thức <em>là một hành trình.</em>",
            "home_student_text": "Theo dõi sự phát triển của bạn với các phân tích trực quan. Mỗi bài học hoàn thành là một bước tiến gần hơn đến mục tiêu của bạn. Duy trì động lực với chuỗi ngày học tập và tiến độ thời gian thực.",
            "home_student_progress": "TIẾN ĐỘ HỌC TẬP",
            "home_student_complete": "HOÀN THÀNH",
            "home_student_streak": "Ngày liên tiếp",
            "home_student_certs": "Chứng chỉ",
            "home_student_guest_tip": "Đăng ký để bắt đầu theo dõi tiến độ của bạn",
            "home_cert_label": "Công nhận thành tích",
            "home_cert_title": "Hành trình học tập của bạn <br>xứng đáng được <em>ghi nhận.</em>",
            "home_cert_name": "Chứng nhận Xuất sắc",
            "home_cert_guest_tip": "Hoàn thành khóa học để nhận chứng chỉ",
            "home_cta_title": "Bắt đầu hành trình học tập <br>thông minh <em>ngay hôm nay.</em>",
            "home_cta_btn": "Tham gia miễn phí",
            "home_free": "Miễn phí",
            "std_btn_continue": "Tiếp tục học",
            "home_btn_buy": "Ghi danh ngay",
            "std_btn_free": "Ghi danh miễn phí",
            "home_btn_login_learn": "Đăng nhập để học",
            "std_no_desc": "Chưa có mô tả chi tiết.",

            // Courses Page
            "courses_title": "Thư viện Khóa học",
            "courses_subtitle": "Khám phá kho tàng kiến thức AI chuyên sâu.",
            "courses_search_placeholder": "Tìm kiếm khóa học...",
            "courses_no_results": "Không tìm thấy khóa học nào phù hợp với từ khóa.",

            // About
            "about_label": "Câu chuyện của chúng tôi",
            "about_hero_title": "Tương lai của học tập<br>đang được <em>tái định nghĩa</em><br>bởi AI.",
            "about_text_1": "AI Study Hub® không chỉ là một nền tảng quản lý học tập (LMS) truyền thống. Chúng tôi là một hệ sinh thái học thuật hiện đại, nơi công nghệ AI được tích hợp sâu sắc để hỗ trợ từng bước chân của người học.",
            "about_text_2": "Sứ mệnh của chúng tôi là tạo ra một môi trường học tập tập trung, thông minh và đầy cảm hứng, giúp học viên không chỉ tiếp thu kiến thức mà còn rèn luyện tư duy giải quyết vấn đề với sự trợ giúp của Gia sư AI cá nhân hóa.",
            "about_values_title": "Giá trị cốt lõi",
            "about_value_1": "<strong>Học tập tập trung:</strong> Giao diện tối giản, loại bỏ xao nhãng.",
            "about_value_2": "<strong>Trí tuệ nhân tạo:</strong> Hỗ trợ giải đáp 24/7 theo ngữ cảnh bài học.",
            "about_value_3": "<strong>Kết nối thực tế:</strong> Tương tác trực tiếp với đội ngũ giảng viên giàu kinh nghiệm.",
            "about_team_title": "Đội ngũ sáng lập",
            "about_cta_title": "Sẵn sàng để bứt phá?",
            "about_cta_text": "Trở thành một phần của cộng đồng học tập thông minh nhất.",

            // Student Dashboard
            "std_welcome": "Chào mừng trở lại, ",
            "std_subtitle": "Hôm nay bạn muốn học thêm điều gì mới?",
            "std_learning": "Đang học",
            "std_completed": "Hoàn thành",
            "std_certs": "Chứng chỉ",
            "std_my_courses": "Khóa học của tôi",
            "std_my_courses_subtitle": "Quản lý lộ trình học tập của bạn",
            "std_no_enrolled": "Bạn chưa đăng ký khóa học nào.",
            "std_explore_new": "Khám phá ngay",
            "std_view_all": "Xem tất cả",
            "std_total": "Tổng số",
            "std_owned": "Đã sở hữu ✅",
            "std_btn_buy": "💳 Mua khóa học",
            "std_btn_free": "Đăng ký Miễn Phí",
            "std_btn_continue": "Tiếp tục học",
            "std_btn_start": "Vào học ngay",
            "std_progress": "Tiến độ học",
            "std_courses_unit": "khóa",
            "std_chart_title": "Biểu đồ tiến độ học tập trong tuần",
            "std_chart_label": "Bài học hoàn thành",
            "home_search_placeholder": "🔍 Tìm khóa học...",

            // Certificates
            "cert_title_card": "CHỨNG NHẬN HOÀN THÀNH",
            "cert_subtitle": "đã hoàn thành xuất sắc chương trình học",
            "cert_director": "Giám đốc đào tạo",
            "cert_official": "Hồ sơ học thuật chính thức",

            // AI Tutor
            "aichat_title": "Gia sư AI",
            "aichat_subtitle": "Trợ lý học tập thông minh Groq™",
            "aichat_welcome": "Xin chào! Tôi có thể giúp gì cho bài học hôm nay của bạn?",
            "aichat_prompt1": "Giải thích khái niệm này...",
            "aichat_prompt2": "Tóm tắt bài học vừa rồi",
            "aichat_prompt3": "Tạo bài tập thực hành",
            "aichat_input_placeholder": "Hỏi AI điều gì đó...",

            // Chat
            "chat_title": "Tin nhắn",
            "chat_search": "Tìm kiếm giảng viên...",
            "chat_select_to_start": "Chọn giảng viên để bắt đầu chat",
            "chat_select_to_start_subtitle": "Kết nối với giảng viên của bạn để được hỗ trợ học tập.",
            "chat_no_enroll_msg": "Bạn chưa đăng ký khóa học nào để nhắn tin với giảng viên.",
            "chat_input": "Nhập tin nhắn...",
            
            // Learning
            "lrn_back_home": "Về trang chủ",
            "lrn_curriculum": "Nội dung học tập",
            "lrn_total_lessons": "tổng bài học",
            "lrn_video_placeholder": "Chọn một bài học để bắt đầu xem video",
            "lrn_select_lesson": "Vui lòng chọn một bài học từ danh sách bên phải",
            "lrn_select_hint": "Chọn một bài học ở danh mục bên phải để bắt đầu.",
            "lrn_ai_ready": "Sẵn sàng hỗ trợ",
            "lrn_ai_welcome": "👋 Chào bạn! Tôi là AI Tutor của bạn. Hỏi tôi bất cứ điều gì về nội dung bài học này nhé!",
            "lrn_btn_rate": "Đánh giá",
            "lrn_mark_complete": "✅ Đánh dấu Đã Học",
            "lrn_loading_curriculum": "Đang tải giáo trình...",
            "lrn_no_content": "Chưa có nội dung.",
            "lrn_total": "Tổng số: ",
            "lrn_lessons": " bài học",
            "lrn_quiz_title": "Thử thách trí tuệ",
            "lrn_quiz_submit": "Nộp Bài Kiểm Tra",
            "lrn_quiz_grading": "Đang chấm điểm...",
            "lrn_quiz_success": "Tuyệt vời! Điểm của bạn là: ",
            "lrn_quiz_result": "Hoàn thành! KẾT QUẢ: ",
            "lrn_no_desc": "Giảng viên chưa cập nhật mô tả chi tiết.",
            
            // About Extra
            "about_cta_btn": "Đăng ký ngay"
        },
        en: {
            "title_register": "Join AI Study Hub®",
            "subtitle_platform": "Start your AI learning journey today",
            "label_role": "I am a...",
            "role_student": "Student",
            "role_teacher": "Teacher",
            "label_display_name": "Display Name",
            "label_email": "Email Address",
            "label_password": "Secure Password",
            "btn_create_account": "Create Free Account",
            "text_already_have_account": "Already have an account?",
            "link_login_now": "Login here",
            "title_login": "Welcome Back",
            "btn_login": "Login Now",
            "text_no_account": "New here?",
            "link_register_now": "Become a member",
            "btn_loading": "Processing...",
            "btn_register": "Register now",
            "toast_login_success": "Welcome back to the hub!",
            "toast_register_success": "Account created successfully!",
            "toast_error": "An error occurred, please try again",

            // Nav
            "nav_home": "Home",
            "nav_courses": "Courses",
            "nav_about": "About",
            "nav_login": "Login",
            "nav_start": "Get Started",
            "nav_student_dashboard": "Dashboard",
            "nav_student_courses": "My Courses",
            "nav_student_ai": "AI Tutor",
            "nav_student_chat": "Messages",
            "nav_student_certificates": "Certificates",
            "nav_back_home": "Back Home",

            // Home
            "home_label_academic": "Academic Excellence",
            "home_hero_title": "Awaken your<br><em>potential</em><br>through intelligent learning.",
            "home_hero_subtitle": "An immersive AI-powered LMS designed for focused studying, smart tutoring, and meaningful academic growth.",
            "home_btn_start": "Get Started",
            "home_btn_explore": "Explore",
            "home_manifesto_label": "Our Vision",
            "home_manifesto_title": "We believe learning should feel <br><em>immersive, focused, intelligent,</em> <br>and deeply human.",
            "home_courses_label": "Curated Learning",
            "home_courses_title": "Featured <em>Experiences</em>",
            "home_loading": "Connecting to data...",
            "home_no_courses": "No courses are currently available.",
            "home_ai_label": "Intelligent Assistant",
            "home_ai_title": "Your <em>Context-Aware</em> <br>AI Tutor.",
            "home_ai_text": "Ask anything, anytime. Our AI doesn't just answer; it understands your lesson context, explains complex code, and provides personalized support 24/7.",
            "home_ai_status": "AI TUTOR ACTIVE",
            "home_student_label": "Student Focus",
            "home_student_title": "Mastery <em>is a journey.</em>",
            "home_student_text": "Track your growth with cinematic analytics. Every lesson completed is a step closer to your goals. Stay motivated with study streaks and real-time progress.",
            "home_student_progress": "PROGRESS TRACKING",
            "home_student_complete": "COMPLETE",
            "home_student_streak": "Day Streak",
            "home_student_certs": "Certificates",
            "home_student_guest_tip": "Register to start tracking your progress",
            "home_cert_label": "Recognition",
            "home_cert_title": "Your learning journey <br>deserves <em>recognition.</em>",
            "home_cert_name": "Certificate of Excellence",
            "home_cert_guest_tip": "Complete courses to earn certificates",
            "home_cta_title": "Begin your intelligent <br>learning <em>journey</em> today.",
            "home_cta_btn": "Join for Free",
            "home_free": "Free",
            "std_btn_continue": "Continue learning",
            "home_btn_buy": "Enroll now",
            "std_btn_free": "Enroll for free",
            "home_btn_login_learn": "Login to learn",
            "std_no_desc": "No description available.",

            // Courses Page
            "courses_title": "Course Library",
            "courses_subtitle": "Explore our deep AI knowledge base.",
            "courses_search_placeholder": "Search courses...",
            "courses_no_results": "No courses found matching your search.",

            // About
            "about_label": "Our Story",
            "about_hero_title": "The future of learning<br>is being <em>redefined</em><br>by AI.",
            "about_text_1": "AI Study Hub® is more than just a traditional LMS. We are a modern academic ecosystem where AI is deeply integrated to support every step of your journey.",
            "about_text_2": "Our mission is to create a focused, intelligent environment that helps students not only learn but master problem-solving with their own AI Tutor.",
            "about_values_title": "Core Values",
            "about_value_1": "<strong>Focused Learning:</strong> Minimalist UI, zero distractions.",
            "about_value_2": "<strong>Artificial Intelligence:</strong> 24/7 context-aware support.",
            "about_value_3": "<strong>Real Connection:</strong> Direct access to industry-leading mentors.",
            "about_team_title": "Founding Team",
            "about_cta_title": "Ready to excel?",
            "about_cta_text": "Join the world's most intelligent learning community.",

            // Student Dashboard
            "std_welcome": "Welcome back, ",
            "std_subtitle": "What do you want to learn today?",
            "std_learning": "Learning",
            "std_completed": "Completed",
            "std_certs": "Certificates",
            "std_my_courses": "My Courses",
            "std_my_courses_subtitle": "Manage your learning journey",
            "std_no_enrolled": "You are not enrolled in any courses yet.",
            "std_explore_new": "Explore Now",
            "std_view_all": "View All",
            "std_total": "Total",
            "std_owned": "Owned ✅",
            "std_btn_buy": "💳 Buy Course",
            "std_btn_free": "Enroll Free",
            "std_btn_continue": "Continue",
            "std_btn_start": "Start Learning",
            "std_progress": "Learning Progress",
            "std_courses_unit": "courses",
            "std_chart_title": "Weekly Learning Progress",
            "std_chart_label": "Completed Lessons",
            "home_search_placeholder": "🔍 Search courses...",

            // Certificates
            "cert_title_card": "CERTIFICATE OF COMPLETION",
            "cert_subtitle": "has successfully mastered the curriculum of",
            "cert_director": "Program Director",
            "cert_official": "Official Academic Record",

            // AI Tutor
            "aichat_title": "AI Tutor",
            "aichat_subtitle": "Groq™ Smart Learning Assistant",
            "aichat_welcome": "Hello! How can I help you with your lessons today?",
            "aichat_prompt1": "Explain this concept...",
            "aichat_prompt2": "Summarize the last lesson",
            "aichat_prompt3": "Create practice exercises",
            "aichat_input_placeholder": "Ask AI anything...",

            // Chat
            "chat_title": "Messages",
            "chat_search": "Search teachers...",
            "chat_select_to_start": "Select a teacher to start chatting",
            "chat_select_to_start_subtitle": "Connect with your teachers for academic support.",
            "chat_no_enroll_msg": "You have not enrolled in any courses to chat with teachers.",
            "chat_input": "Type a message...",

            // Learning
            "lrn_back_home": "Back Home",
            "lrn_curriculum": "Curriculum",
            "lrn_total_lessons": "total lessons",
            "lrn_video_placeholder": "Select a lesson to start watching",
            "lrn_select_lesson": "Please select a lesson from the right sidebar",
            "lrn_select_hint": "Select a lesson from the curriculum to start.",
            "lrn_ai_ready": "Ready to help",
            "lrn_ai_welcome": "👋 Hello! I am your AI Tutor. Ask me anything about this lesson!",
            "lrn_btn_rate": "Rate Now",
            "lrn_mark_complete": "✅ Mark as Completed",
            "lrn_loading_curriculum": "Loading curriculum...",
            "lrn_no_content": "No content available.",
            "lrn_total": "Total: ",
            "lrn_lessons": " lessons",
            "lrn_quiz_title": "Mental Challenge",
            "lrn_quiz_submit": "Submit Quiz",
            "lrn_quiz_grading": "Grading...",
            "lrn_quiz_success": "Great! Your score is: ",
            "lrn_quiz_result": "Done! RESULT: ",
            "lrn_no_desc": "No detailed description available.",
            
            // About Extra
            "about_cta_btn": "Register Now"
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
            const translation = this.get(key);
            if (el.tagName === 'INPUT') {
                el.placeholder = translation;
            } else {
                el.innerHTML = translation;
            }
        });

        document.querySelectorAll('.lang-switch-btn').forEach(btn => {
            if (btn.getAttribute('data-lang') === this.locale) {
                btn.style.opacity = '1';
                btn.style.fontWeight = 'bold';
            } else {
                btn.style.opacity = '0.4';
                btn.style.fontWeight = 'normal';
            }
        });
    }
};

window.I18n = I18n;

document.addEventListener('DOMContentLoaded', () => {
    I18n.render();
});
