# 📚 AI STUDY HUB - Tài liệu tổng quan dự án

## 🎯 **Giới thiệu dự án**

**AI Study Hub** là một nền tảng học tập thông minh tích hợp trí tuệ nhân tạo, được phát triển bằng PHP thuần với kiến trúc MVC. Dự án cung cấp một hệ sinh thái học tập hoàn chỉnh bao gồm AI chatbot, công cụ học tập, game giáo dục và tính năng mạng xã hội.

### 🌟 **Tầm nhìn**

Tạo ra một môi trường học tập hiện đại, tương tác và hiệu quả thông qua công nghệ AI, giúp học sinh học tập một cách thông minh và vui vẻ.

### 🎯 **Mục tiêu**

-   Cung cấp trợ lý AI hỗ trợ học tập 24/7
-   Tạo cộng đồng học sinh kết nối và chia sẻ kiến thức
-   Gamification trong học tập để tăng động lực
-   Tích hợp đa dạng công cụ AI hỗ trợ học tập

---

## ⭐ **Tính năng chính**

### 🤖 **AI Assistant**

-   **AI Chat**: Hỏi đáp với AI về mọi chủ đề học tập (sử dụng Groq API)
-   **AI Homework Solver**: Giải bài tập chi tiết từng bước
-   **AI Quiz Generator**: Tạo câu hỏi trắc nghiệm tự động
-   **AI Summarizer**: Tóm tắt nội dung dài thành những điểm chính
-   **Text-to-Speech**: Chuyển văn bản thành giọng nói (Web Speech API)
-   **AI Image Generator**: Tạo hình ảnh từ mô tả (Pollinations.ai API)

### 👥 **Social Features**

-   **Friendship System**: Kết bạn, gửi/nhận lời mời kết bạn
-   **Real-time Chat**: Nhắn tin trực tiếp với bạn bè (WebSocket)
-   **Profile Management**: Quản lý thông tin cá nhân, avatar

### 🎮 **Educational Games**

-   **Memory Game**: Trò chơi ghép thẻ tăng cường trí nhớ
-   **Math Quiz**: Luyện tập toán học với nhiều cấp độ
-   **Speed Click**: Trò chơi phản xạ và tập trung
-   **Samurai Slash**: Game hành động rèn luyện phản xạ

### 🎭 **VIP System**

-   **Payment Integration**: Hệ thống thanh toán nâng cấp VIP
-   **Premium Features**: Tính năng cao cấp cho thành viên VIP
-   **Google Sheets Integration**: Xác thực thanh toán qua Google Sheets

### 👨‍💼 **Admin Management**

-   **User Management**: Quản lý người dùng, phân quyền
-   **VIP Management**: Quản lý thành viên VIP
-   **System Settings**: Cấu hình hệ thống
-   **Analytics Dashboard**: Thống kê và báo cáo

---

## 🏗️ **Kiến trúc hệ thống**

### 📁 **Cấu trúc thư mục**

```
intelligent-ai-system-v1/
├── public/                     # Public files (CSS, JS, Images)
│   ├── index.php              # Entry point
│   └── assets/
│       ├── css/               # Stylesheets
│       ├── js/                # JavaScript files
│       └── avatars/           # User avatars
├── app/                       # Application logic
│   ├── Controllers/           # Controllers (MVC)
│   ├── Models/               # Models (Database)
│   └── Views/                # Views (UI)
│       ├── layouts/          # Layout components
│       ├── ai/               # AI-related views
│       ├── auth/             # Authentication views
│       ├── games/            # Game views
│       └── admin/            # Admin panel views
├── core/                     # Core system files
│   ├── Database.php          # Database connection
│   ├── Router.php            # URL routing
│   ├── Controller.php        # Base controller
│   ├── Env.php              # Environment variables
│   ├── Groq.php             # AI API integration
│   └── Model.php            # Base model
├── routes/
│   └── web.php              # Route definitions
├── vendor/                  # Composer dependencies
├── .env                     # Environment configuration
└── composer.json            # Dependencies
```

### 🔧 **Kiến trúc MVC**

-   **Model**: Xử lý dữ liệu và logic nghiệp vụ
-   **View**: Hiển thị giao diện người dùng
-   **Controller**: Xử lý request và điều hướng

### 🗄️ **Database Design**

-   **users**: Thông tin người dùng
-   **friendships**: Quan hệ bạn bè
-   **messages**: Tin nhắn chat
-   **ai_messages**: Lịch sử chat với AI

---

## 🛠️ **Công nghệ sử dụng**

### 📋 **Backend**

-   **PHP 8.0+**: Ngôn ngữ lập trình chính
-   **PDO MySQL**: Database interaction
-   **Composer**: Dependency management
-   **WebSocket (Ratchet)**: Real-time communication

### 🎨 **Frontend**

-   **HTML5 & CSS3**: Markup và styling
-   **Bootstrap 5**: UI framework
-   **JavaScript ES6+**: Client-side logic
-   **Font Awesome**: Icon library

### 🤖 **AI Integration**

-   **Groq API**: Chat AI và language processing
-   **Pollinations.ai**: Image generation
-   **Web Speech API**: Text-to-speech

### 🗄️ **Database**

-   **MySQL 8.0+**: Primary database
-   **PDO**: Database abstraction layer

