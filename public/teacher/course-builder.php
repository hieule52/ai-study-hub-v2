<?php
$pageTitle = 'Course Builder - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$footerMode = 'none';
$extraHead = '
    <link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">
    <link rel="stylesheet" href="/assets/css/teacher/builder.css?v=' . time() . '">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <!-- Sidebar (Teacher Navigation) -->
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <div class="builder-layout">
        <!-- ═══════════════════════════════════════
             BUILDER SIDEBAR (Curriculum)
             ═══════════════════════════════════════ -->
        <aside class="builder-sidebar">
            <div class="builder-sidebar-header">
                <h3 id="course-title-display">Đang tải...</h3>

                <!-- Stats Strip -->
                <div class="sidebar-stats" id="sidebar-stats">
                    <span class="sidebar-stat-badge">
                        <span class="stat-icon">📚</span>
                        <span class="stat-count" id="stat-chapters">0</span> Chương
                    </span>
                    <span class="sidebar-stat-badge">
                        <span class="stat-icon">📝</span>
                        <span class="stat-count" id="stat-lessons">0</span> Bài học
                    </span>
                    <span class="sidebar-stat-badge">
                        <span class="stat-icon">❓</span>
                        <span class="stat-count" id="stat-quizzes">0</span> Quiz
                    </span>
                </div>

                <button class="btn btn-primary w-full" onclick="createNewChapter()" style="border-radius: 100px;">
                    + Thêm chương mới
                </button>
            </div>
            <div class="sidebar-scroll" id="curriculum-container">
                <!-- Curriculum items via JS -->
            </div>
        </aside>

        <!-- ═══════════════════════════════════════
             MAIN EDITOR (Tabbed)
             ═══════════════════════════════════════ -->
        <main class="builder-content">
            <div id="editor-panel" style="display:none;">
                <div class="editor-card">
                    <!-- Card Header -->
                    <div class="editor-card-header">
                        <span class="header-icon" id="editor-icon">📝</span>
                        <span class="header-text" id="editor-title-text">Thêm bài học</span>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="lesson-tabs" id="lesson-tabs">
                        <button class="lesson-tab active" data-tab="tab-info" onclick="switchTab('tab-info', this)">
                            <span class="tab-icon">📋</span> Thông tin
                        </button>
                        <button class="lesson-tab" data-tab="tab-content" onclick="switchTab('tab-content', this)">
                            <span class="tab-icon">📄</span> Nội dung
                        </button>
                        <button class="lesson-tab" data-tab="tab-ai" onclick="switchTab('tab-ai', this)" id="tab-btn-ai">
                            <span class="tab-icon">🤖</span> AI Tutor
                        </button>
                        <button class="lesson-tab" data-tab="tab-quiz" onclick="switchTab('tab-quiz', this)" id="tab-btn-quiz">
                            <span class="tab-icon">❓</span> Quiz
                        </button>
                        <button class="lesson-tab" data-tab="tab-preview" onclick="switchTab('tab-preview', this)">
                            <span class="tab-icon">👁️</span> Xem trước
                        </button>
                    </div>

                    <form id="lesson-form">
                        <input type="hidden" id="edit_mode" value="create">
                        <input type="hidden" id="edit_lesson_id">
                        <input type="hidden" id="chapter_id">
                        <input type="hidden" id="video_filename">

                        <div class="lesson-tab-panels">
                            <!-- ══════════════════════════════════
                                 TAB 1 — Thông tin bài học
                                 ══════════════════════════════════ -->
                            <div class="lesson-tab-panel active" id="tab-info">
                                <div class="form-group">
                                    <label class="form-label">Tên bài học <span style="color:var(--danger)">*</span></label>
                                    <input type="text" id="lesson_title" class="form-control" required placeholder="VD: Giới thiệu về Neural Networks">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Loại nội dung</label>
                                        <select id="content_type" class="form-control" onchange="toggleContentFields()">
                                            <option value="video">🎬 Video Bài Giảng</option>
                                            <option value="text">📄 Tài Liệu Đọc</option>
                                            <option value="quiz">❓ Bài Kiểm Tra</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Thứ tự hiển thị</label>
                                        <input type="number" id="order_index" class="form-control" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- ══════════════════════════════════
                                 TAB 2 — Nội dung
                                 ══════════════════════════════════ -->
                            <div class="lesson-tab-panel" id="tab-content">
                                <!-- Video Section -->
                                <div id="video-upload-section">
                                    <label class="form-label">Video bài giảng</label>
                                    <div id="video-info-display"></div>
                                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('videoFileInput').click()">
                                        <div class="upload-icon"><i class="fas fa-video"></i></div>
                                        <div style="font-weight:700; color:var(--text-primary); margin-bottom:0.5rem;">Click để tải lên video</div>
                                        <div style="font-size: 0.8rem; opacity: 0.5;">MP4 (H.264), WebM, MOV (H.264) — Tối đa 300MB (Khuyến nghị < 200MB)</div>
                                    </div>

                                    <div style="margin: 1.5rem 0; display: flex; align-items: center; gap: 1rem;">
                                        <div style="height: 1px; flex: 1; background: rgba(255,255,255,0.1);"></div>
                                        <span style="font-size: 0.75rem; opacity: 0.4; text-transform: uppercase; letter-spacing: 0.1em;">Hoặc dùng link ngoài</span>
                                        <div style="height: 1px; flex: 1; background: rgba(255,255,255,0.1);"></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Link YouTube / Vimeo / Drive</label>
                                        <div style="position:relative;">
                                            <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); opacity:0.4;"><i class="fab fa-youtube"></i></span>
                                            <input type="text" id="video_url" class="form-control" style="padding-left:2.75rem;" placeholder="https://www.youtube.com/watch?v=...">
                                        </div>
                                        <p style="font-size:0.75rem; opacity:0.4; margin-top:0.5rem;">💡 Nếu nhập link, video tải lên sẽ bị bỏ qua.</p>
                                    </div>
                                    <div id="uploadProgress" style="display:none; margin-bottom: 1.5rem;">
                                        <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:0.5rem;">
                                            <span>Đang tải lên...</span>
                                            <span id="uploadPct">0%</span>
                                        </div>
                                        <div style="height:4px; background:var(--glass-border); border-radius:10px; overflow:hidden;">
                                            <div id="uploadProgressBar" style="height:100%; width:0; background:var(--primary);"></div>
                                        </div>
                                    </div>
                                    <input type="file" id="videoFileInput" accept="video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleVideoSelect(this)">
                                </div>

                                <!-- Text Editor -->
                                <div class="form-group" id="content-group">
                                    <label class="form-label">Nội dung chi tiết bài viết</label>
                                    <div id="lesson_quill_editor"></div>
                                </div>
                            </div>

                            <!-- ══════════════════════════════════
                                 TAB 3 — AI Tutor
                                 ══════════════════════════════════ -->
                            <div class="lesson-tab-panel" id="tab-ai">

                                <!-- Section 1: Cấu hình AI Tutor -->
                                <div class="ai-section-card">
                                    <div class="ai-section-title">
                                        <span class="section-icon">⚙️</span> Cấu hình AI Tutor
                                    </div>
                                    <div class="ai-toggle-list">
                                        <label class="ai-toggle-item">
                                            <input type="checkbox" id="enable_auto_summary" checked>
                                            Tự động tóm tắt bài học
                                        </label>
                                        <label class="ai-toggle-item">
                                            <input type="checkbox" id="enable_auto_keywords" checked>
                                            Tự động gợi ý từ khóa
                                        </label>
                                        <label class="ai-toggle-item">
                                            <input type="checkbox" id="enable_auto_context" checked>
                                            Tự động thiết lập ngữ cảnh
                                        </label>
                                    </div>
                                </div>

                                <!-- Section 2: Chế độ AI -->
                                <div class="ai-section-card strict-mode">
                                    <div class="ai-section-title">
                                        <span class="section-icon">🔒</span> Chế độ AI
                                    </div>
                                    <label class="strict-mode-label">
                                        <input type="checkbox" id="strict_ai_mode">
                                        <div>
                                            <div style="font-weight:700; color:#fff; font-size:0.9rem;">Chế độ AI Nghiêm ngặt (Strict Mode)</div>
                                            <div class="strict-mode-desc">
                                                Khi bật, AI Tutor sẽ CHỈ trả lời dựa trên nội dung bài học do giảng viên cung cấp. Mọi câu hỏi ngoài phạm vi sẽ bị từ chối. Khuyến khích dùng cho bài học có yêu cầu chính xác cao.
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Section 3: Hướng dẫn AI -->
                                <div class="ai-section-card">
                                    <div class="ai-section-title">
                                        <span class="section-icon">📝</span> Hướng dẫn AI
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <textarea id="teacher_notes" class="form-control" rows="3" placeholder="Viết ghi chú hướng dẫn cho AI Tutor. Nội dung này sẽ được ưu tiên cao nhất khi AI trả lời học viên.&#10;VD: Nhấn mạnh vào phần tối ưu hóa thuật toán, không giải thích sâu về cấu trúc dữ liệu."></textarea>
                                        <p style="font-size:0.72rem; opacity:0.4; margin-top:0.5rem;">💡 AI Tutor sẽ ưu tiên nội dung ghi chú này cao nhất khi trả lời câu hỏi học viên.</p>
                                    </div>
                                </div>

                                <!-- Section 4: Nguồn dữ liệu AI -->
                                <div class="ai-section-card data-sources">
                                    <div class="ai-section-title">
                                        <span class="section-icon">📊</span> Nguồn dữ liệu AI
                                    </div>

                                    <!-- Transcript Status Badge -->
                                    <div id="transcript-status-section" style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem;">
                                        <div style="font-size:0.82rem; font-weight:600; color:rgba(255,255,255,0.6);">Trạng thái bản dịch:</div>
                                        <div id="transcript-status-badge" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.85rem; border-radius:100px; font-size:0.75rem; font-weight:700; background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.4); border:1px solid rgba(255,255,255,0.1);">
                                            📝 Chưa có
                                        </div>
                                        <button type="button" id="verifyTranscriptBtn" onclick="verifyTranscript()" style="display:none; padding:0.4rem 1rem; border-radius:100px; border:1px solid rgba(16,185,129,0.3); background:rgba(16,185,129,0.08); color:#10b981; font-size:0.75rem; font-weight:700; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.15)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                                            ✅ Xác minh bản dịch
                                        </button>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Tóm tắt nội dung</label>
                                        <textarea id="ai_summary" class="form-control" rows="2" placeholder="Tóm tắt ngắn gọn nội dung bài học..."></textarea>
                                    </div>

                                    <div class="form-group" id="video-transcript-group">
                                        <label class="form-label">Nội dung lời nói video</label>
                                        <textarea id="video_transcript" class="form-control" rows="4" placeholder="Nhập nội dung hội thoại trong video nếu có..."></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">Ngữ cảnh hỗ trợ AI</label>
                                            <input type="text" id="lesson_context" class="form-control" placeholder="VD: Nhấn mạnh vào phần tính toán...">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Từ khóa chính</label>
                                            <input type="text" id="key_topics" class="form-control" placeholder="VD: logic, loops, syntax">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ══════════════════════════════════
                                 TAB 4 — Quiz
                                 ══════════════════════════════════ -->
                            <div class="lesson-tab-panel" id="tab-quiz">
                                <div class="form-group">
                                    <label class="form-label">Tiêu đề bài kiểm tra</label>
                                    <input type="text" id="quiz_title" class="form-control" placeholder="VD: Câu hỏi ôn tập chương 1">
                                </div>

                                <!-- AI Quiz Helper Fields -->
                                <div class="ai-section-card" style="margin-bottom:1.5rem;">
                                    <div class="ai-section-title">
                                        <span class="section-icon">🧠</span> AI Quiz Assistance
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Giải thích tổng quát (Explanations)</label>
                                        <textarea id="quiz_explanations" class="form-control" rows="2" placeholder="AI sẽ dùng nội dung này để giải thích kết quả cho học viên..."></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">Gợi ý chung (Hints)</label>
                                            <input type="text" id="quiz_hints" class="form-control" placeholder="Gợi ý khi học viên bí bài...">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">AI Tags</label>
                                            <input type="text" id="quiz_ai_tags" class="form-control" placeholder="VD: critical thinking, logic">
                                        </div>
                                    </div>
                                </div>

                                <div id="quiz-questions-container"></div>
                                <button type="button" class="btn btn-outline w-full" onclick="addQuestion()" style="margin-top: 1rem; border-style: dashed;">
                                    + Thêm câu hỏi
                                </button>
                            </div>

                            <!-- ══════════════════════════════════
                                 TAB 5 — Xem trước
                                 ══════════════════════════════════ -->
                            <div class="lesson-tab-panel" id="tab-preview">
                                <div class="preview-panel" id="preview-panel-content">
                                    <div class="preview-empty">
                                        <div class="preview-empty-icon">👁️</div>
                                        <p>Nhập nội dung bài học ở các tab trước để xem trước tại đây.</p>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /lesson-tab-panels -->
                    </form>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="chat-empty">
                <div style="font-size: 5rem; margin-bottom: 2rem; opacity: 0.1;">🛠️</div>
                <h2>Xây dựng giáo trình</h2>
                <p style="opacity: 0.5;">Thêm chương và bài học từ cột bên trái để hoàn thiện khóa học của bạn.</p>
            </div>
        </main>
    </div>
