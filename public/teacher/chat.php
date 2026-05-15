<?php
$pageTitle = 'Teacher Chat - AI Study Hub';
$actor = 'teacher';
$noSidebar = true;
$extraHead = '<link rel="stylesheet" href="/assets/css/teacher/dashboard.css?v=' . time() . '">
              <link rel="stylesheet" href="/assets/css/teacher/chat.css?v=' . time() . '">
              <script src="/assets/js/teacher/notifications.js?v=' . time() . '" defer></script>';
require __DIR__ . '/../layouts/header.php';
?>

<div class="teacher-layout">
    <?php require __DIR__ . '/../layouts/teacher_sidebar.php'; ?>

    <!-- Chat Interface -->
    <div class="chat-container">
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 2rem;">
                    <h2 data-i18n="nav_teacher_chat">Tin nhắn</h2>
                    <div class="notif-bell" id="teacherNotifBell" onclick="toggleTeacherNotif(event)" style="position: relative; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 0.6rem; border-radius: 50%; cursor: pointer;">
                        <i class="fas fa-bell" style="font-size: 0.9rem; opacity: 0.6;"></i>
                        <span id="teacherNotifBadge" style="position: absolute; top: -2px; right: -2px; background: var(--danger); color: white; font-size: 0.5rem; padding: 2px 5px; border-radius: 50%; display: none;">0</span>
                        <div id="teacherNotifDropdown" class="notif-dropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 300px; z-index: 1000; background: var(--bg-layer2); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); margin-top: 1.5rem; box-shadow: 0 30px 60px rgba(0,0,0,0.4); text-align: left;">
                            <div style="padding: 1rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; font-size: 0.85rem;" data-i18n="nav_hub_menu">Thông báo</span>
                                <button onclick="markTeacherNotifRead(event)" style="background: none; border: none; color: var(--primary); font-size: 0.75rem; cursor: pointer;" data-i18n="nav_hub_mark_read">Xác nhận</button>
                            </div>
                            <div id="teacherNotifList" style="max-height: 350px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
                <div class="chat-input-wrap" style="padding: 0.25rem 1rem;">
                    <i class="fas fa-search" style="opacity: 0.3;"></i>
                    <input type="text" id="contactSearch" class="chat-input" data-i18n="tc_chat_search" placeholder="Tìm kiếm học viên..." style="padding: 0.5rem 0.5rem;">
                </div>
            </div>
            <div class="contact-list" id="contactList">
                <div class="p-10 text-center opacity-30">Đang tải học viên...</div>
            </div>
        </aside>

        <main class="chat-main">
            <!-- Empty State -->
            <div id="chatEmptyState" class="chat-empty">
                <div style="font-size: 5rem; margin-bottom: 2rem; opacity: 0.05;">💬</div>
                <h3 data-i18n="tc_chat_select_student">Chọn học viên để bắt đầu hỗ trợ</h3>
                <p data-i18n="tc_chat_select_student_subtitle" style="opacity: 0.4; max-width: 300px;">Giải đáp thắc mắc và đồng hành cùng sự tiến bộ của học viên.</p>
            </div>

            <!-- Active Chat -->
            <div id="chatActiveState" style="display: none; flex-direction: column; height: 100%;">
                <header class="chat-header">
                    <div id="activeAvatar"></div>
                    <div>
                        <h3 id="activeName" style="font-size: 1.2rem; font-weight: 800; margin: 0; letter-spacing: -0.01em;">-</h3>
                        <div id="activeStatus" style="font-size: 0.8rem; font-weight: 500;"></div>
                    </div>
                </header>

                <div class="chat-messages" id="chatWindow">
                    <!-- Messages populated via JS -->
                </div>

                <footer class="chat-footer">
                    <form id="wsChatForm">
                        <div class="chat-input-wrap">
                            <input type="text" id="wsInput" class="chat-input" data-i18n="chat_input" placeholder="Nhập tin nhắn hỗ trợ..." autocomplete="off" required>
                            <button type="submit" class="btn-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </footer>
            </div>
        </main>
    </div>
</div>

<!-- Load Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
let socket;
let currentReceiverId = null;

