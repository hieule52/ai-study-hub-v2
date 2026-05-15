<?php
$pageTitle = 'Chi tiết khóa học - AI Study Hub';
$actor = 'guest';
require __DIR__ . '/layouts/header.php';
?>

<style>
    .cd-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 4rem;
        align-items: start;
        padding-top: 120px;
        padding-bottom: 8rem;
    }

    .cd-sticky {
        position: sticky;
        top: 110px;
    }

    .review-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius-xl);
        padding: 1.5rem 2rem;
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
    }

    .review-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.12);
        transform: translateX(10px);
    }

    .editorial-label {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: 1rem;
        display: block;
    }

    .pricing-panel {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-2xl);
        padding: 2.5rem;
        backdrop-filter: blur(30px);
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4);
    }

    @media (max-width: 1024px) {
        .cd-layout {
            grid-template-columns: 1fr;
            gap: 3rem;
            padding-top: 100px;
        }
        .cd-sticky {
            position: static;
        }
    }
</style>

<div class="container cd-layout">

    <!-- ── LEFT: Course Info ── -->
    <div>

        <p class="editorial-label" data-i18n="home_courses_label">Chương trình đào tạo</p>
        <h1 id="cd_title" style="font-size:clamp(2.5rem,5vw,3.5rem); font-weight:900; letter-spacing:-0.05em; line-height:1.05; margin-bottom:2rem;">
            <span style="opacity:0.2" data-i18n="home_loading">Đang tải dữ liệu...</span>
        </h1>

        <div class="flex items-center gap-3 mb-6">
            <span style="font-size: 1.5rem;">📖</span>
            <h3 style="font-size:1.6rem; font-weight:800; letter-spacing:-0.02em;" data-i18n="cd_intro">Giới thiệu khóa học</h3>
        </div>
        <p id="cd_desc" class="editorial-text" style="color: rgba(248, 250, 252, 0.7); margin-bottom: 4rem; max-width: 100%; line-height: 1.8;" data-i18n="cd_desc_loading">
            Vui lòng chờ trong giây lát trong khi chúng tôi chuẩn bị thông tin khóa học cho bạn.
        </p>

        <!-- Divider -->
        <div style="height:1px; background:linear-gradient(90deg, rgba(255,255,255,0.1), transparent); margin:4rem 0;"></div>

        <!-- Reviews -->
        <div class="flex items-center gap-3 mb-8">
            <span style="font-size: 1.5rem;">⭐</span>
            <h3 style="font-size:1.6rem; font-weight:800; letter-spacing:-0.02em;" data-i18n="cd_reviews_title">Đánh giá từ Học Viên</h3>
        </div>
        <div id="cd_reviews_list" style="display:flex; flex-direction:column; gap:1.5rem;">
            <p class="text-muted" style="padding: 2rem; background: rgba(255,255,255,0.02); border-radius: var(--radius-xl); text-align: center;" data-i18n="cd_reviews_loading">Đang tải nhận xét...</p>
        </div>
    </div>

    <!-- ── RIGHT: Pricing Panel ── -->
    <div class="cd-sticky">
        <div class="pricing-panel">

            <!-- Thumbnail -->
            <div id="cd_thumbnail_container" style="border-radius:var(--radius-xl); overflow:hidden; margin-bottom:2rem; aspect-ratio:16/9; background:rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; position:relative; border: 1px solid rgba(255,255,255,0.05);">
                <span id="cd_thumbnail_icon" style="font-size:4rem; opacity:0.1;">📚</span>
                <img id="cd_thumbnail_img" src="" style="display:none; width:100%; height:100%; object-fit:cover; position:absolute; inset:0; transition: transform 0.6s ease;">
                <span id="cd_premium_badge" class="badge badge-premium" style="display:none; position:absolute; top:1.5rem; right:1.5rem; z-index:2;">PREMIUM</span>
            </div>

            <!-- Price & Rating -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <span id="cd_rating" style="font-size:1.1rem; font-weight:800; color: var(--warning);">0 ⭐</span>
                    <span id="cd_reviews_count" class="text-muted text-xs" style="font-weight: 600;">(0 REVIEWS)</span>
                </div>
                <div class="badge" style="background: rgba(255,255,255,0.05); font-size: 0.65rem;" data-i18n="cd_official">Official Course</div>
            </div>

            <div id="cd_price" style="font-size:2.8rem; font-weight:900; letter-spacing:-0.05em; margin-bottom:2rem; color:#fff;">
                ...
            </div>

            <div id="cd_actions" style="display:flex; flex-direction:column; gap:1rem;">
                <button class="btn btn-primary" style="width:100%; padding:1.2rem; border-radius:100px; font-weight:800;" disabled>
                    <span style="opacity:0.3" data-i18n="cd_syncing">Đang đồng bộ...</span>
                </button>
            </div>

            <div class="mt-8 pt-8" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-4 text-muted" style="font-size:0.8rem; font-weight: 500;">
                    <div class="flex-1 text-center">
                        <span style="display: block; font-size: 1.2rem; margin-bottom: 0.25rem;">♾️</span>
                        <span data-i18n="cd_access_forever">Truy cập vĩnh viễn</span>
                    </div>
                    <div class="flex-1 text-center">
                        <span style="display: block; font-size: 1.2rem; margin-bottom: 0.25rem;">🎓</span>
                        <span data-i18n="cd_cert">Chứng chỉ uy tín</span>
                    </div>
                    <div class="flex-1 text-center">
                        <span style="display: block; font-size: 1.2rem; margin-bottom: 0.25rem;">🤖</span>
                        <span data-i18n="cd_ai_support">Hỗ trợ AI 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const courseId = new URLSearchParams(window.location.search).get('id');
    if (!courseId) { document.getElementById('cd_title').innerText = 'Không tìm thấy khóa học'; return; }

    try {
        const res = await window.api.get(`/courses/${courseId}`);
        const course = res.data;

        document.getElementById('cd_title').innerText = course.title;
        document.getElementById('cd_desc').innerText  = course.description || 'Chưa có mô tả khóa học.';

        if (course.thumbnail) {
            document.getElementById('cd_thumbnail_icon').style.display = 'none';
            const img = document.getElementById('cd_thumbnail_img');
            img.src = course.thumbnail; img.style.display = 'block';
        }
        if (course.is_premium == 1 || course.price > 0) {
            document.getElementById('cd_premium_badge').style.display = 'inline-flex';
            document.getElementById('cd_price').innerHTML =
                new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(course.price);
        } else {
            document.getElementById('cd_price').innerHTML = '<span class="badge badge-free" style="font-size:1rem;padding:0.4rem 1rem;">Miễn phí</span>';
        }

        // Button logic
        let enrolledIds = [];
        if (window.api.getToken()) {
            try { const er = await window.api.get('/student/courses'); enrolledIds = er.data.map(c=>c.id); } catch(e){}
        }
        const isEnrolled = enrolledIds.includes(course.id);
        const isPremium  = course.is_premium == 1 || course.price > 0;
        let btnHtml = '';
        if (isEnrolled) {
            btnHtml = `<button onclick="window.location.href='/student/learning.php?course_id=${course.id}'" class="btn btn-primary" style="width:100%;padding:0.9rem;border-radius:100px;">▶ Tiếp tục học</button>`;
        } else if (isPremium) {
            if (window.api.getToken()) {
                btnHtml = `<button onclick="window.location.href='/student/course-payment.php?course_id=${course.id}&price=${course.price}'" class="btn btn-primary" style="width:100%;padding:0.9rem;border-radius:100px;">💳 Mua khóa học</button>`;
            } else {
                btnHtml = `<button onclick="window.location.href='/login.php'" class="btn btn-primary" style="width:100%;padding:0.9rem;border-radius:100px;">Đăng nhập để Mua</button>`;
            }
        } else {
            if (window.api.getToken()) {
                btnHtml = `<button onclick="enrollAndLearn(${course.id})" class="btn btn-primary" style="width:100%;padding:0.9rem;border-radius:100px;">🚀 Đăng ký miễn phí</button>`;
            } else {
                btnHtml = `<button onclick="window.location.href='/login.php'" class="btn btn-outline" style="width:100%;padding:0.9rem;border-radius:100px;">Đăng nhập để Học</button>`;
            }
        }
        document.getElementById('cd_actions').innerHTML = btnHtml;

        // Reviews
        const rvRes  = await window.api.get(`/courses/${courseId}/reviews`);
        const rvData = rvRes.data;
        document.getElementById('cd_rating').innerText       = (rvData.stats.avg_rating || 0) + ' ⭐';
        document.getElementById('cd_reviews_count').innerText = `(${rvData.stats.total_reviews} đánh giá)`;
        const rvList = document.getElementById('cd_reviews_list');
        if (!rvData.reviews.length) {
            rvList.innerHTML = '<p class="text-muted text-sm">Chưa có đánh giá nào.</p>';
        } else {
            rvList.innerHTML = rvData.reviews.map(r => `
                <div class="review-card">
                    <div class="flex justify-between items-center" style="margin-bottom:0.5rem;">
                        <strong style="font-size:0.9rem;">${r.username || (window.I18n?window.I18n.get('std_anon'):'Học viên ẩn danh')}</strong>
                        <span style="font-size:0.85rem;">${'⭐'.repeat(r.rating)}</span>
                    </div>
                    <p class="text-secondary" style="font-size:0.875rem;margin:0;line-height:1.65;">${r.comment || (window.I18n?window.I18n.get('std_no_comment'):'Không có nhận xét.')}</p>
                </div>`).join('');
        }
        if (window.I18n) window.I18n.render();
    } catch(e) { document.getElementById('cd_title').innerText = window.I18n?window.I18n.get('cd_load_error'):'Lỗi tải dữ liệu khóa học'; }
});

window.enrollAndLearn = async function(id) {
    try { await window.api.post(`/courses/${id}/enroll`, {}); } catch(e) {}
    window.location.href = `/student/learning.php?course_id=${id}`;
};
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>
