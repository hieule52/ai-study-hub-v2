(() => {
  const board = document.getElementById("board");
  const levelSelect = document.getElementById("levelSelect");
  const btnStart = document.getElementById("btnStart");
  const btnRestart = document.getElementById("btnRestart");
  const btnExit = document.getElementById("btnExit");

  const timeText = document.getElementById("timeText");
  const movesText = document.getElementById("movesText");
  const pairsText = document.getElementById("pairsText");

  const modal = document.getElementById("resultModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalDesc = document.getElementById("modalDesc");
  const modalTime = document.getElementById("modalTime");
  const modalMoves = document.getElementById("modalMoves");
  const modalPairs = document.getElementById("modalPairs");
  const btnPlayAgain = document.getElementById("btnPlayAgain");

  const LEVELS = {
    easy:   { pairs: 6,  cols: 4, time: 60 },
    medium: { pairs: 8,  cols: 4, time: 75 },
    hard:   { pairs: 12, cols: 6, time: 100 },
  };

  // Icon pool (>= 12 pairs)
  const ICONS = ["🍎","🍌","🍇","🍉","🍓","🍒","🥝","🍍","🥑","🥕","🍔","🍟","🍕","🍩","🍪","🍫","⚽","🏀","🎮","🎧","📚","🧩","🚀","⭐"];

  let state = {
    running: false,
    lock: false,
    first: null,
    second: null,
    moves: 0,
    matchedPairs: 0,
    totalPairs: 0,
    timeLeft: 0,
    timeLimit: 0,
    timerId: null,
    startedAt: null,
  };

  function shuffle(arr) {
    const a = [...arr];
    for (let i = a.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  }

  function setStats() {
    movesText.textContent = String(state.moves);
    pairsText.textContent = `${state.matchedPairs}/${state.totalPairs}`;
    timeText.textContent = state.running ? `${state.timeLeft}s` : `--`;
  }

  function clearBoard() {
    board.innerHTML = "";
    board.removeAttribute("data-cols");
  }

  function buildDeck(levelKey) {
    const cfg = LEVELS[levelKey];
    state.totalPairs = cfg.pairs;
    state.timeLimit = cfg.time;
    state.timeLeft = cfg.time;

    const selected = shuffle(ICONS).slice(0, cfg.pairs);
    const deck = shuffle([...selected, ...selected]).map((icon, idx) => ({
      id: idx,
      icon,
      key: icon
    }));

    board.setAttribute("data-cols", String(cfg.cols));

    deck.forEach(card => {
      const el = document.createElement("div");
      el.className = "m-card";
      el.setAttribute("data-key", card.key);
      el.setAttribute("data-id", String(card.id));
      el.setAttribute("role", "button");
      el.setAttribute("aria-label", "Memory card");

      el.innerHTML = `
        <div class="m-face m-front">?</div>
        <div class="m-face m-back">${card.icon}</div>
      `;

      el.addEventListener("click", () => onFlip(el));
      board.appendChild(el);
    });
  }

  function resetState(keepLevel = true) {
    stopTimer();
    state.running = false;
    state.lock = false;
    state.first = null;
    state.second = null;
    state.moves = 0;
    state.matchedPairs = 0;
    state.totalPairs = 0;
    state.timeLeft = 0;
    state.timeLimit = 0;
    state.startedAt = null;

    btnRestart.disabled = true;
    btnStart.disabled = false;
    levelSelect.disabled = false;

    hideModal();
    if (!keepLevel) levelSelect.value = "easy";
    setStats();
  }

  function startGame() {
    const levelKey = levelSelect.value;
    clearBoard();
    resetState(true);

    buildDeck(levelKey);

    state.running = true;
    state.startedAt = Date.now();
    btnRestart.disabled = false;
    btnStart.disabled = true;
    levelSelect.disabled = true;

    setStats();
    startTimer();
  }

  function startTimer() {
    stopTimer();
    state.timerId = setInterval(() => {
      if (!state.running) return;
      state.timeLeft -= 1;
      if (state.timeLeft <= 0) {
        state.timeLeft = 0;
        if (!isFinished()) endGame(false, "Hết thời gian!");
      }
      setStats();
    }, 1000);
  }

  function stopTimer() {
    if (state.timerId) {
      clearInterval(state.timerId);
      state.timerId = null;
    }
  }

  function isFinished() {
    return state.matchedPairs >= state.totalPairs && state.totalPairs > 0;
  }

  function onFlip(cardEl) {
    if (!state.running) return;
    if (state.lock) return;
    if (cardEl.classList.contains("is-flipped")) return;
    if (cardEl.classList.contains("is-matched")) return;

    cardEl.classList.add("is-flipped");

    if (!state.first) {
      state.first = cardEl;
      return;
    }

    state.second = cardEl;
    state.moves += 1;
    setStats();

    const a = state.first.getAttribute("data-key");
    const b = state.second.getAttribute("data-key");

    if (a === b) {
        const c1 = state.first;
        const c2 = state.second;

        c1.classList.add("is-flipped", "is-matched");
        c2.classList.add("is-flipped", "is-matched");

        // Giữ 2 giây rồi mới ẩn
        setTimeout(() => {
            c1.classList.add("is-hide");
            c2.classList.add("is-hide");

            setTimeout(() => {
                c1.style.visibility = "hidden";
                c2.style.visibility = "hidden";
            }, 450);
        }, 2000);

        state.matchedPairs += 1;
        setStats();

        state.first = null;
        state.second = null;

        if (isFinished()) endGame(true, "Bạn đã ghép đủ tất cả các cặp!");
        return;
    }

    // not match
    state.lock = true;
    setTimeout(() => {
      state.first?.classList.remove("is-flipped");
      state.second?.classList.remove("is-flipped");
      state.first = null;
      state.second = null;
      state.lock = false;
    }, 650);
  }

  function endGame(win, reasonText) {
    state.running = false;
    state.lock = true;
    stopTimer();

    const timeUsed = state.timeLimit - state.timeLeft;

    modalTitle.textContent = win ? "🎉 Bạn thắng!" : "💥 Bạn thua!";
    modalDesc.textContent = reasonText || (win ? "Xuất sắc!" : "Thử lại nhé!");
    modalTime.textContent = String(timeUsed);
    modalMoves.textContent = String(state.moves);
    modalPairs.textContent = String(state.matchedPairs);

    showModal();
  }

  function showModal() {
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
  }

  function hideModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
  }

  // Exit game
  function exitGame() {
    const ok = confirm("Bạn có chắc muốn thoát game không? Tiến trình sẽ mất.");
    if (!ok) return;
    // về trang games
    window.location.href = "/games";
  }

  // Restart current level
  function restartGame() {
    if (!levelSelect.disabled) {
      // chưa start -> chỉ start luôn
      startGame();
      return;
    }
    startGame();
  }

  // events
  btnStart.addEventListener("click", startGame);
  btnRestart.addEventListener("click", restartGame);
  btnExit.addEventListener("click", exitGame);

  btnPlayAgain.addEventListener("click", () => {
    hideModal();
    startGame();
  });

  // close modal by clicking outside card
  modal.addEventListener("click", (e) => {
    if (e.target === modal) hideModal();
  });

  // initial
  resetState(true);
})();
