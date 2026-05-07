<?php
$pageTitle = 'Tạo Khóa Học - AI Study Hub';
$actor = 'teacher';
ob_start();
?>
<style>
    .create-course-form {
        max-width: 900px;
    }
    .form-section {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .form-section-title span {
        font-size: 1.4rem;
    }
    .thumbnail-preview {
        width: 100%;
        max-width: 400px;
        aspect-ratio: 16/9;
        border-radius: var(--radius-md);
        border: 2px dashed rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: pointer;
        transition: var(--transition);
        background: rgba(0,0,0,0.3);
        margin-bottom: 1rem;
    }
    .thumbnail-preview:hover {
        border-color: var(--primary);
        background: rgba(79,70,229,0.05);
    }
    .thumbnail-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumbnail-preview .placeholder-icon {
        text-align: center;
        color: var(--text-muted);
    }
    .thumbnail-preview .placeholder-icon span {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
    }
    .level-cards {
        display: flex;
        gap: 1rem;
    }
    .level-card {
        flex: 1;
        padding: 1.25rem;
        border-radius: var(--radius-md);
        border: 2px solid rgba(255,255,255,0.05);
        background: rgba(0,0,0,0.2);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }
    .level-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }
    .level-card.selected {
        border-color: var(--primary);
        background: rgba(79,70,229,0.1);
        box-shadow: 0 0 20px rgba(79,70,229,0.1);
    }
    .level-card .level-emoji {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .level-card .level-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .level-card .level-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    .price-toggle {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .price-toggle .toggle-btn {
        flex: 1;
        padding: 1rem;
        border-radius: var(--radius-md);
        border: 2px solid rgba(255,255,255,0.05);
        background: rgba(0,0,0,0.2);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        color: var(--text-secondary);
        font-weight: 600;
    }
    .price-toggle .toggle-btn.active {
        border-color: var(--success);
        background: rgba(16,185,129,0.1);
        color: var(--success);
    }
    .submit-section {
        display: flex;
        gap: 1rem;
        align-items: center;
        padding-top: 1rem;
    }
    .submit-section .btn {
        padding: 1rem 2.5rem;
        font-size: 1.05rem;
        border-radius: var(--radius-lg);
    }
</style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 style="font-size: 2rem;">Tạo Khóa Học Mới</h1>
        <p class="text-secondary mt-2">Điền thông tin để tạo khóa học. Sau khi tạo, bạn sẽ được chuyển đến trang xây dựng nội dung.</p>
    </div>
</div>

<div class="create-course-form">
    <form id="createCourseForm">

        <!-- Section 1: Basic Info -->
        <div class="form-section">
            <div class="form-section-title"><span>📝</span> Thông tin cơ bản</div>

            <div class="form-group">
                <label class="form-label">Tên khóa học <span style="color: var(--danger);">*</span></label>
                <input type="text" id="title" class="form-control" placeholder="Ví dụ: AI Masterclass 2026 - Từ Zero đến Hero" required>
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả khóa học <span style="color: var(--danger);">*</span></label>
                <textarea id="description" class="form-control" rows="4" placeholder="Mô tả nội dung, mục tiêu, đối tượng học viên..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Danh mục</label>
                <select id="category_id" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                </select>
            </div>
        </div>

        <!-- Section 2: Thumbnail -->
        <div class="form-section">
            <div class="form-section-title"><span>🖼️</span> Ảnh bìa (Thumbnail)</div>
            <div class="thumbnail-preview" onclick="document.getElementById('thumbnail').click()">
                <div class="placeholder-icon" id="thumbnailPlaceholder">
                    <span>📷</span>
                    <p style="font-size: 0.9rem;">Click để chọn ảnh (16:9)</p>
                </div>
            </div>
            <input type="file" id="thumbnail" accept="image/*" style="display: none;" onchange="previewThumbnail(this)">
        </div>

        <!-- Section 3: Level -->
        <div class="form-section">
            <div class="form-section-title"><span>📊</span> Cấp độ khóa học</div>
            <input type="hidden" id="level" value="beginner">
            <div class="level-cards">
                <div class="level-card selected" onclick="selectLevel('beginner', this)">
                    <div class="level-emoji">🌱</div>
                    <div class="level-name">Cơ bản</div>
                    <div class="level-desc">Dành cho người mới bắt đầu</div>
                </div>
                <div class="level-card" onclick="selectLevel('intermediate', this)">
                    <div class="level-emoji">🚀</div>
                    <div class="level-name">Trung cấp</div>
                    <div class="level-desc">Có kiến thức nền tảng</div>
                </div>
                <div class="level-card" onclick="selectLevel('advanced', this)">
                    <div class="level-emoji">⚡</div>
                    <div class="level-name">Nâng cao</div>
                    <div class="level-desc">Chuyên sâu, thực chiến</div>
                </div>
            </div>
        </div>

        <!-- Section 4: Pricing -->
        <div class="form-section">
            <div class="form-section-title"><span>💰</span> Giá bán</div>
            <div class="price-toggle">
                <div class="toggle-btn" onclick="togglePricing(false, this)">🆓 Miễn phí</div>
                <div class="toggle-btn active" onclick="togglePricing(true, this)">💎 Có phí</div>
            </div>
            <div id="priceInputGroup">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Giá bán (VNĐ)</label>
                    <input type="number" id="price" class="form-control" placeholder="500000" min="0" value="0">
                </div>
            </div>
        </div>

        <!-- Section 5: Duration -->
        <div class="form-section">
            <div class="form-section-title"><span>⏱️</span> Thời lượng ước tính</div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Tổng thời lượng (phút)</label>
                <input type="number" id="estimated_duration" class="form-control" placeholder="120" min="0" value="0">
                <small class="text-muted">Ước tính tổng thời gian học viên cần để hoàn thành khóa học</small>
            </div>
        </div>

        <!-- Submit -->
        <div class="submit-section">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                🚀 Tạo Khóa Học & Xây Dựng Nội Dung
            </button>
            <a href="/teacher/dashboard.php" class="btn btn-outline">Hủy</a>
        </div>
    </form>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        App.requireAuth(['teacher']);
        loadCategories();

        document.getElementById('createCourseForm').addEventListener('submit', handleSubmit);
    });

    // Load categories
    async function loadCategories() {
        try {
            const res = await window.api.get('/categories');
            const select = document.getElementById('category_id');
            if (res.data && res.data.length > 0) {
                res.data.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = `${cat.icon ? '' : ''} ${cat.name}`;
                    select.appendChild(opt);
                });
            }
        } catch(e) {
            console.warn('Categories not loaded:', e.message);
        }
    }

    // Thumbnail preview
    function previewThumbnail(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.thumbnail-preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Level selection
    function selectLevel(level, el) {
        document.getElementById('level').value = level;
        document.querySelectorAll('.level-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
    }

    // Price toggle
    function togglePricing(isPaid, el) {
        document.querySelectorAll('.price-toggle .toggle-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        
        const priceGroup = document.getElementById('priceInputGroup');
        if (isPaid) {
            priceGroup.style.display = 'block';
        } else {
            priceGroup.style.display = 'none';
            document.getElementById('price').value = 0;
        }
    }

    // Submit form
    async function handleSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ Đang tạo khóa học...';

        try {
            // Upload thumbnail if selected
            let thumbnailUrl = null;
            const fileInput = document.getElementById('thumbnail');
            if (fileInput.files.length > 0) {
                App.showToast('Đang tải ảnh bìa...', 'info');
                const uploadRes = await window.api.uploadFile('/upload/image', fileInput.files[0]);
                thumbnailUrl = uploadRes.data.url;
            }

            const price = parseInt(document.getElementById('price').value) || 0;

            const res = await window.api.post('/courses', {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                thumbnail: thumbnailUrl,
                price: price,
                is_premium: price > 0 ? 1 : 0,
                category_id: document.getElementById('category_id').value || null,
                level: document.getElementById('level').value,
                estimated_duration: parseInt(document.getElementById('estimated_duration').value) || 0
            });

            App.showToast('🎉 Khóa học đã được tạo thành công!', 'success');

            // Redirect to course builder
            const courseId = res.data.id;
            setTimeout(() => {
                window.location.href = `/teacher/course-builder.php?course_id=${courseId}`;
            }, 1000);

        } catch(err) {
            App.showToast(err.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '🚀 Tạo Khóa Học & Xây Dựng Nội Dung';
        }
    }
</script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
