</main>

<!-- Footer -->
<footer id="tentang-kami" class="py-5 mt-auto border-top" style="background-color: #111c44; color: #cbd5e1; border-color: rgba(255,255,255,0.08) !important;">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="icon-shape icon-shape-sm bg-gradient-primary shadow-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ni ni-laptop-86 text-white" style="font-size: 1rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-white tracking-tight">ADCom Jasa TKJ</h5>
                </div>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    Unit Bisnis Jasa Teknik Komputer dan Jaringan. Kami menghadirkan solusi teknologi informasi terpadu, profesional, dan ramah anggaran oleh siswa kejuruan berdedikasi tinggi.
                </p>
                <span class="badge bg-gradient-primary text-white rounded-pill px-3 py-1 text-xs">
                    <i class="ni ni-check-bold me-1"></i> Terpercaya &amp; Bergaransi
                </span>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white mb-3 tracking-tight">Layanan Kami</h6>
                <ul class="list-unstyled text-muted small">
                    <li class="mb-2"><a href="<?= BASEURL; ?>/service" class="text-decoration-none text-muted text-hover-primary">Perbaikan PC &amp; Laptop</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/service" class="text-decoration-none text-muted text-hover-primary">Instalasi Jaringan &amp; WiFi</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/service" class="text-decoration-none text-muted text-hover-primary">Pemasangan Kamera CCTV</a></li>
                    <li class="mb-2"><a href="<?= BASEURL; ?>/service" class="text-decoration-none text-muted text-hover-primary">Instalasi OS &amp; Software</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white mb-3 tracking-tight">Kontak &amp; Alamat</h6>
                <ul class="list-unstyled text-muted small">
                    <li class="mb-2 d-flex align-items-start gap-2"><i class="bi bi-geo-alt text-primary mt-1"></i> <span>Gedung Lab TKJ, Kampus Sekolah SMK</span></li>
                    <li class="mb-2 d-flex align-items-center gap-2"><i class="bi bi-envelope text-primary"></i> <span>tkj.adcom@sekolah.sch.id</span></li>
                    <li class="mb-2 d-flex align-items-center gap-2"><i class="bi bi-telephone text-primary"></i> <span>(021) 1234-5678 / WhatsApp</span></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white mb-3 tracking-tight">Ikuti Media Sosial</h6>
                <p class="small text-muted mb-3">Dapatkan update portofolio kegiatan pengerjaan jasa siswa.</p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.08);">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-muted small">
            <div>
                &copy; <?= date('Y'); ?> Unit Bisnis Siswa TKJ (ADCom). All rights reserved.
            </div>
            <div class="mt-2 mt-sm-0">
                <span class="text-white-50">Theme inspired by </span>
                <span class="fw-bold text-primary">Argon Dashboard 2</span>
            </div>
        </div>
    </div>
</footer>

<!-- ======================================================== -->
<!-- FLOATING AI CHATBOT WIDGET (BERANDA, LAYANAN, TENTANG KAMI, DLL.) -->
<!-- ======================================================== -->
<?php
$aiDynSettings = class_exists('AIService') ? AIService::getDynamicSettings() : [];
$botNameDisplay = !empty($aiDynSettings['bot_name']) ? htmlspecialchars($aiDynSettings['bot_name']) : 'ADCom AI';
$welcomeMsgDisplay = !empty($aiDynSettings['welcome_message']) ? nl2br(htmlspecialchars($aiDynSettings['welcome_message'])) : 'Halo! Saya adalah Asisten AI dari ADCom Jasa TKJ. Saya siap membantu Anda seputar informasi layanan jasa, simulasi biaya, alur pemesanan, dan konsultasi teknis.<br><br>Apa yang bisa saya bantu hari ini?';
$aiIsActive = (($aiDynSettings['ai_enabled'] ?? '1') == '1');
?>

<!-- Floating Trigger Button -->
<button id="adcomChatBtn" type="button" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed" style="bottom: 25px; right: 25px; width: 62px; height: 62px; z-index: 9999; border: 2px solid rgba(255,255,255,0.5); background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%); box-shadow: 0 8px 26px rgba(94, 114, 228, 0.45) !important; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);" title="Tanya <?= $botNameDisplay; ?>" onclick="toggleAdcomChat()">
    <i class="bi bi-robot fs-3 text-white" id="adcomChatBtnIcon"></i>
