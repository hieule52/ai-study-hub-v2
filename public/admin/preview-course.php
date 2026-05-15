<?php
$pageTitle = 'Preview Khóa Học — Admin AI Study Hub';
$actor = 'admin';
$noSidebar = true;
$extraHead = '
    <link rel="stylesheet" href="/assets/css/admin/layout.css?v=' . time() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .preview-mode-tag {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .lesson-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(20px);
        }

        .video-placeholder {
            width: 100%;
            aspect-ratio: 16/9;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            margin-bottom: 2rem;
        }

        .curriculum-sticky {
            position: sticky;
            top: 2rem;
        }

        .chapter-preview {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .chapter-preview-header {
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .lesson-item {
            padding: 0.875rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            border-bottom: 1px solid rgba(255,255,255,0.02);
        }

        .lesson-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .lesson-item.active {
            background: rgba(129, 140, 248, 0.1);
            color: #818cf8;
            border-left: 3px solid #818cf8;
        }

        /* Quill content styling */
        .ql-editor { font-size: 1.05rem; color: #cbd5e1; line-height: 1.7; padding: 0; }
        .ql-editor h1, .ql-editor h2, .ql-editor h3 { color: #f8fafc; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .ql-editor code { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .ql-editor pre { background: #0f172a; padding: 1.5rem; border-radius: 12px; overflow-x: auto; margin: 1rem 0; border: 1px solid rgba(255,255,255,0.1); }
        .ql-editor img { max-width: 100%; height: auto; border-radius: 12px; margin: 1rem 0; }
    </style>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-body">
    <?php require __DIR__ . '/../layouts/admin_sidebar.php'; ?>

    <div class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div class="preview-mode-tag">
                        <i class="fas fa-eye"></i> PREVIEW MODE
                    </div>
                    <h1 class="admin-page-title" id="top-course-title">Xem trước khóa học</h1>
                </div>
                <p class="admin-page-subtitle">Bạn đang xem nội dung với quyền Quản trị viên</p>
            </div>
            <div class="admin-topbar-right">
                <a href="/admin/courses.php" class="admin-btn admin-btn-ghost">
                    <i class="fas fa-arrow-left"></i> Quay lại Danh sách
                </a>
            </div>
        </div>

        <div class="admin-content">
            <!-- Course Summary Header -->
            <div class="admin-card" id="course-header" style="margin-bottom: 2rem; border-left: 4px solid #818cf8;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div id="course-meta">
                        <div style="font-size:0.8rem; opacity:0.4; margin-bottom:0.5rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em;">Thông tin tổng quan</div>
                        <h2 id="course-title-display" style="font-size:1.5rem; margin-bottom:0.5rem;">Đang tải...</h2>
                        <div id="course-badges" style="display:flex; gap:0.75rem; align-items:center;">
                            <!-- Badges will be injected here -->
                        </div>
                    </div>
                    <div id="course-actions" style="display:flex; gap:0.75rem;">
                        <!-- Actions will be injected here -->
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem;">
                <!-- Left Content: Player & Text -->
                <div>
                    <div id="player-container" style="display:none;">
                        <div class="video-placeholder" id="video-root">
                            <!-- Video element injected here -->
                        </div>
                    </div>

                    <div class="admin-card" style="padding: 2.5rem; min-height: 500px;">
                        <h3 id="active-lesson-title" style="font-size:1.75rem; margin-bottom:1.5rem; color:#fff;">Chọn một bài học để bắt đầu</h3>
                        <div id="lesson-body-content">
                            <div style="text-align:center; padding:5rem 2rem; opacity:0.3;">
                                <i class="fas fa-book-open" style="font-size:4rem; margin-bottom:1.5rem;"></i>
                                <p>Sử dụng menu bên phải để xem chi tiết từng chương và bài học</p>
                            </div>
                        </div>
                        
                        <!-- Quiz Preview Injected Here -->
                        <div id="quiz-root" style="margin-top:2rem;"></div>
                    </div>
                </div>

                <!-- Right Sidebar: Curriculum -->
                <div class="curriculum-sticky">
                    <div class="admin-card" style="padding: 1.5rem;">
                        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid rgba(255,255,255,0.05);">
                            <i class="fas fa-list-ul" style="color:#818cf8;"></i>
                            <h4 style="font-weight:800; text-transform:uppercase; font-size:0.85rem; letter-spacing:0.05em;">Cấu trúc bài giảng</h4>
                        </div>
                        <div id="curriculum-container">
                            <!-- Curriculum tree injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['admin']);
        if (!user) return;

        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get('course_id');
        if (!courseId) {
            App.showToast('Không tìm thấy ID khóa học', 'error');
            return;
        }

        await loadFullPreview(courseId);
    });

    async function loadFullPreview(courseId) {
        try {
            // 1. Fetch Course Data
            const courseRes = await window.api.get(`/courses/${courseId}`);
            const course = courseRes.data;

            document.getElementById('top-course-title').innerText = course.title;
            document.getElementById('course-title-display').innerText = course.title;
            
            const badgesRoot = document.getElementById('course-badges');
            badgesRoot.innerHTML = `
                <span class="admin-badge ${course.status === 'approved' ? 'badge-approved' : 'badge-pending'}">${course.status?.toUpperCase()}</span>
                <span style="font-size:0.85rem; opacity:0.6;"><i class="fas fa-user-tie"></i> ${course.teacher_name}</span>
                <span style="font-size:0.85rem; color:#f59e0b; font-weight:700;"><i class="fas fa-coins"></i> ${course.price > 0 ? new Intl.NumberFormat('vi-VN').format(course.price) + 'đ' : 'Miễn phí'}</span>
            `;

            const actionsRoot = document.getElementById('course-actions');
            if (course.status === 'pending') {
                actionsRoot.innerHTML = `
                    <button onclick="approveCourse(${course.id})" class="admin-btn admin-btn-success"><i class="fas fa-check"></i> Phê duyệt</button>
                    <button onclick="rejectCourse(${course.id})" class="admin-btn admin-btn-danger"><i class="fas fa-times"></i> Từ chối</button>
                `;
            } else if (course.status === 'approved') {
                actionsRoot.innerHTML = `<button onclick="rejectCourse(${course.id})" class="admin-btn admin-btn-ghost"><i class="fas fa-undo"></i> Gỡ xuống (Draft)</button>`;
            }

            // 2. Fetch Curriculum
            const currRes = await window.api.get(`/courses/${courseId}/curriculum`);
            const chapters = currRes.data;
            const curriculumRoot = document.getElementById('curriculum-container');

            if (!chapters || chapters.length === 0) {
                curriculumRoot.innerHTML = '<div style="padding:1rem; opacity:0.4; font-size:0.85rem; text-align:center;">Khóa học này chưa có nội dung</div>';
                return;
            }

            curriculumRoot.innerHTML = chapters.map((ch, idx) => `
                <div class="chapter-preview">
                    <div class="chapter-preview-header">
                        <span>Chương ${idx + 1}: ${ch.title}</span>
                        <i class="fas fa-chevron-down" style="font-size:0.7rem; opacity:0.3;"></i>
                    </div>
                    <div class="lesson-list">
                        ${(ch.lessons || []).map(les => `
                            <div class="lesson-item" data-lesson-id="${les.id}" onclick="previewLesson(${les.id}, ${courseId})">
                                <i class="fas ${les.content_type === 'video' ? 'fa-play-circle' : 'fa-file-alt'}" style="opacity:0.4;"></i>
                                <span style="flex:1">${les.title}</span>
                                ${les.is_free ? '<span style="font-size:0.6rem; color:#10b981; font-weight:900;">FREE</span>' : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');

        } catch (e) {
            console.error(e);
            App.showToast('Lỗi hệ thống khi tải preview', 'error');
        }
    }

    async function previewLesson(lessonId, courseId) {
        document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
        const target = document.querySelector(`[data-lesson-id="${lessonId}"]`);
        if (target) target.classList.add('active');

        try {
            const res = await window.api.get(`/lessons/${lessonId}`);
            const lesson = res.data;

            document.getElementById('active-lesson-title').innerText = lesson.title;

            // Handle Player
            const playerWrap = document.getElementById('player-container');
            const videoRoot = document.getElementById('video-root');
            
            if (lesson.content_type === 'video') {
                playerWrap.style.display = 'block';
                if (lesson.video_filename) {
                    try {
                        const tokenRes = await window.api.get(`/video/token/${lessonId}?course_id=${courseId}`);
                        videoRoot.innerHTML = `
                            <video controls controlsList="nodownload" style="width:100%; height:100%; object-fit:cover;">
                                <source src="${tokenRes.data.stream_url}" type="video/mp4">
                            </video>`;
                    } catch (e) {
                        videoRoot.innerHTML = `<div style="padding:2rem; text-align:center; opacity:0.5;"><i class="fas fa-lock" style="font-size:2rem; margin-bottom:1rem;"></i><br>${e.message}</div>`;
                    }
                } else if (lesson.video_url) {
                    videoRoot.innerHTML = `<iframe src="${lesson.video_url}" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>`;
                } else {
                    videoRoot.innerHTML = '<div style="padding:2rem; opacity:0.5;">Video chưa được tải lên</div>';
                }
            } else {
                playerWrap.style.display = 'none';
            }

            // Handle Text Content
            const bodyRoot = document.getElementById('lesson-body-content');
            if (lesson.content) {
                bodyRoot.innerHTML = `<div class="ql-snow"><div class="ql-editor">${lesson.content}</div></div>`;
            } else {
                bodyRoot.innerHTML = '<p style="opacity:0.4;">Không có nội dung văn bản cho bài học này.</p>';
            }

            // Handle Quiz
            const quizRoot = document.getElementById('quiz-root');
            try {
                const quizRes = await window.api.get(`/teacher/lessons/${lessonId}/quiz`);
                if (quizRes.data && quizRes.data.id) {
                    const quiz = quizRes.data;
                    let qhtml = `
                        <div style="background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1); border-radius:20px; padding:2rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; color:#10b981;">
                                <i class="fas fa-question-circle"></i>
                                <h4 style="margin:0;">Bản xem trước Quiz: ${quiz.title}</h4>
                            </div>`;
                    
                    (quiz.questions || []).forEach((q, i) => {
                        qhtml += `
                            <div style="margin-bottom:1.5rem; padding-left:1rem; border-left:2px solid rgba(255,255,255,0.05);">
                                <div style="font-weight:700; margin-bottom:1rem;">Câu ${i+1}: ${q.question}</div>
                                <div style="display:grid; gap:0.5rem;">
                                    ${(q.answers || []).map(a => `
                                        <div style="padding:0.75rem 1rem; background:${a.is_correct ? 'rgba(16,185,129,0.1)' : 'rgba(255,255,255,0.02)'}; border-radius:10px; font-size:0.9rem; border:1px solid ${a.is_correct ? 'rgba(16,185,129,0.2)' : 'transparent'}; display:flex; align-items:center; gap:0.75rem;">
                                            <i class="fas ${a.is_correct ? 'fa-check-circle' : 'fa-circle'}" style="color:${a.is_correct ? '#10b981' : 'rgba(255,255,255,0.2)'};"></i>
                                            ${a.answer_text}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>`;
                    });
                    qhtml += `</div>`;
                    quizRoot.innerHTML = qhtml;
                } else {
                    quizRoot.innerHTML = '';
                }
            } catch (e) { quizRoot.innerHTML = ''; }

        } catch (e) {
            console.error(e);
            App.showToast('Không thể tải chi tiết bài học', 'error');
        }
    }

    async function approveCourse(id) {
        if (!confirm('Bạn có chắc chắn muốn PHÊ DUYỆT khóa học này?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/approve`);
            App.showToast('Khóa học đã được duyệt thành công', 'success');
            setTimeout(() => window.location.href = '/admin/courses.php', 1000);
        } catch (e) { App.showToast(e.message, 'error'); }
    }

    async function rejectCourse(id) {
        if (!confirm('Bạn có chắc muốn TỪ CHỐI/GỠ khóa học này?')) return;
        try {
            await window.api.put(`/admin/courses/${id}/reject`);
            App.showToast('Khóa học đã chuyển về trạng thái Bản nháp', 'success');
            setTimeout(() => window.location.href = '/admin/courses.php', 1000);
        } catch (e) { App.showToast(e.message, 'error'); }
    }
</script>

<?php 
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php'; 
?>
