<?php

require_once __DIR__ . '/../services/AIService.php';

class Ai extends Controller {
    public function __construct() {
        // Public/guest access allowed for widget sendMessage & getWidgetMessages
    }

    public function index() {
        AuthGuard::checkRole('customer');
        $data['judul'] = 'Konsultasi AI';
        $user_id = $_SESSION['user_id'];
        
        $data['conversations'] = $this->model('AIModel')->getConversations($user_id);

        $this->view('templates/header', $data);
        $this->view('customer/ai/index', $data);
        $this->view('templates/footer');
    }

    public function chat($id = null) {
        AuthGuard::checkRole('customer');
        $user_id = $_SESSION['user_id'];
        
        // Start new conversation if id is null
        if ($id === null) {
            $new_id = $this->model('AIModel')->createConversation($user_id, 'Konsultasi Baru');
            if ($new_id) {
                header('Location: ' . BASEURL . '/ai/chat/' . $new_id);
                exit;
            } else {
                Flasher::setFlash('gagal', 'memulai percakapan', 'danger');
                header('Location: ' . BASEURL . '/ai');
                exit;
            }
        }
        
        // Retrieve conversation
        $data['conversation'] = $this->model('AIModel')->getConversationById($id, $user_id);
        
        // Security check
        if (!$data['conversation']) {
            Flasher::setFlash('gagal', 'ditemukan atau bukan milik Anda', 'danger');
            header('Location: ' . BASEURL . '/ai');
            exit;
        }
        
        $data['judul'] = $data['conversation']['title'];
        $data['messages'] = $this->model('AIModel')->getMessages($id);

        $this->view('templates/header', $data);
        $this->view('customer/ai/chat', $data);
        $this->view('templates/footer');
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!$data || empty(trim($data['message'] ?? ''))) {
                echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong']);
                return;
            }
            
            $userMessage = trim($data['message']);
            $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
            $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Pelanggan';
            $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
            
            $aiModel = $this->model('AIModel');
            $isComplaint = AIModel::detectComplaint($userMessage);
            
            // Determine conversation ID from request or session
            $conversation_id = null;
            if (!empty($data['conversation_id']) && is_numeric($data['conversation_id'])) {
                $conversation_id = (int)$data['conversation_id'];
            } elseif (!empty($_SESSION['active_ai_conversation_id'])) {
                $conversation_id = (int)$_SESSION['active_ai_conversation_id'];
            }
            
            $conversation = null;
            if ($conversation_id) {
                $conversation = $aiModel->getConversation($conversation_id);
            }
            
            // If conversation does not exist yet in DB, create it!
            if (!$conversation) {
                $firstSnippet = mb_substr($userMessage, 0, 42);
                if (mb_strlen($userMessage) > 42) $firstSnippet .= '...';
                $titlePrefix = $isComplaint ? 'Keluhan: ' : 'Konsultasi: ';
                $title = $titlePrefix . $firstSnippet;
                
                $guestName = $user_id ? null : ($userName !== 'Pelanggan' ? $userName : 'Pengunjung Web');
                $guestSession = session_id();
                
                $conversation_id = $aiModel->createAdvancedConversation($user_id, $title, $guestName, null, $guestSession, $isComplaint);
                $_SESSION['active_ai_conversation_id'] = $conversation_id;
                $conversation = $aiModel->getConversation($conversation_id);
            } else {
                // If it exists, update complaint flag if new message has complaints
                if ($isComplaint && empty($conversation['is_complaint'])) {
                    $aiModel->markAsComplaint($conversation_id);
                }
                $_SESSION['active_ai_conversation_id'] = $conversation_id;
            }
            
            // Add user message to DB
            $aiModel->addMessage($conversation_id, 'user', $userMessage);
            
            // Retrieve history from DB
            $formattedHistory = [];
            $history = $aiModel->getMessages($conversation_id);
            foreach ($history as $msg) {
                $formattedHistory[] = [
                    'role' => $msg['sender'],
                    'content' => $msg['message']
                ];
            }
            
            // Update widget session
            $_SESSION['widget_chat_history'] = $formattedHistory;
            
            // Services & DB stats for context
            $services = $this->model('ServiceModel')->getActiveServices() ?: [];
            $dbStats = $aiModel->getDatabaseStats();
            
            // Get AI Response
            $aiResponse = AIService::getResponse($formattedHistory, $services, $userName, $dbStats, $userRole);
            
            // Add AI response to DB
            $aiModel->addMessage($conversation_id, 'ai', $aiResponse);
            
            // Update widget session with AI response
            $_SESSION['widget_chat_history'][] = [
                'role' => 'ai',
                'content' => $aiResponse
            ];
            
            echo json_encode([
                'status' => 'success',
                'message' => $aiResponse,
                'conversation_id' => $conversation_id
            ]);
            return;
        }
    }

    public function getWidgetMessages() {
        header('Content-Type: application/json');
        $messages = [];
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $conversation_id = isset($_SESSION['active_ai_conversation_id']) ? (int)$_SESSION['active_ai_conversation_id'] : null;

        if ($conversation_id) {
            $dbMsgs = $this->model('AIModel')->getMessages($conversation_id);
            foreach ($dbMsgs as $m) {
                $messages[] = [
                    'sender' => $m['sender'],
                    'message' => $m['message']
                ];
            }
        } elseif ($user_id) {
            $convs = $this->model('AIModel')->getConversations($user_id);
            if (!empty($convs)) {
                $conversation_id = $convs[0]['id'];
                $_SESSION['active_ai_conversation_id'] = $conversation_id;
                $dbMsgs = $this->model('AIModel')->getMessages($conversation_id);
                foreach ($dbMsgs as $m) {
                    $messages[] = [
                        'sender' => $m['sender'],
                        'message' => $m['message']
                    ];
                }
            }
        }

        if (empty($messages) && isset($_SESSION['widget_chat_history']) && is_array($_SESSION['widget_chat_history'])) {
            foreach ($_SESSION['widget_chat_history'] as $m) {
                $messages[] = [
                    'sender' => ($m['role'] == 'user' ? 'user' : 'ai'),
                    'message' => $m['content']
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'messages' => $messages,
            'conversation_id' => $conversation_id
        ]);
        return;
    }

    public function clearWidgetMessages() {
        unset($_SESSION['widget_chat_history']);
        unset($_SESSION['active_ai_conversation_id']);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        return;
    }
}
