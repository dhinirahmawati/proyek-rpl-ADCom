<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="icon-shape bg-gradient-primary text-white rounded-circle shadow-primary mx-auto mb-3 d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-person-plus-fill fs-4 text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Daftar Akun Customer</h3>
                        <p class="text-muted small">Lengkapi data untuk pendaftaran layanan ADCom Jasa</p>
                    </div>
                    
                    <?php Flasher::flash(); ?>

                    <form action="<?= BASEURL; ?>/auth/processRegister" method="POST" id="registerForm">
                        <?= CSRF::input(); ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold text-dark small">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="name" name="name" required autofocus placeholder="Contoh: Budi Santoso" value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-dark small">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="budi@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label fw-semibold text-dark small d-flex justify-content-between">
                                <span><i class="bi bi-whatsapp text-success me-1"></i>Nomor WhatsApp Aktif</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">Verifikasi OTP</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0" id="phone_number" name="phone_number" required placeholder="081234567890" value="<?= htmlspecialchars($_POST['phone_number'] ?? ''); ?>">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-shield-check text-success me-1"></i>Kode OTP 6 digit akan dikirimkan ke nomor ini untuk verifikasi.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label fw-semibold text-dark small">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="password_confirm" name="password_confirm" required minlength="6" placeholder="Ulangi password Anda">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill">
                            <i class="bi bi-arrow-right-circle me-1"></i> Daftar & Verifikasi WhatsApp
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-2 border-top">
                        <p class="text-muted small mb-0">Sudah punya akun? <a href="<?= BASEURL; ?>/auth/login" class="text-decoration-none fw-bold text-primary">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
