<?php
$pageTitle = 'Admin Dashboard - AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; }
        .badge-role { padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .role-admin { background: rgba(236, 72, 153, 0.1); color: var(--secondary); border: 1px solid var(--secondary); }
        .role-teacher { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid var(--success); }
        .role-student { background: rgba(79, 70, 229, 0.1); color: var(--primary); border: 1px solid var(--primary); }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;">Bảng Điều Khiển Trung Tâm</h1>
                    <p class="text-secondary mt-2">Dữ liệu theo dõi thời gian thực của toàn bộ hệ thống (REST API v2).</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid-cols-4 mb-8" id="stats-container">
                <div class="card p-4">
                    <p class="text-secondary" style="font-size: 0.875rem;">Tổng Doanh Thu</p>
                    <h2 id="s-revenue" style="font-size: 1.75rem; color: var(--warning); margin-top: 0.5rem;">0đ</h2>
                </div>
                <div class="card p-4">
                    <p class="text-secondary" style="font-size: 0.875rem;">Tài khoản VIP</p>
                    <h2 id="s-vip" style="font-size: 1.75rem; color: var(--primary); margin-top: 0.5rem;">0</h2>
                </div>
                <div class="card p-4">
                    <p class="text-secondary" style="font-size: 0.875rem;">Tổng Người Dùng</p>
                    <h2 id="s-users" style="font-size: 1.75rem; color: var(--text-primary); margin-top: 0.5rem;">0</h2>
                </div>
                <div class="card p-4" style="border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05);">
                    <p class="text-secondary" style="font-size: 0.875rem;">Khóa học chờ duyệt</p>
                    <h2 id="s-pending" style="font-size: 1.75rem; color: var(--danger); margin-top: 0.5rem;">0</h2>
                </div>
            </div>

            <!-- Pending Courses (Removed from dashboard, moved to courses.php) -->

        <!-- Chart Section -->
            <div class="card glass-panel" style="padding: 1.5rem;" id="charts">
                <div class="flex justify-between items-center mb-4">
                    <h3>📊 Biểu đồ Doanh Thu Khóa Học Phân Bổ</h3>
                </div>
                <div style="height: 400px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['admin']);
            if (!user) return;

            await loadStats();
            await loadChart();
        });

        async function loadStats() {
            try {
                const res = await window.api.get('/admin/stats');
                const data = res.data;
                document.getElementById('s-revenue').innerText = new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(data.total_revenue);
                document.getElementById('s-vip').innerText = data.total_vip_users;
                document.getElementById('s-users').innerText = data.total_users;
                document.getElementById('s-pending').innerText = data.pending_courses;
            } catch(e) {
                console.error("Lỗi tải thống kê", e);
            }
        }

        // Removed pending courses functions to courses.php



        async function loadChart() {
            try {
                const res = await window.api.get('/admin/chart-data');
                const chartData = res.data;
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#a0a0a0' }
                            }
                        },
                        scales: {
                            y: {
                                ticks: { color: '#a0a0a0', callback: function(value) { return new Intl.NumberFormat('vi-VN').format(value) + 'đ'; } },
                                grid: { color: 'rgba(255,255,255,0.05)' }
                            },
                            x: {
                                ticks: { color: '#a0a0a0' },
                                grid: { color: 'rgba(255,255,255,0.05)' }
                            }
                        }
                    }
                });
            } catch(e) {
                console.error("Lỗi tải biểu đồ", e);
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
