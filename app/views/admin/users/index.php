<?php
$roleLabels = [
    'all' => 'Semua Pengguna',
    'admin' => 'Administrator (Kepsek)',
    'operator' => 'Operator',
    'technician' => 'Teknisi TKJ',
    'customer' => 'Pelanggan',
    'inactive' => 'Pengguna Non-Aktif'
];
$activeRoleLabel = $roleLabels[$data['current_role']] ?? 'Semua Pengguna';
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/admin" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Manajemen Pengguna</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark">Manajemen Pengguna</h2>
            <p class="text-muted mb-0">Kelola hak akses, status, dan data seluruh pengguna sistem ADCom.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASEURL; ?>/admin" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <a href="<?= BASEURL; ?>/admin/createUser" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Flash Notification -->
    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <!-- Metric / Stats Cards (Berfungsi langsung sebagai Tombol Filter Kategori Pengguna) -->
    <div class="row g-3 mb-4">
        <!-- Total / Semua Pengguna -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=all<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'all' ? 'active-all' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Total Pengguna</span>
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-dark"><?= $data['user_stats']['total']; ?></h3>
                                <?php if ($data['current_role'] === 'all'): ?>
                                    <span class="badge bg-primary rounded-pill small py-1 px-2"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small"><?= $data['user_stats']['active']; ?> aktif</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Administrator -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=admin<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'admin' ? 'active-admin' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Admin (Kepsek)</span>
                            <div class="rounded-circle text-white p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #6f42c1;">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0" style="color: #6f42c1;"><?= $data['user_stats']['admin']; ?></h3>
                                <?php if ($data['current_role'] === 'admin'): ?>
                                    <span class="badge rounded-pill small py-1 px-2 text-white" style="background:#6f42c1;"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">Superuser</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Operator -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=operator<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'operator' ? 'active-operator' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Operator</span>
                            <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-headset"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-info"><?= $data['user_stats']['operator']; ?></h3>
                                <?php if ($data['current_role'] === 'operator'): ?>
                                    <span class="badge bg-info text-white rounded-pill small py-1 px-2"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">Pengelola pesanan</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Teknisi -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=technician<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'technician' ? 'active-technician' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Teknisi TKJ</span>
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-warning"><?= $data['user_stats']['technician']; ?></h3>
                                <?php if ($data['current_role'] === 'technician'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill small py-1 px-2"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">Eksekutor lapangan</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Pelanggan / Customer -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=customer<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'customer' ? 'active-customer' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Pelanggan</span>
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-success"><?= $data['user_stats']['customer']; ?></h3>
                                <?php if ($data['current_role'] === 'customer'): ?>
                                    <span class="badge bg-success rounded-pill small py-1 px-2"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">Customer terdaftar</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Status Akun Non-aktif -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= BASEURL; ?>/admin/users?role=inactive<?= !empty($data['keyword']) ? '&q=' . urlencode($data['keyword']) : ''; ?>" 
               class="text-decoration-none d-block h-100">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-filter <?= $data['current_role'] === 'inactive' ? 'active-inactive' : ''; ?>">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Non-Aktif</span>
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-x-fill"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="fw-bold mb-0 text-danger"><?= $data['user_stats']['inactive']; ?></h3>
                                <?php if ($data['current_role'] === 'inactive'): ?>
                                    <span class="badge bg-danger rounded-pill small py-1 px-2"><i class="bi bi-check-lg"></i> Aktif</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">Dinonaktifkan</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Info Kategori & Jumlah Data -->
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <span>Daftar Pengguna</span>
                        <span class="badge rounded-pill bg-light text-dark border small fw-normal">
                            <?= count($data['users']); ?> akun ditemukan
                        </span>
                    </h5>
                    <div class="d-flex align-items-center gap-2 small">
                        <span class="text-muted">Kategori aktif:</span>
                        <span class="badge bg-dark rounded-pill px-2 py-1"><?= $activeRoleLabel; ?></span>
                        <?php if ($data['current_role'] !== 'all' || !empty($data['keyword'])): ?>
                            <a href="<?= BASEURL; ?>/admin/users" class="text-decoration-none text-danger small fw-semibold ms-1">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Search Box -->
                <div style="min-width: 280px; max-width: 420px;" class="flex-grow-1 flex-md-grow-0">
                    <form action="<?= BASEURL; ?>/admin/users" method="GET" class="d-flex gap-2">
                        <?php if ($data['current_role'] !== 'all'): ?>
                            <input type="hidden" name="role" value="<?= htmlspecialchars($data['current_role']); ?>">
                        <?php endif; ?>
                        <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                            <span class="input-group-text bg-white border-0 ps-3">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="q" class="form-control border-0 ps-1" 
                                   placeholder="Cari nama atau email..." 
                                   value="<?= htmlspecialchars($data['keyword']); ?>">
                            <?php if (!empty($data['keyword'])): ?>
                                <a href="<?= BASEURL; ?>/admin/users<?= $data['current_role'] !== 'all' ? '?role=' . $data['current_role'] : ''; ?>" 
                                   class="btn btn-white border-0 text-muted d-flex align-items-center" title="Hapus teks pencarian">
                                    <i class="bi bi-x-circle-fill text-secondary"></i>
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-primary px-4 fw-semibold border-0" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body px-4 py-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col" style="width: 50px;" class="ps-3">No</th>
                            <th scope="col">Pengguna</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status Akun</th>
                            <th scope="col">Terdaftar Pada</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['users'])) : ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4 text-muted">
                                        <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold">Tidak ada pengguna ditemukan</h5>
                                        <p class="small mb-3">Tidak ada data yang cocok dengan kriteria filter atau pencarian Anda.</p>
                                        <a href="<?= BASEURL; ?>/admin/users" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Reset Filter
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php 
                            $no = 1; 
                            foreach ($data['users'] as $user) : 
                                $isSelf = ($user['id'] == $_SESSION['user_id']);
                                // Initials for avatar
                                $words = explode(' ', trim($user['name']));
                                $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                if (empty($initials)) $initials = 'U';

                                // Role appearance styling
                                $roleBadgeClass = '';
                                $roleBadgeStyle = '';
                                $roleIcon = '';
                                $avatarBg = '';
                                switch ($user['role']) {
                                    case 'admin':
                                        $roleBadgeClass = 'text-white';
                                        $roleBadgeStyle = 'background-color: #6f42c1;';
                                        $roleIcon = 'bi-shield-lock-fill';
                                        $avatarBg = 'background: linear-gradient(135deg, #6f42c1, #a17fe0);';
                                        $roleName = 'Administrator';
                                        break;
                                    case 'operator':
                                        $roleBadgeClass = 'bg-primary text-white';
                                        $roleIcon = 'bi-headset';
                                        $avatarBg = 'background: linear-gradient(135deg, #0d6efd, #0dcaf0);';
                                        $roleName = 'Operator';
                                        break;
                                    case 'technician':
                                        $roleBadgeClass = 'bg-warning text-dark';
                                        $roleIcon = 'bi-tools';
                                        $avatarBg = 'background: linear-gradient(135deg, #ffc107, #fd7e14);';
                                        $roleName = 'Teknisi';
                                        break;
                                    case 'customer':
                                    default:
                                        $roleBadgeClass = 'bg-info bg-opacity-25 text-info-emphasis border border-info-subtle';
                                        $roleIcon = 'bi-person';
                                        $avatarBg = 'background: linear-gradient(135deg, #20c997, #0dcaf0);';
                                        $roleName = 'Pelanggan';
                                        break;
                                }
                            ?>
                            <tr class="<?= $isSelf ? 'table-primary bg-opacity-10' : ''; ?>">
                                <td class="ps-3 text-muted fw-semibold"><?= $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Avatar Circle -->
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm fw-bold flex-shrink-0" 
                                             style="width: 44px; height: 44px; font-size: 0.95rem; <?= $avatarBg; ?>">
                                            <?= $initials; ?>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($user['name']); ?></span>
                                                <?php if ($isSelf): ?>
                                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle small py-1 px-2">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Anda
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 <?= $roleBadgeClass; ?>" style="<?= $roleBadgeStyle; ?>">
                                        <i class="bi <?= $roleIcon; ?> me-1"></i><?= $roleName; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i>Non-Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= !empty($user['created_at']) ? date('d M Y, H:i', strtotime($user['created_at'])) : '-'; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden" role="group">
                                        <!-- Edit Button -->
                                        <a href="<?= BASEURL; ?>/admin/editUser/<?= $user['id']; ?>" 
                                           class="btn btn-sm btn-light border-end" 
                                           title="Edit Data Pengguna">
                                            <i class="bi bi-pencil text-primary"></i> Edit
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <?php if ($isSelf): ?>
                                            <button type="button" class="btn btn-sm btn-light border-end text-muted" disabled title="Tidak dapat menonaktifkan akun sendiri">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <?php if ($user['is_active']): ?>
                                                <a href="<?= BASEURL; ?>/admin/toggleUser/<?= $user['id']; ?>/0" 
                                                   class="btn btn-sm btn-light border-end text-warning" 
                                                   onclick="return confirm('Nonaktifkan akun <?= htmlspecialchars(addslashes($user['name'])); ?>? Pengguna tidak akan bisa login.');"
                                                   title="Nonaktifkan Akun">
                                                    <i class="bi bi-toggle-on text-success fs-6"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASEURL; ?>/admin/toggleUser/<?= $user['id']; ?>/1" 
                                                   class="btn btn-sm btn-light border-end text-success" 
                                                   onclick="return confirm('Aktifkan kembali akun <?= htmlspecialchars(addslashes($user['name'])); ?>?');"
                                                   title="Aktifkan Akun">
                                                    <i class="bi bi-toggle-off text-muted fs-6"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Delete Button (Trigger Modal) -->
                                        <?php if ($isSelf): ?>
                                            <button type="button" class="btn btn-sm btn-light text-muted" disabled title="Tidak dapat menghapus akun sendiri">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-light text-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?= $user['id']; ?>" 
                                                    title="Hapus Pengguna">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Delete Confirmation Modal for each user -->
                                    <?php if (!$isSelf): ?>
                                    <div class="modal fade" id="deleteModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $user['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-4 text-start">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel<?= $user['id']; ?>">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Pengguna
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="mb-2">Apakah Anda yakin ingin menghapus akun pengguna berikut secara permanen?</p>
                                                    <div class="p-3 bg-light rounded-3 mb-3 border">
                                                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($user['name']); ?></div>
                                                        <div class="text-muted small"><?= htmlspecialchars($user['email']); ?> &bull; <span class="badge <?= $roleBadgeClass; ?>" style="<?= $roleBadgeStyle; ?>"><?= $roleName; ?></span></div>
                                                    </div>
                                                    <div class="alert alert-warning py-2 small mb-0">
                                                        <i class="bi bi-info-circle me-1"></i> Data riwayat yang terhubung dengan akun ini mungkin akan terpengaruh. Anda juga dapat memilih opsi <strong>Non-aktifkan</strong> jika ingin mempertahankan data historis.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <a href="<?= BASEURL; ?>/admin/deleteUser/<?= $user['id']; ?>" class="btn btn-danger rounded-pill px-4 fw-bold">
                                                        <i class="bi bi-trash me-1"></i> Ya, Hapus Pengguna
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3 px-4 text-muted small d-flex justify-content-between align-items-center">
            <div>
                Menampilkan <strong><?= count($data['users']); ?></strong> pengguna 
                (Kategori: <strong><?= $activeRoleLabel; ?></strong>)
            </div>
            <div>
                <a href="<?= BASEURL; ?>/admin/createUser" class="text-decoration-none fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Pengguna Baru
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card-filter {
    transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
    cursor: pointer;
    border: 2px solid transparent !important;
}
.card-filter:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
}
.card-filter.active-all {
    border-color: #0d6efd !important;
    background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%) !important;
}
.card-filter.active-admin {
    border-color: #6f42c1 !important;
    background: linear-gradient(180deg, #f7f3ff 0%, #ffffff 100%) !important;
}
.card-filter.active-operator {
    border-color: #0dcaf0 !important;
    background: linear-gradient(180deg, #f0fbff 0%, #ffffff 100%) !important;
}
.card-filter.active-technician {
    border-color: #ffc107 !important;
    background: linear-gradient(180deg, #fffdf5 0%, #ffffff 100%) !important;
}
.card-filter.active-customer {
    border-color: #198754 !important;
    background: linear-gradient(180deg, #f2fbf6 0%, #ffffff 100%) !important;
}
.card-filter.active-inactive {
    border-color: #dc3545 !important;
    background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%) !important;
}
</style>
