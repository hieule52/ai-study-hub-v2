<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning Hub - Chat AI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/ai-chat.css">
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
                                <h1 class="chat-title">🤖 Chat AI Thông Minh</h1>
                                <p class="chat-subtitle">Trợ lý AI sẵn sàng giải đáp mọi thắc mắc học tập của bạn</p>
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
                                <span class="stat-label">Câu hỏi</span>
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

                        <!-- CHAT MESSAGES -->
                        <div class="chat-messages" id="chatMessages">

                            <!-- Default AI welcome message -->
                            <div class="message ai-message">
                                <div class="message-avatar">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <p>Xin chào! Tôi là AI Learning Assistant.</p>
                                        <p>Hãy đặt câu hỏi để chúng ta bắt đầu!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- HISTORY FROM DATABASE -->
                            <?php if (!empty($chatHistory)): ?>
                                <?php foreach ($chatHistory as $chat): ?>

                                    <!-- USER MESSAGE -->
                                    <div class="message user-message">
                                        <div class="message-content">
                                            <div class="message-bubble">
                                                <?= nl2br(htmlspecialchars($chat['input_text'])) ?>
                                            </div>
                                        </div>
                                        <div class="message-avatar"><i class="fas fa-user"></i></div>
                                    </div>

                                    <!-- AI RESPONSE -->
                                    <div class="message ai-message">
                                        <div class="message-avatar"><i class="fas fa-robot"></i></div>
                                        <div class="message-content">
                                            <div class="message-bubble">
                                                <?= nl2br(htmlspecialchars($chat['output_text'])) ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- USER NEW MESSAGE -->
                            <?php if (!empty($_POST['message'])): ?>
                                <div class="message user-message">
                                    <div class="message-content">
                                        <div class="message-bubble">
                                            <?= htmlspecialchars($_POST['message']) ?>
                                        </div>
                                    </div>
                                    <div class="message-avatar"><i class="fas fa-user"></i></div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($response)): ?>
                                <div class="message ai-message">
                                    <div class="message-avatar"><i class="fas fa-robot"></i></div>
                                    <div class="message-content">
                                        <div class="message-bubble">
                                            <?= $response ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- CHAT INPUT -->
                        <div class="chat-input-container">
                            <form method="POST" action="/ai-chat" class="chat-form" id="chatForm">
                                <div class="input-wrapper">

                                    <div class="input-actions">
                                        <button type="button" class="action-btn"><i class="fas fa-paperclip"></i></button>
                                        <button type="button" class="action-btn"><i class="fas fa-microphone"></i></button>
                                    </div>
                                    <textarea
                                        name="message"
                                        class="chat-input"
                                        placeholder="Nhập câu hỏi của bạn..."
                                        rows="1"
                                        required></textarea>

                                    <button type="submit" class="send-btn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>

                                </div>
                            </form>
                        </div>

                        <!-- QUICK SUGGESTIONS -->
                        <div class="quick-suggestions">
                            <div class="suggestions-title">💡 Gợi ý câu hỏi:</div>

                            <div class="suggestions-list">
                                <button class="suggestion-btn" onclick="setSuggestion('Giải thích định lý Pythagore')">🔢 Giải thích định lý Pythagore</button>
                                <button class="suggestion-btn" onclick="setSuggestion('Phân tích bài thơ Tràng Giang')">📜 Phân tích bài thơ Tràng Giang</button>
                                <button class="suggestion-btn" onclick="setSuggestion('Cách học từ vựng tiếng Anh hiệu quả')">🇬🇧 Cách học từ vựng tiếng Anh</button>
                                <button class="suggestion-btn" onclick="setSuggestion('Giải phương trình bậc hai')">➗ Giải phương trình bậc hai</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/ai-chat.js"></script>

</body>

</html>