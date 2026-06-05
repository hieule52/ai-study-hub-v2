<?php
$pageTitle = 'Gia Sư AI - AI Study Hub';
$actor = 'student';
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="/assets/css/pages/ai-chat.css?v=' . time() . '">
    <style>
        .external-disclaimer {
            font-size: 0.8rem;
            color: #f59e0b;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed rgba(245, 158, 11, 0.2);
        }
    </style>
';
require __DIR__ . '/../layouts/header.php';
?>

<div class="chat-layout">
    <div class="chat-main">
        
        <div class="chat-header">
            <div class="flex items-center gap-4">
                <div class="ai-avatar-header">🤖</div>
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 1.4rem; margin-bottom: 0.2rem;" data-i18n="aichat_title">Gia sư AI</h2>
                    <p class="text-secondary" style="font-size: 0.9rem;" data-i18n="aichat_subtitle">Trợ lý học tập thông minh Groq™</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.7rem; letter-spacing: 0.05em;">GROQ™ POWERED</span>
            </div>
        </div>

        <div class="chat-body" id="chatBox">
            <!-- Lời chào đầu tiên của AI -->
            <div class="msg-container ai">
                <div class="msg-avatar ai">🤖</div>
                <div class="msg-bubble" data-i18n="aichat_welcome">
                    Xin chào! Tôi có thể giúp gì cho bài học hôm nay của bạn?
                </div>
            </div>
        </div>

        <div class="chat-footer">
            <!-- Suggested Prompts -->
            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <button class="btn-prompt" onclick="usePrompt(window.I18n ? window.I18n.get('aichat_prompt1') : 'Explain this concept...')" data-i18n="aichat_prompt1">Giải thích khái niệm này...</button>
                <button class="btn-prompt" onclick="usePrompt(window.I18n ? window.I18n.get('aichat_prompt2') : 'Summarize the last lesson')" data-i18n="aichat_prompt2">Tóm tắt bài học vừa rồi</button>
                <button class="btn-prompt" onclick="usePrompt(window.I18n ? window.I18n.get('aichat_prompt3') : 'Create practice exercises')" data-i18n="aichat_prompt3">Tạo bài tập thực hành</button>
            </div>

            <!-- Chat Form -->
            <form id="chatForm">
                <div class="input-wrapper">
                    <input type="text" id="chatInput" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" required data-i18n="aichat_input_placeholder">
                    <button type="submit" class="btn-send">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
       </form>
                    </div>

                </div>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = App.requireAuth(['student', 'teacher', 'admin']);
            if (!user) return;
            if (window.I18n) window.I18n.render();
        });

        function usePrompt(text) {
            document.getElementById('chatInput').value = text;
            document.getElementById('chatForm').dispatchEvent(new Event('submit'));
        }

        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const chatBox = document.getElementById('chatBox');
            const message = input.value.trim();
            if (!message) return;

            // Handle rendering text in user bubble
            let userContentHtml = `<span>${message}</span>`;

            // Render Tin nhắn của User
            chatBox.innerHTML += `
                <div class="msg-container user">
                    <div class="msg-avatar me" id="user-avatar-char">👤</div>
                    <div class="msg-bubble">${userContentHtml}</div>
                </div>
            `;
            
            // Render Avatar chữ của UI tạm (sau này có user object đầy đủ sẽ load chữ đầu)
            const currUser = JSON.parse(localStorage.getItem('auth_user') || '{}');
            if (currUser && currUser.username) {
                const els = document.querySelectorAll('#user-avatar-char');
                els.forEach(el => el.innerText = currUser.username.charAt(0).toUpperCase());
            }

            // Capture payload
            const payload = { 
                message: message,
                lang: localStorage.getItem('lang') || 'vi'
            };

            // Clear Input
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            // Render Bubble AI đang suy nghĩ
            const thinkingId = 'think_' + Date.now();
            const analyzingMsg = window.I18n ? window.I18n.get('aichat_analyzing') : 'Đang phân tích câu hỏi...';
            
            chatBox.innerHTML += `
                <div class="msg-container ai" id="container_${thinkingId}">
                    <div class="msg-avatar ai">🤖</div>
                    <div class="msg-bubble thinking-bubble" id="${thinkingId}">
                        <div style="width: 15px; height: 15px; border: 2px solid var(--secondary); border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div> 
                        ${analyzingMsg}
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // Call AI endpoint Backend
                const res = await window.api.post('/ai/chat', payload);
                
                // Thay thế bong bóng suy nghĩ bằng kết quả
                const targetBubble = document.getElementById(thinkingId);
                targetBubble.classList.remove('thinking-bubble');
                
                const data = res.data;
                let htmlResponse = '';

                // Sử dụng thư viện marked để render markdown code block sinh ra bởi AI
                const parsedMarkdown = window.marked && window.marked.parse ? marked.parse(data.ai_response) : data.ai_response.replace(/\n/g, '<br>');
                htmlResponse += parsedMarkdown;

                // Show external knowledge warning disclaimer at the end of the bubble if applicable
                if (data.is_external || data.from_external) {
                    const warningMsg = data.disclaimer || (localStorage.getItem('lang') === 'en'
                        ? '⚠️ This content is generated from external sources outside the AI Study Hub LMS. Please verify the information.'
                        : '⚠️ Nội dung này được tạo từ nguồn kiến thức bên ngoài hệ thống AI Study Hub LMS. Vui lòng kiểm chứng lại thông tin trước khi áp dụng.');
                    htmlResponse += `
                        <div class="external-disclaimer">
                            ${warningMsg}
                        </div>
                    `;
                }

                targetBubble.innerHTML = htmlResponse;
                
                // Add CSS inline fix for code blocks rendering from marked
                const codes = targetBubble.querySelectorAll('pre');
                codes.forEach(c => {
                    c.style.background = 'rgba(0,0,0,0.3)';
                    c.style.padding = '1rem';
                    c.style.borderRadius = '8px';
                    c.style.overflowX = 'auto';
                    c.style.border = '1px solid rgba(255,255,255,0.05)';
                    c.style.marginTop = '10px';
                    c.style.marginBottom = '10px';
                });

            } catch (err) {
                const targetBubble = document.getElementById(thinkingId);
                targetBubble.classList.remove('thinking-bubble');
                targetBubble.style.border = '1px solid var(--danger)';
                const errPrefix = window.I18n ? window.I18n.get('aichat_error') : "❌ Lỗi kết nối tới AI: ";
                targetBubble.innerHTML = errPrefix + err.message;
            }
            
            chatBox.scrollTop = chatBox.scrollHeight;
        });

    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
