<?php

require_once __DIR__ . '/../services/WhatsApp/WhatsAppService.php';

class Admin extends Controller {
    public function __construct() {
        AuthGuard::checkRole('admin');
    }

    public function index() {
        $data['judul'] = 'Dashboard Kepala Sekolah';
        $data['satisfaction_stats'] = $this->model('ReviewModel')->getSatisfactionStats();
        $data['user_stats'] = $this->model('UserModel')->getUserStats();
        
        $this->view('templates/header', $data);
        $this->view('dashboards/admin/index', $data);
        $this->view('templates/footer');
    }

    // ==========================================
    // MANAJEMEN PENGGUNA (USERS)
    // ==========================================

    public function users() {
        $data['judul'] = 'Manajemen Pengguna';
        
        $role = isset($_GET['role']) && !empty($_GET['role']) && $_GET['role'] !== 'all' ? $_GET['role'] : null;
        $keyword = isset($_GET['q']) && !empty(trim($_GET['q'])) ? trim($_GET['q']) : null;

        $userModel = $this->model('UserModel');
        $data['users'] = $userModel->getAllUsers($role, $keyword);
        $data['user_stats'] = $userModel->getUserStats();
        $data['current_role'] = $role ?? 'all';
        $data['keyword'] = $keyword ?? '';

        $this->view('templates/header', $data);
        $this->view('admin/users/index', $data);
        $this->view('templates/footer');
    }

