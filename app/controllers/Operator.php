<?php

class Operator extends Controller {
    public function __construct() {
        AuthGuard::checkRole('operator');
    }

    public function index() {
        $data['judul'] = 'Dashboard Operator';
        
        $this->view('templates/header', $data);
        $this->view('dashboards/operator/index', $data);
        $this->view('templates/footer');
    }

    // ==========================================
    // MANAJEMEN LAYANAN (SERVICES)
    // ==========================================

    public function services() {
        $data['judul'] = 'Manajemen Layanan';
        $data['services'] = $this->model('ServiceModel')->getAllServices();

        $this->view('templates/header', $data);
        $this->view('operator/services/index', $data);
        $this->view('templates/footer');
    }

    public function createService() {
        $data['judul'] = 'Tambah Layanan Baru';
        $data['categories'] = $this->model('CategoryModel')->getAllCategories();

        $this->view('templates/header', $data);
        $this->view('operator/services/create', $data);
        $this->view('templates/footer');
    }

    public function storeService() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'category_id' => $_POST['category_id'],
                'description' => trim($_POST['description']),
                'estimated_price' => trim($_POST['estimated_price']),
                'estimated_time' => trim($_POST['estimated_time']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'image' => null
            ];

            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $fileMime = mime_content_type($fileTmp);

