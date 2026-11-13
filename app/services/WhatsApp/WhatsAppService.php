<?php

require_once __DIR__ . '/WhatsAppServiceInterface.php';
require_once __DIR__ . '/Adapters/MockWhatsAppAdapter.php';
require_once __DIR__ . '/Adapters/FonnteAdapter.php';
require_once __DIR__ . '/Adapters/WablasAdapter.php';
require_once __DIR__ . '/Adapters/MetaCloudAdapter.php';

class WhatsAppService {
    private static $adapter = null;

    /**
     * Resolve the active WhatsApp Adapter
     *
     * @param string|null $preferredProvider
     * @return WhatsAppServiceInterface
     */
    public static function getAdapter($preferredProvider = null): WhatsAppServiceInterface {
        if (self::$adapter !== null && $preferredProvider === null) {
            return self::$adapter;
        }

        // Determine provider name (param > db setting > env/config > fallback mock)
        $provider = $preferredProvider;
        if (empty($provider) && class_exists('Database')) {
            try {
                $db = new Database();
                $db->query("SELECT setting_value FROM auth_settings WHERE setting_key = 'otp_provider' LIMIT 1");
                $row = $db->single();
                if ($row && !empty($row['setting_value'])) {
                    $provider = $row['setting_value'];
                }
            } catch (Exception $e) {
                // Ignore DB error, use env/constant
            }
        }

        if (empty($provider)) {
            $provider = defined('WHATSAPP_PROVIDER') ? WHATSAPP_PROVIDER : 'mock';
        }

        $provider = strtolower(trim($provider));

        switch ($provider) {
            case 'fonnte':
                $adapter = new FonnteAdapter();
                break;
            case 'wablas':
                $adapter = new WablasAdapter();
                break;
            case 'meta_cloud':
            case 'metacloud':
                $adapter = new MetaCloudAdapter();
                break;
            case 'mock':
            default:
                $adapter = new MockWhatsAppAdapter();
                break;
        }

        if ($preferredProvider === null) {
            self::$adapter = $adapter;
        }

        return $adapter;
    }

    /**
     * Normalize an Indonesian WhatsApp number to standard international format without '+' (e.g. 628123456789)
     *
     * @param string $phone
     * @return string
     */
    public static function normalizePhone($phone): string {
        $clean = preg_replace('/[^0-9]/', '', (string)$phone);

        if (empty($clean)) {
            return '';
        }

        // Handle 08xxx -> 628xxx
        if (strpos($clean, '08') === 0) {
            $clean = '62' . substr($clean, 1);
        } elseif (strpos($clean, '8') === 0) {
            // Handle 8xxx -> 628xxx
            $clean = '62' . $clean;
        }

        return $clean;
    }

    /**
     * Validate whether a phone number is a valid Indonesian mobile number
     *
     * @param string $phone
     * @return bool
     */
    public static function isValidIndonesianPhone($phone): bool {
        $normalized = self::normalizePhone($phone);
        
        // Indonesian mobile starts with 628 and has 10 to 15 digits total
        if (preg_match('/^628[1-9][0-9]{7,11}$/', $normalized)) {
            return true;
        }

        return false;
    }

    /**
     * Mask phone number for security on public/verification views (e.g. +62 812-•••-7890)
     *
     * @param string $phone
     * @return string
     */
    public static function maskPhone($phone): string {
        $clean = self::normalizePhone($phone);
        if (strlen($clean) < 8) {
            return $phone;
        }

        $prefix = substr($clean, 0, 5); // e.g. 62812
        $suffix = substr($clean, -3);    // last 3 digits
        return '+' . $prefix . '••••' . $suffix;
    }

    /**
     * Send OTP via current active provider
     *
     * @param string $phoneNumber
     * @param string $otpCode
     * @param int $expiresMinutes
     * @param string $purpose
     * @return array
     */
    public static function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array {
        $normalized = self::normalizePhone($phoneNumber);
        if (!self::isValidIndonesianPhone($normalized)) {
            return [
                'success' => false,
                'message' => 'Format nomor WhatsApp tidak valid. Gunakan nomor Indonesia aktif (contoh: 081234567890).',
                'provider' => 'validation_error',
                'is_simulated' => false
            ];
        }

        $adapter = self::getAdapter();
        return $adapter->sendOtp($normalized, $otpCode, $expiresMinutes, $purpose);
    }