</div>

<!-- ═══════════════════════════════════════
     STICKY ACTION BAR
     ═══════════════════════════════════════ -->
<div class="sticky-action-bar" id="sticky-action-bar">
    <div class="bar-left">
        <button type="button" class="btn-bar bar-danger" id="bar-deleteLessonBtn" style="display:none;" onclick="deleteCurrentLesson()">
            <i class="fas fa-trash-alt"></i> Xóa bài học
        </button>
    </div>
    <button type="button" class="btn-bar bar-ghost" onclick="saveDraft()">
        <i class="fas fa-save"></i> Lưu nháp
    </button>
    <button type="button" class="btn-bar bar-outline" onclick="switchTab('tab-preview', document.querySelector('[data-tab=tab-preview]'))">
        <i class="fas fa-eye"></i> Xem trước
    </button>
    <button type="button" class="btn-bar bar-primary" id="bar-saveBtn" onclick="triggerSave()">
        <i class="fas fa-check"></i> Lưu bài học
    </button>
</div>

<!-- ── Chapter Modal ── -->
<div id="chapterModal" onclick="closeChapterModal(event)" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.6); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); display:none; align-items:center; justify-content:center;">
    <div class="chapter-modal-card" onclick="event.stopPropagation()">
        <div class="chapter-modal-header">
            <div class="chapter-modal-icon" id="chapterModalIcon">📖</div>
            <div>
                <h3 id="chapterModalTitle" style="font-size:1.4rem; font-weight:800; color:#fff; margin:0; letter-spacing:-0.02em;">Thêm chương mới</h3>
                <p style="font-size:0.85rem; opacity:0.4; margin:4px 0 0;">Tạo một phần mới cho giáo trình của bạn</p>
            </div>
            <button onclick="closeChapterModal()" style="margin-left:auto; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.6); width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">&times;</button>
        </div>

        <div style="padding: 2rem;">
            <label style="display:block; font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:rgba(255,255,255,0.4); margin-bottom:0.75rem;">Tên chương <span style="color:#ef4444;">*</span></label>
            <div style="position:relative;">
                <span style="position:absolute; left:1.25rem; top:50%; transform:translateY(-50%); opacity:0.3; font-size:1.1rem;">✏️</span>
                <input type="text" id="chapterModalInput"
                    placeholder="VD: Chương 1 — Giới thiệu về AI"
                    style="width:100%; padding:1rem 1.25rem 1rem 3.25rem; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:14px; color:#fff; font-size:1rem; font-family:inherit; outline:none; transition:all 0.3s; box-sizing:border-box;"
                    onfocus="this.style.borderColor='rgba(99,102,241,0.6)'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.boxShadow='none'"
                    onkeydown="if(event.key==='Enter') submitChapterModal()"
                >
            </div>
            <p style="font-size:0.78rem; opacity:0.3; margin-top:0.75rem;">💡 Đặt tên rõ ràng để học viên dễ dàng theo dõi lộ trình học.</p>
        </div>

        <div style="padding: 0 2rem 2rem; display:flex; gap:1rem;">
            <button onclick="submitChapterModal()" style="flex:1; padding:1rem; background:linear-gradient(135deg, var(--primary), #818cf8); border:none; border-radius:12px; color:#fff; font-size:1rem; font-weight:700; cursor:pointer; transition:all 0.3s; letter-spacing:-0.01em;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 30px rgba(99,102,241,0.35)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <span id="chapterModalBtn">+ Tạo chương</span>
            </button>
            <button onclick="closeChapterModal()" style="padding:1rem 1.5rem; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:rgba(255,255,255,0.6); font-size:1rem; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.04)'">Hủy</button>
        </div>
    </div>