                if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMimeTypes)) {
                    Flasher::setFlash('Gagal', 'Format file tidak didukung. Harap unggah gambar (JPG, PNG, GIF).', 'danger');
                    header('Location: ' . BASEURL . '/operator/createService');
                    exit;
                }

                if ($fileSize > $maxSize) {
                    Flasher::setFlash('Gagal', 'Ukuran file melebihi 2MB.', 'danger');
                    header('Location: ' . BASEURL . '/operator/createService');
                    exit;
                }

                $uploadDir = '../public/uploads/services/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
                $targetFile = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $data['image'] = $newFileName;
                }
            }

            if ($this->model('ServiceModel')->addService($data) > 0) {
                Flasher::setFlash('berhasil', 'ditambahkan', 'success');
            } else {
                Flasher::setFlash('gagal', 'ditambahkan', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/services');
            exit;
        }
    }

    public function editService($id) {
        $data['judul'] = 'Edit Layanan';
        $data['service'] = $this->model('ServiceModel')->getServiceById($id);
        $data['categories'] = $this->model('CategoryModel')->getAllCategories();

        if (!$data['service']) {
            header('Location: ' . BASEURL . '/operator/services');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('operator/services/edit', $data);
        $this->view('templates/footer');
    }

    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $_POST['id'],
                'name' => trim($_POST['name']),
                'category_id' => $_POST['category_id'],
                'description' => trim($_POST['description']),
                'estimated_price' => trim($_POST['estimated_price']),
                'estimated_time' => trim($_POST['estimated_time']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'image' => ''
            ];

            // Handle Image Upload if exists
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $fileMime = mime_content_type($fileTmp);

                if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMimeTypes)) {
                    Flasher::setFlash('Gagal', 'Format file tidak didukung. Harap unggah gambar (JPG, PNG, GIF).', 'danger');
                    header('Location: ' . BASEURL . '/operator/editService/' . $data['id']);
                    exit;
                }

                if ($fileSize > $maxSize) {
                    Flasher::setFlash('Gagal', 'Ukuran file melebihi 2MB.', 'danger');
                    header('Location: ' . BASEURL . '/operator/editService/' . $data['id']);
                    exit;
                }

                $uploadDir = '../public/uploads/services/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
                $targetFile = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $data['image'] = $newFileName;
                    // TODO: Could delete old image here
                }
            }

            if ($this->model('ServiceModel')->updateService($data) >= 0) {
                Flasher::setFlash('berhasil', 'diubah', 'success');
            } else {
                Flasher::setFlash('gagal', 'diubah', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/services');
            exit;
        }
    }

    public function toggleService($id, $status) {
        if ($this->model('ServiceModel')->toggleActive($id, $status) >= 0) {
            Flasher::setFlash('berhasil', 'diupdate statusnya', 'success');
        } else {
            Flasher::setFlash('gagal', 'diupdate statusnya', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/services');
        exit;
    }

    public function deleteService($id) {
        // Optional: delete image file
        $service = $this->model('ServiceModel')->getServiceById($id);
        if ($service && !empty($service['image'])) {
            $imagePath = '../public/uploads/services/' . $service['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->model('ServiceModel')->deleteService($id) > 0) {
            Flasher::setFlash('berhasil', 'dihapus', 'success');
        } else {
            Flasher::setFlash('gagal', 'dihapus', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/services');
        exit;
    }

    // ==========================================
    // MANAJEMEN KATEGORI (CATEGORIES)
    // ==========================================

    public function categories() {
        $data['judul'] = 'Manajemen Kategori';
        $data['categories'] = $this->model('CategoryModel')->getAllCategories();

        $this->view('templates/header', $data);
        $this->view('operator/categories/index', $data);
        $this->view('templates/footer');
    }

    public function createCategory() {
        $data['judul'] = 'Tambah Kategori Baru';

        $this->view('templates/header', $data);
        $this->view('operator/categories/create', $data);
        $this->view('templates/footer');
    }

    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('CategoryModel')->addCategory($_POST) > 0) {
                Flasher::setFlash('berhasil', 'ditambahkan', 'success');
            } else {
                Flasher::setFlash('gagal', 'ditambahkan', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/categories');
            exit;
        }
    }

    public function editCategory($id) {
        $data['judul'] = 'Edit Kategori';
        $data['category'] = $this->model('CategoryModel')->getCategoryById($id);

        if (!$data['category']) {
            header('Location: ' . BASEURL . '/operator/categories');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('operator/categories/edit', $data);
        $this->view('templates/footer');
    }

    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('CategoryModel')->updateCategory($_POST) >= 0) {
                Flasher::setFlash('berhasil', 'diubah', 'success');
            } else {
                Flasher::setFlash('gagal', 'diubah', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/categories');
            exit;
        }
    }

    public function deleteCategory($id) {
        if ($this->model('CategoryModel')->deleteCategory($id) > 0) {
            Flasher::setFlash('berhasil', 'dihapus', 'success');
        } else {
            Flasher::setFlash('gagal', 'dihapus', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/categories');
        exit;
    }

    // ==========================================
    // MANAJEMEN TEKNISI
    // ==========================================

    public function technicians() {
        $data['judul'] = 'Manajemen Teknisi';
        $data['technicians'] = $this->model('UserModel')->getTechnicians();
        
        // Count jobs for each technician
        foreach ($data['technicians'] as &$tech) {
            $tech['active_jobs'] = $this->model('UserModel')->getTechnicianJobCount($tech['id']);
        }

        $this->view('templates/header', $data);
        $this->view('operator/technicians/index', $data);
        $this->view('templates/footer');
    }

    public function createTechnician() {
        $data['judul'] = 'Tambah Teknisi Baru';

        $this->view('templates/header', $data);
        $this->view('operator/technicians/create', $data);
        $this->view('templates/footer');
    }

    public function storeTechnician() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('UserModel')->addTechnician($_POST) > 0) {
                Flasher::setFlash('berhasil', 'ditambahkan', 'success');
            } else {
                Flasher::setFlash('gagal', 'ditambahkan', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/technicians');
            exit;
        }
    }

    public function editTechnician($id) {
        $data['judul'] = 'Edit Teknisi';
        $data['technician'] = $this->model('UserModel')->getTechnicianById($id);

        if (!$data['technician']) {
            header('Location: ' . BASEURL . '/operator/technicians');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('operator/technicians/edit', $data);
        $this->view('templates/footer');
    }

    public function updateTechnician() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('UserModel')->updateTechnician($_POST) >= 0) {
                Flasher::setFlash('berhasil', 'diubah', 'success');
            } else {
                Flasher::setFlash('gagal', 'diubah', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/technicians');
            exit;
        }
    }

    public function toggleTechnician($id, $status) {
        if ($this->model('UserModel')->toggleActive($id, $status) >= 0) {
            Flasher::setFlash('berhasil', 'diupdate statusnya', 'success');
        } else {
            Flasher::setFlash('gagal', 'diupdate statusnya', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/technicians');
        exit;
    }

    // ==========================================
    // MANAJEMEN ORDER
    // ==========================================

    public function orders() {
        $data['judul'] = 'Manajemen Pekerjaan (Order)';
        $data['orders'] = $this->model('OrderModel')->getAllOrders();

        $this->view('templates/header', $data);
        $this->view('operator/orders/index', $data);
        $this->view('templates/footer');
    }

    public function assignTechnician($id) {
        $data['judul'] = 'Tugaskan Teknisi';
        $data['order'] = $this->model('OrderModel')->getOrderById($id);
        
        // Get active technicians
        $technicians = $this->model('UserModel')->getTechnicians();
        $data['technicians'] = array_filter($technicians, function($tech) {
            return $tech['is_active'] == 1;
        });
        
        foreach ($data['technicians'] as &$tech) {
            $tech['active_jobs'] = $this->model('UserModel')->getTechnicianJobCount($tech['id']);
        }

        if (!$data['order']) {
            header('Location: ' . BASEURL . '/operator/orders');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('operator/orders/assign', $data);
        $this->view('templates/footer');
    }

    public function storeAssignTechnician() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $technician_id = $_POST['technician_id'];
            
            if ($this->model('OrderModel')->assignTechnician($order_id, $technician_id) > 0) {
                // Add note to order_updates
                $noteData = [
                    'order_id' => $order_id,
                    'updated_by' => $_SESSION['user_id'],
                    'update_text' => 'Order telah ditugaskan ke teknisi baru.',
                    'image' => null
                ];
                $this->model('OrderUpdateModel')->addUpdate($noteData);
                
                // Get order details for notification
                $order = $this->model('OrderModel')->getOrderById($order_id);
                if ($order) {
                    $serviceName = $order['service_name'];
                    
                    // Notify Customer
                    $this->model('NotificationModel')->addNotification(
                        $order['customer_id'],
                        "Teknisi Ditugaskan",
                        "Teknisi telah ditugaskan untuk pesanan '$serviceName' Anda dan akan segera mengerjakannya."
                    );

                    // Notify Customer via WhatsApp
                    try {
                        WhatsAppService::sendOrderStatusNotification($order_id, 'assigned');
                    } catch (Exception $e) {
                        error_log('WhatsApp Assign Notification Error: ' . $e->getMessage());
                    }
                    
                    // Notify Technician
                    $this->model('NotificationModel')->addNotification(
                        $technician_id,
                        "Tugas Baru",
                        "Anda mendapatkan tugas baru untuk layanan '$serviceName'. Silakan cek detail pekerjaan."
                    );
                }
                
                Flasher::setFlash('berhasil', 'ditugaskan', 'success');
            } else {
                Flasher::setFlash('gagal', 'ditugaskan', 'danger');
            }
            header('Location: ' . BASEURL . '/operator/orders');
            exit;
        }
    }

    public function deleteOrder($id) {
        $order = $this->model('OrderModel')->getOrderById($id);
        if (!$order) {
            Flasher::setFlash('Pesanan tidak ditemukan', '', 'danger');
            header('Location: ' . BASEURL . '/operator/orders');
            exit;
        }

        // Hanya pesanan yang sudah selesai atau dibatalkan yang boleh dihapus
        if (!in_array($order['status'], ['completed', 'cancelled'])) {
            Flasher::setFlash('Pesanan belum selesai dikerjakan', 'tidak dapat dihapus', 'warning');
            header('Location: ' . BASEURL . '/operator/orders');
            exit;
        }

        if ($this->model('OrderModel')->deleteOrder($id) > 0) {
            Flasher::setFlash('Pesanan #' . $id . ' berhasil', 'dihapus', 'success');
        } else {
            Flasher::setFlash('Pesanan gagal', 'dihapus', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/orders');
        exit;
    }

    public function completeOrder($id) {
        $order = $this->model('OrderModel')->getOrderById($id);
        if (!$order) {
            Flasher::setFlash('Pesanan tidak ditemukan', '', 'danger');
            header('Location: ' . BASEURL . '/operator/orders');
            exit;
        }

        if ($this->model('OrderModel')->updateStatus($id, 'completed') >= 0) {
            // Add note to order_updates
            $noteData = [
                'order_id' => $id,
                'updated_by' => $_SESSION['user_id'],
                'update_text' => 'Pekerjaan telah ditandai selesai oleh Operator.',
                'image' => null
            ];
            $this->model('OrderUpdateModel')->addUpdate($noteData);

            // Notify Customer
            $serviceName = $order['service_name'];
            $this->model('NotificationModel')->addNotification(
                $order['customer_id'],
                "Pekerjaan Selesai",
                "Pesanan '$serviceName' Anda telah selesai dikerjakan. Terima kasih telah menggunakan jasa kami!"
            );

            // Notify Customer via WhatsApp
            try {
                WhatsAppService::sendOrderStatusNotification($id, 'completed');
            } catch (Exception $e) {
                error_log('WhatsApp Complete Notification Error: ' . $e->getMessage());
            }

            Flasher::setFlash('Pesanan #' . $id . ' berhasil', 'diselesaikan', 'success');
        } else {
            Flasher::setFlash('Pesanan gagal', 'diselesaikan', 'danger');
        }
        header('Location: ' . BASEURL . '/operator/orders');
        exit;
    }

    // ==========================================
    // MANAJEMEN REVIEW (ULASAN)
    // ==========================================

    public function reviews() {
        $data['judul'] = 'Semua Ulasan & Penilaian';
        $data['reviews'] = $this->model('ReviewModel')->getAllReviews();

        $this->view('templates/header', $data);
        $this->view('operator/reviews/index', $data);
        $this->view('templates/footer');
    }

    public function aiConfig() {
        header('Location: ' . BASEURL . '/aiConfig');
        exit;
    }
}
