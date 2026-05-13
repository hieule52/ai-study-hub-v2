<div align="center">
  <img src="https://img.shields.io/badge/AI_Study_Hub-LMS-4F46E5?style=for-the-badge&logo=codeigniter&logoColor=white" alt="Logo">
  <h1>🎓 AI STUDY HUB LMS</h1>
  <p><strong>Nền tảng Học tập Trực tuyến Thông minh tích hợp Trợ lý Trí tuệ Nhân tạo</strong></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Vanilla_JS-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JS">
    <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
    <img src="https://img.shields.io/badge/AI-Groq_LLaMA-FF6B35?style=for-the-badge&logo=meta&logoColor=white" alt="AI">
    <img src="https://img.shields.io/badge/WebSocket-Ratchet-010101?style=for-the-badge" alt="WebSocket">
  </p>
</div>

---

## 📖 Giới Thiệu (Project Rationale)

**AI Study Hub LMS** được xây dựng nhằm giải quyết những hạn chế của các hệ thống quản lý học tập (LMS) truyền thống như: thiếu sự hỗ trợ cá nhân hóa, tỷ lệ tương tác của học viên thấp, và quy trình vận hành thủ công kém hiệu quả.

Nền tảng kết hợp kiến trúc **MVC + Repository + Service Pattern** mạnh mẽ bằng PHP thuần, cùng với **Trợ lý AI trực tuyến (Groq API - LLaMA 3.1)**. Hệ thống giúp học viên được giải đáp thắc mắc 24/7 theo đúng ngữ cảnh bài giảng, hỗ trợ giảng viên dễ dàng quản lý học liệu và giúp quản trị viên tự động hóa quy trình ghi danh thông qua hệ thống thanh toán tự động.

---

## ⭐ Các Tính Năng Nổi Bật

### 🤖 Trợ Lý Trí Tuệ Nhân Tạo (AI Tutor)
- **Hỗ trợ theo ngữ cảnh**: AI đọc và hiểu tài liệu của bài học hiện tại để giải thích bám sát chương trình giảng dạy.
- **Chatbot Lập trình**: Có khả năng giải thích code, sửa lỗi, tạo bài tập thực hành.
- **Kiểm duyệt nội dung**: Ngăn chặn XSS, SQL Injection và từ chối trả lời các câu hỏi nằm ngoài phạm vi giáo dục.

### 💳 Hệ Thống Thanh Toán Tự Động
- Tích hợp mã QR qua VietQR.
- Cơ chế **Google Apps Script Polling** tự động dò tìm giao dịch qua Google Sheets và kích hoạt khóa học trong vòng 5-10 giây mà không cần con người can thiệp.

### 🌍 Đa Ngôn Ngữ Toàn Diện (i18n)
- Giao diện người dùng hỗ trợ cả **Tiếng Việt** và **Tiếng Anh**.
- Chuyển đổi ngôn ngữ mượt mà theo thời gian thực (Real-time).

### 💬 Tương Tác Thời Gian Thực & Video Streaming
- Nhắn tin trực tiếp giữa Học viên và Giảng viên qua **WebSocket (Ratchet)**.
- **Bảo mật Video**: Video bài giảng được mã hóa bằng Signed Token (HMAC-SHA256), chống tải lậu và hỗ trợ HTTP Range Streaming.

---

## 👥 Phân Quyền Hệ Thống

| Vai trò | Tính năng truy cập |
| :--- | :--- |
| **Học viên (Student)** | - Học qua Video/Text và làm Quiz trắc nghiệm.<br>- Đặt câu hỏi với AI Tutor ngay trong bài học.<br>- Theo dõi tiến độ, nhận Chứng chỉ tự động.<br>- Mua khóa học qua cổng thanh toán VietQR. |
| **Giảng viên (Teacher)** | - Dashboard quản lý doanh thu, học viên.<br>- Course Builder: Tạo chương, bài học, tải lên Video.<br>- Trò chuyện hỗ trợ học viên trực tiếp. |
| **Quản trị viên (Admin)** | - Duyệt/Từ chối khóa học từ Giảng viên.<br>- Quản lý người dùng (Ban/Unban, đổi Role).<br>- Theo dõi Audit Logs và thống kê toàn hệ thống. |

