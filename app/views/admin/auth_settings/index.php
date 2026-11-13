<div class="container-fluid px-3 px-lg-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-gradient-primary rounded-pill px-3 py-1">Security & Auth</span>
                <span class="badge bg-secondary-subtle text-secondary border">Role: Administrator</span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-0">Konfigurasi Autentikasi & WhatsApp OTP</h2>
            <p class="text-muted small mb-0">Kelola kebijakan verifikasi nomor telepon, autentikasi dua langkah (2FA), dan integrasi gateway WhatsApp.</p>
        </div>
        <div>
            <a href="<?= BASEURL; ?>/admin" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <div class="row g-4">
        <!-- Left Column: Settings Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-shape icon-shape-sm bg-gradient-primary text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-toggles text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Kebijakan Verifikasi OTP</h5>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <form action="<?= BASEURL; ?>/admin/updateAuthSettings" method="POST">
                        <?= CSRF::input(); ?>

                        <!-- Master Switch -->
                        <div class="p-3 rounded-4 bg-light border mb-4">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0 d-block" for="otp_enabled">
                                        <i class="bi bi-power text-primary me-1"></i>Master Switch OTP WhatsApp
                                    </label>
                                    <small class="text-muted">Aktifkan atau nonaktifkan seluruh modul verifikasi OTP WhatsApp di aplikasi.</small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" role="switch" id="otp_enabled" name="otp_enabled" value="1" <?= (($data['settings']['otp_enabled']['value'] ?? '1') == '1') ? 'checked' : ''; ?> style="width: 2.75rem; height: 1.5rem;">
                            </div>
                        </div>

                        <!-- Scoped Enforcement -->
                        <h6 class="text-uppercase text-xs fw-bold text-muted mb-3" style="letter-spacing: 0.5px;">Proses yang Mewajibkan OTP</h6>

                        <div class="mb-3 p-3 rounded-3 border">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                <div>
                                    <label class="form-check-label fw-semibold text-dark mb-0 d-block" for="otp_required_registration">
                                        <i class="bi bi-person-check text-success me-1"></i>Wajibkan saat Registrasi Customer
                                    </label>
                                    <small class="text-muted">Customer baru wajib memverifikasi nomor WhatsApp sebelum akun aktif.</small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" role="switch" id="otp_required_registration" name="otp_required_registration" value="1" <?= (($data['settings']['otp_required_registration']['value'] ?? '1') == '1') ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <div class="mb-3 p-3 rounded-3 border">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                <div>
                                    <label class="form-check-label fw-semibold text-dark mb-0 d-block" for="otp_required_login">
                                        <i class="bi bi-shield-lock text-warning me-1"></i>Verifikasi Tambahan (2FA) saat Login
                                    </label>
                                    <small class="text-muted">Pengguna dengan nomor WhatsApp terdaftar wajib memasukkan OTP setelah input password.</small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" role="switch" id="otp_required_login" name="otp_required_login" value="1" <?= (($data['settings']['otp_required_login']['value'] ?? '0') == '1') ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <div class="mb-4 p-3 rounded-3 border">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                <div>
                                    <label class="form-check-label fw-semibold text-dark mb-0 d-block" for="otp_required_reset_password">
                                        <i class="bi bi-key text-danger me-1"></i>Wajibkan untuk Pemulihan Akun (Reset Password)
                                    </label>
                                    <small class="text-muted">Pengguna dapat mereset password melalui verifikasi OTP ke nomor WhatsApp terdaftar.</small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" role="switch" id="otp_required_reset_password" name="otp_required_reset_password" value="1" <?= (($data['settings']['otp_required_reset_password']['value'] ?? '1') == '1') ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <!-- Numeric Parameters -->
                        <h6 class="text-uppercase text-xs fw-bold text-muted mb-3" style="letter-spacing: 0.5px;">Parameter Keamanan & Batasan</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="otp_expiry_minutes" class="form-label small fw-semibold text-dark">Masa Berlaku (Menit)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otp_expiry_minutes" name="otp_expiry_minutes" min="1" max="60" required value="<?= htmlspecialchars($data['settings']['otp_expiry_minutes']['value'] ?? '5'); ?>">
                                    <span class="input-group-text small bg-light">Mnt</span>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">Default: 5 menit.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="otp_max_attempts" class="form-label small fw-semibold text-dark">Batas Percobaan Salah</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otp_max_attempts" name="otp_max_attempts" min="1" max="10" required value="<?= htmlspecialchars($data['settings']['otp_max_attempts']['value'] ?? '5'); ?>">
                                    <span class="input-group-text small bg-light">Kali</span>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">Default: 5 kali.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="otp_resend_cooldown_seconds" class="form-label small fw-semibold text-dark">Cooldown Kirim Ulang</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otp_resend_cooldown_seconds" name="otp_resend_cooldown_seconds" min="10" max="300" required value="<?= htmlspecialchars($data['settings']['otp_resend_cooldown_seconds']['value'] ?? '60'); ?>">
                                    <span class="input-group-text small bg-light">Dtk</span>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">Default: 60 detik.</div>
                            </div>
                        </div>

                        <!-- Provider Selection -->
                        <div class="mb-4">
                            <label for="otp_provider" class="form-label small fw-semibold text-dark">Pilihan Adapter Provider WhatsApp</label>
                            <select class="form-select" id="otp_provider" name="otp_provider">
                                <option value="mock" <?= (($data['settings']['otp_provider']['value'] ?? 'mock') === 'mock') ? 'selected' : ''; ?>>Mock / Demo (Simulasi Localhost Pengujian Internal)</option>
                                <option value="fonnte" <?= (($data['settings']['otp_provider']['value'] ?? '') === 'fonnte') ? 'selected' : ''; ?>>Fonnte WhatsApp Gateway</option>
                                <option value="wablas" <?= (($data['settings']['otp_provider']['value'] ?? '') === 'wablas') ? 'selected' : ''; ?>>Wablas WhatsApp Gateway</option>
                                <option value="meta_cloud" <?= (($data['settings']['otp_provider']['value'] ?? '') === 'meta_cloud') ? 'selected' : ''; ?>>Meta WhatsApp Business Platform (Cloud API)</option>
                            </select>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle me-1"></i>Kredensial token tersimpan aman di environment backend (<code>.env</code>) dan tidak ditampilkan ke publik.
                        <!-- Gateway Credentials (Stored securely into .env) -->
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark small mb-3">
                                <i class="bi bi-key-fill text-warning me-1"></i> Kredensial Provider WhatsApp (Disimpan aman ke <code>.env</code>)
                            </h6>
                            
                            <div class="mb-3">
                                <label for="whatsapp_api_token" class="form-label small fw-semibold text-dark">API Token WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-shield-lock text-muted"></i></span>
                                    <input type="password" class="form-control" id="whatsapp_api_token" name="whatsapp_api_token" placeholder="Masukkan token API baru (kosongkan jika tidak ingin mengubah)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleTokenVisibility()"><i class="bi bi-eye" id="toggleTokenIcon"></i></button>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">
                                    Untuk <strong>Fonnte</strong>: Dapatkan token dari menu Device di dashboard <a href="https://fonnte.com" target="_blank" rel="noopener">fonnte.com</a> setelah scan QR. Token tidak ditampilkan kembali ke publik demi keamanan.
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-7">
                                    <label for="whatsapp_api_url" class="form-label small fw-semibold text-dark">Custom API URL (Opsional)</label>
                                    <input type="text" class="form-control" id="whatsapp_api_url" name="whatsapp_api_url" placeholder="Default Fonnte: https://api.fonnte.com/send" value="<?= (defined('WHATSAPP_API_URL') && WHATSAPP_API_URL !== 'URL_API_DARI_PROVIDER' && WHATSAPP_API_URL !== 'https://api.fonnte.com/send') ? htmlspecialchars(WHATSAPP_API_URL) : ''; ?>">
                                </div>
                                <div class="col-md-5">
                                    <label for="whatsapp_sender_id" class="form-label small fw-semibold text-dark">Sender / Phone ID (Opsional)</label>
                                    <input type="text" class="form-control" id="whatsapp_sender_id" name="whatsapp_sender_id" placeholder="Hanya jika dibutuhkan provider" value="<?= (defined('WHATSAPP_SENDER_ID') && WHATSAPP_SENDER_ID !== 'ID_PENGIRIM_JIKA_DIBUTUHKAN') ? htmlspecialchars(WHATSAPP_SENDER_ID) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan Autentikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Provider Status & Safe Test Tool -->
        <div class="col-lg-5">
            <!-- Provider Status Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape icon-shape-sm bg-gradient-success text-white rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-whatsapp text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Status Provider</h5>
                        </div>
                        <span class="badge <?= $data['provider_info']['badge_class'] ?? 'bg-secondary'; ?> rounded-pill px-3 py-1">
                            <?= htmlspecialchars($data['provider_info']['status_text'] ?? 'Unknown'); ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Active Adapter</span>
                            <span class="fw-semibold text-dark small"><?= htmlspecialchars($data['provider_info']['provider_name'] ?? ''); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Token Status</span>
                            <span class="font-monospace text-dark small bg-light px-2 py-0 rounded border">
                                <?= htmlspecialchars($data['provider_info']['masked_token'] ?? ''); ?>
                            </span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Sender ID</span>
                            <span class="fw-semibold text-dark small"><?= htmlspecialchars($data['provider_info']['sender_id'] ?? '-'); ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="text-muted small mb-1">API Endpoint URL</div>
                            <div class="font-monospace text-truncate small bg-light p-2 rounded border" style="font-size: 11px;">
                                <?= htmlspecialchars($data['provider_info']['api_url'] ?? '-'); ?>
                            </div>
                        </li>
                    </ul>

                    <?php if (!empty($data['provider_info']['is_mock'])) : ?>
                        <div class="alert alert-info border-0 rounded-3 mb-0 small">
                            <i class="bi bi-lightbulb-fill text-info me-1"></i>
                            <strong>Mode Demo Aktif:</strong> Kode OTP disimulasikan secara aman di localhost tanpa biaya gateway SMS/WA. Kode verifikasi ditampilkan langsung di halaman OTP untuk kemudahan pengujian.
                        </div>
                    <?php elseif (!$data['provider_info']['is_configured']) : ?>
                        <div class="alert alert-warning border-0 rounded-3 mb-0 small">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            <strong>Kredensial Belum Lengkap:</strong> Untuk mengaktifkan pengiriman WhatsApp nyata, buka file <code>.env</code> di server Anda dan isi nilai <code>WHATSAPP_API_TOKEN</code> dari dashboard provider.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Safe Test Tool Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-shape icon-shape-sm bg-gradient-info text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-send-check text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Uji Koneksi WhatsApp</h5>
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    <p class="text-muted small mb-3">
                        Kirim pesan uji coba ke nomor WhatsApp Anda untuk memastikan gateway terhubung dan berfungsi dengan benar.
                    </p>

                    <form action="<?= BASEURL; ?>/admin/testWhatsApp" method="POST">
                        <?= CSRF::input(); ?>

                        <div class="mb-3">
                            <label for="test_phone" class="form-label small fw-semibold text-dark">Nomor WhatsApp Tujuan Uji Coba</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="tel" class="form-control" id="test_phone" name="test_phone" placeholder="081234567890" required>
                            </div>
                            <div class="form-text text-muted small" style="font-size: 11px;">
                                Masukkan nomor WhatsApp yang dapat Anda periksa.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100 rounded-pill fw-semibold btn-sm py-2">
                            <i class="bi bi-send me-1"></i> Kirim Pesan Uji Coba
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTokenVisibility() {
    const input = document.getElementById('whatsapp_api_token');
    const icon = document.getElementById('toggleTokenIcon');
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
