<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - AI Study Hub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css"> <!-- Để dùng .hero và .cta-button -->
    <link rel="stylesheet" href="/assets/css/footer.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Hero Section (tùy chỉnh cho Login) -->
    <section class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-3 fw-bold mb-4">🔐 Đăng nhập nhanh chóng</h1>
                    <p class="fs-4 mb-4">Trở lại với thế giới học tập AI! Đăng nhập để tiếp tục hành trình của bạn.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-5" style="background: white;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="feature-card h-100 text-center p-4"> <!-- Giống feature-card ở home -->
                        <span class="feature-icon" style="font-size: 4rem; display: block; margin-bottom: 1rem;">🚀</span>
                        <h2 class="display-5 fw-bold mb-4">Chào mừng trở lại</h2>

                        <!-- Success message từ register -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success mb-4">
                                <?php echo $_SESSION['success'];
                                unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Error message -->
                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/login">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <button type="submit" class="cta-button w-100 mb-3">💬 Đăng nhập</button>
                        </form>

                        <p class="text-muted">Chưa có tài khoản? <a href="/register" class="text-primary fw-bold">Đăng ký miễn phí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>