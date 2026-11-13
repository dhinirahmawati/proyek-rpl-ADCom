<?php

require_once __DIR__ . '/../WhatsAppServiceInterface.php';

class WablasAdapter implements WhatsAppServiceInterface {
    private $token;
    private $apiUrl;

    public function __construct($token = null, $apiUrl = null) {
        $this->token = $token ?: (defined('WHATSAPP_API_TOKEN') ? WHATSAPP_API_TOKEN : '');
        $this->apiUrl = $apiUrl ?: (defined('WHATSAPP_API_URL') && WHATSAPP_API_URL !== 'URL_API_DARI_PROVIDER' ? WHATSAPP_API_URL : 'https://solo.wablas.com/api/send-message');
    }

    public function isConfigured(): bool {
        return !empty($this->token) && $this->token !== 'ISI_TOKEN_DI_ENV';
    }

    public function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array {
        $message = "Kode verifikasi ADCom Jasa Anda adalah {$otpCode}. Kode berlaku selama {$expiresMinutes} menit. Jangan bagikan kode ini kepada siapa pun.";
        return $this->sendMessage($phoneNumber, $message);
    }

    public function sendMessage($phoneNumber, $message): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Token WhatsApp Wablas belum dikonfigurasi di file .env (WHATSAPP_API_TOKEN).',
                'provider' => 'wablas',
                'is_simulated' => false
            ];
        }

        $payload = [
            'phone' => $phoneNumber,
            'message' => $message
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
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
                'message' => 'Gagal menghubungi server Wablas: ' . $err,
                'provider' => 'wablas',
                'is_simulated' => false
            ];
        }

        $resJson = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($resJson['status']) && ($resJson['status'] === true || $resJson['status'] === 'success')) {
            return [
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dikirim melalui Wablas.',
                'provider' => 'wablas',
                'is_simulated' => false,
                'data' => $resJson
            ];
        }

        $errMsg = $resJson['message'] ?? 'Respon error dari Wablas gateway (HTTP ' . $httpCode . ')';
        return [
            'success' => false,
            'message' => 'Pengiriman WhatsApp Wablas gagal: ' . $errMsg,
            'provider' => 'wablas',
            'is_simulated' => false,
            'data' => $resJson
        ];
    }

    public function getProviderName(): string {
        return 'Wablas WhatsApp Gateway';
    }
}
