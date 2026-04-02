<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning Hub - Giải Bài Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/ai-homework.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Chat Container -->
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-content">
                            <div class="ai-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="chat-title">📚 AI Giải Bài Tập</h1>
                                <p class="chat-subtitle">Trợ lý AI giúp bạn giải bài tập từng bước chi tiết</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chat-stats">
                            <div class="stat-item">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Sẵn sàng</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">∞</span>
                                <span class="stat-label">Bài tập</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Chat Messages -->
                        <div class="chat-messages" id="chatMessages">
                            <!-- Default AI welcome message -->
                            <div class="message ai-message">
                                <div class="message-avatar">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <p>Xin chào! Tôi là AI Homework Solver.</p>
                                        <p>Hãy nhập đề bài để tôi giúp bạn giải chi tiết từng bước!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- HISTORY FROM DATABASE -->
                            <?php if (!empty($homeworkHistory)): ?>
                                <?php foreach ($homeworkHistory as $hw): ?>

                                    <!-- USER MESSAGE -->
                                    <div class="message user-message">
                                        <div class="message-content">
                                            <div class="message-bubble">
                                                <?= nl2br(htmlspecialchars($hw['input_text'])) ?>
                                            </div>
                                        </div>
                                        <div class="message-avatar"><i class="fas fa-user"></i></div>
                                    </div>

                                    <!-- AI RESPONSE -->
                                    <div class="message ai-message">
                                        <div class="message-avatar"><i class="fas fa-robot"></i></div>
                                        <div class="message-content">
                                            <div class="message-bubble">
                                                <?= nl2br(htmlspecialchars($hw['output_text'])) ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input-container">
                            <form method="POST" action="/ai/homework-solver" class="chat-form" id="chatForm">
                                <div class="input-wrapper">
                                    <div class="input-actions">
                                        <button type="button" class="action-btn" title="Đính kèm file">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                        <button type="button" class="action-btn" title="Ghi âm">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    <textarea
                                        name="message"
                                        class="chat-input"
                                        placeholder="Nhập đề bài cần giải..."
                                        rows="1"
                                        required></textarea>
                                    <button type="submit" class="send-btn" disabled>
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Quick Suggestions -->
                        <div class="quick-suggestions">
                            <div class="suggestions-title">💡 Gợi ý bài tập:</div>
                            <div class="suggestions-list">
                                <button class="suggestion-btn" onclick="setSuggestion('Giải phương trình: 2x + 5 = 15')">
                                    🔢 Giải phương trình: 2x + 5 = 15
                                </button>
                                <button class="suggestion-btn" onclick="setSuggestion('Tính diện tích hình tròn bán kính 5cm')">
                                    📐 Tính diện tích hình tròn r=5cm
                                </button>
                                <button class="suggestion-btn" onclick="setSuggestion('Phân tích đa thức: x² - 4')">
                                    ➗ Phân tích đa thức: x² - 4
                                </button>
                                <button class="suggestion-btn" onclick="setSuggestion('Giải hệ phương trình: x+y=5, x-y=1')">
                                    📊 Giải hệ phương trình
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/ai-homework.js"></script>
</body>

</html>