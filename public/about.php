<?php
$pageTitle = 'Giới thiệu — AI Study Hub®';
$actor = 'guest';
$extraHead = '<link rel="stylesheet" href="/assets/css/pages/home.css">';
require __DIR__ . '/layouts/header.php';
?>

<div class="cinematic-bg">
    <video autoplay muted loop playsinline class="cinematic-video">
        <source src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4" type="video/mp4">
    </video>
    <div class="cinematic-overlay"></div>
</div>

<section class="cinematic-section container" style="padding-top: 12rem;">
    <div class="editorial-layout">
        <span class="editorial-label fade-up" data-i18n="about_label">Về chúng tôi</span>
        <h1 class="editorial-title fade-up" data-i18n="about_hero_title">
            Tương lai của học tập<br>
            đang được <em>tái định nghĩa</em><br>
            bởi AI.
        </h1>
        <div class="asymmetric-grid mt-12 gap-12 fade-up">
            <div class="col-left-6">
                <p class="editorial-text" style="max-width: none;" data-i18n="about_text_1">
                    AI Study Hub® không chỉ là một nền tảng quản lý học tập (LMS) truyền thống. Chúng tôi là một hệ sinh thái học thuật hiện đại, nơi công nghệ AI được tích hợp sâu sắc để hỗ trợ từng bước chân của người học.
                </p>
                <p class="editorial-text mt-6" style="max-width: none;" data-i18n="about_text_2">
                    Sứ mệnh của chúng tôi là tạo ra một môi trường học tập tập trung, thông minh và đầy cảm hứng, giúp học viên không chỉ tiếp thu kiến thức mà còn rèn luyện tư duy giải quyết vấn đề với sự trợ giúp của Gia sư AI cá nhân hóa.
                </p>
            </div>
            <div class="col-right-6 liquid-glass p-8" style="border-radius: 24px;">
                <h3 class="editorial-title" style="font-size: 1.5rem; margin-bottom: 1.5rem;" data-i18n="about_values_title">Giá trị cốt lõi</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 1rem; display: flex; gap: 0.75rem; align-items: start;">
                        <span style="color: var(--accent);">✓</span>
                        <span class="text-sm" data-i18n="about_value_1"><strong>Học tập tập trung:</strong> Giao diện tối giản, loại bỏ xao nhãng.</span>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; gap: 0.75rem; align-items: start;">
                        <span style="color: var(--accent);">✓</span>
                        <span class="text-sm" data-i18n="about_value_2"><strong>Trí tuệ nhân tạo:</strong> Hỗ trợ giải đáp 24/7 theo ngữ cảnh bài học.</span>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; gap: 0.75rem; align-items: start;">
                        <span style="color: var(--accent);">✓</span>
                        <span class="text-sm" data-i18n="about_value_3"><strong>Kết nối thực tế:</strong> Tương tác trực tiếp với đội ngũ giảng viên giàu kinh nghiệm.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="cinematic-section container" style="padding: 8rem 0;">
    <div class="text-center mb-12">
        <h2 class="editorial-title" style="font-size: 2.5rem;" data-i18n="about_team_title">Đội ngũ phát triển</h2>
    </div>
    <div class="flex justify-center gap-12 fade-up">
        <div class="text-center">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">👨‍💻</div>
            <h4 class="font-bold">Lê Diên Hiếu</h4>
            <p class="text-xs opacity-40 uppercase tracking-widest mt-1">Founder & Developer</p>
        </div>
    </div>
</section>

<section class="cinematic-section text-center" style="background: rgba(99, 102, 241, 0.03); padding: 10rem 0;">
    <div class="container">
        <h2 class="editorial-title" data-i18n="about_cta_title">Sẵn sàng để bứt phá?</h2>
        <p class="editorial-text mx-auto mt-4 mb-8" data-i18n="about_cta_text">Trở thành một phần của cộng đồng học tập thông minh nhất.</p>
        <a href="/register.php" class="btn btn-primary" data-i18n="about_cta_btn" style="border-radius: 100px; padding: 1rem 3.5rem; font-weight: 600;">Đăng ký ngay</a>
    </div>
</section>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>