---

## 🏗️ Kiến Trúc Hệ Thống

Hệ thống được phát triển theo mô hình **MVC kết hợp Service & Repository Pattern**, sử dụng JWT để bảo mật các API.

```mermaid
graph TD
    Client[Trình duyệt Web (Frontend)] -->|HTTP/REST| Router[Core Router / index.php]
    Client <-->|WebSocket :8080| Ratchet[server.php - Ratchet]
    
    Router --> Middleware[Auth & Role Middleware]
    Middleware --> Controller[API Controllers]
    
    Controller --> Service[Services Layer]
    Service <--> External[External APIs: Groq LLaMA, VietQR]
    
    Service --> Repo[Repository Layer]
    Repo <--> DB[(MySQL Database 8.0+)]
    
    Service --> FileSys[Secure File System]
```

---

## 🔄 Hành Vi Của Hệ Thống (User Workflows)

Hệ thống được thiết kế với các luồng hoạt động rõ ràng, tự động hóa tối đa để mang lại trải nghiệm học tập xuyên suốt.

### 1. Luồng Khám Phá & Ghi Danh Khóa Học (Student Flow)
1. **Khách truy cập (Guest)**: Xem danh sách khóa học (Miễn phí & Premium) trên trang chủ. Có thể xem chi tiết giới thiệu khóa học, đánh giá sao, bình luận từ học viên khác mà không cần đăng nhập.
2. **Xác thực**: Khi nhấn "Đăng ký khóa học" (hoặc Mua), hệ thống yêu cầu đăng nhập. Nếu chưa có tài khoản, quy trình đăng ký diễn ra nhanh chóng với JWT cấp quyền tức thì.
3. **Thanh toán tự động**: Với khóa học Premium, hệ thống sinh mã VietQR. Ngay khi học viên quét mã thanh toán thành công qua ứng dụng ngân hàng, Google Sheets Webhook/Polling sẽ xác thực giao dịch trong 5-10 giây và tự động mở khóa bài giảng, chuyển hướng học viên vào lớp học.

### 2. Luồng Học Tập & Tương Tác Trí Tuệ Nhân Tạo
1. **Theo dõi nội dung**: Học viên truy cập `Learning Dashboard` để xem các bài học dưới dạng Video Streaming (bảo mật, chống tải lậu) hoặc bài đọc văn bản.
2. **Kiểm tra kiến thức (Quiz)**: Làm bài kiểm tra trắc nghiệm cuối bài học để củng cố kiến thức, hệ thống chấm điểm tự động.
3. **Trợ lý AI Tutor**: Bất cứ lúc nào gặp khó khăn, học viên có thể hỏi trực tiếp AI ngay trong giao diện học. AI sẽ nhận **ngữ cảnh (context)** của toàn bộ bài học hiện tại (bao gồm text bài học hoặc script của video) để trả lời chính xác, giải thích chi tiết đoạn code hoặc lý thuyết đang học.
4. **Cấp chứng chỉ**: Sau khi tiến độ đạt 100%, hệ thống tự động phát hành chứng chỉ điện tử cho học viên.

### 3. Luồng Giảng Dạy & Quản Lý (Teacher Flow)
1. **Course Builder**: Giảng viên tạo khóa học mới, thiết lập giá tiền, tải lên ảnh bìa. Khóa học sẽ được chuyển trạng thái "Chờ duyệt" (Pending).
2. **Cấu hình bài giảng**: Giảng viên tải lên các video bảo mật qua API của hệ thống (file lưu ở Storage ngoài webroot), tạo các bài Quiz (Câu hỏi + Đáp án đúng).
3. **Tương tác học viên**: Nhận tin nhắn từ học viên qua WebSocket và trả lời trực tiếp trên giao diện Chat Box của giảng viên. Xem thống kê tiến độ của từng học viên.