document.addEventListener('DOMContentLoaded', async () => {
    const user = App.requireAuth(['teacher', 'admin']);
    if (!user) return;
    if (window.I18n) window.I18n.render();

    const token = window.api.getToken();
    if (token) {
        socket = new WebSocket(`ws://localhost:8080?token=${token}`);

        socket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if(data.type === 'message' && data.sender_id == currentReceiverId) {
                renderMessage(data, 'received');
            } else if(data.type === 'sent') {
                renderMessage(data, 'sent');
            }
        };

        const form = document.getElementById('wsChatForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!currentReceiverId) return;
            const input = document.getElementById('wsInput');
            const msg = input.value.trim();
            if(!msg || socket.readyState !== WebSocket.OPEN) return;
            socket.send(JSON.stringify({ receiver_id: currentReceiverId, content: msg }));
            input.value = '';
        });
    }

    loadContacts();
    loadNotifications();

    // Search Filter Logic
    document.getElementById('contactSearch').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.contact-item').forEach(item => {
            const name = item.querySelector('.contact-name').innerText.toLowerCase();
            const preview = item.querySelector('.contact-preview').innerText.toLowerCase();
            if (name.includes(term) || preview.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

function isOnline(lastSeen) {
    if (!lastSeen) return false;
    const diff = (new Date() - new Date(lastSeen)) / 1000 / 60;
    return diff < 5;
}


async function loadContacts() {
    try {
        const res = await window.api.get('/teacher/students');
        const students = res.data || [];
        
        const list = document.getElementById('contactList');
        if (students.length === 0) {
            list.innerHTML = `<div class="p-10 text-center opacity-30">${I18n.get('tc_dash_no_courses')}</div>`;
            return;
        }

        const studentMap = {};
        students.forEach(s => {
            if (!studentMap[s.user_id]) studentMap[s.user_id] = s;
        });
        const uniqueStudents = Object.values(studentMap);

        list.innerHTML = uniqueStudents.map(s => {
            const online = isOnline(s.last_seen);
            return `
                <div class="contact-item ${currentReceiverId == s.user_id ? 'active' : ''}" data-id="${s.user_id}" onclick="selectContact(${s.user_id}, '${escapeHtml(s.username)}', '${s.avatar || ''}', ${online})">
                    <div class="contact-avatar">
                        ${getAvatarHtml(s.username, s.avatar, 48)}
                        <div class="status-indicator ${online ? 'status-online' : 'status-offline'}"></div>
                    </div>
                    <div class="contact-info">
                        <span class="contact-name">${escapeHtml(s.username)}</span>
                        <span class="contact-preview">${escapeHtml(s.course_title)}</span>
                    </div>
                </div>
            `;
        }).join('');
    } catch (err) { console.error(err); }
}

function selectContact(id, name, avatar, online) {
    currentReceiverId = id;
    document.getElementById('chatEmptyState').style.display = 'none';
    document.getElementById('chatActiveState').style.display = 'flex';
    
    document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.contact-item[data-id="${id}"]`)?.classList.add('active');

    document.getElementById('activeName').innerText = name;
    document.getElementById('activeStatus').innerText = online ? I18n.get('chat_online') : I18n.get('chat_offline');
    document.getElementById('activeStatus').style.color = online ? '#10b981' : '#64748b';
    
    const avatarContainer = document.getElementById('activeAvatar');
    avatarContainer.innerHTML = getAvatarHtml(name, avatar, 54);

    loadMessages(id);
}

async function loadMessages(id) {
    const chatWin = document.getElementById('chatWindow');
    chatWin.innerHTML = '<div class="p-10 text-center opacity-30">Đang tải lịch sử hỗ trợ...</div>';
    try {
        const res = await window.api.get(`/chat/history?user_id=${id}`);
        chatWin.innerHTML = '';
        const msgs = res.data || [];
        msgs.forEach(msg => {
            const type = msg.sender_id == id ? 'received' : 'sent';
            renderMessage(msg, type);
        });
        scrollToBottom();
    } catch (err) { console.error(err); }
}

function renderMessage(msg, type) {
    const chatWindow = document.getElementById('chatWindow');
    
    // Fix Invalid Date by providing fallback to current time
    let dateObj = msg.created_at ? new Date(msg.created_at) : new Date();
    if (isNaN(dateObj.getTime())) dateObj = new Date();
    
    const time = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    const div = document.createElement('div');
    div.className = `message-row ${type}`;
    div.innerHTML = `
        <div class="message-bubble">
            ${escapeHtml(msg.content)}
            <span class="message-time">${time}</span>
        </div>
    `;
    chatWindow.appendChild(div);
    scrollToBottom();
}

function scrollToBottom() {
    const chatWindow = document.getElementById('chatWindow');
    chatWindow.scrollTop = chatWindow.scrollHeight;
}

function getAvatarHtml(name, avatarUrl, size = 40) {
    if (avatarUrl) {
        return `<img src="${avatarUrl}" style="width:${size}px; height:${size}px; border-radius: 50%; object-fit:cover; border:1px solid var(--glass-border);">`;
    }
    const initial = name ? name.charAt(0).toUpperCase() : '?';
    return `
        <div style="width:${size}px; height:${size}px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #818cf8); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:${size/2.2}px; border:1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.2); line-height: 1;">
            ${initial}
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
