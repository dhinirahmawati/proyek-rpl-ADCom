<?php

require_once __DIR__ . '/../services/WhatsApp/WhatsAppService.php';

class Auth extends Controller {
    public function index() {
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }

    public function login() {
        AuthGuard::guestOnly();

        $data['judul'] = 'Login - Jasa TKJ';
        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer');
    }

    public function processLogin() {
        AuthGuard::guestOnly();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!CSRF::verify()) {
                Flasher::setFlash('Gagal', 'Token keamanan tidak valid. Silakan coba lagi.', 'danger');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }

            // Basic brute-force protection
            if (isset($_SESSION['lockout_time'])) {
                if (time() < $_SESSION['lockout_time']) {
                    $remaining = ceil(($_SESSION['lockout_time'] - time()) / 60);
                    Flasher::setFlash('Gagal', "Terlalu banyak percobaan. Silakan coba lagi dalam $remaining menit.", 'danger');
                    header('Location: ' . BASEURL . '/auth/login');
                    exit;
                } else {
                    unset($_SESSION['lockout_time']);
                    unset($_SESSION['login_attempts']);
                }
            }

            $name = trim($_POST['name'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                Flasher::setFlash('Gagal', 'Nama pengguna, email, dan password wajib diisi!', 'danger');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }

            $userModel = $this->model('UserModel');
            $user = $userModel->getUserByEmail($email);

            $nameMatches = false;
            if ($user && !empty($user['name'])) {
                $dbName = strtolower(trim($user['name']));
                $inputName = strtolower($name);
                // Allow exact match or friendly partial match (e.g., 'admin' matches 'Administrator')
                if ($dbName === $inputName || strpos($dbName, $inputName) !== false || strpos($inputName, $dbName) !== false) {
                    $nameMatches = true;
                }
            }

            if ($user && $nameMatches && password_verify($password, $user['password'])) {
                // Check if account is active
                if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
                    Flasher::setFlash('Akun Dinonaktifkan', 'Akun Anda saat ini dinonaktifkan oleh Administrator. Silakan hubungi pihak sekolah/operator.', 'danger');
                    header('Location: ' . BASEURL . '/auth/login');
                    exit;
                }

                // Check 2FA WhatsApp OTP setting
                $otpModel = $this->model('OtpModel');
                $phone = !empty($user['phone_number']) ? $user['phone_number'] : '';

                if ($otpModel->isRequiredFor('login') && !empty($phone)) {
                    $otpResult = $otpModel->createOtp($phone, 'login', $user['id']);
                    if ($otpResult['success']) {
                        WhatsAppService::sendOtp($phone, $otpResult['otp_code'], $otpResult['expires_minutes'], 'login');

                        $_SESSION['pending_login_user'] = [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'role' => $user['role'],
                            'phone_number' => $phone
                        ];

                        Flasher::setFlash('Verifikasi Tambahan (2FA)', 'Kode OTP verifikasi telah dikirimkan ke WhatsApp Anda (' . WhatsAppService::maskPhone($phone) . '). Masukkan kode untuk menyelesaikan login.', 'info');
                        header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=login');
                        exit;
                    }
                }

                // Prevent Session Fixation
                session_regenerate_id(true);
                
                // Clear login attempts
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout_time']);

                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_phone'] = $user['phone_number'] ?? null;

                // Redirect based on role
                header('Location: ' . BASEURL . '/' . $user['role']);
                exit;
            } else {
                // Increment login attempts
                if (!isset($_SESSION['login_attempts'])) {
                    $_SESSION['login_attempts'] = 1;
                } else {
                    $_SESSION['login_attempts']++;
                }

                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['lockout_time'] = time() + (3 * 60); // Lockout 3 minutes
                    Flasher::setFlash('Gagal', 'Terlalu banyak percobaan. Akun terkunci sementara selama 3 menit.', 'danger');
                } else {
                    Flasher::setFlash('Gagal', 'Nama pengguna, email, atau password salah!', 'danger');
                }
                
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
        } else {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public function register() {
        AuthGuard::guestOnly();

        $data['judul'] = 'Registrasi Customer - ADCom Jasa';
        $this->view('templates/header', $data);
        $this->view('auth/register', $data);
        $this->view('templates/footer');
    }

    public function processRegister() {
        AuthGuard::guestOnly();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!CSRF::verify()) {
                Flasher::setFlash('Gagal', 'Token keamanan tidak valid. Silakan coba lagi.', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            $name = htmlspecialchars(trim($_POST['name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $phone = trim($_POST['phone_number'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Validation
            if (empty($name) || empty($email) || empty($password) || empty($phone)) {
                Flasher::setFlash('Gagal', 'Semua field wajib diisi termasuk nomor WhatsApp!', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            if (!WhatsAppService::isValidIndonesianPhone($phone)) {
                Flasher::setFlash('Gagal', 'Nomor WhatsApp tidak valid. Masukkan nomor HP Indonesia yang aktif (contoh: 081234567890).', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            $normalizedPhone = WhatsAppService::normalizePhone($phone);

            if ($password !== $password_confirm) {
                Flasher::setFlash('Gagal', 'Konfirmasi password tidak cocok!', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            if (strlen($password) < 6) {
                Flasher::setFlash('Gagal', 'Password minimal 6 karakter!', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            // Check if email exists
            $userModel = $this->model('UserModel');
            if ($userModel->getUserByEmail($email)) {
                Flasher::setFlash('Gagal', 'Email sudah terdaftar! Silakan gunakan email lain atau login.', 'warning');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            // Check if phone number exists
            if ($userModel->isPhoneTaken($normalizedPhone)) {
                Flasher::setFlash('Gagal', 'Nomor WhatsApp sudah terdaftar pada akun lain.', 'warning');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            $otpModel = $this->model('OtpModel');
            if ($otpModel->isRequiredFor('registration')) {
                // Generate OTP
                $otpResult = $otpModel->createOtp($normalizedPhone, 'registration');
                if (!$otpResult['success']) {
                    Flasher::setFlash('Perhatian', $otpResult['message'], 'warning');
                    header('Location: ' . BASEURL . '/auth/register');
                    exit;
                }

                // Send WhatsApp OTP
                $sendRes = WhatsAppService::sendOtp($normalizedPhone, $otpResult['otp_code'], $otpResult['expires_minutes'], 'registration');
                if (!$sendRes['success'] && empty($sendRes['is_simulated'])) {
                    Flasher::setFlash('Gagal Mengirim OTP WhatsApp', $sendRes['message'], 'danger');
                    header('Location: ' . BASEURL . '/auth/register');
                    exit;
                }

                // Save pending registration in session (password hashed)
                $_SESSION['pending_registration'] = [
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $normalizedPhone,
                    'password' => $password,
                    'created_at' => time()
                ];

                $infoNotice = 'Kode OTP 6 digit telah dikirim ke WhatsApp ' . WhatsAppService::maskPhone($normalizedPhone) . '. Masukkan kode untuk menyelesaikan pendaftaran.';
                if (!empty($sendRes['is_simulated'])) {
                    $infoNotice .= ' (Mode Simulasi Demo: ' . $otpResult['otp_code'] . ')';
                }

                Flasher::setFlash('Verifikasi WhatsApp', $infoNotice, 'info');
                header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=registration');
                exit;
            } else {
                // Direct registration without OTP
                if ($userModel->registerCustomer([
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $normalizedPhone,
                    'phone_verified_at' => null,
                    'password' => $password
                ]) > 0) {
                    Flasher::setFlash('Berhasil', 'Registrasi berhasil, silakan login.', 'success');
                    header('Location: ' . BASEURL . '/auth/login');
                    exit;
                } else {
                    Flasher::setFlash('Gagal', 'Registrasi gagal, coba lagi.', 'danger');
                    header('Location: ' . BASEURL . '/auth/register');
                    exit;
                }
            }
        }
    }

    public function verifyOtp() {
        AuthGuard::guestOnly();

        $purpose = isset($_GET['purpose']) ? trim($_GET['purpose']) : '';
        if (empty($purpose)) {
            if (isset($_SESSION['pending_registration'])) $purpose = 'registration';
            elseif (isset($_SESSION['pending_login_user'])) $purpose = 'login';
            elseif (isset($_SESSION['pending_reset_user'])) $purpose = 'reset_password';
            else $purpose = 'registration';
        }

        $phone = '';
        $targetName = '';

        if ($purpose === 'registration') {
            if (!isset($_SESSION['pending_registration'])) {
                Flasher::setFlash('Perhatian', 'Sesi registrasi tidak ditemukan atau sudah selesai. Silakan registrasi.', 'warning');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }
            $phone = $_SESSION['pending_registration']['phone_number'];
            $targetName = $_SESSION['pending_registration']['name'];
        } elseif ($purpose === 'login') {
            if (!isset($_SESSION['pending_login_user'])) {
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
            $phone = $_SESSION['pending_login_user']['phone_number'];
            $targetName = $_SESSION['pending_login_user']['name'];
        } elseif ($purpose === 'reset_password') {
            if (!isset($_SESSION['pending_reset_user'])) {
                header('Location: ' . BASEURL . '/auth/forgotPassword');
                exit;
            }
            $phone = $_SESSION['pending_reset_user']['phone_number'];
            $targetName = $_SESSION['pending_reset_user']['name'];
        }

        $otpModel = $this->model('OtpModel');
        $cooldown = $otpModel->getCooldownRemaining($phone, $purpose);
        $expiryMinutes = (int)$otpModel->getSetting('otp_expiry_minutes', '5');

        $data['judul'] = 'Verifikasi OTP WhatsApp - ADCom Jasa';
        $data['purpose'] = $purpose;
        $data['phone_masked'] = WhatsAppService::maskPhone($phone);
        $data['phone_raw'] = $phone;
        $data['cooldown'] = $cooldown;
        $data['expiry_minutes'] = $expiryMinutes;
        $data['target_name'] = $targetName;
        $data['provider_info'] = WhatsAppService::getStatusInfo();

        $this->view('templates/header', $data);
        $this->view('auth/verify_otp', $data);
        $this->view('templates/footer');
    }

    public function processVerifyOtp() {
        AuthGuard::guestOnly();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        if (!CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid. Silakan coba lagi.', 'danger');
            header('Location: ' . BASEURL . '/auth/verifyOtp');
            exit;
        }

        $purpose = trim($_POST['purpose'] ?? 'registration');
        $otpCode = trim($_POST['otp'] ?? '');

        // Support single or 6 split inputs
        if (empty($otpCode) && isset($_POST['otp_1'])) {
            $otpCode = ($_POST['otp_1'] ?? '') . ($_POST['otp_2'] ?? '') . ($_POST['otp_3'] ?? '') . 
                       ($_POST['otp_4'] ?? '') . ($_POST['otp_5'] ?? '') . ($_POST['otp_6'] ?? '');
        }
        $otpCode = preg_replace('/[^0-9]/', '', $otpCode);

        if (strlen($otpCode) !== 6) {
            Flasher::setFlash('Gagal', 'Kode OTP harus berupa 6 digit angka!', 'danger');
            header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=' . urlencode($purpose));
            exit;
        }

        $phone = '';
        $userId = null;

        if ($purpose === 'registration') {
            if (!isset($_SESSION['pending_registration'])) {
                Flasher::setFlash('Gagal', 'Sesi registrasi Anda telah berakhir. Silakan daftar ulang.', 'warning');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }
            $phone = $_SESSION['pending_registration']['phone_number'];
        } elseif ($purpose === 'login') {
            if (!isset($_SESSION['pending_login_user'])) {
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
            $phone = $_SESSION['pending_login_user']['phone_number'];
            $userId = $_SESSION['pending_login_user']['id'];
        } elseif ($purpose === 'reset_password') {
            if (!isset($_SESSION['pending_reset_user'])) {
                header('Location: ' . BASEURL . '/auth/forgotPassword');
                exit;
            }
            $phone = $_SESSION['pending_reset_user']['phone_number'];
            $userId = $_SESSION['pending_reset_user']['id'];
        }

        $otpModel = $this->model('OtpModel');
        $verifyResult = $otpModel->verifyOtp($phone, $otpCode, $purpose, $userId);

        if (!$verifyResult['success']) {
            Flasher::setFlash('Verifikasi Gagal', $verifyResult['message'], 'danger');
            header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=' . urlencode($purpose));
            exit;
        }

        // Branch by purpose upon success
        if ($purpose === 'registration') {
            $regData = $_SESSION['pending_registration'];
            unset($_SESSION['pending_registration']);

            $userModel = $this->model('UserModel');
            $created = $userModel->registerCustomer([
                'name' => $regData['name'],
                'email' => $regData['email'],
                'phone_number' => $regData['phone_number'],
                'phone_verified_at' => date('Y-m-d H:i:s'),
                'password' => $regData['password']
            ]);

            if ($created > 0) {
                session_regenerate_id(true);
                Flasher::setFlash('Registrasi Berhasil', 'Nomor WhatsApp Anda telah terverifikasi dan akun berhasil dibuat! Silakan login untuk melanjutkan.', 'success');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            } else {
                Flasher::setFlash('Gagal', 'Gagal menyimpan akun ke sistem. Silakan ulangi.', 'danger');
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }
        } elseif ($purpose === 'login') {
            $loginData = $_SESSION['pending_login_user'];
            unset($_SESSION['pending_login_user']);

            session_regenerate_id(true);
            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_time']);

            $_SESSION['user_id'] = $loginData['id'];
            $_SESSION['user_name'] = $loginData['name'];
            $_SESSION['user_role'] = $loginData['role'];
            $_SESSION['user_phone'] = $loginData['phone_number'];

            Flasher::setFlash('Login Berhasil', 'Autentikasi WhatsApp 2FA berhasil. Selamat datang kembali, ' . htmlspecialchars($loginData['name']) . '!', 'success');
            header('Location: ' . BASEURL . '/' . $loginData['role']);
            exit;
        } elseif ($purpose === 'reset_password') {
            $resetData = $_SESSION['pending_reset_user'];
            unset($_SESSION['pending_reset_user']);

            $_SESSION['verified_reset_user_id'] = $resetData['id'];
            Flasher::setFlash('Verifikasi Berhasil', 'Nomor WhatsApp Anda berhasil diverifikasi. Silakan masukkan password baru Anda di bawah ini.', 'success');
            header('Location: ' . BASEURL . '/auth/resetPassword');
            exit;
        }
    }

    public function resendOtp() {
        AuthGuard::guestOnly();

        $purpose = trim($_POST['purpose'] ?? $_GET['purpose'] ?? 'registration');
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !CSRF::verify()) {
            if ($isAjax) {
                echo json_encode(['status' => 'error', 'message' => 'Token keamanan CSRF tidak valid']);
                return;
            }
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=' . urlencode($purpose));
            exit;
        }

        $phone = '';
        $userId = null;

        if ($purpose === 'registration') {
            if (!isset($_SESSION['pending_registration'])) {
                if ($isAjax) { echo json_encode(['status' => 'error', 'message' => 'Sesi registrasi berakhir']); return; }
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }
            $phone = $_SESSION['pending_registration']['phone_number'];
        } elseif ($purpose === 'login') {
            if (!isset($_SESSION['pending_login_user'])) {
                if ($isAjax) { echo json_encode(['status' => 'error', 'message' => 'Sesi login berakhir']); return; }
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
            $phone = $_SESSION['pending_login_user']['phone_number'];
            $userId = $_SESSION['pending_login_user']['id'];
        } elseif ($purpose === 'reset_password') {
            if (!isset($_SESSION['pending_reset_user'])) {
                if ($isAjax) { echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir']); return; }
                header('Location: ' . BASEURL . '/auth/forgotPassword');
                exit;
            }
            $phone = $_SESSION['pending_reset_user']['phone_number'];
            $userId = $_SESSION['pending_reset_user']['id'];
        }

        $otpModel = $this->model('OtpModel');
        $createResult = $otpModel->createOtp($phone, $purpose, $userId);

        if (!$createResult['success']) {
            if ($isAjax) {
                echo json_encode([
                    'status' => 'error',
                    'message' => $createResult['message'],
                    'remaining' => $createResult['remaining'] ?? 0
                ]);
                return;
            }
            Flasher::setFlash('Perhatian', $createResult['message'], 'warning');
            header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=' . urlencode($purpose));
            exit;
        }

        $sendRes = WhatsAppService::sendOtp($phone, $createResult['otp_code'], $createResult['expires_minutes'], $purpose);

        $msg = 'Kode OTP baru telah dikirimkan ke WhatsApp Anda (' . WhatsAppService::maskPhone($phone) . ').';
        if (!empty($sendRes['is_simulated'])) {
            $msg .= ' (Mode Simulasi Demo: ' . $createResult['otp_code'] . ')';
        }

        if ($isAjax) {
            echo json_encode([
                'status' => 'success',
                'message' => $msg,
                'cooldown' => (int)$otpModel->getSetting('otp_resend_cooldown_seconds', '60')
            ]);
            return;
        }

        Flasher::setFlash('Kode OTP Dikirim Ulang', $msg, 'success');
        header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=' . urlencode($purpose));
        exit;
    }

    public function forgotPassword() {
        AuthGuard::guestOnly();

        $data['judul'] = 'Lupa Password - ADCom Jasa';
        $this->view('templates/header', $data);
        $this->view('auth/forgot_password', $data);
        $this->view('templates/footer');
    }

    public function processForgotPassword() {
        AuthGuard::guestOnly();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Permintaan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/auth/forgotPassword');
            exit;
        }

        $identifier = trim($_POST['identifier'] ?? '');
        if (empty($identifier)) {
            Flasher::setFlash('Gagal', 'Masukkan email atau nomor WhatsApp akun Anda!', 'danger');
            header('Location: ' . BASEURL . '/auth/forgotPassword');
            exit;
        }

        $userModel = $this->model('UserModel');
        $user = $userModel->getUserByIdentifier($identifier);

        if ($user && !empty($user['phone_number'])) {
            $otpModel = $this->model('OtpModel');
            $otpRes = $otpModel->createOtp($user['phone_number'], 'reset_password', $user['id']);

            if ($otpRes['success']) {
                $sendRes = WhatsAppService::sendOtp($user['phone_number'], $otpRes['otp_code'], $otpRes['expires_minutes'], 'reset_password');

                $_SESSION['pending_reset_user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'phone_number' => $user['phone_number']
                ];

                $info = 'Kode OTP verifikasi pemulihan akun telah dikirimkan ke WhatsApp ' . WhatsAppService::maskPhone($user['phone_number']) . '.';
                if (!empty($sendRes['is_simulated'])) {
                    $info .= ' (Mode Simulasi: ' . $otpRes['otp_code'] . ')';
                }

                Flasher::setFlash('Kode Pemulihan Terkirim', $info, 'info');
                header('Location: ' . BASEURL . '/auth/verifyOtp?purpose=reset_password');
                exit;
            } else {
                Flasher::setFlash('Perhatian', $otpRes['message'], 'warning');
                header('Location: ' . BASEURL . '/auth/forgotPassword');
                exit;
            }
        } else {
            // Privacy protection: do not leak whether account exists or not
            Flasher::setFlash('Informasi', 'Jika data email atau nomor WhatsApp tersebut terdaftar di sistem, kode OTP pemulihan telah dikirimkan.', 'info');
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public function resetPassword() {
        AuthGuard::guestOnly();

        if (!isset($_SESSION['verified_reset_user_id'])) {
            Flasher::setFlash('Akses Ditolak', 'Silakan lakukan verifikasi OTP terlebih dahulu.', 'warning');
            header('Location: ' . BASEURL . '/auth/forgotPassword');
            exit;
        }

        $data['judul'] = 'Buat Password Baru - ADCom Jasa';
        $this->view('templates/header', $data);
        $this->view('auth/reset_password', $data);
        $this->view('templates/footer');
    }

    public function processResetPassword() {
        AuthGuard::guestOnly();

        if (!isset($_SESSION['verified_reset_user_id'])) {
            header('Location: ' . BASEURL . '/auth/forgotPassword');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::verify()) {
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/auth/resetPassword');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($password) || strlen($password) < 6) {
            Flasher::setFlash('Gagal', 'Password baru minimal 6 karakter!', 'danger');
            header('Location: ' . BASEURL . '/auth/resetPassword');
            exit;
        }

        if ($password !== $password_confirm) {
            Flasher::setFlash('Gagal', 'Konfirmasi password baru tidak cocok!', 'danger');
            header('Location: ' . BASEURL . '/auth/resetPassword');
            exit;
        }

        $userId = $_SESSION['verified_reset_user_id'];
        unset($_SESSION['verified_reset_user_id']);

        $userModel = $this->model('UserModel');
        if ($userModel->updatePassword($userId, $password) >= 0) {
            Flasher::setFlash('Berhasil', 'Password baru berhasil disimpan! Silakan login dengan password baru Anda.', 'success');
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        } else {
            Flasher::setFlash('Gagal', 'Gagal memperbarui password. Silakan coba kembali.', 'danger');
            header('Location: ' . BASEURL . '/auth/resetPassword');
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        
        session_start();
        Flasher::setFlash('Berhasil', 'Anda telah logout.', 'success');
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
}
