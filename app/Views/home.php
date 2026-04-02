<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Hub - Nền tảng học tập thông minh</title> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
</head>

<body>
    <?php include __DIR__ . '/layouts/navbar.php'; ?>

    <!-- Hero Section -->

    <section class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-3 fw-bold mb-4">🚀 AI Study Hub</h1>
                    <p class="fs-4 mb-4">Nền tảng học tập thông minh với AI - Học tập hiệu quả, kết bạn và giải trí</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="/register" class="cta-button">Bắt đầu học tập ngay! 🎯</a>
                        <a href="/demo" class="cta-button secondary">🔍 Xem demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background: white;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-4 fw-bold mb-4">✨ Tính năng nổi bật</h2>
                    <p class="fs-5 text-muted">
                        Khám phá các công cụ AI mạnh mẽ giúp bạn học tập hiệu quả hơn
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <!-- AI Chat -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">🤖</span>
                        <h3>AI Chat Thông Minh</h3>
                        <p>Hỏi đáp với AI về mọi chủ đề học tập. Giải toán, văn học, khoa học... AI sẽ hỗ trợ bạn 24/7!</p>
                    </div>
                </div>

                <!-- Text to Speech -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">🔊</span>
                        <h3>Chuyển Text thành Giọng nói</h3>
                        <p>Nghe AI đọc bài học, từ vựng hay ghi chú của bạn. Học qua thính giác hiệu quả hơn!</p>
                    </div>
                </div>

                <!-- AI Image Generator -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">🎨</span>
                        <h3>Tạo Hình Ảnh AI</h3>
                        <p>Tạo hình ảnh minh họa cho bài học, dự án hay sáng tạo nghệ thuật bằng AI chỉ với mô tả văn bản!</p>
                    </div>
                </div>

                <!-- Social Learning -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">👥</span>
                        <h3>Kết Bạn & Học Nhóm</h3>
                        <p>Kết nối với học sinh cùng trường, tạo nhóm học tập, chia sẻ kiến thức và cùng nhau tiến bộ!</p>
                    </div>
                </div>

                <!-- Real-time Chat -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">💬</span>
                        <h3>Chat Thời Gian Thực</h3>
                        <p>Nhắn tin trực tiếp với bạn bè, thảo luận bài tập, chia sẻ tài liệu học tập mọi lúc mọi nơi!</p>
                    </div>
                </div>

                <!-- Entertainment -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <span class="feature-icon">🎮</span>
                        <h3>Giải Trí & Game Học Tập</h3>
                        <p>Thư giãn với các mini game giáo dục, câu đố thông minh và hoạt động giải trí bổ ích!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-4 fw-bold mb-4">📊 Thống kê ấn tượng</h2>
                    <p class="fs-5">Con số biết nói về sức mạnh của nền tảng</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="stat-item">
                        <h3 class="display-3 fw-bold">1000+</h3>
                        <p class="fs-5">Học sinh hoạt động</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="stat-item">
                        <h3 class="display-3 fw-bold">50K+</h3>
                        <p class="fs-5">Câu hỏi AI đã trả lời</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="stat-item">
                        <h3 class="display-3 fw-bold">10K+</h3>
                        <p class="fs-5">Hình ảnh AI đã tạo</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="stat-item">
                        <h3 class="display-3 fw-bold">24/7</h3>
                        <p class="fs-5">Hỗ trợ không ngừng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section (cập nhật: dynamic theo session) -->
    <section class="py-5" style="background: white;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-4 fw-bold mb-4">🎯 Sẵn sàng bắt đầu?</h2>
                    <p class="fs-5 text-muted mb-4">
                        Tham gia cộng đồng học tập thông minh ngay hôm nay và trải nghiệm sức mạnh của AI trong giáo dục!
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php if (isset($_SESSION['username'])): ?>
                            <!-- Đã login: Ẩn đăng ký, thay bằng AI -->
                            <a href="/assistantai" class="cta-button">🤖 Bắt đầu với AI! 🚀</a>
                            <a href="/demo" class="cta-button secondary">🔍 Xem demo</a>
                        <?php else: ?>
                            <!-- Chưa login: Giữ nguyên đăng ký + demo -->
                            <a href="/register" class="cta-button">📝 Đăng ký miễn phí</a>
                            <a href="/demo" class="cta-button secondary">🔍 Xem demo</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/layouts/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>