<p align="center">
  <h1 align="center">🎓 AI STUDY HUB LMS</h1>
  <p align="center"><strong>Nền tảng học tập thông minh tích hợp Trí tuệ Nhân tạo</strong></p>
  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
    <img src="https://img.shields.io/badge/AI-Groq_LLaMA-FF6B35?style=for-the-badge" alt="AI">
    <img src="https://img.shields.io/badge/WebSocket-Ratchet-010101?style=for-the-badge" alt="WebSocket">
    <img src="https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=jsonwebtokens" alt="JWT">
  </p>
</p>

---

## 📋 Mục Lục

- [Giới thiệu](#-giới-thiệu)
- [Tính năng chính](#-tính-năng-chính)
- [Kiến trúc hệ thống](#️-kiến-trúc-hệ-thống)
- [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
- [Công nghệ sử dụng](#️-công-nghệ-sử-dụng)
- [Cơ sở dữ liệu](#️-cơ-sở-dữ-liệu)
- [API Documentation](#-api-documentation)
- [Cài đặt và triển khai](#-cài-đặt-và-triển-khai)
- [Bảo mật](#-bảo-mật)
- [Đa ngôn ngữ](#-đa-ngôn-ngữ-i18n)
- [Tác giả](#-tác-giả)

---

## 🎯 Giới Thiệu

**AI Study Hub LMS** là hệ thống quản lý học tập (Learning Management System) được xây dựng bằng **PHP thuần** theo kiến trúc **MVC + Repository + Service Pattern**. Hệ thống tích hợp **trợ lý AI thông minh** sử dụng Groq API (LLaMA 3.1) để hỗ trợ học viên trong quá trình học, kết hợp với các tính năng quản lý khóa học, video streaming bảo mật, hệ thống thanh toán tự động và chat thời gian thực.

### 🌟 Điểm nổi bật

- **AI Tutor thông minh**: Hiểu ngữ cảnh bài học đang xem, trả lời bám sát nội dung giảng viên
- **Video Streaming bảo mật**: Signed Token (HMAC-SHA256), lưu ngoài webroot, HTTP Range support
- **3 vai trò người dùng**: Student, Teacher, Admin — phân quyền rõ ràng qua JWT + Middleware
- **Thanh toán tự động**: Tích hợp VietQR + Google Sheets polling để xác nhận giao dịch
- **Chat thời gian thực**: WebSocket (Ratchet) cho nhắn tin trực tiếp
- **Đa ngôn ngữ**: Hỗ trợ Tiếng Việt và Tiếng Anh (i18n)
- **RESTful API**: Backend API chuẩn REST cho toàn bộ nghiệp vụ

---

## ⭐ Tính Năng Chính

### 👨‍🎓 Dành cho Học viên (Student)

| Tính năng | Mô tả |
|---|---|
| **Dashboard** | Thống kê tiến độ học tập, biểu đồ tuần |
| **Học bài** | Xem video bài giảng, đọc nội dung văn bản |
| **Quiz** | Làm bài kiểm tra trắc nghiệm sau mỗi bài |
| **AI Tutor** | Hỏi đáp AI với ngữ cảnh bài học hiện tại |
| **AI Chat** | Chat AI tổng quát về lập trình, bài tập |
| **Thanh toán** | Mua khóa học Premium qua VietQR |
| **Chat với Giảng viên** | Nhắn tin thời gian thực qua WebSocket |
| **Đánh giá khóa học** | Đánh giá sao & nhận xét khóa học |
| **Chứng chỉ** | Cấp chứng chỉ tự động khi hoàn thành khóa học |
| **Lộ trình học** | Theo dõi learning path có nhiều khóa |

### 👨‍🏫 Dành cho Giảng viên (Teacher)

| Tính năng | Mô tả |
|---|---|
| **Dashboard** | Thống kê khóa học, học viên, đánh giá |
| **Tạo khóa học** | Tạo và cấu hình khóa học (free/premium) |
| **Course Builder** | Quản lý chương, bài học, video, quiz |
| **Upload Video** | Upload video lên hệ thống bảo mật |
| **Quiz Builder** | Tạo bài kiểm tra với nhiều câu hỏi/đáp án |
| **Quản lý học viên** | Xem danh sách và tiến độ học viên |
| **Chat hỗ trợ** | Nhắn tin hỗ trợ học viên |

### 👨‍💼 Dành cho Quản trị viên (Admin)

| Tính năng | Mô tả |
|---|---|
| **Dashboard** | Thống kê toàn hệ thống, biểu đồ |
| **Quản lý Users** | CRUD, đổi role, ban/unban tài khoản |
| **Duyệt khóa học** | Approve/Reject khóa học từ giảng viên |
| **Quản lý VIP** | Xem giao dịch VIP, thêm/xóa VIP |
| **Audit Logs** | Theo dõi nhật ký hoạt động hệ thống |

### 🤖 Trợ lý AI

- **AI Tutor trong bài học**: Đọc nội dung bài giảng → trả lời bám sát 100% tài liệu
- **Content Moderation**: Chặn SQL injection, XSS, nội dung ngoài phạm vi giáo dục
- **Output Sanitization**: Ẩn thông tin nhạy cảm (API key, password, IP nội bộ)
- **Gợi ý bài tiếp**: Tự động đề xuất bài học kế tiếp trong khóa
- **Lịch sử chat**: Lưu và dùng 2 tin gần nhất làm context

---

## 🏗️ Kiến Trúc Hệ Thống

```
┌─────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   Browser    │────▶│  PHP Built-in    │────▶│   index.php     │
│  (Frontend)  │     │  Server :8000    │     │  (Entry Point)  │
└─────────────┘     └──────────────────┘     └────────┬────────┘
       │                                              │
       │ WebSocket :8080                    ┌─────────▼─────────┐
       │                                    │   Router (core/)  │
       ▼                                    │  Pattern Matching │
┌─────────────┐                             └─────────┬─────────┘
│  server.php │                                       │
│  (Ratchet)  │                          ┌────────────┼────────────┐
└─────────────┘                          ▼            ▼            ▼
                                    /api/*      /page.php     Static Files
                                    (REST)      (SSR View)    (CSS/JS/IMG)
                                      │
                          ┌───────────▼───────────┐
                          │    API Controllers     │
                          │  (AuthMiddleware +     │
                          │   RoleMiddleware)      │
                          └───────────┬───────────┘
                                      │
                          ┌───────────▼───────────┐
                          │      Services          │
                          │  (Business Logic)      │
                          └───────────┬───────────┘
                                      │
                          ┌───────────▼───────────┐
                          │    Repositories        │
                          │  (Data Access Layer)   │
                          └───────────┬───────────┘
                                      │
                          ┌───────────▼───────────┐
                          │   MySQL (PDO)          │
                          └───────────────────────┘
```

**Design Patterns áp dụng:**
- **MVC**: Tách biệt Controller → View → Model
- **Repository Pattern**: Tách logic truy vấn DB khỏi business logic
- **Service Pattern**: Tập trung business logic (AiService, VideoService, AuthService...)
- **Singleton**: Database connection (PDO)
- **Middleware**: Xác thực JWT và phân quyền Role

---

## 📁 Cấu Trúc Thư Mục

```
AIStudyHubLMS/
├── public/                          # Webroot (Entry point)
│   ├── index.php                    # Bootstrap: routing, CORS, env
│   ├── home.php                     # Trang chủ (guest + student)
│   ├── login.php                    # Đăng nhập
│   ├── register.php                 # Đăng ký (multi-step)
│   ├── profile.php                  # Quản lý hồ sơ cá nhân
│   ├── course-detail.php            # Chi tiết khóa học
│   ├── student/                     # Views cho Student
│   │   ├── dashboard.php            #   Dashboard học tập
│   │   ├── learning.php             #   Trang học bài (video + quiz + AI)
│   │   ├── ai-chat.php              #   Chat AI tổng quát
│   │   ├── chat.php                 #   Chat với giảng viên (WebSocket)
│   │   └── course-payment.php       #   Thanh toán VietQR
│   ├── teacher/                     # Views cho Teacher
│   │   ├── dashboard.php            #   Dashboard giảng dạy
│   │   ├── create-course.php        #   Tạo khóa học mới
│   │   ├── course-builder.php       #   Xây dựng nội dung khóa học
│   │   ├── students.php             #   Quản lý học viên
│   │   └── chat.php                 #   Chat hỗ trợ học viên
│   ├── admin/                       # Views cho Admin
│   │   ├── dashboard.php            #   Dashboard hệ thống
│   │   ├── users.php                #   Quản lý người dùng
│   │   ├── courses.php              #   Duyệt khóa học
│   │   ├── vip.php                  #   Quản lý VIP
│   │   └── logs.php                 #   Audit logs
│   ├── layouts/                     # Layout components
│   │   ├── header.php               #   Header + Navigation + Sidebar
│   │   └── footer.php               #   Footer + Scripts
│   ├── assets/
│   │   ├── css/style.css            # Stylesheet chính
│   │   ├── js/
│   │   │   ├── app.js               # Logic chung (toast, utils)
│   │   │   ├── api.js               # HTTP Client (JWT auto-attach)
│   │   │   └── i18n.js              # Đa ngôn ngữ (VI/EN)
│   │   └── avatars/                 # Avatar người dùng
│   └── uploads/                     # File upload (images)
│
├── app/                             # Application Layer
│   ├── Controllers/Api/             # REST API Controllers
│   │   ├── AuthController.php       #   Đăng ký, đăng nhập (JWT)
│   │   ├── UserController.php       #   Profile, avatar, đổi mật khẩu
│   │   ├── CourseController.php     #   CRUD khóa học
│   │   ├── LessonController.php     #   Curriculum, bài học, hoàn thành
│   │   ├── QuizController.php       #   Quiz theo bài, nộp bài
│   │   ├── StudentController.php    #   Enrolled courses, stats, enroll
│   │   ├── TeacherController.php    #   Dashboard, students, CRUD course
│   │   ├── TeacherCurriculumController.php  # CRUD chapters/lessons/quizzes
│   │   ├── AdminController.php      #   Users, courses, VIP, logs, stats
│   │   ├── AiController.php         #   AI chat endpoint
│   │   ├── VideoStreamController.php #  Video token + streaming
│   │   ├── UploadController.php     #   Upload image/video
│   │   ├── CategoryController.php   #   CRUD danh mục khóa học
│   │   ├── LearningPathController.php #  Lộ trình học
│   │   ├── VipPaymentController.php #   Thanh toán VIP
│   │   └── CassoWebhookController.php # Webhook thanh toán
│   ├── Services/                    # Business Logic Layer
│   │   ├── AiService.php            #   AI chat + moderation + context
│   │   ├── AuthService.php          #   Register, login, JWT
│   │   ├── CourseService.php        #   Course business logic
│   │   ├── LessonService.php        #   Lesson operations
│   │   ├── QuizService.php          #   Quiz grading
│   │   ├── TeacherService.php       #   Teacher-specific logic
│   │   ├── VideoService.php         #   Upload, stream, token, security
│   │   ├── LearningPathService.php  #   Learning path operations
│   │   └── VipPaymentService.php    #   Payment processing
│   ├── Repositories/                # Data Access Layer
│   │   ├── UserRepository.php
│   │   ├── CourseRepository.php
│   │   ├── ChapterRepository.php
│   │   ├── LessonRepository.php
│   │   ├── QuizRepository.php
│   │   ├── EnrollmentRepository.php
│   │   ├── CategoryRepository.php
│   │   ├── LearningPathRepository.php
│   │   ├── VideoTokenRepository.php
│   │   ├── VipPaymentRepository.php
│   │   ├── AiRepository.php
│   │   └── ChatRepository.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php       #   JWT verification
│   │   └── RoleMiddleware.php       #   Role-based access control
│   ├── Models/
│   │   ├── User.php
│   │   └── Course.php
│   └── WebSocket/
│       ├── Chat.php                 #   WebSocket handler
│       └── ChatSocket.php           #   Socket logic
│
├── core/                            # Framework Core
│   ├── Router.php                   # URL routing (regex pattern matching)
│   ├── Request.php                  # HTTP Request wrapper
│   ├── Response.php                 # JSON Response helper
│   ├── Database.php                 # PDO Singleton connection
│   ├── Controller.php               # Base controller
│   ├── Model.php                    # Base model
│   ├── View.php                     # View renderer
│   ├── Env.php                      # .env file parser
│   ├── JWTHandler.php               # JWT encode/decode (firebase/php-jwt)
│   ├── Groq.php                     # Groq API client (LLaMA)
│   └── Gemini.php                   # Google Gemini API client
│
├── routes/
│   ├── api.php                      # RESTful API routes (133 lines)
│   └── web.php                      # Server-rendered page routes
│
├── database/
│   ├── aistudyhublms.sql            # Schema chính (17 bảng)
│   └── migration_v2.sql             # Migration: categories, video tokens, learning paths
│
├── storage/
│   └── videos/                      # Video files (ngoài webroot, bảo mật)
│
├── server.php                       # WebSocket server (Ratchet)
├── composer.json                    # PHP dependencies
├── .env.example                     # Mẫu biến môi trường
└── .gitignore
```

---

## 🛠️ Công Nghệ Sử Dụng

### Backend
| Công nghệ | Phiên bản | Vai trò |
|---|---|---|
| PHP | 8.0+ | Ngôn ngữ chính, custom MVC framework |
| MySQL | 8.0+ | Cơ sở dữ liệu quan hệ |
| PDO | — | Database abstraction, prepared statements |
| Composer | 2.x | Dependency management, PSR-4 autoload |
| Ratchet | 0.4.4 | WebSocket server (real-time chat) |
| firebase/php-jwt | 7.0 | JWT authentication |
| vlucas/phpdotenv | 5.6 | Environment variables |

### Frontend
| Công nghệ | Vai trò |
|---|---|
| HTML5 + CSS3 | Cấu trúc và giao diện |
| JavaScript ES6+ | Client-side logic, SPA-like behavior |
| Bootstrap 5.3 | UI framework, responsive design |
| Font Awesome | Icon library |
| Chart.js | Biểu đồ thống kê |

### AI & External Services
| Dịch vụ | Vai trò |
|---|---|
| Groq API (LLaMA 3.1 8B) | AI Tutor, chat thông minh |
| Google Gemini API | AI provider phụ |
| VietQR + Google Sheets | Thanh toán tự động |
| Web Speech API | Text-to-speech |

---

## 🗄️ Cơ Sở Dữ Liệu

### Sơ đồ ERD (17+ bảng)

```
users ─────────┬──── enrollments ──── courses ──── course_categories
  │            │         │               │
  │            │         │          chapters
  │            │         │               │
  │            │         │           lessons ──── lesson_progress
  │            │         │               │
  │            │         │           quizzes ── questions ── answers
  │            │         │               │
  │            │         │          quiz_results
  │            │
  ├── messages (sender/receiver)
  ├── ai_messages
  ├── vip_payments
  ├── notifications
  ├── audit_logs
  ├── reports
  ├── course_reviews
  ├── video_tokens
  ├── learning_paths ── learning_path_courses
  └── learning_path_enrollments
```

### Bảng chính

| Bảng | Mô tả |
|---|---|
| `users` | Người dùng (student/teacher/admin), VIP, soft delete |
| `courses` | Khóa học (draft/pending/approved), giá, premium |
| `course_categories` | Danh mục khóa học (hỗ trợ phân cấp) |
| `chapters` | Chương trong khóa học (có thứ tự) |
| `lessons` | Bài học (video/text/quiz), content, video metadata |
| `lesson_progress` | Tiến độ hoàn thành bài của học viên |
| `enrollments` | Đăng ký khóa học + % tiến độ |
| `quizzes` | Bài kiểm tra gắn với bài học |
| `questions` | Câu hỏi trong quiz |
| `answers` | Đáp án (đánh dấu đúng/sai) |
| `quiz_results` | Kết quả làm bài |
| `messages` | Tin nhắn chat giữa users |
| `ai_messages` | Lịch sử chat với AI |
| `video_tokens` | Token tạm để stream video (có hạn) |
| `vip_payments` | Giao dịch thanh toán VIP |
| `audit_logs` | Nhật ký hành động hệ thống |
| `learning_paths` | Lộ trình học tập |

---

## 📡 API Documentation

Base URL: `http://localhost:8000/api`

### 🔐 Authentication
```
POST /api/auth/register          # Đăng ký tài khoản
POST /api/auth/login             # Đăng nhập → JWT token
```

### 👤 User Profile
```
GET    /api/user/profile         # Xem profile (Auth required)
PUT    /api/user/profile         # Cập nhật profile
PUT    /api/user/change-password # Đổi mật khẩu
POST   /api/user/avatar          # Upload avatar
```

### 📚 Courses
```
GET    /api/courses              # Danh sách khóa học (public)
GET    /api/courses/:id          # Chi tiết khóa học
POST   /api/courses              # Tạo khóa học (Teacher)
POST   /api/courses/:id/enroll   # Đăng ký khóa học
POST   /api/courses/:id/verify-purchase  # Xác nhận mua
```

### 📖 Lessons & Quiz
```
GET    /api/courses/:id/curriculum  # Giáo trình khóa học
GET    /api/lessons/:id             # Chi tiết bài học
POST   /api/lessons/:id/complete    # Đánh dấu hoàn thành
GET    /api/lessons/:id/quiz        # Quiz của bài học
POST   /api/quizzes/:id/submit      # Nộp bài quiz
```

### 🎬 Video Streaming
```
GET    /api/video/token/:lessonId   # Lấy signed token
GET    /api/video/stream?token=xxx  # Stream video (Range support)
```

### 🤖 AI
```
POST   /api/ai/chat              # Chat với AI (có lesson context)
```

### 👨‍🎓 Student
```
GET    /api/student/courses      # Khóa học đã đăng ký
GET    /api/student/stats        # Thống kê học tập
```

### 👨‍🏫 Teacher
```
GET    /api/teacher/dashboard    # Thống kê giảng dạy
PUT    /api/teacher/courses/:id  # Sửa khóa học
DELETE /api/teacher/courses/:id  # Xóa khóa học
GET    /api/teacher/students     # Danh sách học viên
POST   /api/teacher/chapters     # Tạo chương
POST   /api/teacher/lessons      # Tạo bài học
POST   /api/teacher/quizzes      # Tạo quiz
POST   /api/upload/video         # Upload video
POST   /api/upload/image         # Upload ảnh
```

### 👨‍💼 Admin
```
GET    /api/admin/stats              # Thống kê hệ thống
GET    /api/admin/chart-data         # Dữ liệu biểu đồ
GET    /api/admin/users              # Danh sách users
PUT    /api/admin/users/:id          # Sửa user
DELETE /api/admin/users/:id          # Xóa user
PUT    /api/admin/users/:id/role     # Đổi role
PUT    /api/admin/users/:id/status   # Ban/Unban
GET    /api/admin/courses/pending    # Khóa học chờ duyệt
PUT    /api/admin/courses/:id/approve # Duyệt khóa học
GET    /api/admin/audit-logs         # Nhật ký hệ thống
```

> **Auth**: Các API cần xác thực phải gửi header `Authorization: Bearer <JWT_TOKEN>`

---

## 🚀 Cài Đặt Và Triển Khai

### 📋 Yêu cầu

- PHP 8.0+
- MySQL 8.0+
- Composer 2.x
- XAMPP / WAMP / MAMP (hoặc PHP CLI)

### ⚙️ Các bước cài đặt

**1. Clone repository**
```bash
git clone https://github.com/hieule52/ai-study-hub-v2.git
cd AIStudyHubLMS
```

**2. Cài đặt dependencies**
```bash
composer install
composer dump-autoload
```

**3. Cấu hình môi trường**
```bash
cp .env.example .env
```

Chỉnh sửa file `.env`:
```env
# Database
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=lms_v2_db

# App Settings
APP_ENV=development
APP_URL=http://localhost:8000

# JWT Authentication
JWT_SECRET=your_strong_secret_key_min_32_chars
JWT_EXPIRATION=7200

# AI (Groq API - https://console.groq.com)
GROQ_API_KEY=gsk_xxxxxxxxxxxx

# Payment
VIP_SECRET=your_vip_secret
VIP_AMOUNT_VND=500000

# Video Security
VIDEO_STORAGE_PATH=storage/videos
VIDEO_TOKEN_SECRET=change_this_to_a_random_secret
VIDEO_TOKEN_EXPIRY=14400
VIDEO_MAX_SIZE_MB=200
```

**4. Import Database**
```bash
mysql -u root -p lms_v2_db < database/aistudyhublms.sql
mysql -u root -p lms_v2_db < database/migration_v2.sql
```

**5. Khởi chạy**
```bash
# Terminal 1: PHP Development Server
php -S localhost:8000 -t public

# Terminal 2: WebSocket Server (cho chat real-time)
php server.php
```

**6. Truy cập**

Mở trình duyệt: http://localhost:8000

---

## 🔐 Bảo Mật

| Cơ chế | Chi tiết |
|---|---|
| **JWT Authentication** | Token HS256, hết hạn 2h, gửi qua Bearer header |
| **Role-based Access** | AuthMiddleware + RoleMiddleware kiểm tra mỗi request |
| **SQL Injection** | PDO Prepared Statements toàn bộ |
| **XSS Protection** | Input sanitization, output encoding |
| **Password Hashing** | BCrypt (`password_hash`) |
| **Video Security** | File lưu ngoài webroot, signed token HMAC-SHA256 có hạn 4h |
| **AI Content Moderation** | Chặn SQL injection, XSS, nội dung phi giáo dục trong AI chat |
| **AI Output Sanitization** | Ẩn API key, password, IP nội bộ từ phản hồi AI |
| **CORS** | Cấu hình headers cho cross-origin requests |
| **Soft Delete** | Dữ liệu không bị xóa vĩnh viễn (deleted_at + deleted_by) |

---

## 🌐 Đa Ngôn Ngữ (i18n)

Hệ thống hỗ trợ **2 ngôn ngữ**: Tiếng Việt (mặc định) và Tiếng Anh.

- File cấu hình: `public/assets/js/i18n.js`
- Cơ chế: Dùng attribute `data-i18n` trên HTML elements
- Lưu trữ: `localStorage` (key: `lang`)
- Phạm vi: Toàn bộ giao diện Student, Teacher, Auth

---

## 👥 Tác Giả

| Vai trò | Thông tin |
|---|---|
| **Developer** | Lê Diên Hiếu |
| **Email** | lehieu2900.in@gmail.com |

---

## 📄 License

This project is licensed under the **MIT License**.

---

<p align="center"><em>AI Study Hub LMS v2.0 — Cập nhật: Tháng 5/2026</em></p>
