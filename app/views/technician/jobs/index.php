<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0 text-gray-800"><?= $data['judul']; ?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Layanan</th>
                            <th>Pelanggan</th>
                            <th>Tgl Ditugaskan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach( $data['jobs'] as $job ) : ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td class="fw-bold text-primary"><?= $job['service_name']; ?></td>
                            <td><?= $job['customer_name']; ?></td>
                            <td><?= date('d M Y', strtotime($job['updated_at'])); ?></td>
                            <td>
                                <?php 
                                    $badgeClass = 'bg-secondary';
                                    if ($job['status'] == 'assigned') $badgeClass = 'bg-info text-dark';
                                    else if ($job['status'] == 'processing') $badgeClass = 'bg-primary';
                                    else if ($job['status'] == 'completed') $badgeClass = 'bg-success';
                                ?>
                                <span class="badge <?= $badgeClass; ?> rounded-pill px-3 py-2 text-uppercase">
                                    <?= $job['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASEURL; ?>/technician/detail/<?= $job['id']; ?>" class="btn btn-sm btn-primary shadow-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($data['jobs'])) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pekerjaan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
