<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0 text-gray-800"><i class="fas fa-robot text-primary me-2"></i> <?= $data['judul']; ?></h2>
            <a href="<?= BASEURL; ?>/ai/chat" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus me-1"></i> Mulai Konsultasi Baru
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($data['conversations'])) : ?>
                            <div class="text-center py-5">
                                <img src="<?= BASEURL; ?>/img/ai-bot.svg" alt="AI Bot" style="width: 150px; opacity: 0.5;" class="mb-3">
                                <h5 class="text-muted">Belum ada riwayat konsultasi</h5>
                                <p class="text-muted">Mulai percakapan dengan AI kami untuk mendapatkan bantuan teknis.</p>
                                <a href="<?= BASEURL; ?>/ai/chat" class="btn btn-outline-primary mt-2">Mulai Sekarang</a>
                            </div>
                        <?php else : ?>
                            <?php foreach ($data['conversations'] as $conv) : ?>
                                <a href="<?= BASEURL; ?>/ai/chat/<?= $conv['id']; ?>" class="list-group-item list-group-item-action p-4 border-bottom">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1 fw-bold text-dark"><i class="far fa-comment-dots text-primary me-2"></i> <?= htmlspecialchars($conv['title']); ?></h5>
                                            <small class="text-muted d-block mt-2">
                                                Dibuat: <?= date('d M Y, H:i', strtotime($conv['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="btn btn-sm btn-light rounded-circle"><i class="fas fa-chevron-right text-muted"></i></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
