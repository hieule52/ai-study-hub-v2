// ============================================
// AI SUMMARIZER - Simplified Version
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const chatInput = document.querySelector('.chat-input');
    const sendBtn = document.querySelector('.send-btn');
    const chatForm = document.getElementById('chatForm');
    const chatMessages = document.getElementById('chatMessages');

    // ============================================
    // 1. AUTO-RESIZE TEXTAREA
    // ============================================
    chatInput?.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
        sendBtn.disabled = !this.value.trim();
    });

    // ============================================
    // 2. ENTER KEY SUBMIT
    // ============================================
    chatInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && chatInput.value.trim()) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // ============================================
    // 3. FORM SUBMIT (AJAX)
    // ============================================
    chatForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        // UI: Loading state
        setLoading(true);
        addMessage(message, 'user');
        showTyping();

        try {
            // Gửi request
            const response = await fetch('/ai/summarizer', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message })
            });

            const aiResponse = await response.text();

            // Hiển thị response
            hideTyping();
            addMessage(aiResponse, 'ai');

        } catch (error) {
            hideTyping();
            addMessage('❌ Lỗi kết nối. Vui lòng thử lại!', 'ai');
        } finally {
            // Reset form
            chatInput.value = '';
            chatInput.style.height = 'auto';
            setLoading(false);
            chatInput.focus();
        }
    });

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    // Set loading state
    function setLoading(isLoading) {
        if (isLoading) {
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendBtn.disabled = true;
            chatInput.readOnly = true;
        } else {
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            sendBtn.disabled = true;
            chatInput.readOnly = false;
        }
    }

    // Add message (user hoặc ai)
    function addMessage(text, type) {
        if (!chatMessages) return;

        const isUser = type === 'user';
        const icon = isUser ? 'fa-user' : 'fa-robot';
        const className = isUser ? 'user-message' : 'ai-message';

        const html = `
            <div class="message ${className}">
                ${isUser ? '' : `<div class="message-avatar"><i class="fas ${icon}"></i></div>`}
                <div class="message-content">
                    <div class="message-bubble">${escapeHtml(text)}</div>
                </div>
                ${isUser ? `<div class="message-avatar"><i class="fas ${icon}"></i></div>` : ''}
            </div>
        `;

        chatMessages.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Show typing indicator
    function showTyping() {
        if (!chatMessages) return;

        const html = `
            <div class="message ai-message typing-message">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <div class="message-bubble">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        chatMessages.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Hide typing indicator
    function hideTyping() {
        document.querySelector('.typing-message')?.remove();
    }

    // Scroll to bottom
    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Escape HTML (bảo mật)
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Scroll to bottom khi load trang
    scrollToBottom();
});

// ============================================
// GLOBAL FUNCTIONS (cho suggestion buttons)
// ============================================

function setSuggestion(text) {
    const chatInput = document.querySelector('.chat-input');
    const sendBtn = document.querySelector('.send-btn');

    if (chatInput) {
        chatInput.value = text;
        chatInput.style.height = 'auto';
        chatInput.style.height = chatInput.scrollHeight + 'px';
        chatInput.focus();
        if (sendBtn) sendBtn.disabled = false;
    }
}
