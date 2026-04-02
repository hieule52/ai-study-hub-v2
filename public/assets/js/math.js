(() => {
  const el = (id) => document.getElementById(id);

  const levelSelect = el("levelSelect");
  const btnStart = el("btnStart");
  const btnRestart = el("btnRestart");
  const btnExit = el("btnExit");

  const timeText = el("timeText");
  const qText = el("qText");
  const correctText = el("correctText");
  const scoreText = el("scoreText");

  const questionText = el("questionText");
  const answerInput = el("answerInput");
  const btnSubmit = el("btnSubmit");
  const feedback = el("feedback");
  const choicesWrap = el("choices");

  const resultModal = el("resultModal");
  const modalTitle = el("modalTitle");
  const modalDesc = el("modalDesc");
  const modalTime = el("modalTime");
  const modalQ = el("modalQ");
  const modalCorrect = el("modalCorrect");
  const modalScore = el("modalScore");
  const btnPlayAgain = el("btnPlayAgain");

  const LEVELS = {
    easy:   { totalQ: 10, time: 60, ops: ["+", "-"], range: [1, 20] },
    medium: { totalQ: 12, time: 75, ops: ["+", "-", "×"], range: [5, 50] },
    hard:   { totalQ: 15, time: 100, ops: ["+", "-", "×", "÷"], range: [10, 99] },
  };

  const state = {
    running: false,
    lock: false,
    level: "easy",
    timeLeft: 0,
    timer: null,

    totalQ: 0,
    index: 0,
    correct: 0,
    score: 0,

    current: null, // {a,b,op,answer,text}
  };

  function randInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  function pick(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
  }

  function makeQuestion(levelKey) {
    const cfg = LEVELS[levelKey];
    const [min, max] = cfg.range;
    let a = randInt(min, max);
    let b = randInt(min, max);
    let op = pick(cfg.ops);

    // đảm bảo phép trừ không âm (dễ chơi)
    if (op === "-" && b > a) [a, b] = [b, a];

    // phép chia: ra số nguyên
    if (op === "÷") {
      // tạo b trước rồi nhân để a chia hết
      b = randInt(2, 12);
      const k = randInt(2, 12);
      a = b * k;
    }

    let answer = 0;
    let text = "";

    if (op === "+") { answer = a + b; text = `${a} + ${b}`; }
    if (op === "-") { answer = a - b; text = `${a} - ${b}`; }
    if (op === "×") { answer = a * b; text = `${a} × ${b}`; }
    if (op === "÷") { answer = a / b; text = `${a} ÷ ${b}`; }

    return { a, b, op, answer, text };
  }

  function makeChoices(answer, levelKey) {
    // tạo 3 đáp án sai “hợp lý”
    const cfg = LEVELS[levelKey];
    const out = new Set([answer]);

    while (out.size < 4) {
      let delta = randInt(1, levelKey === "hard" ? 12 : 8);
      let sign = Math.random() < 0.5 ? -1 : 1;
      let wrong = answer + sign * delta;

      // tránh âm ở easy/medium
      if (wrong < 0) wrong = answer + delta;

      out.add(wrong);
    }

    // shuffle
    const arr = Array.from(out);
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  function setStats() {
    timeText.textContent = state.running ? `${state.timeLeft}s` : "--";
    qText.textContent = `${state.index}/${state.totalQ}`;
    correctText.textContent = `${state.correct}`;
    scoreText.textContent = `${state.score}`;
  }

  function setFeedback(type, msg) {
    feedback.classList.remove("ok", "bad");
    if (!msg) { feedback.textContent = ""; return; }
    feedback.textContent = msg;
    if (type) feedback.classList.add(type);
  }

  function renderQuestion() {
    const q = state.current;
    questionText.textContent = q.text;
    answerInput.value = "";
    answerInput.focus();

    setFeedback("", "");

    // render choices
    choicesWrap.innerHTML = "";
    const choices = makeChoices(q.answer, state.level);

    choices.forEach((val) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "mq-choice";
      btn.textContent = val;
      btn.addEventListener("click", () => submitAnswer(val, btn));
      choicesWrap.appendChild(btn);
    });
  }

  function lockChoices(lock) {
    Array.from(choicesWrap.querySelectorAll(".mq-choice")).forEach((b) => {
      b.disabled = lock;
      if (lock) b.style.pointerEvents = "none";
      else b.style.pointerEvents = "";
    });
  }

  function nextQuestion() {
    if (!state.running) return;

    if (state.index >= state.totalQ) {
      endGame(true, "Hoàn thành đủ câu hỏi!");
      return;
    }

    state.current = makeQuestion(state.level);
    state.index += 1;
    setStats();
    renderQuestion();
  }

  function calcScore(isCorrect) {
    if (!isCorrect) return 0;
    // điểm theo cấp độ + còn thời gian
    const base = state.level === "easy" ? 10 : state.level === "medium" ? 15 : 20;
    const bonus = Math.min(10, Math.floor(state.timeLeft / 10));
    return base + bonus;
  }

  function markChoices(correctVal, pickedVal) {
    const buttons = Array.from(choicesWrap.querySelectorAll(".mq-choice"));
    buttons.forEach((b) => {
      const v = Number(b.textContent);
      if (v === correctVal) b.classList.add("correct");
      if (v === pickedVal && pickedVal !== correctVal) b.classList.add("wrong");
    });
  }

  function submitAnswer(val, clickedBtn = null) {
    if (!state.running || state.lock) return;

    const userVal = Number(val);
    if (Number.isNaN(userVal)) return;

    state.lock = true;
    lockChoices(true);
    btnSubmit.disabled = true;
    answerInput.disabled = true;

    const correctVal = state.current.answer;
    const ok = userVal === correctVal;

    if (clickedBtn) {
      markChoices(correctVal, userVal);
    } else {
      // nếu nhập tay thì vẫn highlight lựa chọn đúng
      markChoices(correctVal, userVal);
    }

    if (ok) {
      state.correct += 1;
      const gained = calcScore(true);
      state.score += gained;
      setFeedback("ok", `✅ Đúng rồi! +${gained} điểm`);
    } else {
      setFeedback("bad", `❌ Sai! Đáp án đúng: ${correctVal}`);
    }

    setStats();

    setTimeout(() => {
      state.lock = false;
      lockChoices(false);
      btnSubmit.disabled = false;
      answerInput.disabled = false;

      nextQuestion();
    }, 900);
  }

  function tick() {
    state.timeLeft -= 1;
    if (state.timeLeft <= 0) {
      state.timeLeft = 0;
      setStats();
      endGame(false, "Hết thời gian!");
      return;
    }
    setStats();
  }

  function startGame() {
    const lv = levelSelect.value;
    const cfg = LEVELS[lv];

    state.level = lv;
    state.timeLeft = cfg.time;
    state.totalQ = cfg.totalQ;
    state.index = 0;
    state.correct = 0;
    state.score = 0;
    state.current = null;

    state.running = true;
    state.lock = false;

    btnStart.disabled = true;
    btnRestart.disabled = false;
    levelSelect.disabled = true;

    answerInput.disabled = false;
    btnSubmit.disabled = false;
    lockChoices(false);

    clearInterval(state.timer);
    state.timer = setInterval(tick, 1000);

    nextQuestion();
  }

  function resetUIIdle() {
    state.running = false;
    state.lock = false;
    clearInterval(state.timer);
    state.timer = null;

    btnStart.disabled = false;
    btnRestart.disabled = true;
    levelSelect.disabled = false;

    answerInput.disabled = true;
    btnSubmit.disabled = true;
    lockChoices(true);

    questionText.innerHTML = `Chọn cấp độ rồi bấm <b>Bắt đầu</b>.`;
    choicesWrap.innerHTML = "";
    setFeedback("", "");
    setStats();
  }

  function endGame(isWin, reason) {
    if (!state.running) return;

    state.running = false;
    clearInterval(state.timer);
    state.timer = null;

    // tiêu chí thắng: trả lời đúng >= 70% hoặc hoàn thành hết câu
    const passRate = Math.round((state.correct / state.totalQ) * 100);
    const pass = isWin && passRate >= 70;

    const title = pass ? "🎉 Bạn thắng!" : "😥 Bạn thua!";
    const desc = `${reason} | Đúng: ${state.correct}/${state.totalQ} (${passRate}%).`;

    modalTitle.textContent = title;
    modalDesc.textContent = desc;
    modalTime.textContent = `${LEVELS[state.level].time - state.timeLeft}`;
    modalQ.textContent = `${state.totalQ}`;
    modalCorrect.textContent = `${state.correct}`;
    modalScore.textContent = `${state.score}`;

    resultModal.classList.add("show");

    btnStart.disabled = false;
    btnRestart.disabled = false;
    levelSelect.disabled = false;

    answerInput.disabled = true;
    btnSubmit.disabled = true;
    lockChoices(true);
  }

  function closeModal() {
    resultModal.classList.remove("show");
  }

  // events
  btnStart.addEventListener("click", startGame);
  btnRestart.addEventListener("click", () => {
    closeModal();
    resetUIIdle();
    startGame();
  });

  btnExit.addEventListener("click", () => {
    if (confirm("Bạn muốn thoát game Math Quiz?")) window.location.href = "/games";
  });

  btnPlayAgain.addEventListener("click", () => {
    closeModal();
    resetUIIdle();
    startGame();
  });

  resultModal.addEventListener("click", (e) => {
    if (e.target === resultModal) closeModal();
  });

  btnSubmit.addEventListener("click", () => {
    const v = Number(answerInput.value);
    if (Number.isNaN(v)) {
      setFeedback("bad", "Nhập số hợp lệ nhé!");
      return;
    }
    submitAnswer(v);
  });

  answerInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !btnSubmit.disabled) btnSubmit.click();
  });

  // init
  resetUIIdle();
})();
