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
    <link rel="stylesheet" href="/assets/css/memory.css">
</head>

<body>

<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<section class="memory-wrap">
  <div class="container">

    <div class="memory-header">
      <div>
        <h1 class="memory-title">🧠 Memory Card</h1>
        <p class="memory-subtitle">Lật thẻ – ghép cặp – thắng trước khi hết giờ!</p>
      </div>

      <div class="memory-actions">
        <button id="btnExit" class="btn btn-outline-danger btn-sm">Thoát game</button>
      </div>
    </div>

    <!-- Control Bar -->
    <div class="memory-bar">
      <div class="bar-left">
        <label class="bar-label">Cấp độ</label>
        <select id="levelSelect" class="form-select form-select-sm">
          <option value="easy">Easy (6 cặp) - 60s</option>
          <option value="medium">Medium (8 cặp) - 75s</option>
          <option value="hard">Hard (12 cặp) - 100s</option>
        </select>

        <button id="btnStart" class="btn btn-primary btn-sm">Bắt đầu</button>
        <button id="btnRestart" class="btn btn-outline-primary btn-sm" disabled>Chơi lại</button>
      </div>

      <div class="bar-right">
        <div class="stat">
          <span class="stat-label">⏳ Thời gian</span>
          <span id="timeText" class="stat-value">--</span>
        </div>
        <div class="stat">
          <span class="stat-label">🎯 Lượt</span>
          <span id="movesText" class="stat-value">0</span>
        </div>
        <div class="stat">
          <span class="stat-label">✅ Đúng</span>
          <span id="pairsText" class="stat-value">0</span>
        </div>
      </div>
    </div>

    <!-- Game Board -->
    <div id="board" class="memory-board" aria-label="Memory board"></div>

    <div class="memory-hint">
      Tip: Chọn cấp độ rồi bấm <b>Bắt đầu</b>. Ghép đủ cặp trước khi hết thời gian để thắng.
    </div>

  </div>
</section>

<!-- Result Modal -->
<div class="memory-modal" id="resultModal" aria-hidden="true">
  <div class="memory-modal-card">
    <div class="modal-title" id="modalTitle">Kết quả</div>
    <div class="modal-desc" id="modalDesc"></div>

    <div class="modal-stats">
      <div class="pill">⏱ <span id="modalTime">0</span>s</div>
      <div class="pill">🎯 <span id="modalMoves">0</span> lượt</div>
      <div class="pill">✅ <span id="modalPairs">0</span> cặp</div>
    </div>

    <div class="modal-actions">
      <button id="btnPlayAgain" class="btn btn-primary btn-sm">Chơi lại</button>
      <a href="/games" class="btn btn-outline-secondary btn-sm">Thoát</a>
    </div>
  </div>
</div>

<script src="/assets/js/memory.js"></script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>

</body>
</html>