</div>

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
// Support clean URL (/teacher/course-builder/8) and legacy (?course_id=8)
let courseId = <?= json_encode($_GET['course_id'] ?? null) ?> ?? new URLSearchParams(window.location.search).get('course_id');
let currentCurriculum = [];
let quill;

// ════════════════════════════════════════════════════
// TAB SWITCHING
// ════════════════════════════════════════════════════
function switchTab(tabId, btn) {
    // Deactivate all
    document.querySelectorAll('.lesson-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.lesson-tab-panel').forEach(p => p.classList.remove('active'));
    // Activate target
    if (btn) btn.classList.add('active');
    const panel = document.getElementById(tabId);
    if (panel) panel.classList.add('active');

    // If switching to preview, build preview content
    if (tabId === 'tab-preview') buildPreview();
}

function buildPreview() {
    const container = document.getElementById('preview-panel-content');
    const title = document.getElementById('lesson_title').value;
    const type = document.getElementById('content_type').value;
    let html = '';

    if (!title && !quill.getText().trim()) {
        container.innerHTML = '<div class="preview-empty"><div class="preview-empty-icon">👁️</div><p>Nhập nội dung bài học ở các tab trước để xem trước tại đây.</p></div>';
        return;
    }

    html += `<div class="preview-section">
        <div class="preview-section-title">Tiêu đề bài học</div>
        <div class="preview-content-box" style="font-size:1.1rem; font-weight:700;">${escapeHtml(title || '(Chưa đặt tên)')}</div>
    </div>`;

    html += `<div class="preview-section">
        <div class="preview-section-title">Loại nội dung</div>
        <div class="preview-content-box">${type === 'video' ? '🎬 Video Bài Giảng' : (type === 'quiz' ? '❓ Bài Kiểm Tra' : '📄 Tài Liệu Đọc')}</div>
    </div>`;

    if (type !== 'quiz') {
        const content = quill.root.innerHTML;
        if (content && content !== '<p><br></p>') {
            html += `<div class="preview-section">
                <div class="preview-section-title">Nội dung</div>
                <div class="preview-content-box">${content}</div>
            </div>`;
        }
    }

    const videoUrl = document.getElementById('video_url').value.trim();
    if (videoUrl) {
        let embedUrl = videoUrl;
        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
            const pattern = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
            const matches = videoUrl.match(pattern);
            if (matches && matches[1]) embedUrl = `https://www.youtube.com/embed/${matches[1]}`;
        }
        html += `<div class="preview-section">
            <div class="preview-section-title">Video</div>
            <div style="border-radius:12px; overflow:hidden; position:relative; padding-top:56.25%; background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.08);">
                <iframe src="${embedUrl}" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allowfullscreen></iframe>
            </div>
        </div>`;
    }

    const summary = document.getElementById('ai_summary').value;
    if (summary) {
        html += `<div class="preview-section">
            <div class="preview-section-title">Tóm tắt nội dung</div>
            <div class="preview-content-box">${escapeHtml(summary)}</div>
        </div>`;
    }

    container.innerHTML = html;
}


