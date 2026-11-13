<div class="container py-4" style="max-width: 760px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/technicians" class="text-decoration-none text-muted">Data Teknisi</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Tambah Teknisi</li>
        </ol>
    </nav>

    <!-- Flash message -->
    <?php Flasher::flash(); ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Header Banner -->
        <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-plus-fill fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Tambah Teknisi Baru</h4>
                        <p class="text-white-50 mb-0 small">Daftarkan akun teknisi untuk mengerjakan pesanan servis.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/operator/technicians" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="<?= BASEURL; ?>/operator/storeTechnician" method="POST">
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-person text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="name" name="name" required placeholder="Masukkan nama lengkap teknisi" autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="email@contoh.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-key text-muted"></i>
                        </span>
                        <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password" required placeholder="Password minimal 6 karakter" minlength="6">
                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePasswordVisibility('password', this)">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Minimal 6 karakter kombinasi huruf dan angka.</small>
                </div>

                <!-- Status Aktif Switch -->
                <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="form-check form-switch d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 ms-0 mt-0" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            <span class="fw-bold d-block">Status Teknisi Aktif</span>
                            <span class="text-muted small">Jika aktif, teknisi langsung siap menerima penugasan servis.</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/operator/technicians" class="btn btn-light rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
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
