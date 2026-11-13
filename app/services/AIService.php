<?php

class AIService {
    
    // Abstract/Mock service for testing without real API Key
    public static function getResponse($messages, $servicesData, $userName = 'Pelanggan', $dbStats = [], $userRole = null) {
        $apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
        
        if (empty($userRole) && isset($_SESSION['user_role'])) {
            $userRole = $_SESSION['user_role'];
        }

        // Ambil konfigurasi dinamis dari database
        $settings = self::getDynamicSettings();
        $aiEnabled = isset($settings['ai_enabled']) ? (int)$settings['ai_enabled'] : 1;
        $fallbackFaq = isset($settings['ai_fallback_faq']) ? (int)$settings['ai_fallback_faq'] : 1;

        // Cek jika AI dinonaktifkan
        if ($aiEnabled === 0) {
            if ($fallbackFaq === 1) {
                $resp = self::getMockResponse($messages, $servicesData, $userName, $dbStats, $userRole);
            } else {
                return "Mohon maaf, layanan Chatbot AI saat ini sedang dinonaktifkan oleh administrator. Silakan hubungi kami melalui kontak atau form pemesanan layanan.";
            }
        } else {
            // Cek jika menggunakan mock/fallback (API Key kosong)
            if (empty($apiKey) || $apiKey === 'ISI_API_KEY_DI_ENV') {
                $resp = self::getMockResponse($messages, $servicesData, $userName, $dbStats, $userRole);
            } else {
                $resp = self::callGeminiAPI($messages, $servicesData, $apiKey, $userName, $dbStats, $userRole, $settings);
            }
        }

        // Jika user adalah admin, operator, atau teknisi: jawab to-the-point dan hapus komentar tambahan
        if (in_array($userRole, ['admin', 'operator', 'technician'])) {
            $resp = self::cleanStaffResponse($resp, $userRole);
        }

        return $resp;
    }
    
