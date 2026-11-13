<?php
/**
 * Database Migration for AI Configuration
 * Creates `ai_settings` table and seeds default configurations
 */

require_once __DIR__ . '/../app/init.php';

echo "Running AI Configuration Migration...\n";

$db = new Database();

$createTableSql = "
CREATE TABLE IF NOT EXISTS ai_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    description VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $db->query($createTableSql);
    $db->execute();
    echo "[OK] Table 'ai_settings' created or verified.\n";
} catch (PDOException $e) {
    die("[FAIL] Creating table ai_settings failed: " . $e->getMessage() . "\n");
}

$defaultSettings = [
    [
        'key' => 'ai_enabled',
        'value' => '1',
        'desc' => 'Status aktif chatbot AI (1 = Aktif, 0 = Nonaktif)'
    ],
    [
        'key' => 'ai_provider',
        'value' => 'gemini',
        'desc' => 'Provider AI yang digunakan (gemini)'
    ],
    [
        'key' => 'ai_model',
        'value' => 'gemini-flash-latest',
        'desc' => 'Nama model Gemini aktif (gemini-flash-latest, gemini-2.5-flash, gemini-2.5-pro, dll)'
    ],
    [
        'key' => 'ai_temperature',
        'value' => '0.7',
        'desc' => 'Tingkat kreativitas respons (0.0 - 2.0)'
    ],
    [
        'key' => 'ai_max_tokens',
        'value' => '800',
        'desc' => 'Batas maksimum token output respons'
    ],
    [
        'key' => 'ai_timeout',
        'value' => '15',
        'desc' => 'Batas waktu timeout request ke API Gemini (detik)'
    ],
    [
        'key' => 'ai_fallback_faq',
        'value' => '1',
        'desc' => 'Gunakan fallback FAQ lokal jika Gemini API gagal/limit (1 = Ya, 0 = Tidak)'
    ],
    [
        'key' => 'bot_name',
        'value' => 'ADCom Virtual Assistant',
        'desc' => 'Nama tampilan chatbot AI'
    ],
    [
        'key' => 'welcome_message',
        'value' => 'Halo! Saya Asisten Virtual ADCom Jasa. Ada yang bisa saya bantu seputar layanan servis komputer, laptop, printer, atau instalasi jaringan kami hari ini?',
        'desc' => 'Pesan pembuka selamat datang saat widget chat pertama kali dibuka'
    ],
    [
        'key' => 'response_language',
        'value' => 'id',
        'desc' => 'Bahasa respons utama chatbot (id = Bahasa Indonesia)'
    ],
    [
        'key' => 'bot_tone',
        'value' => 'friendly_professional',
        'desc' => 'Gaya bahasa chatbot (friendly_professional, concise, educational)'
    ],
    [
        'key' => 'system_instruction',
        'value' => "Anda adalah Chatbot Asisten AI untuk 'ADCom Jasa TKJ'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik dan sopan.",
        'desc' => 'Persona dan instruksi sistem dasar chatbot'
    ],
    [
        'key' => 'last_test_at',
        'value' => null,
        'desc' => 'Waktu pengujian koneksi API terakhir'
    ],
    [
        'key' => 'last_test_status',
        'value' => null,
        'desc' => 'Status hasil pengujian terakhir (success, error)'
    ],
    [
        'key' => 'last_test_latency',
        'value' => null,
        'desc' => 'Latensi pengujian terakhir dalam milidetik (ms)'
    ],
    [
        'key' => 'last_test_message',
        'value' => null,
        'desc' => 'Pesan atau respons pengujian terakhir'
    ]
];

foreach ($defaultSettings as $s) {
    $db->query("SELECT id FROM ai_settings WHERE setting_key = :k LIMIT 1");
    $db->bind('k', $s['key']);
    $existing = $db->single();

    if (!$existing) {
        $db->query("INSERT INTO ai_settings (setting_key, setting_value, description) VALUES (:k, :v, :d)");
        $db->bind('k', $s['key']);
        $db->bind('v', $s['value']);
        $db->bind('d', $s['desc']);
        $db->execute();
        echo "[INSERT] Seeded: {$s['key']}\n";
    }
}

echo "[SUCCESS] AI Configuration Migration Completed!\n";
