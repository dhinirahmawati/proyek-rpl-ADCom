<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold"><i class="bi bi-star-fill text-warning me-2"></i>Semua Ulasan & Penilaian</h2>
            <a href="<?= BASEURL; ?>/operator" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <?php if(empty($data['reviews'])) : ?>
                <div class="alert alert-light text-center py-5 mb-0">
                    <i class="bi bi-chat-left-text text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">Belum ada ulasan yang diberikan oleh customer.</h5>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Layanan</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['reviews'] as $review) : ?>
                                <tr>
                                    <td><?= date('d M Y H:i', strtotime($review['created_at'])); ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($review['customer_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                            <?= htmlspecialchars($review['service_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($review['comment']) : ?>
                                            <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($review['comment']); ?>">
                                                <?= htmlspecialchars($review['comment']); ?>
                                            </span>
                                        <?php else : ?>
                                            <span class="text-muted fst-italic">Tidak ada komentar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- Tombol untuk melihat detail order dari review tersebut -->
                                        <a href="<?= BASEURL; ?>/operator/assignTechnician/<?= $review['order_id']; ?>" class="btn btn-sm btn-primary rounded-pill">
                                            <i class="bi bi-eye"></i> Detail Order
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
