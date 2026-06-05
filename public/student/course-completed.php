<?php
$pageTitle = 'Chúc Mừng Hoàn Thành Khóa Học! - AI Study Hub';
$actor = 'student';
$noSidebar = true;
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/css/student/dashboard.css?v=' . time() . '">
    <style>
        .completion-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            text-align: center;
        }
        .celebration-card {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 24px;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
        }
        .celebration-icon {
            font-size: 5rem;
            animation: bounce 2s infinite;
            margin-bottom: 20px;
        }
        .congrats-title {
            font-family: var(--font-heading);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #a855f7 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
            letter-spacing: -0.03em;
        }
        .congrats-subtitle {
            color: rgba(240, 240, 244, 0.7);
            font-size: 1.15rem;
            max-width: 600px;
            margin: 0 auto 40px auto;
            line-height: 1.6;
        }
        .stats-summary {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        .summary-tile {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            min-width: 180px;
            transition: all 0.3s;
        }
        .summary-tile:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(168, 85, 247, 0.3);
        }
        .tile-label {
            font-size: 0.8rem;
            color: rgba(240, 240, 244, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .tile-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        .certificate-section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(168, 85, 247, 0.3);
            border-radius: 20px;
            padding: 40px;
            margin-top: 40px;
            text-align: center;
        }
        /* Certificate PDF Template (Hidden off-screen) */
        #certTemplateContainer {
            position: absolute;
            left: -9999px;
            top: -9999px;
        }
        .cert-card {
            width: 800px;
            height: 560px;
            padding: 40px;
            background: #0f0c1b;
            color: #fff;
            font-family: "Inter", sans-serif;
            border: 10px double #a855f7;
            position: relative;
            box-sizing: border-box;
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 40%);
        }
        .cert-border {
            border: 2px solid rgba(168, 85, 247, 0.4);
            height: 100%;
            width: 100%;
            padding: 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .cert-header {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #a855f7;
            font-weight: 700;
        }
        .cert-title {
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin: 10px 0;
            background: linear-gradient(135deg, #fff 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .cert-recipient {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 5px;
            margin: 20px 0;
            min-width: 300px;
            text-align: center;
        }
        .cert-body {
            font-size: 1rem;
            color: rgba(240, 240, 244, 0.8);
            text-align: center;
            line-height: 1.6;
            max-width: 550px;
        }
        .cert-footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
        }
        .cert-meta {
            text-align: left;
            font-size: 0.8rem;
            color: rgba(240, 240, 244, 0.5);
        }
        .cert-signature {
            text-align: right;
        }
        .sig-line {
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            width: 150px;
            margin-top: 5px;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="completion-container">
    <div class="celebration-card">
        <div class="celebration-icon">🎓</div>
        <h1 class="congrats-title" id="courseCompletedTitle">Chúc Mừng Bạn!</h1>
        <p class="congrats-subtitle" id="courseSubtitle">Bạn đã nỗ lực xuất sắc và hoàn thành 100% các bài học cũng như vượt qua mọi bài kiểm tra để sở hữu chứng nhận khóa học này.</p>

        <div class="stats-summary">
            <div class="summary-tile">
                <div class="tile-label">Hoàn thành ngày</div>
                <div class="tile-value" id="completionDate">--/--/----</div>
            </div>
            <div class="summary-tile">
                <div class="tile-label">Điểm trung bình</div>
                <div class="tile-value" id="avgScore">--%</div>
            </div>
            <div class="summary-tile" style="max-width: 250px;">
                <div class="tile-label">Đánh giá của bạn</div>
                <div class="tile-value" id="userRating">⭐⭐⭐⭐⭐</div>
            </div>
        </div>

        <div class="certificate-section" id="certificateBox" style="display: none;">
            <div style="font-size: 3rem; margin-bottom: 15px;">📜</div>
            <h3 style="color: #fff; font-size: 1.4rem; font-weight: 700; margin-bottom: 10px;">Chứng Chỉ Đã Được Cấp!</h3>
            <p style="color: rgba(240,240,244,0.6); font-size: 0.9rem; margin-bottom: 25px; max-width: 500px; margin-left: auto; margin-right: auto;" id="certCodeDesc">Mã chứng chỉ: ...</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button onclick="viewCertificate()" class="btn btn-outline" style="border-radius: 100px; padding: 0.75rem 2rem;">📜 Xem chứng chỉ</button>
                <button onclick="downloadPDF()" class="btn btn-primary" style="border-radius: 100px; padding: 0.75rem 2rem; background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%); border: none;">📥 Tải PDF</button>
            </div>
        </div>

        <div style="margin-top: 40px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <button onclick="resetLearningProgress()" class="btn btn-outline" style="border-radius: 100px; padding: 0.75rem 2rem; border-color: rgba(239, 68, 68, 0.4); color: #ef4444; background: rgba(239, 68, 68, 0.05); cursor: pointer;"><i class="fas fa-redo"></i> Học lại từ đầu</button>
            <a href="/student/dashboard" class="btn btn-ghost" style="border-radius: 100px; padding: 0.75rem 2rem;">Quay về Góc học tập</a>
            <a href="/courses" class="btn btn-outline" style="border-radius: 100px; padding: 0.75rem 2rem;">Khám phá khóa học khác &rarr;</a>
        </div>
    </div>
</div>

<!-- OFF-SCREEN CERTIFICATE FOR PDF EXPORT -->
<div id="certTemplateContainer">
    <div class="cert-card" id="pdfCertCard">
        <div class="cert-border">
            <div class="cert-header">Chứng Nhận Hoàn Thành</div>
            <div style="text-align: center; width: 100%;">
                <p style="font-size: 0.85rem; color: rgba(240,240,244,0.6); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.05em;">Hệ thống học tập AI Study Hub chứng nhận</p>
                <div class="cert-recipient" id="pdfRecipient">Học viên</div>
                <p style="font-size: 0.9rem; color: rgba(240,240,244,0.7); margin-bottom: 5px;">Đã hoàn thành xuất sắc khóa học</p>
                <div class="cert-title" id="pdfCourseTitle">Tên khóa học</div>
            </div>
            <div class="cert-body">
                Chúc mừng bạn đã nỗ lực học tập, nghiên cứu và hoàn thành xuất sắc toàn bộ nội dung học trình, bài tập thực hành ứng dụng công nghệ trí tuệ nhân tạo.
            </div>
            <div class="cert-footer">
                <div class="cert-meta">
                    <div>Mã số: <span id="pdfCertCode">...</span></div>
                    <div>Ngày cấp: <span id="pdfCertDate">...</span></div>
                    <div>Điểm trung bình: <span id="pdfCertScore">100.0</span>%</div>
                </div>
                <div class="cert-signature">
                    <div style="font-size: 0.9rem; font-style: italic; font-weight: 600; color: #a855f7;">AI Study Hub Board</div>
                    <div style="font-size: 0.75rem; color: rgba(240,240,244,0.5); margin-top: 2px;">Hội đồng Thẩm định</div>
                    <div class="sig-line"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    const user = App.requireAuth(['student']);
    
    // Extract courseId from route params ($_GET injected by Router)
    const courseId = <?= json_encode($_GET['course_id'] ?? null) ?> ?? new URLSearchParams(window.location.search).get('course_id');
    let certCode = '';

    document.addEventListener('DOMContentLoaded', async () => {
        if (!courseId) {
            App.showToast('Thiếu ID khóa học', 'error');
            return;
        }

        // Fire nice confetti celebration
        fireConfetti();

        try {
            // Load Course & Progress & Certificate Details
            const [courseRes, reviewRes, certsRes] = await Promise.all([
                window.api.get(`/courses/${courseId}`),
                window.api.get(`/student/courses/${courseId}/my-review`),
                window.api.get('/certificates/my')
            ]);

            const course = courseRes.data;
            const review = reviewRes.data;
            const certificates = certsRes.data || [];
            let myCert = certificates.find(c => c.course_id == courseId);

            if (!myCert) {
                try {
                    await window.api.post(`/certificates/claim/${courseId}`);
                    const retryRes = await window.api.get('/certificates/my');
                    const updatedCerts = retryRes.data || [];
                    myCert = updatedCerts.find(c => c.course_id == courseId);
                } catch (claimErr) {
                    console.error('Auto-claim certificate failed:', claimErr);
                }
            }

            document.getElementById('courseCompletedTitle').innerText = 'Chúc Mừng Bạn Đã Hoàn Thành!';
            document.getElementById('courseSubtitle').innerHTML = `Bạn đã nỗ lực xuất sắc và hoàn thành 100% các bài học thuộc khóa học <strong>"${course.title}"</strong>.`;

            // Fill summary tiles
            if (myCert) {
                const rawDate = new Date(myCert.completion_date || myCert.created_at);
                document.getElementById('completionDate').innerText = rawDate.toLocaleDateString('vi-VN');
                document.getElementById('avgScore').innerText = myCert.score ? parseFloat(myCert.score).toFixed(1) + '%' : '100%';

                certCode = myCert.certificate_code;
                document.getElementById('certCodeDesc').innerText = 'Mã số chứng chỉ bảo mật: ' + certCode;
                document.getElementById('certificateBox').style.display = 'block';

                // Fill PDF template values
                document.getElementById('pdfRecipient').innerText = user.username || 'Học viên';
                document.getElementById('pdfCourseTitle').innerText = course.title;
                document.getElementById('pdfCertCode').innerText = certCode;
                document.getElementById('pdfCertDate').innerText = rawDate.toLocaleDateString('vi-VN');
                document.getElementById('pdfCertScore').innerText = myCert.score ? parseFloat(myCert.score).toFixed(1) : '100.0';
            }

            if (review && review.rating) {
                document.getElementById('userRating').innerText = '⭐'.repeat(review.rating);
            } else {
                document.getElementById('userRating').innerText = 'Chưa đánh giá';
            }

        } catch (err) {
            App.showToast('Không thể tải thông tin hoàn thành: ' + err.message, 'error');
        }
    });

    function fireConfetti() {
        const duration = 3 * 1000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 10000 };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);
            // double confetti bursts
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    }

    function viewCertificate() {
        if (!certCode) return;
        window.open(`/certificate/verify?code=${certCode}`, '_blank');
    }

    function downloadPDF() {
        const element = document.getElementById('pdfCertCard');
        const opt = {
            margin:       0,
            filename:     `chung-chi-${certCode || 'course'}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
        };

        // Run html2pdf download
        html2pdf().set(opt).from(element).save();
    }

    async function resetLearningProgress() {
        const confirmed = await App.confirm({
            title: 'Học lại từ đầu',
            message: 'Hành động này sẽ xóa toàn bộ tiến trình học tập của khóa học này để bạn có thể học lại từ đầu. Chứng chỉ cũ và đánh giá cũng sẽ được reset. Bạn có chắc chắn muốn tiếp tục?',
            type: 'danger',
            confirmText: 'Reset ngay',
            cancelText: 'Hủy bỏ'
        });

        if (!confirmed) return;

        try {
            App.showToast('Đang thiết lập lại tiến độ...', 'info');
            const res = await window.api.post(`/student/courses/${courseId}/reset-progress`);
            localStorage.removeItem(`skip_review_${courseId}`);
            App.showToast(res.message || 'Thiết lập lại tiến độ thành công!', 'success');
            setTimeout(() => {
                window.location.replace(`/student/learning/${courseId}`);
            }, 1200);
        } catch (err) {
            App.showToast(err.message || 'Có lỗi xảy ra khi reset tiến độ.', 'error');
        }
    }
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/../layouts/footer.php';
?>
