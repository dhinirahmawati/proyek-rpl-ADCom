<?php

class ChatHistory extends Controller {
    public function __construct() {
        AuthGuard::checkLogin();
        if (!in_array($_SESSION['user_role'], ['admin', 'operator', 'technician'])) {
            Flasher::setFlash('Akses Ditolak', 'Halaman ini hanya dapat diakses oleh Administrator, Operator, dan Teknisi.', 'danger');
            header('Location: ' . BASEURL . '/' . $_SESSION['user_role']);
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Riwayat Chatbot & Keluhan Pelanggan';
        $data['userRole'] = $_SESSION['user_role'];
        $data['userName'] = $_SESSION['user_name'];

        $filterType = isset($_GET['type']) ? trim($_GET['type']) : 'all';
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
        $dateFilter = isset($_GET['date']) ? trim($_GET['date']) : 'all';
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

        $aiModel = $this->model('AIModel');
        $data['conversations'] = $aiModel->getAllConversations($filterType, $status, $dateFilter, $keyword);
        $data['stats'] = $aiModel->getConversationStats();
        
        $data['filterType'] = $filterType;
        $data['status'] = $status;
        $data['dateFilter'] = $dateFilter;
        $data['keyword'] = $keyword;

        $this->view('templates/header', $data);
        $this->view('chat_history/index', $data);
        $this->view('templates/footer');
    }

    /**
     * AJAX Endpoint to get full conversation transcript
     */
    public function detail($id = 0) {
        header('Content-Type: application/json');
        $id = (int)$id;

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID percakapan tidak valid']);
            return;
        }

        $aiModel = $this->model('AIModel');
        $conv = $aiModel->getConversationWithDetails($id);

        if (!$conv) {
            echo json_encode(['status' => 'error', 'message' => 'Percakapan tidak ditemukan']);
            return;
        }

        $messages = $aiModel->getMessages($id);

        echo json_encode([
            'status' => 'success',
            'conversation' => $conv,
            'messages' => $messages
        ]);
        return;
    }

    /**
     * Update follow-up status of a conversation (new, followed_up, closed)
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/chatHistory');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        if ($id <= 0 || !in_array($status, ['new', 'followed_up', 'closed'])) {
            Flasher::setFlash('Gagal', 'Parameter status atau ID tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/chatHistory');
            exit;
        }

        $aiModel = $this->model('AIModel');
        if ($aiModel->updateConversationStatus($id, $status)) {
            $statusLabels = [
                'new' => 'Baru',
                'followed_up' => 'Ditindaklanjuti',
                'closed' => 'Selesai / Ditutup'
            ];
            Flasher::setFlash('Berhasil', 'Status percakapan diubah menjadi: ' . ($statusLabels[$status] ?? $status), 'success');
        } else {
            Flasher::setFlash('Gagal', 'Gagal memperbarui status percakapan.', 'danger');
        }

        header('Location: ' . BASEURL . '/chatHistory');
        exit;
    }

    /**
     * Delete conversation (Admin only)
     */
    public function delete($id = 0) {
        $id = (int)$id;

        if ($_SESSION['user_role'] !== 'admin') {
            Flasher::setFlash('Akses Ditolak', 'Hanya Administrator yang dapat menghapus riwayat percakapan.', 'danger');
            header('Location: ' . BASEURL . '/chatHistory');
            exit;
        }

        if ($id <= 0) {
            Flasher::setFlash('Gagal', 'ID percakapan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/chatHistory');
            exit;
        }

        $aiModel = $this->model('AIModel');
        if ($aiModel->deleteConversation($id)) {
            Flasher::setFlash('Berhasil', 'Riwayat percakapan berhasil dihapus dari sistem.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'Gagal menghapus percakapan.', 'danger');
        }

        header('Location: ' . BASEURL . '/chatHistory');
        exit;
    }
}
