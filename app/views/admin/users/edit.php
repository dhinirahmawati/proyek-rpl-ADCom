<div class="container py-4 px-md-4" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/admin" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/admin/users" class="text-decoration-none text-muted">Manajemen Pengguna</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Edit Pengguna</li>
        </ol>
    </nav>

    <!-- Flash message -->
    <?php Flasher::flash(); ?>

    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Header Banner -->
        <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-25 p-3 text-info d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-gear fs-3 text-white"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">Edit Pengguna</h4>
                        <p class="text-light mb-0 small opacity-75">Perbarui data, hak akses, atau kata sandi pengguna.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/admin/users" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php if ($data['is_self']): ?>
                <div class="alert alert-info border-0 rounded-3 shadow-sm d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                    <div>
                        <strong>Catatan Akun Aktif:</strong> Anda sedang mengedit akun Anda sendiri. Role Administrator dan status keaktifan akun dikunci untuk mencegah penguncian sistem.
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= BASEURL; ?>/admin/updateUser" method="POST" id="formEditUser">
                <?= CSRF::input(); ?>
                <input type="hidden" name="id" value="<?= $data['user']['id']; ?>">

                <!-- Bagian Informasi Akun -->
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-person-vcard text-primary me-2"></i>Informasi Akun
                </h5>

                <div class="row g-3 mb-4">
                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-start-0" id="name" name="name" 
                                   value="<?= htmlspecialchars($data['user']['name']); ?>" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold text-dark">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control bg-light border-start-0" id="email" name="email" 
                                   value="<?= htmlspecialchars($data['user']['email']); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Bagian Peran (Role) -->
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-shield-lock text-primary me-2"></i>Hak Akses & Peran (Role) <span class="text-danger">*</span>
                </h5>

                <div class="row g-3 mb-4">
                    <!-- Option Admin -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border rounded-3 p-3 role-card cursor-pointer" onclick="selectRole('admin')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_admin" value="admin" 
                                       <?= $data['user']['role'] === 'admin' ? 'checked' : ''; ?> 
                                       <?= $data['is_self'] ? '' : ''; ?> onchange="updateRoleHighlight()">
                                <label class="form-check-label fw-bold text-dark" for="role_admin">
                                    <i class="bi bi-shield-lock-fill text-purple me-1" style="color: #6f42c1;"></i> Admin
                                </label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">Kepala sekolah, monitoring & user manager.</p>
                        </div>
                    </div>

                    <!-- Option Operator -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border rounded-3 p-3 role-card <?= $data['is_self'] ? 'opacity-50' : 'cursor-pointer'; ?>" 
                             onclick="<?= $data['is_self'] ? '' : "selectRole('operator')"; ?>">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_operator" value="operator" 
                                       <?= $data['user']['role'] === 'operator' ? 'checked' : ''; ?> 
                                       <?= $data['is_self'] ? 'disabled' : ''; ?> onchange="updateRoleHighlight()">
                                <label class="form-check-label fw-bold text-dark" for="role_operator">
                                    <i class="bi bi-headset text-primary me-1"></i> Operator
                                </label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">Kelola pesanan, layanan & assign teknisi.</p>
                        </div>
                    </div>

                    <!-- Option Teknisi -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border rounded-3 p-3 role-card <?= $data['is_self'] ? 'opacity-50' : 'cursor-pointer'; ?>" 
                             onclick="<?= $data['is_self'] ? '' : "selectRole('technician')"; ?>">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_technician" value="technician" 
                                       <?= $data['user']['role'] === 'technician' ? 'checked' : ''; ?> 
                                       <?= $data['is_self'] ? 'disabled' : ''; ?> onchange="updateRoleHighlight()">
                                <label class="form-check-label fw-bold text-dark" for="role_technician">
                                    <i class="bi bi-tools text-warning me-1"></i> Teknisi
                                </label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">Siswa/teknisi pelaksana servis & update order.</p>
                        </div>
                    </div>

                    <!-- Option Customer -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border rounded-3 p-3 role-card <?= $data['is_self'] ? 'opacity-50' : 'cursor-pointer'; ?>" 
                             onclick="<?= $data['is_self'] ? '' : "selectRole('customer')"; ?>">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_customer" value="customer" 
                                       <?= $data['user']['role'] === 'customer' ? 'checked' : ''; ?> 
                                       <?= $data['is_self'] ? 'disabled' : ''; ?> onchange="updateRoleHighlight()">
                                <label class="form-check-label fw-bold text-dark" for="role_customer">
                                    <i class="bi bi-person text-info me-1"></i> Pelanggan
                                </label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">Klien umum, membuat order dan konsultasi AI.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian Keamanan (Password Baru - Opsional) -->
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-key text-primary me-2"></i>Ubah Kata Sandi (Opsional)
                </h5>

                <div class="alert alert-secondary py-2 small mb-3 border-0 bg-light">
                    <i class="bi bi-info-circle me-1"></i> Biarkan kolom password kosong jika tidak ingin mengubah password saat ini.
                </div>

                <div class="row g-3 mb-4">
                    <!-- Password Baru -->
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold text-dark">
                            Password Baru
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control bg-light border-start-0 border-end-0" id="password" name="password" 
                                   placeholder="Kosongkan jika tidak diubah" minlength="6">
                            <button class="btn btn-light border border-start-0" type="button" onclick="togglePasswordVisibility('password', this)">
                                <i class="bi bi-eye text-muted"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label fw-semibold text-dark">
                            Konfirmasi Password Baru
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-shield-check text-muted"></i>
                            </span>
                            <input type="password" class="form-control bg-light border-start-0 border-end-0" id="confirm_password" name="confirm_password" 
                                   placeholder="Ulangi password baru jika diubah" minlength="6">
                            <button class="btn btn-light border border-start-0" type="button" onclick="togglePasswordVisibility('confirm_password', this)">
                                <i class="bi bi-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Aktif Switch -->
                <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="form-check form-switch d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 ms-0 mt-0" type="checkbox" id="is_active" name="is_active" value="1" 
                               <?= $data['user']['is_active'] ? 'checked' : ''; ?> 
                               <?= $data['is_self'] ? 'disabled' : ''; ?>>
                        <label class="form-check-label" for="is_active">
                            <span class="fw-bold text-dark d-block">Status Akun Aktif</span>
                            <span class="text-muted small">
                                <?= $data['is_self'] ? 'Akun login aktif Anda tidak dapat dinonaktifkan.' : 'Jika dinonaktifkan, pengguna tidak dapat masuk ke sistem.'; ?>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/admin/users" class="btn btn-light rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .role-card { transition: all 0.2s ease; }
    .role-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .role-card.selected { border-color: #0d6efd !important; background-color: #f0f7ff; }
</style>

<script>
function selectRole(role) {
    const radio = document.getElementById('role_' + role);
    if (radio && !radio.disabled) {
        radio.checked = true;
        updateRoleHighlight();
    }
}

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

function updateRoleHighlight() {
    document.querySelectorAll('.role-card').forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });
}

// Initial highlight
document.addEventListener('DOMContentLoaded', updateRoleHighlight);
</script>
