(() => {
  const arena = document.getElementById("arena");
  const target = document.getElementById("target");
  const guide = document.getElementById("guide");
  const overlay = document.getElementById("overlay");
  const overlayTitle = document.getElementById("overlayTitle");
  const overlayDesc = document.getElementById("overlayDesc");

  const modeSelect = document.getElementById("modeSelect");
  const btnStart = document.getElementById("btnStart");
  const btnRestart = document.getElementById("btnRestart");
  const btnExit = document.getElementById("btnExit");

  const timeText = document.getElementById("timeText");
  const clickText = document.getElementById("clickText");
  const cpsText = document.getElementById("cpsText");
  const bestText = document.getElementById("bestText");
  const progressBar = document.getElementById("progressBar");

  const resultModal = document.getElementById("resultModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalDesc = document.getElementById("modalDesc");
  const modalClicks = document.getElementById("modalClicks");
  const modalCps = document.getElementById("modalCps");
  const modalBest = document.getElementById("modalBest");
  const btnPlayAgain = document.getElementById("btnPlayAgain");

  const BEST_KEY = "clickspeed_best";

  const state = {
    running: false,
    clicks: 0,
    duration: 5,
    startAt: 0,
    tickTimer: null,
    spawnTimer: null,
    endTimer: null,
  };

  function loadBest() {
    const v = Number(localStorage.getItem(BEST_KEY) || "0");
    bestText.textContent = String(v);
    return v;
  }

  function setOverlay(show, title, desc) {
    overlayTitle.textContent = title || "";
    overlayDesc.textContent = desc || "";
    if (show) overlay.classList.remove("hide");
    else overlay.classList.add("hide");
  }

  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function rand(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  function updateHUD() {
    const elapsed = state.running ? (Date.now() - state.startAt) / 1000 : 0;
    const remain = state.running ? Math.max(0, state.duration - elapsed) : state.duration;

    timeText.textContent = state.running ? `${Math.ceil(remain)}s` : "--";
    clickText.textContent = String(state.clicks);

    const cps = state.running
      ? (state.clicks / Math.max(0.001, elapsed))
      : 0;

    cpsText.textContent = state.running ? cps.toFixed(2) : "0.00";

    const pct = state.running
      ? clamp((elapsed / state.duration) * 100, 0, 100)
      : 0;

    progressBar.style.width = `${pct}%`;
  }

  function hideTarget() {
    target.classList.remove("show");
  }

  function placeTargetRandom() {
    const rect = arena.getBoundingClientRect();
    const size = target.offsetWidth || 64;

    // chừa biên để không dính cạnh
    const padding = 16;
    const minX = padding;
    const minY = padding + 48; // chừa guide
    const maxX = Math.max(minX, rect.width - size - padding);
    const maxY = Math.max(minY, rect.height - size - padding);

    const x = rand(minX, Math.floor(maxX));
    const y = rand(minY, Math.floor(maxY));

    target.style.left = `${x}px`;
    target.style.top = `${y}px`;
  }

  function spawnTarget() {
    if (!state.running) return;
    placeTargetRandom();
    target.classList.add("show");

    // mỗi lần xuất hiện tối đa ~900ms nếu không click thì tự đổi vị trí
    const life = rand(500, 900);
    clearTimeout(state.spawnTimer);
    state.spawnTimer = setTimeout(() => {
      hideTarget();
      // spawn lại sau 80~200ms cho “nhịp”
      state.spawnTimer = setTimeout(spawnTarget, rand(80, 200));
    }, life);
  }

  function clearAllTimers() {
    clearInterval(state.tickTimer);
    clearTimeout(state.spawnTimer);
    clearTimeout(state.endTimer);
    state.tickTimer = null;
    state.spawnTimer = null;
    state.endTimer = null;
  }

  function startGame() {
    clearAllTimers();
    hideTarget();

    state.duration = Number(modeSelect.value || "5");
    state.clicks = 0;
    state.running = false;
    updateHUD();

    btnStart.disabled = true;
    btnRestart.disabled = true;
    modeSelect.disabled = true;

    setOverlay(true, "Chuẩn bị...", "Mục tiêu sẽ xuất hiện sau một chút. Đừng click sớm 😄");

    const delay = rand(700, 1700);

    state.endTimer = setTimeout(() => {
      state.running = true;
      state.startAt = Date.now();
      setOverlay(false);

      // tick HUD
      state.tickTimer = setInterval(() => {
        updateHUD();
        const elapsed = (Date.now() - state.startAt) / 1000;
        if (elapsed >= state.duration) endGame();
      }, 80);

      // spawn lần đầu
      spawnTarget();

      // auto end
      state.endTimer = setTimeout(endGame, state.duration * 1000);
    }, delay);
  }

  function endGame() {
    if (!state.running) {
      // đang chờ delay mà bấm restart/thoát
      clearAllTimers();
      btnStart.disabled = false;
      btnRestart.disabled = true;
      modeSelect.disabled = false;
      setOverlay(true, "Sẵn sàng?", "Nhấn Bắt đầu để chơi.");
      hideTarget();
      updateHUD();
      return;
    }

    state.running = false;
    clearAllTimers();
    hideTarget();
    updateHUD();

    btnRestart.disabled = false;
    btnStart.disabled = false;
    modeSelect.disabled = false;

    const best = loadBest();
    let newBest = best;

    if (state.clicks > best) {
      newBest = state.clicks;
      localStorage.setItem(BEST_KEY, String(newBest));
      bestText.textContent = String(newBest);
    }

    const cps = state.clicks / Math.max(0.001, state.duration);

    modalTitle.textContent = "Kết thúc!";
    modalDesc.textContent = (state.clicks > best)
      ? "Bạn vừa lập kỷ lục mới! 🔥"
      : "Cố lên! Thử lại để phá kỷ lục nhé.";

    modalClicks.textContent = String(state.clicks);
    modalCps.textContent = cps.toFixed(2);
    modalBest.textContent = String(newBest);

    resultModal.classList.add("show");
  }

  function closeModal() {
    resultModal.classList.remove("show");
  }

  // Click target -> tăng điểm + spawn lại nhanh
  target.addEventListener("click", () => {
    if (!state.running) return;
    state.clicks += 1;
    hideTarget();
    updateHUD();
    clearTimeout(state.spawnTimer);
    state.spawnTimer = setTimeout(spawnTarget, rand(60, 160));
  });

  // Click ra ngoài arena không tính
  arena.addEventListener("click", (e) => {
    // chỉ xử lý click miss nếu đang chạy và không phải click vào target
    if (!state.running) return;
    if (e.target === target) return;
  });

  btnStart.addEventListener("click", () => {
    closeModal();
    startGame();
  });

  btnRestart.addEventListener("click", () => {
    closeModal();
    startGame();
  });

  btnPlayAgain.addEventListener("click", () => {
    closeModal();
    startGame();
  });

  btnExit.addEventListener("click", () => {
    // dừng game và quay về games
    state.running = false;
    clearAllTimers();
    window.location.href = "/games";
  });

  // Click nền modal để đóng
  resultModal.addEventListener("click", (e) => {
    if (e.target === resultModal) closeModal();
  });

  // init
  loadBest();
  setOverlay(true, "Sẵn sàng?", "Nhấn Bắt đầu để chơi.");
  updateHUD();
})();
