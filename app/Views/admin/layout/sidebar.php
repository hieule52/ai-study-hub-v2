<?php
$active = $active ?? 'users';
function activeClass($key, $active) { return $key === $active ? 'active' : ''; }
?>
<aside class="admin-sidebar">
  <div class="admin-brand">
    <div class="brand-icon">🛡️</div>
    <div>
      <div class="brand-title">Admin Panel</div>
      <div class="brand-sub">AI Study Hub</div>
    </div>
  </div>

  <nav class="admin-nav">
    <a class="admin-link <?= activeClass('users', $active) ?>" href="/admin/users">👥 Quản lý người dùng</a>
    <a class="admin-link <?= activeClass('vip', $active) ?>" href="/admin/vip">👑 Quản lý VIP</a>
    <a class="admin-link <?= activeClass('payments', $active) ?>" href="/admin/payments">💳 Thanh toán</a>
    <a class="admin-link <?= activeClass('settings', $active) ?>" href="/admin/settings">⚙️ Cài đặt</a>
  </nav>

  <div class="admin-bottom">
    <a class="admin-back" href="/">← Về trang chủ</a>
  </div>
</aside>
