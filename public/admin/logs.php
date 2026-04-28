<?php
$pageTitle = 'Audit Logs - Admin AI Study Hub';
$actor = 'admin';
ob_start();
?>
<style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; text-transform: uppercase; }
        .log-action { font-family: monospace; background: rgba(255,255,255,0.05); padding: 0.2rem 0.5rem; border-radius: 4px; color: var(--primary); }
    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-between mb-8">
                <div>
                    <h1 style="font-size: 2rem;">📋 Nhật Ký Hệ Thống (Audit Logs)</h1>
                    <p class="text-secondary mt-2">Theo dõi mọi hành vi thay đổi dữ liệu trên toàn máy chủ nhằm mục đích bảo mật.</p>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="card glass-panel" style="padding: 1.5rem; border-color: rgba(255, 255, 255, 0.1);" id="logs">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NGƯỜI THỰC HIỆN</th>
                                <th>HÀNH ĐỘNG</th>
                                <th>BẢNG DỮ LIÊU</th>
                                <th>RECORD TARGET</th>
                                <th>THỜI GIAN</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table">
                            <tr><td colspan="6" style="text-align: center; padding: 2rem;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['admin']);
            if (!user) return;
            await loadLogs();
        });

        async function loadLogs() {
            try {
                const res = await window.api.get('/admin/audit-logs');
                const tbody = document.getElementById('logs-table');
                if(!res.data || res.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">Hệ thống chưa ghi nhận log nào.</td></tr>';
                    return;
                }

                tbody.innerHTML = res.data.map(l => {
                    const dateObj = new Date(l.created_at);
                    const timeStr = dateObj.toLocaleTimeString('vi-VN') + ' ' + dateObj.toLocaleDateString('vi-VN');

                    return `
                    <tr>
                        <td style="color: var(--text-secondary); font-family: monospace;">#${l.id}</td>
                        <td>
                            <div style="font-weight: 500;">${l.email}</div>
                            <div style="font-size: 0.8rem; color:var(--text-secondary);">${l.username}</div>
                        </td>
                        <td><span class="log-action">${l.action}</span></td>
                        <td style="color: var(--warning);">${l.table_name}</td>
                        <td style="font-family: monospace;">ID: ${l.record_id}</td>
                        <td style="font-size: 0.85rem; color: var(--text-secondary);">${timeStr}</td>
                    </tr>
                `}).join('');
            } catch(e) {
                console.error("Lỗi tải audit logs", e);
                App.showToast("Không thể tải nhật ký.", "error");
            }
        }
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
