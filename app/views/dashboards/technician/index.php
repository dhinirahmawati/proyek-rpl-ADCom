<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $data['judul']; ?></h1>
        <a href="<?= BASEURL; ?>/chatHistory" class="btn btn-sm btn-outline-danger shadow-sm rounded-pill px-3 fw-bold">
            <i class="bi bi-chat-dots-fill me-1"></i> Pantau Keluhan Customer (Chatbot)
        </a>
    </div>

    <!-- Statistik Pekerjaan -->
    <div class="row">
        <!-- Active Jobs Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pekerjaan Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['active']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Jobs Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pekerjaan Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['completed']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jobs Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pekerjaan (Riwayat)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $data['stats']['total']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Jobs List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Tugas Aktif (Perlu Dikerjakan)</h6>
            <a href="<?= BASEURL; ?>/technician/jobs" class="btn btn-sm btn-outline-primary">Lihat Semua Riwayat</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Layanan</th>
                            <th>Pelanggan</th>
                            <th>Deskripsi Masalah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach( $data['active_jobs'] as $job ) : ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td class="fw-bold"><?= $job['service_name']; ?></td>
                            <td><?= $job['customer_name']; ?></td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= $job['problem_description']; ?>">
                                    <?= $job['problem_description']; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $badgeClass = 'bg-info text-dark';
                                    if ($job['status'] == 'processing') $badgeClass = 'bg-primary';
                                ?>
                                <span class="badge <?= $badgeClass; ?> rounded-pill px-3 py-2 text-uppercase">
                                    <?= $job['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASEURL; ?>/technician/detail/<?= $job['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($data['active_jobs'])) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard-check fa-3x mb-3 d-block text-gray-300"></i>
                                Tidak ada tugas aktif saat ini.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
