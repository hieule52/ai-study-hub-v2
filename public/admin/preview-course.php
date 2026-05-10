<?php
$pageTitle = 'Preview Khóa Học - Admin AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
    /* Preview Mode Banner */
    .preview-banner {
        background: linear-gradient(135deg, rgba(251, 146, 60, 0.15), rgba(239, 68, 68, 0.1));
        border: 1px solid rgba(251, 146, 60, 0.3);
        border-radius: var(--radius-lg);
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .preview-banner .badge {
        background: var(--warning);
        color: #000;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .preview-banner .info {
        flex: 1;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Course Info Card */
    .course-info-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    /* Chapter & Lesson Preview */
    .preview-chapter {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .preview-chapter-header {
        padding: 1rem 1.25rem;
        background: rgba(79, 70, 229, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .preview-chapter-header:hover {
        background: rgba(79, 70, 229, 0.1);
    }
    .preview-lesson {
        padding: 0.75rem 1.25rem 0.75rem 2.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.02);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    .preview-lesson:hover {
        background: rgba(79, 70, 229, 0.08);
        color: var(--text-primary);
    }
    .preview-lesson.active {
        background: rgba(79, 70, 229, 0.12);
        color: var(--primary);
        border-left: 3px solid var(--primary);
    }

    /* Lesson Content Preview */
    .lesson-preview-area {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: var(--radius-lg);
        padding: 2rem;
        min-height: 400px;
    }

    /* Quill content styling */
    .ql-editor { font-size: 1.05rem; color: #cbd5e1; line-height: 1.7; padding: 0; }
    .ql-editor h1, .ql-editor h2, .ql-editor h3 { color: #f8fafc; margin-top: 1.5rem; margin-bottom: 0.75rem; }
    .ql-editor code { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; font-family: monospace; }
    .ql-editor pre { background: #0f172a; padding: 1.5rem; border-radius: 12px; overflow-x: auto; margin: 1rem 0; border: 1px solid rgba(255,255,255,0.1); }
    .ql-editor pre code { background: transparent; padding: 0; }
    .ql-editor img { max-width: 100%; height: auto; border-radius: 12px; margin: 1rem 0; }
</style>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<!-- Preview Mode Banner -->
<div class="preview-banner">
    <span class="badge">👁️ PREVIEW MODE</span>
    <span class="info">Bạn đang xem trước khóa học với tư cách <strong>Quản trị viên</strong>. Không có hành động nào (đánh dấu hoàn thành, ghi nhận tiến độ) được thực hiện.</span>
    <a href="/admin/courses.php" class="btn btn-outline" style="border-radius: 20px; font-size: 0.85rem; white-space: nowrap;">← Quay lại Duyệt</a>
</div>

<!-- Course Info -->
<div class="course-info-card" id="course-info">
    <p class="text-secondary">Đang tải thông tin khóa học...</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem;">
    <!-- Left: Lesson Content -->
    <div>
        <h3 id="lesson-title" style="margin-bottom: 1rem; font-size: 1.5rem;">Chọn một bài học để xem trước</h3>
        
        <!-- Video Preview -->
        <div id="video-area" style="display: none; margin-bottom: 2rem; background: #000; border-radius: var(--radius-lg); aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
        </div>

        <!-- Lesson Content -->
        <div class="lesson-preview-area" id="lesson-content">
            <div style="text-align: center; padding: 4rem 2rem; opacity: 0.5;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📖</div>
                <p>Chọn một bài học ở cột bên phải để xem nội dung.</p>
            </div>
        </div>

        <!-- Quiz Preview -->
        <div id="quiz-area" style="margin-top: 2rem;"></div>
    </div>

    <!-- Right: Curriculum -->
    <div>
        <div class="card glass-panel" style="padding: 1.5rem; position: sticky; top: 100px;">
            <h3 style="margin-bottom: 1rem;">📋 Giáo Trình</h3>
            <div id="curriculum-list">
                <p class="text-secondary">Đang tải...</p>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['admin']);
        if (!user) return;

        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get('course_id');
        if (!courseId) {
            App.showToast('Thiếu course_id trên URL', 'error');
            return;
        }

        await loadCoursePreview(courseId);
    });

    async function loadCoursePreview(courseId) {
        try {
            // Load course info
            const courseRes = await window.api.get(`/courses/${courseId}`);
            const course = courseRes.data;

            document.getElementById('course-info').innerHTML = `
                <div class="flex items-center gap-4">
                    <div style="font-size: 3rem;">📚</div>
                    <div style="flex: 1;">
                        <h2 style="margin-bottom: 0.5rem;">${course.title}</h2>
                        <p class="text-secondary" style="margin-bottom: 0.5rem;">${course.description || 'Chưa có mô tả'}</p>
                        <div class="flex gap-4 text-sm" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                            <span>👨‍🏫 Giảng viên: <strong>${course.teacher_name || course.teacher_email || 'Chưa rõ'}</strong></span>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <span>📊 ${course.level || 'beginner'}</span>
                            <span>💰 ${course.price > 0 ? new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(course.price) : 'Miễn phí'}</span>
                            <span>📝 ${course.total_lessons || 0} bài học</span>
                            <span style="background: ${course.status === 'pending' ? 'var(--warning)' : course.status === 'approved' ? 'var(--success)' : 'var(--text-secondary)'}; color: #000; padding: 2px 8px; border-radius: 10px; font-weight: 600; font-size: 0.75rem;">${course.status?.toUpperCase()}</span>
                        </div>
                    </div>
                    <div>
                        <button onclick="approveCourseFromPreview(${course.id})" class="btn" style="background: var(--success); color: #fff; border: none; margin-right: 0.5rem;">✅ Duyệt</button>
                        <button onclick="rejectCourseFromPreview(${course.id})" class="btn btn-outline" style="border-color: var(--danger); color: var(--danger);">❌ Từ chối</button>
                    </div>
                </div>
            `;

            // Load curriculum
            const currRes = await window.api.get(`/courses/${courseId}/curriculum`);
            const chapters = currRes.data;
            const list = document.getElementById('curriculum-list');

            if (chapters.length === 0) {
                list.innerHTML = '<p class="text-secondary">Khóa học chưa có nội dung.</p>';
                return;
            }

            let html = '';
            chapters.forEach(ch => {
                html += `<div class="preview-chapter">
                    <div class="preview-chapter-header">
                        <span>📁 ${ch.title}</span>
                        <small class="text-secondary">${ch.lessons?.length || 0} bài</small>
                    </div>`;
                if (ch.lessons) {
                    ch.lessons.forEach(les => {
                        const icon = les.content_type === 'video' ? '🎬' : les.content_type === 'quiz' ? '📝' : '📄';
                        html += `<div class="preview-lesson" data-lesson-id="${les.id}" onclick="previewLesson(${les.id}, ${courseId})">
                            <span>${icon}</span>
                            <span style="flex:1">${les.title}</span>
                            ${les.is_free ? '<small style="color:var(--success)">FREE</small>' : ''}
                        </div>`;
                    });
                }
                html += '</div>';
            });
            list.innerHTML = html;

        } catch (e) {
            App.showToast('Lỗi tải khóa học: ' + e.message, 'error');
        }
    }

    async function previewLesson(lessonId, courseId) {
        // Update active state
        document.querySelectorAll('.preview-lesson').forEach(el => el.classList.remove('active'));
        const active = document.querySelector(`[data-lesson-id="${lessonId}"]`);
        if (active) active.classList.add('active');

        try {
            const res = await window.api.get(`/lessons/${lessonId}`);
            const lesson = res.data;

            document.getElementById('lesson-title').innerText = lesson.title;

            // Video area
            const videoArea = document.getElementById('video-area');
            if (lesson.content_type === 'video' && (lesson.video_filename || lesson.video_url)) {
                videoArea.style.display = 'flex';
                if (lesson.video_filename) {
                    try {
                        const tokenRes = await window.api.get(`/video/token/${lessonId}?course_id=${courseId}`);
                        videoArea.innerHTML = `
                            <video controls controlsList="nodownload" style="width:100%;height:100%;border-radius:var(--radius-lg);">
                                <source src="${tokenRes.data.stream_url}" type="video/mp4">
                            </video>`;
                    } catch (e) {
                        videoArea.innerHTML = `<div style="text-align:center;"><div style="font-size:2rem;">🔒</div><p class="text-secondary">${e.message}</p></div>`;
                    }
                } else if (lesson.video_url) {
                    videoArea.innerHTML = `<iframe src="${lesson.video_url}" width="100%" height="100%" frameborder="0" allowfullscreen style="border-radius:var(--radius-lg);"></iframe>`;
                }
            } else {
                videoArea.style.display = 'none';
            }

            // Content area
            const contentArea = document.getElementById('lesson-content');
            if (lesson.content) {
                contentArea.innerHTML = `<div class="ql-snow"><div class="ql-editor">${lesson.content}</div></div>`;
            } else {
                contentArea.innerHTML = '<p class="text-secondary">Bài học này chưa có nội dung văn bản.</p>';
            }

            // Quiz preview (read-only)
            const quizArea = document.getElementById('quiz-area');
            try {
                const quizRes = await window.api.get(`/teacher/lessons/${lessonId}/quiz`);
                if (quizRes.data && quizRes.data.id) {
                    const quiz = quizRes.data;
                    let qhtml = `<div class="card glass-panel" style="padding: 1.5rem; border-color: rgba(16,185,129,0.2);">
                        <h3 style="color: var(--success); margin-bottom: 1rem;">🧠 Quiz: ${quiz.title}</h3>`;
                    if (quiz.questions) {
                        quiz.questions.forEach((q, i) => {
                            qhtml += `<div style="padding: 1rem; margin-bottom: 0.75rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md);">
                                <p style="font-weight: 600; margin-bottom: 0.5rem;">Câu ${i+1}: ${q.question}</p>`;
                            q.answers.forEach(a => {
                                qhtml += `<div style="padding: 0.4rem 0.75rem; margin-bottom: 0.25rem; opacity: 0.8; ${a.is_correct ? 'color: var(--success); font-weight: 600;' : ''}">
                                    ${a.is_correct ? '✅' : '⬚'} ${a.answer_text}
                                </div>`;
                            });
                            qhtml += '</div>';
                        });
                    }
                    qhtml += '<p class="text-secondary text-sm" style="margin-top: 0.75rem;">💡 Đáp án đúng được đánh dấu ✅ (chỉ hiển thị trong preview mode)</p></div>';
                    quizArea.innerHTML = qhtml;
                } else {
                    quizArea.innerHTML = '';
                }
            } catch (e) {
                quizArea.innerHTML = '';
            }

        } catch (e) {
            App.showToast('Lỗi tải bài học: ' + e.message, 'error');
        }
    }

    async function approveCourseFromPreview(id) {
        if (!confirm('Xác nhận DUYỆT khóa học này?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/approve`);
            App.showToast('Duyệt khóa học thành công!', 'success');
            setTimeout(() => window.location.href = '/admin/courses.php', 1500);
        } catch (e) {
            App.showToast(e.message, 'error');
        }
    }

    async function rejectCourseFromPreview(id) {
        if (!confirm('Từ chối khóa học? Trạng thái sẽ trở về Bản nháp.')) return;
        try {
            await window.api.put(`/admin/courses/${id}/reject`);
            App.showToast('Đã từ chối khóa học.', 'success');
            setTimeout(() => window.location.href = '/admin/courses.php', 1500);
        } catch (e) {
            App.showToast(e.message, 'error');
        }
    }
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>
