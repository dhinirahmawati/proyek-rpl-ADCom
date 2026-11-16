# ADCom - Sistem Informasi Manajemen Layanan & Jasa Servis Komputer, Jaringan, dan IT Support

Project Tugas Besar / Ujian Akhir Semester (UAS)  
Mata Kuliah: Rekayasa Perangkat Lunak (RPL)

---

## 1. Identitas Anggota Kelompok

* **Nomor Kelompok:** [Kelompok XX] *(Silakan lengkapi nomor kelompok)*
* **Daftar Anggota:**
  1. **[Nama Lengkap Anggota 1]** — NIM: `[NIM Anggota 1]` *(Ketua Kelompok)*
  2. **[Nama Lengkap Anggota 2]** — NIM: `[NIM Anggota 2]` *(Anggota)*
  3. **[Nama Lengkap Anggota 3]** — NIM: `[NIM Anggota 3]` *(Anggota)*
  4. **[Nama Lengkap Anggota 4]** — NIM: `[NIM Anggota 4]` *(Anggota)*

---

## 2. Tema / Topik Project

* **Judul Tema:**  
  `[Pengembangan Sistem Informasi Manajemen Pemesanan Jasa Servis Perangkat IT Berbasis Web dengan Integrasi Asisten Virtual Cerdas dan Notifikasi OTP]` *(Silakan sesuaikan dengan judul resmi dari dosen/kelompok)*

---

## 3. Deskripsi Aplikasi

### Penjelasan Aplikasi
**ADCom (ADCom Jasa)** adalah platform sistem informasi berbasis web yang dirancang khusus untuk memfasilitasi dan mengelola seluruh siklus pemesanan serta perbaikan perangkat keras (*hardware*), perangkat lunak (*software*), dan infrastruktur jaringan komputer. Aplikasi ini mengintegrasikan seluruh pemangku kepentingan dalam satu ekosistem terpadu: **Pelanggan (*Customer*)**, **Operator Layanan**, **Teknisi Lapangan/Bengkel**, dan **Administrator Sistem**.

### Masalah yang Ingin Diselesaikan
Dalam industri reparasi dan instalasi perangkat IT berskala lokal (seperti bengkel komputer atau unit produksi sekolah kejuruan TKJ), proses operasional konvensional kerap menghadapi beberapa kendala utama:
1. **Pencatatan Manual yang Rentan Hilang/Tertukar:** Pemesanan jasa melalui telepon atau pesan teks manual sering tidak terdokumentasi rapi, menyebabkan salah jadwal dan hilangnya riwayat kerusakan perangkat.
2. **Kurangnya Transparansi Progres Perbaikan:** Pelanggan sering merasa cemas karena tidak mengetahui status penanganan unit mereka secara tepat waktu (*real-time*).
3. **Ketidakpastian Biaya dan Layanan:** Pelanggan awam kesulitan mendiagnosis awal masalah perangkat mereka serta tidak mendapatkan perkiraan estimasi harga dan waktu pengerjaan sebelum memesan.
4. **Koordinasi Tim Internal yang Lambat:** Operator kesulitan memetakan ketersediaan teknisi aktif untuk mendistribusikan pesanan servis secara cepat dan merata.

### Tujuan Pembuatan Aplikasi
1. **Digitalisasi Manajemen Servis:** Menyediakan sistem alur kerja terstruktur mulai dari pemesanan (*order*), penugasan ke teknisi (*dispatch*), pengerjaan unit (*processing*), hingga serah terima (*completed*).
2. **Transparansi Layanan dan Pelacakan Real-Time:** Memungkinkan pelanggan memantau perkembangan perbaikan lengkap dengan catatan teknisi dan foto dokumentasi pengerjaan.
3. **Konsultasi Interaktif 24/7:** Menyediakan asisten virtual bertenaga kecerdasan buatan (*Artificial Intelligence*) untuk diagnosa awal kerusakan dan rekomendasi paket jasa.
4. **Keamanan Akun Terverifikasi:** Mengamankan akun pengguna melalui verifikasi kode *One-Time Password* (OTP) berbasis WhatsApp Gateway.

---

## 4. Fitur Aplikasi

Aplikasi dibangun dengan hak akses berbasis peran (*Role-Based Access Control* / RBAC):

