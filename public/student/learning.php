$pageTitle = 'Đang Học - AI Study Hub';
$actor = 'guest';
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
';
ob_start();
?>
<style>
        body {
            background: #000; /* Cinematic background */
        }
        
        .learning-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .main-player {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }

        .video-wrapper {
            width: 100%;
            background: #000;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .content-area {
            flex: 1;
            padding: 3rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Sidebar Glassmorphism */
        .curriculum-sidebar {
            width: 380px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .chapter-title {
            padding: 1.25rem 1.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .lesson-item {
            padding: 1rem 1.5rem 1rem 2.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
        }

        .lesson-item:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--text-primary);
        }

        .lesson-item.playing {
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.15) 0%, transparent 100%);
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        /* AI Floating Widget */
        .ai-chat-btn {
            position: fixed;
            bottom: 40px;
            right: 420px;
            background: linear-gradient(135deg, #4f46e5, #ec4899);
            color: white;
            border: none;
            border-radius: 50%;
            width: 65px;
            height: 65px;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.5);
            cursor: pointer;
            z-index: 1000;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: bounce-glow 3s infinite;
        }

        @keyframes bounce-glow {
            0%, 100% { transform: translateY(0); box-shadow: 0 10px 25px rgba(236, 72, 153, 0.5); }
            50% { transform: translateY(-10px); box-shadow: 0 20px 35px rgba(236, 72, 153, 0.8); }
        }

        .ai-chat-btn:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .ai-popup {
            position: fixed;
            bottom: 120px;
            right: 420px;
            width: 380px;
            height: 600px;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(236, 72, 153, 0.3);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(236, 72, 153, 0.1);
            display: flex;
            flex-direction: column;
            z-index: 999;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .ai-popup.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        .ai-header {
            padding: 1.25rem;
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.2), rgba(236, 72, 153, 0.2));
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ai-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .msg {
            max-width: 85%;
            padding: 1rem 1.25rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .msg.user {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .msg.bot {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .ai-input-area {
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            gap: 0.75rem;
        }

        .ai-input-area input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            padding: 0.75rem 1.25rem;
        }

        /* Quiz UI Premium */
        .quiz-container {
            margin-top: 3rem;
            padding: 2.5rem;
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: var(--radius-xl);
            box-shadow: inset 0 0 20px rgba(16, 185, 129, 0.05);
        }

        .question-block {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(0,0,0,0.2);
            border-radius: var(--radius-lg);
        }

        .answer-option {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
        }

        .answer-option:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .answer-option input[type="radio"] {
<style>

        /* Sidebar Glassmorphism */
        .curriculum-sidebar {
            width: 380px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .chapter-title {
            padding: 1.25rem 1.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .lesson-item {
            padding: 1rem 1.5rem 1rem 2.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-secondary);
        }

        .lesson-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .lesson-item.playing {
            background: rgba(79, 70, 229, 0.15);
            color: var(--primary);
            border-left: 4px solid var(--primary);
            font-weight: 600;
        }

        .lesson-item .type-icon {
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .lesson-item.playing .type-icon {
            opacity: 1;
        }

        /* AI Floating Widget */
        .ai-chat-btn {
            position: fixed;
            bottom: 40px;
            right: 420px;
            background: linear-gradient(135deg, #4f46e5, #ec4899);
            color: white;
            border: none;
            border-radius: 50%;
            width: 65px;
            height: 65px;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.5);
            cursor: pointer;
            z-index: 1000;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: bounce-glow 3s infinite;
        }

        @keyframes bounce-glow {
            0%, 100% { transform: translateY(0); box-shadow: 0 10px 25px rgba(236, 72, 153, 0.5); }
            50% { transform: translateY(-10px); box-shadow: 0 20px 35px rgba(236, 72, 153, 0.8); }
        }

        .ai-chat-btn:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .ai-popup {
            position: fixed;
            bottom: 120px;
            right: 420px;
            width: 380px;
            height: 600px;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(236, 72, 153, 0.3);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(236, 72, 153, 0.1);
            display: flex;
            flex-direction: column;
            z-index: 999;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .ai-popup.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        .ai-header {
            padding: 1.25rem;
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.2), rgba(236, 72, 153, 0.2));
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ai-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .msg {
            max-width: 85%;
            padding: 1rem 1.25rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .msg.user {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .msg.bot {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .ai-input-area {
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            gap: 0.75rem;
        }

        .ai-input-area input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            padding: 0.75rem 1.25rem;
        }

        /* Quiz UI Premium */
        .quiz-container {
            margin-top: 3rem;
            padding: 2.5rem;
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: var(--radius-xl);
            box-shadow: inset 0 0 20px rgba(16, 185, 129, 0.05);
        }

        .question-block {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(0,0,0,0.2);
            border-radius: var(--radius-lg);
        }

        .answer-option {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
        }

        .answer-option:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .answer-option input[type="radio"] {
            margin-right: 1rem;
            transform: scale(1.2);
            accent-color: var(--success);
        }

        .quiz-submit-btn {
            background: linear-gradient(135deg, var(--success), #059669);
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        /* Custom Content Styling over Quill */
        .ql-editor {
            font-family: inherit;
            font-size: 1.15rem;
            color: #cbd5e1;
            line-height: 1.8;
            padding: 0;
            white-space: pre-wrap; /* Fix line breaks for pasted text */
        }
        .ql-editor h1, .ql-editor h2, .ql-editor h3 {
            color: #f8fafc;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            font-weight: 800;
            border: none;
        }
        .ql-editor p {
            margin-bottom: 1.5rem;
        }
        .ql-editor code {
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 6px;
            font-family: 'Fira Code', monospace;
            font-size: 0.9em;
            color: #f472b6; /* Pinkish code */
        }
        .ql-editor pre {
            background: #0f172a;
            padding: 2rem;
            border-radius: 16px;
            overflow-x: auto;
            margin: 2rem 0;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .ql-editor pre code {
            background: transparent;
            padding: 0;
            color: inherit;
        }
        .ql-editor blockquote {
            border-left: 5px solid var(--primary);
            padding: 1rem 2rem;
            margin: 2rem 0;
            background: rgba(79, 70, 229, 0.05);
            border-radius: 0 12px 12px 0;
            color: #94a3b8;
            font-style: italic;
        }
        .ql-editor img {
            max-width: 100%;
            border-radius: 20px;
            margin: 2.5rem 0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        /* Quill Alignment Display Fix */
        .ql-align-center { text-align: center !important; }
        .ql-align-right { text-align: right !important; }
        .ql-align-justify { text-align: justify !important; }

        /* Objectives Styling */
        .objectives-card {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(236, 72, 153, 0.05));
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
        }
        .objectives-icon {
            font-size: 1.8rem;
            background: rgba(79, 70, 229, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .objectives-list {
            flex: 1;
        }
        .objectives-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        .objectives-items {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .objective-tag {
            background: rgba(255,255,255,0.05);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-secondary);
        }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="learning-layout">

        <!-- Left: Video Area -->
        <div class="main-player">
            <!-- Go Back nav -->
            <div style="padding: 1rem 2rem; background: transparent; position: absolute; top: 0; left: 0; right: 0; z-index: 20; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);">
                <a href="/student/dashboard.php" class="btn btn-outline" style="border-radius: 20px; font-size: 0.9rem; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);" data-i18n="lrn_back_home">&larr; Quay lại Home</a>
                <span style="font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,0.8);" id="course_title_span">...</span>
            </div>

            <!-- Video Player -->
            <div class="video-wrapper" id="video_wrapper">
                <div style="text-align: center;">
                    <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🎬</div>
                    <h2 class="text-secondary" id="video_placeholder" data-i18n="lrn_video_placeholder">Trình phát Video sẽ mô phỏng ở đây.</h2>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="flex justify-between items-center mb-6">
                    <h1 id="lesson_title" style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1px;">...</h1>
                    <button class="btn btn-primary" id="btn_mark_complete" style="display:none; border-radius: 20px;" onclick="markComplete()" data-i18n="lrn_mark_complete">✅ Đánh dấu Đã Học</button>
                </div>

                <!-- Learning Objectives Container -->
                <div id="objectives_container"></div>
                
                <div id="lesson_content">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem; opacity: 0.7;" data-i18n="lrn_select_lesson">
                        <span>👈</span> Chọn một bài học ở danh mục bên phải để bắt đầu.
                    </div>
                </div>

                <!-- Khu vực Quiz -->
                <div id="quiz_area"></div>
            </div>
        </div>

        <!-- Right: Curriculum Sidebar -->
        <div class="curriculum-sidebar">
            <div style="padding: 2rem 1.5rem; background: rgba(0,0,0,0.2);">
                <h3 style="font-size: 1.25rem;" data-i18n="lrn_curriculum">Nội Dung Cùng Khóa</h3>
                <div class="flex items-center justify-between gap-2 mt-2">
                    <div style="font-size: 0.875rem; color: var(--success); font-weight: 600;">
                        <span id="curriculum-progress" data-i18n="lrn_progress">Tiến độ: --</span>
                    </div>
                    <button id="btn_review_course" onclick="showReviewModal()" style="display:none;" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">⭐ Đánh giá</button>
                </div>
            </div>

            <div style="flex: 1; overflow-y: auto;" id="curriculumList">
                <div class="p-4 text-center text-muted" data-i18n="lrn_loading_curriculum">Đang tải giáo trình...</div>
            </div>
        </div>

    </div>

    <!-- AI Chat Popup & Button -->
    <button class="ai-chat-btn" onclick="toggleAIChat()" title="Gọi trợ lý AI">✨</button>
    
    <div class="ai-popup" id="aiPopup">
        <!-- AI Header -->
        <div class="ai-header">
            <div class="flex items-center gap-3">
                <div style="width: 35px; height: 35px; background: #fff; border-radius: 50%; display: flex; align-items:center; justify-content:center; font-size: 1.2rem; box-shadow: 0 0 10px rgba(255,255,255,0.5);">🧠</div>
                <div>
                    <div style="line-height: 1; margin-bottom: 2px;">Trợ lý AI</div>
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.7);">Luôn sẵn sàng hỗ trợ</div>
                </div>
            </div>
            <span style="font-size: 0.65rem; color: #fff; background: linear-gradient(135deg, var(--secondary), var(--warning)); padding: 0.2rem 0.6rem; border-radius: 12px; font-weight: bold; box-shadow: 0 2px 5px rgba(236,72,153,0.3);">PRO VIP</span>
        </div>
        
        <!-- AI Messages Body -->
        <div class="ai-messages" id="chatBox">
            <div class="msg bot">✌️ Chào bạn! Tôi là AI Tutor trực tuyến của bạn. Bạn gặp phần nào khó hiểu trong bài giảng này, cứ mạnh dạn hỏi tôi nhé!</div>
        </div>

        <!-- AI Input -->
        <form class="ai-input-area" id="chatForm">
            <input type="text" id="chatInput" class="form-control" placeholder="Hỏi AI về nội dung bài..." required style="flex: 1;">
            <button type="submit" class="btn btn-primary" style="padding: 0; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items:center; justify-content:center; background: var(--secondary);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 2L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background: var(--bg-dark); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid rgba(255,255,255,0.1); width: 90%; max-width: 500px;">
            <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.5rem;">⭐ Đánh giá Khóa Học</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">Chúc mừng bạn đã hoàn thành khóa học! Hãy để lại đánh giá của mình nhé.</p>
            <form id="reviewForm" onsubmit="submitReview(event)">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">Đánh giá (1-5 sao)</label>
                    <select id="reviewRating" class="form-control" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1);" required>
                        <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>
                        <option value="4">⭐⭐⭐⭐ (4 sao)</option>
                        <option value="3">⭐⭐⭐ (3 sao)</option>
                        <option value="2">⭐⭐ (2 sao)</option>
                        <option value="1">⭐ (1 sao)</option>
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">Nhận xét</label>
                    <textarea id="reviewComment" class="form-control" rows="4" placeholder="Khóa học rất hay..." style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1);"></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('reviewModal').style.display='none'">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitReview">Gửi Đánh Giá</button>
                </div>
            </form>
        </div>
    </div>

<?php ob_start(); ?>
<script>
        const user = App.requireAuth();

        // === ROLE GUARD: Block admin/teacher from student learning flow ===
        if (user && user.role === 'admin') {
            const redirectUrl = '/admin/preview-course.php' + window.location.search;
            window.location.replace(redirectUrl);
            throw new Error('Redirecting admin to preview mode');
        }
        if (user && user.role === 'teacher') {
            const redirectUrl = '/teacher/course-builder.php' + window.location.search;
            window.location.replace(redirectUrl);
            throw new Error('Redirecting teacher to course builder');
        }

        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get('course_id');
        let currentLessonId = null;
        let currentQuizId = null;

        document.addEventListener('DOMContentLoaded', async () => {
            if (!courseId) {
                App.showToast('Không tìm thấy ID khóa học ở URL', 'error');
                return;
            }
            await loadCurriculum();
        });

        async function loadCurriculum() {
            try {
                // Fetch course info
                const courseRes = await window.api.get(`/courses/${courseId}`);
                document.getElementById('course_title_span').innerText = courseRes.data.title;

                // Cấu trúc giáo trình
                const res = await window.api.get(`/courses/${courseId}/curriculum`);
                const chapters = res.data;
                const sidebar = document.getElementById('curriculumList');
                sidebar.innerHTML = '';

                let totalLessons = 0;

                if (chapters.length === 0) {
                    sidebar.innerHTML = '<div class="p-4 text-secondary text-center" data-i18n="lrn_no_content">Chưa có nội dung.</div>';
                    if (window.I18n) window.I18n.render();
                    return;
                }

                chapters.forEach(chap => {
                    const chDiv = document.createElement('div');
                    chDiv.className = 'chapter-title';
                    chDiv.innerText = chap.title;
                    sidebar.appendChild(chDiv);

                    if (chap.lessons && chap.lessons.length > 0) {
                        chap.lessons.forEach(lesson => {
                            totalLessons++;
                            const lesDiv = document.createElement('div');
                            lesDiv.className = 'lesson-item';
                            lesDiv.id = `nav-lesson-${lesson.id}`;
                            lesDiv.innerHTML = `<span>📺</span> <span style="flex: 1">${lesson.title}</span>`;
                            lesDiv.onclick = () => loadLesson(lesson.id);
                            sidebar.appendChild(lesDiv);
                        });
                    }
                });

                document.getElementById('curriculum-progress').innerHTML = `<span data-i18n="lrn_total">Tổng số: </span>${totalLessons}<span data-i18n="lrn_lessons"> bài học</span>`;
                if (window.I18n) window.I18n.render();

                // Check progress to show review button
                try {
                    const enrolledRes = await window.api.get('/student/courses');
                    const myCourse = enrolledRes.data.find(c => c.id == courseId);
                    if (myCourse && myCourse.progress_percent === 100) {
                        document.getElementById('btn_review_course').style.display = 'block';
                    }
                } catch(e) { console.log(e); }

            } catch (err) {
                App.showToast(err.message, 'error');
            }
        }

        async function loadLesson(lessonId) {
            currentLessonId = lessonId;
            currentQuizId = null;

            // Update UI State
            document.querySelectorAll('.lesson-item').forEach(el => {
                el.classList.remove('playing');
                if(el.innerHTML.includes('▶️')) {
                    el.innerHTML = el.innerHTML.replace('▶️', '📺');
                }
            });
            const activeEl = document.getElementById(`nav-lesson-${lessonId}`);
            if (activeEl) {
                activeEl.classList.add('playing');
                activeEl.innerHTML = activeEl.innerHTML.replace('📺', '▶️');
            }

            try {
                const res = await window.api.get(`/lessons/${lessonId}`);
                const lesson = res.data;

                document.getElementById('lesson_title').innerText = lesson.title;
                
                // Show objectives if any
                const objContainer = document.getElementById('objectives_container');
                if (lesson.objectives) {
                    const tags = lesson.objectives.split(',').map(t => `<span class="objective-tag">${t.trim()}</span>`).join('');
                    objContainer.innerHTML = `
                        <div class="objectives-card">
                            <div class="objectives-icon">🎯</div>
                            <div class="objectives-list">
                                <div class="objectives-title">Bạn sẽ học được gì:</div>
                                <div class="objectives-items">${tags}</div>
                            </div>
                        </div>
                    `;
                } else {
                    objContainer.innerHTML = '';
                }

                // Render HTML Content (from Quill)
                if (lesson.content) {
                    document.getElementById('lesson_content').innerHTML = `
                    <div class="ql-snow">
                        <div class="ql-editor" style="background: rgba(255,255,255,0.02); padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid rgba(255,255,255,0.05);">
                            ${lesson.content}
                        </div>
                    </div>`;
                    // Highlight code blocks
                    setTimeout(() => {
                        document.querySelectorAll('pre').forEach((block) => {
                            hljs.highlightElement(block);
                        });
                    }, 100);
                } else {
                    document.getElementById('lesson_content').innerHTML = '<div class="text-muted" data-i18n="lrn_no_desc">Giảng viên chưa cập nhật mô tả chi tiết bài học này.</div>';
                }

                // Completion status
                const btnMark = document.getElementById('btn_mark_complete');
                if (lesson.is_completed) {
                    btnMark.style.display = 'block';
                    btnMark.innerHTML = '✅ Đã hoàn thành bài học';
                    btnMark.classList.replace('btn-primary', 'btn-outline');
                    btnMark.style.color = 'var(--success)';
                    btnMark.style.borderColor = 'var(--success)';
                    btnMark.disabled = true;
                } else {
                    if (lesson.content_type === 'quiz') {
                        btnMark.style.display = 'none'; // Require quiz submission
                    } else {
                        btnMark.style.display = 'block';
                        btnMark.innerHTML = '✅ Đánh dấu Đã Học';
                        btnMark.classList.add('btn-primary');
                        btnMark.classList.remove('btn-outline');
                        btnMark.style.color = '';
                        btnMark.style.borderColor = '';
                        btnMark.disabled = false;
                    }
                }

                // === VIDEO DISPLAY LOGIC ===
                const videoWrapper = document.getElementById('video_wrapper');
                
                if (lesson.content_type === 'video') {
                    videoWrapper.style.display = 'flex';
                    if (lesson.video_filename) {
                        // Secured video: lấy signed token rồi stream
                        try {
                            videoWrapper.innerHTML = `
                                <div style="text-align: center;">
                                    <div style="font-size: 3rem; animation: spin 2s linear infinite;">⏳</div>
                                    <p class="text-secondary mt-2">Đang tải video bảo mật...</p>
                                </div>`;

                            const tokenRes = await window.api.get(`/video/token/${lessonId}?course_id=${courseId}`);
                            const streamUrl = tokenRes.data.stream_url;

                            videoWrapper.innerHTML = `
                                <video id="secureVideoPlayer" controls controlsList="nodownload" disablePictureInPicture
                                       style="width:100%;height:100%;background:#000;"
                                       oncontextmenu="return false;">
                                    <source src="${streamUrl}" type="video/mp4">
                                    Trình duyệt không hỗ trợ video.
                                </video>`;

                            // Thêm event listener cho video errors
                            const videoEl = document.getElementById('secureVideoPlayer');
                            if (videoEl) {
                                videoEl.addEventListener('error', () => {
                                    videoWrapper.innerHTML = `
                                        <div style="text-align:center;padding:2rem;">
                                            <div style="font-size:3rem;opacity:0.5;">⚠️</div>
                                            <p class="text-secondary">Video không thể phát. Vui lòng tải lại trang.</p>
                                        </div>`;
                                });
                            }
                        } catch(videoErr) {
                            videoWrapper.innerHTML = `
                                <div style="text-align: center;">
                                    <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🔒</div>
                                    <h2 class="text-secondary">${videoErr.message || 'Không thể tải video'}</h2>
                                    <p class="text-muted mt-2">Vui lòng đảm bảo bạn đã đăng ký khóa học này.</p>
                                </div>`;
                        }
                    } else if (lesson.video_url) {
                        // External URL (YouTube, Vimeo, etc.)
                        const url = lesson.video_url;
                        if (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com')) {
                            videoWrapper.innerHTML = `<iframe src="${url}" width="100%" height="100%" frameborder="0" allowfullscreen style="box-shadow: 0 10px 40px rgba(0,0,0,0.8);"></iframe>`;
                        } else {
                            // Direct video URL
                            videoWrapper.innerHTML = `
                                <video controls style="width:100%;height:100%;background:#000;">
                                    <source src="${url}" type="video/mp4">
                                </video>`;
                        }
                    } else {
                        videoWrapper.innerHTML = `
                            <div style="text-align: center;">
                                <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🎥</div>
                                <h2 class="text-secondary" data-i18n="lrn_video_placeholder">Trình phát Video sẽ mô phỏng ở đây.</h2>
                            </div>`;
                    }
                } else {
                    videoWrapper.style.display = 'none';
                    videoWrapper.innerHTML = '';
                }

                loadQuizData(lessonId);
                if (window.I18n) window.I18n.render();

            } catch (err) {
                App.showToast(err.message, 'error');
            }
        }

        async function loadQuizData(lessonId) {
            const quizArea = document.getElementById('quiz_area');
            quizArea.innerHTML = '';
            try {
                const res = await window.api.get(`/lessons/${lessonId}/quiz`);
                if (res.data && res.data.id) {
                    currentQuizId = res.data.id;
                    const quiz = res.data;

                    let html = `<div class="quiz-container">
                        <div class="flex items-center gap-3 mb-6">
                            <div style="font-size: 2rem;">🧠</div>
                            <div>
                                <h3 style="color: var(--success); font-size: 1.5rem; margin-bottom: 5px;" data-i18n="lrn_quiz_title">Thử thách trí tuệ</h3>
                                <p class="text-secondary text-sm">${quiz.title}</p>
                            </div>
                        </div>
                        <form id="quizForm">`;

                    if (quiz.questions) {
                        quiz.questions.forEach((q, qIndex) => {
                            html += `<div class="question-block" id="qb_${q.id}">
                                <p style="font-weight: 600; font-size: 1.1rem; margin-bottom: 1rem; color: #fff;">Câu ${qIndex + 1}: ${q.question}</p>
                                <div class="options-grid">`;
                            q.options.forEach(ans => {
                                html += `<label class="answer-option">
                                    <input type="radio" name="q_${q.id}" value="${ans.id}" required>
                                    <span style="flex: 1">${ans.answer_text}</span>
                                </label>`;
                            });
                            html += `</div></div>`;
                        });
                    }

                    html += `<button type="submit" class="btn btn-primary quiz-submit-btn" data-i18n="lrn_quiz_submit">Nộp Bài Kiểm Tra</button>
                    </form></div>`;

                    quizArea.innerHTML = html;
                    if (window.I18n) window.I18n.render();

                    document.getElementById('quizForm').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        const answers = {};
                        for (let [key, value] of formData.entries()) {
                            const qId = key.replace('q_', '');
                            answers[qId] = parseInt(value);
                        }

                        try {
                            const btnSubmit = e.target.querySelector('button[type="submit"]');
                            btnSubmit.innerHTML = window.I18n ? window.I18n.get('lrn_quiz_grading') : 'Đang chấm điểm...';
                            btnSubmit.disabled = true;

                            const submitRes = await window.api.post(`/quizzes/${currentQuizId}/submit`, { answers });
                            
                            const successMsg = window.I18n ? window.I18n.get('lrn_quiz_success') : 'Tuyệt vời! Điểm của bạn là: ';
                            App.showToast(`${successMsg}${submitRes.data.score}`, 'success');
                            
                            const resultMsg = window.I18n ? window.I18n.get('lrn_quiz_result') : 'Hoàn thành! KẾT QUẢ: ';
                            btnSubmit.innerHTML = `${resultMsg}${submitRes.data.score}`;
                            btnSubmit.style.background = 'var(--success)';
                            markComplete();
                        } catch (err) {
                            App.showToast(err.message, 'error');
                            e.target.querySelector('button[type="submit"]').innerHTML = 'Nộp Lại';
                            e.target.querySelector('button[type="submit"]').disabled = false;
                        }
                    });
                }
            } catch (error) {
                // Không có quiz
            }
        }

        async function markComplete() {
            if (!currentLessonId) return;
            try {
                await window.api.post(`/lessons/${currentLessonId}/complete`, {});
                const btn = document.getElementById('btn_mark_complete');
                btn.style.display = 'block';
                btn.innerHTML = window.I18n ? window.I18n.get('lrn_btn_completed') : '✅ Đã hoàn thành bài học';
                btn.classList.replace('btn-primary', 'btn-outline');
                btn.style.color = 'var(--success)';
                btn.style.borderColor = 'var(--success)';
                btn.disabled = true;

                if (res && res.data && res.data.progress === 100) {
                    document.getElementById('btn_review_course').style.display = 'block';
                    showReviewModal();
                }
            } catch (e) {
                console.log(e);
            }
        }

        function toggleAIChat() {
            document.getElementById('aiPopup').classList.toggle('open');
        }

        // AI Chat — gửi context bài học + khóa học
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatAiResponse(text) {
            // Bold: **text**
            text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            // Bullet points
            text = text.replace(/^[-•]\s+(.+)$/gm, '<span style="display:block;padding-left:1rem;">• $1</span>');
            // Numbered list
            text = text.replace(/^(\d+)\.\s+(.+)$/gm, '<span style="display:block;padding-left:1rem;">$1. $2</span>');
            // Emoji headers
            text = text.replace(/\n/g, '<br>');
            return text;
        }

        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const chatBox = document.getElementById('chatBox');

            const message = input.value.trim();
            if (!message) return;

            chatBox.innerHTML += `<div class="msg user">${escapeHtml(message)}</div>`;
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            const thinkingId = 'think_' + Date.now();
            const thinkingMsg = window.I18n ? window.I18n.get('lrn_ai_thinking') : 'AI đang phân tích bài học...';
            chatBox.innerHTML += `
                <div class="msg bot" id="${thinkingId}">
                    <div class="flex items-center gap-2">
                        <span style="font-size: 1.2rem; animation: spin 2s linear infinite;">🧠</span> ${thinkingMsg}
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // Gửi kèm context bài học đang xem
                const res = await window.api.post('/ai/chat', {
                    message,
                    lesson_id: currentLessonId,
                    course_id: courseId
                });

                let html = formatAiResponse(res.data.ai_response);

                // Hiển thị gợi ý bài tiếp theo
                if (res.data.suggestions && res.data.suggestions.length > 0) {
                    html += `<div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid rgba(255,255,255,0.1);">`;
                    html += `<small style="color:var(--primary);">📚 Gợi ý bài tiếp:</small>`;
                    res.data.suggestions.forEach(s => {
                        const icon = s.content_type === 'video' ? '🎬' : '📝';
                        html += `<div style="margin-top:4px;cursor:pointer;color:rgba(255,255,255,0.7);font-size:0.85rem;" 
                                      onclick="loadLesson(${s.id})">${icon} ${escapeHtml(s.title)}</div>`;
                    });
                    html += `</div>`;
                }

                // Nếu bị moderated
                if (res.data.moderated) {
                    document.getElementById(thinkingId).style.borderColor = 'rgba(251,146,60,0.3)';
                }

                document.getElementById(thinkingId).innerHTML = html;
            } catch (err) {
                const errMsg = window.I18n ? window.I18n.get('lrn_ai_error') : 'Lỗi kết nối tới AI: ';
                document.getElementById(thinkingId).innerHTML = '❌ ' + errMsg + (err.message || '');
                document.getElementById(thinkingId).style.color = 'var(--danger)';
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        function showReviewModal() {
            document.getElementById('reviewModal').style.display = 'flex';
        }

        async function submitReview(e) {
            e.preventDefault();
            const rating = document.getElementById('reviewRating').value;
            const comment = document.getElementById('reviewComment').value;
            const btn = document.getElementById('btnSubmitReview');
            btn.disabled = true;
            btn.innerText = 'Đang gửi...';

            try {
                await window.api.post(`/student/courses/${courseId}/reviews`, { rating, comment });
                App.showToast('Cảm ơn bạn đã đánh giá khóa học!', 'success');
                document.getElementById('reviewModal').style.display = 'none';
                document.getElementById('btn_review_course').style.display = 'none';
            } catch (err) {
                App.showToast(err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Gửi Đánh Giá';
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
