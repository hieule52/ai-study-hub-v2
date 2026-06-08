<?php
$pageTitle = 'Create Course - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <!-- Sidebar -->
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <main class="teacher-content">
        <header class="dash-header">
            <div class="dash-title-group">
                <h1 id="page-title-text" data-i18n="tc_dash_btn_create">Tạo Khóa Học Mới</h1>
                <p id="page-subtitle-text">Khởi tạo hành trình tri thức mới của bạn</p>
            </div>
        </header>

        <div class="editor-card" style="max-width: 900px; margin: 0;">
            <form id="createCourseForm">
                <div class="grid grid-cols-2 gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <div class="form-group">
                            <label class="form-label" data-i18n="tc_dash_col_name">Tên khóa học</label>
                            <input type="text" id="title" class="form-control" placeholder="VD: Làm chủ AI trong 30 ngày" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô tả khóa học</label>
                            <textarea id="description" class="form-control" rows="5" placeholder="Mô tả mục tiêu và nội dung..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Danh mục</label>
                            <select id="category_id" class="form-control">
                                <option value="">-- Chọn danh mục --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <div class="form-group">
                            <label class="form-label">Ảnh bìa (16:9)</label>
                            <div class="upload-zone" onclick="document.getElementById('thumbnail').click()" id="thumbZone">
                                <div id="thumbPreview" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; flex-direction:column;">
                                    <div class="upload-icon"><i class="fas fa-image"></i></div>
                                    <p style="font-size: 0.8rem; opacity: 0.5;">Click để chọn ảnh</p>
                                </div>
                            </div>
                            <input type="file" id="thumbnail" accept="image/*" style="display: none;" onchange="previewThumbnail(this)">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label" data-i18n="tc_dash_col_price">Học phí (VNĐ)</label>
                                <input type="number" id="price" class="form-control" value="0" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Cấp độ</label>
                                <select id="level" class="form-control">
                                    <option value="beginner">Cơ bản</option>
                                    <option value="intermediate">Trung cấp</option>
                                    <option value="advanced">Nâng cao</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Thời lượng ước tính (phút)</label>
                            <input type="number" id="estimated_duration" class="form-control" value="120" min="0">
                        </div>
                    </div>
                </div>

                <!-- 🤖 AI Learning Context Section -->
                <div style="margin-top: 2.5rem; padding: 2rem; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: var(--radius-lg);">
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem;">
                        <div style="font-size:1.5rem;">🧠</div>
                        <div>
                            <h4 style="margin:0; font-size:1rem; font-weight:700; color:var(--primary);">AI Learning Context</h4>
                            <p style="margin:0; font-size:0.75rem; opacity:0.6;">Cung cấp ngữ cảnh để AI Tutor hiểu sâu hơn về khóa học của bạn.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tóm tắt dành cho AI (AI Summary) <span style="color:var(--danger);">*</span></label>
                        <textarea id="ai_course_summary" class="form-control" rows="3" placeholder="Tóm tắt ngắn gọn mục tiêu cốt lõi của khóa học dành cho AI..." required></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Từ khóa trọng tâm (AI Keywords) <span style="color:var(--danger);">*</span></label>
                            <input type="text" id="ai_keywords" class="form-control" placeholder="VD: machine learning, neural networks, python" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Trọng tâm giảng dạy (AI Learning Focus) <span style="color:var(--danger);">*</span></label>
                            <input type="text" id="ai_focus" class="form-control" placeholder="VD: Thực hành dự án thực tế, Tư duy thuật toán" required>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 3rem; display: flex; gap: 1rem; border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="padding: 1rem 2.5rem; border-radius: 100px;">
                        Tiếp theo: Xây dựng nội dung
                    </button>
                    <a href="/teacher/dashboard" class="btn btn-ghost" style="padding: 1rem 2rem;">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
const courseId = new URLSearchParams(window.location.search).get('id');
let currentThumbnail = null;

document.addEventListener('DOMContentLoaded', async () => {
    App.requireAuth(['teacher', 'admin']);
    await loadCategories();
    
    if (courseId) {
        await loadCourseData();
    }
    
    if (window.I18n) window.I18n.render();
    document.getElementById('createCourseForm').addEventListener('submit', handleSubmit);
});