### ☁️ **External Services**

-   **Google Sheets API**: Payment verification
-   **WebSocket Server**: Real-time messaging

---

## 🚀 **Cài đặt và triển khai**

### 📋 **Yêu cầu hệ thống**

-   PHP 8.0 hoặc cao hơn
-   MySQL 8.0 hoặc cao hơn
-   Composer
-   Web server (Apache/Nginx)
-   SSL certificate (cho production)

### ⚙️ **Cài đặt**

1. **Clone repository**

```bash
git clone [repository-url]
cd intelligent-ai-system-v1
```

2. **Install dependencies**

```bash
composer install
```

3. **Environment setup**

```bash
cp .env.example .env
# Chỉnh sửa .env với thông tin database và API keys
```

4. **Database setup**

```sql
-- Import database schema
mysql -u username -p database_name < database/schema.sql
```

5. **Start development server**

```bash
# Terminal 1: PHP server
cd public
php -S localhost:8000

# Terminal 2: WebSocket server (nếu cần)
php server.php
```

### 🔑 **Environment Variables**

```env
# Database
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=aio_ai

# AI APIs
GROQ_API_KEY=your_groq_api_key

# VIP System
VIP_SECRET=your_secret_key
VIP_AMOUNT=5000
```

---

## 📡 **API Documentation**

### 🤖 **AI Endpoints**

```php
POST /ai-chat                 # AI Chat conversation
POST /ai/homework-solver      # Homework solving
POST /ai/quiz-generator       # Quiz generation
POST /ai/summarizer           # Content summarization
GET  /ai/text-to-speech       # Text-to-speech interface
GET  /ai/image-generator      # Image generation interface
```

### 👥 **Social Endpoints**

```php
GET  /friends                 # Friends management
POST /friend/send             # Send friend request
POST /friend/accept           # Accept friend request
POST /friend/decline          # Decline friend request
GET  /chat                    # Chat interface
POST /chat/send               # Send message
```

### 🎮 **Game Endpoints**

```php
GET /games                    # Games dashboard
GET /games/memory             # Memory game
GET /games/math               # Math quiz game
GET /games/speed              # Speed click game
GET /games/samurai            # Samurai slash game
```

### 💎 **VIP Endpoints**

```php
GET  /vip/upgrade             # VIP upgrade page
POST /vip/confirm             # Confirm VIP payment
GET  /vip/check               # Check VIP status
```

---

## 🔐 **Bảo mật**

### 🛡️ **Security Features**

-   **Session Management**: Secure session handling
-   **CSRF Protection**: Request validation
-   **SQL Injection Prevention**: Prepared statements
-   **XSS Protection**: Input sanitization
-   **Password Hashing**: BCrypt hashing
-   **Access Control**: Role-based permissions

### 🔑 **Authentication**

-   Session-based authentication
-   Role-based access control (User, VIP, Admin)
-   Secure password storage

---

## 📊 **Performance & Monitoring**

### ⚡ **Optimization**

-   **Database Indexing**: Optimized queries
-   **Asset Minification**: CSS/JS compression
-   **Caching Strategy**: Session and data caching
-   **Lazy Loading**: On-demand resource loading

### 📈 **Monitoring**

-   Error logging
-   Performance metrics
-   User activity tracking
-   System health monitoring

---

## 🧪 **Testing**

### 🔬 **Test Coverage**

-   Unit tests cho core functions
-   Integration tests cho API endpoints
-   UI tests cho critical user flows
-   Performance tests cho AI features

### 🚀 **Development Workflow**

1. Feature development
2. Code review
3. Testing
4. Staging deployment
5. Production release

---

## 🚀 **Roadmap**

### 📅 **Version 2.0 (Upcoming)**

-   [ ] Mobile app (React Native)
-   [ ] Advanced AI tutoring system
-   [ ] Video call integration
-   [ ] Offline mode support
-   [ ] Multi-language support

### 🔮 **Future Features**

-   Machine learning recommendations
-   AR/VR learning experiences
-   Blockchain-based certificates
-   Advanced analytics dashboard

---

## 👥 **Team & Contributors**

### 🏢 **Development Team**

-   **Project Lead**: Tên leader Nguyễn Văn An
-   **Backend Developer**: PHP/MySQL specialist
-   **Frontend Developer**: UI/UX specialist
-   **AI Engineer**: Machine learning specialist
-   **DevOps Engineer**: Infrastructure specialist

### 🤝 **Contributing**

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

---

## 📞 **Support & Contact**

### 📧 **Contact Information**

-   **Email**: support@aistudyhub.com
-   **Phone**: +84 123 456 789
-   **Website**: https://aistudyhub.com
-   **GitHub**: [Repository Link]

### 🆘 **Support**

-   📚 **Documentation**: Detailed technical docs
-   💬 **Community**: Discord/Telegram support
-   🐛 **Issues**: GitHub issue tracking
-   ⭐ **FAQ**: Frequently asked questions

---

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 **Acknowledgments**

-   Groq AI for powerful language models
-   Pollinations.ai for image generation
-   Bootstrap team for UI framework
-   Open source community for various libraries

---

_Tài liệu này được cập nhật thường xuyên. Phiên bản hiện tại: v1.0 - Ngày cập nhật: 2025_
