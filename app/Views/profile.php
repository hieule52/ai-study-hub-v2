<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AI Study Hub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css"> <!-- Để dùng .hero, .feature-card -->
    <link rel="stylesheet" href="/assets/css/footer.css">
</head>

<body>
    <?php include __DIR__ . '/layouts/navbar.php'; ?>

    <!-- Hero Section cho Profile -->
    <section class="hero py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">👤 Hồ sơ cá nhân</h1>
                    <p class="fs-5 mb-4">Quản lý thông tin của bạn và kết nối với bạn bè.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="py-5 pb-5" style="background: white;">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-10">

                    <!-- Flash messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success mb-4">
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Main layout: 2 columns -->
                    <div class="row g-4 d-flex">
                        <!-- Left: Avatar + Info + Quick links -->
                        <div class="col-lg-6">
                            <div class="feature-card text-center p-4 h-100 d-flex flex-column">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img
                                        id="current-avatar"
                                        src="/assets/avatars/<?php echo htmlspecialchars($user['avatar']); ?>"
                                        alt="Avatar"
                                        class="rounded-circle mb-3 mx-auto"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div
                                        class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center bg-secondary text-white"
                                        style="width: 150px; height: 150px; font-size: 4rem;">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>

                                <h2 class="h3 fw-bold mb-1">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </h2>
                                <p class="mb-1 text-muted">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </p>
                                <p class="mb-1 text-muted">
                                    Thành viên từ:
                                    <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                </p>

                                <!-- Hiển thị Vai trò (Admin hoặc User) -->
                                <p class="mb-1 fw-bold text-primary">
                                    👑 Vai trò:
                                    <span class="badge <?php echo ($user['is_admin'] ?? 0) == 1 ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                                        <?php echo ($user['is_admin'] ?? 0) == 1 ? 'Admin' : 'User'; ?>
                                    </span>
                                </p>

                                <!-- Hiển thị Trạng thái (VIP hoặc Thành viên thường) -->
                                <p class="mb-1 fw-bold text-info">
                                    ⭐ Trạng thái:
                                    <span class="badge <?php echo ($user['is_vip'] ?? 0) == 1 ? 'bg-info text-white' : 'bg-secondary'; ?>">
                                        <?php echo ($user['is_vip'] ?? 0) == 1 ? 'VIP' : 'Thành viên thường'; ?>
                                    </span>
                                </p>

                                <p class="fw-bold fs-5 mb-4">
                                    👥 Số bạn bè: <?php echo $user['friend_count'] ?? 0; ?>
                                </p>

                                <!-- Quick Links -->
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="/friends" class="btn btn-outline-primary">
                                            👥 Xem bạn bè
                                        </a>
                                        <a href="/assistantai" class="btn btn-outline-success">
                                            🤖 Trợ lý AI
                                        </a>
                                        <a href="/games" class="btn btn-outline-warning">
                                            🎮 Chơi game
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Edit Form -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h3 class="mb-0">Chỉnh sửa thông tin</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/profile" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="username" class="form-label fw-bold">Username</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="username"
                                                    name="username"
                                                    value="<?php echo htmlspecialchars($user['username']); ?>"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label fw-bold">Email</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="email"
                                                    name="email"
                                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                                    required>
                                            </div>
                                        </div>

                                        <!-- Avatar upload -->
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label fw-bold">
                                                Avatar mới (JPG, PNG, GIF - tối đa 2MB)
                                            </label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="avatar"
                                                name="avatar"
                                                accept="image/jpeg,image/png,image/gif">
                                            <img
                                                id="preview-avatar"
                                                class="mt-2 rounded-circle"
                                                style="width: 100px; height: 100px; object-fit: cover; display: none;"
                                                alt="Preview">
                                            <small class="text-muted d-block mt-1">
                                                Avatar hiện tại:
                                                <?php echo htmlspecialchars($user['avatar'] ?? 'Không có'); ?>
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            💾 Cập nhật
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row g-4 -->

                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/layouts/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Preview Avatar -->
    <script>
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-avatar');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>