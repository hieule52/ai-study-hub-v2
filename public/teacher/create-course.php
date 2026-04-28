<?php
$pageTitle = 'Tạo Khóa Học - AI Study Hub';
$actor = 'teacher';
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;">Thêm Mới Khóa Học</h1>
                    <p class="text-secondary mt-2">Điền thông tin form bên dưới để tạo trang Course.</p>
                </div>
            </div>

            <div class="card glass-panel" style="padding: 2rem; max-width: 800px;">
                <form id="createCourseForm">
                    <div class="form-group">
                        <label class="form-label">Tên khóa học</label>
                        <input type="text" id="title" class="form-control" placeholder="Ví dụ: AI Masterclass 2026" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mô tả (Description)</label>
                        <textarea id="description" class="form-control" rows="5" placeholder="Mô tả nội dung học..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Thumbnail (Ảnh bìa chia sẻ khóa học)</label>
                        <input type="file" id="thumbnail" class="form-control" accept="image/*">
                    </div>

                    <div class="grid-cols-2">
                        <div class="form-group">
                            <label class="form-label">Giá bán (VNĐ)</label>
                            <input type="number" id="price" class="form-control" placeholder="500000" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gắn mác Khóa học Premium?</label>
                            <select id="is_premium" class="form-control">
                                <option value="1">Có (Yêu cầu VIP / Mua đứt gốc)</option>
                                <option value="0">Không (Khóa miễn phí)</option>
                            </select>
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 2rem 0;">

                    <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">Lưu Khóa Học Lên Hệ Thống</button>
                    <button type="button" class="btn btn-outline" style="padding: 1rem 2rem; margin-left: 1rem;">Lưu Nháp</button>
                </form>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', () => {
            App.requireAuth(['teacher']);

            document.getElementById('createCourseForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                try {
                    const title = document.getElementById('title').value;
                    const desc = document.getElementById('description').value;
                    const price = document.getElementById('price').value;
                    const premium = document.getElementById('is_premium').value;

                    let thumbnailUrl = null;
                    const fileInput = document.getElementById('thumbnail');
                    if (fileInput.files.length > 0) {
                        const uploadRes = await window.api.uploadFile('/upload', fileInput.files[0]);
                        thumbnailUrl = uploadRes.data.url;
                    }

                    const res = await window.api.post('/courses', {
                        title: title,
                        description: desc,
                        thumbnail: thumbnailUrl,
                        price: price,
                        is_premium: premium
                    });

                    App.showToast('Khóa học đã được tạo thành công!', 'success');
                    setTimeout(() => window.location.href='/teacher/dashboard.php', 1500);
                } catch(err) {
                    App.showToast(err.message, 'error');
                }
            });
        });
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
