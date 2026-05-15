<?php
$pageTitle = 'Doanh thu ghi danh — Admin AI Study Hub';
$actor = 'admin';
$noSidebar = true;
$extraHead = '
    <link rel="stylesheet" href="/assets/css/admin/layout.css?v=' . time() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-body">
    <?php require __DIR__ . '/../layouts/admin_sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <h1 class="admin-page-title">Doanh thu ghi danh</h1>
                <p class="admin-page-subtitle">Giám sát lịch sử ghi danh và học phí của toàn bộ học viên</p>
            </div>
        </div>

        <div class="admin-content">
            <!-- Summary Grid -->
            <div class="admin-stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 2rem;">
                <div class="admin-stat-card" style="--card-glow: rgba(16,185,129,0.08);">
                    <div class="admin-stat-icon" style="background: rgba(16,185,129,0.12); color: #10b981;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="admin-stat-label">Tổng doanh thu ghi danh</div>
                    <div class="admin-stat-value" id="totalRevenue" style="color: #10b981;">—</div>
                </div>

                <div class="admin-stat-card" style="--card-glow: rgba(129,140,248,0.08);">
                    <div class="admin-stat-icon" style="background: rgba(129,140,248,0.12); color: #818cf8;">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <div class="admin-stat-label">Tổng lượt ghi danh</div>
                    <div class="admin-stat-value" id="totalCount" style="color: #818cf8;">—</div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fas fa-receipt" style="color:#10b981;"></i>
                        Lịch sử ghi danh
                    </div>
                    <span id="enrollCount" style="font-size:0.8rem; opacity:0.4;"></span>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>GHI DANH</th>
                            <th>HỌC VIÊN</th>
                            <th>KHÓA HỌC</th>
                            <th>HỌC PHÍ</th>
                            <th>THỜI GIAN</th>
                            <th>TIẾN ĐỘ</th>
                        </tr>
                    </thead>
                    <tbody id="vip-table">
                        <tr><td colspan="6" style="text-align:center;padding:3rem;opacity:0.4;">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
        const { items, total_revenue } = res.data;
        
        document.getElementById('totalRevenue').innerText = new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(total_revenue);
        document.getElementById('totalCount').innerText   = (items.length).toLocaleString('vi-VN') + ' lượt';
        document.getElementById('enrollCount').innerText  = `${items.length} bản ghi`;

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:3rem;opacity:0.4;">Chưa có lượt ghi danh nào.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(e => {
            const price = e.course_price > 0
                ? `<span style="color:#10b981;font-weight:700;">${new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(e.course_price)}</span>`
                : '<span style="opacity:0.4;">Miễn phí</span>';

            const pct = e.progress_percent || 0;
            const barColor = pct >= 100 ? '#10b981' : pct > 50 ? '#818cf8' : 'var(--primary)';

            return `
                <tr>
                    <td style="opacity:0.4; font-size:0.8rem;">#ENR_${e.id}</td>
                    <td>
                        <div style="font-weight:600; color:#fff;">${e.student_name}</div>
                        <div style="font-size:0.78rem; opacity:0.4;">${e.student_email}</div>
                    </td>
                    <td>
                        <div style="font-weight:500;">${e.course_title}</div>
                        <div style="font-size:0.78rem; opacity:0.4; margin-top:2px;">GV: ${e.teacher_name || 'Hệ thống'}</div>
                    </td>
                    <td>${price}</td>
                    <td style="font-size:0.8rem; opacity:0.5;">${new Date(e.enrolled_at).toLocaleString('vi-VN')}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div style="flex:1; height:5px; background:rgba(255,255,255,0.06); border-radius:10px; overflow:hidden; min-width:80px;">
                                <div style="width:${pct}%; height:100%; background:${barColor}; border-radius:10px; transition:width 0.5s;"></div>
                            </div>
                            <span style="font-size:0.78rem; font-weight:700; color:${barColor}; min-width:30px;">${pct}%</span>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } catch(e) { console.error(e); }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
