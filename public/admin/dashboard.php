<?php
$pageTitle = 'Admin Dashboard — AI Study Hub';
$actor = 'admin';
$noSidebar = true;
$extraHead = '
    <link rel="stylesheet" href="/assets/css/admin/layout.css?v=' . time() . '">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="admin-body">
    <?php require __DIR__ . '/../layouts/admin_sidebar.php'; ?>

    <div class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="admin-topbar-left">
                <h1 class="admin-page-title">Bảng điều khiển</h1>
                <p class="admin-page-subtitle">Dữ liệu hệ thống cập nhật theo thời gian thực</p>
            </div>
            <div class="admin-topbar-right">
                <div class="admin-user-pill">
                    <div class="admin-user-avatar" id="adminAvatarInitial">A</div>
                    <span class="admin-user-name" id="adminUserName">Admin</span>
                    <span class="admin-user-role">ADMIN</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">

            <!-- Stats Grid -->
            <div class="admin-stats-grid">
                <div class="admin-stat-card" style="--card-glow: rgba(99,102,241,0.06);">
                    <div class="admin-stat-icon" style="background: rgba(99,102,241,0.12); color: #818cf8;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="admin-stat-label">Tổng doanh thu ghi danh</div>
                    <div class="admin-stat-value" id="s-revenue" style="color: #818cf8;">—</div>
                </div>

                <div class="admin-stat-card" style="--card-glow: rgba(16,185,129,0.06);">
                    <div class="admin-stat-icon" style="background: rgba(16,185,129,0.12); color: #10b981;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="admin-stat-label">Khóa học hoạt động</div>
                    <div class="admin-stat-value" id="s-vip" style="color: #10b981;">—</div>
                </div>

                <div class="admin-stat-card" style="--card-glow: rgba(16,185,129,0.06);">
                    <div class="admin-stat-icon" style="background: rgba(16,185,129,0.12); color: #10b981;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-label">Tổng người dùng</div>
                    <div class="admin-stat-value" id="s-users" style="color: #10b981;">—</div>
                </div>

                <div class="admin-stat-card"
                    style="--card-glow: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.15);">
                    <div class="admin-stat-icon" style="background: rgba(239,68,68,0.12); color: #ef4444;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-label">Khóa học chờ duyệt</div>
                    <div class="admin-stat-value" id="s-pending" style="color: #ef4444;">—</div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fas fa-chart-area" style="color: #818cf8;"></i>
                        Biểu đồ Lượt Ghi Danh theo Tháng
                    </div>
                    <span style="font-size: 0.75rem; opacity: 0.4;">6 tháng gần nhất</span>
                </div>
                <div class="admin-chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem;">
                <a href="/admin/courses.php"
                    style="display:flex; align-items:center; gap:1rem; padding:1.25rem 1.5rem; background:rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.15); border-radius:16px; text-decoration:none; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-3px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div
                        style="width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,0.12);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                        <i class="fas fa-book-open"></i></div>
                    <div>
                        <div style="font-weight:800;color:#fff;font-size:0.9rem;">Duyệt khóa học</div>
                        <div style="font-size:0.75rem;opacity:0.4;margin-top:2px;">Xem & phê duyệt</div>
                    </div>
                </a>

                <a href="/admin/users.php"
                    style="display:flex; align-items:center; gap:1rem; padding:1.25rem 1.5rem; background:rgba(99,102,241,0.05); border:1px solid rgba(99,102,241,0.15); border-radius:16px; text-decoration:none; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-3px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div
                        style="width:44px;height:44px;border-radius:12px;background:rgba(99,102,241,0.12);color:#818cf8;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                        <i class="fas fa-users-cog"></i></div>
                    <div>
                        <div style="font-weight:800;color:#fff;font-size:0.9rem;">Quản lý người dùng</div>
                        <div style="font-size:0.75rem;opacity:0.4;margin-top:2px;">Phân quyền tài khoản</div>
                    </div>
                </a>

                <a href="/admin/vip.php"
                    style="display:flex; align-items:center; gap:1rem; padding:1.25rem 1.5rem; background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.15); border-radius:16px; text-decoration:none; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-3px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div
                        style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,0.12);color:#10b981;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                        <i class="fas fa-receipt"></i></div>
                    <div>
                        <div style="font-weight:800;color:#fff;font-size:0.9rem;">Doanh thu ghi danh</div>
                        <div style="font-size:0.75rem;opacity:0.4;margin-top:2px;">Lịch sử & thống kê</div>
                    </div>
                </a>
            </div>

        </div><!-- .admin-content -->
    </div><!-- .admin-main -->
</div><!-- .admin-body -->

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['admin']);
        if (!user) return;

        // Set username
        const name = user.username || user.email?.split('@')[0] || 'Admin';
        document.getElementById('adminUserName').innerText = name;
        document.getElementById('adminAvatarInitial').innerText = name.charAt(0).toUpperCase();

        await loadStats();
        await loadChart();
    });

    async function loadStats() {
        try {
            const res = await window.api.get('/admin/stats');
            const d = res.data;
            document.getElementById('s-revenue').innerText = (d.total_revenue ?? 0).toLocaleString('vi-VN') + ' lượt';
            document.getElementById('s-vip').innerText = d.total_vip_users ?? 0;
            document.getElementById('s-users').innerText = d.total_users ?? 0;
            document.getElementById('s-pending').innerText = d.pending_courses ?? 0;
        } catch (e) { console.error(e); }
    }

    async function loadChart() {
        try {
            const res = await window.api.get('/admin/chart-data');
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: res.data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: 'rgba(255,255,255,0.5)', font: { size: 12 } } }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: 'rgba(255,255,255,0.3)',
                                callback: v => v + ' lượt',
                                stepSize: 1
                            },
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            border: { color: 'transparent' }
                        },
                        x: {
                            ticks: { color: 'rgba(255,255,255,0.3)' },
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            border: { color: 'transparent' }
                        }
                    }
                }
            });
        } catch (e) { console.error(e); }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>