### A. Fitur Pelanggan (Customer)
* **Katalog Layanan & Informasi Harga:** Menjelajahi kategori jasa (Networking, Hardware, Software, Security/CCTV) lengkap dengan estimasi harga dan durasi pengerjaan.
* **Pemesanan Jasa Online (*Create Order*):** Formulir pemesanan dengan deskripsi keluhan kerusakan, alamat lokasi penjemputan/instalasi, dan nomor kontak.
* **Pelacakan Progres Servis Real-Time (*Order Tracking*):** Melihat status tahapan pengerjaan (*Pending*, *Assigned*, *Processing*, *Completed*, *Cancelled*) beserta log catatan teknisi.
* **Riwayat Pesanan & Detail Servis:** Mengakses arsip seluruh riwayat pemesanan yang pernah dilakukan sebelumnya.
* **Ulasan & Penilaian Bintang (*Review & Rating*):** Memberikan nilai kepuasan (1-5 bintang) dan testimoni setelah pesanan selesai.
* **Autentikasi & Keamanan Akun:**
  * Registrasi akun pelanggan dengan verifikasi nomor WhatsApp via OTP.
  * Autentikasi Login (didukung opsi verifikasi 2-Langkah / 2FA OTP).
  * Pemulihan kata sandi (*Forgot Password*) mandiri menggunakan validasi kode OTP WhatsApp.

### B. Fitur Operator
* **Dashboard Monitoring Operasional:** Ringkasan statistik jumlah pesanan masuk, pesanan yang sedang diproses, dan pesanan selesai.
* **Manajemen Penugasan (*Assign Order*):** Memvalidasi pesanan baru dan menugaskan teknisi yang berstatus aktif sesuai bidang keahlian.
* **Manajemen Katalog Layanan (CRUD):** Menambah, mengubah, mengaktifkan/menonaktifkan layanan jasa, serta mengunggah gambar layanan.
* **Manajemen Kategori Layanan (CRUD):** Mengelola klasifikasi kategori jasa pendukung.
* **Monitoring Ulasan & Rating:** Meninjau seluruh penilaian pelanggan untuk evaluasi mutu layanan.
* **Monitoring Data Teknisi:** Melihat daftar teknisi terdaftar beserta beban tugas aktif masing-masing teknisi.

### C. Fitur Teknisi (Technician)
* **Dashboard Tugas Teknisi:** Antarmuka khusus menampilkan daftar penugasan servis yang diberikan kepada teknisi yang bersangkutan.
* **Detail Pekerjaan & Informasi Kendala:** Melihat rincian kerusakan unit, alamat pelanggan, dan nomor kontak yang dapat dihubungi.
* **Pembaruan Status & Catatan Pengerjaan (*Work Updates*):** Memperbarui status pengerjaan (dari *Assigned* ke *Processing*, hingga *Completed*), menyertakan catatan diagnosa perbaikan teknis dan dokumentasi foto.

### D. Fitur Administrator (Admin)
* **Master Dashboard Eksekutif:** Statistik menyeluruh pengguna sistem, total transaksi, pendapatan estimasi, dan utilisasi layanan.
* **Manajemen Pengguna (User Management CRUD):** Pengelolaan akun Admin, Operator, Teknisi, dan Pelanggan, termasuk aktivasi atau penonaktifan akun.
* **Konfigurasi Autentikasi & OTP (Auth Settings):** Pengaturan durasi kedaluwarsa kode OTP, batas maksimum percobaan salah (*max attempts*), jeda pengiriman ulang (*cooldown*), dan pemilihan provider WhatsApp Gateway (Fonnte, Wablas, Meta Cloud API, atau Mock Driver).
* **Konfigurasi Chatbot AI (AI Settings):** Panel dinamis untuk mengatur status aktif chatbot, pemilihan model Google Gemini, pengaturan *temperature*, batas *tokens*, pesan selamat datang, persona/instruksi sistem, dan opsi *fallback FAQ*.
* **Laporan & Rekapitulasi Data (Reporting):** Filter dan rekapitulasi data pesanan berdasarkan periode tanggal untuk pelaporan operasional.

---

## 5. Fitur AI (Asisten Virtual Pintar)

### Penjelasan & Fungsi AI
Aplikasi mengintegrasikan modul asisten cerdas bernama **ADCom Virtual Assistant**. Fitur ini dirancang untuk:
1. **Konsultasi Gangguan Teknis 24/7:** Menjawab pertanyaan pelanggan mengenai gejala kerusakan perangkat (misalnya: PC sering *blue screen*, laptop *overheating*, jaringan Wi-Fi lambat, atau pemilihan kamera CCTV).
2. **Rekomendasi Layanan Otomatis:** Memberikan saran solusi terbaik serta mengarahkan pengguna ke katalog layanan yang relevan beserta estimasi biayanya.
3. **Asisten Operasional untuk Staf Internal:** Memberikan respons analitik yang ringkas, faktual, dan *to-the-point* bagi staf manajemen (Admin, Operator, Teknisi) saat menanyakan status pesanan atau ketersediaan teknisi.

