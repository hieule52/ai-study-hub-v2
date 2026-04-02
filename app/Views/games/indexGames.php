<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Hub - Giải trí</title> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <link rel="stylesheet" href="/assets/css/games.css">
</head>

<?php
$isVip = false;
if (!empty($_SESSION['user_id'])) {
  require_once __DIR__ . '/../../Controllers/VipController.php';
  $isVip = \App\Controllers\VipController::isVip((int)$_SESSION['user_id']);
}
?>

<body>

<?php include __DIR__ . '/../layouts/navbar.php'; ?>

<!-- HERO -->
<section class="games-hero">
    <h1>AI Learning Games</h1>
    <p>Học tập qua trò chơi – vừa vui vừa ghi nhớ</p>

    <div class="stats">
        <div>
            <strong>4</strong>
            <span>Games</span>
        </div>
        <div>
            <strong>3</strong>
            <span>Miễn phí</span>
        </div>
        <div>
            <strong>1</strong>
            <span>VIP</span>
        </div>
    </div>
</section>

<section class="games-filter">
    <button class="active" data-filter="all">Tất cả</button>
    <button data-filter="memory">Trí nhớ</button>
    <button data-filter="logic">Logic</button>
    <button data-filter="reaction">Phản xạ</button>
    <button data-filter="action">Hành động</button>
    <button data-filter="vip" class="vip">VIP</button>
</section>

<!-- GAME LIST -->
<section class="games-list" id="gamesList">

    <!-- MEMORY -->
    <div class="game-card" data-category="memory free">
        <div class="game-thumb">
            <img src="/assets/images/games/memory.jpg" alt="Memory Card">
        </div>
        <div class="game-body">
            <h3>🧠 Memory Card</h3>
            <p>Rèn luyện trí nhớ ngắn hạn</p>

            <div class="meta">
                <span class="tag easy">Easy</span>
                ⭐
            </div>

            <a href="/games/memory" class="btn play">▶ Chơi ngay</a>

        </div>
    </div>

    <!-- MATH QUIZ -->
    <div class="game-card" data-category="logic free">
        <div class="game-thumb">
            <img src="/assets/images/games/math.png" alt="Memory Card">
        </div>        
        <div class="game-body">
            <h3>🧮 Math Quiz</h3>
            <p>Rèn tư duy logic & tính toán</p>

            <div class="meta">
                <span class="tag medium">Medium</span>
                ⭐⭐
            </div>

            <a href="/games/math" class="btn play">▶ Chơi ngay</a>
        </div>
    </div>

    <!-- CLICK SPEED -->
    <div class="game-card" data-category="reaction free">
        <div class="game-thumb">
            <img src="/assets/images/games/click.jpg" alt="Memory Card">
        </div>        
        <div class="game-body">
            <h3>🎯 Click Speed</h3>
            <p>Thử thách phản xạ trong 5 giây</p>

            <div class="meta">
                <span class="tag medium">Medium</span>
                ⭐⭐
            </div>

            <a href="/games/speed" class="btn play">▶ Chơi ngay</a>
        </div>
    </div>

    <!-- SAMURAI (VIP) -->
    <div class="game-card vip" data-category="action vip">
        <span class="badge">VIP</span>
        <div class="game-thumb">
            <img src="/assets/images/games/samurai.jpg" alt="Memory Card">
        </div>
        <div class="game-body">
            <h3>⚔️ Samurai Slash</h3>
            <p>Chém mục tiêu bằng chuột</p>

            <div class="meta">
                <span class="tag hard">Hard</span>
                ⭐⭐⭐
            </div>

            <?php if ($isVip): ?>
                <a href="/games/samurai" class="btn play">▶ Chơi ngay</a>
            <?php else: ?>
                <a href="/vip/upgrade" class="btn vip-upgrade">🔒 Nâng cấp VIP</a>
            <?php endif; ?>


        </div>
    </div>

</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
const buttons = document.querySelectorAll('.games-filter button');
const cards = document.querySelectorAll('.game-card');

buttons.forEach(btn => {
    btn.addEventListener('click', () => {

        // active button
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filter = btn.dataset.filter;

        cards.forEach(card => {
            const categories = card.dataset.category || '';

            if (filter === 'all' || categories.includes(filter)) {
                card.classList.remove('hide');
            } else {
                card.classList.add('hide');
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
