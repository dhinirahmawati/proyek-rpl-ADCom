<div class="container-fluid px-3 px-lg-4 mt-4 mb-5">
    <!-- Top Flash Alert -->
    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <!-- Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center position-relative">
                    <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 mb-3">
                        <i class="bi bi-shield-check me-1"></i> Panel Administrator
                    </span>
                    <h2 class="fw-bold mb-2 display-6">Dashboard Kepala Sekolah</h2>
                    <p class="text-light lead mb-0 opacity-75">Monitoring performa, kepuasan pelanggan, dan tata kelola akun Unit Bisnis Siswa TKJ.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Pengguna Sistem -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people-fill text-primary me-2"></i>Statistik Pengguna Sistem
                </h4>
                <a href="<?= BASEURL; ?>/admin/users" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    Kelola Pengguna <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-3">
                <!-- Total Pengguna -->
                <div class="col-6 col-md-4 col-xl">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 stat-hover">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Pengguna</span>
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-0 text-dark"><?= $data['user_stats']['total'] ?? 0; ?></h2>
                            <span class="text-success small fw-semibold">
                                <i class="bi bi-check-circle-fill"></i> <?= $data['user_stats']['active'] ?? 0; ?> aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Admin -->
                <div class="col-6 col-md-4 col-xl">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 stat-hover">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Admin (Kepsek)</span>
                                <div class="rounded-circle text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #6f42c1;">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-0" style="color: #6f42c1;"><?= $data['user_stats']['admin'] ?? 0; ?></h2>
                            <span class="text-muted small">Superuser</span>
                        </div>
                    </div>
                </div>

                <!-- Operator -->
                <div class="col-6 col-md-4 col-xl">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 stat-hover">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Operator</span>
                                <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-headset"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-0 text-info"><?= $data['user_stats']['operator'] ?? 0; ?></h2>
                            <span class="text-muted small">Manajemen Pesanan</span>
                        </div>
                    </div>
                </div>

                <!-- Teknisi -->
                <div class="col-6 col-md-4 col-xl">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 stat-hover">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Teknisi Siswa</span>
                                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-tools"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-0 text-warning"><?= $data['user_stats']['technician'] ?? 0; ?></h2>
                            <span class="text-muted small">Unit Kerja Lapangan</span>
                        </div>
                    </div>
                </div>

                <!-- Pelanggan -->
                <div class="col-6 col-md-4 col-xl">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 stat-hover">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Pelanggan</span>
                                <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-0 text-success"><?= $data['user_stats']['customer'] ?? 0; ?></h2>
                            <span class="text-muted small">Customer Terdaftar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kepuasan Customer -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-3 text-dark">
                <i class="bi bi-star-fill text-warning me-2"></i>Statistik Kepuasan Pelanggan
            </h4>
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Rata-rata Rating -->
                        <div class="col-md-4 text-center border-end mb-4 mb-md-0">
                            <h1 class="display-1 fw-bold text-dark mb-0">
                                <?= number_format($data['satisfaction_stats']['average_rating'], 1); ?>
                            </h1>
                            <div class="text-warning mb-2" style="font-size: 1.5rem;">
                                <?php 
                                $avg = round($data['satisfaction_stats']['average_rating']);
                                for($i=1; $i<=5; $i++): 
                                ?>
                                    <i class="bi bi-star<?= $i <= $avg ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bi bi-person-fill"></i> Total <?= $data['satisfaction_stats']['total_reviews']; ?> Ulasan
                            </p>
                        </div>
                        
                        <!-- Distribusi Rating -->
                        <div class="col-md-8">
                            <?php 
                            $total = $data['satisfaction_stats']['total_reviews'] > 0 ? $data['satisfaction_stats']['total_reviews'] : 1;
                            for($i=5; $i>=1; $i--): 
                                $count = $data['satisfaction_stats']['distribution'][$i] ?? 0;
                                $percentage = ($count / $total) * 100;
                            ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-nowrap me-3" style="width: 45px;">
                                    <?= $i; ?> <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percentage; ?>%;" aria-valuenow="<?= $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="ms-3 text-muted" style="width: 40px; text-align: right;">
                                    <?= $count; ?>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links / Aksi Cepat -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-3 text-dark">Aksi Cepat Administrator</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?= BASEURL; ?>/report" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-graph-up me-1"></i> Lihat Laporan & Monitoring Lengkap
                        </a>
                        <a href="<?= BASEURL; ?>/admin/users" class="btn btn-outline-dark btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-people me-1"></i> Manajemen Pengguna
                        </a>
                        <a href="<?= BASEURL; ?>/admin/authSettings" class="btn btn-outline-warning btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-shield-lock me-1"></i> Konfigurasi Autentikasi (OTP)
                        </a>
                        <a href="<?= BASEURL; ?>/aiConfig" class="btn btn-outline-info btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-robot me-1"></i> Konfigurasi AI Chatbot
                        </a>
                        <a href="<?= BASEURL; ?>/chatHistory" class="btn btn-outline-danger btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-chat-dots-fill me-1"></i> Riwayat Chat &amp; Keluhan Customer
                        </a>
                        <a href="<?= BASEURL; ?>/admin/createUser" class="btn btn-outline-primary btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#settingsThemeModal">
                            <i class="bi bi-palette me-1"></i> Pengaturan Tema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}
</style>
