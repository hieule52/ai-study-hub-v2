<?php
$pageTitle = 'Đang Học - AI Study Hub';
$actor = 'student';
$noSidebar = true;
$footerMode = 'none';
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/student/learning.css?v=' . time() . '">
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="learning-layout">

        <!-- Left: Video Area -->
        <div class="main-player">
            <!-- Go Back nav -->
            <div class="player-nav">
                <a href="/student/dashboard" class="btn btn-ghost player-nav-btn" data-i18n="lrn_back_home">&larr; Dashboard</a>
                <span class="player-course-title" id="course_title_span">...</span>
                <button class="btn btn-ghost curriculum-toggle-btn" onclick="toggleCurriculum()" style="display: none; align-items: center; gap: 6px; border-radius: 100px; font-size: 0.85rem; border: 1px solid var(--glass-border); padding: 0.4rem 1rem; color: #fff; background: rgba(0,0,0,0.4); backdrop-filter: blur(12px); cursor: pointer;">
                    <i class="fas fa-list"></i> <span data-i18n="lrn_curriculum">Bài học</span>
                </button>
            </div>

            <!-- Video Player -->
            <div class="video-wrapper" id="video_wrapper" style="display:none;">
                <div style="text-align: center;">
                    <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🎬</div>
                    <h2 class="text-secondary" id="video_placeholder" data-i18n="lrn_video_placeholder">Chọn một bài học để bắt đầu xem video</h2>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="flex justify-between items-center mb-6">
                    <h1 id="lesson_title" style="font-size:clamp(1.5rem,2.5vw,2rem);font-weight:800;letter-spacing:-0.03em;">...</h1>
                    <button class="btn btn-outline" id="btn_mark_complete" style="display:none;border-radius:100px;font-size:0.82rem;color:var(--success);border-color:rgba(110,231,183,0.3);white-space:nowrap;" onclick="markComplete()" data-i18n="lrn_mark_complete">✅ Đánh dấu Đã Học</button>
                </div>

                <!-- Learning Objectives Container -->
                <div id="objectives_container"></div>
                
                <div id="lesson_content">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem; opacity: 0.7;">
                        <span>👉</span> <span data-i18n="lrn_select_hint">Chọn một bài học ở danh mục bên phải để bắt đầu.</span>
                    </div>
                </div>

                <!-- Khu vực Quiz -->
                <div id="quiz_area"></div>
            </div>
        </div>

        <!-- Right: Curriculum Sidebar -->
        <div class="curriculum-sidebar">
            <div class="sidebar-header">
                <div>
                    <p style="font-size:0.65rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.3);margin-bottom:0.5rem;" data-i18n="lrn_curriculum">Nội Dung Khóa Học</p>
                    <div id="curriculum-progress" style="font-size:0.8rem;color:var(--success);font-weight:600;display:flex;align-items:center;gap:0.5rem;">
                        <span>Tiến độ: --</span>
                    </div>
                </div>
                <button id="btn_review_course" onclick="showReviewModal()" style="display:none; padding:0.4rem 1rem; font-size:0.75rem; border-radius:100px; color:var(--warning); border-color:rgba(251,191,36,0.3);" class="btn btn-outline">⭐ <span data-i18n="lrn_btn_rate">Đánh giá</span></button>
            </div>
            <div class="sidebar-content" id="curriculumList">
                <div class="p-4 text-center text-muted" style="font-size:0.875rem;" data-i18n="lrn_loading_curriculum">Đang tải giáo trình...</div>
            </div>
        </div>

    </div>

    <!-- AI Chat Popup & Button -->
    <button class="ai-chat-btn" onclick="toggleAIChat()" title="Gọi trợ lý AI">🤖</button>

    <div class="ai-popup" id="aiPopup">
        <!-- AI Header -->
        <div class="ai-header">
            <div class="ai-header-label">
                <div style="width:30px;height:30px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;">🧠</div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;line-height:1;">AI Tutor</div>
                    <div id="ai_tutor_lesson_title" style="font-size:0.65rem;color:rgba(255,255,255,0.5);line-height:1.2;margin:2px 0;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Đang tải bài học...</div>
                    <div style="font-size:0.68rem;color:rgba(240,240,244,0.45);display:flex;align-items:center;gap:0.3rem;margin-top:2px;">
                        <div class="ai-online-dot"></div> <span data-i18n="lrn_ai_ready">Sẵn sàng hỗ trợ</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <span class="badge" style="font-size:0.62rem;">Contextual AI</span>
            </div>
        </div>

        <!-- AI Messages Body -->
        <div class="ai-messages" id="chatBox">
            <div class="msg bot" data-i18n="lrn_ai_welcome">👋 Chào bạn! Tôi là AI Tutor của bạn. Hỏi tôi bất cứ điều gì về nội dung bài học này nhé!</div>
        </div>

        <!-- AI Input -->
        <form class="ai-input-area" id="chatForm">
            <input type="text" id="chatInput" placeholder="Hỏi AI về nội dung bài..." data-i18n="aichat_input_placeholder" required>
            <button type="submit" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:#f0f0f4;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(10px);">
        <div class="review-modal-card" style="background: rgba(23, 23, 23, 0.95); padding: 2.5rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); width: 90%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); text-align: center;">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">🏆</div>
            <h3 style="margin-bottom: 0.5rem; color: #fff; font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em;" data-i18n="lrn_review_modal_title">Khóa học Hoàn tất!</h3>
            <p style="color: rgba(255,255,255,0.6); margin-bottom: 2rem; font-size: 0.95rem;" data-i18n="lrn_review_modal_subtitle">Chúc mừng bạn đã chinh phục thành công khóa học này. Hãy chia sẻ cảm nhận của bạn nhé!</p>
            
            <form id="reviewForm" onsubmit="submitReview(event)">
                <input type="hidden" id="reviewRating" value="5">
                
                <div class="star-rating-wrapper" style="display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 2rem;">
                    <span class="star-item active" data-value="1" onclick="setRating(1)">★</span>
                    <span class="star-item active" data-value="2" onclick="setRating(2)">★</span>
                    <span class="star-item active" data-value="3" onclick="setRating(3)">★</span>
                    <span class="star-item active" data-value="4" onclick="setRating(4)">★</span>
                    <span class="star-item active" data-value="5" onclick="setRating(5)">★</span>
                </div>

                <div style="margin-bottom: 2rem; text-align: left;">
                    <label style="display: block; margin-bottom: 0.75rem; color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;" data-i18n="lrn_review_label">Nhận xét của bạn</label>
                    <textarea id="reviewComment" class="form-control" rows="4" placeholder="Bạn thấy khóa học này thế nào? Nội dung có hữu ích không?..." data-i18n="lrn_review_placeholder" style="background: rgba(255,255,255,0.03); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 1rem; width: 100%; resize: none; outline: none; transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.background='rgba(255,255,255,0.05)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.03)'"></textarea>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" class="btn btn-ghost" style="flex: 1; border-radius: 12px;" onclick="skipReview()" data-i18n="lrn_review_skip">Bỏ qua</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitReview" style="flex: 2; border-radius: 12px; font-weight: 600; padding: 0.8rem;" data-i18n="lrn_review_submit">Gửi Đánh Giá</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .star-item {
            font-size: 2.5rem;
            color: rgba(255,255,255,0.15);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .star-item:hover {
            transform: scale(1.2);
            color: var(--warning);
        }
        .star-item.active {
            color: #fbbf24;
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.4);
        }
    </style>

