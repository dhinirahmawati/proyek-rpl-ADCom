<?php

require_once __DIR__ . '/../services/AIService.php';

class AiConfig extends Controller {

    public function __construct() {
        // Akses dibatasi secara ketat hanya untuk role admin (Kepala Sekolah) dan operator
        AuthGuard::checkRole(['admin', 'operator']);
    }

    /**
     * Halaman Utama Konfigurasi AI
     */
    public function index() {
        $aiModel = $this->model('AIModel');
        $rawSettings = $aiModel->getAllSettings();
        
        $settings = [];
        foreach ($rawSettings as $k => $item) {
            $settings[$k] = $item['value'];
        }

        // Cek status API Key tanpa pernah mengirimkan nilai raw ke view
        $currentApiKey = Env::get('AI_API_KEY', defined('AI_API_KEY') ? AI_API_KEY : '');
        $hasApiKey = (!empty($currentApiKey) && $currentApiKey !== 'ISI_API_KEY_DI_ENV');
        $maskedKey = $hasApiKey ? Env::mask($currentApiKey) : 'Belum Dikonfigurasi';

        // Daftar Model Google Gemini resmi dan terverifikasi
        $availableModels = [
            'gemini-flash-latest' => [
                'name' => 'Gemini Flash Latest',
                'badge' => 'Rekomendasi Utama',
                'badge_class' => 'bg-success',
                'desc' => 'Generasi mutakhir Gemini Flash: respons super cepat, cerdas, dan efisien untuk interaksi pelanggan.'
            ],
            'gemini-2.5-flash' => [
                'name' => 'Gemini 2.5 Flash',
                'badge' => 'Cepat & Stabil',
                'badge_class' => 'bg-info',
                'desc' => 'Cocok untuk FAQ, layanan pelanggan, dan konsultasi interaktif seputar pesanan servis.'
            ],
            'gemini-2.5-pro' => [
                'name' => 'Gemini 2.5 Pro',
                'badge' => 'Penalaran Lanjut',
                'badge_class' => 'bg-primary',
                'desc' => 'Ideal untuk kalkulasi harga jasa kompleks, analisis kerusakan teknis mendalam, dan troubleshooting.'
            ],
            'gemini-pro-latest' => [
                'name' => 'Gemini Pro Latest',
                'badge' => 'Model Pro Terkini',
                'badge_class' => 'bg-dark',
                'desc' => 'Versi Pro terbaru dengan pemahaman konteks percakapan yang sangat tinggi.'
            ],
            'gemini-2.5-flash-lite' => [
                'name' => 'Gemini 2.5 Flash Lite',
                'badge' => 'Ringan',
                'badge_class' => 'bg-secondary',
                'desc' => 'Varian ringan untuk respon cepat dengan penggunaan sumber daya minimal.'
            ]
        ];

        $data = [
            'judul' => 'Konfigurasi Chatbot AI',
            'settings' => $settings,
            'hasApiKey' => $hasApiKey,
            'maskedKey' => $maskedKey,
            'availableModels' => $availableModels,
            'userRole' => $_SESSION['user_role'] ?? 'admin',
            'userName' => $_SESSION['user_name'] ?? 'Petugas'
        ];

        $this->view('templates/header', $data);
        $this->view('admin/ai_config/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Simpan / Perbarui Pengaturan Konfigurasi AI
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/aiConfig');
            exit;
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

        if (!CSRF::verify()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Token keamanan CSRF tidak valid. Silakan muat ulang halaman.']);
                exit;
            }
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid. Silakan coba kembali.', 'danger');
            header('Location: ' . BASEURL . '/aiConfig');
            exit;
        }

        // Validasi seluruh input di sisi server
        $errors = [];

        // 1. Model Gemini
        $model = trim($_POST['ai_model'] ?? 'gemini-flash-latest');
        if (empty($model)) {
            $errors[] = 'Nama model Gemini wajib dipilih.';
        }

        // 2. Temperature (rentang 0.0 s/d 2.0)
        $temperature = filter_var($_POST['ai_temperature'] ?? '0.7', FILTER_VALIDATE_FLOAT);
        if ($temperature === false || $temperature < 0.0 || $temperature > 2.0) {
            $errors[] = 'Nilai Temperature harus berada di antara 0.0 hingga 2.0.';
        }

        // 3. Batas Maksimum Output Token (50 s/d 8192)
        $maxTokens = filter_var($_POST['ai_max_tokens'] ?? '800', FILTER_VALIDATE_INT);
        if ($maxTokens === false || $maxTokens < 50 || $maxTokens > 8192) {
            $errors[] = 'Batas Maksimum Token harus berupa angka bulat antara 50 hingga 8192.';
        }

        // 4. Batas Waktu Request (Timeout 5 s/d 60 detik)
        $timeout = filter_var($_POST['ai_timeout'] ?? '15', FILTER_VALIDATE_INT);
        if ($timeout === false || $timeout < 5 || $timeout > 60) {
            $errors[] = 'Batas Waktu Request (Timeout) harus berupa angka antara 5 hingga 60 detik.';
        }

        // 5. Switches (ai_enabled & ai_fallback_faq)
        $aiEnabled = isset($_POST['ai_enabled']) ? '1' : '0';
        $fallbackFaq = isset($_POST['ai_fallback_faq']) ? '1' : '0';

        // 6. Parameter Chatbot & Persona
        $botName = trim($_POST['bot_name'] ?? 'ADCom Virtual Assistant');
        if (empty($botName) || mb_strlen($botName) > 100) {
            $errors[] = 'Nama Chatbot wajib diisi (maksimal 100 karakter).';
        }

        $welcomeMessage = trim($_POST['welcome_message'] ?? '');
        if (empty($welcomeMessage)) {
            $errors[] = 'Pesan sambutan chatbot wajib diisi.';
        }

        $responseLanguage = trim($_POST['response_language'] ?? 'id');
        $botTone = trim($_POST['bot_tone'] ?? 'friendly_professional');

        $systemInstruction = trim($_POST['system_instruction'] ?? '');
        if (empty($systemInstruction)) {
            $errors[] = 'System instruction / instruksi perilaku chatbot wajib diisi.';
        }

        if (!empty($errors)) {
            $errorMsg = implode(' ', $errors);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $errorMsg]);
                exit;
            }
            Flasher::setFlash('Validasi Gagal', $errorMsg, 'danger');
            header('Location: ' . BASEURL . '/aiConfig');
            exit;
        }

        // 7. Penanganan API Key baru jika diisi
        $rawApiKey = trim($_POST['ai_api_key'] ?? '');
        $apiKeyUpdated = false;
        if (!empty($rawApiKey) && strpos($rawApiKey, '•••') === false && $rawApiKey !== 'ISI_API_KEY_DI_ENV') {
            if (strlen($rawApiKey) < 15) {
                $msg = 'Format API Key Google Gemini tidak valid (panjang minimal 15 karakter).';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                Flasher::setFlash('Gagal', $msg, 'danger');
                header('Location: ' . BASEURL . '/aiConfig');
                exit;
            }

            // Simpan API Key ke lingkungan aman (.env)
            $savedEnv = Env::setMany(['AI_API_KEY' => $rawApiKey]);
            if (!$savedEnv) {
                $msg = 'Gagal memperbarui file .env. Pastikan hak akses file environment tersedia.';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                Flasher::setFlash('Gagal', $msg, 'danger');
                header('Location: ' . BASEURL . '/aiConfig');
                exit;
            }
            $apiKeyUpdated = true;
        }

        // Simpan konfigurasi non-rahasia ke tabel ai_settings
        $aiModel = $this->model('AIModel');
        $saveSuccess = $aiModel->updateSettings([
            'ai_enabled' => $aiEnabled,
            'ai_provider' => 'gemini',
            'ai_model' => $model,
            'ai_temperature' => (string)$temperature,
            'ai_max_tokens' => (string)$maxTokens,
            'ai_timeout' => (string)$timeout,
            'ai_fallback_faq' => $fallbackFaq,
            'bot_name' => $botName,
            'welcome_message' => $welcomeMessage,
            'response_language' => $responseLanguage,
            'bot_tone' => $botTone,
            'system_instruction' => $systemInstruction
        ]);

        if ($saveSuccess) {
            $successMsg = 'Konfigurasi AI Chatbot berhasil disimpan dan langsung diterapkan!';
            if ($apiKeyUpdated) {
                $successMsg .= ' API Key baru telah diperbarui secara aman.';
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $successMsg,
                    'masked_key' => $apiKeyUpdated ? Env::mask($rawApiKey) : null
                ]);
                exit;
            }

            Flasher::setFlash('Berhasil', $successMsg, 'success');
        } else {
            $msg = 'Terjadi kesalahan sistem saat memperbarui konfigurasi database.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Flasher::setFlash('Gagal', $msg, 'danger');
        }

        header('Location: ' . BASEURL . '/aiConfig');
        exit;
    }

    /**
     * Endpoint Uji Koneksi Nyata ke Google Gemini API
     */
    public function testConnection() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        if (!CSRF::verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Token keamanan CSRF tidak valid. Silakan muat ulang halaman.']);
            exit;
        }

        $apiKey = trim($_POST['ai_api_key'] ?? '');
        $model = trim($_POST['ai_model'] ?? '');

        // Abaikan jika user tidak mengganti API key (tetap memakai masking placeholder)
        if (strpos($apiKey, '•••') !== false || $apiKey === 'ISI_API_KEY_DI_ENV') {
            $apiKey = null;
        }

        $result = AIService::testConnection($apiKey, $model);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * Reset Pengaturan AI ke Nilai Bawaan (Default)
     */
    public function resetDefault() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/aiConfig');
            exit;
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

        if (!CSRF::verify()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Token keamanan CSRF tidak valid.']);
                exit;
            }
            Flasher::setFlash('Gagal', 'Token keamanan CSRF tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/aiConfig');
            exit;
        }

        $aiModel = $this->model('AIModel');
        $success = $aiModel->resetSettingsToDefault();

        if ($success) {
            $msg = 'Seluruh parameter model dan persona AI berhasil di-reset ke nilai bawaan (default).';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg]);
                exit;
            }
            Flasher::setFlash('Berhasil', $msg, 'success');
        } else {
            $msg = 'Gagal me-reset pengaturan AI ke nilai bawaan.';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Flasher::setFlash('Gagal', $msg, 'danger');
        }

        header('Location: ' . BASEURL . '/aiConfig');
        exit;
    }
}
