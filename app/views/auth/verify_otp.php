<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <div class="icon-shape bg-gradient-success text-white rounded-circle shadow-success mx-auto mb-3 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-whatsapp fs-2 text-white"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Verifikasi WhatsApp</h4>
                        <p class="text-muted small mb-2">
                            Masukkan 6 digit kode verifikasi yang telah dikirimkan ke nomor WhatsApp:
                        </p>
                        <div class="d-inline-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border">
                            <i class="bi bi-telephone-fill text-success small"></i>
                            <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($data['phone_masked'] ?? ''); ?></span>
                        </div>

                        <?php if (!empty($data['purpose'])) : ?>
                            <div class="mt-2">
                                <?php if ($data['purpose'] === 'registration') : ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Verifikasi Registrasi Akun</span>
                                <?php elseif ($data['purpose'] === 'login') : ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Autentikasi Dua Langkah (2FA)</span>
                                <?php elseif ($data['purpose'] === 'reset_password') : ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Pemulihan / Reset Password</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php Flasher::flash(); ?>

                    <!-- Demo Mode Notice (For local test environments) -->
                    <?php if (isset($_SESSION['debug_last_otp']) && !empty($_SESSION['debug_last_otp']['code'])) : ?>
                        <div class="alert alert-info border-0 rounded-3 shadow-sm d-flex align-items-start gap-2 mb-4">
                            <i class="bi bi-info-circle-fill text-info fs-5 mt-1"></i>
                            <div class="small">
                                <strong>Info Pengujian (Mode Demo):</strong><br>
                                Kode OTP simulasi Anda adalah: <span class="badge bg-primary fs-6 font-monospace px-2 py-1"><?= $_SESSION['debug_last_otp']['code']; ?></span>
                                <div class="text-muted mt-1" style="font-size: 11px;">(Pada integrasi gateway WhatsApp aktif, kode ini otomatis terkirim ke nomor pengguna).</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASEURL; ?>/auth/processVerifyOtp" method="POST" id="otpForm">
                        <?= CSRF::input(); ?>
                        <input type="hidden" name="purpose" value="<?= htmlspecialchars($data['purpose'] ?? 'registration'); ?>">
                        <input type="hidden" name="otp" id="fullOtpInput">

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-center d-block text-dark small mb-3">6 Digit Kode OTP</label>
                            
                            <div class="d-flex justify-content-center gap-2" id="otpContainer">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_1" autofocus required autocomplete="off">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_2" required autocomplete="off">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_3" required autocomplete="off">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_4" required autocomplete="off">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_5" required autocomplete="off">
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" class="form-control text-center fs-4 fw-bold otp-digit rounded-3" style="width: 48px; height: 56px;" name="otp_6" required autocomplete="off">
                            </div>

                            <div class="text-center text-muted small mt-3">
                                <i class="bi bi-clock me-1"></i>Kode berlaku selama <span class="fw-semibold text-dark"><?= $data['expiry_minutes'] ?? 5; ?> menit</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill mb-3" id="btnSubmitOtp">
                            <i class="bi bi-check2-circle me-1"></i> Verifikasi Kode
                        </button>
                    </form>

                    <!-- Resend Section with Live Countdown -->
                    <div class="text-center pt-3 border-top">
                        <div id="countdownBox" class="text-muted small mb-2" style="<?= ($data['cooldown'] ?? 0) > 0 ? '' : 'display: none;'; ?>">
                            Kirim ulang kode dalam <span id="countdownTimer" class="fw-bold text-danger">00:<?= sprintf('%02d', $data['cooldown'] ?? 0); ?></span>
                        </div>

                        <form action="<?= BASEURL; ?>/auth/resendOtp" method="POST" id="resendForm">
                            <?= CSRF::input(); ?>
                            <input type="hidden" name="purpose" value="<?= htmlspecialchars($data['purpose'] ?? 'registration'); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-4 fw-semibold" id="btnResend" <?= ($data['cooldown'] ?? 0) > 0 ? 'disabled' : ''; ?>>
                                <i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang OTP WhatsApp
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <?php if (($data['purpose'] ?? '') === 'registration') : ?>
                            <a href="<?= BASEURL; ?>/auth/register" class="text-muted small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Ubah nomor atau daftar ulang
                            </a>
                        <?php else : ?>
                            <a href="<?= BASEURL; ?>/auth/login" class="text-muted small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-digit');
    const fullOtpInput = document.getElementById('fullOtpInput');
    const otpForm = document.getElementById('otpForm');

    // Auto-focus and navigation for 6 digit boxes
    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Filter non-digits
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
            updateFullOtp();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });

        // Support paste full 6 digit string
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').trim();
            if (pasteData.length === 6) {
                inputs.forEach((inp, idx) => {
                    inp.value = pasteData[idx] || '';
                });
                inputs[5].focus();
                updateFullOtp();
            }
        });
    });

    function updateFullOtp() {
        let code = '';
        inputs.forEach(inp => code += inp.value);
        fullOtpInput.value = code;
    }

    otpForm.addEventListener('submit', function(e) {
        updateFullOtp();
        if (fullOtpInput.value.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
            inputs[0].focus();
        }
    });

    // Cooldown countdown logic
    let remainingSeconds = <?= (int)($data['cooldown'] ?? 0); ?>;
    const countdownBox = document.getElementById('countdownBox');
    const countdownTimer = document.getElementById('countdownTimer');
    const btnResend = document.getElementById('btnResend');

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    }

    if (remainingSeconds > 0) {
        btnResend.disabled = true;
        countdownBox.style.display = 'block';

        const timerInterval = setInterval(function() {
            remainingSeconds--;
            countdownTimer.textContent = formatTime(remainingSeconds);

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                countdownBox.style.display = 'none';
                btnResend.disabled = false;
            }
        }, 1000);
    }
});
</script>
