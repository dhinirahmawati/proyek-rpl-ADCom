<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="icon-shape bg-gradient-success text-white rounded-circle shadow-success mx-auto mb-3 d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-shield-check fs-3 text-white"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Buat Password Baru</h4>
                        <p class="text-muted small">Verifikasi WhatsApp berhasil. Silakan tentukan password baru yang aman untuk akun Anda.</p>
                    </div>

                    <?php Flasher::flash(); ?>

                    <form action="<?= BASEURL; ?>/auth/processResetPassword" method="POST">
                        <?= CSRF::input(); ?>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark small">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" required minlength="6" autofocus placeholder="Minimal 6 karakter">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label fw-semibold text-dark small">Ulangi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="password_confirm" name="password_confirm" required minlength="6" placeholder="Konfirmasi password baru">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill mb-3">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Password Baru
                        </button>
                    </form>

                    <div class="text-center mt-3 pt-2 border-top">
                        <a href="<?= BASEURL; ?>/auth/login" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Batal dan kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
