<?php
// app/Views/games/math/math.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Math Quiz | AI Study Hub</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS chung -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/navbar.css">
  <link rel="stylesheet" href="/assets/css/home.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/games.css">

  <!-- CSS game -->
  <link rel="stylesheet" href="/assets/css/math.css">
</head>
<body>

<?php include __DIR__ . '/../../layouts/navbar.php'; ?>

<section class="mq-wrap">
  <div class="container">

    <div class="mq-header">
      <div>
        <h1 class="mq-title">🧮 Math Quiz</h1>
        <p class="mq-subtitle">Trả lời nhanh – đúng nhiều – thắng trước khi hết giờ!</p>
      </div>

      <div class="mq-actions">
        <button id="btnExit" class="btn btn-outline-danger btn-sm">Thoát game</button>
      </div>
    </div>

    <div class="mq-bar">
      <div class="bar-left">
        <label class="bar-label">Cấp độ</label>
        <select id="levelSelect" class="form-select form-select-sm">
          <option value="easy">Easy (10 câu) - 60s</option>
          <option value="medium">Medium (12 câu) - 75s</option>
          <option value="hard">Hard (15 câu) - 100s</option>
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
          <span class="stat-label">📌 Câu</span>
          <span id="qText" class="stat-value">0/0</span>
        </div>
        <div class="stat">
          <span class="stat-label">✅ Đúng</span>
          <span id="correctText" class="stat-value">0</span>
        </div>
        <div class="stat">
          <span class="stat-label">⭐ Điểm</span>
          <span id="scoreText" class="stat-value">0</span>
        </div>
      </div>
    </div>

    <div class="mq-card">
      <div class="mq-question">
        <div class="mq-qtitle">Câu hỏi</div>
        <div id="questionText" class="mq-qtext">Chọn cấp độ rồi bấm <b>Bắt đầu</b>.</div>
      </div>

      <div class="mq-answer">
        <div class="mq-qtitle">Trả lời</div>

        <div class="mq-input-row">
          <input id="answerInput" type="number" class="form-control" placeholder="Nhập đáp án..." disabled>
          <button id="btnSubmit" class="btn btn-success" disabled>Trả lời</button>
        </div>

        <div class="mq-choices" id="choices">
          <!-- 4 lựa chọn (JS render) -->
        </div>

        <div id="feedback" class="mq-feedback"></div>
      </div>
    </div>

    <div class="mq-hint">
      Tip: Bạn có thể <b>nhập đáp án</b> hoặc <b>chọn 1 trong 4 đáp án</b>.
    </div>

  </div>
</section>

<!-- Result Modal -->
<div class="mq-modal" id="resultModal" aria-hidden="true">
  <div class="mq-modal-card">
    <div class="modal-title" id="modalTitle">Kết quả</div>
    <div class="modal-desc" id="modalDesc"></div>

    <div class="modal-stats">
      <div class="pill">⏱ <span id="modalTime">0</span>s</div>
      <div class="pill">📌 <span id="modalQ">0</span> câu</div>
      <div class="pill">✅ <span id="modalCorrect">0</span> đúng</div>
      <div class="pill">⭐ <span id="modalScore">0</span> điểm</div>
    </div>

    <div class="modal-actions">
      <button id="btnPlayAgain" class="btn btn-primary btn-sm">Chơi lại</button>
      <a href="/games" class="btn btn-outline-secondary btn-sm">Thoát</a>
    </div>
  </div>
</div>

<script src="/assets/js/math.js"></script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>