    /**
     * Send test message
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public static function testMessage($phoneNumber, $message): array {
        $normalized = self::normalizePhone($phoneNumber);
        if (!self::isValidIndonesianPhone($normalized)) {
            return [
                'success' => false,
                'message' => 'Format nomor WhatsApp tidak valid. Gunakan nomor Indonesia aktif (contoh: 081234567890).',
                'provider' => 'validation_error',
                'is_simulated' => false
            ];
        }

        $adapter = self::getAdapter();
        return $adapter->sendMessage($normalized, $message);
    }

    /**
     * Get provider status information for dashboard settings
     *
     * @return array
     */
    public static function getStatusInfo(): array {
        $adapter = self::getAdapter();
        $providerName = $adapter->getProviderName();
        $isConfigured = $adapter->isConfigured();

        $token = defined('WHATSAPP_API_TOKEN') ? WHATSAPP_API_TOKEN : '';
        $apiUrl = defined('WHATSAPP_API_URL') ? WHATSAPP_API_URL : '';
        $senderId = defined('WHATSAPP_SENDER_ID') ? WHATSAPP_SENDER_ID : '';
        $providerKey = defined('WHATSAPP_PROVIDER') ? WHATSAPP_PROVIDER : 'mock';

        $isMock = ($providerKey === 'mock' || $adapter instanceof MockWhatsAppAdapter);

        $statusText = 'Belum Dikonfigurasi';
        $badgeClass = 'bg-danger';

        if ($isMock) {
            $statusText = 'Mode Demo / Simulasi (Mock)';
            $badgeClass = 'bg-info';
        } elseif ($isConfigured) {
            $statusText = 'Terkonfigurasi (Siap Digunakan)';
            $badgeClass = 'bg-success';
        }

        return [
            'provider_key' => $providerKey,
            'provider_name' => $providerName,
            'is_configured' => $isConfigured,
            'is_mock' => $isMock,
            'status_text' => $statusText,
            'badge_class' => $badgeClass,
            'api_url' => $apiUrl,
            'masked_token' => Env::mask($token),
            'sender_id' => ($senderId && $senderId !== 'ID_PENGIRIM_JIKA_DIBUTUHKAN') ? $senderId : 'Tidak ditentukan / Opsional'
        ];
    }

