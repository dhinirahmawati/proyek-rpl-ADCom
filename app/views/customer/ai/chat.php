<style>
    .chat-container {
        height: 400px;
        overflow-y: auto;
        padding: 15px;
        background-color: #f8f9fc;
    }
    .chat-bubble {
        max-width: 85%;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 12px;
        line-height: 1.4;
        position: relative;
        font-size: 14px;
    }
    .chat-user {
        background-color: #4e73df;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }
    .chat-ai {
        background-color: #ffffff;
        color: #5a5c69;
        margin-right: auto;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #e3e6f0;
    }
    .typing-indicator span {
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #858796;
        border-radius: 50%;
        margin: 0 2px;
        animation: typing 1s infinite alternate;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing {
        0% { transform: translateY(0); opacity: 0.5; }
        100% { transform: translateY(-4px); opacity: 1; }
    }
    
    /* Formatting AI bold text */
    .chat-ai strong {
        color: #2e59d9;
    }
    
    /* Floating Chat Widget */
    .chat-widget-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 360px;
        z-index: 1050;
    /* Hide global footer floating button on this specific page */
    #adcomChatBtn, #adcomChatWindow {
        display: none !important;
    }
</style>

<div class="chat-widget-container">
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <h6 class="mb-0 text-gray-800 fw-bold"><i class="fas fa-robot text-primary me-2"></i> Asisten Layanan</h6>
        <a href="<?= BASEURL; ?>/ai" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;" title="Tutup">
            <i class="fas fa-times" style="font-size: 12px;"></i>
        </a>
    </div>
    
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="border: 1px solid #e3e6f0 !important;">
        <!-- Chat Header -->
        <div class="card-header bg-primary text-white py-2 d-flex align-items-center">
            <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                <i class="fas fa-robot text-primary fs-6"></i>
            </div>
            <div>
                <h6 class="m-0 fw-bold fs-6">ADCom AI</h6>
                <small class="text-white-50" style="font-size: 11px;">Online | Siap membantu</small>
            </div>
        </div>
        
        <!-- Chat Body -->
        <div class="card-body p-0">
            <div class="chat-container" id="chatBox">
                
                <!-- Welcome message if empty -->
                <?php if (empty($data['messages'])) : ?>
                    <div class="chat-bubble chat-ai">
                        Halo! Saya adalah Asisten AI dari ADCom. Saya dapat membantu Anda berkonsultasi mengenai masalah komputer, jaringan, dan instalasi CCTV.<br><br>Apa keluhan teknis yang sedang Anda alami saat ini?
                    </div>
                <?php else : ?>
                    <!-- Load historical messages -->
                    <?php foreach ($data['messages'] as $msg) : ?>
                        <div class="chat-bubble <?= ($msg['sender'] == 'user') ? 'chat-user' : 'chat-ai'; ?>">
                            <?php 
                            $text = htmlspecialchars($msg['message']);
                            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
                            $text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);
                            echo nl2br($text); 
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Typing Indicator (Hidden by default) -->
                <div class="chat-bubble chat-ai d-none" id="typingIndicator">
                    <div class="typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Footer -->
        <div class="card-footer bg-white p-2 border-top">
            <form id="chatForm">
                <input type="hidden" id="conversation_id" value="<?= $data['conversation']['id']; ?>">
                <div class="input-group">
                    <input type="text" id="messageInput" class="form-control rounded-pill px-3 py-1 border-1 bg-light" style="font-size: 14px;" placeholder="Ketik keluhan..." required autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-circle ms-2 shadow-sm d-flex justify-content-center align-items-center" style="width: 38px; height: 38px;" id="sendBtn">
                        <i class="fas fa-paper-plane" style="font-size: 14px;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const typingIndicator = document.getElementById('typingIndicator');
    const sendBtn = document.getElementById('sendBtn');
    const conversationId = document.getElementById('conversation_id').value;
    
    // Auto scroll to bottom
    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    
    // Scroll initially
    scrollToBottom();
    
    // Format response (simple markdown to HTML for bold)
    function formatResponse(text) {
        let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }
    
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (message === '') return;
        
        // 1. Append User Message to UI
        const userBubble = document.createElement('div');
        userBubble.className = 'chat-bubble chat-user';
        userBubble.textContent = message;
        chatBox.insertBefore(userBubble, typingIndicator);
        
        // Clear input and show typing
        messageInput.value = '';
        messageInput.disabled = true;
        sendBtn.disabled = true;
        
        typingIndicator.classList.remove('d-none');
        scrollToBottom();
        
        // 2. Send via Fetch API
        fetch('<?= BASEURL; ?>/ai/sendMessage', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide typing
            typingIndicator.classList.add('d-none');
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();
            
            if (data.status === 'success') {
                // 3. Append AI Response
                const aiBubble = document.createElement('div');
                aiBubble.className = 'chat-bubble chat-ai';
                aiBubble.innerHTML = formatResponse(data.message);
                chatBox.insertBefore(aiBubble, typingIndicator);
                scrollToBottom();
            } else {
                // Error limit or others
                const errBubble = document.createElement('div');
                errBubble.className = 'chat-bubble chat-ai border-danger text-danger';
                errBubble.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> ' + data.message;
                chatBox.insertBefore(errBubble, typingIndicator);
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            typingIndicator.classList.add('d-none');
            messageInput.disabled = false;
            sendBtn.disabled = false;
            
            const errBubble = document.createElement('div');
            errBubble.className = 'chat-bubble chat-ai border-danger text-danger';
            errBubble.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> Maaf, terjadi kesalahan koneksi. Silakan coba lagi nanti.';
            chatBox.insertBefore(errBubble, typingIndicator);
            scrollToBottom();
        });
    });
});
</script>
