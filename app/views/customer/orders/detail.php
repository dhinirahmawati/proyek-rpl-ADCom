<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold">Detail Pesanan #<?= $data['order']['id']; ?></h2>
            <div>
                <?php if (in_array($data['order']['status'], ['pending', 'cancelled'])) : ?>
                    <a href="<?= BASEURL; ?>/customer/deleteOrder/<?= $data['order']['id']; ?>" class="btn btn-outline-danger me-2" onclick="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus pesanan ini?');">
                        <i class="bi bi-trash me-1"></i> Batalkan Pesanan
                    </a>
                <?php endif; ?>
                <a href="<?= BASEURL; ?>/customer/orders" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Informasi Layanan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Layanan</div>
                        <div class="col-sm-8 fw-bold"><?= $data['order']['service_name']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Status</div>
                        <div class="col-sm-8">
                            <?php 
                            $badgeClass = 'bg-secondary';
                            if ($data['order']['status'] == 'completed') $badgeClass = 'bg-success';
                            else if ($data['order']['status'] == 'processing') $badgeClass = 'bg-primary';
                            else if ($data['order']['status'] == 'assigned') $badgeClass = 'bg-info';
                            else if ($data['order']['status'] == 'cancelled') $badgeClass = 'bg-danger';
                            else $badgeClass = 'bg-warning text-dark';
                            ?>
                            <span class="badge rounded-pill <?= $badgeClass; ?> px-3 py-2">
                                <?= ucfirst($data['order']['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Tanggal Pesanan</div>
                        <div class="col-sm-8"><?= date('d M Y H:i', strtotime($data['order']['created_at'])); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Nomor WhatsApp / HP</div>
                        <div class="col-sm-8 fw-semibold"><?= htmlspecialchars($data['order']['customer_phone'] ?? '-'); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Alamat Kunjungan</div>
                        <div class="col-sm-8"><?= nl2br(htmlspecialchars($data['order']['customer_address'] ?? '-')); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Deskripsi Masalah</div>
                        <div class="col-sm-8"><?= nl2br($data['order']['problem_description']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-muted">Teknisi</div>
                        <div class="col-sm-8"><?= $data['order']['technician_name'] ? $data['order']['technician_name'] : '-'; ?></div>
                    </div>
                </div>
            </div>

            <!-- Bagian Review -->
            <?php if ($data['order']['status'] == 'completed') : ?>
                <div class="card shadow-sm border-0 rounded-4 mt-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Ulasan Layanan</h5>
                        
                        <?php if ($data['review']) : ?>
                            <!-- Sudah Direview -->
                            <div class="d-flex align-items-center mb-3">
                                <h2 class="mb-0 me-3 text-warning">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $data['review']['rating'] ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                </h2>
                                <span class="fs-4 fw-bold"><?= $data['review']['rating']; ?> / 5</span>
                            </div>
                            <?php if ($data['review']['comment']) : ?>
                                <div class="p-3 bg-white rounded-3 border">
                                    <p class="mb-0 text-muted fst-italic">"<?= nl2br(htmlspecialchars($data['review']['comment'])); ?>"</p>
                                </div>
                            <?php endif; ?>
                            <p class="text-muted small mt-2">Diberikan pada <?= date('d M Y H:i', strtotime($data['review']['created_at'])); ?></p>
                        <?php else : ?>
                            <!-- Belum Direview, tampilkan Form -->
                            <p class="text-muted">Bagaimana pengalaman Anda dengan layanan ini? Berikan penilaian Anda.</p>
                            <form action="<?= BASEURL; ?>/customer/submitReview" method="post">
                                <input type="hidden" name="order_id" value="<?= $data['order']['id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rating</label>
                                    <div class="rating-stars mb-2" style="font-size: 2rem; color: #ffc107; cursor: pointer;">
                                        <input type="hidden" name="rating" id="ratingInput" value="5" required>
                                        <i class="bi bi-star-fill star-item" data-value="1"></i>
                                        <i class="bi bi-star-fill star-item" data-value="2"></i>
                                        <i class="bi bi-star-fill star-item" data-value="3"></i>
                                        <i class="bi bi-star-fill star-item" data-value="4"></i>
                                        <i class="bi bi-star-fill star-item" data-value="5"></i>
                                    </div>
                                    <div class="form-text">Pilih 1 hingga 5 bintang.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comment" class="form-label fw-bold">Komentar (Opsional)</label>
                                    <textarea class="form-control rounded-3" id="comment" name="comment" rows="3" placeholder="Bagikan pengalaman Anda..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Ulasan</button>
                            </form>
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const stars = document.querySelectorAll('.star-item');
                                    const ratingInput = document.getElementById('ratingInput');
                                    
                                    stars.forEach(star => {
                                        star.addEventListener('click', function() {
                                            const value = this.getAttribute('data-value');
                                            ratingInput.value = value;
                                            
                                            // Update stars UI
                                            stars.forEach(s => {
                                                if (s.getAttribute('data-value') <= value) {
                                                    s.classList.remove('bi-star');
                                                    s.classList.add('bi-star-fill');
                                                } else {
                                                    s.classList.remove('bi-star-fill');
                                                    s.classList.add('bi-star');
                                                }
                                            });
                                        });
                                    });
                                });
                            </script>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold">Riwayat Update</h5>
                </div>
                <div class="card-body p-4">
                    <?php if(empty($data['updates'])) : ?>
                        <p class="text-muted text-center mb-0">Belum ada update untuk pesanan ini.</p>
                    <?php else : ?>
                        <div class="timeline position-relative ps-4 border-start border-2 border-primary">
                            <?php foreach($data['updates'] as $update) : ?>
                                <div class="timeline-item mb-4 position-relative">
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -29px; top: 5px;"></div>
                                    <div class="small text-muted mb-1"><?= date('d M Y H:i', strtotime($update['created_at'])); ?></div>
                                    <div class="fw-bold mb-1"><?= $update['updater_name']; ?> (<?= ucfirst($update['updater_role']); ?>)</div>
                                    <div class="p-3 bg-light rounded-3">
                                        <?= nl2br($update['update_text']); ?>
                                        <?php if($update['image']) : ?>
                                            <div class="mt-2">
                                                <img src="<?= BASEURL; ?>/uploads/updates/<?= $update['image']; ?>" class="img-fluid rounded" alt="Update Image">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
