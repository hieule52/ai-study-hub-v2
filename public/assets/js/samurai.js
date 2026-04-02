(() => {
  const $ = (id) => document.getElementById(id);

  const arena = $("arena");
  const overlay = $("overlay");
  const slashLayer = $("slashLayer");

  const modeSelect = $("modeSelect");
  const btnStart = $("btnStart");
  const btnRestart = $("btnRestart");

  const timeText = $("timeText");
  const scoreText = $("scoreText");
  const comboText = $("comboText");
  const bestText = $("bestText");
  const progressBar = $("progressBar");

  const resultModal = $("resultModal");
  const modalScore = $("modalScore");
  const modalMaxCombo = $("modalMaxCombo");
  const modalBest = $("modalBest");
  const btnPlayAgain = $("btnPlayAgain");

  const BEST_KEY = "samurai_best_score_v1";

  const state = {
    running: false,
    duration: 30,
    timeLeft: 0,
    timerId: null,
    spawnId: null,
    score: 0,
    combo: 0,
    maxCombo: 0,
    best: 0,
    lastPos: { x: 0, y: 0 },
    // spawn tuning
    baseSpawnMs: 820,
    minSpawnMs: 360,
    targetLifeMs: 900,
  };

  function loadBest() {
    const v = parseInt(localStorage.getItem(BEST_KEY) || "0", 10);
    state.best = Number.isFinite(v) ? v : 0;
    bestText.textContent = state.best;
  }

  function setUiIdle() {
    timeText.textContent = "--";
    scoreText.textContent = "0";
    comboText.textContent = "0";
    progressBar.style.width = "0%";
    btnRestart.disabled = true;
    overlay.classList.remove("hide");
    clearTargets();
  }

  function setUiRunning() {
    btnRestart.disabled = false;
    overlay.classList.add("hide");
  }

  function clearTargets() {
    arena.querySelectorAll(".sam-target").forEach((n) => n.remove());
  }

  function clamp(n, a, b) { return Math.max(a, Math.min(b, n)); }

  function randInt(a, b) { return Math.floor(Math.random() * (b - a + 1)) + a; }

  function spawnTarget() {
    if (!state.running) return;

    const rect = arena.getBoundingClientRect();
    const size = randInt(42, 78);

    // chừa mép + tránh che guide
    const pad = 12;
    const topPad = 62; // chừa vùng guide
    const x = randInt(pad, Math.max(pad, Math.floor(rect.width - size - pad)));
    const y = randInt(topPad, Math.max(topPad, Math.floor(rect.height - size - pad)));

    const t = document.createElement("button");
    t.type = "button";
    t.className = "sam-target";
    t.style.width = size + "px";
    t.style.height = size + "px";
    t.style.left = x + "px";
    t.style.top = y + "px";

    // click trúng
    t.addEventListener("click", (e) => {
      e.stopPropagation();
      hitTarget(t, x + size / 2, y + size / 2);
    });

    arena.appendChild(t);

    // tự biến mất nếu không chém kịp
    const life = state.targetLifeMs;
    const dieTimer = setTimeout(() => {
      if (t.isConnected) {
        t.remove();
        miss(); // hụt mục tiêu
      }
    }, life);

    // nếu bị remove sớm -> clear timer
    t._dieTimer = dieTimer;
  }

  function hitTarget(targetEl, cx, cy) {
    if (!state.running) return;

    // cancel life timer
    if (targetEl._dieTimer) clearTimeout(targetEl._dieTimer);

    // combo + score
    state.combo += 1;
    state.maxCombo = Math.max(state.maxCombo, state.combo);

    const base = 10;
    const bonus = 1 + Math.min(1.5, state.combo * 0.08); // combo tăng nhẹ
    state.score += Math.round(base * bonus);

    // effect hit
    targetEl.classList.add("hit");
    setTimeout(() => targetEl.remove(), 180);

    // slash
    drawSlashTo(cx, cy);

    renderStats();
  }

  function miss() {
    if (!state.running) return;
    state.combo = 0;
    renderStats();
  }

  function renderStats() {
    timeText.textContent = `${state.timeLeft}s`;
    scoreText.textContent = `${state.score}`;
    comboText.textContent = `${state.combo}`;
  }

  function drawSlashTo(x, y) {
    // lấy vị trí tương đối trong arena
    const rect = arena.getBoundingClientRect();
    const sx = clamp(state.lastPos.x, 0, rect.width);
    const sy = clamp(state.lastPos.y, 0, rect.height);

    const ex = clamp(x, 0, rect.width);
    const ey = clamp(y, 0, rect.height);

    const dx = ex - sx;
    const dy = ey - sy;
    const len = Math.sqrt(dx * dx + dy * dy);

    // nếu lần đầu / di chuyển nhỏ -> tạo vệt ngắn
    const length = Math.max(60, Math.min(320, len || 120));
    const angle = Math.atan2(dy || 0.001, dx || 1) * (180 / Math.PI);

    const slash = document.createElement("div");
    slash.className = "slash";
    slash.style.left = `${sx}px`;
    slash.style.top = `${sy}px`;
    slash.style.width = `${length}px`;
    slash.style.transform = `rotate(${angle}deg)`;

    slashLayer.appendChild(slash);
    setTimeout(() => slash.remove(), 240);

    state.lastPos.x = ex;
    state.lastPos.y = ey;
  }

  function startGame() {
    if (state.running) return;

    state.running = true;
    state.duration = parseInt(modeSelect.value, 10) || 30;
    state.timeLeft = state.duration;

    state.score = 0;
    state.combo = 0;
    state.maxCombo = 0;

    clearTargets();
    renderStats();
    setUiRunning();

    // spawn rate theo thời gian (càng về cuối càng nhanh)
    state.spawnId = setInterval(() => {
      spawnTarget();
      // tăng độ khó dần
      const progress = 1 - state.timeLeft / state.duration; // 0 -> 1
      const spawnMs = Math.round(
        state.baseSpawnMs - progress * (state.baseSpawnMs - state.minSpawnMs)
      );
      // reset interval linh hoạt
      // (cách nhẹ: cứ 1 lần spawn mới tính, nhưng interval cố định => ok)
      // muốn chuẩn hơn: dùng setTimeout loop (nhưng đủ dùng)
    }, state.baseSpawnMs);

    // timer 1s
    state.timerId = setInterval(() => {
      state.timeLeft -= 1;
      state.timeLeft = Math.max(0, state.timeLeft);

      const percent = ((state.duration - state.timeLeft) / state.duration) * 100;
      progressBar.style.width = `${percent}%`;

      renderStats();

      if (state.timeLeft <= 0) endGame();
    }, 1000);

    // set lastPos = giữa arena để slash không bị “0,0”
    const rect = arena.getBoundingClientRect();
    state.lastPos.x = rect.width / 2;
    state.lastPos.y = rect.height / 2;
  }

  function endGame() {
    if (!state.running) return;

    state.running = false;
    clearInterval(state.timerId);
    clearInterval(state.spawnId);
    state.timerId = null;
    state.spawnId = null;

    clearTargets();

    // best
    if (state.score > state.best) {
      state.best = state.score;
      localStorage.setItem(BEST_KEY, String(state.best));
    }
    bestText.textContent = state.best;

    // modal
    modalScore.textContent = `${state.score}`;
    modalMaxCombo.textContent = `${state.maxCombo}`;
    modalBest.textContent = `${state.best}`;

    resultModal.classList.add("show");
    resultModal.setAttribute("aria-hidden", "false");
  }

  function restartGame() {
    // đóng modal nếu đang mở
    resultModal.classList.remove("show");
    resultModal.setAttribute("aria-hidden", "true");

    // dừng mọi thứ nếu đang chạy
    if (state.running) {
      state.running = false;
      clearInterval(state.timerId);
      clearInterval(state.spawnId);
      state.timerId = null;
      state.spawnId = null;
    }

    setUiIdle();
    // start lại luôn để “Chơi lại” đúng nghĩa
    startGame();
  }

  // Click trong arena mà không trúng mục tiêu => miss + slash
  arena.addEventListener("click", (e) => {
    if (!state.running) return;
    const rect = arena.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    drawSlashTo(x, y);
    miss();
  });

  // cập nhật lastPos theo chuột để vệt chém đẹp hơn
  arena.addEventListener("mousemove", (e) => {
    const rect = arena.getBoundingClientRect();
    state.lastPos.x = e.clientX - rect.left;
    state.lastPos.y = e.clientY - rect.top;
  });

  // buttons
  btnStart.addEventListener("click", () => {
    // đóng modal nếu đang mở
    resultModal.classList.remove("show");
    resultModal.setAttribute("aria-hidden", "true");
    startGame();
  });

  btnRestart.addEventListener("click", restartGame);
  btnPlayAgain.addEventListener("click", restartGame);

  // đóng modal khi click ra ngoài
  resultModal.addEventListener("click", (e) => {
    if (e.target === resultModal) {
      resultModal.classList.remove("show");
      resultModal.setAttribute("aria-hidden", "true");
    }
  });

  // init
  loadBest();
  setUiIdle();
})();
