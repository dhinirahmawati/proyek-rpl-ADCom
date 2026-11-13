<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/service" class="text-decoration-none">Katalog Layanan</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($data['service']['name']); ?></li>
        </ol>
    </nav>

    <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="row g-0">
            <div class="col-md-5">
                <?php 
                $catDefaults = [
                    'Networking' => 'lan_network.jpg',
                    'Hardware' => 'pc_service.jpg',
                    'Software' => 'software_os.jpg',
                    'Security' => 'cctv_installation.jpg'
                ];
                $detailImg = !empty($data['service']['image']) ? $data['service']['image'] : ($catDefaults[$data['service']['category_name'] ?? ''] ?? null);
                ?>
                <?php if (!empty($detailImg)) : ?>
                    <img src="<?= BASEURL; ?>/uploads/services/<?= $detailImg; ?>" class="img-fluid w-100 h-100" style="object-fit: cover; min-height: 380px;" alt="<?= htmlspecialchars($data['service']['name']); ?>">
                <?php else : ?>
                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <i class="bi bi-tools text-muted" style="font-size: 8rem; opacity: 0.2;"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-7">
                <div class="card-body p-5">
                    <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill"><?= htmlspecialchars($data['service']['category_name'] ?? 'Umum'); ?></span>
                    <h2 class="card-title fw-bold mb-4"><?= htmlspecialchars($data['service']['name']); ?></h2>
                    
                    <h5 class="text-muted fw-bold">Deskripsi Layanan</h5>
                    <p class="card-text text-secondary mb-4" style="line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($data['service']['description'])); ?>
                    </p>

                    <div class="row mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1"><i class="bi bi-tag-fill me-1"></i> Estimasi Harga</small>
                                <span class="fs-4 fw-bold text-danger">Rp <?= number_format($data['service']['estimated_price'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1"><i class="bi bi-clock-fill me-1"></i> Estimasi Waktu</small>
                                <span class="fs-5 fw-bold text-dark"><?= htmlspecialchars($data['service']['estimated_time'] ?? '-'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer') : ?>
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-request="orderModal" data-bs-target="#orderModal">
                                <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                            </button>
                        <?php elseif (!isset($_SESSION['user_id'])) : ?>
                            <a href="<?= BASEURL; ?>/auth/login" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus"></i> Login untuk Memesan
                            </a>
                        <?php else : ?>
                            <button class="btn btn-secondary btn-lg" disabled>
                                Hanya pelanggan yang dapat memesan
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer') : ?>
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= BASEURL; ?>/service/storeOrder" method="POST">
                    <?= CSRF::input(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="orderModalLabel">Pesan Layanan: <?= htmlspecialchars($data['service']['name']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="service_id" value="<?= $data['service']['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label fw-bold"><i class="bi bi-whatsapp text-success me-1"></i> Nomor WhatsApp / HP Aktif</label>
                            <input type="tel" class="form-control rounded-3" id="customer_phone" name="customer_phone" required placeholder="Contoh: 081234567890" value="<?= htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>">
                            <div class="form-text text-muted">Nomor WhatsApp aktif Anda. Notifikasi status pesanan akan dikirimkan otomatis ke nomor ini.</div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_address" class="form-label fw-bold"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Alamat Lengkap Kunjungan</label>
                            <textarea class="form-control rounded-3" id="customer_address" name="customer_address" rows="3" required placeholder="Contoh: Jl. Sukarno Hatta No. 45, RT 03/RW 02, Bandung (Patokan samping kantor pos)"></textarea>
                            <div class="form-text text-muted">Tuliskan alamat jelas dan patokan agar teknisi dapat menemukan lokasi dengan mudah.</div>
                        </div>

                        <div class="mb-3">
                            <label for="problem_description" class="form-label fw-bold"><i class="bi bi-tools text-primary me-1"></i> Jelaskan Permasalahan Anda</label>
                            <textarea class="form-control rounded-3" id="problem_description" name="problem_description" rows="3" required placeholder="Contoh: Layar laptop bergaris dan berkedip saat dinyalakan..."></textarea>
                            <div class="form-text text-muted">Berikan detail sebanyak mungkin agar teknisi kami dapat mempersiapkan suku cadang dan solusi terbaik.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <!-- Review Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="fw-bold mb-4">Ulasan & Penilaian</h4>
            
            <?php if ($data['rating_stats']['total_reviews'] > 0) : ?>
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body text-center p-4">
                        <h1 class="display-4 fw-bold text-dark mb-0"><?= number_format($data['rating_stats']['avg_rating'], 1); ?></h1>
                        <div class="text-warning mb-2" style="font-size: 1.5rem;">
                            <?php 
                            $avg = round($data['rating_stats']['avg_rating']);
                            for($i=1; $i<=5; $i++): 
                            ?>
                                <i class="bi bi-star<?= $i <= $avg ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-muted mb-0">Berdasarkan <?= $data['rating_stats']['total_reviews']; ?> ulasan</p>
                    </div>
                </div>

                <div class="list-group list-group-flush shadow-sm rounded-4">
                    <?php foreach ($data['reviews'] as $review) : ?>
                        <div class="list-group-item p-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($review['customer_name']); ?></h6>
                                <small class="text-muted"><?= date('d M Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <div class="text-warning mb-2" style="font-size: 0.9rem;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if ($review['comment']) : ?>
                                <p class="mb-0 text-secondary"><?= nl2br(htmlspecialchars($review['comment'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="alert alert-light text-center py-5 border-0 shadow-sm" role="alert">
                    <i class="bi bi-chat-square-text text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">Belum ada ulasan untuk layanan ini.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
