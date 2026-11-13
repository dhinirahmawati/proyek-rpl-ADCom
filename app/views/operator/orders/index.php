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
                            <th>Tgl Pesan</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach( $data['orders'] as $order ) : ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td class="fw-bold text-primary"><?= $order['service_name']; ?></td>
                            <td><?= $order['customer_name']; ?></td>
                            <td><?= date('d M Y, H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <?php 
                                    $badgeClass = 'bg-secondary';
                                    if ($order['status'] == 'pending') $badgeClass = 'bg-warning text-dark';
                                    else if ($order['status'] == 'assigned') $badgeClass = 'bg-info text-dark';
                                    else if ($order['status'] == 'processing') $badgeClass = 'bg-primary';
                                    else if ($order['status'] == 'completed') $badgeClass = 'bg-success';
                                    else if ($order['status'] == 'cancelled') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass; ?> rounded-pill px-3 py-2 text-uppercase">
                                    <?= $order['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($order['technician_name']) : ?>
                                    <span class="text-dark fw-bold"><i class="fas fa-user-cog text-muted me-1"></i> <?= $order['technician_name']; ?></span>
                                <?php else : ?>
                                    <span class="text-muted fst-italic">Belum Ditugaskan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($order['status'] == 'pending') : ?>
                                    <a href="<?= BASEURL; ?>/operator/assignTechnician/<?= $order['id']; ?>" class="btn btn-sm btn-primary shadow-sm" title="Tugaskan Teknisi">
                                        <i class="fas fa-user-plus me-1"></i> Tugaskan
                                    </a>
                                <?php elseif(in_array($order['status'], ['assigned', 'processing'])) : ?>
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <?php if($order['status'] == 'assigned') : ?>
                                            <a href="<?= BASEURL; ?>/operator/assignTechnician/<?= $order['id']; ?>" class="btn btn-sm btn-primary shadow-sm" title="Ubah Penugasan Teknisi">
                                                <i class="fas fa-user-edit me-1"></i> Tugaskan
                                            </a>
                                        <?php else : ?>
                                            <span class="badge bg-light text-secondary border px-2 py-2 small" title="Pekerjaan sedang dikerjakan oleh teknisi">
                                                <i class="fas fa-tools text-primary me-1"></i> Sedang Dikerjakan
                                            </span>
                                        <?php endif; ?>
                                        
                                        <a href="<?= BASEURL; ?>/operator/completeOrder/<?= $order['id']; ?>" 
                                           class="btn btn-sm btn-success shadow-sm" 
                                           title="Tandai Pekerjaan Selesai"
                                           onclick="return confirm('Apakah Anda yakin ingin menandai pesanan #<?= $order['id']; ?> (<?= htmlspecialchars(addslashes($order['service_name'])); ?>) sebagai SELESAI?');">
                                            <i class="fas fa-check-circle me-1"></i> Selesaikan
                                        </a>
                                    </div>
                                <?php elseif(in_array($order['status'], ['completed', 'cancelled'])) : ?>
                                    <a href="<?= BASEURL; ?>/operator/deleteOrder/<?= $order['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger shadow-sm" 
                                       title="Hapus Pesanan Selesai / Batal"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan #<?= $order['id']; ?> (<?= htmlspecialchars(addslashes($order['service_name'])); ?>)?');">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($data['orders'])) : ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada order.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
