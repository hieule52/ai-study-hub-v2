<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat với <?= htmlspecialchars($friend['username']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <link rel="stylesheet" href="/assets/css/chat.css">
</head>

<body>

    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                Chat với <?= htmlspecialchars($friend['username']) ?>
            </div>
            <div class="chat-box" id="chatBox">
                <?php foreach ($messages as $m):
                    $isSent = $m['sender_id'] == $_SESSION['user_id'];
                    $avatar = $isSent ? $_SESSION['avatar'] : ($friend['avatar'] ?: 'default.png');

                ?>
                    <div class="message-wrapper <?= $isSent ? 'sent' : 'received' ?>">
                        <?php if (!$isSent): ?>
                            <div class="message-avatar">
                                <img src="/assets/avatars/<?= htmlspecialchars($avatar) ?>" alt="avatar">
                            </div>
                        <?php endif; ?>

                        <div class="message">
                            <?= htmlspecialchars($m['content']) ?>
                        </div>

                        <?php if ($isSent): ?>
                            <div class="message-avatar">
                                <img src="/assets/avatars/<?= htmlspecialchars($avatar) ?>" alt="avatar">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form id="chatForm" class="chat-input">
                <input type="text" id="msgInput" placeholder="Nhập tin nhắn..." required>
                <button type="submit">Gửi</button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?>;
        const FRIEND_ID = <?= $friend['id'] ?>;
        const CURRENT_AVATAR = "/assets/avatars/<?= addslashes($_SESSION['avatar'] ?: 'default.png') ?>";
        const FRIEND_AVATAR = "/assets/avatars/<?= addslashes($friend['avatar'] ?: 'default.png') ?>";


        const WEBSOCKET_PORT = "<?= $_ENV['WEBSOCKET_PORT'] ?? '9090' ?>";
        const ws = new WebSocket(`ws://localhost:${WEBSOCKET_PORT}`);

        ws.onopen = () => {
            ws.send(JSON.stringify({
                type: "register",
                user_id: CURRENT_USER_ID
            }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === "message") {
                const chatBox = document.getElementById("chatBox");
                const divWrapper = document.createElement("div");
                divWrapper.classList.add("message-wrapper");
                const isSent = data.sender_id == CURRENT_USER_ID;
                divWrapper.classList.add(isSent ? "sent" : "received");

                const avatarDiv = document.createElement("div");
                avatarDiv.classList.add("message-avatar");
                const img = document.createElement("img");
                img.src = isSent ? CURRENT_AVATAR : FRIEND_AVATAR;
                avatarDiv.appendChild(img);

                const msgDiv = document.createElement("div");
                msgDiv.classList.add("message");
                msgDiv.textContent = data.content;

                if (isSent) {
                    divWrapper.appendChild(msgDiv);
                    divWrapper.appendChild(avatarDiv);
                } else {
                    divWrapper.appendChild(avatarDiv);
                    divWrapper.appendChild(msgDiv);
                }

                chatBox.appendChild(divWrapper);
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };

        document.getElementById("chatForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const content = document.getElementById("msgInput").value.trim();
            if (!content) return;
            ws.send(JSON.stringify({
                type: "message",
                sender_id: CURRENT_USER_ID,
                receiver_id: FRIEND_ID,
                content: content
            }));
            document.getElementById("msgInput").value = "";
        });

        // Scroll xuống dưới khi load trang
        const chatBox = document.getElementById("chatBox");
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>

</body>

</html>