// ════════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {
    App.requireAuth(['teacher', 'admin']);

    quill = new Quill('#lesson_quill_editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image']
            ]
        }
    });

    if (!courseId) {
        window.location.href = '/teacher/dashboard';
        return;
    }

    await loadCourseInfo();
    await loadCurriculum();
    if (window.I18n) window.I18n.render();

    // Lắng nghe sự kiện để cập nhật preview video YouTube/ngoài ngay lập tức
    document.getElementById('video_url').addEventListener('input', updateVideoPreview);
});

async function loadCourseInfo() {
    try {
        const res = await window.api.get(`/courses/${courseId}`);
        document.getElementById('course-title-display').innerText = res.data.title;
    } catch(e) {}
}

async function loadCurriculum() {
    try {
        const res = await window.api.get(`/courses/${courseId}/curriculum`);
        currentCurriculum = res.data;
        renderCurriculum();
    } catch(e) {
        console.error("Lỗi khi tải giáo trình:", e);
        App.showToast("Không thể tải giáo trình: " + (e.message || e), "error");
    }
}

function renderCurriculum() {
    const container = document.getElementById('curriculum-container');
    if (currentCurriculum.length === 0) {
        container.innerHTML = '<p class="text-center opacity-30 p-10">Chưa có nội dung.</p>';
        updateSidebarStats();
        return;
    }

    container.innerHTML = currentCurriculum.map((ch, idx) => `
        <div class="chapter-card">
            <div class="chapter-header">
                <span class="ch-title">${idx+1}. ${escapeHtml(ch.title)}</span>
                <div class="chapter-actions">
                    <button onclick="openEditor(${ch.id})" title="Thêm bài"><i class="fas fa-plus"></i></button>
                    <button onclick="editChapter(${ch.id}, '${escapeHtml(ch.title)}')" title="Sửa"><i class="fas fa-edit"></i></button>
                </div>
            </div>
            ${(ch.lessons || []).map(l => `
                <div class="lesson-item" id="lesson-nav-${l.id}" onclick="editExistingLesson(${l.id}, ${ch.id})">
                    <span class="type-icon">${l.content_type === 'video' ? '🎬' : (l.content_type === 'quiz' ? '❓' : '📄')}</span>
                    <span class="lesson-name">${escapeHtml(l.title)}</span>
                    <div class="lesson-actions">
                        <button onclick="event.stopPropagation(); deleteLesson(${l.id})" title="Xóa bài học" style="color: var(--danger);"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            `).join('')}
            <div class="add-lesson-btn" onclick="openEditor(${ch.id})">+ Thêm bài học</div>
        </div>
    `).join('');

    updateSidebarStats();
}

// ════════════════════════════════════════════════════
// SIDEBAR STATS
// ════════════════════════════════════════════════════
function updateSidebarStats() {
    let chapters = currentCurriculum.length;
    let lessons = 0;
    let quizzes = 0;
    currentCurriculum.forEach(ch => {
        (ch.lessons || []).forEach(l => {
            lessons++;
            if (l.content_type === 'quiz') quizzes++;
        });
    });
    document.getElementById('stat-chapters').textContent = chapters;
    document.getElementById('stat-lessons').textContent = lessons;
    document.getElementById('stat-quizzes').textContent = quizzes;
}

// ════════════════════════════════════════════════════
// CHAPTER MODAL
// ════════════════════════════════════════════════════
let _chapterModalMode = 'create';
let _chapterModalEditId = null;

function openChapterModal(mode = 'create', id = null, oldTitle = '') {
    _chapterModalMode = mode;
    _chapterModalEditId = id;
    const modal = document.getElementById('chapterModal');
    const input = document.getElementById('chapterModalInput');
    const titleEl = document.getElementById('chapterModalTitle');
    const iconEl = document.getElementById('chapterModalIcon');
    const btnEl = document.getElementById('chapterModalBtn');

    if (mode === 'edit') {
        titleEl.innerText = 'Chỉnh sửa chương';
        iconEl.innerText = '✏️';
        btnEl.innerText = '💾 Lưu thay đổi';
        input.value = oldTitle;
    } else {
        titleEl.innerText = 'Thêm chương mới';
        iconEl.innerText = '📖';
        btnEl.innerText = '+ Tạo chương';
        input.value = '';
    }

    modal.style.display = 'flex';
    setTimeout(() => input.focus(), 100);
}

function closeChapterModal(event) {
    if (event && event.target !== document.getElementById('chapterModal')) return;
    document.getElementById('chapterModal').style.display = 'none';
}

async function submitChapterModal() {
    const title = document.getElementById('chapterModalInput').value.trim();
    if (!title) {
        document.getElementById('chapterModalInput').style.borderColor = '#ef4444';
        document.getElementById('chapterModalInput').focus();
        return;
    }
    try {
        if (_chapterModalMode === 'edit') {
            await window.api.put(`/teacher/chapters/${_chapterModalEditId}`, { title });
            App.showToast('Đã cập nhật tên chương!', 'success');
        } else {
            await window.api.post('/teacher/chapters', { course_id: courseId, title, order_index: currentCurriculum.length });
            App.showToast('Đã thêm chương mới!', 'success');
        }
        document.getElementById('chapterModal').style.display = 'none';
        await loadCurriculum();
    } catch(e) { App.showToast(e.message, 'error'); }
}

function createNewChapter() {
    openChapterModal('create');
}

function editChapter(id, oldTitle) {
    openChapterModal('edit', id, oldTitle);
}

