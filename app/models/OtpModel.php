<?php

class OtpModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Get a setting value from auth_settings
     */
    public function getSetting($key, $default = null) {
        $this->db->query("SELECT setting_value FROM auth_settings WHERE setting_key = :k LIMIT 1");
        $this->db->bind('k', $key);
        $row = $this->db->single();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Get all auth settings as associative array
     */
    public function getAllSettings(): array {
        $this->db->query("SELECT setting_key, setting_value, description FROM auth_settings ORDER BY id ASC");
        $rows = $this->db->resultSet();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = [
                'value' => $r['setting_value'],
                'description' => $r['description']
            ];
        }
        return $settings;
    }

    /**
     * Update settings in auth_settings
     */
    public function updateSettings(array $settings): bool {
        try {
            foreach ($settings as $key => $val) {
                $this->db->query("UPDATE auth_settings SET setting_value = :val WHERE setting_key = :key");
                $this->db->bind('val', (string)$val);
                $this->db->bind('key', (string)$key);
                $this->db->execute();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check whether OTP is enabled globally
     */
    public function isOtpEnabled(): bool {
        return (int)$this->getSetting('otp_enabled', '1') === 1;
    }

    /**
     * Check whether OTP is required for a specific purpose
     */
    public function isRequiredFor($purpose): bool {
        if (!$this->isOtpEnabled()) {
            return false;
        }

        switch ($purpose) {
            case 'registration':
                return (int)$this->getSetting('otp_required_registration', '1') === 1;
            case 'login':
                return (int)$this->getSetting('otp_required_login', '0') === 1;
            case 'reset_password':
                return (int)$this->getSetting('otp_required_reset_password', '1') === 1;
            case 'verify_phone':
                return true;
            default:
                return false;
        }
    }

    /**
     * Get remaining cooldown seconds for resending OTP
     */
    public function getCooldownRemaining($phoneNumber, $purpose = 'registration'): int {
        $cooldownLimit = (int)$this->getSetting('otp_resend_cooldown_seconds', '60');

        $this->db->query("
            SELECT (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(created_at)) AS elapsed_seconds
            FROM otps 
            WHERE phone_number = :phone AND purpose = :purpose 
            ORDER BY id DESC LIMIT 1
        ");
        $this->db->bind('phone', $phoneNumber);
        $this->db->bind('purpose', $purpose);
        $row = $this->db->single();

        if (!$row || $row['elapsed_seconds'] === null) {
            return 0;
        }

        $elapsed = (int)$row['elapsed_seconds'];
        if ($elapsed < $cooldownLimit && $elapsed >= 0) {
            return $cooldownLimit - $elapsed;
        }

        return 0;
    }

    /**
     * Generate and store a new cryptographically secure OTP
     *
     * @param string $phoneNumber
     * @param string $purpose
     * @param int|null $userId
     * @param string|null $ipAddress
     * @return array
     */
    public function createOtp($phoneNumber, $purpose = 'registration', $userId = null, $ipAddress = null): array {
        // 1. Check cooldown
        $remaining = $this->getCooldownRemaining($phoneNumber, $purpose);
        if ($remaining > 0) {
            return [
                'success' => false,
                'error' => 'cooldown',
                'remaining' => $remaining,
                'message' => "Mohon tunggu {$remaining} detik sebelum meminta pengiriman ulang kode OTP."
            ];
        }

        // 2. Invalidate any existing unused OTPs for this phone + purpose
        $this->db->query("
            UPDATE otps 
            SET used_at = NOW() 
            WHERE phone_number = :phone AND purpose = :purpose AND used_at IS NULL
        ");
        $this->db->bind('phone', $phoneNumber);
        $this->db->bind('purpose', $purpose);
        $this->db->execute();

        // 3. Generate 6-digit cryptographically secure numeric OTP
        $otpCode = (string)random_int(100000, 999999);
        $otpHash = password_hash($otpCode, PASSWORD_BCRYPT);

        // 4. Fetch settings
        $expiryMinutes = (int)$this->getSetting('otp_expiry_minutes', '5');
        if ($expiryMinutes <= 0) $expiryMinutes = 5;

        $maxAttempts = (int)$this->getSetting('otp_max_attempts', '5');
        if ($maxAttempts <= 0) $maxAttempts = 5;

        // 5. Insert to DB using MySQL DATE_ADD for accurate timestamp sync
        $this->db->query("
            INSERT INTO otps (user_id, phone_number, otp_hash, purpose, expires_at, attempts, max_attempts, ip_address)
            VALUES (:uid, :phone, :hash, :purpose, DATE_ADD(NOW(), INTERVAL :mins MINUTE), 0, :max_att, :ip)
        ");
        $this->db->bind('uid', $userId);
        $this->db->bind('phone', $phoneNumber);
        $this->db->bind('hash', $otpHash);
        $this->db->bind('purpose', $purpose);
        $this->db->bind('mins', $expiryMinutes);
        $this->db->bind('max_att', $maxAttempts);
        $this->db->bind('ip', $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'));

        try {
            $ok = $this->db->execute();
            if (!$ok) {
                return [
                    'success' => false,
                    'error' => 'db_error',
                    'message' => 'Gagal membuat kode verifikasi di sistem database.'
                ];
            }
            return [
                'success' => true,
                'otp_code' => $otpCode,
                'phone_number' => $phoneNumber,
                'purpose' => $purpose,
                'expires_minutes' => $expiryMinutes
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'db_error',
                'message' => 'Gagal membuat kode verifikasi di sistem: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify submitted OTP
     *
     * @param string $phoneNumber
     * @param string $otpInput
     * @param string $purpose
     * @param int|null $userId
     * @return array
     */
    public function verifyOtp($phoneNumber, $otpInput, $purpose = 'registration', $userId = null): array {
        $otpInput = trim((string)$otpInput);

        // Find active OTP record with expiration evaluation directly in MySQL
        $query = "
            SELECT *, (NOW() > expires_at) AS is_expired FROM otps 
            WHERE phone_number = :phone AND purpose = :purpose AND used_at IS NULL 
            ORDER BY id DESC LIMIT 1
        ";
        $this->db->query($query);
        $this->db->bind('phone', $phoneNumber);
        $this->db->bind('purpose', $purpose);
        $otp = $this->db->single();

        if (!$otp) {
            return [
                'success' => false,
                'error' => 'not_found',
                'message' => 'Kode OTP tidak ditemukan atau sudah pernah digunakan. Silakan minta kode baru.'
            ];
        }

        // Check if expired
        if ((int)$otp['is_expired'] === 1) {
            $this->db->query("UPDATE otps SET used_at = NOW() WHERE id = :id");
            $this->db->bind('id', $otp['id']);
            $this->db->execute();

            return [
                'success' => false,
                'error' => 'expired',
                'message' => 'Kode OTP telah kedaluwarsa. Silakan klik tombol kirim ulang kode OTP.'
            ];
        }

        // Check max attempts
        if ($otp['attempts'] >= $otp['max_attempts']) {
            $this->db->query("UPDATE otps SET used_at = NOW() WHERE id = :id");
            $this->db->bind('id', $otp['id']);
            $this->db->execute();

            return [
                'success' => false,
                'error' => 'max_attempts',
                'message' => 'Batas percobaan salah telah terlampaui. Kode OTP dibatalkan demi keamanan. Silakan minta kode baru.'
            ];
        }

        // Verify hash
        if (password_verify($otpInput, $otp['otp_hash'])) {
            // Success! Mark as used
            $this->db->query("UPDATE otps SET used_at = NOW() WHERE id = :id");
            $this->db->bind('id', $otp['id']);
            $this->db->execute();

            // If user exists, mark phone as verified
            if (!empty($otp['user_id'])) {
                $this->db->query("
                    UPDATE users 
                    SET phone_number = :phone, phone_verified_at = NOW() 
                    WHERE id = :uid
                ");
                $this->db->bind('phone', $phoneNumber);
                $this->db->bind('uid', $otp['user_id']);
                $this->db->execute();
            }

            return [
                'success' => true,
                'message' => 'Verifikasi OTP berhasil!',
                'otp_id' => $otp['id'],
                'user_id' => $otp['user_id'],
                'phone_number' => $phoneNumber
            ];
        } else {
            // Increment failed attempt
            $newAttempts = $otp['attempts'] + 1;
            $remaining = max(0, $otp['max_attempts'] - $newAttempts);

            $this->db->query("UPDATE otps SET attempts = attempts + 1 WHERE id = :id");
            $this->db->bind('id', $otp['id']);
            $this->db->execute();

            if ($remaining <= 0) {
                // Invalidate OTP
                $this->db->query("UPDATE otps SET used_at = NOW() WHERE id = :id");
                $this->db->bind('id', $otp['id']);
                $this->db->execute();

                return [
                    'success' => false,
                    'error' => 'max_attempts',
                    'remaining_attempts' => 0,
                    'message' => 'Kode OTP salah. Kesempatan habis! Kode telah hangus, silakan klik kirim ulang kode baru.'
                ];
            }

            return [
                'success' => false,
                'error' => 'invalid_code',
                'remaining_attempts' => $remaining,
                'message' => "Kode OTP salah! Sisa percobaan Anda: {$remaining} kali."
            ];
        }
    }
}
