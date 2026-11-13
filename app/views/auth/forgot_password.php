<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="icon-shape bg-gradient-warning text-white rounded-circle shadow-warning mx-auto mb-3 d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-key-fill fs-4 text-white"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Lupa Password</h4>
                        <p class="text-muted small">Masukkan email atau nomor WhatsApp yang terdaftar untuk menerima kode verifikasi OTP pemulihan akun</p>
                    </div>

                    <?php Flasher::flash(); ?>

                    <form action="<?= BASEURL; ?>/auth/processForgotPassword" method="POST">
                        <?= CSRF::input(); ?>
                        
                        <div class="mb-4">
                            <label for="identifier" class="form-label fw-semibold text-dark small">Email atau Nomor WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="identifier" name="identifier" required autofocus placeholder="contoh: budi@gmail.com atau 081234567890">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-shield-lock me-1"></i>Kode OTP 6 digit akan dikirimkan ke nomor WhatsApp yang terdaftar.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill mb-3">
                            <i class="bi bi-send me-1"></i> Kirim Kode OTP WhatsApp
                        </button>
                    </form>

                    <div class="text-center mt-3 pt-2 border-top">
                        <a href="<?= BASEURL; ?>/auth/login" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