### 4. Luồng Quản Trị Hệ Thống (Admin Flow)
1. **Kiểm duyệt nội dung**: Nhận thông báo có khóa học mới, kiểm tra nội dung và nhấn "Duyệt" (Approve) để xuất bản khóa học lên trang chủ.
2. **Quản lý rủi ro**: Theo dõi Audit Logs các hành động nhạy cảm trong hệ thống. Quản lý trạng thái tài khoản (Ban/Unban) khi có dấu hiệu vi phạm.

---

## 🛠️ Công Nghệ Sử Dụng

### Backend
- **Core**: PHP 8.0+ (Custom MVC Framework)
- **Database**: MySQL 8.0+ (PDO, Prepared Statements)
- **Authentication**: JWT (JSON Web Tokens)
- **Real-time**: Ratchet (WebSocket Server cho PHP)

### Frontend
- **UI/UX**: HTML5, CSS3, Vanilla JS, Bootstrap 5.3
- **Tools**: Chart.js (Thống kê), FontAwesome (Icons)

### Tích Hợp (Third-party)
- **AI**: Groq API (Mô hình LLaMA 3.1 8B)
- **Payment**: VietQR + Google Sheets API (Polling)

---

## 🚀 Hướng Dẫn Cài Đặt

### 1. Yêu Cầu Môi Trường
- XAMPP / WAMP / LAMP stack (PHP 8.0+, MySQL 8.0+)
- Composer 2.x

### 2. Cài Đặt Khởi Tạo
Clone dự án về máy và cài đặt thư viện:
```bash
git clone https://github.com/hieule52/ai-study-hub-v2.git
cd AIStudyHubLMS
composer install
composer dump-autoload
```

### 3. Cấu Hình Biến Môi Trường
Copy file cấu hình và thiết lập các API keys:
```bash
cp .env.example .env
```
Mở file `.env` và cập nhật:
```env
DB_HOST=localhost
DB_NAME=lms_v2_db
DB_USER=root
DB_PASS=

JWT_SECRET=your_super_secret_key_min_32_chars
GROQ_API_KEY=gsk_your_groq_api_key

VIDEO_STORAGE_PATH=storage/videos
VIDEO_TOKEN_SECRET=video_security_secret
```

### 4. Cơ Sở Dữ Liệu
Import file SQL vào MySQL:
```bash
mysql -u root -p lms_v2_db < database/aistudyhublms.sql
```

### 5. Khởi Chạy Server
Khởi chạy đồng thời 2 server (1 cho Web, 1 cho WebSocket):
```bash
# Terminal 1: WebSocket Server (Chat)
php server.php


# Terminal 2: Web Server
php -S localhost:8000 -t public

```
Truy cập hệ thống tại: `http://localhost:8000`

---

## 🔒 Cơ Chế Bảo Mật Tích Hợp

- **Xác thực JWT (JSON Web Tokens)**: Thời gian sống ngắn, truyền qua Bearer Header, chống CSRF.
- **Chống SQL Injection**: Sử dụng hoàn toàn kỹ thuật PDO Prepared Statements.
- **Bảo mật File**: Video được lưu trữ ngoài thư mục `public/`. URL tải video là dạng sinh token tự động kèm thời gian hết hạn bằng `HMAC-SHA256`.
- **Soft Deletes**: Dữ liệu quan trọng chỉ bị ẩn chứ không xóa vĩnh viễn khỏi DB.

---

## 📄 Bản Quyền & Tác Giả

Dự án được phát triển bởi **Lê Diên Hiếu** (`lehieu2900.in@gmail.com`). 

*Giấy phép hoạt động (License)*: **MIT License**. Bạn có quyền tự do chỉnh sửa và sử dụng cho mục đích cá nhân hoặc thương mại.

<div align="center">
  <p><em>Được tạo ra với ❤️ để thay đổi cách chúng ta học tập.</em></p>
</div>
