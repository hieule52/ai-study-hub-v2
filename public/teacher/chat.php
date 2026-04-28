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
        
        .bubble-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            max-width: 70%;
        }

        .bubble-wrapper.me {
            align-self: flex-end;
            align-items: flex-end;
        }

        .bubble-wrapper.other {
            align-self: flex-start;
            align-items: flex-start;
        }

        .bubble { 
            padding: 1rem 1.25rem; 
            border-radius: var(--radius-lg); 
            position: relative; 
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
            font-size: 0.7rem;
            color: var(--text-muted);
            margin: 0 0.5rem;
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
                        <p class="text-secondary" style="font-size: 0.8rem;">World Chat (Hiện tại gộp chung lớp học)</p>
                    </div>
                    
                    <div class="contacts-list" id="contactList">
                        <div class="contact-item active">
                            <div class="avatar" style="background: linear-gradient(135deg, var(--warning), var(--danger));">
                                🌎
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="font-size: 0.95rem; margin: 0;">Lớp Học Chung</h4>
                                    <span style="font-size: 0.7rem; color: var(--success);">Live</span>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 2px;">Trực tuyến nhận câu hỏi...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chat-main">
                    <div class="chat-header">
                        <div class="avatar" style="background: linear-gradient(135deg, var(--warning), var(--danger));">🌎</div>
                        <div class="chat-header-info">
                            <h3>Lớp Học Chung (World)</h3>
                            <div style="font-size: 0.8rem; color: var(--success); display: flex; align-items: center; gap: 5px;">
                                <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div> WebSocket Connected
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
                            <input type="text" id="wsInput" placeholder="Nhập câu trả lời (Gửi chung)..." autocomplete="off" required>
                            <button type="submit" class="btn-send">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M22 2L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

<?php ob_start(); ?>
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = App.requireAuth(['teacher', 'admin']);
            if (!user) return;

            const token = window.api.getToken();
            let socket;

            const getTime = () => {
                const now = new Date();
                let h = now.getHours();
                let m = now.getMinutes() < 10 ? '0'+now.getMinutes() : now.getMinutes();
                let ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                return `${h}:${m} ${ampm}`;
            };

            if (token) {
                socket = new WebSocket(`ws://localhost:8080?token=${token}`);

                socket.onopen = function() {
                    console.log("Teacher Socket Connected!");
                };

                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    if(data.error) return;

                    const chatWin = document.getElementById('chatWindow');
                    chatWin.innerHTML += `
                        <div class="bubble-wrapper other">
                            <div class="bubble other"><strong>Student</strong>: ${data.content}</div>
                            <span class="timestamp">${getTime()}</span>
                        </div>
                    `;
                    chatWin.scrollTop = chatWin.scrollHeight;
                };

                socket.onerror = function(error) {
                    App.showToast("Lỗi WebSocket. Hãy chắc chắn ws server đang chạy.", "error");
                };

                const form = document.getElementById('wsChatForm');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const input = document.getElementById('wsInput');
                    const msg = input.value.trim();
                    if(!msg) return;

                    // Receiver 1 = global room / mock
                    const payload = { receiver_id: 1, content: msg };
                    if(socket.readyState === WebSocket.OPEN) {
                        socket.send(JSON.stringify(payload));
                    }

                    const chatWin = document.getElementById('chatWindow');
                    chatWin.innerHTML += `
                        <div class="bubble-wrapper me">
                            <div class="bubble me">${msg}</div>
                            <span class="timestamp">${getTime()}</span>
                        </div>
                    `;
                    input.value = '';
                    setTimeout(() => { chatWin.scrollTop = chatWin.scrollHeight; }, 50);
                });
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
