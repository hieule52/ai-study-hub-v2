<?php
// app/Views/games/speed/speed.php
$navbar = __DIR__ . '/../../layouts/navbar.php';
$footer = __DIR__ . '/../../layouts/footer.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Click Speed | AI Study Hub</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS chung -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/navbar.css">
  <link rel="stylesheet" href="/assets/css/home.css">
  <link rel="stylesheet" href="/assets/css/footer.css">

  <!-- CSS game -->
  <link rel="stylesheet" href="/assets/css/speed.css">
</head>
<body>

<?php include $navbar; ?>

<section class="speed-wrap">
  <div class="container">

    <div class="speed-header">
      <div>
        <h1 class="speed-title">🎯 Click Speed</h1>
        <p class="speed-subtitle">Click càng nhiều càng tốt trong thời gian giới hạn!</p>
      </div>

      <div class="speed-actions">
        <button id="btnExit" class="btn-exit-pill" type="button">Thoát game</button>
      </div>
    </div>

    <div class="speed-bar">
      <div class="bar-left">
        <label class="bar-label">Chế độ</label>
        <select id="modeSelect" class="form-select form-select-sm">
          <option value="5">5 giây (Chuẩn)</option>
          <option value="10">10 giây</option>
          <option value="15">15 giây</option>
        </select>

        <!-- dùng pill cho đẹp + dễ đồng bộ -->
        <button id="btnStart" class="speed-btn-pill speed-btn-primary" type="button">Bắt đầu</button>
        <button id="btnRestart" class="speed-btn-pill" type="button" disabled>Chơi lại</button>
      </div>

      <div class="bar-right">
        <div class="stat">
          <span class="stat-label">⏳ Thời gian</span>
          <span id="timeText" class="stat-value">--</span>
        </div>
        <div class="stat">
          <span class="stat-label">🖱 Click</span>
          <span id="clickText" class="stat-value">0</span>
        </div>
        <div class="stat">
          <span class="stat-label">⚡ CPS</span>
          <span id="cpsText" class="stat-value">0.00</span>
        </div>
        <div class="stat">
          <span class="stat-label">🏆 Best</span>
          <span id="bestText" class="stat-value">0</span>
        </div>
      </div>
    </div>

    <div class="speed-arena-wrap">
      <div id="arena" class="speed-arena">
        <!-- overlay (mặc định hiện) -->
        <div id="overlay" class="speed-overlay">
          <div class="overlay-card">
            <div id="overlayTitle" class="overlay-title">Sẵn sàng?</div>
            <div id="overlayDesc" class="overlay-desc">Nhấn <b>Bắt đầu</b> để chơi.</div>
          </div>
        </div>

        <!-- mục tiêu -->
        <button id="target" class="speed-target" type="button" aria-label="target"></button>

        <div id="guide" class="speed-guide">
          Nhấn <b>Bắt đầu</b> → chờ mục tiêu xuất hiện → click nhanh nhất có thể!
        </div>
      </div>

      <div class="speed-progress">
        <div id="progressBar" class="progress-bar"></div>
      </div>
    </div>

  </div>
</section>

<!-- Result Modal -->
<div class="speed-modal" id="resultModal" aria-hidden="true">
  <div class="speed-modal-card">
    <div class="modal-title" id="modalTitle">Kết thúc!</div>
    <div class="modal-desc" id="modalDesc">Cố lên! Thử lại để phá kỷ lục nhé.</div>

    <div class="modal-stats">
      <div class="pill">🖱 <span id="modalClicks">0</span> click</div>
      <div class="pill">⚡ <span id="modalCps">0.00</span> CPS</div>
      <div class="pill">🏆 Best: <span id="modalBest">0</span></div>
    </div>

    <div class="modal-actions">
      <button id="btnPlayAgain" class="speed-btn-pill speed-btn-primary" type="button">Chơi lại</button>
      <a href="/games" class="btn-exit-pill" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Thoát</a>
    </div>
  </div>
</div>

<script src="/assets/js/speed.js"></script>

<?php include $footer; ?>
</body>
</html>
