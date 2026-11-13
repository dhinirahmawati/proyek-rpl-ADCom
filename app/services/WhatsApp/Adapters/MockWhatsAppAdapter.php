<?php

require_once __DIR__ . '/../WhatsAppServiceInterface.php';

class MockWhatsAppAdapter implements WhatsAppServiceInterface {
    public function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array {
        $message = "Kode verifikasi ADCom Jasa Anda adalah {$otpCode}. Kode berlaku selama {$expiresMinutes} menit. Jangan bagikan kode ini kepada siapa pun.";
        
        // Save for local testing convenience (only in session, not in log or publicly)
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['debug_last_otp'] = [
                'phone' => $phoneNumber,
                'code' => $otpCode,
                'expires_at' => time() + ($expiresMinutes * 60),
                'purpose' => $purpose,
                'time' => date('Y-m-d H:i:s')
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP berhasil disimulasikan (Mode Demo / Mock WhatsApp).',
            'provider' => 'mock',
            'is_simulated' => true,
            'data' => [
                'target' => $phoneNumber,
                'status' => 'simulated'
            ]
        ];
    }

    public function sendMessage($phoneNumber, $message): array {
        return [
            'success' => true,
            'message' => 'Pesan uji coba berhasil disimulasikan (Mode Demo / Mock WhatsApp).',
            'provider' => 'mock',
            'is_simulated' => true,
            'data' => [
                'target' => $phoneNumber,
                'status' => 'simulated'
            ]
        ];
    }

    public function getProviderName(): string {
        return 'Mock / Demo WhatsApp (Simulasi Localhost)';
    }

    public function isConfigured(): bool {
        // Mock is always available for local dev
        return true;
    }
}
