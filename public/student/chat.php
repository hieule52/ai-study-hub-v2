<?php
$pageTitle = 'Tin nhắn - AI Study Hub';
$actor = 'student';
$extraHead = '<link rel="stylesheet" href="/assets/css/student/chat.css?v=' . time() . '">';
require __DIR__ . '/../layouts/header.php';
?>

<div class="chat-layout">
    <!-- Contacts Sidebar -->
    <aside class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h2 style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em;" data-i18n="chat_title">Tin nhắn</h2>
            <div class="chat-search-wrap">
                <div style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); opacity: 0.5;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input type="text" id="search-teacher-input" class="chat-search-input" placeholder="Tìm giảng viên..." data-i18n="chat_search">
            </div>
        </div>
        
        <div class="contact-list" id="contactList">
            <div class="p-6 text-center text-muted" style="font-size: 0.85rem;" data-i18n="home_loading">Đang tải giảng viên...</div>
        </div>
    </aside>

    <!-- Main Chat Window -->
    <main class="chat-main" id="chatMain">
        <!-- Default State -->
        <div class="chat-empty" id="chatEmptyState">
            <div style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;" data-i18n="chat_select_to_start">Chọn giảng viên để bắt đầu</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;" data-i18n="chat_select_to_start_subtitle">Kết nối với giảng viên của bạn để được hỗ trợ học tập.</p>
        </div>

        <!-- Chat Active State (Hidden by default) -->
        <div id="chatActiveState" style="display: none; flex-direction: column; height: 100%;">
            <div class="chat-header" id="chatHeader">
                <!-- Avatar & Info populated via JS -->
            </div>
            
            <div class="chat-messages" id="chatWindow">
                <!-- Messages populated via JS -->
            </div>

            <div class="chat-input-area">
                <button class="btn-ghost" style="width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; opacity: 0.6;">📎</button>
                <div class="chat-input-wrap">
                    <form id="wsChatForm" style="display: flex; gap: 0.75rem;">
                        <input type="text" id="wsInput" class="chat-input" placeholder="Nhập tin nhắn..." autocomplete="off" required data-i18n="chat_input">
                        <button type="submit" class="btn-send">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php ob_start(); ?>
