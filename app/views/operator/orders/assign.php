<div class="container py-4" style="max-width: 1080px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/orders" class="text-decoration-none text-muted">Daftar Pesanan</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Penugasan Teknisi #<?= $data['order']['id']; ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-gray-800"><?= $data['judul']; ?></h3>
            <p class="text-muted mb-0 small">Atur dan tugaskan teknisi yang kompeten untuk pesanan ini.</p>
        </div>
        <a href="<?= BASEURL; ?>/operator/orders" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan
        </a>
    </div>

    <div class="row g-4">
        <!-- Informasi Order -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                <div class="card-header bg-primary text-white py-3 px-4 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard2-check fs-5"></i>
                        <h6 class="m-0 fw-bold">Detail Pekerjaan</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Layanan</small>
                        <span class="fw-bold fs-5 text-primary"><?= htmlspecialchars($data['order']['service_name']); ?></span>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Pelanggan</small>
                        <span class="fw-semibold"><?= htmlspecialchars($data['order']['customer_name']); ?></span>
                        <div class="text-muted small"><?= htmlspecialchars($data['order']['customer_email']); ?></div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Kontak Pelanggan</small>
                        <?php if (!empty($data['order']['customer_phone'])) : ?>
                            <?php 
                                $cleanPhone = preg_replace('/[^0-9]/', '', $data['order']['customer_phone']);
                                $waNumber = (strpos($cleanPhone, '0') === 0) ? '62' . substr($cleanPhone, 1) : $cleanPhone;
                            ?>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="fw-semibold"><i class="bi bi-telephone text-muted me-1"></i> <?= htmlspecialchars($data['order']['customer_phone']); ?></span>
                                <a href="https://wa.me/<?= $waNumber; ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2 small" title="Hubungi via WhatsApp">
                                    <i class="bi bi-whatsapp"></i> Chat WA
                                </a>
                            </div>
                        <?php else : ?>
                            <span class="text-muted fst-italic">Belum dicantumkan</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Alamat Kunjungan</small>
                        <?php if (!empty($data['order']['customer_address'])) : ?>
                            <div class="bg-light p-3 rounded-3 mt-1 border">
                                <div class="small mb-2"><i class="bi bi-geo-alt text-danger me-1"></i> <?= nl2br(htmlspecialchars($data['order']['customer_address'])); ?></div>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($data['order']['customer_address']); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 small">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Maps
                                </a>
                            </div>
                        <?php else : ?>
                            <span class="text-muted fst-italic">Belum dicantumkan</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Deskripsi Masalah</small>
                        <div class="bg-light p-3 rounded-3 border small">
                            <?= nl2br(htmlspecialchars($data['order']['problem_description'])); ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">Status Saat Ini</small>
                        <span class="badge bg-secondary rounded-pill px-3 py-2 text-uppercase fw-semibold"><?= htmlspecialchars($data['order']['status']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Penugasan -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white py-3 px-4 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-gear fs-5 text-warning"></i>
                        <h6 class="m-0 fw-bold">Pilih Teknisi Pelaksana</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?= BASEURL; ?>/operator/storeAssignTechnician" method="POST">
                        <input type="hidden" name="order_id" value="<?= $data['order']['id']; ?>">
                        
                        <div class="table-responsive mb-4 rounded-3 border overflow-hidden">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 70px;">Pilih</th>
                                        <th>Nama Teknisi</th>
                                        <th style="width: 160px;">Pekerjaan Aktif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['technicians'] as $tech) : ?>
                                    <tr <?= ($tech['active_jobs'] > 3) ? 'class="table-warning"' : ''; ?>>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="technician_id" id="tech_<?= $tech['id']; ?>" value="<?= $tech['id']; ?>" required <?= ($data['order']['technician_id'] == $tech['id']) ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="form-check-label w-100 fw-semibold cursor-pointer" for="tech_<?= $tech['id']; ?>">
                                                <?= htmlspecialchars($tech['name']); ?>
                                            </label>
                                        </td>
                                        <td>
                                            <span class="badge <?= ($tech['active_jobs'] > 3) ? 'bg-danger' : 'bg-primary'; ?> rounded-pill px-3 py-1">
                                                <?= $tech['active_jobs']; ?> Pekerjaan
                                            </span>
                                            <?php if($tech['active_jobs'] > 3) : ?>
                                                <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle"></i> Sangat Sibuk</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if(empty($data['technicians'])) : ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Tidak ada teknisi yang aktif.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                            <div>
                                <?php if($data['order']['status'] != 'completed') : ?>
                                    <a href="<?= BASEURL; ?>/operator/completeOrder/<?= $data['order']['id']; ?>" 
                                       class="btn btn-outline-success rounded-pill px-3"
                                       onclick="return confirm('Apakah Anda yakin ingin menandai pesanan #<?= $data['order']['id']; ?> sebagai SELESAI agar pelanggan dapat memberikan penilaian dan rating?');">
                                        <i class="bi bi-check2-all me-1"></i> Selesaikan Pesanan Ini
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= BASEURL; ?>/operator/orders" class="btn btn-light rounded-pill px-4">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" <?= empty($data['technicians']) ? 'disabled' : ''; ?>>
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Penugasan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
