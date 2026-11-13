<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold">Riwayat Pesanan Saya</h2>
            <a href="<?= BASEURL; ?>/customer" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">ID Pesanan</th>
                                    <th class="py-3">Layanan</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Tanggal Dibuat</th>
                                    <th class="px-4 py-3 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['orders'])) : ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Anda belum memiliki riwayat pesanan.</td>
                                </tr>
                                <?php else : ?>
                                    <?php foreach($data['orders'] as $order) : ?>
                                    <tr>
                                        <td class="px-4 fw-bold">#<?= $order['id']; ?></td>
                                        <td><?= $order['service_name']; ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'bg-secondary';
                                            $statusText = ucfirst($order['status']);
                                            if ($order['status'] == 'completed') {
                                                $badgeClass = 'bg-success';
                                            } else if ($order['status'] == 'processing') {
                                                $badgeClass = 'bg-primary';
                                            } else if ($order['status'] == 'assigned') {
                                                $badgeClass = 'bg-info';
                                            } else if ($order['status'] == 'cancelled') {
                                                $badgeClass = 'bg-danger';
                                            } else {
                                                $badgeClass = 'bg-warning text-dark';
                                            }
                                            ?>
                                            <span class="badge rounded-pill <?= $badgeClass; ?> px-3 py-2">
                                                <?= $statusText; ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td class="px-4 text-end text-nowrap">
                                            <a href="<?= BASEURL; ?>/customer/orderDetail/<?= $order['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Detail</a>
                                            <?php if (in_array($order['status'], ['pending', 'cancelled'])) : ?>
                                                <a href="<?= BASEURL; ?>/customer/deleteOrder/<?= $order['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1" onclick="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus pesanan ini?');">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
