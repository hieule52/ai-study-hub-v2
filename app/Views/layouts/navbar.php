<nav class="navbar navbar-expand-lg header">
    <div class="container">
        <a class="navbar-brand logo" href="/">
            <span class="emoji">🤖</span>
            <span class="text">AI Study Hub</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">🏠 Trang chủ</a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Chỉ hiển thị khi đã đăng nhập -->
                    <li class="nav-item">
                        <a class="nav-link" href="/assistantai">💬 Trợ lý AI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/friends">👥 Bạn bè</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/games">🎮 Giải trí</a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link" href="/about">ℹ️ Giới thiệu</a>
                </li>
                
                <?php if ((int)($_SESSION['is_admin'] ?? 0) === 1): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/management">🔧 Quản lý</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['username'])): ?>
                    <!-- Đã login: Hiển thị username + logout -->
                    <div class="dropdown me-2">
                        <a class="nav-link dropdown-toggle login-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            👋 Chào <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile">👤 Hồ sơ</a></li> <!-- Tùy chọn, nếu có route profile -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="/logout">🚪 Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Chưa login: Đăng nhập + Đăng ký -->
                    <a href="/login" class="login-btn me-2">🔐 Đăng nhập</a>
                    <a href="/register" class="btn btn-outline-primary">📝 Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>