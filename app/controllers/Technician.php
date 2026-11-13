<?php

class Technician extends Controller {
    public function __construct() {
        AuthGuard::checkRole('technician');
    }

    public function index() {
        $data['judul'] = 'Dashboard Teknisi';
        $technician_id = $_SESSION['user_id'];
        
        $data['stats'] = $this->model('OrderModel')->getTechnicianStats($technician_id);
        
        // Get active jobs for dashboard
        $data['active_jobs'] = $this->model('OrderModel')->getOrdersByTechnician($technician_id, ['assigned', 'processing']);
        
        $this->view('templates/header', $data);
        $this->view('dashboards/technician/index', $data);
        $this->view('templates/footer');
    }

    public function jobs() {
        $data['judul'] = 'Riwayat Pekerjaan';
        $technician_id = $_SESSION['user_id'];
        
        $data['jobs'] = $this->model('OrderModel')->getOrdersByTechnician($technician_id);
        
        $this->view('templates/header', $data);
        $this->view('technician/jobs/index', $data);
        $this->view('templates/footer');
    }

    public function detail($id) {
        $data['judul'] = 'Detail Pekerjaan';
        $technician_id = $_SESSION['user_id'];
        
        $data['order'] = $this->model('OrderModel')->getOrderById($id);
        
        // Security check: ensure the job belongs to this technician
        if (!$data['order'] || $data['order']['technician_id'] != $technician_id) {
            header('Location: ' . BASEURL . '/technician/jobs');
            exit;
        }
        
        $data['updates'] = $this->model('OrderUpdateModel')->getUpdatesByOrderId($id);
        
        $this->view('templates/header', $data);
        $this->view('technician/jobs/detail', $data);
        $this->view('templates/footer');
    }

    public function updateJobStatus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $status = $_POST['status'];
            $technician_id = $_SESSION['user_id'];
            
            // Verify ownership
            $order = $this->model('OrderModel')->getOrderById($order_id);
            if ($order['technician_id'] == $technician_id) {
                if ($this->model('OrderModel')->updateStatus($order_id, $status) >= 0) {
                    
                    // Add automatic note about status change
                    $status_names = [
                        'processing' => 'Proses Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan'
                    ];
                    
                    $statusText = isset($status_names[$status]) ? $status_names[$status] : $status;
                    
                    $noteData = [
                        'order_id' => $order_id,
                        'updated_by' => $technician_id,
                        'update_text' => 'Status pekerjaan diubah menjadi: ' . $statusText,
                        'image' => null
                    ];
                    $this->model('OrderUpdateModel')->addUpdate($noteData);
                    
                    // Notify Customer and Operator
                    $serviceName = $order['service_name'];
                    $title = "";
                    $message = "";
                    
                    if ($status == 'processing') {
                        $title = "Pekerjaan Dimulai";
                        $message = "Teknisi telah mulai mengerjakan pesanan '$serviceName' Anda.";
                    } elseif ($status == 'completed') {
                        $title = "Pekerjaan Selesai";
                        $message = "Pesanan '$serviceName' Anda telah selesai dikerjakan.";
                    } elseif ($status == 'cancelled') {
                        $title = "Pesanan Dibatalkan";
                        $message = "Mohon maaf, pesanan '$serviceName' dibatalkan oleh teknisi.";
                    }
                    
                    if ($title != "") {
                        // To Customer
                        $this->model('NotificationModel')->addNotification($order['customer_id'], $title, $message);
                        
                        // To Customer WhatsApp
                        try {
                            WhatsAppService::sendOrderStatusNotification($order_id, $status, [
                                'technician_name' => $_SESSION['user_name'] ?? 'Teknisi ADCom'
                            ]);
                        } catch (Exception $e) {
                            error_log('WhatsApp Technician Job Notification Error: ' . $e->getMessage());
                        }

                        // To Operator
                        $operatorTitle = "Status Pekerjaan: " . $statusText;
                        $operatorMsg = "Pekerjaan '$serviceName' (ID: $order_id) telah diupdate menjadi '$statusText' oleh Teknisi.";
                        $this->model('NotificationModel')->addNotificationToRole('operator', $operatorTitle, $operatorMsg);
                    }
                    
                    Flasher::setFlash('berhasil', 'diupdate', 'success');
                } else {
                    Flasher::setFlash('gagal', 'diupdate', 'danger');
                }
            }
            header('Location: ' . BASEURL . '/technician/detail/' . $order_id);
            exit;
        }
    }

    public function addNote() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'];
            $technician_id = $_SESSION['user_id'];
            
            // Verify ownership
            $order = $this->model('OrderModel')->getOrderById($order_id);
            if ($order['technician_id'] == $technician_id) {
                
                $noteData = [
                    'order_id' => $order_id,
                    'updated_by' => $technician_id,
                    'update_text' => trim($_POST['update_text']),
                    'image' => null
                ];
                
                // Handle Image Upload if any
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
                        header('Location: ' . BASEURL . '/technician/detail/' . $order_id);
                        exit;
                    }

                    if ($fileSize > $maxSize) {
                        Flasher::setFlash('Gagal', 'Ukuran file melebihi 2MB.', 'danger');
                        header('Location: ' . BASEURL . '/technician/detail/' . $order_id);
                        exit;
                    }

                    $uploadDir = '../public/uploads/updates/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
                    $targetFile = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmp, $targetFile)) {
                        $noteData['image'] = $newFileName;
                    }
                }
                
                if ($this->model('OrderUpdateModel')->addUpdate($noteData) > 0) {
                    Flasher::setFlash('berhasil', 'ditambahkan', 'success');
                } else {
                    Flasher::setFlash('gagal', 'ditambahkan', 'danger');
                }
            }
            header('Location: ' . BASEURL . '/technician/detail/' . $order_id);
            exit;
        }
    }
}