async function loadCourseData() {
    try {
        const res = await window.api.get(`/courses/${courseId}`);
        const c = res.data;
        
        document.getElementById('page-title-text').innerText = 'Chỉnh sửa khóa học';
        document.getElementById('page-subtitle-text').innerText = 'Cập nhật thông tin chi tiết cho khóa học của bạn';
        document.getElementById('submitBtn').innerHTML = '💾 Lưu thay đổi';
        
        document.getElementById('title').value = c.title;
        document.getElementById('description').value = c.description;
        document.getElementById('category_id').value = c.category_id || '';
        document.getElementById('price').value = c.price || 0;
        document.getElementById('level').value = c.level || 'beginner';
        document.getElementById('estimated_duration').value = c.estimated_duration || 0;
        document.getElementById('ai_course_summary').value = c.ai_course_summary || '';
        document.getElementById('ai_keywords').value = c.ai_keywords || '';
        document.getElementById('ai_focus').value = c.ai_focus || '';
        
        if (c.thumbnail) {
            currentThumbnail = c.thumbnail;
            document.getElementById('thumbZone').innerHTML = `<img src="${c.thumbnail}" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-md);">`;
        }
    } catch(e) {
        App.showToast('Không thể tải dữ liệu khóa học', 'error');
    }
}

async function loadCategories() {
    try {
        const res = await window.api.get('/categories');
        const select = document.getElementById('category_id');
        res.data.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            select.appendChild(opt);
        });
    } catch(e) {}
}

function previewThumbnail(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('thumbZone').innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-md);">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');

    // ── Comprehensive Validation ──
    const validationRules = [
        { id: 'title',              label: 'Tên khóa học' },
        { id: 'description',        label: 'Mô tả khóa học' },
        { id: 'ai_course_summary',  label: 'Tóm tắt dành cho AI (AI Summary)' },
        { id: 'ai_keywords',        label: 'Từ khóa trọng tâm (AI Keywords)' },
        { id: 'ai_focus',           label: 'Trọng tâm giảng dạy (AI Learning Focus)' }
    ];

    for (const rule of validationRules) {
        const el = document.getElementById(rule.id);
        if (!el || !el.value.trim()) {
            App.showToast(`Vui lòng nhập: ${rule.label}`, 'error');
            if (el) {
                el.focus();
                el.style.borderColor = 'var(--danger)';
                el.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.15)';
                setTimeout(() => {
                    el.style.borderColor = '';
                    el.style.boxShadow = '';
                }, 3000);
            }
            return;
        }
    }

    // Check thumbnail for new courses
    const fileInput = document.getElementById('thumbnail');
    if (!courseId && !currentThumbnail && fileInput.files.length === 0) {
        App.showToast('Vui lòng chọn ảnh bìa cho khóa học', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = courseId ? '⏳ Đang cập nhật...' : '⏳ Đang khởi tạo...';

    try {
        let thumbnailUrl = currentThumbnail;
        if (fileInput.files.length > 0) {
            const uploadRes = await window.api.uploadFile('/upload/image', fileInput.files[0]);
            thumbnailUrl = uploadRes.data.url;
        }

        const price = parseInt(document.getElementById('price').value) || 0;
        const payload = {
            title: document.getElementById('title').value.trim(),
            description: document.getElementById('description').value.trim(),
            thumbnail: thumbnailUrl,
            price: price,
            is_premium: price > 0 ? 1 : 0,
            category_id: document.getElementById('category_id').value || null,
            level: document.getElementById('level').value,
            estimated_duration: parseInt(document.getElementById('estimated_duration').value) || 0,
            ai_course_summary: document.getElementById('ai_course_summary').value.trim(),
            ai_keywords: document.getElementById('ai_keywords').value.trim(),
            ai_focus: document.getElementById('ai_focus').value.trim()
        };

        if (courseId) {
            await window.api.put(`/teacher/courses/${courseId}`, payload);
            App.showToast('Đã cập nhật khóa học!', 'success');
            setTimeout(() => {
                window.location.href = '/teacher/dashboard';
            }, 1000);
        } else {
            const res = await window.api.post('/courses', payload);
            App.showToast('Khóa học đã được tạo!', 'success');
            setTimeout(() => {
                window.location.href = `/teacher/course-builder/${res.data.id}`;
            }, 1000);
        }
    } catch(err) {
        App.showToast(err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = courseId ? '💾 Lưu thay đổi' : 'Tiếp theo: Xây dựng nội dung';
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
