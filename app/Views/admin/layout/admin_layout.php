<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<body>

<div class="admin-wrap">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <div class="title">
        <div class="badge-icon">🛡️</div>
        <div>
          <div class="h5 m-0">Admin Dashboard</div>
          <div class="text-muted small">
            Chào mừng, <b><?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?></b>
          </div>
        </div>
      </div>
      <a class="btn btn-outline-secondary btn-sm" href="/">Về trang chủ</a>
    </div>

    <div class="admin-content">
      <?php
        if (isset($contentView) && file_exists($contentView)) {
          include $contentView;
        } else {
          echo "<div class='alert alert-danger'>Không tìm thấy view: " . htmlspecialchars($contentView ?? '') . "</div>";
        }
      ?>
    </div>
  </main>
</div>

</body>
</html>
