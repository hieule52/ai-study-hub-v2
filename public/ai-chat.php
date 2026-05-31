<?php
// Dynamic role determination based on route path
$uri = $_SERVER['REQUEST_URI'] ?? '/ai-chat';
$actor = 'guest';
if (strpos($uri, '/student/ai-chat') !== false) {
    $actor = 'student';
} elseif (strpos($uri, '/teacher/ai-chat') !== false) {
    $actor = 'teacher';
}

$pageTitle = 'Gia Sư AI - AI Study Hub';
$extraHead = '
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="/assets/css/pages/ai-chat.css?v=' . time() . '">
    <style>
        .warning-banner {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            color: #f59e0b;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .external-disclaimer {
            font-size: 0.8rem;
            color: #f59e0b;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed rgba(245, 158, 11, 0.2);
        }
    </style>
';
require __DIR__ . '/layouts/header.php';
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
                <span id="guest-mode-badge" class="badge" style="display:none; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.7rem; letter-spacing: 0.05em;">👤 GUEST MODE</span>
            </div>
        </div>

        <!-- Guest Mode Notice Banner (hidden by default, shown for guests) -->
        <div id="guest-mode-banner" style="display:none; align-items:center; gap:10px; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); border-radius: 10px; padding: 10px 16px; margin: 0 0 12px 0; font-size: 0.82rem; color: #f59e0b; line-height: 1.5;">
            <span style="font-size:1.2rem;">⚠️</span>
            <span>
                Bạn đang sử dụng Gia sư AI ở chế độ <strong>khách</strong> — lịch sử trò chuyện sẽ <strong>không được lưu lại</strong>.
                <a href="/register" style="color:#f59e0b; font-weight:700; margin-left:6px; text-decoration:underline;">Tạo tài khoản miễn phí</a> để lưu lịch sử và truy cập đầy đủ tính năng.
            </span>
        </div>

        <div class="chat-body" id="chatBox">
            <!-- Lời chào đầu tiên của AI -->
            <div class="msg-container ai">
                <div class="msg-avatar ai">🤖</div>
                <div class="msg-bubble" data-i18n="aichat_welcome">
                    Xin chào! Tôi là Gia sư AI của bạn. Tôi có thể giải đáp các kiến thức rộng, thông tin về các khóa học hoặc đề xuất lộ trình học tập phù hợp nhất cho bạn. Bạn muốn bắt đầu từ đâu?
                </div>
            </div>
        </div>

        <div class="chat-footer">
            <!-- Image Preview Container -->
            <div id="imagePreviewContainer" style="display: none; position: relative; width: 100px; height: 100px; margin-bottom: 1rem; border-radius: 12px; overflow: hidden; border: 2px solid var(--secondary); box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);">
                <img id="imagePreview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                <button onclick="removeImage()" style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>

            <!-- Chat Form -->
            <form id="chatForm">
                <input type="file" id="imageInput" accept="image/png, image/jpeg, image/webp" style="display: none;">
                <div class="input-wrapper">
                    <button type="button" onclick="document.getElementById('imageInput').click()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.4rem; padding: 0 0.75rem; transition: all 0.3s;" onmouseover="this.style.color='var(--secondary)'; this.style.transform='scale(1.1)'" onmouseout="this.style.color='var(--text-muted)'; this.style.transform='scale(1)'">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </button>
                    <input type="text" id="chatInput" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" required data-i18n="aichat_input_placeholder">
                    <button type="submit" class="btn-send">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = JSON.parse(localStorage.getItem('auth_user') || 'null');
            const path = window.location.pathname;

            // Route guard and redirection logic
            if (user) {
                if (user.role === 'student' && path === '/ai-chat') {
                    window.location.replace('/student/ai-chat');
                    return;
                }
                if (user.role === 'teacher' && path === '/ai-chat') {
                    window.location.replace('/teacher/ai-chat');
                    return;
                }
            } else {
                // Guest mode: show badge + notice banner
                const guestBadge  = document.getElementById('guest-mode-badge');
                const guestBanner = document.getElementById('guest-mode-banner');
                if (guestBadge)  guestBadge.style.display  = 'inline-flex';
                if (guestBanner) guestBanner.style.display  = 'flex';

                // If visiting role-specific paths without being logged in, redirect to guest assistant
                if (path.startsWith('/student/') || path.startsWith('/teacher/')) {
                    window.location.replace('/ai-chat');
                    return;
                }
            }

            if (window.I18n) window.I18n.render();
        });

        let currentBase64Image = null;

        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Max 5MB
            if (file.size > 5 * 1024 * 1024) {
                alert("File ảnh quá lớn! Vui lòng chọn ảnh dưới 5MB.");
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                currentBase64Image = event.target.result;
                document.getElementById('imagePreview').src = currentBase64Image;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        function removeImage() {
            currentBase64Image = null;
            document.getElementById('imageInput').value = '';
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const chatBox = document.getElementById('chatBox');
            const message = input.value.trim();
            if (!message && !currentBase64Image) return;

            let userContentHtml = '';
            if (currentBase64Image) {
                userContentHtml += `<img src="${currentBase64Image}" style="max-width: 200px; border-radius: 8px; margin-bottom: 8px; display: block;">`;
            }
            if (message) {
                userContentHtml += `<span>${escapeHtml(message)}</span>`;
            }

            // Render User Message
            chatBox.innerHTML += `
                <div class="msg-container user">
                    <div class="msg-avatar me" id="user-avatar-char">👤</div>
                    <div class="msg-bubble">${userContentHtml}</div>
                </div>
            `;
            
            const currUser = JSON.parse(localStorage.getItem('auth_user') || '{}');
            if (currUser && currUser.username) {
                const els = document.querySelectorAll('#user-avatar-char');
                els.forEach(el => el.innerText = currUser.username.charAt(0).toUpperCase());
            }

            const payload = { message: message };
            if (currentBase64Image) {
                payload.base64_image = currentBase64Image;
            }

            input.value = '';
            removeImage();
            chatBox.scrollTop = chatBox.scrollHeight;

            // Render Thinking Bubble
            const thinkingId = 'think_' + Date.now();
            chatBox.innerHTML += `
                <div class="msg-container ai" id="container_${thinkingId}">
                    <div class="msg-avatar ai">🤖</div>
                    <div class="msg-bubble thinking-bubble" id="${thinkingId}">
                        <div style="width: 15px; height: 15px; border: 2px solid var(--secondary); border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div> 
                        Đang phân tích câu hỏi...
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // Call public or authed chat API
                const headers = {};
                const token = localStorage.getItem('auth_token');
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }

                // Execute the post request using window.api or fetch
                const res = await window.api.post('/ai/chat', payload, { headers });
                
                const targetBubble = document.getElementById(thinkingId);
                targetBubble.classList.remove('thinking-bubble');

                const data = res.data;
                let htmlResponse = '';

                // Show external knowledge warning banner if is_external is true
                if (data.is_external || data.from_external) {
                    htmlResponse += `
                        <div class="warning-banner">
                            <span>⚠️ Nội dung dưới đây được tạo từ nguồn kiến thức bên ngoài hệ thống AI Study Hub LMS. Vui lòng kiểm chứng lại thông tin trước khi áp dụng.</span>
                        </div>
                    `;
                }

                // Configure marked renderer: internal links open in same tab, external in new tab
                if (window.marked) {
                    const renderer = new marked.Renderer();
                    renderer.link = (href, title, text) => {
                        let hrefStr = "";
                        if (href) {
                            if (typeof href === 'object' && href.href) {
                                hrefStr = href.href;
                                if (!text && href.text) text = href.text;
                                if (!title && href.title) title = href.title;
                            } else {
                                hrefStr = String(href);
                            }
                        }
                        const isInternal = hrefStr && (hrefStr.startsWith('/') || hrefStr.startsWith(window.location.origin));
                        const target = isInternal ? '' : ' target="_blank" rel="noopener noreferrer"';
                        const titleAttr = title ? ` title="${title}"` : '';
                        return `<a href="${hrefStr}"${titleAttr}${target} style="color:var(--primary);text-decoration:underline;font-weight:600;">${text || hrefStr}</a>`;
                    };
                    marked.setOptions({ renderer });
                }

                const parsedMarkdown = window.marked && window.marked.parse ? marked.parse(data.ai_response) : data.ai_response.replace(/\n/g, '<br>');
                htmlResponse += parsedMarkdown;

                targetBubble.innerHTML = htmlResponse;
                
                // Styles for code blocks
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
                targetBubble.innerHTML = "❌ Lỗi kết nối tới AI: " + err.message;
            }
            
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>