// ════════════════════════════════════════════════════
// EDITOR OPEN / CLOSE / TAB VISIBILITY
// ════════════════════════════════════════════════════
function openEditor(chapterId) {
    document.getElementById('empty-state').style.display = 'none';
    document.getElementById('editor-panel').style.display = 'block';
    document.getElementById('chapter_id').value = chapterId;
    document.getElementById('edit_mode').value = 'create';
    document.getElementById('lesson-form').reset();
    quill.root.innerHTML = '';
    document.getElementById('video-info-display').innerHTML = '';

    // Hide delete button in sticky bar
    const barDelete = document.getElementById('bar-deleteLessonBtn');
    if (barDelete) barDelete.style.display = 'none';

    // Reset AI config fields
    document.getElementById('enable_auto_summary').checked = true;
    document.getElementById('enable_auto_keywords').checked = true;
    document.getElementById('enable_auto_context').checked = true;
    document.getElementById('strict_ai_mode').checked = false;
    document.getElementById('teacher_notes').value = '';
    updateTranscriptBadge('empty');
    toggleContentFields();

    // Reset to first tab
    switchTab('tab-info', document.querySelector('[data-tab="tab-info"]'));

    // Show sticky bar
    document.getElementById('sticky-action-bar').style.display = 'flex';
}

async function editExistingLesson(lessonId, chapterId) {
    try {
        const res = await window.api.get(`/lessons/${lessonId}`);
        const l = res.data;
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('editor-panel').style.display = 'block';
        document.getElementById('edit_mode').value = 'edit';
        document.getElementById('edit_lesson_id').value = lessonId;

        // Show delete button in sticky bar
        const barDelete = document.getElementById('bar-deleteLessonBtn');
        if (barDelete) barDelete.style.display = 'inline-flex';

        document.getElementById('chapter_id').value = chapterId;
        document.getElementById('lesson_title').value = l.title;
        document.getElementById('content_type').value = l.content_type;
        document.getElementById('order_index').value = l.order_index;
        document.getElementById('video_url').value = l.video_url || '';
        quill.root.innerHTML = l.content || '';

        if (l.video_filename) {
            document.getElementById('video_filename').value = l.video_filename;
        }
        await updateVideoPreview();

        toggleContentFields();
        document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
        document.getElementById(`lesson-nav-${lessonId}`)?.classList.add('active');

        if (l.content_type === 'quiz') loadQuiz(lessonId);

        // AI Fields
        document.getElementById('ai_summary').value = l.ai_summary || '';
        document.getElementById('video_transcript').value = l.video_transcript || '';
        document.getElementById('lesson_context').value = l.lesson_context || '';
        document.getElementById('key_topics').value = l.key_topics || '';

        // AI Config Fields
        document.getElementById('enable_auto_summary').checked = l.enable_auto_summary != 0;
        document.getElementById('enable_auto_keywords').checked = l.enable_auto_keywords != 0;
        document.getElementById('enable_auto_context').checked = l.enable_auto_context != 0;
        document.getElementById('strict_ai_mode').checked = l.strict_ai_mode == 1;
        document.getElementById('teacher_notes').value = l.teacher_notes || '';
        updateTranscriptBadge(l.transcript_status || 'empty');

        // Reset to first tab
        switchTab('tab-info', document.querySelector('[data-tab="tab-info"]'));

        // Show sticky bar
        document.getElementById('sticky-action-bar').style.display = 'flex';

    } catch(e) { App.showToast(e.message, 'error'); }
}

function hideEditor() {
    document.getElementById('editor-panel').style.display = 'none';
    document.getElementById('empty-state').style.display = 'flex';
    document.getElementById('sticky-action-bar').style.display = 'none';
}

// ════════════════════════════════════════════════════
// CONTENT TYPE TOGGLING (Tab Visibility)
// ════════════════════════════════════════════════════
function toggleContentFields() {
    const type = document.getElementById('content_type').value;

    // Video upload section
    document.getElementById('video-upload-section').style.display = (type === 'video') ? 'block' : 'none';
    // Rich text editor
    document.getElementById('content-group').style.display = (type === 'quiz') ? 'none' : 'block';

    // Tab visibility: show/hide quiz tab, show/hide AI tab
    const tabBtnQuiz = document.getElementById('tab-btn-quiz');
    const tabBtnAi = document.getElementById('tab-btn-ai');
    if (tabBtnQuiz) tabBtnQuiz.style.display = (type === 'quiz') ? 'inline-flex' : 'none';
    if (tabBtnAi) tabBtnAi.style.display = (type === 'quiz') ? 'none' : 'inline-flex';

    // Sticky bar save button: for quiz, use "Lưu Quiz" label
    const barSave = document.getElementById('bar-saveBtn');
    if (type === 'quiz') {
        barSave.innerHTML = '<i class="fas fa-check"></i> Lưu Quiz';
        barSave.setAttribute('onclick', 'saveQuiz()');
    } else {
        barSave.innerHTML = '<i class="fas fa-check"></i> Lưu bài học';
        barSave.setAttribute('onclick', 'triggerSave()');
    }

    // Hide/show video transcript field
    const transGroup = document.getElementById('video-transcript-group');
    if (transGroup) {
        transGroup.style.display = (type === 'video') ? 'block' : 'none';
    }

    // Auto-add first question if quiz and empty
    if (type === 'quiz' && document.getElementById('quiz-questions-container').children.length === 0) {
        addQuestion();
    }

    // If current tab is hidden, switch to info tab
    const activeTab = document.querySelector('.lesson-tab-panel.active');
    if (activeTab) {
        if (activeTab.id === 'tab-quiz' && type !== 'quiz') {
            switchTab('tab-info', document.querySelector('[data-tab="tab-info"]'));
        }
        if (activeTab.id === 'tab-ai' && type === 'quiz') {
            switchTab('tab-info', document.querySelector('[data-tab="tab-info"]'));
        }
    }
}

// ════════════════════════════════════════════════════
// STICKY BAR ACTIONS
// ════════════════════════════════════════════════════
function triggerSave() {
    document.getElementById('lesson-form').requestSubmit();
}

function saveDraft() {
    // Draft saves via same API (no separate draft endpoint)
    const type = document.getElementById('content_type').value;
    if (type === 'quiz') {
        saveQuiz();
    } else {
        document.getElementById('lesson-form').requestSubmit();
    }
    // Toast will be overridden by the submit handler's success toast
}

// ════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════
async function deleteLesson(lessonId) {
    const confirmed = await App.confirm({
        title: 'Xóa bài học',
        message: 'Bạn có chắc chắn muốn xóa bài học này không? Hành động này không thể hoàn tác.',
        type: 'danger',
        confirmText: 'Xóa ngay',
        cancelText: 'Hủy bỏ'
    });
    if (!confirmed) {
        return;
    }
    try {
        await window.api.delete(`/teacher/lessons/${lessonId}`);
        App.showToast('Đã xóa bài học thành công!', 'success');

        const currentEditId = document.getElementById('edit_lesson_id').value;
        const currentMode = document.getElementById('edit_mode').value;
        if (currentMode === 'edit' && currentEditId == lessonId) {
            hideEditor();
        }

        await loadCurriculum();
    } catch(e) {
        App.showToast(e.message || 'Xóa bài học thất bại.', 'error');
    }
}