    /**
     * Send Order Lifecycle Notification to Customer WhatsApp
     *
     * @param int|array $order Either order ID or order row array
     * @param string $status Event/Status: created, assigned, processing, completed, cancelled
     * @param array $extra Optional metadata (notes, technician_name, etc.)
     * @return array
     */
    public static function sendOrderStatusNotification($order, string $status, array $extra = []): array {
        // Resolve order data if only order ID passed
        if (is_numeric($order)) {
            if (class_exists('Database')) {
                $db = new Database();
                $db->query("
                    SELECT o.*, 
                           u.name as customer_name, u.phone_number as user_phone, 
                           s.name as service_name, s.estimated_price,
                           t.name as technician_name, t.phone_number as technician_phone
                    FROM orders o
                    LEFT JOIN users u ON o.customer_id = u.id
                    LEFT JOIN services s ON o.service_id = s.id
                    LEFT JOIN users t ON o.technician_id = t.id
                    WHERE o.id = :id
                    LIMIT 1
                ");
                $db->bind('id', (int)$order);
                $order = $db->single();
            }
        }

        if (empty($order) || !is_array($order)) {
            return [
                'success' => false,
                'message' => 'Data pesanan tidak ditemukan untuk notifikasi WhatsApp.'
            ];
        }

        // Determine destination customer phone number
        $rawPhone = !empty($order['customer_phone']) ? $order['customer_phone'] : ($order['user_phone'] ?? '');
        if (empty($rawPhone)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp customer tidak dicantumkan.'
            ];
        }

        $normalized = self::normalizePhone($rawPhone);
        if (!self::isValidIndonesianPhone($normalized)) {
            return [
                'success' => false,
                'message' => 'Format nomor WhatsApp customer tidak valid.'
            ];
        }

        $orderId = $order['id'] ?? '-';
        $customerName = !empty($order['customer_name']) ? $order['customer_name'] : 'Pelanggan';
        $serviceName = !empty($order['service_name']) ? $order['service_name'] : 'Layanan ADCom';
        $problem = !empty($order['problem_description']) ? $order['problem_description'] : '-';
        $technicianName = !empty($order['technician_name']) ? $order['technician_name'] : ($extra['technician_name'] ?? 'Teknisi Kami');
        $note = $extra['note'] ?? '';
        $detailUrl = (defined('BASEURL') ? BASEURL : 'http://localhost/ADCom') . '/customer/orderDetail/' . $orderId;

        $message = "";

        switch (strtolower(trim($status))) {
            case 'created':
            case 'pending':
                $message = "Halo *{$customerName}*,\n\n"
                    . "Terima kasih telah melakukan pemesanan di *ADCom Jasa*! 🛠️\n\n"
                    . "📋 *DETAIL PESANAN #{$orderId}*\n"
                    . "• Layanan: *{$serviceName}*\n"
                    . "• Keluhan / Kendala: {$problem}\n"
                    . "• Status: *Menunggu Konfirmasi Operator* ⏳\n\n"
                    . "Pesanan Anda telah masuk ke sistem kami dan sedang menunggu penugasan teknisi. Tim kami akan segera memproses pesanan Anda.\n\n"
                    . "Cek progres pesanan Anda di sini:\n"
                    . "👉 {$detailUrl}\n\n"
                    . "_Pesan otomatis oleh Sistem ADCom Jasa_";
                break;

            case 'assigned':
                $message = "Halo *{$customerName}*,\n\n"
                    . "Update terbaru untuk pesanan Anda di *ADCom Jasa*! 👨‍🔧\n\n"
                    . "📋 *PESANAN #{$orderId} - {$serviceName}*\n"
                    . "• Status: *Teknisi Telah Ditugaskan* 🎯\n"
                    . "• Teknisi Bertugas: *{$technicianName}*\n\n"
                    . "Teknisi kami telah menerima tugas ini dan sedang mempersiapkan peralatan untuk menangani pesanan Anda.\n\n"
                    . "Lihat detail pesanan:\n"
                    . "👉 {$detailUrl}\n\n"
                    . "_Terima kasih telah mempercayai ADCom Jasa!_";
                break;

            case 'processing':
                $message = "Halo *{$customerName}*,\n\n"
                    . "Pemberitahuan pengerjaan pesanan Anda: ⚙️\n\n"
                    . "📋 *PESANAN #{$orderId} - {$serviceName}*\n"
                    . "• Status: *SEDANG DIKERJAKAN* 🔧\n"
                    . "• Teknisi: *{$technicianName}*\n"
                    . (!empty($note) ? "• Catatan: _{$note}_\n" : "")
                    . "\nTeknisi kami saat ini sedang aktif melakukan proses pengerjaan/perbaikan pada perangkat Anda. Kami akan memberi tahu Anda kembali saat pekerjaan telah selesai.\n\n"
                    . "Pantau progres di akun Anda:\n"
                    . "👉 {$detailUrl}\n\n"
                    . "_Semoga perangkat Anda lekas berfungsi optimal kembali!_";
                break;

            case 'completed':
                $message = "Halo *{$customerName}*,\n\n"
                    . "Kabar gembira! Pekerjaan untuk pesanan Anda telah *SELESAI*! 🎉\n\n"
                    . "📋 *PESANAN #{$orderId} - {$serviceName}*\n"
                    . "• Status: *SELESAI (COMPLETED)* ✅\n"
                    . "• Teknisi: *{$technicianName}*\n\n"
                    . "Perangkat atau instalasi Anda telah selesai dikerjakan dengan baik.\n\n"
                    . "⭐ *Beri Kami Rating & Ulasan*\n"
                    . "Pendapat Anda sangat berarti bagi kami dan performa teknisi. Mohon luangkan waktu sebentar untuk memberikan penilaian:\n"
                    . "👉 {$detailUrl}\n\n"
                    . "Terima kasih banyak telah menggunakan layanan *ADCom Jasa*! 🙏";
                break;

            case 'cancelled':
                $message = "Halo *{$customerName}*,\n\n"
                    . "Informasi perubahan status pesanan di *ADCom Jasa*:\n\n"
                    . "📋 *PESANAN #{$orderId} - {$serviceName}*\n"
                    . "• Status: *DIBATALKAN* ❌\n"
                    . (!empty($note) ? "• Alasan / Keterangan: _{$note}_\n" : "")
                    . "\nJika Anda membutuhkan informasi lebih lanjut atau ingin membuat pesanan baru, silakan kunjungi website kami:\n"
                    . "👉 " . (defined('BASEURL') ? BASEURL : 'http://localhost/ADCom') . "\n\n"
                    . "_Salam hangat, Tim ADCom Jasa_";
                break;

            default:
                $message = "Halo *{$customerName}*,\n\n"
                    . "Pemberitahuan status untuk Pesanan #{$orderId} ({$serviceName}):\n"
                    . "Status saat ini: *" . ucfirst($status) . "*\n\n"
                    . "Detail pesanan Anda: {$detailUrl}";
                break;
        }

        $adapter = self::getAdapter();
        try {
            return $adapter->sendMessage($normalized, $message);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim notifikasi WhatsApp: ' . $e->getMessage()
            ];
        }
    }
}

