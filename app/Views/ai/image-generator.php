<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning Hub - AI Image Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/ai-images.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Image Generator Container -->
    <div class="image-container">
        <!-- Header -->
        <div class="image-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-content">
                            <div class="ai-avatar">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="image-title">🎨 AI Image Generator</h1>
                                <p class="image-subtitle">Tạo hình ảnh tuyệt đẹp từ mô tả văn bản với AI</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="image-stats">
                            <div class="stat-item">
                                <span class="stat-number">∞</span>
                                <span class="stat-label">Miễn phí</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">🎨</span>
                                <span class="stat-label">Sáng tạo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="image-main">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Input Section -->
                        <div class="input-container">
                            <h3 class="input-title">
                                <i class="fas fa-pen-fancy"></i> Mô tả hình ảnh bạn muốn tạo
                            </h3>

                            <textarea
                                id="promptInput"
                                class="prompt-input"
                                placeholder="Lưu ý: chỉ nên dùng tiếng anh mô tả để có thể chính xác tạo ra ảnh Ví dụ: A beautiful sunset over mountains with a lake in the foreground, digital art style..."
                                rows="4"></textarea>

                            <!-- Style Selection -->
                            <div class="style-selection mt-3">
                                <label class="form-label">
                                    <i class="fas fa-palette"></i> Chọn phong cách:
                                </label>
                                <div class="style-grid">
                                    <button class="style-btn active" data-style="">
                                        <i class="fas fa-magic"></i> Auto
                                    </button>
                                    <button class="style-btn" data-style="digital art">
                                        <i class="fas fa-laptop"></i> Digital Art
                                    </button>
                                    <button class="style-btn" data-style="anime">
                                        <i class="fas fa-dragon"></i> Anime
                                    </button>
                                    <button class="style-btn" data-style="realistic">
                                        <i class="fas fa-camera"></i> Realistic
                                    </button>
                                    <button class="style-btn" data-style="oil painting">
                                        <i class="fas fa-paint-brush"></i> Oil Painting
                                    </button>
                                    <button class="style-btn" data-style="3d render">
                                        <i class="fas fa-cube"></i> 3D Render
                                    </button>
                                </div>
                            </div>

                            <!-- Size Selection -->
                            <div class="size-selection mt-3">
                                <label class="form-label">
                                    <i class="fas fa-expand"></i> Kích thước:
                                </label>
                                <select id="sizeSelect" class="form-select">
                                    <option value="512x512">Square (512x512)</option>
                                    <option value="768x512">Landscape (768x512)</option>
                                    <option value="512x768">Portrait (512x768)</option>
                                    <option value="1024x1024">Large Square (1024x1024)</option>
                                </select>
                            </div>

                            <!-- Generate Button -->
                            <div class="generate-section mt-4">
                                <button id="generateBtn" class="btn-generate">
                                    <i class="fas fa-magic"></i> Tạo hình ảnh
                                </button>
                                <div class="loading-indicator" id="loadingIndicator" style="display: none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Đang tạo hình ảnh... Vui lòng đợi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Result Section -->
                        <div class="result-container" id="resultContainer" style="display: none">
                            <h3 class="result-title"><i class="fas fa-check-circle"></i> Kết quả</h3>
                            <div class="result-image-wrapper">
                                <img id="resultImage" src="" alt="Generated Image" class="result-image">
                                <div class="image-actions">
                                    <button class="action-btn" onclick="downloadImage()">
                                        <i class="fas fa-download"></i> Tải xuống
                                    </button>
                                    <button class="action-btn" onclick="shareImage()">
                                        <i class="fas fa-share"></i> Chia sẻ
                                    </button>
                                </div>
                            </div>
                            <div class="result-prompt" id="resultPrompt"></div>
                        </div>

                        <!-- Examples -->
                        <div class="examples-section">
                            <h3 class="examples-title">💡 Ví dụ mô tả:</h3>
                            <div class="examples-grid">
                                <button
                                    class="example-btn"
                                    onclick="setExample('A majestic dragon flying over a medieval castle at sunset, fantasy art')">
                                    🐉 Fantasy Dragon
                                </button>
                                <button
                                    class="example-btn"
                                    onclick="setExample('A cute robot reading a book in a cozy library, digital illustration')">
                                    🤖 Robot Reading
                                </button>
                                <button
                                    class="example-btn"
                                    onclick="setExample('A beautiful cherry blossom tree in full bloom, anime style')">
                                    🌸 Cherry Blossom
                                </button>
                                <button
                                    class="example-btn"
                                    onclick="setExample('A futuristic city with flying cars and neon lights, cyberpunk style')">
                                    🌃 Cyberpunk City
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/ai-images.js"></script>
</body>

</html>