function deleteCurrentLesson() {
    const lessonId = document.getElementById('edit_lesson_id').value;
    if (lessonId) {
        deleteLesson(lessonId);
    }
}

// ════════════════════════════════════════════════════
// VIDEO UPLOAD
// ════════════════════════════════════════════════════
let currentLocalVideoUrl = null;

async function handleVideoSelect(input) {
    if (!input.files[0]) return;
    const file = input.files[0];

    // Client-side size validation (300MB max)
    const MAX_VIDEO_SIZE_MB = 300;
    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
    if (file.size > MAX_VIDEO_SIZE_MB * 1024 * 1024) {
        input.value = '';
        const infoDisplay = document.getElementById('video-info-display');
        infoDisplay.innerHTML = `
            <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25); border-radius:14px; padding:1.5rem; margin-bottom:1.5rem;">
                <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1rem;">
                    <span style="font-size:1.5rem;">⚠️</span>
                    <div>
                        <div style="font-weight:700; color:#ef4444; font-size:0.95rem;">Video quá lớn (${fileSizeMB}MB)</div>
                        <div style="font-size:0.8rem; color:rgba(255,255,255,0.5);">Giới hạn tối đa: ${MAX_VIDEO_SIZE_MB}MB</div>
                    </div>
                </div>
                <div style="font-size:0.85rem; color:rgba(255,255,255,0.7); line-height:1.6; margin-bottom:1rem;">
                    Video vượt quá dung lượng cho phép (300MB). Vui lòng sử dụng Youtube, Vimeo hoặc Google Drive đối với video dung lượng lớn.
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('video_url').focus(); document.getElementById('video_url').scrollIntoView({ behavior: 'smooth' });" style="background:var(--primary); border:none; padding:0.5rem 1rem; border-radius:8px; font-weight:700; font-size:0.85rem; color:#fff; cursor:pointer; display:inline-flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
                    <i class="fas fa-link"></i> Sử dụng Link Youtube / Vimeo / Drive
                </button>
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <a href="https://studio.youtube.com" target="_blank" rel="noopener" style="display:flex; align-items:center; gap:0.75rem; padding:0.85rem 1.15rem; background:rgba(255,0,0,0.08); border:1px solid rgba(255,0,0,0.2); border-radius:10px; text-decoration:none; color:#fff; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,0,0,0.15)'" onmouseout="this.style.background='rgba(255,0,0,0.08)'">
                        <i class="fab fa-youtube" style="font-size:1.4rem; color:#ff0000;"></i>
                        <div>
                            <div style="font-weight:700; font-size:0.88rem;">YouTube Studio</div>
                            <div style="font-size:0.72rem; opacity:0.5;">Tải lên miễn phí, không giới hạn dung lượng</div>
                        </div>
                        <i class="fas fa-external-link-alt" style="margin-left:auto; font-size:0.7rem; opacity:0.3;"></i>
                    </a>
                    <a href="https://drive.google.com" target="_blank" rel="noopener" style="display:flex; align-items:center; gap:0.75rem; padding:0.85rem 1.15rem; background:rgba(66,133,244,0.08); border:1px solid rgba(66,133,244,0.2); border-radius:10px; text-decoration:none; color:#fff; transition:all 0.2s;" onmouseover="this.style.background='rgba(66,133,244,0.15)'" onmouseout="this.style.background='rgba(66,133,244,0.08)'">
                        <i class="fab fa-google-drive" style="font-size:1.3rem; color:#4285f4;"></i>
                        <div>
                            <div style="font-weight:700; font-size:0.88rem;">Google Drive</div>
                            <div style="font-size:0.72rem; opacity:0.5;">15GB miễn phí, chia sẻ link dễ dàng</div>
                        </div>
                        <i class="fas fa-external-link-alt" style="margin-left:auto; font-size:0.7rem; opacity:0.3;"></i>
                    </a>
                </div>
                <div style="margin-top:1rem; font-size:0.75rem; color:rgba(255,255,255,0.35); line-height:1.5;">
                    💡 <strong>Mẹo:</strong> Sau khi tải lên, sao chép link video và dán vào ô "Link YouTube / Vimeo / Drive" bên dưới.
                </div>
            </div>
        `;
        return;
    }

    currentLocalVideoUrl = URL.createObjectURL(file);

    const chapterId = document.getElementById('chapter_id').value;
    if (!chapterId) {
        App.showToast('Vui lòng chọn hoặc tạo chương trước khi tải video.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('video', file);
    formData.append('course_id', courseId);
    formData.append('chapter_id', chapterId);

    document.getElementById('uploadProgress').style.display = 'block';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/upload/video');
    xhr.setRequestHeader('Authorization', `Bearer ${window.api.getToken()}`);

    xhr.upload.onprogress = (e) => {
        const pct = Math.round((e.loaded / e.total) * 100);
        document.getElementById('uploadPct').innerText = pct + '%';
        document.getElementById('uploadProgressBar').style.width = pct + '%';
    };

    xhr.onload = () => {
        document.getElementById('uploadProgress').style.display = 'none';
        if (xhr.status === 200) {
            const res = JSON.parse(xhr.responseText);
            document.getElementById('video_filename').value = res.data.path;
            App.showToast('Tải video lên thành công!', 'success');
            document.getElementById('video-info-display').innerHTML = '';
            updateVideoPreview();
        } else {
            try {
                const errRes = JSON.parse(xhr.responseText);
                App.showToast(errRes.message || 'Tải video lên thất bại.', 'error');
            } catch(e) {
                App.showToast('Tải video lên thất bại.', 'error');
            }
        }
    };
    xhr.onerror = () => {
        document.getElementById('uploadProgress').style.display = 'none';
        App.showToast('Lỗi kết nối khi tải video.', 'error');
    };
    xhr.send(formData);
}

// ════════════════════════════════════════════════════
// FORM SUBMIT (Lesson Save)
// ════════════════════════════════════════════════════
document.getElementById('lesson-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const mode = document.getElementById('edit_mode').value;
    const data = {
        chapter_id: document.getElementById('chapter_id').value,
        title: document.getElementById('lesson_title').value,
        content_type: document.getElementById('content_type').value,
        content: quill.root.innerHTML,
        video_filename: document.getElementById('video_filename').value,
        video_url: document.getElementById('video_url').value,
        order_index: document.getElementById('order_index').value,
        ai_summary: document.getElementById('ai_summary').value,
        video_transcript: document.getElementById('video_transcript').value,
        lesson_context: document.getElementById('lesson_context').value,
        key_topics: document.getElementById('key_topics').value,
        // AI Config Fields
        enable_auto_summary: document.getElementById('enable_auto_summary').checked ? 1 : 0,
        enable_auto_keywords: document.getElementById('enable_auto_keywords').checked ? 1 : 0,
        enable_auto_context: document.getElementById('enable_auto_context').checked ? 1 : 0,
        strict_ai_mode: document.getElementById('strict_ai_mode').checked ? 1 : 0,
        teacher_notes: document.getElementById('teacher_notes').value
    };
    try {
        if (mode === 'edit') await window.api.put(`/teacher/lessons/${document.getElementById('edit_lesson_id').value}`, data);
        else await window.api.post('/teacher/lessons', data);
        App.showToast('Đã lưu bài học!', 'success');
        await loadCurriculum();
        hideEditor();
    } catch(e) { App.showToast(e.message, 'error'); }
});

// ════════════════════════════════════════════════════
// QUIZ
// ════════════════════════════════════════════════════
let qCount = 0;
function addQuestion(data = null) {
    qCount++;
    const container = document.getElementById('quiz-questions-container');
    const div = document.createElement('div');
    div.className = 'quiz-card-v2';
    div.id = `quiz-card-${qCount}`;
    const currentQ = qCount;
    div.innerHTML = `
        <button type="button" class="quiz-card-delete" onclick="removeQuestion(this)" title="Xóa câu hỏi">&times;</button>
        <div class="quiz-card-header">
            <div class="quiz-card-num">${currentQ}</div>
            <input type="text" class="quiz-q-input form-control" placeholder="Câu hỏi..." value="${data?.question || ''}">
        </div>
        <div class="quiz-answers">
            ${[0,1,2,3].map(i => `
                <div class="quiz-answer-row">
                    <input type="radio" name="ans_${currentQ}" ${data?.answers[i]?.is_correct ? 'checked' : ''} class="quiz-answer-radio">
                    <input type="text" class="form-control" placeholder="Đáp án ${i+1}" value="${data?.answers[i]?.answer_text || ''}">
                </div>
            `).join('')}
        </div>
    `;
    container.appendChild(div);
}

function removeQuestion(btn) {
    const card = btn.closest('.quiz-card-v2');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        card.style.transition = 'all 0.25s ease';
        setTimeout(() => {
            card.remove();
            renumberQuestions();
        }, 250);
    }
}

