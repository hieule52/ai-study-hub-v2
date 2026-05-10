$pageTitle = 'Xây Dựng Khóa Học';
$actor = 'teacher';
$extraHead = '
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
';
ob_start();
?>
<style>
    .builder-layout { display: flex; gap: 0; margin-top: 0; height: calc(100vh - 65px); }
    .builder-sidebar { width: 380px; background: rgba(15,23,42,0.95); backdrop-filter: blur(20px); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; }
    .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem; }
    .builder-content { flex: 1; padding: 2.5rem; overflow-y: auto; }

    .chapter-card { background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); border-radius: 10px; margin-bottom: 1rem; overflow: hidden; }
    .chapter-header { padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.03); }
    .chapter-header .ch-title { font-weight: 700; color: var(--text-primary); font-size: 0.95rem; }
    .chapter-actions { display: flex; gap: 0.5rem; }
    .chapter-actions button { background: none; border: none; cursor: pointer; font-size: 1rem; opacity: 0.5; transition: 0.2s; padding: 4px; }
    .chapter-actions button:hover { opacity: 1; }

    .lesson-item { padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: 0.15s; border-bottom: 1px solid rgba(255,255,255,0.02); color: var(--text-secondary); font-size: 0.9rem; }
    .lesson-item:hover { background: rgba(79,70,229,0.1); color: var(--text-primary); }
    .lesson-item.active { background: rgba(79,70,229,0.15); color: var(--primary); border-left: 3px solid var(--primary); }
    .lesson-item .type-icon { font-size: 1.1rem; flex-shrink: 0; }
    .lesson-item .lesson-name { flex: 1; }
    .lesson-item .lesson-actions { display: none; gap: 0.25rem; }
    .lesson-item:hover .lesson-actions { display: flex; }
    .lesson-item .lesson-actions button { background: none; border: none; cursor: pointer; font-size: 0.85rem; opacity: 0.6; padding: 2px; }
    .lesson-item .lesson-actions button:hover { opacity: 1; }

    .add-lesson-btn { padding: 0.6rem 1rem; text-align: center; color: var(--primary); cursor: pointer; font-size: 0.85rem; font-weight: 600; opacity: 0.7; transition: 0.15s; }
    .add-lesson-btn:hover { opacity: 1; background: rgba(79,70,229,0.05); }

    .editor-card { background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2rem; }
    .editor-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }

    .upload-zone { border: 2px dashed rgba(255,255,255,0.1); border-radius: var(--radius-lg); padding: 2.5rem; text-align: center; cursor: pointer; transition: 0.2s; background: rgba(0,0,0,0.2); margin-bottom: 1rem; }
    .upload-zone:hover { border-color: var(--primary); background: rgba(79,70,229,0.05); }
    .upload-zone.uploading { border-color: var(--warning); opacity: 0.7; pointer-events: none; }
    .upload-zone .upload-icon { font-size: 3rem; margin-bottom: 0.75rem; }
    .upload-zone .upload-text { color: var(--text-muted); font-size: 0.9rem; }

    .progress-bar-wrap { background: rgba(255,255,255,0.05); border-radius: 10px; height: 8px; overflow: hidden; margin-top: 0.75rem; display: none; }
    .progress-bar-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 10px; transition: width 0.3s; width: 0%; }

    .video-info { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.2); border-radius: var(--radius-md); margin-bottom: 1rem; }
    .video-info .vi-icon { font-size: 2rem; }
    .video-info .vi-details { flex: 1; }
    .video-info .vi-name { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }
    .video-info .vi-size { font-size: 0.8rem; color: var(--text-muted); }

    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 60vh; color: var(--text-muted); text-align: center; }
    .empty-state .es-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .free-toggle { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: rgba(255,255,255,0.03); border-radius: var(--radius-md); cursor: pointer; margin-top: 0.5rem; }
    .free-toggle input { transform: scale(1.3); accent-color: var(--success); }

    /* ===== QUIZ BUILDER ===== */
    .quiz-builder { margin-top: 0.5rem; }
    .quiz-question-card {
        background: rgba(0,0,0,0.25);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        margin-bottom: 1rem;
        position: relative;
        transition: border-color 0.2s;
    }
    .quiz-question-card:hover { border-color: rgba(79,70,229,0.4); }
    .quiz-q-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
    .quiz-q-num { background: var(--primary); color: white; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 700; flex-shrink: 0; }
    .quiz-q-input { flex: 1; background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.6rem 0.9rem; color: var(--text-primary); font-family: inherit; font-size: 0.9rem; }
    .quiz-q-input:focus { outline: none; border-color: var(--primary); }
    .quiz-q-remove { background: none; border: none; cursor: pointer; font-size: 1.1rem; opacity: 0.4; transition: 0.2s; padding: 4px; }
    .quiz-q-remove:hover { opacity: 1; color: var(--danger); }

    .quiz-answers { display: flex; flex-direction: column; gap: 0.5rem; }
    .quiz-answer-row { display: flex; align-items: center; gap: 0.6rem; }
    .quiz-answer-radio { accent-color: var(--success); width: 16px; height: 16px; cursor: pointer; flex-shrink: 0; }
    .quiz-answer-input { flex: 1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-sm); padding: 0.5rem 0.75rem; color: var(--text-primary); font-family: inherit; font-size: 0.875rem; }
    .quiz-answer-input:focus { outline: none; border-color: var(--success); background: rgba(16,185,129,0.06); }
    .quiz-answer-correct-label { font-size: 0.72rem; color: var(--success); font-weight: 600; min-width: 48px; }

    .quiz-add-q-btn { width: 100%; padding: 0.75rem; border: 2px dashed rgba(79,70,229,0.4); border-radius: var(--radius-lg); background: transparent; color: var(--primary); cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: 0.2s; font-family: inherit; }
    .quiz-add-q-btn:hover { background: rgba(79,70,229,0.08); border-color: var(--primary); }
    .quiz-save-btn { margin-top: 1rem; width: 100%; padding: 0.875rem; font-size: 1rem; }

    /* Custom Form Inputs */
    .form-control {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: var(--text-primary) !important;
        border-radius: var(--radius-md) !important;
        padding: 0.75rem 1rem !important;
        transition: all 0.2s !important;
    }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
    }
    .form-label { font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem; display: block; font-size: 0.875rem; }

    /* ===== QUILL EDITOR DARK THEME ===== */
    .ql-toolbar.ql-snow {
        background: rgba(30, 41, 59, 0.95) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-top-left-radius: var(--radius-md);
        border-top-right-radius: var(--radius-md);
        position: sticky;
        top: -2.5rem; /* Bù trừ cho padding của container */
        z-index: 100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .ql-container.ql-snow {
        background: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-bottom-left-radius: var(--radius-md);
        border-bottom-right-radius: var(--radius-md);
        font-family: inherit;
        font-size: 1rem;
        color: #e2e8f0 !important;
        min-height: 300px;
    }
    .ql-editor { min-height: 300px; }
    .ql-snow .ql-stroke { stroke: #94a3b8 !important; }
    .ql-snow .ql-fill { fill: #94a3b8 !important; }
    .ql-snow .ql-picker { color: #94a3b8 !important; }
    .ql-snow.ql-toolbar button:hover .ql-stroke { stroke: #fff !important; }
    .ql-snow.ql-toolbar button:hover .ql-fill { fill: #fff !important; }
    .ql-snow.ql-toolbar button.ql-active .ql-stroke { stroke: var(--primary) !important; }

    /* Quill Alignment Fix */
    .ql-align-center { text-align: center !important; }
    .ql-align-right { text-align: right !important; }
    .ql-align-justify { text-align: justify !important; }
</style>
<?php $extraHead = ob_get_clean(); require __DIR__ . '/../layouts/header.php'; ?>

<div class="builder-layout">
    <div class="builder-sidebar">
        <div class="sidebar-header">
            <h3 style="font-size:1.15rem; margin-bottom:0.75rem;" id="course-title-display">Đang tải...</h3>
            <button class="btn btn-outline" onclick="createNewChapter()" style="width:100%; padding:0.6rem; font-size:0.9rem;">+ Thêm Chương Mới</button>
        </div>
        <div class="sidebar-scroll" id="curriculum-container">
            <p class="text-muted" style="text-align:center; padding:2rem;">Đang tải...</p>
        </div>
    </div>

    <div class="builder-content">
        <!-- Editor Panel (hidden by default) -->
        <div id="editor-panel" style="display:none;">
            <div class="editor-card">
                <div class="editor-title"><span id="editor-icon">📝</span> <span id="editor-title-text">Thêm Bài Học</span></div>
                <form id="lesson-form">
                    <input type="hidden" id="edit_mode" value="create">
                    <input type="hidden" id="edit_lesson_id">
                    <input type="hidden" id="chapter_id">
                    <input type="hidden" id="video_filename" value="">

                    <div class="form-group">
                        <label class="form-label">Tên bài học <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="lesson_title" class="form-control" required placeholder="VD: Giới thiệu Machine Learning">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Loại nội dung</label>
                            <select id="content_type" class="form-control" onchange="toggleContentFields()">
                                <option value="video">🎬 Video Bài Giảng</option>
                                <option value="text">📄 Tài Liệu Đọc</option>
                                <option value="quiz">❓ Bài Kiểm Tra</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" id="order_index" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <!-- Video Upload Zone -->
                    <div id="video-upload-section">
                        <label class="form-label">Video bài giảng</label>
                        <div id="video-info-display" style="display:none;"></div>
                        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('videoFileInput').click()">
                            <div class="upload-icon">🎥</div>
                            <div style="font-weight:600; color:var(--text-primary); margin-bottom:0.25rem;">Click để chọn video</div>
                            <div class="upload-text">MP4, WebM, MOV — Tối đa 200MB</div>
                        </div>
                        <div class="progress-bar-wrap" id="uploadProgress">
                            <div class="progress-bar-fill" id="uploadProgressBar"></div>
                        </div>
                        <input type="file" id="videoFileInput" accept="video/mp4,video/webm,video/quicktime" style="display:none;" onchange="handleVideoSelect(this)">
                        <div class="form-group" style="margin-top:1rem;">
                            <label class="form-label">Hoặc nhập URL video (YouTube/Vimeo)</label>
                            <input type="url" id="video_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>

                    <!-- Learning Objectives -->
                    <div class="form-group" id="objectives-group">
                        <label class="form-label">🎯 Mục tiêu bài học <small style="color:var(--text-muted); font-weight:400;">(Điều học viên sẽ đạt được)</small></label>
                        <input type="text" id="lesson_objectives" class="form-control" placeholder="VD: Hiểu về mạng Neuron, Phân biệt Supervised và Unsupervised Learning...">
                    </div>

                    <!-- Text Content -->
                    <div class="form-group" id="content-group">
                        <label class="form-label">📝 Nội dung chi tiết bài học</label>
                        <div id="lesson_quill_editor"></div>
                        <input type="hidden" id="lesson_content">
                        <small style="color:var(--text-muted); margin-top:0.25rem; display:block;">💡 Bạn có thể copy ảnh trực tiếp vào trình soạn thảo.</small>
                    </div>

                    <!-- AI Summary -->
                    <div class="form-group" id="ai-summary-group">
                        <label class="form-label">📌 Tóm tắt cho AI Tutor <small style="color:var(--text-muted); font-weight:400;">(Tùy chọn)</small></label>
                        <textarea id="ai_summary" class="form-control" rows="3" placeholder="Tóm tắt ngắn gọn nội dung bài. VD: Bài này dạy về vòng lặp for/while trong Python, cách dùng break/continue, và bài tập tính tổng 1-100."></textarea>
                    </div>

                    <!-- ===== QUIZ BUILDER ===== -->
                    <div id="quiz-builder-section" style="display:none;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                            <label class="form-label" style="margin:0;">❓ Câu hỏi trắc nghiệm</label>
                            <span style="font-size:0.8rem; color:var(--text-muted);">Chọn ⬤ để đánh dấu đáp án đúng</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tiêu đề bài kiểm tra</label>
                            <input type="text" id="quiz_title" class="form-control" placeholder="VD: Kiểm tra cuối chương 1">
                        </div>

                        <div class="quiz-builder" id="quiz-questions-container"></div>

                        <button type="button" class="quiz-add-q-btn" onclick="addQuestion()">
                            ➕ Thêm câu hỏi
                        </button>

                        <button type="button" class="btn btn-primary quiz-save-btn" id="quizSaveBtn" onclick="saveQuiz()">
                            💾 Lưu Bài Kiểm Tra
                        </button>
                    </div>

                    <!-- Free lesson toggle -->
                    <label class="free-toggle">
                        <input type="checkbox" id="is_free">
                        <span>🆓 Bài học miễn phí (cho phép xem trước khi mua)</span>
                    </label>

                    <div style="margin-top:1.5rem; display:flex; gap:1rem;" id="lesson-save-btns">
                        <button type="submit" class="btn btn-primary" id="saveBtn" style="padding:0.75rem 2rem;">💾 Lưu Bài Học</button>
                        <button type="button" class="btn btn-outline" onclick="hideEditor()">Hủy</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="empty-state">
            <div class="es-icon">📚</div>
            <h2 style="color:var(--text-primary); margin-bottom:0.5rem;">Xây dựng nội dung khóa học</h2>
            <p>Thêm chương từ sidebar bên trái, sau đó thêm bài học vào mỗi chương.</p>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let courseId = new URLSearchParams(window.location.search).get('course_id');
    let currentCurriculum = [];
    let uploadedVideoFilename = '';
    let quill;

    document.addEventListener('DOMContentLoaded', async () => {
        App.requireAuth(['teacher']);
        
        // Initialize Quill Editor
        quill = new Quill('#lesson_quill_editor', {
            theme: 'snow',
            placeholder: 'Viết nội dung bài giảng tại đây...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Custom Image Handler for Optimization
        quill.getModule('toolbar').addHandler('image', () => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch('/api-upload-image.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!res.ok) throw new Error('Lỗi server');
                        const data = await res.json();
                        if (data.error) throw new Error(data.error);
                        
                        // Fix lỗi mất focus:
                        let range = quill.getSelection(true);
                        if (!range) {
                            range = { index: quill.getLength() };
                        }
                        quill.insertEmbed(range.index, 'image', data.url);
                    } catch (err) {
                        console.error(err);
                        App.showToast('Lỗi khi chèn ảnh: ' + err.message, 'error');
                    }
                }
            };
        });

        if (!courseId) {
            App.showToast('Vui lòng chọn khóa học từ Dashboard', 'error');
            setTimeout(() => window.location.href = '/teacher/dashboard.php', 1500);
            return;
        }
        await loadCourseInfo();
        await loadCurriculum();
    });

    async function loadCourseInfo() {
        try {
            const res = await window.api.get(`/courses/${courseId}`);
            document.getElementById('course-title-display').textContent = res.data.title;
        } catch(e) {}
    }

    async function loadCurriculum() {
        try {
            const res = await window.api.get(`/courses/${courseId}/curriculum`);
            currentCurriculum = res.data;
            renderCurriculum();
        } catch(e) {
            App.showToast("Không thể tải giáo trình.", "error");
        }
    }

    function renderCurriculum() {
        const container = document.getElementById('curriculum-container');
        if (!currentCurriculum || currentCurriculum.length === 0) {
            container.innerHTML = '<p class="text-muted" style="text-align:center;padding:2rem;">Chưa có chương nào. Hãy thêm chương mới!</p>';
            return;
        }
        container.innerHTML = currentCurriculum.map((chapter, ci) => `
            <div class="chapter-card">
                <div class="chapter-header">
                    <span class="ch-title">📖 Chương ${ci+1}: ${chapter.title}</span>
                    <div class="chapter-actions">
                        <button onclick="openEditor(${chapter.id})" title="Thêm bài">➕</button>
                        <button onclick="editChapter(${chapter.id}, '${chapter.title.replace(/'/g,"\\'")}')" title="Sửa">✏️</button>
                        <button onclick="deleteChapter(${chapter.id})" title="Xóa">🗑️</button>
                    </div>
                </div>
                ${(chapter.lessons && chapter.lessons.length > 0) ? chapter.lessons.map(l => `
                    <div class="lesson-item" id="lesson-nav-${l.id}" onclick="editExistingLesson(${l.id}, ${chapter.id})">
                        <span class="type-icon">${l.content_type==='video'?'🎬':(l.content_type==='quiz'?'❓':'📄')}</span>
                        <span class="lesson-name">${l.title}</span>
                        ${l.is_free?'<span style="font-size:0.7rem;background:var(--success);color:#fff;padding:2px 6px;border-radius:4px;">FREE</span>':''}
                        <div class="lesson-actions">
                            <button onclick="event.stopPropagation();deleteLesson(${l.id})" title="Xóa">🗑️</button>
                        </div>
                    </div>
                `).join('') : '<div style="padding:0.75rem 1.25rem;color:var(--text-muted);font-size:0.85rem;">Chưa có bài học</div>'}
                <div class="add-lesson-btn" onclick="openEditor(${chapter.id})">+ Thêm bài học</div>
            </div>
        `).join('');
    }

    // Chapter CRUD
    async function createNewChapter() {
        const title = prompt("Nhập tên Chương mới:");
        if (!title) return;
        try {
            await window.api.post('/teacher/chapters', { course_id: courseId, title, order_index: currentCurriculum.length });
            App.showToast('✅ Thêm chương thành công!');
            loadCurriculum();
        } catch(e) { App.showToast(e.message, 'error'); }
    }

    async function editChapter(id, oldTitle) {
        const title = prompt("Sửa tên chương:", oldTitle);
        if (!title || title === oldTitle) return;
        try {
            await window.api.put(`/teacher/chapters/${id}`, { title });
            App.showToast('✅ Đã cập nhật chương!');
            loadCurriculum();
        } catch(e) { App.showToast(e.message, 'error'); }
    }

    async function deleteChapter(id) {
        if (!confirm('Xóa chương này? Tất cả bài học trong chương sẽ bị xóa.')) return;
        try {
            await window.api.delete(`/teacher/chapters/${id}`);
            App.showToast('✅ Đã xóa chương!');
            hideEditor();
            loadCurriculum();
        } catch(e) { App.showToast(e.message, 'error'); }
    }

    // Lesson Editor
    function openEditor(chapterId) {
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('editor-panel').style.display = 'block';
        document.getElementById('chapter_id').value = chapterId;
        document.getElementById('edit_mode').value = 'create';
        document.getElementById('edit_lesson_id').value = '';
        document.getElementById('editor-title-text').textContent = 'Thêm Bài Học Mới';
        document.getElementById('editor-icon').textContent = '➕';
        document.getElementById('lesson-form').reset();
        quill.root.innerHTML = '';
        document.getElementById('video_filename').value = '';
        document.getElementById('video-info-display').style.display = 'none';
        uploadedVideoFilename = '';
        toggleContentFields();
    }

    async function editExistingLesson(lessonId, chapterId) {
        try {
            const res = await window.api.get(`/lessons/${lessonId}`);
            const lesson = res.data;
            document.getElementById('empty-state').style.display = 'none';
            document.getElementById('editor-panel').style.display = 'block';
            document.getElementById('edit_mode').value = 'edit';
            document.getElementById('edit_lesson_id').value = lessonId;
            document.getElementById('chapter_id').value = chapterId;
            document.getElementById('editor-title-text').textContent = 'Chỉnh sửa: ' + lesson.title;
            document.getElementById('editor-icon').textContent = '✏️';
            document.getElementById('lesson_title').value = lesson.title || '';
            document.getElementById('content_type').value = lesson.content_type || 'video';
            document.getElementById('video_url').value = lesson.video_url || '';
            quill.root.innerHTML = lesson.content || '';
            document.getElementById('lesson_objectives').value = lesson.objectives || '';
            document.getElementById('ai_summary').value = lesson.ai_summary || '';
            document.getElementById('order_index').value = lesson.order_index || 0;
            document.getElementById('is_free').checked = !!lesson.is_free;
            
            if (lesson.video_filename) {
                uploadedVideoFilename = lesson.video_filename;
                document.getElementById('video_filename').value = lesson.video_filename;
                document.getElementById('video-info-display').style.display = 'block';
                document.getElementById('video-info-display').innerHTML = `<div class="video-info"><div class="vi-icon">✅</div><div class="vi-details"><div class="vi-name">Video đã upload</div><div class="vi-size">${lesson.video_filename}</div></div></div>`;
            } else {
                document.getElementById('video-info-display').style.display = 'none';
            }
            toggleContentFields();
            document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
            const activeEl = document.getElementById(`lesson-nav-${lessonId}`);
            if (activeEl) activeEl.classList.add('active');
        } catch(e) { App.showToast(e.message, 'error'); }
    }

    function hideEditor() {
        document.getElementById('editor-panel').style.display = 'none';
        document.getElementById('empty-state').style.display = 'flex';
        document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
    }

    function toggleContentFields() {
        const type = document.getElementById('content_type').value;
        const isQuiz = type === 'quiz';
        document.getElementById('video-upload-section').style.display = (type === 'video') ? 'block' : 'none';
        document.getElementById('content-group').style.display = isQuiz ? 'none' : 'block';
        document.getElementById('ai-summary-group').style.display = isQuiz ? 'none' : 'block';
        document.getElementById('quiz-builder-section').style.display = isQuiz ? 'block' : 'none';
        // Hide/show default save button when quiz (quiz has own save btn)
        document.getElementById('lesson-save-btns').style.display = isQuiz ? 'none' : 'flex';
        // Auto-load quiz if editing existing quiz lesson
        if (isQuiz) {
            const lessonId = document.getElementById('edit_lesson_id').value;
            if (lessonId) loadQuiz(lessonId);
            else if (document.getElementById('quiz-questions-container').children.length === 0) addQuestion();
        }
    }

    // ==========================================
    // QUIZ BUILDER LOGIC
    // ==========================================
    let questionCounter = 0;

    function addQuestion(data = null) {
        questionCounter++;
        const qNum = questionCounter;
        const container = document.getElementById('quiz-questions-container');
        const qId = `q_${Date.now()}_${qNum}`;

        const div = document.createElement('div');
        div.className = 'quiz-question-card';
        div.dataset.qid = qId;

        const questionText = data?.question ?? '';
        const answers = data?.answers ?? [
            { answer_text: '', is_correct: 1 },
            { answer_text: '', is_correct: 0 },
            { answer_text: '', is_correct: 0 },
            { answer_text: '', is_correct: 0 }
        ];

        div.innerHTML = `
            <div class="quiz-q-header">
                <div class="quiz-q-num">${qNum}</div>
                <input class="quiz-q-input" type="text" placeholder="Nhập câu hỏi..." value="${questionText.replace(/"/g,'&quot;')}">
                <button type="button" class="quiz-q-remove" onclick="removeQuestion(this)" title="Xóa câu hỏi">🗑️</button>
            </div>
            <div class="quiz-answers">
                ${answers.map((a, i) => `
                <div class="quiz-answer-row">
                    <input type="radio" class="quiz-answer-radio" name="correct_${qId}" value="${i}" ${a.is_correct ? 'checked' : ''} title="Đáp án đúng">
                    <input class="quiz-answer-input" type="text" placeholder="Đáp án ${i+1}..." value="${(a.answer_text||'').replace(/"/g,'&quot;')}">
                    <span class="quiz-answer-correct-label">${a.is_correct ? '✅ Đúng' : ''}</span>
                </div>`).join('')}
            </div>
        `;

        // Update ✅ label on radio change
        div.querySelectorAll('.quiz-answer-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                div.querySelectorAll('.quiz-answer-correct-label').forEach((lbl, i) => {
                    lbl.textContent = (i === parseInt(radio.value)) ? '✅ Đúng' : '';
                });
            });
        });

        container.appendChild(div);
        // Renumber all
        renumberQuestions();
    }

    function removeQuestion(btn) {
        btn.closest('.quiz-question-card').remove();
        renumberQuestions();
    }

    function renumberQuestions() {
        document.querySelectorAll('#quiz-questions-container .quiz-question-card').forEach((card, i) => {
            card.querySelector('.quiz-q-num').textContent = i + 1;
        });
    }

    function collectQuizData() {
        const title = document.getElementById('quiz_title').value.trim();
        const questions = [];
        document.querySelectorAll('#quiz-questions-container .quiz-question-card').forEach(card => {
            const qText = card.querySelector('.quiz-q-input').value.trim();
            if (!qText) return;
            const radios = card.querySelectorAll('.quiz-answer-radio');
            const inputs = card.querySelectorAll('.quiz-answer-input');
            const answers = [];
            let checkedIdx = 0;
            radios.forEach((r, i) => { if (r.checked) checkedIdx = i; });
            inputs.forEach((inp, i) => {
                const txt = inp.value.trim();
                if (!txt) return;
                answers.push({ text: txt, is_correct: (i === checkedIdx) ? 1 : 0 });
            });
            if (answers.length >= 2) questions.push({ question: qText, answers });
        });
        return { title, questions };
    }

    async function saveQuiz() {
        let lessonId = document.getElementById('edit_lesson_id').value;
        const { title, questions } = collectQuizData();
        
        if (!title) { App.showToast('Nhập tiêu đề bài kiểm tra.', 'error'); return; }
        if (questions.length === 0) { App.showToast('Cần ít nhất 1 câu hỏi có 2+ đáp án.', 'error'); return; }

        const btn = document.getElementById('quizSaveBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ Đang lưu...';

        try {
            // Collect lesson data
            const lessonData = {
                chapter_id: document.getElementById('chapter_id').value,
                title: document.getElementById('lesson_title').value,
                content_type: 'quiz',
                video_url: '', video_filename: '', content: '', objectives: '', ai_summary: '',
                order_index: parseInt(document.getElementById('order_index').value) || 0,
                is_free: document.getElementById('is_free').checked ? 1 : 0
            };
            
            if (!lessonData.title) { throw new Error('Vui lòng nhập tên bài học ở phía trên'); }

            // Create or update lesson
            if (!lessonId) {
                const res = await window.api.post('/teacher/lessons', lessonData);
                lessonId = res.data?.id;
                document.getElementById('edit_lesson_id').value = lessonId;
                document.getElementById('edit_mode').value = 'edit';
            } else {
                await window.api.put(`/teacher/lessons/${lessonId}`, lessonData);
            }

            // Save quiz questions
            await window.api.post(`/teacher/lessons/${lessonId}/quiz`, { title, questions });
            App.showToast(`✅ Đã lưu toàn bộ bài kiểm tra!`, 'success');
            
            await loadCurriculum();
            hideEditor();
        } catch(e) { 
            App.showToast(e.message, 'error'); 
        }
        
        btn.disabled = false;
        btn.innerHTML = '💾 Lưu Bài Kiểm Tra';
    }

    async function loadQuiz(lessonId) {
        try {
            const res = await window.api.get(`/teacher/lessons/${lessonId}/quiz`);
            const quiz = res.data;
            document.getElementById('quiz-questions-container').innerHTML = '';
            questionCounter = 0;
            if (quiz && quiz.questions && quiz.questions.length > 0) {
                document.getElementById('quiz_title').value = quiz.title || '';
                quiz.questions.forEach(q => addQuestion(q));
            } else {
                document.getElementById('quiz_title').value = '';
                addQuestion();
            }
        } catch(e) {
            // Chưa có quiz — thêm 1 câu hỏi trống
            document.getElementById('quiz-questions-container').innerHTML = '';
            questionCounter = 0;
            addQuestion();
        }
    }

    // Video Upload
    async function handleVideoSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        if (file.size > 200 * 1024 * 1024) {
            App.showToast('Video vượt quá 200MB!', 'error');
            return;
        }

        const zone = document.getElementById('uploadZone');
        const progressWrap = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');
        zone.classList.add('uploading');
        zone.innerHTML = `<div class="upload-icon">⏳</div><div style="font-weight:600;color:var(--text-primary);">Đang tải lên: ${file.name}</div><div class="upload-text">${(file.size / 1024 / 1024).toFixed(1)} MB</div>`;
        progressWrap.style.display = 'block';

        try {
            const chapterId = document.getElementById('chapter_id').value;
            const formData = new FormData();
            formData.append('video', file);
            formData.append('course_id', courseId);
            formData.append('chapter_id', chapterId);

            // XHR for progress tracking
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/api/upload/video');
            const token = window.api.getToken();
            if (token) xhr.setRequestHeader('Authorization', `Bearer ${token}`);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = pct + '%';
                }
            };

            const result = await new Promise((resolve, reject) => {
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        const err = JSON.parse(xhr.responseText);
                        reject(new Error(err.message || 'Upload failed'));
                    }
                };
                xhr.onerror = () => reject(new Error('Network error'));
                xhr.send(formData);
            });

            uploadedVideoFilename = result.data.filename;
            document.getElementById('video_filename').value = result.data.path;
            
            zone.classList.remove('uploading');
            zone.innerHTML = `<div class="upload-icon">✅</div><div style="font-weight:600;color:var(--success);">Upload thành công!</div><div class="upload-text">${result.data.original_name} — ${result.data.size_formatted}</div>`;
            
            document.getElementById('video-info-display').style.display = 'block';
            document.getElementById('video-info-display').innerHTML = `<div class="video-info"><div class="vi-icon">🎬</div><div class="vi-details"><div class="vi-name">${result.data.original_name}</div><div class="vi-size">${result.data.size_formatted} • Video đã được mã hóa bảo mật</div></div><button type="button" onclick="removeVideo()" style="background:none;border:none;cursor:pointer;font-size:1.2rem;opacity:0.5;">❌</button></div>`;

            App.showToast('✅ Upload video thành công!', 'success');
        } catch(e) {
            zone.classList.remove('uploading');
            zone.innerHTML = `<div class="upload-icon">❌</div><div style="font-weight:600;color:var(--danger);">Upload thất bại</div><div class="upload-text">${e.message}</div>`;
            App.showToast(e.message, 'error');
        }
        setTimeout(() => { progressWrap.style.display = 'none'; }, 2000);
    }

    function removeVideo() {
        uploadedVideoFilename = '';
        document.getElementById('video_filename').value = '';
        document.getElementById('video-info-display').style.display = 'none';
        const zone = document.getElementById('uploadZone');
        zone.innerHTML = `<div class="upload-icon">🎥</div><div style="font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Click để chọn video</div><div class="upload-text">MP4, WebM, MOV — Tối đa 200MB</div>`;
    }

    // Save Lesson
    document.getElementById('lesson-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const mode = document.getElementById('edit_mode').value;
        const type = document.getElementById('content_type').value;
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ Đang lưu...';

        const data = {
            chapter_id: document.getElementById('chapter_id').value,
            title: document.getElementById('lesson_title').value,
            content_type: type,
            video_url: document.getElementById('video_url').value || '',
            video_filename: document.getElementById('video_filename').value || '',
            content: quill.root.innerHTML || '',
            objectives: document.getElementById('lesson_objectives').value || '',
            ai_summary: document.getElementById('ai_summary').value || '',
            order_index: parseInt(document.getElementById('order_index').value) || 0,
            is_free: document.getElementById('is_free').checked ? 1 : 0
        };

        if (type !== 'quiz' && quill.getText().trim().length === 0) {
            App.showToast('Vui lòng nhập nội dung chi tiết cho bài học.', 'warning');
            btn.disabled = false;
            btn.innerHTML = '💾 Lưu Bài Học';
            return;
        }

        try {
            let savedLessonId = null;
            if (mode === 'edit') {
                savedLessonId = document.getElementById('edit_lesson_id').value;
                await window.api.put(`/teacher/lessons/${savedLessonId}`, data);
                App.showToast('✅ Cập nhật bài học thành công!');
            } else {
                const res = await window.api.post('/teacher/lessons', data);
                savedLessonId = res.data?.id;
                App.showToast('✅ Tạo bài học thành công!');
                // Nếu là quiz — set lesson id rồi chuyển sang quiz builder
                if (type === 'quiz' && savedLessonId) {
                    document.getElementById('edit_lesson_id').value = savedLessonId;
                    document.getElementById('edit_mode').value = 'edit';
                    btn.disabled = false;
                    btn.innerHTML = '💾 Lưu Bài Học';
                    await loadCurriculum();
                    App.showToast('📝 Bài học đã lưu! Hãy thêm câu hỏi quiz bên dưới.', 'info');
                    return;
                }
            }
            await loadCurriculum();
            hideEditor();
        } catch(e) {
            App.showToast(e.message, 'error');
        }
        btn.disabled = false;
        btn.innerHTML = '💾 Lưu Bài Học';
    });

    // Delete Lesson
    async function deleteLesson(id) {
        if (!confirm('Xóa bài học này?')) return;
        try {
            await window.api.delete(`/teacher/lessons/${id}`);
            App.showToast('✅ Đã xóa bài học!');
            hideEditor();
            loadCurriculum();
        } catch(e) { App.showToast(e.message, 'error'); }
    }
</script>
<?php $extraScripts = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
