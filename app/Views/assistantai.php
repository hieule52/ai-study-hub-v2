<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Hub - Trợ lý AI</title> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/assistantai.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
</head>

<body>
    <?php include __DIR__ . '/layouts/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="assistant-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-3 fw-bold mb-4">🤖 Trợ lý AI</h1>
                    <p class="fs-5 mb-4">
                        Khám phá sức mạnh của trí tuệ nhân tạo - Chọn công cụ AI phù hợp với nhu cầu học tập của bạn
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Features Section -->
    <section class="ai-features">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-4">✨ Chọn công cụ AI</h2>
                    <p class="fs-5 text-muted">
                        Mỗi công cụ được thiết kế đặc biệt để hỗ trợ học tập hiệu quả
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <!-- AI Chat -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card chat h-100">
                        <span class="ai-feature-icon">🤖</span>
                        <h3>AI Chat Thông Minh</h3>
                        <p>
                            Hỏi đáp với AI về mọi chủ đề học tập. Giải toán, văn học, khoa học...
                            AI sẽ hỗ trợ bạn 24/7 với câu trả lời chi tiết và dễ hiểu!
                        </p>
                        <a href="/ai-chat" class="ai-feature-btn">
                            💬 Bắt đầu chat
                        </a>
                    </div>
                </div>

                <!-- Text to Speech -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card tts h-100">
                        <span class="ai-feature-icon">🔊</span>
                        <h3>Chuyển Text thành Giọng nói</h3>
                        <p>
                            Nghe AI đọc bài học, từ vựng hay ghi chú.
                            Giọng đọc tự nhiên giúp bạn học tiếng anh tốt hơn!
                        </p>
                        <a href="/ai/text-to-speech" class="ai-feature-btn">
                            🎵 Tạo giọng nói
                        </a>
                    </div>
                </div>

                <!-- AI Image Generator -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card image h-100">
                        <span class="ai-feature-icon">🎨</span>
                        <h3>Tạo Hình Ảnh AI</h3>
                        <p>
                            Tạo hình ảnh minh họa cho dự án đặc biệt là các bạn học kiến trúc có thể dùng để luyện tập vẽ
                            bằng AI chỉ với mô tả văn bản đơn giản!
                        </p>
                        <a href="/ai/image-generator" class="ai-feature-btn">
                            🖼️ Tạo ảnh ngay
                        </a>
                    </div>
                </div>

                <!-- AI Homework Solver -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card homework h-100">
                        <span class="ai-feature-icon">📝</span>
                        <h3>AI Giải Bài Tập</h3>
                        <p>
                            Upload ảnh bài tập hoặc nhập đề bài, AI sẽ giải chi tiết từng bước.
                            Hỗ trợ toán, lý, hóa, văn và nhiều môn học khác!
                        </p>
                        <a href="/ai/homework-solver" class="ai-feature-btn">
                            ✏️ Giải bài tập
                        </a>
                    </div>
                </div>

                <!-- AI Content Summarizer -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card summarizer h-100">
                        <span class="ai-feature-icon">📄</span>
                        <h3>AI Tóm Tắt Nội Dung</h3>
                        <p>
                            Tóm tắt bài giảng, tài liệu, bài báo dài thành những ý chính ngắn gọn.
                            Tiết kiệm thời gian học tập hiệu quả!
                        </p>
                        <a href="/ai/summarizer" class="ai-feature-btn">
                            📋 Tóm tắt ngay
                        </a>
                    </div>
                </div>

                <!-- AI Quiz Generator -->
                <div class="col-lg-4 col-md-6">
                    <div class="ai-feature-card quiz h-100">
                        <span class="ai-feature-icon">🎯</span>
                        <h3>AI Tạo Quiz Trắc Nghiệm</h3>
                        <p>
                            Tạo bộ câu hỏi trắc nghiệm từ nội dung bài học.
                            Ôn tập hiệu quả với quiz tự động được AI tạo ra!
                        </p>
                        <a href="/ai/quiz-generator" class="ai-feature-btn">
                            🎲 Tạo quiz
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Info Section -->
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="p-4 bg-white rounded-4 shadow-sm border">
                        <h4 class="mb-3">💡 Mẹo sử dụng hiệu quả</h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <span style="font-size: 2em;">📝</span>
                                    <p class="mt-2 mb-0 small text-muted">
                                        <strong>Mô tả rõ ràng</strong><br>
                                        Càng chi tiết, AI hiểu càng chính xác
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <span style="font-size: 2em;">⚡</span>
                                    <p class="mt-2 mb-0 small text-muted">
                                        <strong>Phản hồi nhanh</strong><br>
                                        Kết quả trong vài giây
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <span style="font-size: 2em;">🔄</span>
                                    <p class="mt-2 mb-0 small text-muted">
                                        <strong>Thử lại nhiều lần</strong><br>
                                        Mỗi lần sẽ có kết quả khác nhau
                                    </p>
                                </div>
                            </div>
                        </div>
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