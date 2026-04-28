<?php
$pageTitle = 'Học viên của tôi - AI Study Hub';
$actor = 'teacher';
ob_start();
?>
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; text-transform: uppercase;}
        
        .progress-bar-bg { width: 100%; height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; margin-top: 0.5rem; }
        .progress-bar-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;">Danh Sách Học Viên</h1>
                    <p class="text-secondary mt-2">Theo dõi tiến độ học tập của các học viên đã đăng ký khóa học của bạn.</p>
                </div>
            </div>

            <div class="card glass-panel" style="padding: 1.5rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>HỌC VIÊN</th>
                            <th>EMAIL</th>
                            <th>KHÓA HỌC</th>
                            <th>TIẾN ĐỘ</th>
                            <th>NGÀY ĐĂNG KÝ</th>
                        </tr>
                    </thead>
                    <tbody id="students-table">
                        <tr><td colspan="5" style="text-align: center;">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['teacher', 'admin']);
            if (!user) return;

            try {
                const res = await window.api.get('/teacher/students');
                const students = res.data;
                const tbody = document.getElementById('students-table');

                if (students.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Chưa có học viên nào đăng ký.</td></tr>';
                    return;
                }

                tbody.innerHTML = students.map(s => `
                    <tr>
                        <td style="font-weight: 500;">
                            <div class="flex items-center gap-2">
                                <span style="font-size: 1.5rem;">👤</span>
                                ${s.username}
                            </div>
                        </td>
                        <td>${s.email}</td>
                        <td><a href="/student/learning.php?course_id=${s.course_id}" target="_blank" style="color:var(--primary)" title="Xem trước khóa học">${s.course_title}</a></td>
                        <td style="min-width: 150px;">
                            <span style="font-weight:bold; color: ${s.progress_percent >= 100 ? 'var(--success)' : 'var(--text-primary)'}">${s.progress_percent}%</span>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${s.progress_percent}%; ${s.progress_percent>=100 ? 'background: var(--success)' : ''}"></div>
                            </div>
                        </td>
                        <td class="text-secondary">${new Date(s.enrolled_at).toLocaleDateString('vi-VN')}</td>
                    </tr>
                `).join('');

            } catch (err) {
                console.error(err);
                App.showToast('Lỗi tải danh sách học viên', 'error');
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