    private static function callGeminiAPI($messages, $servicesData, $apiKey, $userName = 'Pelanggan', $dbStats = [], $userRole = null, $settings = []) {
        $modelName = !empty($settings['ai_model']) ? trim($settings['ai_model']) : 'gemini-flash-latest';
        $modelName = preg_replace('#^models/#', '', $modelName);
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode($modelName) . ':generateContent?key=' . $apiKey;
        $isStaff = in_array($userRole, ['admin', 'operator', 'technician']);
        $botName = !empty($settings['bot_name']) ? trim($settings['bot_name']) : 'Chatbot Asisten AI';

        // Buat system instruction
        if ($isStaff) {
            $roleTitle = strtoupper($userRole);
            $systemPrompt = "Anda adalah {$botName} untuk 'ADCom Jasa TKJ'. Pengguna saat ini adalah {$roleTitle} (Internal Staff / Manajemen ADCom) bernama {$userName}.\n";
            $systemPrompt .= "ATURAN MUTLAK UNTUK {$roleTitle}:\n";
            $systemPrompt .= "1. JAWAB HANYA SESUAI DENGAN PERTANYAAN SECARA LANGSUNG, SINGKAT, PADAT, FAKTUAL, DAN AKURAT.\n";
            $systemPrompt .= "2. DILARANG MENAMBAHKAN KOMENTAR TAMBAHAN APAPUN: tidak perlu menambahkan kalimat penutup ramah-tamah (seperti 'Apakah ada yang ingin ditanyakan lagi?', 'Semoga harimu menyenangkan', dll), ajakan memesan/promosi, saran/rekomendasi yang tidak ditanyakan, atau basa-basi.\n";
            $systemPrompt .= "3. Langsung sajikan data, angka, nama, atau status yang diminta tanpa embel-embel komentar.\n\n";
        } else {
            $baseInstruction = !empty($settings['system_instruction']) ? trim($settings['system_instruction']) : "Anda adalah {$botName} untuk 'ADCom Jasa TKJ'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik.";
            $systemPrompt = $baseInstruction . "\n\n";
            $systemPrompt .= "Pengguna yang saat ini berbicara dengan Anda bernama: " . $userName . ".\n";
            $systemPrompt .= "Jika ditanya siapa Anda, jawablah bahwa Anda adalah {$botName} resmi dari ADCom yang siap membantu konsultasi teknis dan informasi layanan kami.\n";
            $systemPrompt .= "Jika ditanya 'siapa saya' atau 'siapa namaku', jawablah dengan ramah bahwa ia adalah pelanggan terdaftar bernama " . $userName . ".\n";
        }
        if (!empty($dbStats)) {
            $totalAll = $dbStats['total_all_users'] ?? 0;
            $techTotal = $dbStats['total_technicians'] ?? 0;
            $techActiveCount = $dbStats['total_technicians_active'] ?? 0;
            $techInactiveCount = $dbStats['total_technicians_inactive'] ?? 0;
            $techActiveNames = $dbStats['technicians_active_names'] ?? '-';
            $techInactiveNames = $dbStats['technicians_inactive_names'] ?? '-';
            $techStatusDetail = $dbStats['technicians_with_status'] ?? '';

            $systemPrompt .= "Data statistik dan pengguna sistem saat ini:\n";
            $systemPrompt .= "- Total seluruh pengguna yang ada di dalam sistem: {$totalAll} orang\n";
            $systemPrompt .= "- Pelanggan (Customer): " . ($dbStats['total_customers'] ?? 0) . " orang (yaitu: " . ($dbStats['customer_names'] ?? '') . ")\n";
            $systemPrompt .= "- Teknisi: {$techTotal} orang. Rincian status teknisi:\n";
            $systemPrompt .= "  * Teknisi Aktif ({$techActiveCount} orang): {$techActiveNames} (hanya teknisi aktif ini yang siap bertugas/membantu layanan)\n";
            $systemPrompt .= "  * Teknisi Non-Aktif ({$techInactiveCount} orang): {$techInactiveNames} (sedang tidak aktif/tidak bertugas)\n";
            $systemPrompt .= "  * Status per teknisi: {$techStatusDetail}\n";
            $systemPrompt .= "- Operator: " . ($dbStats['total_operators'] ?? 0) . " orang (yaitu: " . ($dbStats['operator_names'] ?? '') . ")\n";
            $systemPrompt .= "- Admin: " . ($dbStats['total_admins'] ?? 0) . " orang (yaitu: " . ($dbStats['admin_names'] ?? '') . ")\n";
            $systemPrompt .= "- Total layanan jasa aktif: " . (count($servicesData)) . " layanan\n";
            $systemPrompt .= "- Data Pesanan / Pekerjaan (Orders) saat ini:\n";
            $systemPrompt .= "  * Total Pesanan: " . ($dbStats['total_orders'] ?? 0) . " pesanan\n";
            $systemPrompt .= "  * Sedang Dikerjakan (Processing) (" . ($dbStats['count_processing'] ?? 0) . " pesanan):\n";
            if (!empty($dbStats['orders_processing'])) {
                foreach ($dbStats['orders_processing'] as $po) {
                    $systemPrompt .= "    - #{$po['id']}: Layanan '{$po['service_name']}' untuk Pelanggan '{$po['customer_name']}' sedang dikerjakan oleh '{$po['technician_name']}'\n";
                }
            } else {
                $systemPrompt .= "    - Belum ada pesanan yang sedang diproses.\n";
            }
            $systemPrompt .= "  * Ditugaskan ke Teknisi (Assigned) (" . ($dbStats['count_assigned'] ?? 0) . " pesanan):\n";
            if (!empty($dbStats['orders_assigned'])) {
                foreach ($dbStats['orders_assigned'] as $ao) {
                    $systemPrompt .= "    - #{$ao['id']}: Layanan '{$ao['service_name']}' untuk Pelanggan '{$ao['customer_name']}' ditugaskan ke '{$ao['technician_name']}'\n";
                }
            }
            $systemPrompt .= "  * Menunggu Konfirmasi (Pending): " . ($dbStats['count_pending'] ?? 0) . " pesanan\n";
            $systemPrompt .= "  * Selesai (Completed) (" . ($dbStats['count_completed'] ?? 0) . " pesanan):\n";
            if (!empty($dbStats['orders_completed'])) {
                foreach ($dbStats['orders_completed'] as $co) {
                    $systemPrompt .= "    - #{$co['id']}: Layanan '{$co['service_name']}' untuk Pelanggan '{$co['customer_name']}' selesai dikerjakan oleh '{$co['technician_name']}'\n";
                }
            }
            $systemPrompt .= "- Data Ulasan & Penilaian (Reviews) saat ini:\n";
            $systemPrompt .= "  * Total Ulasan: " . ($dbStats['total_reviews'] ?? 0) . " ulasan\n";
            if (!empty($dbStats['reviews_list'])) {
                foreach ($dbStats['reviews_list'] as $rv) {
                    $systemPrompt .= "    - Pelanggan {$rv['customer_name']} memberikan rating {$rv['rating']} bintang untuk layanan '{$rv['service_name']}' (Teknisi: {$rv['technician_name']}) dengan komentar: \"{$rv['comment']}\"\n";
                }
            } else {
                $systemPrompt .= "    - Belum ada ulasan yang masuk dari pelanggan.\n";
            }
            $systemPrompt .= "\n";

            $systemPrompt .= "ATURAN MENJAWAB SEPUTAR TEKNISI & STATUS AKTIF / NON-AKTIF:\n";
            $systemPrompt .= "- Jika pengguna menanyakan teknisi yang TIDAK AKTIF / NON-AKTIF (contoh: 'siapa teknisi yang tidak aktif', 'teknisi non aktif', 'siapa yang tidak aktif'): Jawab dengan tegas dan tepat bahwa teknisi yang berstatus Non-Aktif adalah {$techInactiveNames} ({$techInactiveCount} orang). Jangan pernah menyebut mereka siap membantu!\n";
            $systemPrompt .= "- Jika pengguna menanyakan teknisi yang AKTIF (contoh: 'siapa teknisi yang aktif', 'teknisi aktif'): Sebutkan bahwa teknisi yang Aktif adalah {$techActiveNames} ({$techActiveCount} orang), dan merekalah yang siap melayani pesanan.\n";
            $systemPrompt .= "- Jika pengguna menanyakan teknisi secara umum (contoh: 'ada berapa teknisi', 'siapa saja teknisi'): Sebutkan total {$techTotal} teknisi dengan membedakan statusnya secara jelas: Aktif ({$techActiveNames}) dan Non-Aktif ({$techInactiveNames}).\n";
            $systemPrompt .= "- Jika pengguna menanyakan status seseorang (misal Dina, Agus, Jono): Jawab sesuai data statusnya ({$techStatusDetail}).\n\n";

            $systemPrompt .= "ATURAN MENJAWAB JUMLAH & DAFTAR PENGGUNA SISTEM:\n";
            $systemPrompt .= "1. JIKA PENGGUNA MENANYAKAN BERAPA JUMLAH / TOTAL PENGGUNA (contoh: 'berapa total pengguna', 'berapa pengguna', 'ada berapa pengguna', 'jumlah pengguna', 'total user'):\n";
            $systemPrompt .= "   WAJIB JAWAB TOTALNYA SAJA SECARA SINGKAT DAN JELAS! Contoh: 'Saat ini terdapat total {$totalAll} pengguna yang terdaftar di dalam sistem ADCom.' JANGAN menyebutkan rincian nama atau peran kecuali jika ditanya 'siapa'!\n";
            $systemPrompt .= "2. JIKA PENGGUNA MENANYAKAN SIAPA / SIAPA SAJA / SEBUTKAN PENGGUNANYA (contoh: 'siapa total pengguna', 'siapa pengguna', 'siapa penggguna', 'siapa saja pengguna', 'siapa saja total pengguna', 'siapa saja penggunanya', 'sebutkan siapa saja penggunanya', 'daftar pengguna'):\n";
            $systemPrompt .= "   BARU SEBUTKAN RINCIAN DAN NAMA-NAMA SIAPA SAJA PENGGUNANYA SECARA LENGKAP untuk setiap peran:\n";
            $systemPrompt .= "   - Pelanggan (Customer): " . ($dbStats['total_customers'] ?? 0) . " orang (yaitu: " . ($dbStats['customer_names'] ?? '') . ")\n";
            $systemPrompt .= "   - Teknisi (Technician): {$techTotal} orang (Aktif: {$techActiveNames}" . ($techInactiveCount > 0 ? ", Non-Aktif: {$techInactiveNames}" : "") . ")\n";
            $systemPrompt .= "   - Operator: " . ($dbStats['total_operators'] ?? 0) . " orang (yaitu: " . ($dbStats['operator_names'] ?? '') . ")\n";
            $systemPrompt .= "   - Admin: " . ($dbStats['total_admins'] ?? 0) . " orang (yaitu: " . ($dbStats['admin_names'] ?? '') . ")\n";
            $systemPrompt .= "   Pastikan semua nama pengguna disebutkan secara jelas dan lengkap jika ditanya 'siapa'!\n\n";

            $systemPrompt .= "ATURAN MENJAWAB STATUS PESANAN / PEKERJAAN (PENTING UNTUK OPERATOR):\n";
            $systemPrompt .= "- Jika pengguna bertanya 'pesanan siapa saja yang sedang dikerjakan', 'siapa yang sedang dikerjakan', 'pesanan yang sedang diproses', atau pekerjaan aktif:\n";
            $systemPrompt .= "  WAJIB JAWAB SESUAI DATA STATUS 'PROCESSING' DI ATAS! Sebutkan nama pelanggan, layanan, dan teknisi yang mengerjakannya.\n";
            $systemPrompt .= "  Sebutkan juga pesanan berstatus 'Assigned' yang siap dikerjakan jika ada.\n";
            $systemPrompt .= "  JANGAN PERNAH MENAMPILKAN DAFTAR KATALOG JASA jika ditanya mengenai pesanan yang sedang dikerjakan!\n\n";

            $systemPrompt .= "ATURAN MENJAWAB ULASAN & PENILAIAN / RATING (REVIEWS):\n";
            $systemPrompt .= "- Jika pengguna/operator bertanya 'siapa yang sudah memberi ulasan', 'siapa yang memberi rating', 'daftar ulasan', 'review customer', atau testimoni:\n";
            $systemPrompt .= "  WAJIB JAWAB SESUAI DATA ULASAN DI ATAS! Sebutkan nama pelanggan yang telah memberi ulasan ({$dbStats['reviewers_names']}), bintang rating yang diberikan, layanan yang dinilai, dan isi komentarnya secara lengkap dan ramah.\n";
        }
        $systemPrompt .= "ATURAN UTAMA MENJAWAB LAYANAN/JASA:\n";
        $systemPrompt .= "1. Jika pengguna menanyakan 'ada jasa apa saja', 'jasa apa saja', 'layanan apa saja', 'daftar jasa', atau paket layanan yang ada: WAJIB JAWAB SESUAI DATA layanan yang tercantum di bawah ini secara lengkap, sebutkan nama paket layanan, kategori, estimasi biaya, estimasi waktu pengerjaan, dan deskripsinya secara rapi dan ramah.\n";
        $systemPrompt .= "2. Jika pengguna menanyakan harga, biaya, atau konsultasi masalah teknis, selalu hubungkan dengan rekomendasi layanan yang sesuai dari data layanan kami di bawah.\n";
        $systemPrompt .= "3. ALUR / CARA PEMESANAN: Jika pengguna menanyakan cara memesan, cara order, alur pemesanan, atau ingin memesan jasa (contoh: 'bagaimana cara ingin pesan jasanya', 'bagaimana pesan layanan/jasa', 'alur pemesanan', 'cara pesan'): WAJIB JELASKAN LANGKAH-LANGKAH PEMESANAN DENGAN JELAS:\n";
        $systemPrompt .= "   - Masuk ke menu Pesanan atau tekan tombol 'Buat Pesanan Baru' di dashboard Customer.\n";
        $systemPrompt .= "   - Pilih layanan jasa yang dibutuhkan dan isi keluhan teknis secara detail beserta alamat.\n";
        $systemPrompt .= "   - Operator akan memverifikasi pesanan dan menugaskan teknisi aktif.\n";
        $systemPrompt .= "   - Teknisi datang ke lokasi sesuai jadwal untuk survei dan pengerjaan.\n";
        $systemPrompt .= "   - Selesai, pembayaran, dan pelanggan memberikan ulasan/review.\n";
        $systemPrompt .= "   JANGAN hanya menampilkan daftar katalog jasa jika pengguna menanyakan cara atau alur pemesanan!\n";
        $systemPrompt .= "4. TAWAR MENAWAR HARGA / DISKON / NEGO: Jika pelanggan menawar harga (contoh: 'harganya bisa kurang tidak', 'bisa kurang', 'bisa nego', 'ada diskon', 'bisa tawar', 'kemahalan'): JANGAN PERNAH langsung menampilkan daftar katalog jasa secara mentah! Jelaskan dengan ramah bahwa harga tercantum adalah estimasi standar yang sudah sangat terjangkau khas unit bisnis siswa SMK. Namun biaya akhir sangat fleksibel tergantung tingkat kerusakan setelah dicek/disurvei teknisi secara langsung (bisa lebih murah jika ringan), serta tersedia harga khusus/diskon bundling jika memesan lebih dari 1 unit. Ajak untuk menjadwalkan pengecekan atau konsultasi dengan operator.\n";
        $systemPrompt .= "5. HANYA jika pertanyaan benar-benar di luar konteks layanan dan di luar keahlian teknis kami (misal: minta resep masakan, koding/pemrograman umum di luar jasa teknis, politik, puisi, dongeng), barulah jawab persis: 'Maaf, saya tidak dapat membantu jawab pertanyaan anda. Saya assistant AI yang bertugas membantu seputar layanan jasa.'\n\n";
        $systemPrompt .= "Berikut adalah seluruh data layanan jasa aktif kami di database:\n";
        if (!empty($servicesData)) {
            $no = 1;
            foreach ($servicesData as $service) {
                $price = isset($service['estimated_price']) && is_numeric($service['estimated_price']) ? 'Rp ' . number_format($service['estimated_price'], 0, ',', '.') : ($service['estimated_price'] ?? 'Harga menyusul');
                $cat = !empty($service['category_name']) ? " [Kategori: {$service['category_name']}]" : '';
                $time = !empty($service['estimated_time']) ? " [Estimasi Waktu: {$service['estimated_time']}]" : '';
                $systemPrompt .= "{$no}. {$service['name']} ({$price}){$cat}{$time}: {$service['description']}\n";
                $no++;
            }
        }

        // Format histori pesan
        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] == 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]]
            ];
        }

        $temperature = isset($settings['ai_temperature']) && is_numeric($settings['ai_temperature']) ? (float)$settings['ai_temperature'] : 0.7;
        $maxOutputTokens = isset($settings['ai_max_tokens']) && is_numeric($settings['ai_max_tokens']) ? (int)$settings['ai_max_tokens'] : 800;
        $timeout = isset($settings['ai_timeout']) && is_numeric($settings['ai_timeout']) ? (int)$settings['ai_timeout'] : 15;

        $data = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxOutputTokens
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }
        }
        
        // Jika API gagal atau gangguan (misal 429 quota limit / 503)
        $fallbackFaq = isset($settings['ai_fallback_faq']) ? (int)$settings['ai_fallback_faq'] : 1;
        if ($fallbackFaq === 0) {
            return "Mohon maaf, layanan AI sedang mengalami kendala jaringan atau batas kuota terlampaui. Mode FAQ Fallback saat ini dinonaktifkan oleh administrator. Silakan hubungi kami melalui kontak atau form pemesanan layanan.";
        }
        return self::getMockResponse($messages, $servicesData, $userName, $dbStats, $userRole);
    }

    public static function formatAllServices($servicesData, $intro = "Berikut adalah daftar layanan jasa yang tersedia di ADCom sesuai data kami:", $isStaff = false) {
        if (empty($servicesData)) {
            return "Saat ini belum ada data layanan jasa yang aktif di sistem kami.";
        }

        if ($isStaff) {
            $intro = "Daftar layanan jasa aktif di ADCom:";
        }

        $response = $intro . "\n\n";
        $no = 1;
        foreach ($servicesData as $service) {
            $name = $service['name'] ?? 'Layanan';
            $price = isset($service['estimated_price']) && is_numeric($service['estimated_price']) 
                ? 'Rp ' . number_format($service['estimated_price'], 0, ',', '.') 
                : ($service['estimated_price'] ?? 'Harga menyusul');
            $cat = !empty($service['category_name']) ? " (Kategori: {$service['category_name']})" : '';
            $time = !empty($service['estimated_time']) ? " | Estimasi: {$service['estimated_time']}" : '';
            $desc = !empty($service['description']) ? trim($service['description']) : '';

            $response .= "{$no}. **{$name}**{$cat}\n";
            $response .= "   - Biaya: {$price}{$time}\n";
            if (!empty($desc)) {
                $response .= "   - Deskripsi: {$desc}\n";
            }
            $response .= "\n";
            $no++;
        }

        if (!$isStaff) {
            $response .= "Apakah ada layanan jasa di atas yang ingin Anda pesan atau tanyakan lebih lanjut?";
        }
        return trim($response);
    }

    public static function formatOrdersResponse($dbStats, $type = 'processing', $isStaff = false) {
        $processing = $dbStats['orders_processing'] ?? [];
        $assigned = $dbStats['orders_assigned'] ?? [];
        $pending = $dbStats['orders_pending'] ?? [];
        $completed = $dbStats['orders_completed'] ?? [];
        $total = $dbStats['total_orders'] ?? 0;

        if ($type === 'processing') {
            $response = $isStaff 
                ? "Daftar pesanan yang sedang berlangsung:\n\n"
                : "Berdasarkan data sistem ADCom saat ini, berikut rincian pesanan yang sedang berlangsung:\n\n";
            
            // 1. Sedang dikerjakan (Processing)
            $response .= "🔧 **Pesanan yang Sedang Dikerjakan (Status: Processing):**\n";
            if (!empty($processing)) {
                $no = 1;
                foreach ($processing as $ord) {
                    $tech = !empty($ord['technician_name']) ? $ord['technician_name'] : 'Belum ditentukan';
                    $response .= "{$no}. **{$ord['service_name']}**\n";
                    $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                    $response .= "   - **Teknisi yang Mengerjakan:** {$tech}\n";
                    if (!empty($ord['problem_description'])) {
                        $response .= "   - **Kendala:** " . trim($ord['problem_description']) . "\n";
                    }
                    $response .= "\n";
                    $no++;
                }
            } else {
                $response .= "   *(Saat ini tidak ada pesanan yang berstatus Sedang Dikerjakan / Processing)*\n\n";
            }

            // 2. Ditugaskan (Assigned)
            if (!empty($assigned)) {
                $response .= "📌 **Pesanan yang Sudah Ditugaskan ke Teknisi (Status: Assigned):**\n";
                $no = 1;
                foreach ($assigned as $ord) {
                    $tech = !empty($ord['technician_name']) ? $ord['technician_name'] : 'Belum ditentukan';
                    $response .= "{$no}. **{$ord['service_name']}**\n";
                    $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                    $response .= "   - **Teknisi Bertugas:** {$tech}\n";
                    $response .= "\n";
                    $no++;
                }
            }

            if (!empty($pending)) {
                $response .= "⏳ **Pesanan Baru Menunggu Penugasan (Pending):** " . count($pending) . " pesanan\n\n";
            }

            if (!$isStaff) {
                $response .= "Sebagai Operator, Anda dapat memantau atau mengelola seluruh pesanan ini di halaman **Manajemen Pekerjaan (Order)**.";
            }
            return trim($response);
        }

        if ($type === 'assigned') {
            $response = "Berikut daftar pesanan yang **sudah ditugaskan ke teknisi (Status: Assigned)**:\n\n";
            if (!empty($assigned)) {
                $no = 1;
                foreach ($assigned as $ord) {
                    $tech = !empty($ord['technician_name']) ? $ord['technician_name'] : 'Belum ditentukan';
                    $response .= "{$no}. **{$ord['service_name']}**\n";
                    $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                    $response .= "   - **Teknisi Bertugas:** {$tech}\n";
                    $response .= "\n";
                    $no++;
                }
            } else {
                $response .= "*(Belum ada pesanan dengan status Assigned)*\n";
            }
            return trim($response);
        }

        if ($type === 'completed') {
            $response = "Berikut data pesanan yang **telah selesai dikerjakan (Status: Completed)**:\n\n";
            if (!empty($completed)) {
                $no = 1;
                foreach ($completed as $ord) {
                    $tech = !empty($ord['technician_name']) ? $ord['technician_name'] : '-';
                    $response .= "{$no}. **{$ord['service_name']}**\n";
                    $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                    $response .= "   - **Teknisi:** {$tech}\n";
                    $response .= "\n";
                    $no++;
                }
            } else {
                $response .= "*(Belum ada riwayat pesanan yang selesai)*\n";
            }
            return trim($response);
        }

        // Default / All orders list
        $response = "Data pesanan sistem ADCom (Total **{$total} pesanan**):\n\n";
        
        $response .= "1. **Sedang Dikerjakan (Processing) - " . count($processing) . " pesanan:**\n";
        if (!empty($processing)) {
            foreach ($processing as $ord) {
                $response .= "   - **{$ord['service_name']}** (Pelanggan: {$ord['customer_name']} | Teknisi: {$ord['technician_name']})\n";
            }
        } else {
            $response .= "   *(Tidak ada)*\n";
        }
        $response .= "\n";

        $response .= "2. **Sudah Ditugaskan (Assigned) - " . count($assigned) . " pesanan:**\n";
        if (!empty($assigned)) {
            foreach ($assigned as $ord) {
                $response .= "   - **{$ord['service_name']}** (Pelanggan: {$ord['customer_name']} | Teknisi: {$ord['technician_name']})\n";
            }
        } else {
            $response .= "   *(Tidak ada)*\n";
        }
        $response .= "\n";

        $response .= "3. **Selesai (Completed) - " . count($completed) . " pesanan:**\n";
        if (!empty($completed)) {
            foreach ($completed as $ord) {
                $response .= "   - **{$ord['service_name']}** (Pelanggan: {$ord['customer_name']} | Teknisi: {$ord['technician_name']})\n";
            }
        } else {
            $response .= "   *(Tidak ada)*\n";
        }
        $response .= "\n";

        if (!empty($pending)) {
            $response .= "4. **Menunggu Konfirmasi (Pending) - " . count($pending) . " pesanan**\n\n";
        }

        if (!$isStaff) {
            $response .= "Apakah ada pesanan tertentu yang ingin Anda tanyakan lebih lanjut?";
        }
        return trim($response);
    }

    public static function formatReviewsResponse($dbStats, $specificCustomer = null, $isStaff = false) {
        $reviews = $dbStats['reviews_list'] ?? [];
        $total = $dbStats['total_reviews'] ?? count($reviews);
        $reviewers = $dbStats['reviewers_names'] ?? '';

        if (empty($reviews)) {
            return "Saat ini belum ada ulasan atau penilaian yang masuk dari pelanggan di sistem ADCom.";
        }

        if ($specificCustomer) {
            $filtered = [];
            foreach ($reviews as $rev) {
                if (stripos($rev['customer_name'], $specificCustomer) !== false) {
                    $filtered[] = $rev;
                }
            }
            if (!empty($filtered)) {
                $reviews = $filtered;
            }
        }

        $response = "";
        if (!empty($reviewers)) {
            $response .= $isStaff 
                ? "Pelanggan yang sudah memberikan ulasan: **{$reviewers}**.\n\nRincian ulasan & penilaian:\n\n"
                : "Berdasarkan data sistem ADCom, pelanggan yang sudah memberikan ulasan adalah **{$reviewers}**.\n\nBerikut rincian ulasan & penilaian (rating) yang tercatat:\n\n";
        } else {
            $response .= "Rincian ulasan & penilaian (rating) pelanggan di sistem ADCom:\n\n";
        }

        $no = 1;
        foreach ($reviews as $rev) {
            $stars = str_repeat('⭐', max(1, min(5, (int)$rev['rating'])));
            $tech = !empty($rev['technician_name']) ? $rev['technician_name'] : '-';
            $date = !empty($rev['created_at']) ? date('d M Y H:i', strtotime($rev['created_at'])) : '';
            $dateStr = $date ? " ({$date})" : '';

            $response .= "{$no}. **{$rev['customer_name']}**{$dateStr}\n";
            $response .= "   - **Layanan:** {$rev['service_name']}\n";
            $response .= "   - **Penilaian:** {$stars} ({$rev['rating']}/5)\n";
            $response .= "   - **Teknisi yang Mengerjakan:** {$tech}\n";
            $response .= "   - **Komentar:** \"{$rev['comment']}\"\n\n";
            $no++;
        }

        if (!$isStaff) {
            $response .= "Sebagai Operator, Anda juga dapat memantau seluruh ulasan ini di menu **Semua Ulasan & Penilaian**.";
        }
        return trim($response);
    }

    private static function getMockResponse($messages, $servicesData, $userName = 'Pelanggan', $dbStats = [], $userRole = null) {
        // Ambil pesan terakhir user
        $lastUserMessage = '';
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if ($messages[$i]['role'] == 'user') {
                $lastUserMessage = trim(strtolower($messages[$i]['content']));
                break;
            }
        }

        // Normalisasi kata: perbaiki typo pengulangan huruf (misal: "pekerjaaan" -> "pekerjaan", "adakahhh" -> "adakah")
        $cleanMessage = preg_replace('/a{3,}/', 'aa', $lastUserMessage);
        $cleanMessage = preg_replace('/([^a])\1{2,}/', '$1', $cleanMessage);

        // Deteksi apakah pengguna yang sedang chat adalah Teknisi
        $isTechnician = false;
        $matchedTechName = null;
        if ($userRole === 'technician' || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'technician')) {
            $isTechnician = true;
            $matchedTechName = $userName;
        } elseif (!empty($dbStats['technicians_list']) && is_array($dbStats['technicians_list'])) {
            foreach ($dbStats['technicians_list'] as $t) {
                if (stripos($t['name'], $userName) !== false || stripos($userName, $t['name']) !== false) {
                    $isTechnician = true;
                    $matchedTechName = $t['name'];
                    break;
                }
            }
        }

        $isStaff = in_array($userRole, ['admin', 'operator', 'technician']);

        // =============================================================
        // 0.0. CEK PERTANYAAN TUGAS / PEKERJAAN YANG HARUS DISELESAIKAN (JOB INQUIRY)
        // =============================================================
        $isJobInquiry = (
            strpos($cleanMessage, 'siapa') === false &&
            (strpos($cleanMessage, 'pekerjaan') !== false || strpos($cleanMessage, 'kerjaan') !== false || strpos($cleanMessage, 'tugas') !== false || strpos($cleanMessage, 'order') !== false || strpos($cleanMessage, 'pesanan') !== false || strpos($cleanMessage, 'job') !== false) &&
            (
                strpos($cleanMessage, 'diselesaikan') !== false ||
                strpos($cleanMessage, 'dikerjakan') !== false ||
                strpos($cleanMessage, 'selesai') !== false ||
                strpos($cleanMessage, 'harus') !== false ||
                strpos($cleanMessage, 'perlu') !== false ||
                strpos($cleanMessage, 'untuk saya') !== false ||
                strpos($cleanMessage, 'saya') !== false ||
                strpos($cleanMessage, 'aktif') !== false ||
                strpos($cleanMessage, 'hari ini') !== false ||
                strpos($cleanMessage, 'ada') !== false ||
                strpos($cleanMessage, 'apakah') !== false
            )
        );

        if ($isJobInquiry && strpos($cleanMessage, 'bagaimana') === false && strpos($cleanMessage, 'cara') === false) {
            // Jika yang bertanya adalah TEKNISI (misal: Agus Teknisi, Jono Teknisi, dsb)
            if ($isTechnician) {
                $myActive = [];
                $myCompleted = [];
                $tSearch = strtolower($matchedTechName ?: $userName);
                $tFirst = explode(' ', $tSearch)[0];

                if (!empty($dbStats['orders_list']) && is_array($dbStats['orders_list'])) {
                    foreach ($dbStats['orders_list'] as $o) {
                        $oTech = strtolower($o['technician_name'] ?? '');
                        if ($oTech === $tSearch || (strlen($tFirst) >= 3 && strpos($oTech, $tFirst) !== false)) {
                            $st = strtolower($o['status'] ?? '');
                            if ($st === 'assigned' || $st === 'processing') {
                                $myActive[] = $o;
                            } elseif ($st === 'completed') {
                                $myCompleted[] = $o;
                            }
                        }
                    }
                }

                if (empty($myActive)) {
                    if ($isStaff) {
                        $response = "Saat ini tidak ada tugas atau pekerjaan aktif yang harus Anda selesaikan.\n";
                        if (!empty($myCompleted)) {
                            $response .= "Riwayat: " . count($myCompleted) . " pekerjaan telah selesai dikerjakan.";
                        }
                        return trim($response);
                    }
                    $response = "Halo Bapak/Ibu **{$userName}**! Saat ini **tidak ada tugas atau pekerjaan aktif** yang harus Anda selesaikan.\n\n";
                    if (!empty($myCompleted)) {
                        $response .= "Anda memiliki riwayat **" . count($myCompleted) . " pekerjaan yang telah selesai** dikerjakan.\n";
                    } else {
                        $response .= "Belum ada pesanan yang ditugaskan kepada Anda oleh Operator saat ini.\n";
                    }
                    $response .= "Anda dapat terus memantau halaman **Dashboard Teknisi** jika sewaktu-waktu ada tugas baru yang ditugaskan.";
                    return $response;
                }

                if ($isStaff) {
                    $response = "Terdapat " . count($myActive) . " tugas aktif yang harus diselesaikan:\n\n";
                    $no = 1;
                    foreach ($myActive as $ord) {
                        $st = $ord['status'] ?? 'assigned';
                        $stBadge = strtoupper($st);
                        $stDesc = ($st === 'processing') ? 'Sedang Dikerjakan' : 'Sudah Ditugaskan';
                        $response .= "{$no}. **{$ord['service_name']}** (Order #{$ord['id']})\n";
                        $response .= "   - Pelanggan: {$ord['customer_name']}\n";
                        $response .= "   - Status: {$stBadge} ({$stDesc})\n";
                        if (!empty($ord['problem_description'])) {
                            $response .= "   - Kendala: " . trim($ord['problem_description']) . "\n";
                        }
                        $response .= "\n";
                        $no++;
                    }
                    return trim($response);
                }

                $response = "Halo Bapak/Ibu **{$userName}**! Saat ini Anda memiliki **" . count($myActive) . " tugas aktif** yang harus diselesaikan:\n\n";
                $no = 1;
                foreach ($myActive as $ord) {
                    $st = $ord['status'] ?? 'assigned';
                    $stBadge = strtoupper($st);
                    $stDesc = ($st === 'processing') ? 'Sedang Dikerjakan' : 'Sudah Ditugaskan';
                    $response .= "{$no}. **{$ord['service_name']}** (Order #{$ord['id']})\n";
                    $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                    $response .= "   - **Status:** **{$stBadge}** ({$stDesc})\n";
                    if (!empty($ord['problem_description'])) {
                        $response .= "   - **Kendala:** " . trim($ord['problem_description']) . "\n";
                    }
                    $response .= "\n";
                    $no++;
                }
                $response .= "Silakan buka menu **Tugas Aktif** di dashboard Anda untuk melihat detail dan memperbarui status pengerjaan.";
                return $response;
            }

            // Jika yang bertanya bukan Teknisi (misal Operator, Admin, atau Customer)
            $activeOrders = array_merge($dbStats['orders_processing'] ?? [], $dbStats['orders_assigned'] ?? []);
            if (empty($activeOrders)) {
                return "Saat ini **tidak ada pekerjaan aktif yang harus diselesaikan** di sistem ADCom. Seluruh pesanan telah berstatus selesai (Completed) atau belum ada pesanan baru yang masuk.\n\nTotal riwayat pesanan yang telah selesai: **" . ($dbStats['count_completed'] ?? 0) . " pesanan**.";
            }

            $response = "Saat ini terdapat **" . count($activeOrders) . " pekerjaan aktif** yang harus diselesaikan oleh teknisi:\n\n";
            $no = 1;
            foreach ($activeOrders as $ord) {
                $tech = !empty($ord['technician_name']) ? $ord['technician_name'] : 'Belum ditentukan';
                $st = $ord['status'] ?? 'assigned';
                $stBadge = strtoupper($st);
                $response .= "{$no}. **{$ord['service_name']}** (Order #{$ord['id']})\n";
                $response .= "   - **Pelanggan:** {$ord['customer_name']}\n";
                $response .= "   - **Teknisi:** {$tech}\n";
                $response .= "   - **Status:** **{$stBadge}**\n\n";
                $no++;
            }
            if (!$isStaff) {
                $response .= "Operator dapat memantau dan mengelola seluruh pekerjaan ini di menu **Manajemen Pekerjaan**.";
            }
            return trim($response);
        }

        // =============================================================
        // 0.A. CEK PERTANYAAN SEPUTAR ULASAN & PENILAIAN / RATING (REVIEWS)
        // =============================================================
        $isAskingReviews = (
            strpos($lastUserMessage, 'ulasan') !== false ||
            strpos($lastUserMessage, 'review') !== false ||
            strpos($lastUserMessage, 'rating') !== false ||
            strpos($lastUserMessage, 'bintang') !== false ||
            strpos($lastUserMessage, 'penilaian') !== false ||
            strpos($lastUserMessage, 'testimoni') !== false ||
            strpos($lastUserMessage, 'komentar customer') !== false ||
            strpos($lastUserMessage, 'komentar pelanggan') !== false ||
            strpos($lastUserMessage, 'komentar ulasan') !== false
        );

        if ($isAskingReviews) {
            // Cek jika menanyakan ulasan pelanggan tertentu (misal: "ulasan dini", "review dini rahma")
            $matchedCustomer = null;
            if (!empty($dbStats['reviews_list'])) {
                foreach ($dbStats['reviews_list'] as $rItem) {
                    $cName = strtolower($rItem['customer_name'] ?? '');
                    $cParts = explode(' ', $cName);
                    $cFirst = $cParts[0];
                    if (strpos($lastUserMessage, $cName) !== false || (strlen($cFirst) >= 3 && strpos($lastUserMessage, $cFirst) !== false)) {
                        $matchedCustomer = $rItem['customer_name'];
                        break;
                    }
                }
            }

            // Jika khusus menanyakan jumlah / berapa
            if (strpos($lastUserMessage, 'berapa') !== false || strpos($lastUserMessage, 'jumlah') !== false || strpos($lastUserMessage, 'total') !== false) {
                $totalRev = $dbStats['total_reviews'] ?? 0;
                $names = $dbStats['reviewers_names'] ?? '';
                if ($totalRev > 0) {
                    if ($isStaff) {
                        return "Terdapat **{$totalRev} ulasan** dari pelanggan: **{$names}**.";
                    }
                    return "Saat ini terdapat **{$totalRev} ulasan** yang telah masuk di sistem ADCom dari pelanggan: **{$names}**.\n\nKetik 'siapa yang sudah memberi ulasan' atau 'daftar ulasan' untuk melihat komentar dan rating lengkapnya.";
                } else {
                    return "Saat ini belum ada ulasan atau penilaian yang masuk dari pelanggan di sistem ADCom.";
                }
            }

            return self::formatReviewsResponse($dbStats, $matchedCustomer, $isStaff);
        }

        // =============================================================
        // 0.B. CEK PERTANYAAN SEPUTAR STATUS PESANAN / PEKERJAAN (ORDERS)
        // =============================================================

        // A. Pesanan yang sedang dikerjakan / diproses / aktif
        $isAskingProcessingOrders = (
            strpos($lastUserMessage, 'sedang dikerjakan') !== false ||
            strpos($lastUserMessage, 'lagi dikerjakan') !== false ||
            strpos($lastUserMessage, 'yang dikerjakan') !== false ||
            strpos($lastUserMessage, 'sedang diproses') !== false ||
            strpos($lastUserMessage, 'lagi diproses') !== false ||
            strpos($lastUserMessage, 'yang diproses') !== false ||
            strpos($lastUserMessage, 'pesanan aktif') !== false ||
            strpos($lastUserMessage, 'pekerjaan aktif') !== false ||
            strpos($lastUserMessage, 'order aktif') !== false ||
            strpos($lastUserMessage, 'tugas aktif') !== false ||
            strpos($lastUserMessage, 'siapa saja yang sedang dikerjakan') !== false ||
            strpos($lastUserMessage, 'pesanan siapa saja yang sedang dikerjakan') !== false ||
            strpos($lastUserMessage, 'siapa yang sedang dikerjakan') !== false ||
            strpos($lastUserMessage, 'siapa yg sedang dikerjakan') !== false ||
            strpos($lastUserMessage, 'siapa yang dikerjakan') !== false ||
            strpos($lastUserMessage, 'siapa yg dikerjakan') !== false ||
            strpos($lastUserMessage, 'progres pengerjaan') !== false ||
            strpos($lastUserMessage, 'progress pengerjaan') !== false ||
            (strpos($lastUserMessage, 'dikerjakan') !== false && (strpos($lastUserMessage, 'siapa') !== false || strpos($lastUserMessage, 'pesanan') !== false || strpos($lastUserMessage, 'order') !== false || strpos($lastUserMessage, 'apa') !== false || strpos($lastUserMessage, 'mana') !== false || strpos($lastUserMessage, 'ada') !== false)) ||
            (strpos($lastUserMessage, 'diproses') !== false && (strpos($lastUserMessage, 'siapa') !== false || strpos($lastUserMessage, 'pesanan') !== false || strpos($lastUserMessage, 'order') !== false || strpos($lastUserMessage, 'apa') !== false || strpos($lastUserMessage, 'mana') !== false || strpos($lastUserMessage, 'ada') !== false))
        );

        if ($isAskingProcessingOrders) {
            return self::formatOrdersResponse($dbStats, 'processing', $isStaff);
        }

        // B. Pesanan yang sudah selesai (Completed)
        $isAskingCompletedOrders = (
            strpos($lastUserMessage, 'pesanan selesai') !== false ||
            strpos($lastUserMessage, 'pesanan yang selesai') !== false ||
            strpos($lastUserMessage, 'pesanan yg selesai') !== false ||
            strpos($lastUserMessage, 'pesanan yang sudah selesai') !== false ||
            strpos($lastUserMessage, 'pesanan yg sudah selesai') !== false ||
            strpos($lastUserMessage, 'pekerjaan selesai') !== false ||
            strpos($lastUserMessage, 'pekerjaan yang selesai') !== false ||
            strpos($lastUserMessage, 'order selesai') !== false ||
            strpos($lastUserMessage, 'order yang selesai') !== false
        );

        if ($isAskingCompletedOrders) {
            return self::formatOrdersResponse($dbStats, 'completed', $isStaff);
        }

        // C. Pesanan yang ditugaskan (Assigned)
        $isAskingAssignedOrders = (
            strpos($lastUserMessage, 'pesanan ditugaskan') !== false ||
            strpos($lastUserMessage, 'pesanan yang ditugaskan') !== false ||
            strpos($lastUserMessage, 'pesanan yg ditugaskan') !== false ||
            strpos($lastUserMessage, 'pekerjaan ditugaskan') !== false ||
            strpos($lastUserMessage, 'pekerjaan yang ditugaskan') !== false ||
            strpos($lastUserMessage, 'tugas yang ditugaskan') !== false
        );

        if ($isAskingAssignedOrders) {
            return self::formatOrdersResponse($dbStats, 'assigned', $isStaff);
        }

        // D. Daftar seluruh pesanan / siapa saja yang pesan / status pesanan
        $isAskingOrderList = (
            strpos($lastUserMessage, 'pesanan siapa saja') !== false ||
            strpos($lastUserMessage, 'pesanan siapa') !== false ||
            strpos($lastUserMessage, 'siapa saja yang pesan') !== false ||
            strpos($lastUserMessage, 'siapa yang pesan') !== false ||
            strpos($lastUserMessage, 'siapa yg pesan') !== false ||
            strpos($lastUserMessage, 'siapa saja yang order') !== false ||
            strpos($lastUserMessage, 'siapa yang order') !== false ||
            strpos($lastUserMessage, 'siapa yg order') !== false ||
            strpos($lastUserMessage, 'daftar pesanan') !== false ||
            strpos($lastUserMessage, 'list pesanan') !== false ||
            strpos($lastUserMessage, 'semua pesanan') !== false ||
            strpos($lastUserMessage, 'status pesanan') !== false ||
            strpos($lastUserMessage, 'cek pesanan') !== false ||
            strpos($lastUserMessage, 'rekap pesanan') !== false ||
            strpos($lastUserMessage, 'ada pesanan apa saja') !== false ||
            strpos($lastUserMessage, 'ada pesanan apa aja') !== false ||
            strpos($lastUserMessage, 'data pesanan') !== false
        );

        if ($isAskingOrderList) {
            return self::formatOrdersResponse($dbStats, 'all', $isStaff);
        }

        // E. Cek jika menanyakan pesanan pelanggan tertentu (misal: "pesanan dini rahma", "pesanan ahmad", "pesanan rini")
        if (!empty($dbStats['orders_list']) && is_array($dbStats['orders_list'])) {
            foreach ($dbStats['orders_list'] as $ord) {
                $cName = strtolower($ord['customer_name'] ?? '');
                $cParts = explode(' ', $cName);
                $cFirst = $cParts[0];

                $isCustomerMentioned = (strpos($lastUserMessage, $cName) !== false || (strlen($cFirst) >= 3 && strpos($lastUserMessage, $cFirst) !== false));
                if ($isCustomerMentioned && (
                    strpos($lastUserMessage, 'pesan') !== false || 
                    strpos($lastUserMessage, 'order') !== false || 
                    strpos($lastUserMessage, 'status') !== false || 
                    strpos($lastUserMessage, 'servis') !== false || 
                    strpos($lastUserMessage, 'service') !== false
                )) {
                    $statusBadge = strtoupper($ord['status']);
                    $statusDesc = '';
                    if ($ord['status'] == 'processing') $statusDesc = 'Sedang dikerjakan oleh teknisi';
                    elseif ($ord['status'] == 'assigned') $statusDesc = 'Sudah ditugaskan ke teknisi';
                    elseif ($ord['status'] == 'pending') $statusDesc = 'Menunggu penugasan teknisi';
                    elseif ($ord['status'] == 'completed') $statusDesc = 'Sudah selesai dikerjakan';
                    elseif ($ord['status'] == 'cancelled') $statusDesc = 'Dibatalkan';

                    $response = "Berikut data pesanan untuk pelanggan **{$ord['customer_name']}**:\n\n";
                    $response .= "- **Nomor Pesanan:** #{$ord['id']}\n";
                    $response .= "- **Layanan:** {$ord['service_name']}\n";
                    $response .= "- **Status:** **{$statusBadge}** ({$statusDesc})\n";
                    $response .= "- **Teknisi:** " . ($ord['technician_name'] ?: 'Belum ditugaskan') . "\n";
                    if (!empty($ord['problem_description'])) {
                        $response .= "- **Deskripsi Kendala:** " . trim($ord['problem_description']) . "\n";
                    }
                    return $response;
                }
            }
        }

        // F. Cek jika menanyakan tugas/pekerjaan teknisi tertentu (misal: "tugas jono", "pekerjaan agus", "tugas dina")
        if (!empty($dbStats['technicians_list']) && is_array($dbStats['technicians_list'])) {
            foreach ($dbStats['technicians_list'] as $tech) {
                $tName = strtolower($tech['name']);
                $tParts = explode(' ', $tName);
                $tFirst = $tParts[0];

                $isTechMentioned = (strpos($lastUserMessage, $tName) !== false || (strlen($tFirst) >= 3 && strpos($lastUserMessage, $tFirst) !== false));
                if ($isTechMentioned && (
                    strpos($lastUserMessage, 'tugas') !== false || 
                    strpos($lastUserMessage, 'pekerjaan') !== false || 
                    strpos($lastUserMessage, 'kerjaan') !== false || 
                    strpos($lastUserMessage, 'job') !== false || 
                    strpos($lastUserMessage, 'dikerjakan') !== false
                )) {
                    $techOrders = [];
                    if (!empty($dbStats['orders_list'])) {
                        foreach ($dbStats['orders_list'] as $o) {
                            if (strtolower($o['technician_name'] ?? '') === $tName || strpos(strtolower($o['technician_name'] ?? ''), $tFirst) !== false) {
                                $techOrders[] = $o;
                            }
                        }
                    }

                    if (empty($techOrders)) {
                        return "Teknisi **{$tech['name']}** saat ini belum memiliki tugas yang aktif.";
                    }

                    $response = "Berikut daftar tugas/pekerjaan untuk teknisi **{$tech['name']}**:\n\n";
                    $no = 1;
                    foreach ($techOrders as $to) {
                        $stBadge = strtoupper($to['status']);
                        $response .= "{$no}. **{$to['service_name']}** (Pelanggan: {$to['customer_name']})\n";
                        $response .= "   - Status: **{$stBadge}**\n";
                        $no++;
                    }
                    return $response;
                }
            }
        }

        // 1. Cek apakah menanyakan layanan spesifik berdasarkan data di database
        $matchedServices = [];
        $isGeneralListRequest = (
            strpos($lastUserMessage, 'apa saja') !== false ||
            strpos($lastUserMessage, 'apa aja') !== false ||
            strpos($lastUserMessage, 'semua') !== false ||
            strpos($lastUserMessage, 'daftar') !== false ||
            strpos($lastUserMessage, 'list') !== false ||
            strpos($lastUserMessage, 'sebutkan') !== false ||
            in_array($lastUserMessage, ['jasa', 'layanan', 'servis', 'service', 'jasanya', 'layanannya', 'ada jasa apa', 'jasa apa', 'layanan apa', 'ada layanan apa'])
        );

        if (!empty($servicesData)) {
            foreach ($servicesData as $service) {
                $sName = strtolower($service['name']);
                
                // Cek nama lengkap layanan
                if (strpos($lastUserMessage, $sName) !== false) {
                    $matchedServices[] = $service;
                    continue;
                }
                
                // Cek kata kunci spesifik layanan
                if (strpos($sName, 'cctv') !== false && (strpos($lastUserMessage, 'cctv') !== false || strpos($lastUserMessage, 'kamera') !== false)) {
                    $matchedServices[] = $service;
                } elseif (strpos($sName, 'mikrotik') !== false && (strpos($lastUserMessage, 'mikrotik') !== false || strpos($lastUserMessage, 'router') !== false)) {
                    $matchedServices[] = $service;
                } elseif (strpos($sName, 'lan') !== false && (preg_match('/\blan\b/i', $lastUserMessage) || strpos($lastUserMessage, 'kabel utp') !== false || strpos($lastUserMessage, 'kabel jaringan') !== false)) {
                    $matchedServices[] = $service;
                } elseif (strpos($sName, 'software') !== false && (strpos($lastUserMessage, 'software') !== false || strpos($lastUserMessage, 'aplikasi') !== false || strpos($lastUserMessage, 'windows') !== false || strpos($lastUserMessage, 'linux') !== false || strpos($lastUserMessage, 'office') !== false || strpos($lastUserMessage, 'install ulang') !== false || strpos($lastUserMessage, 'instal ulang') !== false)) {
                    $matchedServices[] = $service;
                } elseif (strpos($sName, 'laptop') !== false && strpos($lastUserMessage, 'laptop') !== false && strpos($lastUserMessage, 'komputer') === false) {
                    $matchedServices[] = $service;
                } elseif (strpos($sName, 'komputer') !== false && (strpos($lastUserMessage, 'komputer') !== false || preg_match('/\bpc\b/i', $lastUserMessage)) && strpos($lastUserMessage, 'laptop') === false) {
                    $matchedServices[] = $service;
                }
            }
        }

        // Jika ada layanan spesifik yang cocok dan user BUKAN meminta semua daftar secara umum
        if (!empty($matchedServices) && !$isGeneralListRequest) {
            $response = "Berikut informasi layanan jasa yang sesuai dengan pencarian Anda:\n\n";
            foreach ($matchedServices as $service) {
                $price = isset($service['estimated_price']) && is_numeric($service['estimated_price']) 
                    ? 'Rp ' . number_format($service['estimated_price'], 0, ',', '.') 
                    : ($service['estimated_price'] ?? 'Harga menyusul');
                $cat = !empty($service['category_name']) ? " (Kategori: {$service['category_name']})" : '';
                $time = !empty($service['estimated_time']) ? " | Estimasi Waktu: {$service['estimated_time']}" : '';
                $desc = !empty($service['description']) ? trim($service['description']) : '';

                $response .= "- **{$service['name']}**{$cat}\n";
                $response .= "  - Estimasi Biaya: {$price}{$time}\n";
                if (!empty($desc)) {
                    $response .= "  - Deskripsi: {$desc}\n";
                }
                $response .= "\n";
            }
            if (!$isStaff) {
                $response .= "Apakah Anda ingin melakukan pemesanan untuk layanan ini sekarang?";
            }
            return trim($response);
        }

        // 2. Alur Pemesanan & Panduan Cara Memesan Jasa (Diprioritaskan sebelum katalog layanan)
        $isAskingOrderingFlow = (
            strpos($lastUserMessage, 'cara pesan') !== false || 
            strpos($lastUserMessage, 'cara order') !== false || 
            strpos($lastUserMessage, 'cara memesan') !== false || 
            strpos($lastUserMessage, 'cara booking') !== false || 
            strpos($lastUserMessage, 'alur pemesanan') !== false || 
            strpos($lastUserMessage, 'alur pesan') !== false || 
            strpos($lastUserMessage, 'alur order') !== false || 
            strpos($lastUserMessage, 'langkah pemesanan') !== false || 
            strpos($lastUserMessage, 'langkah memesan') !== false || 
            strpos($lastUserMessage, 'langkah pesan') !== false || 
            strpos($lastUserMessage, 'prosedur pemesanan') !== false || 
            strpos($lastUserMessage, 'prosedur pesan') !== false || 
            strpos($lastUserMessage, 'tata cara pemesanan') !== false || 
            strpos($lastUserMessage, 'tata cara pesan') !== false || 
            strpos($lastUserMessage, 'bagaimana pesan') !== false || 
            strpos($lastUserMessage, 'gimana pesan') !== false || 
            strpos($lastUserMessage, 'ingin pesan') !== false || 
            strpos($lastUserMessage, 'mau pesan') !== false || 
            strpos($lastUserMessage, 'mau order') !== false ||
            (
                (strpos($lastUserMessage, 'pesan') !== false || strpos($lastUserMessage, 'memesan') !== false || strpos($lastUserMessage, 'order') !== false) && 
                (strpos($lastUserMessage, 'bagaimana') !== false || strpos($lastUserMessage, 'gimana') !== false || strpos($lastUserMessage, 'cara') !== false || strpos($lastUserMessage, 'alur') !== false || strpos($lastUserMessage, 'tahap') !== false || strpos($lastUserMessage, 'proses') !== false || strpos($lastUserMessage, 'ingin') !== false || strpos($lastUserMessage, 'mau') !== false)
            )
        );

        if ($isAskingOrderingFlow) {
            $response = "Alur dan cara pemesanan layanan jasa di ADCom sangat mudah:\n\n";
            $response .= "1. **Buka Menu Pesanan / Buat Pesanan Baru**:\n";
            $response .= "   - Jika Anda sudah login di dashboard **Customer**, Anda dapat langsung menekan tombol biru **'Buat Pesanan Baru'** di layar dashboard Anda atau membuka menu **Layanan Kami / Pesanan** di bilah navigasi atas.\n\n";
            $response .= "2. **Pilih Layanan & Isi Formulir Keluhan**:\n";
            $response .= "   - Pilih jenis jasa yang Anda perlukan (misal: Service Komputer, Service Laptop, CCTV, Jaringan LAN, atau Mikrotik).\n";
            $response .= "   - Masukkan alamat lokasi Anda dan jelaskan keluhan teknis yang dialami secara detail.\n\n";
            $response .= "3. **Penjadwalan & Verifikasi Operator**:\n";
            $response .= "   - Operator kami akan memeriksa pesanan Anda dan menugaskan teknisi ahli yang sedang berstatus aktif (seperti Agus Teknisi atau Jono Teknisi).\n\n";
            $response .= "4. **Kunjungan & Pengerjaan oleh Teknisi**:\n";
            $response .= "   - Teknisi kami akan datang ke lokasi Anda sesuai jadwal untuk melakukan survei dan perbaikan.\n\n";
            $response .= "5. **Selesai, Pembayaran & Ulasan**:\n";
            $response .= "   - Setelah pengerjaan selesai dengan baik, Anda dapat menyelesaikan pembayaran serta memberikan Rating & Ulasan (Review) pada akun Anda.\n\n";
            if (!$isStaff) {
                $response .= "💡 **Tips:** Anda saat ini berada di Dashboard Customer, cukup klik tombol biru **'Buat Pesanan Baru'** di layar Anda untuk langsung memulai pemesanan!";
            }
            return trim($response);
        }

        // 3. Cek jika menanyakan daftar/pilihan jasa atau layanan secara umum
        $isAskingServicesList = false;

        $servicePhrases = [
            'jasa apa', 'layanan apa', 'servis apa', 'service apa', 
            'ada jasa', 'ada layanan', 'ada servis', 'ada service',
            'daftar jasa', 'daftar layanan', 'list jasa', 'list layanan',
            'apa saja jasa', 'apa saja layanan', 'apa saja jasanya', 'apa saja layanannya',
            'jasa apa saja', 'layanan apa saja', 'jasa apa aja', 'layanan apa aja',
            'jasa yang ada', 'layanan yang ada', 'jasa yang tersedia', 'layanan yang tersedia',
            'paket jasa', 'paket layanan', 'pilihan jasa', 'pilihan layanan',
            'menu jasa', 'menu layanan', 'bisa servis apa', 'servis apa saja',
            'melayani apa', 'bisa apa saja', 'bisa perbaiki apa', 'perbaiki apa saja',
            'produk apa saja', 'kalian menyediakan apa', 'kalian melayani apa',
            'jasa kalian', 'layanan kalian', 'jasa di sini', 'layanan di sini',
            'semua jasa', 'semua layanan', 'jasa yang kamu', 'layanan yang kamu',
            'rekomendasi jasa', 'rekomendasi layanan', 'info jasa', 'info layanan'
        ];

        foreach ($servicePhrases as $phrase) {
            if (strpos($lastUserMessage, $phrase) !== false) {
                $isAskingServicesList = true;
                break;
            }
        }

        if (!$isAskingServicesList) {
            $hasJasaWord = (
                strpos($lastUserMessage, 'jasa') !== false || 
                strpos($lastUserMessage, 'layanan') !== false || 
                strpos($lastUserMessage, 'servis') !== false
            );
            $hasQueryWord = (
                strpos($lastUserMessage, 'apa') !== false || 
                strpos($lastUserMessage, 'ada') !== false || 
                strpos($lastUserMessage, 'daftar') !== false || 
                strpos($lastUserMessage, 'list') !== false || 
                strpos($lastUserMessage, 'sebutkan') !== false || 
                strpos($lastUserMessage, 'tersedia') !== false || 
                strpos($lastUserMessage, 'macam') !== false || 
                strpos($lastUserMessage, 'jenis') !== false || 
                strpos($lastUserMessage, 'pilihan') !== false ||
                preg_match('/\bmana\b/', $lastUserMessage) ||
                strpos($lastUserMessage, 'punya') !== false ||
                in_array($lastUserMessage, ['jasa', 'layanan', 'servis', 'service', 'jasanya', 'layanannya', 'jasa jasa', 'layanan layanan'])
            );
            if ($hasJasaWord && $hasQueryWord) {
                $isAskingServicesList = true;
            }
        }

        // 3.5. Cek tawar-menawar harga, nego, diskon, atau potongan biaya
        $isNegotiating = (
            strpos($cleanMessage, 'kurang') !== false ||
            strpos($cleanMessage, 'nego') !== false ||
            strpos($cleanMessage, 'tawar') !== false ||
            strpos($cleanMessage, 'diskon') !== false ||
            strpos($cleanMessage, 'potongan') !== false ||
            strpos($cleanMessage, 'promo') !== false ||
            strpos($cleanMessage, 'kemahalan') !== false ||
            strpos($cleanMessage, 'bisa murah') !== false ||
            strpos($cleanMessage, 'lebih murah') !== false ||
            strpos($cleanMessage, 'bisa turun') !== false ||
            strpos($cleanMessage, 'ada potongan') !== false ||
            strpos($cleanMessage, 'bisa kurang') !== false ||
            strpos($cleanMessage, 'kurangin') !== false ||
            strpos($cleanMessage, 'bisa diskon') !== false
        );

        if ($isNegotiating) {
            if ($isStaff) {
                return "Kebijakan tarif ADCom: Harga yang tertera merupakan estimasi standar. Penyesuaian biaya aktual ditentukan oleh Operator setelah survei teknisi lapangan atau jika terdapat pesanan paket bundling.";
            }

            $response = "Halo kak! Mengenai biaya layanan di **ADCom Jasa TKJ**:\n\n";
            $response .= "1. **Tarif Sudah Ramah Anggaran** 💰\n";
            $response .= "   - Tarif yang tertera pada katalog kami merupakan **estimasi standar** yang sudah kami rancang sangat terjangkau, ramah kantong pelajar, dan kompetitif dibanding jasa komersial umum.\n\n";
            $response .= "2. **Biaya Aktual Fleksibel Sesuai Tingkat Kerusakan** 🛠️\n";
            $response .= "   - Biaya akhir sangat fleksibel dan dapat disesuaikan setelah teknisi kami melakukan **pengecekan/survei langsung** ke perangkat Anda. Jika kerusakannya ringan atau hanya butuh perbaikan minor, biayanya tentu bisa lebih hemat!\n\n";
            $response .= "3. **Penawaran Khusus & Diskon Paket (Bundling)** 🎁\n";
            $response .= "   - Jika Anda memesan lebih dari 1 unit (misalnya instalasi beberapa PC sekaligus, paket perbaikan hardware + software, atau banyak titik CCTV), Operator kami dapat memberikan **penawaran harga khusus / diskon borongan**.\n\n";
            $response .= "Apakah Anda ingin teknisi kami jadwalkan untuk survei dan cek kondisi perangkat Anda terlebih dahulu?";
            return trim($response);
        }

        // 4. Cek biaya/harga secara umum
        if (strpos($lastUserMessage, 'biaya') !== false || strpos($lastUserMessage, 'harga') !== false || strpos($lastUserMessage, 'tarif') !== false || strpos($lastUserMessage, 'ongkos') !== false) {
            return self::formatAllServices($servicesData, "Berikut adalah estimasi biaya/harga layanan kami sesuai data:", $isStaff);
        }

        // 5. Cek keluhan teknis umum
        if (strpos($lastUserMessage, 'wifi') !== false || strpos($lastUserMessage, 'putus') !== false || strpos($lastUserMessage, 'sinyal') !== false || strpos($lastUserMessage, 'jaringan') !== false) {
            $response = "Sepertinya Anda mengalami kendala pada koneksi jaringan/WiFi. Beberapa penyebab umum:\n";
            $response .= "- Posisi router terhalang tembok tebal atau perangkat elektronik lain.\n";
            $response .= "- Terlalu banyak perangkat yang terhubung atau pengaturan bandwidth kurang optimal.\n";
            $response .= "- Gangguan kabel LAN atau konfigurasi router/Mikrotik.\n\n";
            $response .= "Layanan yang kami rekomendasikan dari data kami:\n";
            foreach ($servicesData as $s) {
                if (strpos(strtolower($s['name']), 'jaringan') !== false || strpos(strtolower($s['name']), 'mikrotik') !== false) {
                    $price = isset($s['estimated_price']) && is_numeric($s['estimated_price']) ? 'Rp ' . number_format($s['estimated_price'], 0, ',', '.') : ($s['estimated_price'] ?? 'Harga menyusul');
                    $response .= "- **{$s['name']}**: {$price}\n";
                }
            }
            if (!$isStaff) {
                $response .= "\nApakah Anda ingin teknisi kami melakukan survei/perbaikan?";
            }
            return trim($response);
        }

        if (strpos($lastUserMessage, 'cctv') !== false || strpos($lastUserMessage, 'kamera') !== false) {
            $response = "Untuk kebutuhan sistem keamanan, kami menyediakan layanan pemasangan CCTV.\n\n";
            foreach ($servicesData as $s) {
                if (strpos(strtolower($s['name']), 'cctv') !== false) {
                    $price = isset($s['estimated_price']) && is_numeric($s['estimated_price']) ? 'Rp ' . number_format($s['estimated_price'], 0, ',', '.') : ($s['estimated_price'] ?? 'Harga menyusul');
                    $response .= "- **{$s['name']}**: {$price} (Estimasi: {$s['estimated_time']})\n  Deskripsi: {$s['description']}\n\n";
                }
            }
            if (!$isStaff) {
                $response .= "Apakah Anda ingin menjadwalkan instalasi CCTV?";
            }
            return trim($response);
        }

        if (strpos($lastUserMessage, 'laptop') !== false || strpos($lastUserMessage, 'komputer') !== false || strpos($lastUserMessage, 'mati') !== false || strpos($lastUserMessage, 'lemot') !== false || strpos($lastUserMessage, 'overheat') !== false) {
            $response = "Kendala komputer/laptop mati sendiri atau lambat biasanya disebabkan:\n";
            $response .= "- Overheating (suhu terlalu panas) karena debu menumpuk atau pasta termal kering.\n";
            $response .= "- Kerusakan storage (HDD/SSD) atau memori RAM penuh.\n";
            $response .= "- Adanya malware/virus atau sistem operasi bermasalah.\n\n";
            $response .= "Layanan kami yang siap membantu:\n";
            foreach ($servicesData as $s) {
                if (strpos(strtolower($s['name']), 'komputer') !== false || strpos(strtolower($s['name']), 'laptop') !== false || strpos(strtolower($s['name']), 'software') !== false) {
                    $price = isset($s['estimated_price']) && is_numeric($s['estimated_price']) ? 'Rp ' . number_format($s['estimated_price'], 0, ',', '.') : ($s['estimated_price'] ?? 'Harga menyusul');
                    $response .= "- **{$s['name']}**: {$price}\n";
                }
            }
            if (!$isStaff) {
                $response .= "\nApakah Anda ingin membuat pesanan perbaikan?";
            }
            return trim($response);
        }

        // 6. Database Stats

        // A1. SIAPA Pengguna di dalam sistem (Menanyakan siapa saja penggunanya / sebutkan nama-namanya)
        $isAskingWhoAreUsers = (
            strpos($lastUserMessage, 'siapa total pengguna') !== false ||
            strpos($lastUserMessage, 'siapa saja total pengguna') !== false ||
            strpos($lastUserMessage, 'siapa pengguna') !== false ||
            strpos($lastUserMessage, 'siapa penggguna') !== false ||
            strpos($lastUserMessage, 'siapa saja pengguna') !== false ||
            strpos($lastUserMessage, 'siapa saja penggguna') !== false ||
            strpos($lastUserMessage, 'siapa saja penggunanya') !== false ||
            strpos($lastUserMessage, 'siapa saja pengggunanya') !== false ||
            strpos($lastUserMessage, 'siapa penggunanya') !== false ||
            strpos($lastUserMessage, 'ada siapa saja pengguna') !== false ||
            strpos($lastUserMessage, 'ada siapa saja penggunanya') !== false ||
            strpos($lastUserMessage, 'ada siapa pengguna') !== false ||
            strpos($lastUserMessage, 'sebutkan pengguna') !== false ||
            strpos($lastUserMessage, 'sebutkan siapa saja pengguna') !== false ||
            strpos($lastUserMessage, 'sebutkan siapa saja penggunanya') !== false ||
            strpos($lastUserMessage, 'sebutkan ada siapa saja penggunanya') !== false ||
            strpos($lastUserMessage, 'daftar pengguna') !== false ||
            strpos($lastUserMessage, 'list pengguna') !== false ||
            strpos($lastUserMessage, 'semua pengguna') !== false ||
            strpos($lastUserMessage, 'rincian pengguna') !== false ||
            strpos($lastUserMessage, 'siapa user') !== false ||
            strpos($lastUserMessage, 'siapa saja user') !== false ||
            strpos($lastUserMessage, 'siapa total user') !== false ||
            strpos($lastUserMessage, 'siapa saja total user') !== false ||
            strpos($lastUserMessage, 'daftar user') !== false ||
            strpos($lastUserMessage, 'list user') !== false ||
            strpos($lastUserMessage, 'semua user') !== false ||
            strpos($lastUserMessage, 'daftar akun') !== false ||
            preg_match('/\b(siapa|sebutkan|daftar|list|ada siapa)\b.*pengg*una/i', $lastUserMessage) ||
            preg_match('/pengg*una.*\b(siapa|sebutkan|ada siapa|rincian)\b/i', $lastUserMessage) ||
            preg_match('/\b(siapa|sebutkan|daftar|list|ada siapa)\b.*user/i', $lastUserMessage) ||
            preg_match('/user.*\b(siapa|sebutkan|ada siapa)\b/i', $lastUserMessage)
        );

        if ($isAskingWhoAreUsers) {
            $totalAll = $dbStats['total_all_users'] ?? 0;
            $totalCust = $dbStats['total_customers'] ?? 0;
            $custNames = !empty($dbStats['customer_names']) ? $dbStats['customer_names'] : '-';
            $totalTech = $dbStats['total_technicians'] ?? 0;
            $totalTechActive = $dbStats['total_technicians_active'] ?? 0;
            $totalTechInactive = $dbStats['total_technicians_inactive'] ?? 0;
            $techActiveNames = !empty($dbStats['technicians_active_names']) ? $dbStats['technicians_active_names'] : '-';
            $techInactiveNames = !empty($dbStats['technicians_inactive_names']) ? $dbStats['technicians_inactive_names'] : '-';
            $totalOp = $dbStats['total_operators'] ?? 0;
            $opNames = !empty($dbStats['operator_names']) ? $dbStats['operator_names'] : '-';
            $totalAdm = $dbStats['total_admins'] ?? 0;
            $admNames = !empty($dbStats['admin_names']) ? $dbStats['admin_names'] : '-';

            $techDetail = "{$totalTech} orang";
            if ($totalTechInactive > 0) {
                $techDetail .= " [Aktif: {$totalTechActive} ({$techActiveNames}), Non-Aktif: {$totalTechInactive} ({$techInactiveNames})]";
            } elseif ($totalTechActive > 0) {
                $techDetail .= " (Aktif: {$techActiveNames})";
            }

            $response = "Saat ini terdapat total **{$totalAll} pengguna** yang terdaftar di dalam sistem ADCom, dengan rincian sebagai berikut:\n\n";
            $response .= "- **Pelanggan (Customer):** {$totalCust} orang (yaitu: {$custNames})\n";
            $response .= "- **Teknisi (Technician):** {$techDetail}\n";
            $response .= "- **Operator:** {$totalOp} orang (yaitu: {$opNames})\n";
            $response .= "- **Admin:** {$totalAdm} orang (yaitu: {$admNames})\n\n";
            if (!$isStaff) {
                $response .= "Apakah ada hal lain seputar sistem atau layanan kami yang ingin Anda tanyakan?";
            }
            return trim($response);
        }

        // A2. BERAPA / TOTAL Pengguna (Hanya jawab total angkanya saja)
        $isAskingCountUsers = (
            strpos($lastUserMessage, 'berapa pengguna') !== false ||
            strpos($lastUserMessage, 'berapa banyak pengguna') !== false ||
            strpos($lastUserMessage, 'pengguna yang ada') !== false ||
            strpos($lastUserMessage, 'pengguna di dalam sistem') !== false ||
            strpos($lastUserMessage, 'pengguna sistem') !== false ||
            strpos($lastUserMessage, 'jumlah pengguna') !== false ||
            strpos($lastUserMessage, 'total pengguna') !== false ||
            strpos($lastUserMessage, 'ada berapa pengguna') !== false ||
            strpos($lastUserMessage, 'berapa user') !== false ||
            strpos($lastUserMessage, 'berapa banyak user') !== false ||
            strpos($lastUserMessage, 'jumlah user') !== false ||
            strpos($lastUserMessage, 'total user') !== false ||
            strpos($lastUserMessage, 'user yang ada') !== false ||
            strpos($lastUserMessage, 'user di dalam sistem') !== false ||
            strpos($lastUserMessage, 'user sistem') !== false ||
            strpos($lastUserMessage, 'berapa akun') !== false ||
            strpos($lastUserMessage, 'berapa banyak akun') !== false ||
            strpos($lastUserMessage, 'jumlah akun') !== false ||
            strpos($lastUserMessage, 'total akun') !== false ||
            strpos($lastUserMessage, 'ada berapa user') !== false ||
            strpos($lastUserMessage, 'ada berapa akun') !== false ||
            $lastUserMessage === 'pengguna' ||
            $lastUserMessage === 'user'
        );

        if ($isAskingCountUsers) {
            $totalAll = $dbStats['total_all_users'] ?? 0;
            if ($isStaff) {
                return "Saat ini terdapat total **{$totalAll} pengguna** yang terdaftar di dalam sistem ADCom.";
            }
            return "Saat ini terdapat total **{$totalAll} pengguna** yang terdaftar di dalam sistem ADCom. Ada yang bisa saya bantu terkait layanan jasa kami?";
        }

        // B. Khusus Pelanggan / Customer
        $isAskingCustomerWho = (
            strpos($lastUserMessage, 'siapa pelanggan') !== false ||
            strpos($lastUserMessage, 'siapa saja pelanggan') !== false ||
            strpos($lastUserMessage, 'daftar pelanggan') !== false ||
            strpos($lastUserMessage, 'sebutkan pelanggan') !== false ||
            strpos($lastUserMessage, 'siapa customer') !== false ||
            strpos($lastUserMessage, 'siapa saja customer') !== false ||
            strpos($lastUserMessage, 'daftar customer') !== false ||
            strpos($lastUserMessage, 'sebutkan customer') !== false
        );

        $isAskingCustomerCount = (
            strpos($lastUserMessage, 'berapa pelanggan') !== false ||
            strpos($lastUserMessage, 'jumlah pelanggan') !== false ||
            strpos($lastUserMessage, 'total pelanggan') !== false ||
            strpos($lastUserMessage, 'ada berapa pelanggan') !== false ||
            strpos($lastUserMessage, 'berapa customer') !== false ||
            strpos($lastUserMessage, 'jumlah customer') !== false ||
            strpos($lastUserMessage, 'total customer') !== false ||
            strpos($lastUserMessage, 'ada berapa customer') !== false ||
            $lastUserMessage === 'pelanggan' ||
            $lastUserMessage === 'customer'
        );

        if ($isAskingCustomerWho) {
            $total = $dbStats['total_customers'] ?? 0;
            $names = $dbStats['customer_names'] ?? '';
            $add = !empty($names) ? " (yaitu: {$names})" : '';
            if ($isStaff) {
                return "Terdapat **{$total} pelanggan/customer** terdaftar di sistem ADCom{$add}.";
            }
            return "Saat ini kami memiliki **{$total} pelanggan/customer** terdaftar di database kami{$add}. Ada yang bisa saya bantu terkait layanan jasa kami?";
        }

        if ($isAskingCustomerCount) {
            $total = $dbStats['total_customers'] ?? 0;
            if ($isStaff) {
                return "Terdapat **{$total} pelanggan/customer** terdaftar di sistem ADCom.";
            }
            return "Saat ini kami memiliki **{$total} pelanggan/customer** terdaftar di database kami. Ada yang bisa saya bantu terkait layanan jasa kami?";
        }

        // C. Khusus Teknisi (Dengan akurasi status Aktif & Non-Aktif)
        
        // 1) Cek apakah menanyakan teknisi yang TIDAK AKTIF / NON-AKTIF
        $isAskingInactiveTech = (
            (strpos($lastUserMessage, 'teknisi') !== false && (
                strpos($lastUserMessage, 'tidak aktif') !== false ||
                strpos($lastUserMessage, 'non aktif') !== false ||
                strpos($lastUserMessage, 'non-aktif') !== false ||
                strpos($lastUserMessage, 'nonaktif') !== false ||
                strpos($lastUserMessage, 'ga aktif') !== false ||
                strpos($lastUserMessage, 'gak aktif') !== false ||
                strpos($lastUserMessage, 'off') !== false ||
                strpos($lastUserMessage, 'tidak bertugas') !== false ||
                strpos($lastUserMessage, 'tidak bekerja') !== false ||
                strpos($lastUserMessage, 'libur') !== false ||
                strpos($lastUserMessage, 'cuti') !== false
            )) ||
            (strpos($lastUserMessage, 'siapa') !== false && (
                strpos($lastUserMessage, 'tidak aktif') !== false ||
                strpos($lastUserMessage, 'non aktif') !== false ||
                strpos($lastUserMessage, 'non-aktif') !== false ||
                strpos($lastUserMessage, 'nonaktif') !== false
            )) ||
            strpos($lastUserMessage, 'teknisi tidak aktif') !== false ||
            strpos($lastUserMessage, 'teknisi non aktif') !== false ||
            strpos($lastUserMessage, 'teknisi non-aktif') !== false ||
            strpos($lastUserMessage, 'teknisi nonaktif') !== false ||
            strpos($lastUserMessage, 'teknisi yg tidak aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yang tidak aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yg non aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yang non aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yang non-aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi yang tidak aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi tidak aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi non aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi yang non aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi yang non-aktif') !== false ||
            strpos($lastUserMessage, 'siapa yang tidak aktif') !== false ||
            strpos($lastUserMessage, 'siapa yang non aktif') !== false ||
            strpos($lastUserMessage, 'siapa yg tidak aktif') !== false ||
            strpos($lastUserMessage, 'siapa yg non aktif') !== false ||
            strpos($lastUserMessage, 'yang tidak aktif') !== false ||
            strpos($lastUserMessage, 'yang non aktif') !== false ||
            strpos($lastUserMessage, 'yang non-aktif') !== false ||
            strpos($lastUserMessage, 'ada teknisi yang tidak aktif') !== false ||
            strpos($lastUserMessage, 'ada teknisi tidak aktif') !== false ||
            strpos($lastUserMessage, 'ada teknisi non aktif') !== false
        );

        if ($isAskingInactiveTech) {
            $totalInactive = $dbStats['total_technicians_inactive'] ?? 0;
            $inactiveNames = !empty($dbStats['technicians_inactive_names']) ? $dbStats['technicians_inactive_names'] : '';
            $totalActive = $dbStats['total_technicians_active'] ?? 0;
            $activeNames = !empty($dbStats['technicians_active_names']) ? $dbStats['technicians_active_names'] : '-';

            if ($totalInactive > 0) {
                $response = "Teknisi yang berstatus **Non-Aktif**:\n";
                $response .= "❌ **{$inactiveNames}** ({$totalInactive} orang - sedang tidak aktif/tidak bertugas).\n\n";
                $response .= "Teknisi yang berstatus **Aktif**:\n";
                $response .= "✅ **{$activeNames}** ({$totalActive} orang).";
                if (!$isStaff) {
                    $response .= "\n\nApakah ada kebutuhan layanan jasa atau perbaikan yang ingin Anda tanyakan?";
                }
                return trim($response);
            } else {
                if ($isStaff) {
                    return "Seluruh teknisi berstatus **Aktif** ({$activeNames}) dan tidak ada teknisi yang non-aktif.";
                }
                return "Berdasarkan data sistem kami, saat ini **seluruh teknisi berstatus Aktif** ({$activeNames}) dan tidak ada teknisi yang non-aktif. Semua siap melayani pesanan Anda!";
            }
        }

        // 2) Cek apakah menanyakan teknisi yang AKTIF
        $isAskingActiveTech = (
            (strpos($lastUserMessage, 'teknisi') !== false && (
                strpos($lastUserMessage, 'aktif') !== false ||
                strpos($lastUserMessage, 'active') !== false ||
                strpos($lastUserMessage, 'siap') !== false ||
                strpos($lastUserMessage, 'bertugas') !== false ||
                strpos($lastUserMessage, 'standby') !== false
            ) && strpos($lastUserMessage, 'tidak') === false && strpos($lastUserMessage, 'non') === false) ||
            strpos($lastUserMessage, 'siapa teknisi yang aktif') !== false ||
            strpos($lastUserMessage, 'siapa teknisi aktif') !== false ||
            strpos($lastUserMessage, 'siapa yang aktif') !== false ||
            strpos($lastUserMessage, 'siapa yg aktif') !== false ||
            strpos($lastUserMessage, 'teknisi aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yang aktif') !== false ||
            strpos($lastUserMessage, 'teknisi yg aktif') !== false
        );

        if ($isAskingActiveTech) {
            $totalActive = $dbStats['total_technicians_active'] ?? 0;
            $activeNames = !empty($dbStats['technicians_active_names']) ? $dbStats['technicians_active_names'] : '-';
            $totalInactive = $dbStats['total_technicians_inactive'] ?? 0;
            $inactiveNames = !empty($dbStats['technicians_inactive_names']) ? $dbStats['technicians_inactive_names'] : '';

            $response = "Teknisi yang saat ini berstatus **Aktif**:\n";
            $response .= "✅ **{$activeNames}** ({$totalActive} orang)\n\n";
            if ($totalInactive > 0) {
                $response .= "*(Catatan: Terdapat {$totalInactive} teknisi berstatus Non-Aktif, yaitu: {$inactiveNames})*\n";
            }
            if (!$isStaff) {
                $response .= "\nIngin membuat pesanan atau konsultasi perbaikan dengan teknisi kami?";
            }
            return trim($response);
        }

        // 3) Cek apakah menanyakan nama teknisi tertentu (misal Dina, Agus, Jono)
        if (!empty($dbStats['technicians_list']) && is_array($dbStats['technicians_list'])) {
            foreach ($dbStats['technicians_list'] as $tech) {
                $tName = strtolower($tech['name']);
                $parts = explode(' ', $tName);
                $firstName = $parts[0];
                
                $nameMatched = (strpos($lastUserMessage, $tName) !== false || (strlen($firstName) >= 3 && strpos($lastUserMessage, $firstName) !== false));
                
                if ($nameMatched && (
                    strpos($lastUserMessage, 'status') !== false ||
                    strpos($lastUserMessage, 'aktif') !== false ||
                    strpos($lastUserMessage, 'teknisi') !== false ||
                    strpos($lastUserMessage, 'siapa') !== false ||
                    strpos($lastUserMessage, 'apakah') !== false ||
                    strpos($lastUserMessage, 'bisa') !== false ||
                    strpos($lastUserMessage, 'kerja') !== false ||
                    strpos($lastUserMessage, 'pesan') !== false
                )) {
                    $isActive = (isset($tech['is_active']) && $tech['is_active'] == 1);
                    $statusText = $isActive ? "Aktif ✅" : "Non-Aktif ❌";
                    $statusDesc = $isActive 
                        ? "saat ini siap menerima dan mengerjakan pesanan layanan." 
                        : "saat ini sedang tidak aktif dan tidak menerima pesanan tugas.";
                    
                    if ($isStaff) {
                        return "Teknisi **{$tech['name']}** berstatus **{$statusText}** ({$statusDesc}).";
                    }

                    $response = "Berdasarkan data sistem kami, teknisi **{$tech['name']}** berstatus **{$statusText}** dan {$statusDesc}";
                    if (!$isActive && !empty($dbStats['technicians_active_names'])) {
                        $response .= "\n\nUntuk kebutuhan layanan, Anda dapat menggunakan teknisi kami yang sedang aktif, yaitu: **{$dbStats['technicians_active_names']}**.";
                    }
                    return $response;
                }
            }
        }

        // 4) Pertanyaan umum seputar teknisi ('ada berapa teknisi', 'siapa teknisi', dsb)
        if (
            strpos($lastUserMessage, 'berapa teknisi') !== false || 
            strpos($lastUserMessage, 'jumlah teknisi') !== false || 
            strpos($lastUserMessage, 'total teknisi') !== false ||
            strpos($lastUserMessage, 'ada berapa teknisi') !== false
        ) {
            $total = $dbStats['total_technicians'] ?? 0;
            if ($isStaff) {
                return "Saat ini terdapat total **{$total} teknisi** yang terdaftar di ADCom.";
            }
            return "Berdasarkan data sistem kami, saat ini terdapat total **{$total} teknisi** yang terdaftar di ADCom.";
        }

        if (
            strpos($lastUserMessage, 'siapa teknisi') !== false ||
            strpos($lastUserMessage, 'siapa saja teknisi') !== false ||
            strpos($lastUserMessage, 'daftar teknisi') !== false ||
            strpos($lastUserMessage, 'list teknisi') !== false ||
            strpos($lastUserMessage, 'semua teknisi') !== false ||
            strpos($lastUserMessage, 'teknisi adcom') !== false ||
            strpos($lastUserMessage, 'teknisi kami') !== false ||
            $lastUserMessage === 'teknisi'
        ) {
            $total = $dbStats['total_technicians'] ?? 0;
            $totalActive = $dbStats['total_technicians_active'] ?? 0;
            $activeNames = !empty($dbStats['technicians_active_names']) ? $dbStats['technicians_active_names'] : '-';
            $totalInactive = $dbStats['total_technicians_inactive'] ?? 0;
            $inactiveNames = !empty($dbStats['technicians_inactive_names']) ? $dbStats['technicians_inactive_names'] : '';

            $response = "Berdasarkan data sistem kami, saat ini terdapat total **{$total} teknisi** yang terdaftar di ADCom, dengan rincian status:\n\n";
            $response .= "- **Teknisi Aktif ({$totalActive} orang):** {$activeNames} *(siap membantu pengerjaan pesanan)*\n";
            if ($totalInactive > 0) {
                $response .= "- **Teknisi Non-Aktif ({$totalInactive} orang):** {$inactiveNames} *(sedang tidak aktif/tidak bertugas)*\n";
            }
            if (!$isStaff) {
                $response .= "\nApakah Anda ingin membuat pesanan atau konsultasi perbaikan dengan teknisi kami?";
            }
            return trim($response);
        }

        // D. Khusus Operator
        if (strpos($lastUserMessage, 'siapa operator') !== false || strpos($lastUserMessage, 'siapa saja operator') !== false || strpos($lastUserMessage, 'daftar operator') !== false) {
            $total = $dbStats['total_operators'] ?? 0;
            $names = $dbStats['operator_names'] ?? '';
            $add = !empty($names) ? " (yaitu: {$names})" : '';
            if ($isStaff) {
                return "Terdapat **{$total} operator**{$add} di sistem ADCom.";
            }
            return "Kami memiliki **{$total} operator**{$add} yang siap melayani dan mengkoordinasikan pesanan Anda.";
        }

        if (strpos($lastUserMessage, 'berapa operator') !== false || strpos($lastUserMessage, 'jumlah operator') !== false || strpos($lastUserMessage, 'total operator') !== false) {
            $total = $dbStats['total_operators'] ?? 0;
            if ($isStaff) {
                return "Terdapat **{$total} operator** di sistem ADCom.";
            }
            return "Kami memiliki **{$total} operator** yang siap melayani dan mengkoordinasikan pesanan Anda.";
        }

        // E. Khusus Admin
        if (strpos($lastUserMessage, 'siapa admin') !== false || strpos($lastUserMessage, 'siapa saja admin') !== false || strpos($lastUserMessage, 'daftar admin') !== false) {
            $total = $dbStats['total_admins'] ?? 0;
            $names = $dbStats['admin_names'] ?? '';
            $add = !empty($names) ? " (yaitu: {$names})" : '';
            if ($isStaff) {
                return "Terdapat **{$total} administrator**{$add} yang mengelola sistem ADCom.";
            }
            return "Saat ini terdapat **{$total} administrator**{$add} yang mengelola sistem ADCom.";
        }

        if (strpos($lastUserMessage, 'berapa admin') !== false || strpos($lastUserMessage, 'jumlah admin') !== false || strpos($lastUserMessage, 'total admin') !== false) {
            $total = $dbStats['total_admins'] ?? 0;
            if ($isStaff) {
                return "Terdapat **{$total} administrator** yang mengelola sistem ADCom.";
            }
            return "Saat ini terdapat **{$total} administrator** yang mengelola sistem ADCom.";
        }

        // F. Pesanan & Layanan
        if (strpos($lastUserMessage, 'berapa pesanan') !== false || strpos($lastUserMessage, 'jumlah pesanan') !== false || strpos($lastUserMessage, 'total pesanan') !== false || strpos($lastUserMessage, 'total order') !== false) {
            $total = $dbStats['total_orders'] ?? 0;
            $nProc = $dbStats['count_processing'] ?? 0;
            $nAss = $dbStats['count_assigned'] ?? 0;
            $nComp = $dbStats['count_completed'] ?? 0;
            $nPend = $dbStats['count_pending'] ?? 0;
            
            $response = "Total **{$total} pesanan** di dalam sistem ADCom:\n";
            $response .= "- **Sedang Dikerjakan (Processing):** {$nProc} pesanan\n";
            $response .= "- **Sudah Ditugaskan (Assigned):** {$nAss} pesanan\n";
            $response .= "- **Selesai (Completed):** {$nComp} pesanan\n";
            if ($nPend > 0) {
                $response .= "- **Menunggu Konfirmasi (Pending):** {$nPend} pesanan\n";
            }
            if (!$isStaff) {
                $response .= "\nKetik 'pesanan siapa saja yang sedang dikerjakan' untuk melihat rincian pekerjaan aktif.";
            }
            return trim($response);
        }

        if (strpos($lastUserMessage, 'berapa total layanan') !== false || strpos($lastUserMessage, 'jumlah total layanan') !== false || strpos($lastUserMessage, 'berapa jenis jasa') !== false) {
            $total = count($servicesData);
            if ($isStaff) {
                return "Terdapat **{$total} layanan jasa aktif** yang tersedia di sistem.";
            }
            return "Saat ini kami memiliki **{$total} layanan jasa aktif** yang tersedia. Anda dapat melihat daftarnya dengan mengetik 'ada jasa apa saja'.";
        }

        // 7. Identity & Greetings
        if (strpos($lastUserMessage, 'siapa saya') !== false || strpos($lastUserMessage, 'siapa namaku') !== false || strpos($lastUserMessage, 'nama saya siapa') !== false) {
            if ($isStaff) {
                $roleTitle = ucfirst($userRole ?: 'Staff');
                return "Anda login sebagai **{$roleTitle}** dengan nama **{$userName}**.";
            }
            return "Anda adalah pelanggan terhormat kami, Bapak/Ibu " . $userName . ". Apakah ada keluhan teknis atau layanan jasa yang bisa saya bantu hari ini?";
        }

        if (strpos($lastUserMessage, 'siapa anda') !== false || strpos($lastUserMessage, 'siapa kamu') !== false || strpos($lastUserMessage, 'kamu siapa') !== false || strpos($lastUserMessage, 'anda siapa') !== false || strpos($lastUserMessage, 'perkenalkan') !== false || strpos($lastUserMessage, 'tentang kamu') !== false) {
            if ($isStaff) {
                return "Saya adalah Chatbot Asisten AI internal sistem ADCom Jasa TKJ.";
            }
            return "Halo! Saya adalah Chatbot Asisten AI untuk ADCom Jasa TKJ. Saya bertugas membantu Anda berkonsultasi mengenai keluhan teknis (komputer, laptop, jaringan, mikrotik, dan CCTV), memberikan rincian data layanan jasa kami, alur pemesanan, dan simulasi harga. Ada yang bisa saya bantu?";
        }

        if (strpos($lastUserMessage, 'halo') !== false || strpos($lastUserMessage, 'hai') !== false || strpos($lastUserMessage, 'selamat pagi') !== false || strpos($lastUserMessage, 'selamat siang') !== false || strpos($lastUserMessage, 'selamat sore') !== false || strpos($lastUserMessage, 'selamat malam') !== false || strpos($lastUserMessage, 'assalamualaikum') !== false) {
            if ($isStaff) {
                $roleTitle = ucfirst($userRole ?: 'Staff');
                return "Halo {$userName} ({$roleTitle}). Silakan sampaikan pertanyaan atau data sistem yang ingin Anda periksa.";
            }
            return "Halo Bapak/Ibu " . $userName . "! Selamat datang di Layanan Asisten AI ADCom. Saya siap membantu Anda memberikan informasi paket jasa maupun konsultasi teknis seputar komputer, jaringan, dan CCTV. Ada keluhan yang ingin Anda konsultasikan?";
        }

        // 8. Out of scope
        $outKeywords = [
            'pemrograman', 'coding', 'koding', 'bikin aplikasi', 'buatkan kode', 
            'resep masakan', 'resep', 'politik', 'puisi', 'pantun', 'cerita dongeng', 
            'siapa presiden', 'cuaca hari ini', 'ramalan', 'zodiak', 'lagu'
        ];
        foreach ($outKeywords as $okw) {
            if (strpos($lastUserMessage, $okw) !== false) {
                return "Maaf, saya tidak dapat membantu jawab pertanyaan anda. Saya assistant AI yang bertugas membantu seputar layanan jasa.";
            }
        }

        // 9. Related keywords fallback
        $keywords = [
            'rusak', 'error', 'bantu', 'pesan', 'layanan', 'jasa', 'service', 'servis', 
            'internet', 'koneksi', 'lambat', 'lemot', 'tanya', 'keluhan', 'teknis', 
            'perbaikan', 'pasang', 'biaya', 'harga', 'tarif', 'ongkos', 'order', 
            'jadwal', 'masalah', 'kendala', 'solusi', 'komputer', 'laptop', 'cctv', 
            'jaringan', 'mikrotik', 'wifi', 'lan', 'pc', 'os', 'windows',
            'ulasan', 'review', 'rating', 'bintang', 'komentar', 'penilaian', 'testimoni',
            'pekerjaan', 'pekerjaaan', 'tugas', 'kerjaan', 'diselesaikan', 'selesaikan'
        ];
        $isRelated = false;
        foreach ($keywords as $kw) {
            if (strpos($lastUserMessage, $kw) !== false || strpos($cleanMessage, $kw) !== false) {
                $isRelated = true;
                break;
            }
        }

        if ($isRelated) {
            return self::formatAllServices($servicesData, "Berikut adalah daftar layanan jasa yang kami sediakan:", $isStaff);
        }

        return "Maaf, saya tidak dapat membantu jawab pertanyaan anda. Saya assistant AI yang bertugas membantu seputar layanan jasa.";
    }

    /**
     * Membersihkan respon dari komentar tambahan, basa-basi penutup, ajakan promosi,
     * atau saran yang tidak ditanyakan khusus untuk peran internal (admin, operator, teknisi).
     */
    public static function cleanStaffResponse($text, $userRole = null) {
        if (!is_string($text) || empty(trim($text))) {
            return $text;
        }

        // 1. Hapus baris pembuka basa-basi seperti:
        // "Tentu, berikut datanya:", "Tentu saja, saya bantu:", "Baik Bapak/Ibu..."
        $text = preg_replace('/^(tentu( saja)?|baik|halo|selamat (pagi|siang|sore|malam))[^\n]*?(:|\n+)/i', '', $text);

        // 2. Filter baris demi baris dari bawah untuk menghapus komentar penutup, pertanyaan retoris, atau saran yang tidak diminta
        $lines = explode("\n", $text);
        
        $commentaryPatterns = [
            '/apakah ada (yang|hal|layanan|pesanan|bantuan|pertanyaan|masalah)[^\n]*\??$/i',
            '/apakah anda (ingin|membutuhkan|perlu|tertarik)[^\n]*\??$/i',
            '/ingin (membuat|memesan|konsultasi|menanyakan)[^\n]*\??$/i',
            '/ada yang bisa (saya|kami) bantu[^\n]*\??$/i',
            '/ada keluhan yang[^\n]*\??$/i',
            '/sebagai (operator|admin|teknisi|staff)[^\n]*(dapat|bisa|silakan|pantau)[^\n]*/i',
            '/anda dapat (terus )?memantau[^\n]*/i',
            '/silakan (buka|cek|pantau|kunjungi|hubungi|menghubungi)[^\n]*(menu|halaman|dashboard|operator|admin)[^\n]*/i',
            '/ketik [\'"][^\n]+[\'"][^\n]*/i',
            '/[💡ℹ️]\s*\**tips:?\**[^\n]*/i',
            '/semoga (informasi|penjelasan|hari|membantu)[^\n]*/i',
            '/jangan ragu untuk (bertanya|menghubungi)[^\n]*/i',
            '/jika ada pertanyaan (lain|lebih lanjut)[^\n]*/i',
            '/jika butuh bantuan[^\n]*/i',
            '/terima kasih (telah|sudah)[^\n]*/i',
            '/siap membantu (anda|keluhan)[^\n]*/i'
        ];

        // Lakukan pembersihan baris dari belakang (trailing commentary lines)
        while (!empty($lines)) {
            $lastLine = trim(end($lines));
            
            // Jika baris terakhir kosong, buang
            if ($lastLine === '') {
                array_pop($lines);
                continue;
            }

            $matched = false;
            foreach ($commentaryPatterns as $pattern) {
                if (preg_match($pattern, $lastLine)) {
                    array_pop($lines);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                break;
            }
        }

        // Juga bersihkan inline jika kalimat penutup berada di baris terakhir yang sama
        $result = implode("\n", $lines);
        foreach ($commentaryPatterns as $pattern) {
            $result = preg_replace($pattern, '', $result);
        }

        return trim($result);
    }

    /**
     * Mengambil konfigurasi dinamis AI dari database (tabel ai_settings)
     */
    public static function getDynamicSettings(): array {
        try {
            if (class_exists('AIModel')) {
                $aiModel = new AIModel();
                $all = $aiModel->getAllSettings();
                $settings = [];
                foreach ($all as $key => $item) {
                    $settings[$key] = $item['value'];
                }
                return $settings;
            }
        } catch (Throwable $e) {
            // Silently fall through to defaults
        }

        return [
            'ai_enabled' => '1',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-flash-latest',
            'ai_temperature' => '0.7',
            'ai_max_tokens' => '800',
            'ai_timeout' => '15',
            'ai_fallback_faq' => '1',
            'bot_name' => 'ADCom Virtual Assistant',
            'welcome_message' => 'Halo! Saya Asisten Virtual ADCom Jasa. Ada yang bisa saya bantu seputar layanan servis komputer, laptop, printer, atau instalasi jaringan kami hari ini?',
            'response_language' => 'id',
            'bot_tone' => 'friendly_professional',
            'system_instruction' => "Anda adalah Chatbot Asisten AI untuk 'ADCom Jasa TKJ'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik dan sopan."
        ];
    }

    /**
     * Menguji koneksi riil ke Google Gemini API
     * Tidak membocorkan secret API key ke output dan mencatat hasil pengujian ke database
     */
    public static function testConnection($overrideKey = null, $overrideModel = null): array {
        $apiKey = !empty($overrideKey) ? trim($overrideKey) : (defined('AI_API_KEY') ? AI_API_KEY : '');
        
        // Periksa apakah API Key kosong atau placeholder default
        if (empty($apiKey) || $apiKey === 'ISI_API_KEY_DI_ENV' || strpos($apiKey, '•••') !== false) {
            $apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
        }

        if (empty($apiKey) || $apiKey === 'ISI_API_KEY_DI_ENV') {
            $msg = 'API Key Google Gemini belum dikonfigurasi. Silakan simpan API Key Anda terlebih dahulu.';
            self::logTestResult('failed', null, $msg);
            return [
                'success' => false,
                'status' => 'failed',
                'latency' => null,
                'message' => $msg,
                'model' => $overrideModel ?? 'gemini-flash-latest'
            ];
        }

        $settings = self::getDynamicSettings();
        $modelName = !empty($overrideModel) ? trim($overrideModel) : (!empty($settings['ai_model']) ? trim($settings['ai_model']) : 'gemini-flash-latest');
        $modelClean = preg_replace('#^models/#', '', $modelName);
        $timeout = isset($settings['ai_timeout']) && is_numeric($settings['ai_timeout']) ? min(60, max(5, (int)$settings['ai_timeout'])) : 15;

        // Gunakan endpoint verifikasi model Gemini untuk pengujian koneksi yang cepat, akurat, dan tidak terhambat thinking latency
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode($modelClean) . '?key=' . $apiKey;

        $startTime = microtime(true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $latencyMs = round((microtime(true) - $startTime) * 1000);

        if ($curlErr) {
            $msg = 'Koneksi gagal: Batas waktu request terlampaui (' . $timeout . 's) atau kendala jaringan server.';
            self::logTestResult('failed', $latencyMs, $msg);
            return [
                'success' => false,
                'status' => 'failed',
                'latency' => $latencyMs,
                'message' => $msg,
                'model' => $modelClean
            ];
        }

        $resultData = !empty($response) ? json_decode($response, true) : null;

        if ($httpCode === 200 && $resultData && !empty($resultData['name'])) {
            $displayName = $resultData['displayName'] ?? $modelClean;
            $msg = "Koneksi ke Google Gemini API ({$displayName}) berhasil! Terhubung dalam {$latencyMs} ms.";
            self::logTestResult('success', $latencyMs, $msg);
            return [
                'success' => true,
                'status' => 'success',
                'latency' => $latencyMs,
                'message' => $msg,
                'model' => $modelClean,
                'display_name' => $displayName
            ];
        }

        // Tangani kode error HTTP secara informatif dan aman tanpa memaparkan kunci
        if ($httpCode === 400) {
            $errText = $resultData['error']['message'] ?? '';
            if (stripos($errText, 'API key not valid') !== false) {
                $msg = "Autentikasi Gagal (HTTP 400): API Key Google Gemini tidak valid. Silakan periksa kembali API Key Anda.";
            } else {
                $msg = "Gagal (HTTP 400): Parameter request tidak didukung atau model '{$modelClean}' tidak valid.";
            }
        } elseif ($httpCode === 403 || $httpCode === 401) {
            $msg = "Autentikasi Gagal (HTTP {$httpCode}): API Key tidak valid, dinonaktifkan, atau tidak memiliki izin akses Google Gemini.";
        } elseif ($httpCode === 404) {
            $msg = "Model Tidak Ditemukan (HTTP 404): Nama model '{$modelClean}' tidak tersedia pada endpoint API Gemini saat ini.";
        } elseif ($httpCode === 429) {
            $msg = "Batas Kuota Terlampaui (HTTP 429): Quota request Gemini API Anda habis atau rate limit tercapai. Silakan coba beberapa saat lagi.";
        } elseif ($httpCode >= 500) {
            $msg = "Layanan Google Bermasalah (HTTP {$httpCode}): Server Google Gemini sedang mengalami gangguan sementara.";
        } else {
            $msg = "Koneksi gagal dengan status HTTP {$httpCode}.";
        }

        self::logTestResult('failed', $latencyMs, $msg);
        return [
            'success' => false,
            'status' => 'failed',
            'latency' => $latencyMs,
            'message' => $msg,
            'model' => $modelClean
        ];
    }

    private static function logTestResult($status, $latencyMs, $message) {
        try {
            if (class_exists('AIModel')) {
                $aiModel = new AIModel();
                $aiModel->updateTestStatus($status, $latencyMs, $message);
            }
        } catch (Throwable $e) {
            // Ignore error during logging
        }
    }
}
