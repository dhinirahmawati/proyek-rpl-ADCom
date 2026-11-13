-- phpMyAdmin SQL Dump
-- Database: `adcom_db`
--

CREATE DATABASE IF NOT EXISTS `adcom_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `adcom_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `phone_verified_at` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','technician','operator','admin') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`),
  KEY `role_idx` (`role`),
  KEY `phone_idx` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `services`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `estimated_time` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id_idx` (`category_id`),
  CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `status` enum('pending','assigned','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `problem_description` text NOT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id_idx` (`customer_id`),
  KEY `service_id_idx` (`service_id`),
  KEY `technician_id_idx` (`technician_id`),
  KEY `status_idx` (`status`),
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `order_updates`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_updates`;
CREATE TABLE `order_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id_idx` (`order_id`),
  CONSTRAINT `fk_updates_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_updates_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL COMMENT '1 to 5',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id_unique` (`order_id`),
  CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `ai_conversations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ai_conversations`;
CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT 'New Consultation',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `fk_ai_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `ai_messages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ai_messages`;
CREATE TABLE `ai_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `sender` enum('user','ai') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `conversation_id_idx` (`conversation_id`),
  CONSTRAINT `fk_msg_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- DATA SEEDING (SAMPLE DATA)
-- ========================================================

-- Insert Users (Password is explicitly 'password123' hashed using PHP password_hash for all users for testing purpose)
-- Note: the hash generated below is for 'password123' using BCRYPT.
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Budi Kepala Sekolah', 'kepsek@sekolah.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Siti Operator', 'operator@adcom.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator'),
(3, 'Agus Teknisi', 'agus@teknisi.adcom.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician'),
(4, 'Dina Teknisi', 'dina@teknisi.adcom.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician'),
(5, 'Ahmad Customer', 'ahmad@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
(6, 'Rini Customer', 'rini@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Networking', 'Layanan terkait instalasi, konfigurasi, dan perbaikan jaringan komputer.'),
(2, 'Hardware', 'Layanan perbaikan dan maintenance perangkat keras komputer dan laptop.'),
(3, 'Software', 'Layanan instalasi sistem operasi, aplikasi, dan troubleshooting perangkat lunak.'),
(4, 'Security', 'Layanan pemasangan dan pemeliharaan sistem keamanan seperti CCTV.');

-- Insert Services
INSERT INTO `services` (`id`, `category_id`, `name`, `description`, `estimated_price`, `estimated_time`, `image`, `is_active`) VALUES
(1, 4, 'Instalasi CCTV', 'Pemasangan kamera CCTV 4 channel hingga 16 channel untuk rumah dan kantor.', 1500000.00, '1-2 Hari', 'cctv_installation.jpg', 1),
(2, 1, 'Instalasi Jaringan LAN', 'Penarikan kabel UTP, terminasi, dan konfigurasi switch/router.', 500000.00, '1 Hari', 'lan_network.jpg', 1),
(3, 2, 'Service Komputer PC', 'Perbaikan motherboard, power supply, dan komponen hardware lainnya.', 250000.00, '3-5 Jam', 'pc_service.jpg', 1),
(4, 2, 'Service Laptop', 'Pembersihan heatsink, ganti thermal paste, perbaikan engsel, atau ganti layar.', 300000.00, '2-4 Jam', 'laptop_service.jpg', 1),
(5, 3, 'Instalasi Software & OS', 'Instalasi Windows, Linux, Microsoft Office, dan aplikasi desain.', 100000.00, '1-2 Jam', 'software_os.jpg', 1),
(6, 1, 'Konfigurasi Mikrotik', 'Setting bandwidth management, firewall, dan hotspot pada Mikrotik.', 450000.00, '2-3 Jam', 'mikrotik_config.jpg', 1);

-- Insert Orders
INSERT INTO `orders` (`id`, `customer_id`, `service_id`, `technician_id`, `status`, `problem_description`) VALUES
(1, 5, 4, 3, 'completed', 'Laptop sering mati sendiri saat dipakai edit video. Kemungkinan overheat.'),
(2, 6, 1, 4, 'processing', 'Ingin pasang 4 titik CCTV di sekitar rumah baru. Kabel disediakan sendiri.'),
(3, 5, 2, NULL, 'pending', 'Butuh ditarik 10 titik kabel LAN di lab sekolah. Minta survei dulu.');

-- Insert Order Updates
INSERT INTO `order_updates` (`id`, `order_id`, `updated_by`, `update_text`) VALUES
(1, 1, 2, 'Pesanan telah diterima dan dijadwalkan untuk teknisi Agus.'),
(2, 1, 3, 'Pengecekan awal: thermal paste sudah kering. Dilakukan penggantian dan pembersihan kipas.'),
(3, 1, 3, 'Perbaikan selesai. Laptop ditest rendering video 30 menit tidak mati.'),
(4, 2, 2, 'Pesanan diterima. Teknisi Dina akan survey lokasi besok sore.');

-- Insert Reviews
INSERT INTO `reviews` (`id`, `order_id`, `rating`, `comment`) VALUES
(1, 1, 5, 'Pelayanan mantap dan cepat. Laptop saya sekarang sedingin es. Terima kasih mas Agus!');

-- Insert AI Conversations
INSERT INTO `ai_conversations` (`id`, `user_id`, `title`) VALUES
(1, 5, 'Konsultasi: Laptop Lemot'),
(2, 6, 'Rekomendasi CCTV Outdoor');

-- --------------------------------------------------------
-- Table structure for table `otps`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `otps`;
CREATE TABLE `otps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `phone_number` varchar(30) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `purpose` enum('registration','login','reset_password','verify_phone') NOT NULL DEFAULT 'registration',
  `expires_at` datetime NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 5,
  `used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone_purpose` (`phone_number`,`purpose`),
  KEY `idx_user_purpose` (`user_id`,`purpose`),
  KEY `idx_expires` (`expires_at`),
  CONSTRAINT `fk_otps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `auth_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `auth_settings`;
CREATE TABLE `auth_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert Default Auth Settings
INSERT INTO `auth_settings` (`setting_key`, `setting_value`, `description`) VALUES
('otp_enabled', '1', 'Master switch OTP WhatsApp (1: aktif, 0: nonaktif)'),
('otp_required_registration', '1', 'Wajibkan verifikasi OTP WhatsApp saat registrasi Customer'),
('otp_required_login', '0', 'Verifikasi tambahan (2FA) saat login pengguna'),
('otp_required_reset_password', '1', 'Wajibkan OTP untuk pemulihan akun / reset password'),
('otp_expiry_minutes', '5', 'Masa berlaku kode OTP dalam satuan menit'),
('otp_max_attempts', '5', 'Maksimum percobaan salah sebelum OTP hangus'),
('otp_resend_cooldown_seconds', '60', 'Jeda waktu (cooldown) pengiriman ulang OTP dalam detik'),
('otp_provider', 'mock', 'Provider WhatsApp (mock, fonnte, wablas, meta_cloud, generic)');

-- Table structure for table `ai_settings`
CREATE TABLE IF NOT EXISTS `ai_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default AI Settings
INSERT INTO `ai_settings` (`setting_key`, `setting_value`, `description`) VALUES
('ai_enabled', '1', 'Status aktif chatbot AI (1 = Aktif, 0 = Nonaktif)'),
('ai_provider', 'gemini', 'Provider AI yang digunakan (gemini)'),
('ai_model', 'gemini-flash-latest', 'Nama model Gemini aktif'),
('ai_temperature', '0.7', 'Tingkat kreativitas respons (0.0 - 2.0)'),
('ai_max_tokens', '800', 'Batas maksimum token output respons'),
('ai_timeout', '15', 'Batas waktu timeout request ke API Gemini (detik)'),
('ai_fallback_faq', '1', 'Gunakan fallback FAQ lokal jika Gemini API gagal/limit'),
('bot_name', 'ADCom Virtual Assistant', 'Nama tampilan chatbot AI'),
('welcome_message', 'Halo! Saya Asisten Virtual ADCom Jasa. Ada yang bisa saya bantu seputar layanan servis komputer, laptop, printer, atau instalasi jaringan kami hari ini?', 'Pesan pembuka selamat datang saat widget chat pertama kali dibuka'),
('response_language', 'id', 'Bahasa respons utama chatbot (id = Bahasa Indonesia)'),
('bot_tone', 'friendly_professional', 'Gaya bahasa chatbot'),
('system_instruction', 'Anda adalah Chatbot Asisten AI untuk \'ADCom Jasa TKJ\'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik dan sopan.', 'Persona dan instruksi sistem dasar chatbot'),
('last_test_at', NULL, 'Waktu pengujian koneksi API terakhir'),
('last_test_status', NULL, 'Status hasil pengujian terakhir (success, error)'),
('last_test_latency', NULL, 'Latensi pengujian terakhir dalam milidetik (ms)'),
('last_test_message', NULL, 'Pesan atau respons pengujian terakhir');

