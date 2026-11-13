<?php

require_once __DIR__ . '/../WhatsAppServiceInterface.php';

class FonnteAdapter implements WhatsAppServiceInterface {
    private $token;
    private $apiUrl;

    public function __construct($token = null, $apiUrl = null) {
        $this->token = $token ?: (defined('WHATSAPP_API_TOKEN') ? WHATSAPP_API_TOKEN : '');
        $this->apiUrl = $apiUrl ?: (defined('WHATSAPP_API_URL') && WHATSAPP_API_URL !== 'URL_API_DARI_PROVIDER' ? WHATSAPP_API_URL : 'https://api.fonnte.com/send');
    }

    public function isConfigured(): bool {
        return !empty($this->token) && $this->token !== 'ISI_TOKEN_DI_ENV';
    }

    public function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['debug_last_otp'])) {
            unset($_SESSION['debug_last_otp']);
        }
        $message = "Kode verifikasi ADCom Jasa Anda adalah {$otpCode}. Kode berlaku selama {$expiresMinutes} menit. Jangan bagikan kode ini kepada siapa pun.";
        return $this->sendMessage($phoneNumber, $message);
    }

    public function sendMessage($phoneNumber, $message): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Token WhatsApp Fonnte belum dikonfigurasi di file .env (WHATSAPP_API_TOKEN).',
                'provider' => 'fonnte',
                'is_simulated' => false
            ];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->token
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'target' => $phoneNumber,
            'message' => $message,
            'countryCode' => '62'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server WhatsApp: ' . $err,
                'provider' => 'fonnte',
                'is_simulated' => false
            ];
        }

        $resJson = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($resJson['status']) && $resJson['status'] === true) {
            return [
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dikirim melalui Fonnte.',
                'provider' => 'fonnte',
                'is_simulated' => false,
                'data' => $resJson
            ];
        }

        $errMsg = $resJson['reason'] ?? $resJson['message'] ?? 'Respon error dari gateway provider (HTTP ' . $httpCode . ')';
        return [
            'success' => false,
            'message' => 'Pengiriman WhatsApp gagal: ' . $errMsg,
            'provider' => 'fonnte',
            'is_simulated' => false,
            'data' => $resJson
        ];
    }

    public function getProviderName(): string {
        return 'Fonnte WhatsApp Gateway';
    }
}
