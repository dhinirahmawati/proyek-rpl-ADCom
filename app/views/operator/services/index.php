<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-gray-800">Daftar Layanan</h3>
            <p class="text-muted mb-0 small">Katalog paket perbaikan, perkiraan tarif jasa, dan durasi estimasi pengerjaan.</p>
        </div>
        <a href="<?= BASEURL; ?>/operator/createService" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Layanan
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
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th style="width: 80px;">Gambar</th>
                            <th>Nama Layanan</th>
                            <th>Kategori</th>
                            <th>Harga (Est.)</th>
                            <th>Waktu (Est.)</th>
                            <th>Status</th>
                            <th class="text-center pe-4" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data['services'] as $service) : ?>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold"><?= $no++; ?></td>
                                <td>
                                    <?php 
                                    $catDefaults = [
                                        'Networking' => 'lan_network.jpg',
                                        'Hardware' => 'pc_service.jpg',
                                        'Software' => 'software_os.jpg',
                                        'Security' => 'cctv_installation.jpg'
                                    ];
                                    $tableImg = !empty($service['image']) ? $service['image'] : ($catDefaults[$service['category_name'] ?? ''] ?? null);
                                    ?>
                                    <?php if (!empty($tableImg)) : ?>
                                        <img src="<?= BASEURL; ?>/uploads/services/<?= $tableImg; ?>" alt="Gambar" width="46" height="46" class="rounded-3 object-fit-cover border shadow-sm">
                                    <?php else : ?>
                                        <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 46px; height: 46px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong class="text-dark"><?= htmlspecialchars($service['name']); ?></strong></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                                        <?= htmlspecialchars($service['category_name'] ?? 'Umum'); ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-primary">Rp <?= number_format($service['estimated_price'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($service['estimated_time']); ?></span>
                                </td>
                                <td>
                                    <?php if ($service['is_active']) : ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-slash-circle-fill me-1"></i> Non-Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1" role="group">
                                        <!-- Tombol Edit -->
                                        <a href="<?= BASEURL; ?>/operator/editService/<?= $service['id']; ?>" 
                                           class="btn btn-sm btn-warning text-dark rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           title="Edit Layanan" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- Tombol Non-aktif / Aktifkan -->
                                        <?php if ($service['is_active']) : ?>
                                            <a href="<?= BASEURL; ?>/operator/toggleService/<?= $service['id']; ?>/0" 
                                               class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 34px; height: 34px;" 
                                               onclick="return confirm('Non-aktifkan layanan ini?');" 
                                               title="Non-aktifkan Layanan" 
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-slash-circle"></i>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?= BASEURL; ?>/operator/toggleService/<?= $service['id']; ?>/1" 
                                               class="btn btn-sm btn-outline-success rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 34px; height: 34px;" 
                                               onclick="return confirm('Aktifkan kembali layanan ini?');" 
                                               title="Aktifkan Layanan" 
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-check-circle"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Tombol Hapus -->
                                        <a href="<?= BASEURL; ?>/operator/deleteService/<?= $service['id']; ?>" 
                                           class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           onclick="return confirm('Yakin ingin menghapus layanan ini?');" 
                                           title="Hapus Layanan" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($data['services'])): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-wrench-adjustable-circle fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Belum ada data paket layanan yang didaftarkan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
