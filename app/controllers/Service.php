<?php

class Service extends Controller {
    public function index() {
        $serviceModel = $this->model('ServiceModel');
        $categoryModel = $this->model('CategoryModel');

        $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
        $keyword = isset($_GET['q']) ? $_GET['q'] : null;

        $data['services'] = $serviceModel->getActiveServices($categoryId, $keyword);
        $data['categories'] = $categoryModel->getAllCategories();
        $data['title'] = 'Katalog Layanan - ADCom';
        $data['current_category'] = $categoryId;
        $data['keyword'] = $keyword;

        $this->view('templates/header', $data);
        $this->view('service/index', $data);
        $this->view('templates/footer');
    }

    public function detail($id = null) {
        if ($id == null) {
            header('Location: ' . BASEURL . '/service');
            exit;
        }

        $serviceModel = $this->model('ServiceModel');
        $service = $serviceModel->getServiceById($id);

        if (!$service || $service['is_active'] == 0) {
            // Service not found or inactive
            header('Location: ' . BASEURL . '/service');
            exit;
        }

        $data['service'] = $service;
        $data['title'] = $service['name'] . ' - ADCom';
        $data['reviews'] = $this->model('ReviewModel')->getReviewsByServiceId($id);
        $data['rating_stats'] = $this->model('ReviewModel')->getServiceRatingStats($id);

        $this->view('templates/header', $data);
        $this->view('service/detail', $data);
        $this->view('templates/footer');
    }

    public function storeOrder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!CSRF::verify()) {
                Flasher::setFlash('Gagal', 'Token keamanan tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/service/detail/' . $_POST['service_id']);
                exit;
            }

            AuthGuard::checkRole('customer');
            
            $customerPhone = trim($_POST['customer_phone'] ?? '');
            if (empty($customerPhone) && !empty($_SESSION['user_phone'])) {
                $customerPhone = $_SESSION['user_phone'];
            }

            $data = [
                'customer_id' => $_SESSION['user_id'],
                'service_id' => $_POST['service_id'],
                'problem_description' => trim($_POST['problem_description']),
                'customer_address' => trim($_POST['customer_address'] ?? ''),
                'customer_phone' => $customerPhone
            ];
            
            $order_id = $this->model('OrderModel')->createOrder($data);
            if ($order_id > 0) {
                // Get Service Name for Notification
                $service = $this->model('ServiceModel')->getServiceById($data['service_id']);
                $serviceName = $service ? $service['name'] : 'Layanan';

                // Trigger Notification to Customer
                $this->model('NotificationModel')->addNotification(
                    $data['customer_id'], 
                    "Pesanan Berhasil Dibuat", 
                    "Pesanan untuk '$serviceName' telah kami terima dan sedang menunggu konfirmasi."
                );

                // Send WhatsApp Notification to Customer
                try {
                    WhatsAppService::sendOrderStatusNotification($order_id, 'created');
                } catch (Exception $e) {
                    error_log('WhatsApp Notification Order Error: ' . $e->getMessage());
                }

                // Trigger Notification to Operator
                $this->model('NotificationModel')->addNotificationToRole(
                    'operator',
                    "Pesanan Baru Masuk",
                    "Ada pesanan baru untuk layanan '$serviceName' dari pelanggan " . $_SESSION['user_name'] . "."
                );

                Flasher::setFlash('Berhasil', 'Pesanan Anda berhasil dibuat dan sedang diproses.', 'success');
                header('Location: ' . BASEURL . '/customer/orders');
            } else {
                Flasher::setFlash('Gagal', 'Gagal membuat pesanan. Silakan coba lagi.', 'danger');
                header('Location: ' . BASEURL . '/service/detail/' . $data['service_id']);
            }
            exit;
        }
    }
}
