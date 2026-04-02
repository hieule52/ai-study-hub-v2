# 📦 team-php-intelligent-ai-system-v1

## 📁 Cấu trúc thư mục

```
project/
│
├── public/
│   └── index.php          # File vào chính (entry point)
│
├── core/
│   ├── Router.php         # Bộ định tuyến xử lý URL
│   └── Database.php       # Kết nối database (nếu dùng)
│
├── app/
│   ├── Controllers/
│   │   └── HomeController.php
│   └── Views/
│       ├── home.php       # Giao diện trang chủ
│       └── about.php      # Giao diện trang giới thiệu
│
└── routes/
    └── web.php           # Định nghĩa tất cả các route của hệ thống
```

## 🗄 Import Database (nếu có)

Nếu project có file .sql, hãy import vào phpMyAdmin / MySQL / XAMPP trước khi chạy web.

## 🚀 Cách chạy project

### 1️⃣ Bước 1: Di chuyển vào thư mục public/

```bash
cd public
```

### 2️⃣ Bước 2: Chạy PHP server

```bash
php -S localhost:8000
 php -S localhost:8000 -t public ( nếu không di chuyển vào public nha ae)
```

### 3️⃣ Bước 3: Truy cập website

👉 http://localhost:8000

### Tài khoản admin được cung cấp:
email: annguyenmxh@gmail.com
password: annguyenmxh@gmail.com

## Hướng dẫn cài đặt và chạy WebSocket với Ratchet
 ### B1: cài đặt ( lưu ý là phải chạy composer dump-autoload trước)
  - composer require cboden/ratchet
  - composer require vlucas/phpdotenv
 ### B2: chạy
  - Mở thêm một teminal:
    + chạy lệnh php server.php để socket hoạt động
