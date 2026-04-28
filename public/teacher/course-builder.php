<?php
$pageTitle = 'Xây Dựng Lộ Trình Khóa Học';
$actor = 'teacher';
ob_start();
?>
<style>
        .builder-layout { display: flex; gap: 2rem; margin-top: 2rem; }
        .builder-sidebar { width: 350px; background: rgba(255,255,255,0.03); border-right: 1px solid var(--border-color); padding: 1.5rem; height: calc(100vh - 80px); overflow-y: auto; }
        .builder-content { flex: 1; padding: 2rem; }
        
        .chapter-card { background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; padding: 1rem; }
        .chapter-title { font-weight: bold; color: var(--text-primary); margin-bottom: 0.5rem; display: flex; justify-content: space-between;}
        .lesson-item { padding: 0.5rem; margin-top: 0.5rem; background: rgba(255,255,255,0.05); border-radius: 4px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .lesson-item:hover { background: rgba(79, 70, 229, 0.2); }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="builder-layout">
        <!-- Sidebar: Curriculum List -->
        <div class="builder-sidebar">
            <h3 class="mb-4">Cấu Trúc Lộ Trình</h3>
            <button class="btn btn-outline mb-4" onclick="createNewChapter()" style="width: 100%;">+ Thêm Chương Mới</button>
            <div id="curriculum-container">
                <p class="text-muted">Đang tải...</p>
            </div>
        </div>

        <!-- Main Content Area: Editor -->
        <div class="builder-content">
            <h1 id="course-title-display">Đang tải...</h1>
            <p class="text-secondary mb-8">Quản lý nội dung bài giảng, thêm video và trắc nghiệm.</p>

            <div class="card glass-panel" id="editor-panel" style="padding: 2rem; display: none;">
                <!-- Form for creating a lesson/quiz inside a chapter -->
                <h3 id="editor-title" class="mb-4">Thêm Bài Học Mới</h3>
                <form id="lesson-form">
                    <input type="hidden" id="chapter_id" required>
                    <div class="form-group">
                        <label class="form-label">Tên bài học</label>
                        <input type="text" id="lesson_title" class="form-control" required placeholder="Làm quen với hệ thống...">
                    </div>
                    <div class="form-group grid-cols-2">
                        <div>
                            <label class="form-label">Loại nội dung</label>
                            <select id="content_type" class="form-control" onchange="toggleContentFields()">
                                <option value="video">Video Lecture</option>
                                <option value="document">Tài Liệu Đọc</option>
                                <option value="quiz">Bài Kiểm Tra (Quiz)</option>
                            </select>
                        </div>
                        <div id="video-url-group">
                            <label class="form-label">Tải Video / Tài liệu (hoặc nhập URL bên dưới)</label>
                            <input type="file" id="lesson_file" class="form-control" style="margin-bottom: 0.5rem;" accept="video/*,.pdf,.doc,.docx">
                            <input type="url" id="video_url" class="form-control" placeholder="Hoặc nhập URL (YouTube/Vimeo/Google Drive...)">
                        </div>
                    </div>
                    <div class="form-group" id="content-group">
                        <label class="form-label">Nội dung chi tiết</label>
                        <textarea id="lesson_content" class="form-control" rows="8" placeholder="Nhập nội dung bài học..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Lưu Nội Dung</button>
                    <button type="button" class="btn btn-outline ml-2" onclick="document.getElementById('editor-panel').style.display='none'">Hủy</button>
                </form>
            </div>
            
            <div id="empty-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 50vh; color: var(--text-muted);">
                <span style="font-size: 4rem;">📝</span>
                <p class="mt-4">Chọn hoặc thêm nội dung từ cột bên trái.</p>
            </div>
        </div>
    </div>

<?php ob_start(); ?>
<script>
        let courseId = new URLSearchParams(window.location.search).get('course_id');
        let currentCurriculum = [];

        document.addEventListener('DOMContentLoaded', async () => {
            App.requireAuth(['teacher']);
            if (!courseId) {
                App.showToast('Vui lòng chọn Hành trang lộ trình từ Dashboard', 'error');
                setTimeout(() => window.location.href = '/teacher/dashboard.php', 1500);
                return;
            }
            loadCurriculum();
        });

        async function loadCurriculum() {
            try {
                // Check course ownership (just fetch to verify it works)
                // We're using the curriculum endpoint built for guests/students
                const res = await window.api.get(`/courses/${courseId}/curriculum`);
                currentCurriculum = res.data;
                renderCurriculum();
            } catch(e) {
                App.showToast("Không thể tải lộ trình.", "error");
            }
        }

        function renderCurriculum() {
            const container = document.getElementById('curriculum-container');
            if (currentCurriculum.length === 0) {
                container.innerHTML = '<p class="text-muted">Chưa có chương nào. Hãy thêm chương mới.</p>';
                return;
            }

            container.innerHTML = currentCurriculum.map(chapter => `
                <div class="chapter-card">
                    <div class="chapter-title">
                        <span>${chapter.title}</span>
                        <button onclick="openEditor(${chapter.id})" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:1.2rem;">+</button>
                    </div>
                    <div>
                        ${chapter.lessons.map(l => `
                            <div class="lesson-item">
                                <span>${l.content_type === 'video' ? '▶️' : (l.content_type==='quiz'?'❓':'📄')} ${l.title}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        }

        async function createNewChapter() {
            const title = prompt("Nhập tên Chương mới:");
            if (!title) return;
            try {
                await window.api.post('/teacher/chapters', { course_id: courseId, title: title, order_index: currentCurriculum.length + 1 });
                App.showToast('Thêm chương thành công!');
                loadCurriculum();
            } catch(e) {
                App.showToast(e.message, 'error');
            }
        }

        function openEditor(chapterId) {
            document.getElementById('empty-state').style.display = 'none';
            document.getElementById('editor-panel').style.display = 'block';
            document.getElementById('chapter_id').value = chapterId;
            document.getElementById('lesson-form').reset();
            toggleContentFields();
        }

        function toggleContentFields() {
            const type = document.getElementById('content_type').value;
            document.getElementById('video-url-group').style.display = type === 'quiz' ? 'none' : 'block';
            document.getElementById('content-group').style.display = type === 'quiz' ? 'none' : 'block';
        }

        document.getElementById('lesson-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const type = document.getElementById('content_type').value;
            const data = {
                chapter_id: document.getElementById('chapter_id').value,
                title: document.getElementById('lesson_title').value,
            };

            if(type === 'quiz') {
                data.lesson_id = data.chapter_id; // Simple mapping for now
                try {
                    await window.api.post('/teacher/quizzes', data);
                    App.showToast('Tạo Quiz thành công!');
                    loadCurriculum();
                    document.getElementById('editor-panel').style.display = 'none';
                } catch(e) { App.showToast(e.message, 'error'); }
            } else {
                data.content_type = type;
                data.video_url = document.getElementById('video_url').value;
                data.content = document.getElementById('lesson_content').value;

                try {
                    const fileInput = document.getElementById('lesson_file');
                    if (fileInput && fileInput.files.length > 0) {
                        App.showToast('Đang tải file lên, vui lòng đợi...', 'info');
                        const uploadRes = await window.api.uploadFile('/upload', fileInput.files[0]);
                        data.video_url = uploadRes.data.url;
                    }

                    await window.api.post('/teacher/lessons', data);
                    App.showToast('Tạo bài học thành công!');
                    loadCurriculum();
                    document.getElementById('editor-panel').style.display = 'none';
                } catch(e) { App.showToast(e.message, 'error'); }
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
