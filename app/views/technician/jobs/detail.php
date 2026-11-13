<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0 text-gray-800"><?= $data['judul']; ?></h2>
            <a href="<?= BASEURL; ?>/technician/jobs" class="btn btn-light shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-gray-500"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <div class="row">
        <!-- Detail Pekerjaan -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Informasi Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Layanan</small>
                        <span class="fw-bold fs-5"><?= $data['order']['service_name']; ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Pelanggan</small>
                        <span class="fw-bold"><?= $data['order']['customer_name']; ?></span>
                        <div class="text-muted small"><?= $data['order']['customer_email']; ?></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Kontak Pelanggan</small>
                        <?php if (!empty($data['order']['customer_phone'])) : ?>
                            <?php 
                                $cleanPhone = preg_replace('/[^0-9]/', '', $data['order']['customer_phone']);
                                $waNumber = (strpos($cleanPhone, '0') === 0) ? '62' . substr($cleanPhone, 1) : $cleanPhone;
                            ?>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="fw-bold"><i class="fas fa-phone text-muted me-1"></i> <?= htmlspecialchars($data['order']['customer_phone']); ?></span>
                                <a href="https://wa.me/<?= $waNumber; ?>" target="_blank" class="btn btn-sm btn-success py-1 px-3 shadow-sm" title="Hubungi via WhatsApp">
                                    <i class="fab fa-whatsapp me-1"></i> Chat WA
                                </a>
                            </div>
                        <?php else : ?>
                            <span class="text-muted fst-italic">Belum dicantumkan</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Alamat Lengkap Kunjungan</small>
                        <?php if (!empty($data['order']['customer_address'])) : ?>
                            <div class="bg-light p-3 rounded mt-1 border">
                                <div class="text-dark mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= nl2br(htmlspecialchars($data['order']['customer_address'])); ?></div>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($data['order']['customer_address']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-directions me-1"></i> Buka Rute Google Maps
                                </a>
                            </div>
                        <?php else : ?>
                            <span class="text-muted fst-italic">Belum dicantumkan</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Deskripsi Masalah</small>
                        <p class="bg-light p-3 rounded mt-1 border">
                            <?= nl2br($data['order']['problem_description']); ?>
                        </p>
                    </div>
                    
                    <hr>
                    
                    <form action="<?= BASEURL; ?>/technician/updateJobStatus" method="POST">
                        <input type="hidden" name="order_id" value="<?= $data['order']['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status Pekerjaan</label>
                            <select name="status" class="form-select" <?= ($data['order']['status'] == 'completed' || $data['order']['status'] == 'cancelled') ? 'disabled' : ''; ?>>
                                <option value="assigned" <?= ($data['order']['status'] == 'assigned') ? 'selected' : ''; ?>>Ditugaskan</option>
                                <option value="processing" <?= ($data['order']['status'] == 'processing') ? 'selected' : ''; ?>>Proses Dikerjakan</option>
                                <option value="completed" <?= ($data['order']['status'] == 'completed') ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                        <?php if(!in_array($data['order']['status'], ['completed', 'cancelled'])) : ?>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">
                            <i class="fas fa-sync-alt me-1"></i> Update Status
                        </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Riwayat dan Update -->
        <div class="col-lg-7">
            <!-- Tambah Catatan -->
            <?php if(!in_array($data['order']['status'], ['completed', 'cancelled'])) : ?>
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle me-1"></i> Tambah Catatan Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL; ?>/technician/addNote" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" value="<?= $data['order']['id']; ?>">
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="update_text" rows="3" placeholder="Tuliskan catatan progres atau tindakan yang dilakukan..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small"><i class="fas fa-camera me-1"></i> Unggah Foto Dokumentasi (Opsional)</label>
                            <input class="form-control form-control-sm" type="file" name="image" accept="image/*">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Kirim Catatan</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Timeline Catatan -->
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-1"></i> Riwayat Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if(empty($data['updates'])) : ?>
                            <p class="text-center text-muted py-3">Belum ada riwayat aktivitas.</p>
                        <?php else : ?>
                            <?php foreach($data['updates'] as $update) : ?>
                            <div class="mb-4 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <span class="fw-bold"><?= $update['updater_name']; ?></span>
                                        <span class="badge bg-light text-dark ms-1"><?= $update['updater_role']; ?></span>
                                    </div>
                                    <small class="text-muted"><?= date('d M Y, H:i', strtotime($update['created_at'])); ?></small>
                                </div>
                                <p class="mb-2"><?= nl2br($update['update_text']); ?></p>
                                
                                <?php if(!empty($update['image'])) : ?>
                                <div class="mt-2">
                                    <img src="<?= BASEURL; ?>/uploads/updates/<?= $update['image']; ?>" class="img-fluid rounded border" style="max-height: 200px;" alt="Dokumentasi">
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
