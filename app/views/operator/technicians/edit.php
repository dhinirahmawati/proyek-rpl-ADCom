<div class="container py-4" style="max-width: 760px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/technicians" class="text-decoration-none text-muted">Data Teknisi</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Edit Teknisi</li>
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
                        <i class="bi bi-tools fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Edit Data Teknisi</h4>
                        <p class="text-white-50 mb-0 small">Perbarui data profil, kata sandi, atau status keaktifan teknisi.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/operator/technicians" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="<?= BASEURL; ?>/operator/updateTechnician" method="POST">
                <input type="hidden" name="id" value="<?= $data['technician']['id']; ?>">

                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-person text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="name" name="name" 
                               value="<?= htmlspecialchars($data['technician']['name']); ?>" required placeholder="Masukkan nama lengkap teknisi">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" 
                               value="<?= htmlspecialchars($data['technician']['email']); ?>" required placeholder="email@adcom.id">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password Baru (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-key text-muted"></i>
                        </span>
                        <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password" 
                               placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePasswordVisibility('password', this)">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Isi hanya jika Anda ingin memperbarui password akun teknisi saat ini.</small>
                </div>

                <!-- Status Aktif Switch dengan Ikon Indikator -->
                <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div id="statusIconBox" class="rounded-circle p-2 d-flex align-items-center justify-content-center <?= $data['technician']['is_active'] ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'; ?>" style="width: 44px; height: 44px;">
                                <i id="statusIcon" class="bi <?= $data['technician']['is_active'] ? 'bi-person-check-fill' : 'bi-person-x-fill'; ?> fs-4"></i>
                            </div>
                            <div>
                                <label class="form-check-label fw-bold d-block mb-0 cursor-pointer" for="is_active">
                                    Status Akun: 
                                    <span id="statusLabel" class="badge <?= $data['technician']['is_active'] ? 'bg-success' : 'bg-danger'; ?> rounded-pill ms-1">
                                        <i class="bi <?= $data['technician']['is_active'] ? 'bi-check-circle' : 'bi-slash-circle'; ?> me-1"></i><?= $data['technician']['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                    </span>
                                </label>
                                <small class="text-muted d-block mt-1">Teknisi aktif dapat menerima dan ditugaskan order baru oleh Operator.</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" id="is_active" name="is_active" value="1" <?= $data['technician']['is_active'] ? 'checked' : ''; ?> onchange="updateStatusIndicator(this)">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/operator/technicians" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Update Data
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

function updateStatusIndicator(checkbox) {
    const iconBox = document.getElementById('statusIconBox');
    const icon = document.getElementById('statusIcon');
    const label = document.getElementById('statusLabel');
    if (checkbox.checked) {
        iconBox.className = 'rounded-circle p-2 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success';
        icon.className = 'bi bi-person-check-fill fs-4';
        label.className = 'badge bg-success rounded-pill ms-1';
        label.innerHTML = '<i class="bi bi-check-circle me-1"></i>Aktif';
    } else {
        iconBox.className = 'rounded-circle p-2 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger';
        icon.className = 'bi bi-person-x-fill fs-4';
        label.className = 'badge bg-danger rounded-pill ms-1';
        label.innerHTML = '<i class="bi bi-slash-circle me-1"></i>Non-Aktif';
    }
}
</script>
