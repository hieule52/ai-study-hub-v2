<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kết nối bạn bè - AI Study Hub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css"> <!-- Để dùng .hero, .feature-card -->
    <link rel="stylesheet" href="/assets/css/footer.css">
    <link rel="stylesheet" href="/assets/css/connect-friend.css" />
</head>

<body>

    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Hero -->
    <section class="connect-hero">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="hero-title">👥 Kết nối bạn bè</h1>
                <p class="hero-subtitle">Kết nối với bạn bè cùng học tập và chia sẻ kiến thức</p>

                <!-- Search -->
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchUsers" placeholder="Tìm kiếm bạn bè..." class="search-input" />

                        <div class="search-filters">
                            <button class="filter-btn active" data-filter="all">Tất cả</button>
                            <button class="filter-btn" data-filter="friends">Bạn bè</button>
                            <button class="filter-btn" data-filter="pending">Chờ xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="friends-section">
        <div class="container">

            <!-- ==================== FRIENDS LIST ==================== -->
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-user-friends me-2"></i>
                    Bạn bè của bạn <span class="friend-count">(<?= count($friends) ?>)</span>
                </h3>
            </div>

            <div class="friends-grid">
                <?php if (empty($friends)): ?>
                    <p class="text-muted">Bạn chưa có bạn bè nào.</p>
                <?php else: ?>
                    <?php foreach ($friends as $f): ?>
                        <div class="user-card friend-card" data-status="friends">
                            <div class="user-avatar">
                                <img src="/assets/avatars/<?= htmlspecialchars($f['avatar'] ?: 'default.png') ?>" alt="">
                                <div class="online-status online"></div>
                            </div>
                            <div class="user-info">
                                <h5 class="user-name"><?= htmlspecialchars($f['username']) ?></h5>
                                <p class="user-status text-muted">Đang online</p>
                            </div>
                            <div class="user-actions">
                                <button class="action-btn message-btn"
                                    onclick="window.location.href='/chat?friend_id=<?= $f['id'] ?>'">
                                    <i class="fas fa-comment"></i>
                                </button>
                                <div class="dropdown">
                                    <button class="action-btn more-btn" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="/friend/remove" method="POST" class="d-inline">
                                                <input type="hidden" name="friend_id" value="<?= $f['id'] ?>">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-user-slash"></i> Hủy kết bạn
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ==================== SUGGESTIONS ==================== -->
            <div class="section-header mt-5">
                <h3 class="section-title">
                    <i class="fas fa-user-plus me-2"></i> Gợi ý kết bạn
                    <span class="friend-count">(<?= count($suggestions) ?>)</span>
                </h3>
            </div>

            <div class="friends-grid">
                <?php if (empty($suggestions)): ?>
                    <p class="text-muted">Không có gợi ý nào.</p>
                <?php else: ?>
                    <?php foreach ($suggestions as $s): ?>
                        <div class="user-card suggested-card" data-status="suggested">
                            <div class="user-avatar">
                                <img src="/assets/avatars/<?= htmlspecialchars($s['avatar'] ?: 'default.png') ?>" alt="">
                            </div>
                            <div class="user-info">
                                <h5 class="user-name"><?= htmlspecialchars($s['username']) ?></h5>
                                <p class="user-status text-muted">Học sinh mới</p>
                            </div>
                            <div class="user-actions">
                                <form method="POST" action="/friend/send">
                                    <input type="hidden" name="friend_id" value="<?= $s['id'] ?>">
                                    <button class="action-btn add-friend-btn">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </form>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ==================== PENDING REQUESTS ==================== -->
            <div class="section-header mt-5">
                <h3 class="section-title">
                    <i class="fas fa-clock me-2"></i> Chờ chấp nhận
                    <span class="friend-count">(<?= count($requests) ?>)</span>
                </h3>
            </div>

            <div class="friends-grid">
                <?php if (empty($requests)): ?>
                    <p class="text-muted">Không có lời mời nào.</p>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <div class="user-card pending-card" data-status="pending">
                            <div class="user-avatar">
                                <img src="/assets/avatars/<?= htmlspecialchars($r['avatar'] ?: 'default.png') ?>" />
                                <div class="pending-badge">!</div>
                            </div>
                            <div class="user-info">
                                <h5 class="user-name"><?= htmlspecialchars($r['username']) ?></h5>
                                <p class="user-status text-muted">Đã gửi lời mời kết bạn</p>
                            </div>
                            <div class="user-actions pending-actions">
                                <form action="/friend/accept" method="POST">
                                    <input type="hidden" name="friend_id" value="<?= $r['id'] ?>">
                                    <button class="action-btn accept-btn"><i class="fas fa-check"></i></button>
                                </form>
                                <form action="/friend/decline" method="POST">
                                    <input type="hidden" name="friend_id" value="<?= $r['id'] ?>">
                                    <button class="action-btn decline-btn"><i class="fas fa-times"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/connect-friend.js"></script>

</body>

</html>