<?php ob_start(); ?>
<script>
        const user = App.requireAuth();

        // === ROLE GUARD: Block admin/teacher from student learning flow ===
        if (user && user.role === 'admin') {
            const _cid = <?= json_encode($_GET['course_id'] ?? null) ?> ?? new URLSearchParams(window.location.search).get('course_id');
            window.location.replace('/admin/preview/' + _cid);
            throw new Error('Redirecting admin to preview mode');
        }
        if (user && user.role === 'teacher') {
            const _cid = <?= json_encode($_GET['course_id'] ?? null) ?> ?? new URLSearchParams(window.location.search).get('course_id');
            window.location.replace('/teacher/course-builder/' + _cid);
            throw new Error('Redirecting teacher to course builder');
        }

        const urlParams = new URLSearchParams(window.location.search);
        // Support clean URL (/student/learning/8) and legacy (?course_id=8)
        const courseId = <?= json_encode($_GET['course_id'] ?? null) ?> ?? urlParams.get('course_id');
        let currentLessonId = null;
        let currentQuizId = null;

        document.addEventListener('DOMContentLoaded', async () => {
            if (!courseId || courseId === '0' || courseId === 'null') {
                App.showToast('Không tìm thấy ID khóa học ở URL. Đang chuyển hướng...', 'error');
                setTimeout(() => window.location.replace('/student/courses'), 1500);
                return;
            }
            await loadCurriculum();
        });

        async function loadCurriculum() {
            const sidebar = document.getElementById('curriculumList');
            // Reset sidebar ngay lập tức
            sidebar.innerHTML = '<div class="p-4 text-center" style="opacity:0.5;font-size:0.85rem;">⏳ Đang tải...</div>';

            try {
                // Fetch course info
                const courseRes = await window.api.get(`/courses/${courseId}`);
                document.getElementById('course_title_span').innerText = courseRes.data.title;

                // Cấu trúc giáo trình
                const res = await window.api.get(`/courses/${courseId}/curriculum`);
                const chapters = res.data;
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
                            lesDiv.className = 'lesson-item' + (lesson.is_locked ? ' locked' : '') + (lesson.is_completed ? ' completed' : '');
                            lesDiv.id = `nav-lesson-${lesson.id}`;

                            // Icon: lock > completed check > content type
                            let icon;
                            if (lesson.is_locked) {
                                icon = '<i class="fas fa-lock" style="color:rgba(255,255,255,0.25);font-size:0.8rem;"></i>';
                            } else if (lesson.is_completed) {
                                icon = '<i class="fas fa-check-circle" style="color:var(--success);"></i>';
                            } else {
                                icon = lesson.content_type === 'quiz' ? '📝' : '🎬';
                            }

                            // Progress bar for in-progress lessons (>0% but not completed)
                            const prog = lesson.content_type === 'video' ? (lesson.video_progress||0) : (lesson.text_progress||0);
                            const progressBar = (!lesson.is_completed && prog > 0)
                                ? `<div style="height:2px;background:rgba(255,255,255,0.08);border-radius:1px;margin-top:4px;"><div style="height:2px;width:${prog}%;background:var(--primary);border-radius:1px;transition:width 0.4s;"></div></div>`
                                : '';

                            lesDiv.innerHTML = `
                                <span class="lesson-icon">${icon}</span>
                                <div style="flex:1;min-width:0;">
                                    <span class="lesson-name" style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;${lesson.is_locked?'color:rgba(255,255,255,0.3);':''}"
                                    >${lesson.title}</span>
                                    ${progressBar}
                                </div>
                                ${lesson.is_locked ? '<span style="font-size:0.65rem;color:rgba(255,255,255,0.2);white-space:nowrap;">🔒 Chưa mở</span>' : ''}
                            `;

                            if (lesson.is_locked) {
                                lesDiv.style.cursor = 'not-allowed';
                                lesDiv.onclick = () => {
                                    App.showToast('Hãy hoàn thành bài học trước để mở khoá bài này.', 'warning');
                                };
                            } else {
                                lesDiv.onclick = () => {
                                    loadLesson(lesson.id);
                                    const cSidebar = document.querySelector('.curriculum-sidebar');
                                    const cOverlay = document.querySelector('.sidebar-overlay');
                                    if (cSidebar && window.innerWidth <= 992) {
                                        cSidebar.classList.remove('open');
                                        if (cOverlay) {
                                            cOverlay.classList.remove('active');
                                            cOverlay.onclick = typeof toggleSidebar !== 'undefined' ? toggleSidebar : null;
                                        }
                                    }
                                };
                            }
                            sidebar.appendChild(lesDiv);
                        });
                    }
                });

                // Count completed
                const completedCount = document.querySelectorAll('.lesson-item.completed').length;
                const progressPct = totalLessons > 0 ? Math.round(completedCount / totalLessons * 100) : 0;
                document.getElementById('curriculum-progress').innerHTML =
                    `<span style="color:var(--success);">${progressPct}%</span>
                     <span style="color:rgba(255,255,255,0.4);font-size:0.75rem;"> — ${completedCount}/${totalLessons} bài học</span>`;
                if (window.I18n) window.I18n.render();

                // Check progress and existing review to show review button / redirect
                try {
                    const enrolledRes = await window.api.get('/student/courses');
                    const myCourse = enrolledRes.data.find(c => c.id == courseId);
                    
                    if (myCourse && myCourse.progress_percent === 100) {
                        const reviewRes = await window.api.get(`/student/courses/${courseId}/my-review`);
                        const myReview = reviewRes.data;

                        const btnReview = document.getElementById('btn_review_course');
                        btnReview.style.display = 'block';

                        const hasSkipped = localStorage.getItem(`skip_review_${courseId}`) === 'true';

                        if ((myReview && myReview.rating) || hasSkipped) {
                            // Redirect to course completed page immediately if completed and reviewed (or skipped)
                            window.location.replace(`/student/course-completed/${courseId}`);
                            return;
                        } else {
                            // If completed but not reviewed, show the evaluation popup modal immediately
                            showReviewModal();
                        }
                    }
                } catch(e) { console.log(e); }

                // Auto-load first allowed lesson on initial load if not set
                if (!currentLessonId) {
                    let activeLessonId = null;
                    let firstUnlocked = null;
                    let firstIncompleteUnlocked = null;
                    chapters.forEach(chap => {
                        if (chap.lessons && chap.lessons.length > 0) {
                            chap.lessons.forEach(lesson => {
                                if (!lesson.is_locked) {
                                    if (!firstUnlocked) firstUnlocked = lesson.id;
                                    if (!lesson.is_completed && !firstIncompleteUnlocked) {
                                        firstIncompleteUnlocked = lesson.id;
                                    }
                                }
                            });
                        }
                    });
                    activeLessonId = firstIncompleteUnlocked || firstUnlocked;
                    if (activeLessonId) {
                        loadLesson(activeLessonId);
                    } else {
                        // Không có bài học nào có thể học — hiển thị thông báo
                        const allLocked = chapters.every(ch => (ch.lessons||[]).every(l => l.is_locked));
                        if (allLocked && chapters.length > 0) {
                            document.getElementById('lesson_content').innerHTML = `
                                <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;margin-top:3rem;opacity:0.7;">
                                    <div style="font-size:3rem;">🔒</div>
                                    <h3 style="color:rgba(255,255,255,0.8);">Bạn chưa đăng ký khóa học này</h3>
                                    <p style="color:rgba(255,255,255,0.5);text-align:center;max-width:400px;">Vui lòng đăng ký hoặc mua khóa học để bắt đầu học.</p>
                                    <a href="/course/${courseId}" class="btn btn-primary" style="border-radius:100px;padding:0.8rem 2rem;margin-top:0.5rem;">Xem chi tiết khóa học</a>
                                </div>`;
                        } else {
                            document.getElementById('lesson_content').innerHTML = `
                                <div style="display:flex;align-items:center;gap:1rem;margin-top:2rem;opacity:0.7;">
                                    <span>👉</span> <span>Chọn một bài học ở danh mục bên phải để bắt đầu.</span>
                                </div>`;
                        }
                    }
                }

            } catch (err) {
                // 403 = chưa đăng ký hoặc bài bị khoá
                if (err.httpStatus === 403) {
                    if (err.responseData && err.responseData.redirect_lesson_id) {
                        const redirectId = err.responseData.redirect_lesson_id;
                        App.showToast('🔒 ' + (err.responseData.message || 'Bài học này chưa được mở khoá. Đang chuyển hướng...'), 'warning');
                        setTimeout(() => loadLesson(redirectId), 800);
                    } else {
                        // Chưa enrolled
                        document.getElementById('curriculumList').innerHTML = `<div class="p-4 text-center" style="color:rgba(255,255,255,0.5);font-size:0.85rem;">🔒 Bạn chưa đăng ký khóa học này.</div>`;
                        document.getElementById('lesson_content').innerHTML = `
                            <div style="display:flex;flex-direction:column;align-items:center;gap:1.5rem;margin-top:4rem;text-align:center;">
                                <div style="font-size:4rem;">🔒</div>
                                <h2 style="color:#fff;font-weight:700;">Bạn chưa có quyền truy cập</h2>
                                <p style="color:rgba(255,255,255,0.5);max-width:420px;line-height:1.7;">Vui lòng đăng ký hoặc mua khóa học này để bắt đầu học.</p>
                                <a href="/course/${courseId}" class="btn btn-primary" style="border-radius:100px;padding:0.85rem 2.5rem;font-weight:700;font-size:1rem;">📖 Xem trang giới thiệu khóa học</a>
                            </div>`;
                    }
                } else if (err.httpStatus === 404) {
                    sidebar.innerHTML = `<div class="p-4 text-center" style="color:rgba(255,255,255,0.4);font-size:0.85rem;">😕 Không tìm thấy khóa học</div>`;
                    document.getElementById('lesson_content').innerHTML = `
                        <div style="display:flex;flex-direction:column;align-items:center;gap:1.5rem;margin-top:4rem;text-align:center;">
                            <div style="font-size:4rem;">😕</div>
                            <h2 style="color:#fff;">Không tìm thấy khóa học</h2>
                            <a href="/student/courses" class="btn btn-outline" style="border-radius:100px;padding:0.75rem 2rem;">← Quay lại khóa học của tôi</a>
                        </div>`;
                } else {
                    sidebar.innerHTML = `<div class="p-4 text-center" style="color:rgba(255,255,255,0.4);font-size:0.85rem;">⚠️ ${err.message || 'Lỗi tải giáo trình'}</div>`;
                    App.showToast(err.message || 'Lỗi tải giáo trình', 'error');
                }
            }
        }

        async function loadLesson(lessonId) {
            // Force sync progress of previous lesson before switching
            if (currentLessonId) {
                try {
                    await syncCurrentProgress();
                } catch(e) { console.error('Error syncing before loadLesson:', e); }
            }

            currentLessonId = lessonId;
            currentQuizId = null;
            window._confirmReadChecked = false;

            // Update UI State
            document.querySelectorAll('.lesson-item').forEach(el => {
                el.classList.remove('playing');
                if (el.dataset.originalIcon) {
                    el.querySelector('span').innerText = el.dataset.originalIcon;
                }
            });
            const activeEl = document.getElementById(`nav-lesson-${lessonId}`);
            if (activeEl) {
                activeEl.classList.add('playing');
                const originalIcon = activeEl.querySelector('span').innerText;
                activeEl.querySelector('span').innerText = '▶️';
                activeEl.dataset.originalIcon = originalIcon;
            }

            try {
                const res = await window.api.get(`/lessons/${lessonId}`);
                const lesson = res.data;

                document.getElementById('lesson_title').innerText = lesson.title;
                const aiTutorLessonTitle = document.getElementById('ai_tutor_lesson_title');
                if (aiTutorLessonTitle) {
                    aiTutorLessonTitle.innerText = `Đang học: ${lesson.title}`;
                    aiTutorLessonTitle.title = lesson.title;
                }

                // Show objectives if any
                const objContainer = document.getElementById('objectives_container');
                if (lesson.objectives) {
                    const tags = lesson.objectives.split(',').map(t => `<span class="objective-tag">${t.trim()}</span>`).join('');
                    const objTitle = window.I18n ? window.I18n.get('lrn_what_you_will_learn') : 'Bạn sẽ học được gì:';
                    objContainer.innerHTML = `
                        <div class="objectives-card">
                            <div class="objectives-icon">🎯</div>
                            <div class="objectives-list">
                                <div class="objectives-title">${objTitle}</div>
                                <div class="objectives-items">${tags}</div>
                            </div>
                        </div>`;
                } else {
                    objContainer.innerHTML = '';
                }

                // Render HTML Content
                const lessonContentEl = document.getElementById('lesson_content');
                if (lesson.content) {
                    const checkboxHtml = (!lesson.is_completed && lesson.content_type === 'text') ? `
                        <div class="reading-confirmation-wrapper" style="margin-top: 2.5rem; padding: 1.5rem; background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.15); border-radius: 16px; display: flex; flex-direction: column; gap: 15px; align-items: flex-start; backdrop-filter: blur(10px);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="chk_confirm_read" style="width: 20px; height: 20px; cursor: pointer;" onchange="handleReadConfirmChange(this)">
                                <label for="chk_confirm_read" style="color: rgba(255,255,255,0.8); font-size: 0.95rem; cursor: pointer; user-select: none; font-weight: 500;">
                                    Tôi xác nhận đã đọc và hiểu rõ nội dung tài liệu này.
                                </label>
                            </div>
                            <button class="btn btn-primary" id="btn_mark_complete_bottom" style="border-radius: 100px; padding: 0.6rem 1.8rem; font-size: 0.85rem; opacity: 0.5;" disabled onclick="markComplete()">
                                ✅ Đánh dấu Đã Học
                            </button>
                        </div>
                    ` : '';

                    lessonContentEl.innerHTML = `
                        <div class="ql-snow">
                            <div class="ql-editor lesson-content-wrapper">${lesson.content}</div>
                        </div>
                        ${checkboxHtml}`;
                    setTimeout(() => {
                        document.querySelectorAll('pre').forEach(b => hljs.highlightElement(b));
                    }, 100);
                    // Start text scroll progress tracking
                    if (!lesson.is_completed) startTextProgressTracking(lesson.text_progress || 0);
                } else {
                    lessonContentEl.innerHTML = '<div class="text-muted" data-i18n="lrn_no_desc">Giảng viên chưa cập nhật mô tả chi tiết bài học này.</div>';
                }

                // Completion status button
                if (lesson.is_completed) {
                    updateMarkCompleteButtons({
                        display: 'block',
                        innerHTML: '✅ Đã hoàn thành bài học',
                        className: 'btn btn-outline',
                        color: 'var(--success)',
                        borderColor: 'rgba(110,231,183,0.3)',
                        disabled: true,
                        opacity: '1'
                    });
                } else if (lesson.content_type !== 'quiz') {
                    updateMarkCompleteButtons({
                        display: 'block',
                        innerHTML: '✅ Đánh dấu Đã Học',
                        className: 'btn btn-primary',
                        color: '',
                        borderColor: '',
                        disabled: lesson.content_type === 'text',
                        opacity: lesson.content_type === 'text' ? '0.5' : '1'
                    });
                } else {
                    updateMarkCompleteButtons({
                        display: 'none'
                    });
                }

                // === VIDEO DISPLAY & PROGRESS TRACKING ===
                const videoWrapper = document.getElementById('video_wrapper');
                clearVideoProgressInterval();

                if (lesson.content_type === 'video') {
                    videoWrapper.style.display = 'flex';
                    let videoEl = null;

                    if (lesson.video_filename) {
                        try {
                            videoWrapper.innerHTML = `<div style="text-align:center;"><div style="font-size:3rem;animation:spin 2s linear infinite;">⏳</div><p class="text-secondary mt-2">Đang tải video bảo mật...</p></div>`;
                            const tokenRes = await window.api.get(`/video/token/${lessonId}?course_id=${courseId}`);
                            videoWrapper.innerHTML = `<video id="secureVideoPlayer" controls controlsList="nodownload nofullscreen noremoteplayback" disablePictureInPicture style="width:100%;height:100%;background:#000;" oncontextmenu="return false;"></video>`;
                            videoEl = document.getElementById('secureVideoPlayer');
                            videoEl.src = tokenRes.data.stream_url;
                            videoEl.load();
                            videoEl.addEventListener('keydown', e => {
                                if ((e.ctrlKey||e.metaKey) && ['s','u','j'].includes(e.key.toLowerCase())) e.preventDefault();
                            });
                            videoEl.addEventListener('error', () => {
                                videoWrapper.innerHTML = `<div style="text-align:center;padding:2rem;"><div style="font-size:3rem;opacity:0.5;">⚠️</div><p class="text-secondary">Video không thể phát. Vui lòng tải lại trang.</p></div>`;
                            });
                        } catch(videoErr) {
                            videoWrapper.innerHTML = `<div style="text-align:center;"><div style="font-size:4rem;opacity:0.5;margin-bottom:1rem;">🔒</div><h2 class="text-secondary">Lỗi kết nối</h2><p class="text-muted mt-2">Vui lòng đảm bảo bạn đã đăng ký khóa học này.</p></div>`;
                        }
                    } else if (lesson.video_url) {
                        const url = lesson.video_url;
                        let embedUrl = url;
                        if (url.includes('youtube.com') || url.includes('youtu.be')) {
                            let vid = '';
                            if (url.includes('v=')) vid = url.split('v=')[1].split('&')[0];
                            else if (url.includes('youtu.be/')) vid = url.split('youtu.be/')[1].split('?')[0];
                            if (vid) embedUrl = `https://www.youtube.com/embed/${vid}?rel=0&modestbranding=1&autoplay=1`;
                        } else if (url.includes('vimeo.com')) {
                            const vid = url.split('/').pop().split('?')[0];
                            if (vid) embedUrl = `https://player.vimeo.com/video/${vid}?autoplay=1`;
                        }
                        if (embedUrl !== url || url.includes('embed')) {
                            videoWrapper.innerHTML = `<iframe src="${embedUrl}" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                        } else {
                            videoWrapper.innerHTML = `<video controls autoplay style="width:100%;height:100%;background:#000;"><source src="${url}" type="video/mp4"></video>`;
                            videoEl = videoWrapper.querySelector('video');
                        }
                    } else {
                        videoWrapper.innerHTML = `<div style="text-align:center;"><div style="font-size:4rem;opacity:0.5;margin-bottom:1rem;">🎥</div><h2 class="text-secondary">Trình phát Video sẽ mô phỏng ở đây.</h2></div>`;
                    }

                    // Video progress tracking (every 10s)
                    if (videoEl && !lesson.is_completed) {
                        // Restore saved position
                        if (lesson.video_progress > 0 && lesson.video_progress < 95) {
                            videoEl.addEventListener('loadedmetadata', () => {
                                videoEl.currentTime = Math.floor(videoEl.duration * lesson.video_progress / 100);
                            }, { once: true });
                        }
                        startVideoProgressTracking(videoEl, lesson.video_progress || 0);
                    }
                } else {
                    videoWrapper.style.display = 'none';
                    videoWrapper.innerHTML = '';
                }

                loadQuizData(lessonId);
                await loadAIChatHistory(lessonId, lesson.title);
                if (window.I18n) window.I18n.render();

            } catch (err) {
                // 403 = bài học bị khoá (sequential lock) — tự động snap-back về bài được phép
                if (err.httpStatus === 403 && err.responseData && err.responseData.redirect_lesson_id) {
                    const redirectId = err.responseData.redirect_lesson_id;
                    App.showToast('🔒 ' + (err.responseData.message || 'Bài học này chưa được mở khoá. Đang chuyển hướng...'), 'warning');
                    setTimeout(() => loadLesson(redirectId), 800);
                } else {
                    App.showToast(err.message, 'error');
                }
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
                                <p class="question-text">
                                    <span style="opacity: 0.5;">Q${qIndex + 1}</span>
                                    <span>${q.question}</span>
                                </p>
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
                            answers[key.replace('q_', '')] = parseInt(value);
                        }

                        try {
                            const btnSubmit = e.target.querySelector('button[type="submit"]');
                            btnSubmit.innerHTML = 'Đang chấm điểm...';
                            btnSubmit.disabled = true;

                            const submitRes = await window.api.post(`/quizzes/${currentQuizId}/submit`, { answers });
                            const d = submitRes.data;
                            const passed = d.passed;
                            const passingScore = d.passing_score ?? 80;

                            if (passed) {
                                App.showToast(`🎉 Xuất sắc! Điểm: ${d.score}/${d.total_questions * 10 || 100} — Đạt yêu cầu!`, 'success');
                                btnSubmit.innerHTML = `✅ Điểm: ${d.score} — Đã đạt (>= ${passingScore})`;
                                btnSubmit.style.background = 'var(--success)';
                                btnSubmit.style.border = 'none';
                                // Trigger lesson completion from server (already done in QuizService)
                                // Just refresh sidebar
                                setTimeout(refreshCurriculumProgress, 500);

                                // Reflect mark-complete
                                updateMarkCompleteButtons({
                                    display: 'block',
                                    innerHTML: '✅ Đã hoàn thành bài học',
                                    className: 'btn btn-outline',
                                    color: 'var(--success)',
                                    borderColor: 'rgba(110,231,183,0.3)',
                                    disabled: true,
                                    opacity: '1'
                                });

                                // Course completed?
                                if (d.course_completed) {
                                    setTimeout(() => {
                                        if (d.needs_review) showReviewModal();
                                        else if (d.certificate_issued) showCertificateToast();
                                    }, 800);
                                }
                            } else {
                                App.showToast(`❌ Điểm: ${d.score} — Chưa đạt (cần >= ${passingScore}). Hãy thử lại!`, 'error');
                                btnSubmit.innerHTML = `❌ Điểm: ${d.score} — Thử lại`;
                                btnSubmit.style.background = 'var(--danger)';
                                btnSubmit.disabled = false;
                                setTimeout(() => {
                                    btnSubmit.style.background = '';
                                    btnSubmit.innerHTML = 'Nộp Bài Kiểm Tra';
                                }, 3000);
                            }
                        } catch (err) {
                            App.showToast(err.message, 'error');
                            const btn = e.target.querySelector('button[type="submit"]');
                            btn.innerHTML = 'Nộp Lại';
                            btn.disabled = false;
                        }
                    });
                }
            } catch (error) {
                const errMsg = error.response?.data?.message || error.message || "";
                if (errMsg.includes("80%") || errMsg.includes("nội dung bài học")) {
                    quizArea.innerHTML = `
                        <div class="quiz-locked-card" style="background: rgba(239, 68, 68, 0.05); border: 1px dashed rgba(239, 68, 68, 0.2); border-radius: 16px; padding: 2.5rem; text-align: center; margin-top: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔒</div>
                            <h4 style="color: #ef4444; font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;" data-i18n="lrn_quiz_locked">Thử thách trí tuệ bị khóa</h4>
                            <p style="color: rgba(240, 240, 244, 0.6); font-size: 0.9rem; line-height: 1.5; max-width: 380px; margin: 0 auto;" data-i18n="lrn_quiz_locked_desc">
                                Bạn cần hoàn thành ít nhất 80% nội dung bài học trước khi làm bài kiểm tra.
                            </p>
                        </div>
                    `;
                    if (window.I18n) window.I18n.render();
                } else {
                    quizArea.innerHTML = '';
                }
            }
        }

        // ─── Progress Tracking ──────────────────────────────────
        let _videoPollInterval = null;
        let _videoTimer        = null;
        let _textTimer         = null;
        let _lastVideoProgress = 0;
        let _lastTextProgress  = 0;
        let _videoWatchedTime  = 0;
        let _textReadTime      = 0;
        window._confirmReadChecked = false;

        function updateMarkCompleteButtons(state) {
            const buttons = [
                document.getElementById('btn_mark_complete'),
                document.getElementById('btn_mark_complete_bottom')
            ];
            buttons.forEach(btn => {
                if (!btn) return;
                if (state.display !== undefined) {
                    if (typeof state.display === 'boolean') {
                        btn.style.display = state.display ? 'block' : 'none';
                    } else {
                        btn.style.display = state.display;
                    }
                }
                if (state.innerHTML !== undefined) btn.innerHTML = state.innerHTML;
                if (state.className !== undefined) btn.className = state.className;
                if (state.color !== undefined) btn.style.color = state.color;
                if (state.borderColor !== undefined) btn.style.borderColor = state.borderColor;
                if (state.disabled !== undefined) btn.disabled = state.disabled;
                if (state.opacity !== undefined) btn.style.opacity = state.opacity;
            });
        }

        function clearVideoProgressInterval() {
            if (_videoPollInterval) { clearInterval(_videoPollInterval); _videoPollInterval = null; }
            if (_videoTimer) { clearInterval(_videoTimer); _videoTimer = null; }
            if (_textTimer) { clearInterval(_textTimer); _textTimer = null; }
            if (window._textScrollHandler) {
                window.removeEventListener('scroll', window._textScrollHandler);
                window._textScrollHandler = null;
            }
        }

        async function syncCurrentProgress() {
            if (!currentLessonId) return;
            
            const videoEl = document.getElementById('secureVideoPlayer') || document.querySelector('#video_wrapper video');
            let videoProgress = _lastVideoProgress;
            if (videoEl && videoEl.duration) {
                videoProgress = Math.min(100, Math.round(_videoWatchedTime / videoEl.duration * 100));
            }

            let textProgress = _lastTextProgress;
            if (window._confirmReadChecked) {
                textProgress = 100;
            } else {
                const contentEl = document.getElementById('lesson_content');
                if (contentEl && _textTimer) {
                    const text = contentEl.innerText || "";
                    const words = text.trim().split(/\s+/).filter(w => w.length > 0);
                    const wordCount = words.length;
                    const requiredTime = Math.max(5, Math.min(20, Math.round(wordCount / 10)));
                    
                    const timePct = Math.min(100, Math.round(_textReadTime / requiredTime * 100));
                    
                    const rect = contentEl.getBoundingClientRect();
                    const total = contentEl.offsetHeight;
                    let scrollPct = 0;
                    if (total > 0) {
                        const scrolledPast = window.scrollY + window.innerHeight - (window.scrollY + rect.top);
                        scrollPct = Math.min(100, Math.round(Math.max(scrolledPast, 0) / total * 100));
                    }
                    textProgress = Math.min(timePct, scrollPct);
                }
            }

            if (videoProgress > _lastVideoProgress || textProgress > _lastTextProgress) {
                _lastVideoProgress = videoProgress;
                _lastTextProgress = textProgress;
                try {
                    await window.api.post(`/lessons/${currentLessonId}/progress`, {
                        video_progress: videoProgress,
                        text_progress: textProgress
                    });
                } catch(e) { console.error('Failed to sync progress:', e); }
            }
        }

        function startVideoProgressTracking(videoEl, savedProgress) {
            _lastVideoProgress = savedProgress;
            
            const initWatchedTime = () => {
                if (videoEl.duration) {
                    _videoWatchedTime = (savedProgress / 100) * videoEl.duration;
                }
            };
            
            if (videoEl.readyState >= 1) {
                initWatchedTime();
            } else {
                videoEl.addEventListener('loadedmetadata', initWatchedTime, { once: true });
            }

            clearVideoProgressInterval();

            // Track play time every second and poll progress to server every 10s
            let lastPollTime = Date.now();
            _videoTimer = setInterval(async () => {
                if (!videoEl || !videoEl.duration) return;
                
                // Only increment if playing, not seeking, and page is visible
                if (!videoEl.paused && !videoEl.seeking && !document.hidden) {
                    _videoWatchedTime += 1;
                }
                
                // Poll progress to server every 10 seconds if progress has increased
                const now = Date.now();
                if (now - lastPollTime >= 10000) {
                    lastPollTime = now;
                    const pct = Math.min(100, Math.round(_videoWatchedTime / videoEl.duration * 100));
                    if (pct > _lastVideoProgress) {
                        _lastVideoProgress = pct;
                        try {
                            const r = await window.api.post(`/lessons/${currentLessonId}/progress`, {
                                video_progress: pct, text_progress: _lastTextProgress
                            });
                            if (r.data.just_completed) handleLessonJustCompleted(r.data);
                        } catch(e) { /* silent */ }
                    }
                }
            }, 1000);

            // Sync progress immediately on pause
            videoEl.addEventListener('pause', async () => {
                await syncCurrentProgress();
            });

            // Send 100% on ended only if actually watched >= 80% of video
            videoEl.addEventListener('ended', async () => {
                const pct = Math.min(100, Math.round(_videoWatchedTime / videoEl.duration * 100));
                if (pct >= 80) {
                    _lastVideoProgress = 100;
                    try {
                        const r = await window.api.post(`/lessons/${currentLessonId}/progress`, {
                            video_progress: 100, text_progress: _lastTextProgress
                        });
                        if (r.data.just_completed) handleLessonJustCompleted(r.data);
                    } catch(e) { /* silent */ }
                }
            });
        }

        function startTextProgressTracking(savedProgress) {
            _lastTextProgress = savedProgress;
            const contentEl = document.getElementById('lesson_content');
            if (!contentEl) return;

            // Estimate required reading time (assuming 3 words per second, min 15s, max 120s)
            const text = contentEl.innerText || "";
            const words = text.trim().split(/\s+/).filter(w => w.length > 0);
            const wordCount = words.length;
            const requiredTime = Math.max(5, Math.min(20, Math.round(wordCount / 10)));

            // Initialize read time from saved progress
            _textReadTime = (savedProgress / 100) * requiredTime;

            clearVideoProgressInterval();

            const calculateScrollPct = () => {
                const rect = contentEl.getBoundingClientRect();
                const total = contentEl.offsetHeight;
                if (total <= 0) return 0;
                const scrolledPast = window.scrollY + window.innerHeight - (window.scrollY + rect.top);
                return Math.min(100, Math.round(Math.max(scrolledPast, 0) / total * 100));
            };

            const sendProgress = async (pct) => {
                if (pct <= _lastTextProgress) return;
                _lastTextProgress = pct;
                try {
                    const r = await window.api.post(`/lessons/${currentLessonId}/progress`, {
                        video_progress: _lastVideoProgress, text_progress: pct
                    });
                    if (r.data.just_completed) handleLessonJustCompleted(r.data);
                } catch(e) { /* silent */ }
            };

            // Every second, increment read time and compute progress (requires scroll + time)
            _textTimer = setInterval(() => {
                if (document.hidden) return;
                _textReadTime += 1;
                
                const timePct = Math.min(100, Math.round(_textReadTime / requiredTime * 100));
                const scrollPct = calculateScrollPct();
                const combinedPct = Math.min(timePct, scrollPct);
                
                if (combinedPct > _lastTextProgress) {
                    if (combinedPct - _lastTextProgress >= 5 || combinedPct === 100 || (_textReadTime % 10 === 0)) {
                        sendProgress(combinedPct);
                    }
                }
            }, 1000);

            // Scroll listener update
            window._textScrollHandler = () => {
                const scrollPct = calculateScrollPct();
                const timePct = Math.min(100, Math.round(_textReadTime / requiredTime * 100));
                const combinedPct = Math.min(timePct, scrollPct);
                if (combinedPct > _lastTextProgress) {
                    sendProgress(combinedPct);
                }
            };
            window.addEventListener('scroll', window._textScrollHandler);
        }

        // Called when backend confirms lesson just became completed
        function handleLessonJustCompleted(data) {
            clearVideoProgressInterval();
            if (window._textScrollHandler) {
                window.removeEventListener('scroll', window._textScrollHandler);
            }

            // Update sidebar icon
            const navEl = document.getElementById(`nav-lesson-${currentLessonId}`);
            if (navEl) {
                navEl.classList.add('completed');
                navEl.classList.remove('locked');
                const iconEl = navEl.querySelector('.lesson-icon');
                if (iconEl) iconEl.innerHTML = '<i class="fas fa-check-circle" style="color:var(--success);"></i>';
            }

            // Update mark-complete button
            updateMarkCompleteButtons({
                display: 'block',
                innerHTML: '✅ Đã hoàn thành bài học',
                className: 'btn btn-outline',
                color: 'var(--success)',
                borderColor: 'rgba(110,231,183,0.3)',
                disabled: true,
                opacity: '1'
            });

            // Unlock next lesson in sidebar
            refreshCurriculumProgress();

            // Course completed?
            if (data.course_completed) {
                setTimeout(() => {
                    if (data.needs_review) {
                        showReviewModal();
                    } else if (data.certificate_issued) {
                        showCertificateToast();
                    }
                }, 800);
            }
        }

        async function markComplete() {
            if (!currentLessonId) return;
            updateMarkCompleteButtons({ disabled: true });
            try {
                // Force sync progress immediately before trying to mark complete
                await syncCurrentProgress();
                const res = await window.api.post(`/lessons/${currentLessonId}/complete`, {});
                handleLessonJustCompleted(res.data || {});
            } catch (e) {
                console.error(e);
                App.showToast(e.message || 'Không thể đánh dấu hoàn thành bài học. Vui lòng kiểm tra lại điều kiện hoàn thành.', 'error');
                const chkConfirm = document.getElementById('chk_confirm_read');
                const isChecked = chkConfirm && chkConfirm.checked;
                updateMarkCompleteButtons({
                    disabled: !isChecked,
                    opacity: isChecked ? '1' : '0.5'
                });
            }
        }

        async function handleReadConfirmChange(checkboxEl) {
            if (checkboxEl.checked) {
                window._confirmReadChecked = true;
                clearVideoProgressInterval();
                updateMarkCompleteButtons({
                    disabled: false,
                    opacity: '1'
                });
                App.showToast('📖 Đã xác nhận đọc xong tài liệu. Hãy nhấn "Đánh dấu Đã Học" để hoàn thành.', 'success');
            } else {
                window._confirmReadChecked = false;
                startTextProgressTracking(0);
                updateMarkCompleteButtons({
                    disabled: true,
                    opacity: '0.5'
                });
            }
        }

        // Auto sync on page hide/unload or visibility change
        window.addEventListener('beforeunload', () => {
            syncCurrentProgress();
        });
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                syncCurrentProgress();
            }
        });

        async function refreshCurriculumProgress() {
            try {
                const res = await window.api.get(`/courses/${courseId}/curriculum`);
                const chapters = res.data;
                let total = 0, completed = 0;
                chapters.forEach(ch => (ch.lessons || []).forEach(l => {
                    total++;
                    if (l.is_completed) completed++;
                    const el = document.getElementById(`nav-lesson-${l.id}`);
                    if (!el) return;

                    if (l.is_locked) {
                        // Khóa bài học
                        el.classList.add('locked');
                        el.classList.remove('completed', 'playing');
                        el.style.cursor = 'not-allowed';
                        el.onclick = () => App.showToast('Hãy hoàn thành bài học trước để mở khóa bài này.', 'warning');
                        const iconEl = el.querySelector('.lesson-icon');
                        if (iconEl) iconEl.innerHTML = '<i class="fas fa-lock" style="color:rgba(255,255,255,0.25);font-size:0.8rem;"></i>';
                        // Cập nhật text làm mờ
                        const nameEl = el.querySelector('.lesson-name');
                        if (nameEl) nameEl.style.color = 'rgba(255,255,255,0.3)';
                    } else {
                        // Mở khóa bài học
                        el.classList.remove('locked');
                        el.style.cursor = 'pointer';
                        el.onclick = () => {
                            loadLesson(l.id);
                            // Đóng sidebar trên mobile
                            const cSidebar = document.querySelector('.curriculum-sidebar');
                            const cOverlay = document.querySelector('.sidebar-overlay');
                            if (cSidebar && window.innerWidth <= 992) {
                                cSidebar.classList.remove('open');
                                if (cOverlay) cOverlay.classList.remove('active');
                            }
                        };
                        const nameEl = el.querySelector('.lesson-name');
                        if (nameEl) nameEl.style.color = '';

                        if (l.is_completed) {
                            el.classList.add('completed');
                            const iconEl = el.querySelector('.lesson-icon');
                            if (iconEl) iconEl.innerHTML = '<i class="fas fa-check-circle" style="color:var(--success);"></i>';
                        } else {
                            // Biểu tượng đúng với loại bài
                            const iconEl = el.querySelector('.lesson-icon');
                            if (iconEl && !el.classList.contains('playing')) {
                                iconEl.innerHTML = l.content_type === 'quiz' ? '📝' : '🎬';
                            }
                        }
                    }
                }));
                const pct = total ? Math.round(completed/total*100) : 0;
                document.getElementById('curriculum-progress').innerHTML =
                    `<span style="color:var(--success);">${pct}%</span>
                     <span style="color:rgba(255,255,255,0.4);font-size:0.75rem;"> — ${completed}/${total} bài học</span>`;
            } catch(e) { /* silent */ }
        }

        function showCertificateToast() {
            App.showToast('🎓 Chứng chỉ của bạn đã được cấp! Xem tại trang Chứng chỉ.', 'success');
            const btnReview = document.getElementById('btn_review_course');
            btnReview.innerHTML = '🎓 Đã hoàn thành';
            btnReview.style.color = 'var(--success)';
        }

        function toggleAIChat() {
            document.getElementById('aiPopup').classList.toggle('open');
        }

        function toggleCurriculum() {
            const sidebar = document.querySelector('.curriculum-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar) {
                sidebar.classList.toggle('open');
                if (overlay) {
                    if (sidebar.classList.contains('open')) {
                        overlay.classList.add('active');
                        overlay.onclick = function() {
                            sidebar.classList.remove('open');
                            overlay.classList.remove('active');
                            overlay.onclick = typeof toggleSidebar !== 'undefined' ? toggleSidebar : null;
                        };
                    } else {
                        overlay.classList.remove('active');
                    }
                }
            }
        }

        // AI Chat — gửi context bài học + khóa học
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatAiResponse(text) {
            if (typeof marked !== 'undefined') {
                return marked.parse(text);
            }
            // Fallback simple formatting
            return text.replace(/\n/g, '<br>');
        }

        async function loadAIChatHistory(lessonId, lessonTitle) {
            const chatBox = document.getElementById('chatBox');
            if (!chatBox) return;

            let welcomeMsg = '';
            const lang = localStorage.getItem('lang') || 'vi';
            if (lang === 'en') {
                welcomeMsg = `👋 Hello! I am your AI Tutor for the lesson "${lessonTitle}". Ask me anything about this lesson!`;
            } else {
                welcomeMsg = `👋 Chào bạn! Tôi là AI Tutor của bạn cho bài học "${lessonTitle}". Hỏi tôi bất cứ điều gì về nội dung bài học này nhé!`;
            }
            chatBox.innerHTML = `<div class="msg bot">${welcomeMsg}</div>`;

            try {
                const res = await window.api.get(`/ai/history?lesson_id=${lessonId}`);
                if (res.data && res.data.history && res.data.history.length > 0) {
                    res.data.history.forEach(msg => {
                        if (msg.role === 'user') {
                            chatBox.innerHTML += `<div class="msg user">${escapeHtml(msg.content)}</div>`;
                        } else if (msg.role === 'assistant') {
                            const html = formatAiResponse(msg.content);
                            chatBox.innerHTML += `<div class="msg bot"><div class="ai-markdown-content">${html}</div></div>`;
                        }
                    });
                    
                    chatBox.querySelectorAll('pre code').forEach(b => hljs.highlightElement(b));
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            } catch (err) {
                console.error("Lỗi khi tải lịch sử AI Tutor:", err);
            }
        }



        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input   = document.getElementById('chatInput');
            const chatBox = document.getElementById('chatBox');
            const message = input.value.trim();
            if (!message) return;

            chatBox.innerHTML += `<div class="msg user">${escapeHtml(message)}</div>`;
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            const thinkingId = 'think_' + Date.now();
            chatBox.innerHTML += `
                <div class="msg bot" id="${thinkingId}">
                    <div class="flex items-center gap-2">
                        <span style="font-size:1.2rem;animation:spin 2s linear infinite;">🧠</span>
                        AI Tutor đang phân tích bài học...
                    </div>
                </div>`;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // ← AI Tutor endpoint (strict, lesson-scoped)
                const res = await window.api.post('/ai/tutor', {
                    message,
                    lesson_id: currentLessonId,
                    course_id: courseId,
                    lang: localStorage.getItem('lang') || 'vi'
                });

                const botMsgDiv = document.getElementById(thinkingId);
                const html = formatAiResponse(res.data.ai_response);
                botMsgDiv.innerHTML = `<div class="ai-markdown-content">${html}</div>`;
                botMsgDiv.querySelectorAll('pre code').forEach(b => hljs.highlightElement(b));
            } catch (err) {
                document.getElementById(thinkingId).innerHTML =
                    '❌ Lỗi kết nối tới AI Tutor: ' + (err.message || '');
                document.getElementById(thinkingId).style.color = 'var(--danger)';
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        function showReviewModal() {
            document.getElementById('reviewModal').style.display = 'flex';
        }

        function setRating(val) {
            document.getElementById('reviewRating').value = val;
            const stars = document.querySelectorAll('.star-item');
            stars.forEach(s => {
                const sVal = parseInt(s.dataset.value);
                if (sVal <= val) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        }

        async function submitReview(e) {
            e.preventDefault();
            const rating  = document.getElementById('reviewRating').value;
            const comment = document.getElementById('reviewComment').value;
            const btn     = document.getElementById('btnSubmitReview');
            btn.disabled  = true;
            btn.innerText = 'Đang gửi...';

            try {
                const res = await window.api.post(`/student/courses/${courseId}/reviews`, { rating, comment });
                document.getElementById('reviewModal').style.display = 'none';

                const btnReview = document.getElementById('btn_review_course');
                btnReview.innerHTML = `${'⭐'.repeat(rating)} Đã đánh giá`;
                btnReview.dataset.reviewed = 'true';

                if (res.data && res.data.cert_issued) {
                    App.showToast('🎓 Cảm ơn bạn! Chứng chỉ của bạn đã được cấp. Đang chuyển hướng...', 'success');
                    btnReview.innerHTML = '🎓 Đã nhận chứng chỉ';
                    btnReview.style.color = 'var(--success)';
                } else {
                    App.showToast('Cảm ơn bạn đã đánh giá khóa học! Đang chuyển hướng...', 'success');
                }

                setTimeout(() => {
                    window.location.replace(`/student/course-completed/${courseId}`);
                }, 1500);
            } catch (err) {
                App.showToast(err.message, 'error');
                btn.disabled  = false;
                btn.innerText = 'Gửi Đánh Giá';
            }
        }

        function skipReview() {
            localStorage.setItem(`skip_review_${courseId}`, 'true');
            document.getElementById('reviewModal').style.display = 'none';
            window.location.replace(`/student/course-completed/${courseId}`);
        }
    </script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php'; ?>
