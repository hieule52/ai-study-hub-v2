<?php
$pageTitle = 'Đăng ký - AI Study Hub';
$actor = 'auth';
require __DIR__ . '/layouts/header.php';
?>

<style>
/* ===== REGISTER MULTI-STEP STYLES ===== */
.reg-wrapper {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background:
        radial-gradient(circle at 20% 20%, rgba(79,70,229,0.18) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(236,72,153,0.12) 0%, transparent 50%),
        var(--bg-main);
}

.reg-card {
    width: 100%;
    max-width: 520px;
    padding: 2.5rem;
    background: var(--bg-surface-glass);
    backdrop-filter: blur(16px);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    box-shadow: 0 25px 50px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
}

.reg-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}

/* Progress Bar */
.reg-progress {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 2rem;
}

.reg-step-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
    border: 2px solid var(--border-color);
    background: var(--bg-surface);
    color: var(--text-muted);
    transition: var(--transition);
    flex-shrink: 0;
    z-index: 1;
}

.reg-step-dot.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    box-shadow: 0 0 16px rgba(79,70,229,0.5);
}

.reg-step-dot.done {
    background: var(--success);
    border-color: var(--success);
    color: white;
}

.reg-step-line {
    flex: 1;
    height: 2px;
    background: var(--border-color);
    transition: var(--transition);
}

.reg-step-line.done {
    background: var(--success);
}

/* Steps */
.reg-step { display: none; }
.reg-step.active { display: block; animation: fadeSlideIn 0.4s ease; }

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Role Cards */
.role-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin: 1.5rem 0;
}

.role-card {
    padding: 1.5rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    background: rgba(255,255,255,0.03);
    cursor: pointer;
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.role-card:hover {
    border-color: var(--primary);
    background: rgba(79,70,229,0.08);
    transform: translateY(-3px);
}

.role-card.selected {
    border-color: var(--primary);
    background: rgba(79,70,229,0.12);
    box-shadow: 0 0 20px rgba(79,70,229,0.25);
}

.role-card .role-icon {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    display: block;
}

.role-card .role-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    display: block;
}

.role-card .role-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.35rem;
    display: block;
    line-height: 1.4;
}

.role-check {
    position: absolute;
    top: 8px; right: 8px;
    width: 20px; height: 20px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    font-size: 0.7rem;
    display: none;
    align-items: center;
    justify-content: center;
}

.role-card.selected .role-check { display: flex; }

/* Success Screen */
.success-screen {
    text-align: center;
    padding: 1rem 0;
}

.success-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--success), #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
    animation: popIn 0.6s cubic-bezier(0.175,0.885,0.32,1.275);
    box-shadow: 0 0 30px rgba(16,185,129,0.4);
}

