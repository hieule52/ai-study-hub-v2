<?php
$pageTitle = 'Quản lý Ghi danh - Admin AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
    .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
    .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; text-transform: uppercase; }
    .badge { padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
    .b-completed { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); }
    .b-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3); }
    .b-failed { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3); }
</style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 style="font-size: 2rem;">💎 Lịch Sử Ghi Danh</h1>
        <p class="text-secondary mt-2">Giám sát doanh thu và các lượt đăng ký khóa học theo thời gian thực.</p>
    </div>
</div>

<div class="card glass-panel" style="padding: 1.5rem; border-color: rgba(99, 102, 241, 0.2);" id="vip">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID GHI DANH</th>
                    <th>HỌC VIÊN</th>
                    <th>KHÓA HỌC</th>
                    <th>PHÍ GHI DANH</th>
                    <th>THỜI GIAN</th>
                    <th>TIẾN ĐỘ</th>
                </tr>
            </thead>
            <tbody id="vip-table">
                <tr><td colspan="5" style="text-align: center; padding: 2rem;">Đang tải dữ liệu...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['admin']);
        if (!user) return;
        await loadEnrollments();
    });

    async function loadEnrollments() {
        try {
            const res = await window.api.get('/admin/enrollments');
            const tbody = document.getElementById('vip-table');
            if(res.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">Chưa có lượt ghi danh nào.</td></tr>';
                return;
            }

            tbody.innerHTML = res.data.map(e => `
                <tr>
                    <td style="color:var(--text-secondary);">#ENR_${e.id}</td>
                    <td style="font-weight: 500;">
                        ${e.student_name}
                        <div style="font-size: 0.8rem; color:var(--text-secondary); margin-top: 4px;">${e.student_email}</div>
                    </td>
                    <td>
                        <div style="font-weight: 500;">${e.course_title}</div>
                        <div style="font-size: 0.8rem; color:var(--text-secondary); margin-top: 4px;">GV: ${e.teacher_name || 'Hệ thống'}</div>
                    </td>
                    <td style="color:var(--warning); font-weight:bold;">
                        ${e.course_price > 0 ? new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(e.course_price) : 'Miễn phí'}
                    </td>
                    <td style="color:var(--text-secondary); font-size: 0.875rem;">${new Date(e.enrolled_at).toLocaleString('vi-VN')}</td>
                    <td>
                        <div class="progress-bar-container" style="width: 100px; height: 6px; background: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; margin-bottom: 4px;">
                            <div style="width: ${e.progress_percent}%; height: 100%; background: var(--primary); border-radius: 4px;"></div>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">${e.progress_percent}%</span>
                    </td>
                </tr>
            `).join('');
        } catch(e) {
            console.error("Lỗi lấy dữ liệu:", e);
            App.showToast("Không thể lấy danh sách ghi danh.", "error");
        }
    }
</script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