</button>

<!-- Floating Chat Window -->
<div id="adcomChatWindow" class="card shadow-lg border-0 rounded-4 overflow-hidden position-fixed" style="bottom: 95px; right: 25px; width: 380px; max-width: calc(100vw - 35px); height: 520px; max-height: calc(100vh - 120px); z-index: 9998; display: none; flex-direction: column; box-shadow: 0 16px 40px rgba(0,0,0,0.25) !important; border: 1px solid rgba(255,255,255,0.1) !important;">
    <!-- Chat Header -->
    <div class="card-header text-white py-3 px-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%);">
        <div class="d-flex align-items-center">
            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 38px; height: 38px;">
                <i class="bi bi-robot text-primary fs-5"></i>
            </div>
            <div>
                <h6 class="m-0 fw-bold fs-6 text-white"><?= $botNameDisplay; ?></h6>
                <div class="d-flex align-items-center gap-1">
                    <span class="badge <?= $aiIsActive ? 'bg-success' : 'bg-secondary'; ?> rounded-pill px-2 py-0" style="font-size: 10px;">
                        <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i><?= $aiIsActive ? 'Online' : 'Offline / FAQ'; ?>
                    </span>
                    <small class="text-white-50 ms-1" style="font-size: 11px;">Asisten Layanan TKJ</small>
                </div>
            </div>
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-sm btn-outline-light rounded-circle p-0 d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;" title="Mulai Baru / Reset Chat" onclick="resetAdcomChat()">
                <i class="bi bi-arrow-clockwise" style="font-size: 14px;"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-circle p-0 d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;" title="Tutup Chat" onclick="toggleAdcomChat()">
                <i class="bi bi-x-lg" style="font-size: 14px;"></i>
            </button>
        </div>
    </div>
    
    <!-- Chat Body -->
    <div class="card-body p-3 d-flex flex-column" style="background-color: #f8f9fc; overflow-y: auto; flex: 1;" id="adcomChatBox">
        <div id="adcomChatHistory">
            <!-- Welcome Message -->
            <div class="adcom-chat-bubble adcom-bubble-ai mb-3">
                <div class="fw-bold text-primary mb-1"><i class="bi bi-robot me-1"></i> <?= $botNameDisplay; ?></div>
                <?= $welcomeMsgDisplay; ?>
            </div>

            <!-- Quick Action Chips -->
            <div class="d-flex flex-wrap gap-1 mb-3" id="adcomQuickChips">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('ada jasa apa saja')">
                    📋 Ada jasa apa saja?
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('ada berapa pengguna')">
                    👥 Ada berapa pengguna?
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('bagaimana cara pesan')">
                    🛒 Alur pemesanan?
                </button>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="adcom-chat-bubble adcom-bubble-ai d-none" id="adcomTyping">
            <div class="adcom-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
    
    <!-- Chat Footer -->
    <div class="card-footer bg-white p-2 border-top">
        <form id="adcomChatForm" onsubmit="handleAdcomChatSubmit(event)">
            <div class="input-group">
                <input type="text" id="adcomMessageInput" class="form-control rounded-pill px-3 py-2 border-1 bg-light" style="font-size: 13.5px;" placeholder="Ketik keluhan atau tanya jasa..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary rounded-circle ms-2 shadow-sm d-flex justify-content-center align-items-center" style="width: 38px; height: 38px; min-width: 38px;" id="adcomSendBtn" title="Kirim Pesan">
                    <i class="bi bi-send-fill" style="font-size: 13px;"></i>
                </button>
            </div>
        </form>
        <div class="text-center text-muted mt-1" style="font-size: 10px;">
            Unit Bisnis Siswa TKJ &bull; ADCom AI
        </div>
    </div>
</div>

<style>
    #adcomChatBtn:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 12px 30px rgba(94, 114, 228, 0.6) !important;
    }
    .adcom-chat-bubble {
        max-width: 86%;
        padding: 10px 14px;
        border-radius: 16px;
        margin-bottom: 11px;
        line-height: 1.45;
        font-size: 13.5px;
        position: relative;
        word-break: break-word;
    }
    .adcom-bubble-user {
        background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
        box-shadow: 0 3px 8px rgba(94, 114, 228, 0.3);
    }
    .adcom-bubble-ai {
        background-color: #ffffff;
        color: #2b2f38;
        margin-right: auto;
        border-bottom-left-radius: 4px;
        border: 1px solid #e3e6f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .adcom-bubble-ai strong {
        color: #0b5ed7;
    }
    .adcom-dots span {
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #6c757d;
        border-radius: 50%;
        margin: 0 2px;
        animation: adcomDots 1s infinite alternate;
    }
    .adcom-dots span:nth-child(2) { animation-delay: 0.2s; }
    .adcom-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes adcomDots {
        0% { transform: translateY(0); opacity: 0.4; }
        100% { transform: translateY(-4px); opacity: 1; }
    }
