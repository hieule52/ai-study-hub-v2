<?php
$pageTitle = 'Phòng Chat Giảng Viên - AI Study Hub';
$actor = 'teacher';
ob_start();
?>
<style>
        .chat-layout { 
            display: flex; 
            height: calc(100vh - 80px); 
            background: var(--bg-main);
        }
        
        .chat-sidebar { 
            width: 320px; 
            border-right: 1px solid rgba(255,255,255,0.05); 
            background: var(--bg-surface); 
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .contacts-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        
        .contact-item { 
            padding: 1rem; 
            border-radius: var(--radius-md); 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            transition: var(--transition); 
            margin-bottom: 0.5rem;
            border: 1px solid transparent;
        }
        
        .contact-item:hover, .contact-item.active { 
            background: rgba(79, 70, 229, 0.1); 
            border-color: rgba(79, 70, 229, 0.2);
        }

        .avatar {
            width: 45px; 
            height: 45px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold;
            font-size: 1.2rem;
            position: relative;
        }

        .status-dot {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: var(--success);
            border-radius: 50%;
            border: 2px solid var(--bg-surface);
        }

        .chat-main { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            position: relative;
        }
        
        .chat-header { 
            padding: 1.25rem 2rem; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            background: rgba(30, 41, 59, 0.8); 
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            z-index: 10;
        }

        .chat-header-info h3 {
            font-size: 1.25rem;
            margin-bottom: 0.1rem;
        }
        
        .chat-body { 
            flex: 1; 
            padding: 2rem; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            gap: 1.5rem; 
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDEwaDQwdjJWMHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMiIvPjxwYXRoIGQ9Ik0xMCAway0ydjQwaDJ6IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz48L3N2Zz4=');
        }
        
        .msg-row {
            display: flex;
            gap: 0.6rem;
            max-width: 75%;
            align-items: flex-end;
        }
        .msg-row.me {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        .msg-row.other {
            align-self: flex-start;
        }
        .msg-avatar {
            width: 32px; height: 32px; min-width: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem; color: #fff;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .msg-avatar.me-avatar { background: linear-gradient(135deg, var(--primary), #6366f1); }
        .msg-avatar.other-avatar { background: linear-gradient(135deg, var(--info), var(--secondary)); }
        .msg-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }

        .bubble-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .bubble-wrapper.me {
            align-items: flex-end;
        }

        .bubble-wrapper.other {
            align-items: flex-start;
        }

        .sender-name {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 2px;
            padding: 0 0.25rem;
        }

        .bubble { 
            padding: 0.75rem 1rem; 
            border-radius: var(--radius-lg); 
            position: relative; 
            font-size: 0.93rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            line-height: 1.45;
        }
        
        .bubble.me { 
            background: linear-gradient(135deg, var(--primary), #6366f1); 
            color: white; 
            border-bottom-right-radius: 4px; 
        }
        
        .bubble.other { 
            background: rgba(255,255,255,0.08); 
            color: var(--text-primary); 
            border-bottom-left-radius: 4px; 
            border: 1px solid rgba(255,255,255,0.05);
        }

        .timestamp {
            font-size: 0.68rem;
            color: var(--text-muted);
            margin: 0 0.25rem;
        }

        .chat-footer { 
            padding: 1.5rem 2rem; 
            border-top: 1px solid rgba(255,255,255,0.05); 
            background: rgba(30, 41, 59, 0.9); 
        }
        
        #wsChatForm {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: rgba(0,0,0,0.2);
            padding: 0.5rem;
            border-radius: var(--radius-xl);
            border: 1px solid rgba(255,255,255,0.1);
        }

        #wsInput {
            flex: 1;
            background: transparent;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
        }

        #wsInput:focus {
            outline: none;
            box-shadow: none;
            background: transparent;
        }
        
        .btn-send {
            background: var(--primary);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-send:hover {
            background: var(--primary-hover);
            transform: scale(1.05);
        }

    </style>
<?php
$extraHead = ob_get_clean();
require __DIR__ . '/../layouts/header.php';
?>

<div class="chat-layout">
                <div class="chat-sidebar">
                    <div class="sidebar-header">
                        <h3 style="font-size: 1.2rem;">Kênh <span class="text-gradient">Giải Đáp</span></h3>
                        <p class="text-secondary" style="font-size: 0.8rem;">Danh sách học viên của bạn</p>
                    </div>
                    
                    <div class="contacts-list" id="contactList">
<p class="text-secondary" style="padding:1rem;font-size:0.85rem;">Đang tải học viên...</p>
</div>
</div>

                <div class="chat-main">
                    <div class="chat-header" id="chatHeader">
                        <div class="avatar" style="background: linear-gradient(135deg, var(--warning), var(--danger));">?</div>
                        <div class="chat-header-info">
                            <h3>Chọn học viên để bắt đầu chat</h3>
<div style="font-size: 0.8rem; color: var(--text-secondary);">Danh sách bên trái là các học viên của bạn</div>
                            <div style="font-size: 0.8rem; color: var(--success); display: flex; align-items: center; gap: 5px;">
                                <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div> 
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-body" id="chatWindow">
                        <div class="bubble-wrapper other">
                            <div class="bubble other">
                                Hệ thống tự động: Xin chào giảng viên, hãy bắt đầu trả lời tin nhắn từ học viên của bạn.
                            </div>
                            <span class="timestamp">00:00 AM</span>
                        </div>
                    </div>

                    <div class="chat-footer">
                        <form id="wsChatForm">
                            <div style="color: var(--text-secondary); cursor: pointer; padding: 0 0.5rem; font-size: 1.2rem;">🗣️</div>
                            <input type="text" id="wsInput" placeholder="Nhập câu trả lời..." autocomplete="off" required>
                            <button type="submit" class="btn-send">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M22 2L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

<?php ob_start(); ?>
<script>
        let socket;
        let currentReceiverId = null;

        document.addEventListener('DOMContentLoaded', async () => {
            const user = App.requireAuth(['teacher', 'admin']);
            if (!user) return;

            const token = window.api.getToken();

            const getTime = () => {
                const now = new Date();
                let h = now.getHours();
                let m = now.getMinutes() < 10 ? '0'+now.getMinutes() : now.getMinutes();
                let ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                return `${h}:${m} ${ampm}`;
            };

            await loadContacts(user);

            if (token) {
                socket = new WebSocket(`ws://localhost:8080?token=${token}`);

                socket.onopen = function() {
                    console.log("[WS] Teacher Connected!");
                };

                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    if(data.error) { App.showToast(data.error, 'error'); return; }
                    if(data.type === 'connected') return;

                    const chatWin = document.getElementById('chatWindow');
                    if (data.type === 'message') {
                        if (data.sender_id == currentReceiverId) {
                            chatWin.innerHTML += `
                                <div class="msg-row other">
                                    ${renderAvatar(data.sender_name, data.sender_avatar, 'other-avatar')}
                                    <div class="bubble-wrapper other">
                                        <span class="sender-name">${escapeHtml(data.sender_name)}</span>
                                        <div class="bubble other">${escapeHtml(data.content)}</div>
                                        <span class="timestamp">${getTime()}</span>
                                    </div>
                                </div>
                            `;
                            chatWin.scrollTop = chatWin.scrollHeight;
                        }
                    } else if (data.type === 'sent') {
                        const myUser = window.api.getUser();
                        chatWin.innerHTML += `
                            <div class="msg-row me">
                                ${renderAvatar(myUser.username || myUser.email, myUser.avatar, 'me-avatar')}
                                <div class="bubble-wrapper me">
                                    <div class="bubble me">${escapeHtml(data.content)}</div>
                                    <span class="timestamp">${getTime()} ${data.delivered ? '✓✓' : '✓'}</span>
                                </div>
                            </div>
                        `;
                        chatWin.scrollTop = chatWin.scrollHeight;
                    }
                };

                socket.onerror = function(error) {
                    App.showToast("Lỗi WebSocket. Hãy chắc chắn ws server đang chạy.", "error");
                };

                const form = document.getElementById('wsChatForm');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (!currentReceiverId) {
                        App.showToast('Vui lòng chọn học viên để nhắn tin', 'error');
                        return;
                    }
                    const input = document.getElementById('wsInput');
                    const msg = input.value.trim();
                    if(!msg || socket.readyState !== WebSocket.OPEN) return;

                    socket.send(JSON.stringify({ receiver_id: currentReceiverId, content: msg }));
                    input.value = '';
                });
            }
        });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderAvatar(name, avatarUrl, cssClass) {
            const initial = (name || '?').charAt(0).toUpperCase();
            if (avatarUrl) {
                return `<div class="msg-avatar ${cssClass}"><img src="${avatarUrl}" alt="${initial}"></div>`;
            }
            return `<div class="msg-avatar ${cssClass}">${initial}</div>`;
        }

        async function loadContacts(user) {
            try {
                const res = await window.api.get('/teacher/students');
                const students = res.data || [];

                const studentMap = {};
                students.forEach(s => {
                    if (s.user_id && !studentMap[s.user_id]) {
                        studentMap[s.user_id] = {
                            id: s.user_id,
                            name: s.username || 'Học viên',
                            avatar: s.avatar || null,
                            course: s.course_title || 'Khóa học'
                        };
                    }
                });

                const contactList = document.getElementById('contactList');
                const uniqueStudents = Object.values(studentMap);

                if (uniqueStudents.length === 0) {
                    contactList.innerHTML = '<p class="text-secondary" style="padding:1rem;font-size:0.85rem;">Bạn chưa có học viên nào.</p>';
                    return;
                }

                contactList.innerHTML = uniqueStudents.map(s => {
                    const initial = s.name.charAt(0).toUpperCase();
                    const avatarHtml = s.avatar
                        ? `<div class="avatar" style="background: linear-gradient(135deg, var(--info), var(--primary));overflow:hidden;"><img src="${s.avatar}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><div class="status-dot"></div></div>`
                        : `<div class="avatar" style="background: linear-gradient(135deg, var(--info), var(--primary));">${initial}<div class="status-dot"></div></div>`;
                    return `
                    <div class="contact-item" data-id="${s.id}" onclick="selectContact(${s.id}, '${escapeHtml(s.name)}', '${s.avatar || ''}')">
                        ${avatarHtml}
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h4 style="font-size: 0.95rem; margin: 0;">🎓 ${escapeHtml(s.name)}</h4>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-secondary); margin-top: 2px;">📚 ${escapeHtml(s.course)}</div>
                        </div>
                    </div>`;
                }).join('');
            } catch (e) {
                console.error('Lỗi tải học viên:', e);
            }
        }

        async function selectContact(studentId, studentName, studentAvatar = '') {
            currentReceiverId = studentId;

            document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
            const activeEl = document.querySelector(`.contact-item[data-id="${studentId}"]`);
            if (activeEl) activeEl.classList.add('active');

            const initial = studentName.charAt(0).toUpperCase();
            const avatarHtml = studentAvatar
                ? `<div class="avatar" style="background: linear-gradient(135deg, var(--info), var(--primary));overflow:hidden;"><img src="${studentAvatar}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>`
                : `<div class="avatar" style="background: linear-gradient(135deg, var(--info), var(--primary));">${initial}</div>`;

            document.getElementById('chatHeader').innerHTML = `
                ${avatarHtml}
                <div class="chat-header-info">
                    <h3>🎓 ${escapeHtml(studentName)}</h3>
                    <div style="font-size: 0.8rem; color: var(--success); display: flex; align-items: center; gap: 5px;">
                        <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div>
                        <span>Học viên của bạn</span>
                    </div>
                </div>
            `;
            
            const chatWin = document.getElementById('chatWindow');
            chatWin.innerHTML = '<p class="text-secondary text-center" style="padding:1rem;">Đang tải tin nhắn...</p>';

            try {
                const res = await window.api.get(`/chat/history?user_id=${studentId}`);
                const msgs = res.data || [];
                
                chatWin.innerHTML = '';
                
                const getTime = (dateStr) => {
                    const now = new Date(dateStr);
                    let h = now.getHours();
                    let m = now.getMinutes() < 10 ? '0' + now.getMinutes() : now.getMinutes();
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    h = h % 12 || 12;
                    return `${h}:${m} ${ampm}`;
                };
                
                const myId = window.api.getUser().id;
                
                if (msgs.length === 0) {
                    chatWin.innerHTML = `<p class="text-secondary text-center" style="padding:1rem;">Bắt đầu trò chuyện với ${escapeHtml(studentName)}</p>`;
                } else {
                    const myUser = window.api.getUser();
                    chatWin.innerHTML = msgs.map(m => {
                        const isMe = (m.sender_id == myId);
                        if (isMe) {
                            return `
                            <div class="msg-row me">
                                ${renderAvatar(myUser.username || myUser.email, myUser.avatar, 'me-avatar')}
                                <div class="bubble-wrapper me">
                                    <div class="bubble me">${escapeHtml(m.content)}</div>
                                    <span class="timestamp">${getTime(m.created_at)} ✓✓</span>
                                </div>
                            </div>`;
                        } else {
                            return `
                            <div class="msg-row other">
                                ${renderAvatar(m.sender_name, m.sender_avatar, 'other-avatar')}
                                <div class="bubble-wrapper other">
                                    <span class="sender-name">${escapeHtml(m.sender_name)}</span>
                                    <div class="bubble other">${escapeHtml(m.content)}</div>
                                    <span class="timestamp">${getTime(m.created_at)}</span>
                                </div>
                            </div>`;
                        }
                    }).join('');
                }
                chatWin.scrollTop = chatWin.scrollHeight;
            } catch (e) {
                chatWin.innerHTML = '<p class="text-danger text-center" style="padding:1rem;">Lỗi tải tin nhắn</p>';
            }
        }

        window.escapeHtml = escapeHtml;
        window.selectContact = selectContact;
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
