<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning Hub - English Practice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/ai-tts.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- TTS Container -->
    <div class="tts-container">
        <!-- TTS Header -->
        <div class="tts-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-content">
                            <div class="ai-avatar">
                                <i class="fas fa-volume-up"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="tts-title">🎓 English Listening & Speaking</h1>
                                <p class="tts-subtitle">Luyện nghe và phát âm tiếng Anh với AI</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="tts-stats">
                            <div class="stat-item">
                                <span class="stat-number">100+</span>
                                <span class="stat-label">Câu mẫu</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">🎯</span>
                                <span class="stat-label">Luyện tập</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main TTS Area -->
        <div class="tts-main">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">

                        <!-- TTS Input -->
                        <div class="tts-input-container">
                            <h3 class="input-title">
                                <i class="fas fa-headphones"></i> Chọn câu tiếng Anh để luyện tập
                            </h3>
                            <textarea
                                id="ttsInput"
                                class="tts-input"
                                placeholder="Nhập văn bản tiếng Anh cần luyện phát âm..."
                                rows="8"></textarea>

                            <!-- Voice Settings -->
                            <div class="voice-settings">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>
                                            <i class="fas fa-user"></i> Giọng nói:
                                        </label>
                                        <select id="voiceSelect" class="form-select">
                                            <option>Đang tải...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>
                                            <i class="fas fa-tachometer-alt"></i> Tốc độ:
                                        </label>
                                        <input
                                            type="range"
                                            id="rateInput"
                                            class="form-range"
                                            min="0.5"
                                            max="2"
                                            step="0.1"
                                            value="1" />
                                        <span id="rateValue">1.0x</span>
                                    </div>
                                    <div class="col-md-4">
                                        <label>
                                            <i class="fas fa-volume-up"></i> Âm lượng:
                                        </label>
                                        <input
                                            type="range"
                                            id="volumeInput"
                                            class="form-range"
                                            min="0"
                                            max="1"
                                            step="0.1"
                                            value="1" />
                                        <span id="volumeValue">100%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Control Buttons -->
                            <div class="control-buttons">
                                <button id="speakBtn" class="btn-speak">
                                    <i class="fas fa-play"></i> Phát
                                </button>
                                <button id="pauseBtn" class="btn-pause" disabled>
                                    <i class="fas fa-pause"></i> Tạm dừng
                                </button>
                                <button id="resumeBtn" class="btn-resume" disabled>
                                    <i class="fas fa-play"></i> Tiếp tục
                                </button>
                                <button id="stopBtn" class="btn-stop" disabled>
                                    <i class="fas fa-stop"></i> Dừng
                                </button>
                            </div>
                        </div>

                        <!-- Quick Suggestions -->
                        <div class="quick-suggestions">
                            <div class="suggestions-title">💡 Câu mẫu để luyện tập:</div>
                            <div class="suggestions-list">
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Hello, how are you today? Nice to meet you!')">
                                    👋 Greetings (Beginner)
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Could you please help me with this problem? I would really appreciate it.')">
                                    🙏 Polite Request (Intermediate)
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('The weather is beautiful today. Let us go for a walk in the park.')">
                                    ☀️ Daily Conversation (Beginner)
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Practice makes perfect. Keep learning and never give up on your dreams.')">
                                    💪 Motivation (Intermediate)
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/ai-tts.js"></script>
</body>

</html>