</style>

<script>
const ADCOM_BASEURL = '<?= BASEURL; ?>';
let adcomChatOpen = false;

function toggleAdcomChat() {
    const chatWindow = document.getElementById('adcomChatWindow');
    const chatBtnIcon = document.getElementById('adcomChatBtnIcon');
    const messageInput = document.getElementById('adcomMessageInput');
    
    if (!chatWindow) return;
    
    if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
        chatWindow.style.display = 'flex';
        chatBtnIcon.className = 'bi bi-x-lg fs-3 text-white';
        adcomChatOpen = true;
        sessionStorage.setItem('adcom_chat_open', '1');
        scrollAdcomChat();
        setTimeout(() => messageInput.focus(), 150);
    } else {
        chatWindow.style.display = 'none';
        chatBtnIcon.className = 'bi bi-robot fs-3 text-white';
        adcomChatOpen = false;
        sessionStorage.setItem('adcom_chat_open', '0');
    }
}

function openAdcomChat() {
    const chatWindow = document.getElementById('adcomChatWindow');
    if (chatWindow && (chatWindow.style.display === 'none' || chatWindow.style.display === '')) {
        toggleAdcomChat();
    }
}

function scrollAdcomChat() {
    const chatBox = document.getElementById('adcomChatBox');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

function formatAdcomMarkdown(text) {
    if (!text) return '';
    let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    formatted = formatted.replace(/\n/g, '<br>');
    return formatted;
}

function sendAdcomQuick(query) {
    const input = document.getElementById('adcomMessageInput');
    if (input) {
        input.value = query;
        handleAdcomChatSubmit(new Event('submit'));
    }
}

function handleAdcomChatSubmit(e) {
    if (e) e.preventDefault();
    
    const messageInput = document.getElementById('adcomMessageInput');
    const sendBtn = document.getElementById('adcomSendBtn');
    const chatHistory = document.getElementById('adcomChatHistory');
    const typing = document.getElementById('adcomTyping');
    
    const message = messageInput.value.trim();
    if (!message) return;
    
    // 1. Append user message
    const userDiv = document.createElement('div');
    userDiv.className = 'adcom-chat-bubble adcom-bubble-user';
    userDiv.textContent = message;
    chatHistory.appendChild(userDiv);
    
    // Save to local session array
    saveAdcomMessage('user', message);
    
    messageInput.value = '';
    messageInput.disabled = true;
    sendBtn.disabled = true;
    
    typing.classList.remove('d-none');
    scrollAdcomChat();
    
    // 2. Send to backend API
    const activeConvId = sessionStorage.getItem('adcom_active_conv_id') || null;
    fetch(ADCOM_BASEURL + '/ai/sendMessage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            message: message,
            conversation_id: activeConvId
        })
    })
    .then(res => res.json())
    .then(data => {
        typing.classList.add('d-none');
        messageInput.disabled = false;
        sendBtn.disabled = false;
        messageInput.focus();
        
        if (data.status === 'success') {
            if (data.conversation_id) {
                sessionStorage.setItem('adcom_active_conv_id', data.conversation_id);
            }
            const aiDiv = document.createElement('div');
            aiDiv.className = 'adcom-chat-bubble adcom-bubble-ai';
            aiDiv.innerHTML = formatAdcomMarkdown(data.message);
            chatHistory.appendChild(aiDiv);
            saveAdcomMessage('ai', data.message);
            scrollAdcomChat();
        } else {
            const errDiv = document.createElement('div');
            errDiv.className = 'adcom-chat-bubble adcom-bubble-ai text-danger';
            errDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> ' + (data.message || 'Terjadi kesalahan.');
            chatHistory.appendChild(errDiv);
            scrollAdcomChat();
        }
    })
    .catch(err => {
        console.error('AI Widget Error:', err);
        typing.classList.add('d-none');
        messageInput.disabled = false;
        sendBtn.disabled = false;
        
        const errDiv = document.createElement('div');
        errDiv.className = 'adcom-chat-bubble adcom-bubble-ai text-danger';
        errDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Gagal terhubung ke server. Silakan coba lagi.';
        chatHistory.appendChild(errDiv);
        scrollAdcomChat();
    });
}