    public function createUser() {
        $data['judul'] = 'Tambah Pengguna Baru';

        $this->view('templates/header', $data);
        $this->view('admin/users/create', $data);
        $this->view('templates/footer');
    }

    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        if (!CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid. Silakan coba kembali.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            Flasher::setFlash('Gagal', 'Semua kolom bertanda bintang wajib diisi.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flasher::setFlash('Gagal', 'Format alamat email tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        $userModel = $this->model('UserModel');
        if ($userModel->isEmailTaken($email)) {
            Flasher::setFlash('Gagal', 'Alamat email sudah terdaftar. Gunakan email lain.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        if (strlen($password) < 6) {
            Flasher::setFlash('Gagal', 'Password minimal terdiri dari 6 karakter.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        if ($password !== $confirm_password) {
            Flasher::setFlash('Gagal', 'Konfirmasi password tidak cocok dengan password.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        if (!in_array($role, ['admin', 'operator', 'technician', 'customer'])) {
            Flasher::setFlash('Gagal', 'Role pengguna tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/createUser');
            exit;
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => $is_active
        ];

        if ($userModel->addUser($userData) > 0) {
            Flasher::setFlash('Pengguna baru berhasil', 'ditambahkan ke sistem.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'menambahkan pengguna baru ke database.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/users');
        exit;
    }

    public function editUser($id) {
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($id);

        if (!$user) {
            Flasher::setFlash('Gagal', 'Data pengguna tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        $data['judul'] = 'Edit Pengguna - ' . htmlspecialchars($user['name']);
        $data['user'] = $user;
        $data['is_self'] = ($user['id'] == $_SESSION['user_id']);
        $data['related'] = $userModel->getUserRelatedInfo($id);

        $this->view('templates/header', $data);
        $this->view('admin/users/edit', $data);
        $this->view('templates/footer');
    }

    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        if (!CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $userModel = $this->model('UserModel');
        $existing = $userModel->getUserById($id);
        if (!$existing) {
            Flasher::setFlash('Gagal', 'Data pengguna tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        if (empty($name) || empty($email) || empty($role)) {
            Flasher::setFlash('Gagal', 'Nama, email, dan role wajib diisi.', 'danger');
            header('Location: ' . BASEURL . '/admin/editUser/' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flasher::setFlash('Gagal', 'Format alamat email tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/editUser/' . $id);
            exit;
        }

        if ($userModel->isEmailTaken($email, $id)) {
            Flasher::setFlash('Gagal', 'Email tersebut sudah digunakan oleh pengguna lain.', 'danger');
            header('Location: ' . BASEURL . '/admin/editUser/' . $id);
            exit;
        }

        // Proteksi jika mengedit akun sendiri
        if ($id == $_SESSION['user_id']) {
            if ($role !== 'admin') {
                Flasher::setFlash('Peringatan', 'Anda tidak dapat mengubah role akun admin Anda sendiri.', 'warning');
                $role = 'admin';
            }
            if ($is_active == 0) {
                Flasher::setFlash('Peringatan', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan login.', 'warning');
                $is_active = 1;
            }
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                Flasher::setFlash('Gagal', 'Password baru minimal 6 karakter.', 'danger');
                header('Location: ' . BASEURL . '/admin/editUser/' . $id);
                exit;
            }
            if ($password !== $confirm_password) {
                Flasher::setFlash('Gagal', 'Konfirmasi password baru tidak cocok.', 'danger');
                header('Location: ' . BASEURL . '/admin/editUser/' . $id);
                exit;
            }
        }

        $userData = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => $password,
            'is_active' => $is_active
        ];

        if ($userModel->updateUser($userData) >= 0) {
            if ($id == $_SESSION['user_id']) {
                $_SESSION['user_name'] = $name;
            }
            Flasher::setFlash('Data pengguna berhasil', 'diperbarui.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'memperbarui data pengguna.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/users');
        exit;
    }

    public function toggleUser($id, $status) {
        $id = (int)$id;
        $status = (int)$status;

        if ($id == $_SESSION['user_id']) {
            Flasher::setFlash('Gagal', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif login.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($id);
        if (!$user) {
            Flasher::setFlash('Gagal', 'Pengguna tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        if ($userModel->toggleActive($id, $status) >= 0) {
            $statusText = $status == 1 ? 'diaktifkan' : 'dinonaktifkan';
            Flasher::setFlash('Status akun pengguna berhasil', $statusText . '.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'mengubah status akun pengguna.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/users');
        exit;
    }

    public function deleteUser($id) {
        $id = (int)$id;

        if ($id == $_SESSION['user_id']) {
            Flasher::setFlash('Gagal', 'Anda tidak dapat menghapus akun sendiri yang sedang aktif login.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($id);
        if (!$user) {
            Flasher::setFlash('Gagal', 'Pengguna tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/users');
            exit;
        }

        // Cek jika teknisi masih memiliki tugas aktif
        if ($user['role'] === 'technician') {
            $activeJobs = $userModel->getTechnicianJobCount($id);
            if ($activeJobs > 0) {
                Flasher::setFlash('Gagal Dihapus', 'Teknisi ini masih memiliki ' . $activeJobs . ' pekerjaan yang sedang aktif. Silakan selesaikan atau alihkan tugas terlebih dahulu, atau non-aktifkan akun ini.', 'warning');
                header('Location: ' . BASEURL . '/admin/users');
                exit;
            }
        }

        if ($userModel->deleteUser($id) > 0) {
            Flasher::setFlash('Pengguna berhasil', 'dihapus dari sistem.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'menghapus pengguna dari sistem.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/users');
        exit;
    }

    // ==========================================
    // KONFIGURASI AUTENTIKASI & OTP WHATSAPP
    // ==========================================

    public function authSettings() {
        $data['judul'] = 'Konfigurasi Autentikasi & WhatsApp OTP';
        $otpModel = $this->model('OtpModel');
        $data['settings'] = $otpModel->getAllSettings();
        $data['provider_info'] = WhatsAppService::getStatusInfo();

        $this->view('templates/header', $data);
        $this->view('admin/auth_settings/index', $data);
        $this->view('templates/footer');
    }

    public function updateAuthSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        if (!CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid. Silakan coba kembali.', 'danger');
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        $otpEnabled = isset($_POST['otp_enabled']) ? '1' : '0';
        $otpRequiredReg = isset($_POST['otp_required_registration']) ? '1' : '0';
        $otpRequiredLogin = isset($_POST['otp_required_login']) ? '1' : '0';
        $otpRequiredReset = isset($_POST['otp_required_reset_password']) ? '1' : '0';
        
        $expiryMinutes = max(1, min(60, (int)($_POST['otp_expiry_minutes'] ?? 5)));
        $maxAttempts = max(1, min(10, (int)($_POST['otp_max_attempts'] ?? 5)));
        $cooldownSeconds = max(10, min(300, (int)($_POST['otp_resend_cooldown_seconds'] ?? 60)));
        
        $provider = in_array($_POST['otp_provider'] ?? '', ['mock', 'fonnte', 'wablas', 'meta_cloud']) 
            ? $_POST['otp_provider'] 
            : 'mock';

        $settings = [
            'otp_enabled' => $otpEnabled,
            'otp_required_registration' => $otpRequiredReg,
            'otp_required_login' => $otpRequiredLogin,
            'otp_required_reset_password' => $otpRequiredReset,
            'otp_expiry_minutes' => (string)$expiryMinutes,
            'otp_max_attempts' => (string)$maxAttempts,
            'otp_resend_cooldown_seconds' => (string)$cooldownSeconds,
            'otp_provider' => $provider
        ];

        $otpModel = $this->model('OtpModel');
        $saveOk = $otpModel->updateSettings($settings);

        // Update environment securely
        $envUpdates = ['WHATSAPP_PROVIDER' => $provider];
        $tokenInput = trim($_POST['whatsapp_api_token'] ?? '');
        if (!empty($tokenInput)) {
            $envUpdates['WHATSAPP_API_TOKEN'] = $tokenInput;
        }
        $urlInput = trim($_POST['whatsapp_api_url'] ?? '');
        if (!empty($urlInput)) {
            $envUpdates['WHATSAPP_API_URL'] = $urlInput;
        }
        $senderInput = trim($_POST['whatsapp_sender_id'] ?? '');
        if (!empty($senderInput)) {
            $envUpdates['WHATSAPP_SENDER_ID'] = $senderInput;
        }

        Env::setMany($envUpdates);

        if ($saveOk) {
            Flasher::setFlash('Berhasil Disimpan', 'Pengaturan Autentikasi & Kredensial WhatsApp berhasil diperbarui.', 'success');
        } else {
            Flasher::setFlash('Gagal', 'Gagal memperbarui pengaturan autentikasi.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/authSettings');
        exit;
    }

    public function testWhatsApp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        if (!CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        $testPhone = trim($_POST['test_phone'] ?? '');
        if (empty($testPhone)) {
            Flasher::setFlash('Gagal', 'Masukkan nomor WhatsApp tujuan uji coba!', 'danger');
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        if (!WhatsAppService::isValidIndonesianPhone($testPhone)) {
            Flasher::setFlash('Gagal', 'Format nomor WhatsApp tidak valid. Masukkan nomor HP Indonesia yang aktif (contoh: 081234567890).', 'danger');
            header('Location: ' . BASEURL . '/admin/authSettings');
            exit;
        }

        $normPhone = WhatsAppService::normalizePhone($testPhone);
        $testMsg = "Halo! Ini adalah pesan uji coba koneksi gateway WhatsApp ADCom Jasa pada " . date('d M Y H:i:s') . ". Integrasi Anda berjalan dengan baik!";

        $res = WhatsAppService::testMessage($normPhone, $testMsg);

        if ($res['success']) {
            $notice = $res['message'];
            if (!empty($res['is_simulated'])) {
                $notice .= ' (Catatan: Ini adalah pengujian pada Mode Simulasi / Mock)';
            }
            Flasher::setFlash('Uji Pengiriman Berhasil', $notice, 'success');
        } else {
            Flasher::setFlash('Uji Pengiriman Gagal', $res['message'], 'danger');
        }

        header('Location: ' . BASEURL . '/admin/authSettings');
        exit;
    }

    public function aiConfig() {
        header('Location: ' . BASEURL . '/aiConfig');
        exit;
    }
}

