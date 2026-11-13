<?php

class Customer extends Controller {
    public function __construct() {
        AuthGuard::checkRole('customer');
    }

    public function index() {
        $data['judul'] = 'Dashboard Customer';
        
        $this->view('templates/header', $data);
        $this->view('dashboards/customer/index', $data);
        $this->view('templates/footer');
    }

    public function orders() {
        $data['judul'] = 'Riwayat Pesanan - Customer';
        $customer_id = $_SESSION['user_id'];
        
        $data['orders'] = $this->model('OrderModel')->getOrdersByCustomer($customer_id);
        
        $this->view('templates/header', $data);
        $this->view('customer/orders/index', $data);
        $this->view('templates/footer');
    }

    public function orderDetail($id = null) {
        if ($id == null) {
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }

        $order = $this->model('OrderModel')->getOrderById($id);
        
        // Cek apakah order ini milik customer yang sedang login
        if (!$order || $order['customer_id'] != $_SESSION['user_id']) {
            Flasher::setFlash('Pesanan tidak ditemukan atau Anda tidak memiliki akses.', 'danger');
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }

        $data['judul'] = 'Detail Pesanan #' . $order['id'];
        $data['order'] = $order;
        $data['updates'] = $this->model('OrderUpdateModel')->getUpdatesByOrderId($id);
        $data['review'] = $this->model('ReviewModel')->getReviewByOrderId($id);

        $this->view('templates/header', $data);
        $this->view('customer/orders/detail', $data);
        $this->view('templates/footer');
    }

    public function submitReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            $customer_id = $_SESSION['user_id'];

            // 1. Validasi order milik customer
            $order = $this->model('OrderModel')->getOrderById($order_id);
            if (!$order || $order['customer_id'] != $customer_id) {
                Flasher::setFlash('Akses ditolak.', 'danger');
                header('Location: ' . BASEURL . '/customer/orders');
                exit;
            }

            // 2. Validasi status order adalah SELESAI (completed)
            if ($order['status'] !== 'completed') {
                Flasher::setFlash('Pesanan belum selesai. Anda tidak dapat memberikan ulasan.', 'danger');
                header('Location: ' . BASEURL . '/customer/orderDetail/' . $order_id);
                exit;
            }

            // 3. Validasi belum pernah direview
            $existingReview = $this->model('ReviewModel')->getReviewByOrderId($order_id);
            if ($existingReview) {
                Flasher::setFlash('Anda sudah memberikan ulasan untuk pesanan ini.', 'warning');
                header('Location: ' . BASEURL . '/customer/orderDetail/' . $order_id);
                exit;
            }

            // 4. Validasi rating 1-5
            if ($rating < 1 || $rating > 5) {
                Flasher::setFlash('Rating harus antara 1 sampai 5.', 'danger');
                header('Location: ' . BASEURL . '/customer/orderDetail/' . $order_id);
                exit;
            }

            $data = [
                'order_id' => $order_id,
                'rating' => $rating,
                'comment' => $comment
            ];

            if ($this->model('ReviewModel')->addReview($data) > 0) {
                Flasher::setFlash('Ulasan berhasil ditambahkan. Terima kasih atas feedback Anda!', 'success');
            } else {
                Flasher::setFlash('Gagal menambahkan ulasan.', 'danger');
            }

            header('Location: ' . BASEURL . '/customer/orderDetail/' . $order_id);
            exit;
        } else {
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }
    }

    public function deleteOrder($id = null) {
        if ($id == null) {
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }

        $order = $this->model('OrderModel')->getOrderById($id);

        // Pastikan pesanan milik customer yang sedang login
        if (!$order || $order['customer_id'] != $_SESSION['user_id']) {
            Flasher::setFlash('Gagal', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.', 'danger');
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }

        // Hanya pesanan dengan status 'pending' atau 'cancelled' yang dapat dihapus/dibatalkan
        if (!in_array($order['status'], ['pending', 'cancelled'])) {
            Flasher::setFlash('Gagal', 'Pesanan yang sedang ditugaskan atau dikerjakan tidak dapat dihapus.', 'warning');
            header('Location: ' . BASEURL . '/customer/orders');
            exit;
        }

        if ($this->model('OrderModel')->deleteOrder($id) > 0) {
            Flasher::setFlash('Berhasil', 'Pesanan #' . $id . ' berhasil dihapus/dibatalkan.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'Gagal menghapus pesanan.', 'danger');
        }

        header('Location: ' . BASEURL . '/customer/orders');
        exit;
    }
}