function saveAdcomMessage(role, text) {
    let saved = [];
    try {
        saved = JSON.parse(sessionStorage.getItem('adcom_widget_msgs') || '[]');
    } catch(e) { saved = []; }
    saved.push({ role, text });
    sessionStorage.setItem('adcom_widget_msgs', JSON.stringify(saved));
}

function loadAdcomSavedMessages() {
    const chatHistory = document.getElementById('adcomChatHistory');
    if (!chatHistory) return;
    
    let saved = [];
    try {
        saved = JSON.parse(sessionStorage.getItem('adcom_widget_msgs') || '[]');
    } catch(e) { saved = []; }
    
    if (saved.length > 0) {
        // Clear quick chips to avoid clutter
        const chips = document.getElementById('adcomQuickChips');
        if (chips) chips.remove();
        
        saved.forEach(msg => {
            const div = document.createElement('div');
            if (msg.role === 'user') {
                div.className = 'adcom-chat-bubble adcom-bubble-user';
                div.textContent = msg.text;
            } else {
                div.className = 'adcom-chat-bubble adcom-bubble-ai';
                div.innerHTML = formatAdcomMarkdown(msg.text);
            }
            chatHistory.appendChild(div);
        });
        scrollAdcomChat();
    }
}

function resetAdcomChat() {
    if (confirm('Mulai percakapan baru dengan Asisten AI?')) {
        sessionStorage.removeItem('adcom_widget_msgs');
        sessionStorage.removeItem('adcom_active_conv_id');
        fetch(ADCOM_BASEURL + '/ai/clearWidgetMessages').catch(() => {});
        
        const chatHistory = document.getElementById('adcomChatHistory');
        chatHistory.innerHTML = `
            <div class="adcom-chat-bubble adcom-bubble-ai mb-3">
                <div class="fw-bold text-primary mb-1"><i class="bi bi-robot me-1"></i> Asisten AI ADCom</div>
                Halo! Saya adalah Asisten AI dari ADCom Jasa TKJ. Saya siap membantu Anda seputar informasi layanan jasa (komputer, laptop, jaringan LAN, Mikrotik, CCTV), simulasi biaya, alur pemesanan, dan konsultasi teknis.<br><br>
                Apa yang bisa saya bantu hari ini?
            </div>
            <div class="d-flex flex-wrap gap-1 mb-3" id="adcomQuickChips">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('ada jasa apa saja')">
                    📋 Ada jasa apa saja?
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('ada berapa pengguna')">
                    👥 Ada berapa pengguna?
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2 text-start shadow-sm bg-white" style="font-size: 12px;" onclick="sendAdcomQuick('bagaimana cara pesan')">
                    🛒 Alur pemesanan?
                </button>
            </div>
        `;
        scrollAdcomChat();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadAdcomSavedMessages();
    if (sessionStorage.getItem('adcom_chat_open') === '1') {
        openAdcomChat();
    }
});
</script>

