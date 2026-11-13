<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="icon-shape icon-shape-lg bg-gradient-primary shadow-primary text-white rounded-circle mx-auto mb-3">
                            <i class="ni ni-key-25 text-white fs-3"></i>
                        </div>
                        <h3 class="fw-bolder tracking-tight mb-1 text-dark">Login ADCom Jasa</h3>
                        <p class="text-muted small">Masukkan kredensial akun Anda untuk mengakses sistem</p>
                    </div>
                    
                    <?php Flasher::flash(); ?>

                    <form action="<?= BASEURL; ?>/auth/processLogin" method="POST">
                        <?= CSRF::input(); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label text-xs text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Nama Pengguna</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="name" name="name" required autofocus placeholder="Masukkan nama pengguna">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-xs text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="contoh: budi@gmail.com">
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label text-xs text-uppercase fw-bold text-muted mb-0" style="letter-spacing: 0.5px;">Password</label>
                                <a href="<?= BASEURL; ?>/auth/forgotPassword" class="text-xs text-decoration-none text-primary fw-semibold">Lupa Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="Masukkan password Anda">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow">
                            <i class="ni ni-curved-next me-2"></i>Masuk Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-2 border-top">
                        <p class="text-muted small mb-0">Belum punya akun? <a href="<?= BASEURL; ?>/auth/register" class="text-decoration-none fw-bold text-primary">Daftar sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
