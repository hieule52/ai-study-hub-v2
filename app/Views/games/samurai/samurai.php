<?php
require_once __DIR__ . '/../../../Controllers/VipController.php';
\App\Controllers\VipController::requireVip();

$navbar = __DIR__ . '/../../layouts/navbar.php';
$footer = __DIR__ . '/../../layouts/footer.php';
?>
<?php include $navbar; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Samurai Slash | AI Study Hub</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS chung -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/navbar.css">
  <link rel="stylesheet" href="/assets/css/home.css">
  <link rel="stylesheet" href="/assets/css/footer.css">

  <!-- CSS game -->
  <link rel="stylesheet" href="/assets/css/samurai.css">
</head>

<body>

<section class="sam-wrap">
  <div class="container">

    <div class="sam-header">
      <div>
        <h1 class="sam-title">⚔️ Samurai Slash</h1>
        <p class="sam-subtitle">Chém mục tiêu càng nhiều càng tốt trước khi hết giờ!</p>
      </div>

      <div class="sam-actions">
        <a href="/games" class="btn-exit-pill">Thoát game</a>
      </div>
    </div>

    <div class="sam-bar">
      <div class="bar-left">
        <label class="bar-label">Chế độ</label>
        <select id="modeSelect" class="form-select form-select-sm">
          <option value="30">30 giây (Chuẩn)</option>
          <option value="45">45 giây</option>
          <option value="60">60 giây (Khó)</option>
        </select>

        <button id="btnStart" class="btn btn-primary btn-sm" type="button">Bắt đầu</button>
        <button id="btnRestart" class="btn btn-outline-primary btn-sm" type="button" disabled>Chơi lại</button>
      </div>

      <div class="bar-right">
        <div class="stat"><span class="stat-label">⏳ Thời gian</span><span id="timeText" class="stat-value">--</span></div>
        <div class="stat"><span class="stat-label">🎯 Điểm</span><span id="scoreText" class="stat-value">0</span></div>
        <div class="stat"><span class="stat-label">🔥 Combo</span><span id="comboText" class="stat-value">0</span></div>
        <div class="stat"><span class="stat-label">🏆 Best</span><span id="bestText" class="stat-value">0</span></div>
      </div>
    </div>

    <div class="sam-arena-wrap">
      <div id="arena" class="sam-arena" aria-label="arena">
        <!-- overlay -->
        <div id="overlay" class="sam-overlay">
          <div class="overlay-card">
            <div class="overlay-title">Sẵn sàng?</div>
            <div class="overlay-desc">Nhấn <b>Bắt đầu</b> để chơi. Click mục tiêu để “chém”.</div>
          </div>
        </div>

        <!-- vệt chém -->
        <div id="slashLayer" class="slash-layer"></div>

        <!-- hint -->
        <div class="sam-guide">
          Tip: Click trúng liên tục để tăng <b>combo</b> (tăng điểm). Click hụt sẽ reset combo.
        </div>
      </div>

      <div class="sam-progress">
        <div id="progressBar" class="progress-bar"></div>
      </div>
    </div>

  </div>
</section>

<!-- Result Modal -->
<div class="sam-modal" id="resultModal" aria-hidden="true">
  <div class="sam-modal-card">
    <div class="modal-title" id="modalTitle">Kết thúc!</div>
    <div class="modal-desc" id="modalDesc">Bạn làm tốt lắm. Thử lại để phá kỷ lục nhé.</div>

    <div class="modal-stats">
      <div class="pill">🎯 Điểm: <span id="modalScore">0</span></div>
      <div class="pill">🔥 Max combo: <span id="modalMaxCombo">0</span></div>
      <div class="pill">🏆 Best: <span id="modalBest">0</span></div>
    </div>

    <div class="modal-actions">
      <button id="btnPlayAgain" class="btn btn-primary btn-sm" type="button">Chơi lại</button>
      <a href="/games" class="btn-exit-pill btn-exit-dark">Thoát</a>
    </div>
  </div>
</div>

<script src="/assets/js/samurai.js"></script>

<?php include $footer; ?>
</body>
</html>