<!-- ======================================================== -->
<!-- MODAL PENGATURAN TEMA & TAMPILAN (DARK / LIGHT THEME) -->
<!-- ======================================================== -->
<div class="modal fade" id="settingsThemeModal" tabindex="-1" aria-labelledby="settingsThemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-palette2 fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="settingsThemeModalLabel">Pengaturan Tema & Tampilan</h5>
                        <p class="mb-0 small text-white-50">Sesuaikan mode tampilan agar nyaman untuk mata Anda</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="alert alert-light border d-flex align-items-center gap-2 py-2 px-3 rounded-3 mb-4">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span class="small">Tema aktif: <strong id="currentThemeStatus" class="text-primary">Tema Terang</strong></span>
                </div>

                <div class="row g-3 mb-2">
                    <!-- Option 1: Light Mode -->
                    <div class="col-12">
                        <div class="card border rounded-3 p-3 theme-option-card cursor-pointer" data-theme-val="light" onclick="applyTheme('light')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-sun-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <span>Tema Terang</span>
                                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill small py-0 px-2" style="font-size: 11px;">Light</span>
                                        </div>
                                        <div class="text-muted small">Tampilan cerah dan bersih, ideal untuk siang hari atau ruangan terang.</div>
                                    </div>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="themeRadio" id="themeRadioLight" value="light" onchange="applyTheme('light')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option 2: Dark Mode -->
                    <div class="col-12">
                        <div class="card border rounded-3 p-3 theme-option-card cursor-pointer" data-theme-val="dark" onclick="applyTheme('dark')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-moon-stars-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <span>Tema Gelap</span>
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill small py-0 px-2" style="font-size: 11px;">Dark</span>
                                        </div>
                                        <div class="text-muted small">Latar gelap yang lembut, mengurangi silau dan kelelahan mata di malam hari.</div>
                                    </div>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="themeRadio" id="themeRadioDark" value="dark" onchange="applyTheme('dark')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option 3: Auto Mode -->
                    <div class="col-12">
                        <div class="card border rounded-3 p-3 theme-option-card cursor-pointer" data-theme-val="auto" onclick="applyTheme('auto')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-display fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <span>Otomatis (Ikuti Sistem)</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small py-0 px-2" style="font-size: 11px;">Auto</span>
                                        </div>
                                        <div class="text-muted small">Menyesuaikan secara dinamis dengan tema perangkat laptop / HP Anda.</div>
                                    </div>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="themeRadio" id="themeRadioAuto" value="auto" onchange="applyTheme('auto')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-primary rounded-pill px-4 w-100 fw-bold shadow-sm" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-1"></i> Simpan & Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
// PENGATURAN TEMA GELAP / TERANG (THEME SWITCHER)
// ==========================================
function applyTheme(themeChoice) {
    localStorage.setItem('adcom_theme', themeChoice);
    
    let effectiveTheme = themeChoice;
    if (themeChoice === 'auto') {
        effectiveTheme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    
    document.documentElement.setAttribute('data-bs-theme', effectiveTheme);
    
    // Update navbar theme quick toggle button icon
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        if (effectiveTheme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill fs-5 text-warning';
            themeIcon.parentElement.setAttribute('title', 'Beralih ke Tema Terang (Saat ini: Gelap)');
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill fs-5 text-light';
            themeIcon.parentElement.setAttribute('title', 'Beralih ke Tema Gelap (Saat ini: Terang)');
        }
    }
    
    // Update radio and active highlight in Theme Settings modal
    document.querySelectorAll('.theme-option-card').forEach(card => {
        const val = card.getAttribute('data-theme-val');
        const radio = card.querySelector('input[type="radio"]');
        if (val === themeChoice) {
            card.classList.add('selected-theme');
            if (radio) radio.checked = true;
        } else {
            card.classList.remove('selected-theme');
            if (radio) radio.checked = false;
        }
    });

    const statusEl = document.getElementById('currentThemeStatus');
    if (statusEl) {
        const labels = {
            'light': 'Tema Terang (Light Mode)',
            'dark': 'Tema Gelap (Dark Mode)',
            'auto': 'Otomatis (Ikuti Sistem - ' + (effectiveTheme === 'dark' ? 'Aktif Gelap' : 'Aktif Terang') + ')'
        };
        statusEl.textContent = labels[themeChoice] || effectiveTheme;
    }
}