### Teknologi & Model AI yang Digunakan
* **Provider AI:** Google Gemini API (*Google Generative Language Platform*).
* **Model AI Aktif:** `gemini-flash-latest` (dengan kapabilitas konfigurasi fleksibel ke `gemini-1.5-flash`, `gemini-pro`, dan model generasi Gemini lainnya).
* **Karakteristik Model:** Menawarkan waktu respons latensi rendah (*low latency*), kemampuan penalaran konteks yang akurat, serta efisiensi konsumsi token.

### Cara AI Terintegrasi dengan Aplikasi
1. **Arsitektur Service Layer Terpadu:** Modul AI dibungkus dalam kelas layanan independen `AIService.php` yang dipanggil melalui controller `Ai.php`.
2. **Komunikasi REST API:** Aplikasi berkomunikasi langsung dengan Google Generative Language API menggunakan cURL berbasis metode `POST` dengan format data JSON.
3. **Retrieval-Augmented Context (RAG In-Memory):** Sebelum query dikirimkan ke model Gemini, sistem secara dinamis menyuntikkan (*inject*) konteks data real-time dari database ke dalam instruksi sistem (*system prompt*):
   * Katalog layanan dan daftar harga terbaru.
   * Statistik operasional sistem.
   * Ketersediaan teknisi aktif dan beban pesanan saat ini.
   * Nama dan identitas pengguna yang sedang berinteraksi.
4. **Adaptive Persona (Penyesuaian Gaya Bahasa Berdasarkan Peran):**
   * **Untuk Pelanggan:** Nada respons ramah, santun, solutif, dan mengedukasi alur pemesanan.
   * **Untuk Staf/Internal:** Menghilangkan basa-basi promosi; hanya menampilkan fakta, angka, dan data langsung.
5. **Mekanisme Fallback Otomatis (Resilience Engine):** Jika kunci API belum dimasukkan, kuota habis, atau koneksi internet terputus, sistem secara otomatis mengalihkan interaksi ke mesin FAQ lokal terstruktur (*Rule-based Fallback*) sehingga pengguna tetap mendapatkan panduan layanan tanpa hambatan *error*.
6. **Penyimpanan Riwayat Percakapan (*Conversation Memory*):** Sesi konsultasi disimpan ke dalam tabel `ai_conversations` dan `ai_messages` di database sehingga riwayat tanya jawab pengguna tetap tersimpan rapi.

---

## 6. Link Video YouTube Demonstrasi Aplikasi

Tautan video presentasi dan demonstrasi alur kerja sistem:

* **Link YouTube:** `[Tempelkan Link Video YouTube Demonstrasi di Sini]` *(Silakan ganti placeholder ini dengan link video demonstrasi kelompok)*

---

## 7. Panduan Instalasi Aplikasi

Ikuti petunjuk langkah demi langkah di bawah ini untuk menginstal dan menjalankan aplikasi pada lingkungan komputer lokal (*localhost*).

### A. Persyaratan Perangkat dan Perangkat Lunak
* **Sistem Operasi:** Windows 10/11, Linux, atau macOS.
* **Web Server:** Apache (disarankan menggunakan paket **XAMPP** atau **Laragon**).
* **PHP Runtime:** PHP versi **8.0 atau lebih tinggi** (disarankan PHP 8.1 / 8.2).
* **Ekstensi PHP Aktif:**
  * `pdo_mysql` (koneksi database)
  * `curl` (komunikasi API Gemini & WhatsApp)
  * `openssl` (keamanan & hashing)
  * `mbstring` (manipulasi string)
* **Database Server:** MySQL / MariaDB versi 10.4 atau lebih baru.
* **Peramban Web (*Browser*):** Google Chrome, Mozilla Firefox, atau Microsoft Edge versi terbaru.
* **Git CLI** (untuk *clone* repositori).

---

