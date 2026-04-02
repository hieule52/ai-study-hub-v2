document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const userCards = document.querySelectorAll(".user-card");
    const searchInput = document.getElementById("searchUsers");

    // Filter theo status
    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const filter = btn.dataset.filter;

            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            userCards.forEach(card => {
                if (filter === "all" || card.dataset.status === filter) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    });

    // Search theo tên
    searchInput.addEventListener("input", () => {
        const keyword = searchInput.value.toLowerCase();
        userCards.forEach(card => {
            const name = card.querySelector(".user-name").textContent.toLowerCase();
            card.style.display = name.includes(keyword) ? "block" : "none";
        });
    });
});
let lastMessageId = 0;

setInterval(() => {
    const friendId = document.querySelector('input[name="receiver_id"]').value;
    fetch(`/chat/fetch?friend_id=${friendId}&after_id=${lastMessageId}`)
        .then(res => res.json())
        .then(data => {
            const chatBox = document.getElementById('chatBox');
            data.messages.forEach(m => {
                const div = document.createElement('div');
                div.innerHTML = `<strong>${m.sender_id == CURRENT_USER_ID ? 'Bạn' : FRIEND_NAME}:</strong> ${m.content}`;
                chatBox.appendChild(div);
                lastMessageId = m.id;
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}, 1000);