function toggleQuickTheme() {
    const current = localStorage.getItem('adcom_theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);
}

// System theme listener
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (localStorage.getItem('adcom_theme') === 'auto') {
            applyTheme('auto');
        }
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('adcom_theme') || 'light';
    applyTheme(saved);
});
</script>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inisialisasi Tooltip Bootstrap di seluruh aplikasi
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php if (isset($_SESSION['user_id'])) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASEURL = '<?= BASEURL; ?>';
    
    function fetchNotifications() {
        fetch(BASEURL + '/notification/getJson')
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    updateNotificationUI(data.count, data.data);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    function updateNotificationUI(count, notifications) {
        const badge = document.getElementById('notifBadge');
        const list = document.getElementById('notificationList');
        
        if(count > 0) {
            badge.style.display = 'inline-block';
            badge.innerText = count;
        } else {
            badge.style.display = 'none';
        }

        list.innerHTML = '';
        if(count === 0) {
            list.innerHTML = '<li><span class="dropdown-item text-center text-muted small py-3">Tidak ada notifikasi baru</span></li>';
        } else {
            notifications.forEach(notif => {
                const date = new Date(notif.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute:'2-digit'});
                const li = document.createElement('li');
                li.innerHTML = `
                    <a class="dropdown-item py-2 border-bottom notif-item" href="#" data-id="${notif.id}">
                        <div class="d-flex justify-content-between">
                            <strong class="text-dark small">${notif.title}</strong>
                            <small class="text-muted" style="font-size: 0.7rem;">${date}</small>
                        </div>
                        <div class="text-muted small text-wrap mt-1" style="font-size: 0.8rem; line-height: 1.2;">${notif.message}</div>
                    </a>
                `;
                list.appendChild(li);
            });
            
            // Add click listeners to mark as read
            document.querySelectorAll('.notif-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    fetch(BASEURL + '/notification/markRead/' + id)
                        .then(() => {
                            fetchNotifications();
                        });
                });
            });
        }
    }

    document.getElementById('markAllReadBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fetch(BASEURL + '/notification/markAllRead')
            .then(() => {
                fetchNotifications();
            });
    });

    // Initial fetch
    fetchNotifications();
    
    // Poll every 30 seconds
    setInterval(fetchNotifications, 30000);
});
</script>
<?php endif; ?>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') : ?>
<!-- Admin Sidebar Backdrop for Mobile -->
<div id="adminSidebarBackdrop" class="admin-sidebar-backdrop" onclick="closeAdminSidebar()"></div>

<script>
function toggleAdminSidebar() {
    const isDesktop = window.innerWidth >= 992;
    if (isDesktop) {
        document.body.classList.toggle('admin-sidebar-collapsed');
        const isCollapsed = document.body.classList.contains('admin-sidebar-collapsed');
        localStorage.setItem('adcom_admin_sidebar', isCollapsed ? 'collapsed' : 'open');
        updateSidebarToggleUI(!isCollapsed);
    } else {
        document.body.classList.toggle('admin-sidebar-mobile-open');
    }
}

function closeAdminSidebar() {
    const isDesktop = window.innerWidth >= 992;
    if (isDesktop) {
        document.body.classList.add('admin-sidebar-collapsed');
        localStorage.setItem('adcom_admin_sidebar', 'collapsed');
        updateSidebarToggleUI(false);
    } else {
        document.body.classList.remove('admin-sidebar-mobile-open');
    }
}

function openAdminSidebar() {
    const isDesktop = window.innerWidth >= 992;
    if (isDesktop) {
        document.body.classList.remove('admin-sidebar-collapsed');
        localStorage.setItem('adcom_admin_sidebar', 'open');
        updateSidebarToggleUI(true);
    } else {
        document.body.classList.add('admin-sidebar-mobile-open');
    }
}

function updateSidebarToggleUI(isOpen) {
    const toggleBtn = document.getElementById('adminSidebarToggleBtn');
    const toggleText = document.getElementById('adminSidebarToggleText');
    if (toggleBtn) {
        if (isOpen) {
            toggleBtn.classList.remove('btn-outline-primary');
            toggleBtn.classList.add('btn-primary');
            toggleBtn.title = 'Tutup Sidebar Dashboard';
            if (toggleText) toggleText.textContent = 'Sidebar';
        } else {
            toggleBtn.classList.remove('btn-primary');
            toggleBtn.classList.add('btn-outline-primary');
            toggleBtn.title = 'Buka Sidebar Dashboard';
            if (toggleText) toggleText.textContent = 'Sidebar';
        }
    }
}

// Sync UI on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    const isDesktop = window.innerWidth >= 992;
    if (isDesktop) {
        const isCollapsed = document.body.classList.contains('admin-sidebar-collapsed');
        updateSidebarToggleUI(!isCollapsed);
    }
    
    // Close sidebar on mobile when navigating via sidebar link
    document.querySelectorAll('.admin-sidebar .nav-link-item').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                document.body.classList.remove('admin-sidebar-mobile-open');
            }
        });
    });
});

// Auto adjust if window resizes
window.addEventListener('resize', function() {
    if (window.innerWidth >= 992) {
        document.body.classList.remove('admin-sidebar-mobile-open');
        const isCollapsed = document.body.classList.contains('admin-sidebar-collapsed');
        updateSidebarToggleUI(!isCollapsed);
    }
});
</script>
<?php endif; ?>

</body>
</html>