@keyframes popIn {
    0%   { transform: scale(0); opacity: 0; }
    70%  { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

.success-confetti {
    font-size: 1.5rem;
    animation: float 2s ease-in-out infinite alternate;
    display: inline-block;
    margin: 0 0.25rem;
}

@keyframes float {
    from { transform: translateY(0); }
    to   { transform: translateY(-8px); }
}

.teacher-notice {
    background: rgba(245,158,11,0.1);
    border: 1px solid rgba(245,158,11,0.3);
    border-radius: var(--radius-md);
    padding: 0.875rem 1rem;
    font-size: 0.85rem;
    color: var(--warning);
    margin-top: 1rem;
    text-align: left;
}

.form-hint {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.3rem;
}

select.form-control option {
    background: var(--bg-surface);
    color: var(--text-primary);
}
</style>

<div class="reg-wrapper">
    <div class="reg-card">

        <!-- Progress -->
        <div class="reg-progress" id="progressBar">
            <div class="reg-step-dot active" id="dot1">1</div>
            <div class="reg-step-line" id="line1"></div>
            <div class="reg-step-dot" id="dot2">2</div>
            <div class="reg-step-line" id="line2"></div>
            <div class="reg-step-dot" id="dot3">3</div>
            <div class="reg-step-line" id="line3"></div>
            <div class="reg-step-dot" id="dot4">✓</div>
        </div>

        <!-- ===== STEP 1: Choose Role ===== -->
        <div class="reg-step active" id="step1">
            <div class="text-center" style="margin-bottom:1.5rem;">
                <h2 style="font-size:1.6rem;margin-bottom:0.4rem;">Bạn muốn đăng ký với tư cách?</h2>
                <p class="text-secondary" style="font-size:0.9rem;">Chọn vai trò phù hợp với bạn</p>
            </div>

            <div class="role-cards">
                <div class="role-card" id="cardStudent" onclick="selectRole('student')">
                    <div class="role-check">✓</div>
                    <span class="role-icon">🎓</span>
                    <span class="role-name">Học Viên</span>
                    <span class="role-desc">Tôi muốn học các khoá học và sử dụng AI Tutor</span>
                </div>
                <div class="role-card" id="cardTeacher" onclick="selectRole('teacher')">
                    <div class="role-check">✓</div>
                    <span class="role-icon">👨‍🏫</span>
                    <span class="role-name">Giảng Viên</span>
                    <span class="role-desc">Tôi muốn tạo và giảng dạy các khoá học</span>
                </div>
            </div>

            <button class="btn btn-primary" id="btnStep1" onclick="goStep(2)" style="width:100%;padding:1rem;font-size:1rem;" disabled>
                Tiếp tục →
            </button>

            <div class="text-center" style="margin-top:1.5rem;">
                <p class="text-secondary" style="font-size:0.875rem;">
                    Đã có tài khoản? <a href="/login.php" class="text-gradient" style="font-weight:600;text-decoration:none;">Đăng nhập ngay</a>
                </p>
            </div>
        </div>

        <!-- ===== STEP 2: Basic Info ===== -->
        <div class="reg-step" id="step2">
            <div style="margin-bottom:1.5rem;">
                <h2 style="font-size:1.5rem;margin-bottom:0.4rem;">Thông tin tài khoản</h2>
                <p class="text-secondary" style="font-size:0.9rem;">Điền thông tin để tạo tài khoản của bạn</p>
            </div>

            <div class="form-group">
                <label class="form-label">Tên hiển thị *</label>
                <input type="text" id="regUsername" class="form-control" placeholder="Nguyễn Văn A" required minlength="3">
                <p class="form-hint">Tên sẽ hiển thị cho người khác thấy</p>
            </div>

            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" id="regEmail" class="form-control" placeholder="email@gmail.com" required>
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Mật khẩu *</label>
                <input type="password" id="regPassword" class="form-control" placeholder="Ít nhất 6 ký tự" required minlength="6">
            </div>

            <div style="display:flex;gap:1rem;">
                <button class="btn btn-outline" onclick="goStep(1)" style="flex:1;padding:0.875rem;">← Quay lại</button>
                <button class="btn btn-primary" onclick="validateStep2()" style="flex:2;padding:0.875rem;">Tiếp tục →</button>
            </div>
        </div>

        <!-- ===== STEP 3: Role-specific Details ===== -->
        <div class="reg-step" id="step3">

            <!-- Student Form -->
            <div id="studentForm">
                <div style="margin-bottom:1.5rem;">
                    <h2 style="font-size:1.5rem;margin-bottom:0.4rem;">🎓 Về việc học của bạn</h2>
                    <p class="text-secondary" style="font-size:0.9rem;">Giúp chúng tôi cá nhân hoá trải nghiệm học tập</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Trình độ hiện tại</label>
                    <select id="stuLevel" class="form-control">
                        <option value="">-- Chọn trình độ --</option>
                        <option value="beginner">Mới bắt đầu</option>
                        <option value="intermediate">Trung cấp</option>
                        <option value="advanced">Nâng cao</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngành / Lĩnh vực quan tâm</label>
                    <select id="stuField" class="form-control">
                        <option value="">-- Chọn lĩnh vực --</option>
                        <option value="it">Công nghệ thông tin</option>
                        <option value="ai">Trí tuệ nhân tạo / AI</option>
                        <option value="business">Kinh doanh / Marketing</option>
                        <option value="design">Thiết kế</option>
                        <option value="language">Ngoại ngữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label class="form-label">Mục tiêu học tập</label>
                    <textarea id="stuGoal" class="form-control" rows="3" placeholder="Tôi muốn học để..." style="resize:none;"></textarea>
                </div>
            </div>

            <!-- Teacher Form -->
            <div id="teacherForm" style="display:none;">
                <div style="margin-bottom:1.5rem;">
                    <h2 style="font-size:1.5rem;margin-bottom:0.4rem;">👨‍🏫 Về kinh nghiệm giảng dạy</h2>
                    <p class="text-secondary" style="font-size:0.9rem;">Chia sẻ để học viên hiểu hơn về bạn</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Chuyên môn / Lĩnh vực giảng dạy</label>
                    <input type="text" id="teaSpecialty" class="form-control" placeholder="VD: Lập trình Python, Machine Learning...">
                </div>

                <div class="form-group">
                    <label class="form-label">Số năm kinh nghiệm</label>
                    <select id="teaExp" class="form-control">
                        <option value="">-- Chọn kinh nghiệm --</option>
                        <option value="1">Dưới 1 năm</option>
                        <option value="2">1 - 3 năm</option>
                        <option value="5">3 - 5 năm</option>
                        <option value="10">Trên 5 năm</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label class="form-label">Giới thiệu bản thân</label>
                    <textarea id="teaBio" class="form-control" rows="3" placeholder="Tôi là giảng viên với..." style="resize:none;"></textarea>
                </div>
            </div>

            <div style="display:flex;gap:1rem;">
                <button class="btn btn-outline" onclick="goStep(2)" style="flex:1;padding:0.875rem;">← Quay lại</button>
                <button class="btn btn-primary" id="btnSubmit" onclick="submitRegister()" style="flex:2;padding:0.875rem;">
                    🚀 Tạo Tài Khoản
                </button>
            </div>
        </div>

        <!-- ===== STEP 4: Success ===== -->
        <div class="reg-step" id="step4">
            <div class="success-screen">
                <div class="success-icon">🎉</div>
                <div style="margin-bottom:0.75rem;">
                    <span class="success-confetti" style="animation-delay:0s;">✨</span>
                    <span class="success-confetti" style="animation-delay:0.3s;">🌟</span>
                    <span class="success-confetti" style="animation-delay:0.6s;">🎊</span>
                </div>
                <h2 style="font-size:1.8rem;margin-bottom:0.5rem;">Chúc mừng!</h2>
                <p class="text-secondary" id="successMsg" style="font-size:0.95rem;margin-bottom:1.5rem;">
                    Tài khoản của bạn đã được tạo thành công.
                </p>

                <div id="teacherNotice" class="teacher-notice" style="display:none;">
                    ⚠️ <strong>Lưu ý cho Giảng Viên:</strong> Tài khoản của bạn đang ở chế độ <em>Giảng Viên</em>. 
                    Bạn có thể bắt đầu tạo khoá học ngay sau khi đăng nhập.
                </div>

                <div style="margin-top:1.5rem;display:flex;flex-direction:column;gap:0.75rem;">
                    <button class="btn btn-primary" id="btnGoHome" onclick="redirectUser()" style="width:100%;padding:1rem;font-size:1rem;">
                        Vào trang chủ →
                    </button>
                    <p style="font-size:0.8rem;color:var(--text-muted);">Đang chuyển hướng sau <span id="countdown">5</span> giây...</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php ob_start(); ?>
<script>
let selectedRole = null;
let regToken = null;
let regUser = null;

function selectRole(role) {
    selectedRole = role;
    document.getElementById('cardStudent').classList.toggle('selected', role === 'student');
    document.getElementById('cardTeacher').classList.toggle('selected', role === 'teacher');
    document.getElementById('btnStep1').disabled = false;
}

function goStep(n) {
    document.querySelectorAll('.reg-step').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + n).classList.add('active');

    // Update dots & lines
    for (let i = 1; i <= 4; i++) {
        const dot = document.getElementById('dot' + i);
        if (i < n) {
            dot.classList.add('done');
            dot.classList.remove('active');
            if (i <= 3) document.getElementById('line' + i).classList.add('done');
        } else if (i === n) {
            dot.classList.add('active');
            dot.classList.remove('done');
        } else {
            dot.classList.remove('active', 'done');
        }
    }

    // Show correct sub-form on step 3
    if (n === 3) {
        document.getElementById('studentForm').style.display = selectedRole === 'student' ? 'block' : 'none';
        document.getElementById('teacherForm').style.display = selectedRole === 'teacher' ? 'block' : 'none';
    }
}

function validateStep2() {
    const name = document.getElementById('regUsername').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const pass = document.getElementById('regPassword').value;

    if (!name || name.length < 3) {
        App.showToast('Tên hiển thị phải có ít nhất 3 ký tự.', 'error');
        return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        App.showToast('Email không hợp lệ.', 'error');
        return;
    }
    if (!pass || pass.length < 6) {
        App.showToast('Mật khẩu phải có ít nhất 6 ký tự.', 'error');
        return;
    }
    goStep(3);
}

async function submitRegister() {
    const btn = document.getElementById('btnSubmit');
    btn.innerHTML = '⏳ Đang tạo tài khoản...';
    btn.disabled = true;

    const username = document.getElementById('regUsername').value.trim();
    const email    = document.getElementById('regEmail').value.trim();
    const password = document.getElementById('regPassword').value;

    // Gather extra profile data (stored for future use / bio)
    let extraData = {};
    if (selectedRole === 'student') {
        extraData = {
            level: document.getElementById('stuLevel').value,
            field: document.getElementById('stuField').value,
            goal:  document.getElementById('stuGoal').value.trim(),
        };
    } else {
        extraData = {
            specialty: document.getElementById('teaSpecialty').value.trim(),
            experience: document.getElementById('teaExp').value,
            bio: document.getElementById('teaBio').value.trim(),
        };
    }

    try {
        // 1. Register
        await window.api.post('/auth/register', {
            username, email, password, role: selectedRole
        });

        // 2. Auto-login
        const loginRes = await window.api.post('/auth/login', { email, password });
        regToken = loginRes.data.token;
        regUser  = loginRes.data.user;
        window.api.setToken(regToken, regUser);

        // 3. Show success
        showSuccess();

    } catch (err) {
        App.showToast(err.message, 'error');
        btn.innerHTML = '🚀 Tạo Tài Khoản';
        btn.disabled = false;
    }
}

function showSuccess() {
    goStep(4);

    const name = regUser ? regUser.username : 'bạn';
    const role = selectedRole;

    document.getElementById('successMsg').textContent =
        role === 'teacher'
        ? `Chào mừng ${name} đến với AI Study Hub với tư cách Giảng Viên! 🏫`
        : `Chào mừng ${name} đến với AI Study Hub! Hành trình học tập của bạn bắt đầu từ đây. 📚`;

    document.getElementById('teacherNotice').style.display = role === 'teacher' ? 'block' : 'none';

    // Countdown redirect
    let secs = 5;
    const cd = document.getElementById('countdown');
    const timer = setInterval(() => {
        secs--;
        cd.textContent = secs;
        if (secs <= 0) {
            clearInterval(timer);
            redirectUser();
        }
    }, 1000);
}

function redirectUser() {
    if (!regUser) { window.location.href = '/login.php'; return; }
    if (regUser.role === 'teacher') window.location.href = '/teacher/dashboard.php';
    else window.location.href = '/';
}
</script>
<?php
$extraScripts = ob_get_clean();
require __DIR__ . '/layouts/footer.php';
?>