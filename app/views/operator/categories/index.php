<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-gray-800">Daftar Kategori Layanan</h3>
            <p class="text-muted mb-0 small">Kelola kelompok kategori perbaikan dan perawatan perangkat.</p>
        </div>
        <a href="<?= BASEURL; ?>/operator/createCategory" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-folder-plus me-1"></i> Tambah Kategori
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
                            <th style="width: 250px;">Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center pe-4" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data['categories'] as $category) : ?>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold"><?= $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            <i class="bi bi-folder2-open"></i>
                                        </div>
                                        <strong class="text-dark"><?= htmlspecialchars($category['name']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small"><?= htmlspecialchars($category['description']); ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1" role="group">
                                        <a href="<?= BASEURL; ?>/operator/editCategory/<?= $category['id']; ?>" 
                                           class="btn btn-sm btn-warning text-dark rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           title="Edit Kategori" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= BASEURL; ?>/operator/deleteCategory/<?= $category['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px;" 
                                           onclick="return confirm('Yakin ingin menghapus kategori ini?');" 
                                           title="Hapus Kategori" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($data['categories'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Belum ada data kategori layanan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
