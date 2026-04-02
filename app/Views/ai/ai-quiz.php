<%@ page contentType="text/html;charset=UTF-8" %> <%@taglib
uri="http://java.sun.com/jsp/jstl/core" prefix="c"%>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Learning Hub - Tạo Câu Hỏi Trắc Nghiệm</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link
        rel="stylesheet"
        href="${pageContext.request.contextPath}/assets/css/style/ai-quiz.css" />
</head>

<body>
    <jsp:include page="../../inc/navbar.jsp" />

    <!-- Chat Container -->
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="header-content">
                            <div class="ai-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="chat-title">❓ AI Tạo Quiz Trắc Nghiệm</h1>
                                <p class="chat-subtitle">
                                    Trợ lý AI tạo câu hỏi trắc nghiệm giúp bạn ôn tập hiệu quả
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chat-stats">
                            <div class="stat-item">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Sẵn sàng</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">∞</span>
                                <span class="stat-label">Câu hỏi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Chat Messages -->
                        <div class="chat-messages" id="chatMessages">
                            <div class="message ai-message">
                                <div class="message-avatar">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-bubble">
                                        <p>Xin chào! Tôi là AI Quiz Generator.</p>
                                        <p>Hãy nhập chủ đề để tôi tạo câu hỏi trắc nghiệm cho bạn!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lịch sử từ database -->
                            <c:forEach var="msg" items="${quizHistory}">

                                <!-- USER MESSAGE -->
                                <c:if test="${msg.inputText != null}">
                                    <div class="message user-message">
                                        <div class="message-content">
                                            <div class="message-bubble">${msg.inputText}</div>
                                        </div>
                                        <div class="message-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </c:if>

                                <!-- AI MESSAGE -->
                                <div class="message ai-message">
                                    <div class="message-avatar">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-bubble">
                                            ${msg.outputText}
                                        </div>
                                    </div>
                                </div>

                            </c:forEach>
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input-container">
                            <form
                                method="post"
                                action="ai-quiz"
                                class="chat-form"
                                id="chatForm">
                                <div class="input-wrapper">
                                    <div class="input-actions">
                                        <button
                                            type="button"
                                            class="action-btn"
                                            title="Đính kèm file">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                        <button type="button" class="action-btn" title="Ghi âm">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    <textarea
                                        name="message"
                                        class="chat-input"
                                        placeholder="Nhập chủ đề cần tạo quiz (VD: Toán lớp 10, Lịch sử Việt Nam...)"
                                        rows="1"
                                        required></textarea>
                                    <button type="submit" class="send-btn" disabled>
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Quick Suggestions -->
                        <div class="quick-suggestions">
                            <div class="suggestions-title">💡 Gợi ý chủ đề:</div>
                            <div class="suggestions-list">
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Tạo quiz về định lý Pythagore')">
                                    🔢 Quiz định lý Pythagore
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Tạo quiz về quang hợp của cây xanh')">
                                    🌱 Quiz quang hợp
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Tạo quiz về Chiến tranh thế giới thứ 2')">
                                    🌍 Quiz Chiến tranh TG2
                                </button>
                                <button
                                    class="suggestion-btn"
                                    onclick="setSuggestion('Tạo quiz về ngữ pháp tiếng Anh')">
                                    🇬🇧 Quiz ngữ pháp tiếng Anh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="${pageContext.request.contextPath}/assets/js/ai-quiz.js?v=3"></script>
</body>

</html>