<script>
    let socket;
    let currentReceiverId = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const user = App.requireAuth(['student', 'teacher']);
        if (!user) return;
        if (window.I18n) window.I18n.render();

        const token = window.api.getToken();

        // Contact Search
        const searchInput = document.getElementById('search-teacher-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const keyword = e.target.value.toLowerCase().trim();
                document.querySelectorAll('.contact-item').forEach(item => {
                    const name = item.querySelector('.contact-name').innerText.toLowerCase();
                    const course = item.querySelector('.contact-status').innerText.toLowerCase();
                    if (name.includes(keyword) || course.includes(keyword)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        await loadContacts(user);

        if (token) {
            connectWS(token);
        }

        document.getElementById('wsChatForm').addEventListener('submit', (e) => {
            e.preventDefault();
            if (!currentReceiverId) {
                App.showToast('Vui lòng chọn người liên hệ.', 'info');
                return;
            }
            const input = document.getElementById('wsInput');
            const msg = input.value.trim();
            
            if (!msg) return;

            if (!socket || socket.readyState !== WebSocket.OPEN) {
                App.showToast('Mất kết nối máy chủ tin nhắn. Đang thử kết nối lại...', 'error');
                connectWS(token);
                return;
            }

            // Gửi qua WebSocket
            socket.send(JSON.stringify({ receiver_id: currentReceiverId, content: msg }));
            
            // Hiển thị ngay lập tức (Optimistic UI)
            appendMessage(msg, 'sent');
            
            input.value = '';
        });
    });

    function connectWS(token) {
        socket = new WebSocket(`ws://localhost:8080?token=${token}`);
        
        socket.onopen = () => {
            console.log('[WS] Connected');
        };

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.error) { App.showToast(data.error, 'error'); return; }
            if (data.type === 'connected') return;

            if (data.type === 'message') {
                if (data.sender_id == currentReceiverId) {
                    appendMessage(data.content, 'received', data.sender_avatar);
                } else {
                    App.showToast(`Tin nhắn mới từ ${data.sender_name}`, 'info');
                }
            }
            // Loại bỏ 'sent' ở đây vì đã hiển thị Optimistic UI
        };

        socket.onclose = () => {
            console.log('[WS] Disconnected');
            // Thử kết nối lại sau 3 giây
            setTimeout(() => connectWS(token), 3000);
        };
    }

    async function loadContacts(user) {
        try {
            const res = await window.api.get('/student/courses');
            const courses = res.data || [];
            const teacherMap = {};
            courses.forEach(c => {
                if (c.teacher_id && !teacherMap[c.teacher_id]) {
                    teacherMap[c.teacher_id] = {
                        id: c.teacher_id,
                        name: c.teacher_name || 'Giảng viên',
                        avatar: c.teacher_avatar || null,
                        course: c.title
                    };
                }
            });

            const contactList = document.getElementById('contactList');
            const teachers = Object.values(teacherMap);

            if (teachers.length === 0) {
                contactList.innerHTML = `<div class="p-6 text-center text-muted" data-i18n="chat_no_enroll_msg">Chưa có giảng viên liên hệ.</div>`;
                if (window.I18n) window.I18n.render();
                return;
            }

            contactList.innerHTML = teachers.map(t => {
                const initial = t.name.charAt(0).toUpperCase();
                const avatarHtml = t.avatar
                    ? `<img src="${t.avatar}" class="contact-avatar">`
                    : `<div class="contact-avatar" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">${initial}</div>`;
                return `
                <div class="contact-item" data-id="${t.id}" onclick="selectContact(${t.id}, '${escapeHtml(t.name)}', '${t.avatar || ''}')">
                    <div style="position: relative;">
                        ${avatarHtml}
                        <div style="position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; background: var(--success); border-radius: 50%; border: 2px solid #0f172a;"></div>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">${escapeHtml(t.name)}</div>
                        <div class="contact-status">${escapeHtml(t.course)}</div>
                    </div>
                </div>`;
            }).join('');
        } catch (e) {
            console.error(e);
        }
    }

    async function selectContact(teacherId, teacherName, teacherAvatar = '') {
        currentReceiverId = teacherId;
        document.getElementById('chatEmptyState').style.display = 'none';
        document.getElementById('chatActiveState').style.display = 'flex';

        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        const activeEl = document.querySelector(`.contact-item[data-id="${teacherId}"]`);
        if (activeEl) activeEl.classList.add('active');

        const initial = teacherName.charAt(0).toUpperCase();
        const avatarHtml = teacherAvatar
            ? `<img src="${teacherAvatar}" class="contact-avatar" style="width:40px; height:40px;">`
            : `<div class="contact-avatar" style="width:40px; height:40px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">${initial}</div>`;

        document.getElementById('chatHeader').innerHTML = `
            ${avatarHtml}
            <div>
                <h3 style="font-size: 1rem; font-weight: 700;">${escapeHtml(teacherName)}</h3>
                <div style="font-size: 0.75rem; color: var(--success); display: flex; align-items: center; gap: 4px;">
                    <div style="width: 6px; height: 6px; background: var(--success); border-radius: 50%;"></div>
                    Đang trực tuyến
                </div>
            </div>
        `;

        const chatWin = document.getElementById('chatWindow');
        chatWin.innerHTML = '<div class="p-6 text-center opacity-50">Đang tải lịch sử...</div>';

        try {
            const res = await window.api.get(`/chat/history?user_id=${teacherId}`);
            const msgs = res.data || [];
            chatWin.innerHTML = '';
            const myId = window.api.getUser().id;
            
            if (msgs.length === 0) {
                chatWin.innerHTML = `<div class="p-6 text-center opacity-40">Bắt đầu trò chuyện với ${escapeHtml(teacherName)}</div>`;
            } else {
                msgs.forEach(m => {
                    const type = (m.sender_id == myId) ? 'sent' : 'received';
                    appendMessage(m.content, type, m.sender_avatar, m.created_at);
                });
            }
            chatWin.scrollTop = chatWin.scrollHeight;
        } catch (e) {
            chatWin.innerHTML = '<div class="p-6 text-center text-danger">Lỗi tải dữ liệu.</div>';
        }
    }

    function appendMessage(content, type, avatar = null, time = null) {
        const chatWin = document.getElementById('chatWindow');
        const now = time ? new Date(time) : new Date();
        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${type}`;
        msgDiv.innerHTML = `
            ${content}
            <span class="message-time">${timeStr}</span>
        `;
        chatWin.appendChild(msgDiv);
        chatWin.scrollTop = chatWin.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
