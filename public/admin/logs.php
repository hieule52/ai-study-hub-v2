<?php
$pageTitle = 'Nhật ký hệ thống — Admin AI Study Hub';
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
                <h1 class="admin-page-title">Nhật ký hệ thống</h1>
                <p class="admin-page-subtitle">Theo dõi mọi thay đổi dữ liệu để đảm bảo tính toàn vẹn và bảo mật</p>
            </div>
            <div class="admin-topbar-right">
                <button onclick="loadLogs()" style="padding:0.6rem 1.25rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:10px;color:rgba(255,255,255,0.6);font-size:0.85rem;cursor:pointer;transition:all 0.2s;font-family:inherit;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.04)'">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fas fa-terminal" style="color:#818cf8;"></i>
                        Audit Logs
                    </div>
                    <span id="logCount" style="font-size:0.8rem; opacity:0.4;"></span>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NGƯỜI THỰC HIỆN</th>
                            <th>HÀNH ĐỘNG</th>
                            <th>BẢNG DỮ LIỆU</th>
                            <th>MỤC TIÊU</th>
                            <th>THỜI GIAN</th>
                        </tr>
                    </thead>
                    <tbody id="logs-table">
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
    await loadLogs();
});

async function loadLogs() {
    try {
        const res = await window.api.get('/admin/audit-logs');
        const tbody = document.getElementById('logs-table');
        const logs = res.data;
        document.getElementById('logCount').innerText = logs ? `${logs.length} bản ghi` : '';

        if (!logs || logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:3rem;opacity:0.4;">Hệ thống chưa ghi nhận log nào.</td></tr>';
            return;
        }

        const actionColors = {
            'INSERT': '#10b981', 'CREATE': '#10b981',
            'UPDATE': '#f59e0b', 'EDIT': '#f59e0b',
            'DELETE': '#ef4444', 'REMOVE': '#ef4444',
        };

        tbody.innerHTML = logs.map(l => {
            const dateObj = new Date(l.created_at);
            const timeStr = isNaN(dateObj) ? l.created_at : dateObj.toLocaleString('vi-VN');
            const actionUpper = (l.action || '').toUpperCase();
            const actionColor = actionColors[actionUpper] || '#818cf8';

            return `
                <tr>
                    <td style="opacity:0.3; font-size:0.78rem; font-family:monospace;">#${l.id}</td>
                    <td>
                        <div style="font-weight:600; color:#fff;">${l.email || '—'}</div>
                        <div style="font-size:0.78rem; opacity:0.4; margin-top:2px;">${l.username || ''}</div>
                    </td>
                    <td>
                        <span style="font-family:monospace; font-size:0.78rem; font-weight:700; color:${actionColor}; background:${actionColor}18; padding:3px 8px; border-radius:6px; border:1px solid ${actionColor}30;">
                            ${l.action}
                        </span>
                    </td>
                    <td style="font-family:monospace; font-size:0.82rem; color:#f59e0b;">${l.table_name}</td>
                    <td style="font-family:monospace; font-size:0.8rem; opacity:0.5;">ID: ${l.record_id}</td>
                    <td style="font-size:0.78rem; opacity:0.4;">${timeStr}</td>
                </tr>
            `;
        }).join('');
    } catch(e) {
        console.error(e);
        App.showToast('Không thể tải nhật ký.', 'error');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