function renumberQuestions() {
    const cards = document.querySelectorAll('.quiz-card-v2');
    cards.forEach((card, idx) => {
        const num = card.querySelector('.quiz-card-num');
        if (num) num.textContent = idx + 1;
        // Update radio group names to avoid conflicts
        const radios = card.querySelectorAll('.quiz-answer-radio');
        radios.forEach(r => r.name = `ans_${idx + 1}`);
    });
    qCount = cards.length;
}

async function saveQuiz() {
    let lessonId = document.getElementById('edit_lesson_id').value;
    const mode = document.getElementById('edit_mode').value;

    // Nếu là tạo mới, phải tạo Lesson trước để lấy ID bài học
    if (mode === 'create' || !lessonId) {
        const title = document.getElementById('lesson_title').value || document.getElementById('quiz_title').value;
        if (!title) {
            App.showToast('Vui lòng nhập tên bài học hoặc tiêu đề quiz', 'error');
            document.getElementById('lesson_title').focus();
            return;
        }

        const lessonData = {
            chapter_id: document.getElementById('chapter_id').value,
            title: title,
            content_type: 'quiz',
            order_index: document.getElementById('order_index').value
        };

        try {
            const res = await window.api.post('/teacher/lessons', lessonData);
            lessonId = res.data.id;
            document.getElementById('edit_lesson_id').value = lessonId;
            document.getElementById('edit_mode').value = 'edit';
        } catch(e) {
            App.showToast('Lỗi tạo bài học: ' + e.message, 'error');
            return;
        }
    }

    const title = document.getElementById('quiz_title').value;
    const questions = [];
    document.querySelectorAll('.quiz-card-v2').forEach(card => {
        const qText = card.querySelector('.quiz-q-input').value;
        const answers = [];
        card.querySelectorAll('.quiz-answer-row').forEach((row, idx) => {
            answers.push({ text: row.querySelector('input[type="text"]').value, is_correct: row.querySelector('input[type="radio"]').checked ? 1 : 0 });
        });
        questions.push({ question: qText, answers });
    });
    const explanations = document.getElementById('quiz_explanations').value;
    const hints = document.getElementById('quiz_hints').value;
    const ai_tags = document.getElementById('quiz_ai_tags').value;

    try {
        await window.api.post(`/teacher/lessons/${lessonId}/quiz`, { title, questions, explanations, hints, ai_tags });
        App.showToast('Đã lưu bài kiểm tra!', 'success');
        await loadCurriculum();
        hideEditor();
    } catch(e) { App.showToast(e.message, 'error'); }
}

async function loadQuiz(lessonId) {
    try {
        const res = await window.api.get(`/teacher/lessons/${lessonId}/quiz`);
        const qContainer = document.getElementById('quiz-questions-container');
        qContainer.innerHTML = '';
        qCount = 0;
        document.getElementById('quiz_title').value = res.data.title || '';
        document.getElementById('quiz_explanations').value = res.data.explanations || '';
        document.getElementById('quiz_hints').value = res.data.hints || '';
        document.getElementById('quiz_ai_tags').value = res.data.ai_tags || '';
        res.data.questions.forEach(q => addQuestion(q));
    } catch(e) {
        document.getElementById('quiz-questions-container').innerHTML = '';
        qCount = 0;
        addQuestion();
    }
}

