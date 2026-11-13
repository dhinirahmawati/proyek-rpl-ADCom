<?php

require_once __DIR__ . '/../WhatsAppServiceInterface.php';

class MetaCloudAdapter implements WhatsAppServiceInterface {
    private $token;
    private $phoneNumberId;
    private $apiUrl;

    public function __construct($token = null, $phoneNumberId = null, $apiUrl = null) {
        $this->token = $token ?: (defined('WHATSAPP_API_TOKEN') ? WHATSAPP_API_TOKEN : '');
        $this->phoneNumberId = $phoneNumberId ?: (defined('WHATSAPP_SENDER_ID') ? WHATSAPP_SENDER_ID : '');
        
        if ($apiUrl && $apiUrl !== 'URL_API_DARI_PROVIDER') {
            $this->apiUrl = $apiUrl;
        } else {
            $id = $this->phoneNumberId ?: '{PHONE_NUMBER_ID}';
            $this->apiUrl = "https://graph.facebook.com/v19.0/{$id}/messages";
        }
    }

    public function isConfigured(): bool {
        return !empty($this->token) && $this->token !== 'ISI_TOKEN_DI_ENV' 
            && !empty($this->phoneNumberId) && $this->phoneNumberId !== 'ID_PENGIRIM_JIKA_DIBUTUHKAN';
    }

    public function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array {
        $message = "Kode verifikasi ADCom Jasa Anda adalah {$otpCode}. Kode berlaku selama {$expiresMinutes} menit. Jangan bagikan kode ini kepada siapa pun.";
        return $this->sendMessage($phoneNumber, $message);
    }

    public function sendMessage($phoneNumber, $message): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Kredensial WhatsApp Cloud API belum lengkap (WHATSAPP_API_TOKEN atau WHATSAPP_SENDER_ID belum diisi di .env).',
                'provider' => 'meta_cloud',
                'is_simulated' => false
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
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
                'message' => 'Gagal menghubungi Meta Graph API: ' . $err,
                'provider' => 'meta_cloud',
                'is_simulated' => false
            ];
        }

        $resJson = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($resJson['messages'][0]['id'])) {
            return [
                'success' => true,
                'message' => 'Pesan berhasil dikirim via WhatsApp Business Cloud API.',
                'provider' => 'meta_cloud',
                'is_simulated' => false,
                'data' => $resJson
            ];
        }

        $errMsg = $resJson['error']['message'] ?? 'Error dari Meta Cloud API (HTTP ' . $httpCode . ')';
        return [
            'success' => false,
            'message' => 'Pengiriman WhatsApp Cloud API gagal: ' . $errMsg,
            'provider' => 'meta_cloud',
            'is_simulated' => false,
            'data' => $resJson
        ];
    }

    public function getProviderName(): string {
        return 'WhatsApp Business Platform (Meta Cloud API)';
    }
}