### B. Langkah Instalasi Source Code
1. Buka terminal atau Git Bash pada folder root web server Anda:
   * Untuk XAMPP di Windows: `C:\xampp\htdocs\`
2. *Clone* repositori ini dari GitHub:
   ```bash
   git clone https://github.com/dhinirahmawati/proyek-rpl-ADCom.git ADCom
   ```
3. Masuk ke direktori proyek:
   ```bash
   cd ADCom
   ```

---

### C. Konfigurasi Database
1. Pastikan modul **Apache** dan **MySQL** telah dijalankan pada **XAMPP Control Panel**.
2. Buka peramban dan akses antarmuka **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
3. Buat database baru dengan nama:
   ```
   adcom_db
   ```
4. Pilih tab **Import**, lalu pilih file skema SQL yang telah disediakan:
   ```
   database/schema.sql
   ```
5. Klik tombol **Go / Kirim** untuk mengeksekusi impor tabel, relasi, dan data awal (*sample seed data*).
6. *(Opsional)* Jika ingin memastikan seluruh migrasi tabel tambahan diperbarui, jalankan perintah CLI:
   ```bash
   php migrate.php
   ```

---

### D. Konfigurasi Environment Variable (`.env`)
Aplikasi telah dilengkapi arsitektur proteksi konfigurasi rahasia menggunakan file `.env`:

1. Di dalam folder root proyek (`ADCom/`), salin file `.env.example` menjadi `.env`:
   * Pada Windows Command Prompt / PowerShell:
     ```powershell
     copy .env.example .env
     ```
   * Atau pada Git Bash / Linux:
     ```bash
     cp .env.example .env
     ```
2. Buka file `.env` menggunakan editor teks (seperti VS Code atau Notepad), kemudian sesuaikan isinya:
   ```env
   # WhatsApp API Provider Configuration (mock, fonnte, wablas, meta_cloud)
   WHATSAPP_PROVIDER=mock
   WHATSAPP_API_URL=URL_API_DARI_PROVIDER
   WHATSAPP_API_TOKEN=ISI_TOKEN_DI_ENV
   WHATSAPP_SENDER_ID=

   # Google Gemini AI API Key
   # Dapatkan API Key gratis di https://aistudio.google.com/
   AI_API_KEY=ISI_DENGAN_GEMINI_API_KEY_ANDA
   ```
   > **Catatan:**
   > * Jika Anda belum memasukkan `AI_API_KEY`, fitur Asisten AI akan secara otomatis beroperasi dalam mode **Fallback FAQ Lokal** sehingga aplikasi tetap berjalan lancar tanpa *error*.
   > * Jika Anda menggunakan provider WhatsApp `mock`, kode OTP pendaftaran akan ditampilkan di log aplikasi atau layar pengujian untuk kemudahan pengujian lokal.

---

### E. Cara Menjalankan dan Mengakses Aplikasi
1. Pastikan Apache di XAMPP aktif pada port default (`80`).
2. Buka web browser Anda dan akses alamat berikut:
   ```
   http://localhost/ADCom/public
   ```
   *(Atau `http://localhost/ADCom` jika virtual host atau URL rewrite aktif).*

---

### F. Akun Pengujian Default (*Demo Credentials*)
Seluruh kata sandi (*password*) untuk akun bawaan di bawah ini telah disetel default ke: **`password123`**

| Peran (Role) | Alamat Email | Kata Sandi | Kegunaan Pengujian |
| :--- | :--- | :--- | :--- |
| **Administrator** | `kepsek@sekolah.edu` | `password123` | Menguji panel Admin, user management, konfigurasi AI & OTP, dan laporan. |
| **Operator** | `operator@adcom.id` | `password123` | Menguji penugasan teknisi, manajemen layanan, dan pemantauan order. |
| **Teknisi 1** | `agus@teknisi.adcom.id` | `password123` | Menguji penerimaan pekerjaan, pembaruan status, dan upload dokumentasi. |
| **Teknisi 2** | `dina@teknisi.adcom.id` | `password123` | Menguji antrean tugas perbaikan teknisi kedua. |
| **Pelanggan 1** | `ahmad@gmail.com` | `password123` | Menguji konsultasi Chatbot AI, membuat order servis baru, dan melacak status. |
| **Pelanggan 2** | `rini@gmail.com` | `password123` | Menguji pemberian rating ulasan dan riwayat pesanan. |

---

## 8. Lisensi & Hak Cipta
Proyek ini dikembangkan untuk keperluan akademik dan evaluasi Ujian Akhir Semester (UAS) mata kuliah **Rekayasa Perangkat Lunak (RPL)**. Seluruh kode sumber terbuka untuk dipelajari dan dikembangkan lebih lanjut.
