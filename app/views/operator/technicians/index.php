<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-gray-800"><?= $data['judul']; ?></h3>
            <p class="text-muted mb-0 small">Kelola data seluruh teknisi, pantau jumlah penugasan pekerjaan, dan status akun.</p>
        </div>
        <a href="<?= BASEURL; ?>/operator/createTechnician" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Teknisi
        </a>
    </div>

    <!-- Flash message -->
    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status Akun</th>
                            <th>Pekerjaan Aktif</th>
                            <th class="text-center pe-4" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach( $data['technicians'] as $tech ) : ?>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold"><?= $i++; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-warning bg-opacity-25 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="fw-bold"><?= htmlspecialchars($tech['name']); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($tech['email']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($tech['is_active']) : ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                <?php else : ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                        <i class="bi bi-slash-circle-fill me-1"></i> Non-Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= ($tech['active_jobs'] > 3) ? 'bg-danger' : 'bg-primary'; ?> rounded-pill px-3 py-2">
                                    <i class="bi bi-briefcase me-1"></i> <?= $tech['active_jobs']; ?> Job
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex gap-1" role="group">
                                    <!-- Tombol Edit dengan Ikon -->
                                    <a href="<?= BASEURL; ?>/operator/editTechnician/<?= $tech['id']; ?>" 
                                       class="btn btn-sm btn-warning text-dark rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                       style="width: 34px; height: 34px;" 
                                       title="Edit Data Teknisi" 
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <!-- Tombol Non-aktif / Aktifkan dengan Ikon -->
                                    <?php if($tech['is_active']) : ?>
                                        <a href="<?= BASEURL; ?>/operator/toggleTechnician/<?= $tech['id']; ?>/0" 
                                           class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           onclick="return confirm('Non-aktifkan teknisi ini? Teknisi tidak akan bisa ditugaskan order baru.');" 
                                           title="Non-aktifkan Akun" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-slash-circle"></i>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?= BASEURL; ?>/operator/toggleTechnician/<?= $tech['id']; ?>/1" 
                                           class="btn btn-sm btn-outline-success rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           onclick="return confirm('Aktifkan kembali teknisi ini?');" 
                                           title="Aktifkan Akun" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($data['technicians'])) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data teknisi yang terdaftar.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
