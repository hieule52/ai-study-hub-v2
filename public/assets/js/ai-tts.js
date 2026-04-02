/**
 * AI Text-to-Speech - Web Speech API
 * Chuyển văn bản thành giọng nói
 */

document.addEventListener('DOMContentLoaded', function () {
    // ===== ELEMENTS =====
    const ttsInput = document.getElementById('ttsInput');
    const voiceSelect = document.getElementById('voiceSelect');
    const rateInput = document.getElementById('rateInput');
    const volumeInput = document.getElementById('volumeInput');
    const rateValue = document.getElementById('rateValue');
    const volumeValue = document.getElementById('volumeValue');

    const speakBtn = document.getElementById('speakBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const resumeBtn = document.getElementById('resumeBtn');
    const stopBtn = document.getElementById('stopBtn');

    // ===== WEB SPEECH API =====
    const synth = window.speechSynthesis; // sài của trình duyệt
    let voices = [];
    let currentUtterance = null;

    // ===== LOAD VOICES =====
    function loadVoices() {
        voices = synth.getVoices();
        voiceSelect.innerHTML = '';

        // Ưu tiên giọng tiếng Anh
        const enVoices = voices.filter((voice) => voice.lang.startsWith('en'));
        const viVoices = voices.filter((voice) => voice.lang.startsWith('vi'));
        const otherVoices = voices.filter((voice) => !voice.lang.startsWith('en') && !voice.lang.startsWith('vi'));

        // Thêm giọng tiếng Anh (ưu tiên)
        if (enVoices.length > 0) {
            const enGroup = document.createElement('optgroup');
            enGroup.label = '🇬🇧 English Voices';
            enVoices.forEach((voice, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = `${voice.name} (${voice.lang})`;
                enGroup.appendChild(option);
            });
            voiceSelect.appendChild(enGroup);
        }

        // Thêm giọng tiếng Việt
        if (viVoices.length > 0) {
            const viGroup = document.createElement('optgroup');
            viGroup.label = '🇻🇳 Tiếng Việt';
            viVoices.forEach((voice, index) => {
                const option = document.createElement('option');
                option.value = enVoices.length + index;
                option.textContent = `${voice.name} (${voice.lang})`;
                viGroup.appendChild(option);
            });
            voiceSelect.appendChild(viGroup);
        }

        // Thêm giọng khác
        if (otherVoices.length > 0) {
            const otherGroup = document.createElement('optgroup');
            otherGroup.label = '🌍 Other Languages';
            otherVoices.forEach((voice, index) => {
                const option = document.createElement('option');
                option.value = enVoices.length + viVoices.length + index;
                option.textContent = `${voice.name} (${voice.lang})`;
                otherGroup.appendChild(option);
            });
            voiceSelect.appendChild(otherGroup);
        }

        // Nếu không có giọng nào
        if (voices.length === 0) {
            const option = document.createElement('option');
            option.textContent = 'No voices available';
            voiceSelect.appendChild(option);
        }
    }

    // Load voices khi sẵn sàng
    if (synth.onvoiceschanged !== undefined) {
        synth.onvoiceschanged = loadVoices;
    }
    loadVoices();

    // ===== UPDATE VALUES =====
    rateInput.addEventListener('input', function () {
        rateValue.textContent = this.value + 'x';
    });

    volumeInput.addEventListener('input', function () {
        volumeValue.textContent = Math.round(this.value * 100) + '%';
    });

    // ===== SPEAK FUNCTION =====
    function speak() {
        const text = ttsInput.value.trim();

        if (!text) {
            alert('❗ Please enter text to speak!');
            return;
        }

        // Dừng nếu đang đọc
        if (synth.speaking) {
            synth.cancel();
        }

        // Tạo utterance mới
        currentUtterance = new SpeechSynthesisUtterance(text); 

        // Chọn giọng
        const selectedVoiceIndex = voiceSelect.value;
        if (voices[selectedVoiceIndex]) {
            currentUtterance.voice = voices[selectedVoiceIndex];
        }

        // Cài đặt
        currentUtterance.rate = parseFloat(rateInput.value); // tốc độ đọc
        currentUtterance.volume = parseFloat(volumeInput.value); // âm lượng
        currentUtterance.pitch = 1;

        // Events
        currentUtterance.onstart = function () {
            speakBtn.disabled = true;
            pauseBtn.disabled = false;
            stopBtn.disabled = false;
            speakBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Speaking...';
        };

        currentUtterance.onend = function () { // đọc xong
            resetButtons();
        };

        currentUtterance.onerror = function (event) {
            console.error('Speech error:', event);
            alert('❗ Error: ' + event.error);
            resetButtons();
        };

        // Bắt đầu đọc
        synth.speak(currentUtterance);
    }

    // ===== PAUSE FUNCTION =====
    function pause() {
        if (synth.speaking && !synth.paused) {
            synth.pause();
            pauseBtn.disabled = true;
            resumeBtn.disabled = false;
        }
    }

    // ===== RESUME FUNCTION =====
    function resume() {
        if (synth.paused) {
            synth.resume();
            pauseBtn.disabled = false;
            resumeBtn.disabled = true;
        }
    }

    // ===== STOP FUNCTION =====
    function stop() {
        synth.cancel();
        resetButtons();
    }

    // ===== RESET BUTTONS =====
    function resetButtons() {
        speakBtn.disabled = false;
        pauseBtn.disabled = true;
        resumeBtn.disabled = true;
        stopBtn.disabled = true;
        speakBtn.innerHTML = '<i class="fas fa-play"></i> Phát';
    }

    // ===== EVENT LISTENERS =====
    speakBtn.addEventListener('click', speak);
    pauseBtn.addEventListener('click', pause);
    resumeBtn.addEventListener('click', resume);
    stopBtn.addEventListener('click', stop);

    // ===== SUGGESTION FUNCTION (Global) =====
    window.setSuggestion = function (text) {
        ttsInput.value = text;
        ttsInput.focus();
    };
});
