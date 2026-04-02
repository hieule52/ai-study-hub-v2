<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - AI Study Hub</title>
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

    <!-- Hero Section (tùy chỉnh cho Register) -->
    <section class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-3 fw-bold mb-4">📝 Đăng ký tài khoản</h1>
                    <p class="fs-4 mb-4">Tham gia cộng đồng học tập thông minh ngay hôm nay! Tạo tài khoản miễn phí để khám phá AI và kết bạn.</p>
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
                        <span class="feature-icon" style="font-size: 4rem; display: block; margin-bottom: 1rem;">👤</span>
                        <h2 class="display-5 fw-bold mb-4">Tạo tài khoản mới</h2>

                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/register">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" >
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" >
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control" id="password" name="password" >
                            </div>
                            <button type="submit" class="cta-button w-100 mb-3">🎯 Đăng ký ngay</button>
                        </form>

                        <p class="text-muted">Đã có tài khoản? <a href="/login" class="text-primary fw-bold">Đăng nhập</a></p>
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