// ════════════════════════════════════════════════════
// VIDEO PREVIEW
// ════════════════════════════════════════════════════
async function updateVideoPreview() {
    const videoFilename = document.getElementById('video_filename').value;
    const videoUrl = document.getElementById('video_url').value.trim();
    const infoDisplay = document.getElementById('video-info-display');
    const editMode = document.getElementById('edit_mode').value;
    const lessonId = document.getElementById('edit_lesson_id').value;

    infoDisplay.innerHTML = '';

    // Trường hợp 1: Link YouTube/Vimeo/Drive ngoài
    if (videoUrl) {
        let embedUrl = videoUrl;
        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
            const pattern = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
            const matches = videoUrl.match(pattern);
            if (matches && matches[1]) {
                embedUrl = `https://www.youtube.com/embed/${matches[1]}`;
            }
        } else if (videoUrl.includes('drive.google.com')) {
            const drivePattern = /\/file\/d\/([^\/?\&#]+)/i;
            const driveMatches = videoUrl.match(drivePattern);
            if (driveMatches && driveMatches[1]) {
                embedUrl = `https://drive.google.com/file/d/${driveMatches[1]}/preview`;
            }
        }
        infoDisplay.innerHTML = `
            <div class="video-preview-wrapper" style="border-radius:16px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); background:rgba(0,0,0,0.4); margin-bottom:1.5rem; position:relative; padding-top:56.25%;">
                <iframe src="${embedUrl}" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allowfullscreen></iframe>
            </div>
        `;
        return;
    }

    // Trường hợp 2: Video tải lên cục bộ an toàn
    if (videoFilename) {
        if (editMode === 'edit' && lessonId) {
            infoDisplay.innerHTML = `
                <div style="display:flex; justify-content:center; padding:1.5rem; opacity:0.5; font-size:0.85rem;">
                    <i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i> Đang tạo luồng xem trước bảo mật...
                </div>
            `;
            try {
                const tokenRes = await window.api.get(`/video/token/${lessonId}?course_id=${courseId}`);
                if (tokenRes.success && tokenRes.data.stream_url) {
                    infoDisplay.innerHTML = `
                        <div class="video-preview-wrapper" style="border-radius:16px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); background:rgba(0,0,0,0.4); margin-bottom:1.5rem;">
                            <video controls style="width:100%; display:block; outline:none; max-height:360px;">
                                <source src="${tokenRes.data.stream_url}" type="video/mp4">
                                Trình duyệt của bạn không hỗ trợ phát video HTML5.
                            </video>
                            <div style="padding:1rem; background:rgba(255,255,255,0.02); display:flex; align-items:center; justify-content:space-between; font-size:0.8rem; border-top:1px solid rgba(255,255,255,0.05);">
                                <span style="opacity:0.6; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:70%;"><i class="fas fa-file-video" style="color:var(--primary); margin-right:6px;"></i>${videoFilename.split('/').pop()}</span>
                                <span style="color:var(--success); font-weight:700; font-size:0.75rem;"><i class="fas fa-shield-alt" style="margin-right:4px;"></i>Secure Stream</span>
                            </div>
                        </div>
                    `;
                    return;
                }
            } catch (e) {
                console.error("Lỗi lấy luồng xem trước:", e);
            }
        }

        // Preview dùng local Object URL nếu là bài học mới tạo chưa lưu hoặc có local video url
        if (currentLocalVideoUrl) {
            infoDisplay.innerHTML = `
                <div class="video-preview-wrapper" style="border-radius:16px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); background:rgba(0,0,0,0.4); margin-bottom:1.5rem;">
                    <video controls style="width:100%; display:block; outline:none; max-height:360px;">
                        <source src="${currentLocalVideoUrl}" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ phát video HTML5.
                    </video>
                    <div style="padding:1rem; background:rgba(255,255,255,0.02); display:flex; align-items:center; justify-content:space-between; font-size:0.8rem; border-top:1px solid rgba(255,255,255,0.05);">
                        <span style="opacity:0.6; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:60%;"><i class="fas fa-file-video" style="color:var(--primary); margin-right:6px;"></i>${videoFilename.split('/').pop()}</span>
                        <span style="color:var(--primary); font-weight:700; font-size:0.75rem;"><i class="fas fa-eye" style="margin-right:4px;"></i>Xem trước cục bộ (Bản nháp)</span>
                    </div>
                </div>
            `;
            return;
        }

        // Fallback banner nếu là bài học mới tạo chưa lưu
        infoDisplay.innerHTML = `
            <div class="video-uploaded-banner" style="border-radius:12px; padding:1.25rem; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                <div style="font-size:1.8rem;">✅</div>
                <div style="flex:1;">
                    <div style="font-weight:700; color:#fff; font-size:0.9rem;">Tải video lên thành công!</div>
                    <div style="font-size:0.75rem; opacity:0.6; margin-top:4px;">File: ${videoFilename.split('/').pop()}</div>
                </div>
                <div style="font-size:0.75rem; color:var(--primary); font-weight:700;">Hãy nhấn "Lưu bài học" để cập nhật xem trước</div>
            </div>
        `;
    }
}

// ════════════════════════════════════════════════════
// UTILS
// ════════════════════════════════════════════════════
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateTranscriptBadge(status) {
    const badge = document.getElementById('transcript-status-badge');
    const verifyBtn = document.getElementById('verifyTranscriptBtn');
    if (!badge) return;

    switch (status) {
        case 'teacher_verified':
            badge.innerHTML = '✅ Giảng viên đã xác minh';
            badge.style.background = 'rgba(16,185,129,0.1)';
            badge.style.color = '#10b981';
            badge.style.borderColor = 'rgba(16,185,129,0.3)';
            if (verifyBtn) verifyBtn.style.display = 'none';
            break;
        case 'auto_generated':
            badge.innerHTML = '🤖 Tự động tạo bởi AI';
            badge.style.background = 'rgba(245,158,11,0.1)';
            badge.style.color = '#f59e0b';
            badge.style.borderColor = 'rgba(245,158,11,0.3)';
            if (verifyBtn) verifyBtn.style.display = 'inline-flex';
            break;
        default:
            badge.innerHTML = '📝 Chưa có';
            badge.style.background = 'rgba(255,255,255,0.06)';
            badge.style.color = 'rgba(255,255,255,0.4)';
            badge.style.borderColor = 'rgba(255,255,255,0.1)';
            if (verifyBtn) verifyBtn.style.display = 'none';
    }
}

async function verifyTranscript() {
    const lessonId = document.getElementById('edit_lesson_id').value;
    if (!lessonId) {
        App.showToast('Vui lòng lưu bài học trước khi xác minh.', 'error');
        return;
    }

    const confirmed = await App.confirm({
        title: 'Xác minh bản dịch',
        message: 'Bạn xác nhận rằng nội dung bản dịch/transcript hiện tại là chính xác và đã được kiểm tra?',
        type: 'success',
        confirmText: 'Xác minh',
        cancelText: 'Hủy'
    });
    if (!confirmed) return;

    try {
        await window.api.put(`/teacher/lessons/${lessonId}/verify-transcript`);
        App.showToast('Đã xác minh bản dịch thành công!', 'success');
        updateTranscriptBadge('teacher_verified');
    } catch (e) {
        App.showToast(e.message || 'Xác minh thất bại.', 'error');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
