<?php
$pageTitle = 'Chi tiết khóa học - AI Study Hub';
$actor = 'guest';
require __DIR__ . '/layouts/header.php';
?>

    <div class="container mt-8" style="padding-top: 100px;">
        <div class="grid-cols-2" style="grid-template-columns: 2fr 1fr; align-items: start;">
            
            <!-- Context -->
            <div>
                <a href="/" class="btn btn-outline mb-4" style="padding: 0.5rem 1rem;">&larr; Về trang chủ</a>
                <h1 id="cd_title" style="font-size: 2.5rem; margin-bottom: 1rem;">Đang tải...</h1>
                <p id="cd_desc" class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                    Đang tải mô tả khóa học...
                </p>

                <h3 class="mt-8 mb-4">Đánh giá từ Học Viên</h3>
                <div id="cd_reviews_list" style="display: flex; flex-direction: column; gap: 1rem;">
                    <p class="text-muted">Đang tải đánh giá...</p>
                </div>
            </div>

            <!-- Pricing Box -->
            <div class="card glass-panel" style="padding: 2rem; position: sticky; top: 100px;">
                <div class="card-img-placeholder mb-4" id="cd_thumbnail_container" style="border-radius: var(--radius-md); overflow: hidden; position: relative;">
                    <span id="cd_thumbnail_icon">📚</span>
                    <img id="cd_thumbnail_img" src="" style="display:none; width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">
                    <span id="cd_premium_badge" style="display:none; position: absolute; top: 10px; right: 10px; background: var(--warning); color: #000; font-size: 0.7rem; font-weight: bold; padding: 2px 8px; border-radius: 10px; z-index: 2;">PREMIUM</span>
                </div>
                
                <div style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 10px;">
                    <span id="cd_rating" style="font-weight: bold; color: var(--warning); font-size: 1.2rem;">0 ⭐</span>
                    <span id="cd_reviews_count" class="text-secondary">(0 đánh giá)</span>
                </div>
                <h2 id="cd_price" style="color: var(--warning); margin-bottom: 1rem; font-size: 2.5rem;">Miễn phí</h2>
                
                <div class="flex flex-column gap-4" id="cd_actions">
                    <button class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;" disabled>Đang xử lý...</button>
                </div>

                <div class="mt-4 text-center text-muted" style="font-size: 0.875rem;">
                    Cam kết chất lượng từ AI Study Hub
                </div>
            </div>

        </div>
    </div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get('id');

        if (!courseId) {
            document.getElementById('cd_title').innerText = "Không tìm thấy khóa học";
            return;
        }

        try {
            // Lấy chi tiết khóa học
            const res = await window.api.get(`/courses/${courseId}`);
            const course = res.data;

            document.getElementById('cd_title').innerText = course.title;
            document.getElementById('cd_desc').innerText = course.description || "Chưa có mô tả khóa học.";
            
            // Xử lý thumbnail
            if (course.thumbnail) {
                document.getElementById('cd_thumbnail_icon').style.display = 'none';
                const img = document.getElementById('cd_thumbnail_img');
                img.src = course.thumbnail;
                img.style.display = 'block';
            }

            // Xử lý giá tiền
            if (course.is_premium == 1 || course.price > 0) {
                document.getElementById('cd_premium_badge').style.display = 'block';
                document.getElementById('cd_price').innerHTML = new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(course.price);
            }

            // Xử lý nút bấm (giống logic ở trang chủ)
            let buttonHtml = '';
            let enrolledCourseIds = [];
            
            if (window.api.getToken()) {
                try {
                    const enrolledRes = await window.api.get('/student/courses');
                    enrolledCourseIds = enrolledRes.data.map(c => c.id);
                } catch(e) {}
            }

            const isEnrolled = enrolledCourseIds.includes(course.id);
            if (isEnrolled) {
                buttonHtml = `<button onclick="window.location.href='/student/learning.php?course_id=${course.id}'" class="btn btn-primary" style="width: 100%; padding: 1rem;">Tiếp tục học</button>`;
            } else if (course.is_premium == 1 || course.price > 0) {
                if (window.api.getToken()) {
                    buttonHtml = `<button onclick="window.location.href='/student/course-payment.php?course_id=${course.id}&price=${course.price}'" class="btn" style="background: var(--warning); color: #000; font-weight: bold; width: 100%; padding: 1rem;">Mua khóa học</button>`;
                } else {
                    buttonHtml = `<button onclick="window.location.href='/login.php'" class="btn" style="background: var(--warning); color: #000; font-weight: bold; width: 100%; padding: 1rem;">Đăng nhập để Mua</button>`;
                }
            } else {
                if (window.api.getToken()) {
                    buttonHtml = `<button onclick="enrollAndLearn(${course.id})" class="btn btn-primary" style="width: 100%; padding: 1rem;">Đăng ký miễn phí</button>`;
                } else {
                    buttonHtml = `<button onclick="window.location.href='/login.php'" class="btn btn-primary" style="width: 100%; padding: 1rem;">Đăng nhập để Học</button>`;
                }
            }

            document.getElementById('cd_actions').innerHTML = buttonHtml;

            // Fetch Reviews
            const rvRes = await window.api.get(`/courses/${courseId}/reviews`);
            const rvData = rvRes.data;

            document.getElementById('cd_rating').innerText = rvData.stats.avg_rating + " ⭐";
            document.getElementById('cd_reviews_count').innerText = `(${rvData.stats.total_reviews} đánh giá)`;

            const rvList = document.getElementById('cd_reviews_list');
            if (rvData.reviews.length === 0) {
                rvList.innerHTML = '<p class="text-muted">Chưa có đánh giá nào cho khóa học này.</p>';
            } else {
                rvList.innerHTML = rvData.reviews.map(r => `
                    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.1);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--text-primary);">${r.username || 'Học viên ẩn danh'}</strong>
                            <span style="color: var(--warning);">${'⭐'.repeat(r.rating)}</span>
                        </div>
                        <p style="color: var(--text-secondary); margin: 0; line-height: 1.6;">${r.comment || 'Không có nhận xét.'}</p>
                    </div>
                `).join('');
            }

        } catch (e) {
            document.getElementById('cd_title').innerText = "Lỗi tải dữ liệu khóa học";
        }
    });

    window.enrollAndLearn = async function(id) {
        try {
            await window.api.post(`/courses/${id}/enroll`, {});
            window.location.href = `/student/learning.php?course_id=${id}`;
        } catch (e) {
            App.showToast(e.message, 'error');
        }
    };
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>
