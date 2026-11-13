<?php

require_once __DIR__ . '/../app/config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running OTP & WhatsApp Migration...\n";

    // 1. Check if phone_number column exists in users
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone_number'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone_number VARCHAR(30) NULL DEFAULT NULL AFTER email");
        echo "Added column 'phone_number' to 'users'.\n";
    } else {
        echo "Column 'phone_number' already exists in 'users'.\n";
    }

    // 2. Check if phone_verified_at column exists in users
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone_verified_at'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone_verified_at DATETIME NULL DEFAULT NULL AFTER phone_number");
        echo "Added column 'phone_verified_at' to 'users'.\n";
    } else {
        echo "Column 'phone_verified_at' already exists in 'users'.\n";
    }

    // 3. Create 'otps' table
    $sqlOtps = "
    CREATE TABLE IF NOT EXISTS `otps` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) DEFAULT NULL,
      `phone_number` VARCHAR(30) NOT NULL,
      `otp_hash` VARCHAR(255) NOT NULL,
      `purpose` ENUM('registration','login','reset_password','verify_phone') NOT NULL DEFAULT 'registration',
      `expires_at` DATETIME NOT NULL,
      `attempts` INT(11) NOT NULL DEFAULT 0,
      `max_attempts` INT(11) NOT NULL DEFAULT 5,
      `used_at` DATETIME DEFAULT NULL,
      `ip_address` VARCHAR(45) DEFAULT NULL,
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_phone_purpose` (`phone_number`, `purpose`),
      KEY `idx_user_purpose` (`user_id`, `purpose`),
      KEY `idx_expires` (`expires_at`),
      CONSTRAINT `fk_otps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    $pdo->exec($sqlOtps);
    echo "Table 'otps' ensured.\n";

    // 4. Create 'auth_settings' table
    $sqlSettings = "
    CREATE TABLE IF NOT EXISTS `auth_settings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `setting_key` VARCHAR(100) NOT NULL UNIQUE,
      `setting_value` TEXT DEFAULT NULL,
      `description` VARCHAR(255) DEFAULT NULL,
      `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    $pdo->exec($sqlSettings);
    echo "Table 'auth_settings' ensured.\n";

    // 5. Seed default auth settings if not present
    $defaultSettings = [
        ['otp_enabled', '1', 'Master switch OTP WhatsApp (1: aktif, 0: nonaktif)'],
        ['otp_required_registration', '1', 'Wajibkan verifikasi OTP WhatsApp saat registrasi Customer'],
        ['otp_required_login', '0', 'Verifikasi tambahan (2FA) saat login pengguna'],
        ['otp_required_reset_password', '1', 'Wajibkan OTP untuk pemulihan akun / reset password'],
        ['otp_expiry_minutes', '5', 'Masa berlaku kode OTP dalam satuan menit'],
        ['otp_max_attempts', '5', 'Maksimum percobaan salah sebelum OTP hangus'],
        ['otp_resend_cooldown_seconds', '60', 'Jeda waktu (cooldown) pengiriman ulang OTP dalam detik'],
        ['otp_provider', 'mock', 'Provider WhatsApp (mock, fonnte, wablas, meta_cloud, generic)']
    ];

    $insertStmt = $pdo->prepare("
        INSERT INTO auth_settings (setting_key, setting_value, description)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE description = VALUES(description)
    ");

    foreach ($defaultSettings as $s) {
        $insertStmt->execute($s);
    }
    echo "Default auth settings seeded.\n";

    // 6. Give existing users sample phone numbers if null
    $samplePhones = [
        1 => '081298765431', // Kepala Sekolah
        2 => '081298765432', // Operator
        3 => '081298765433', // Jono
        4 => '081298765434', // Dina
        5 => '081298765435', // Ahmad
        6 => '081298765436', // Rini
        14 => '081298765414', // Admin
        15 => '081298765415', // Customer
        16 => '081298765416', // Agus
        17 => '081298765417', // Dini Rahma
    ];

    $updatePhoneStmt = $pdo->prepare("
        UPDATE users 
        SET phone_number = :phone, phone_verified_at = IF(phone_verified_at IS NULL, NOW(), phone_verified_at) 
        WHERE id = :id AND (phone_number IS NULL OR phone_number = '')
    ");

    foreach ($samplePhones as $uid => $phone) {
        $updatePhoneStmt->execute(['id' => $uid, 'phone' => $phone]);
    }
    echo "Sample verified phone numbers linked to existing users.\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
