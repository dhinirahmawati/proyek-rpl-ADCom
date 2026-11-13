<div class="container py-4" style="max-width: 820px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/services" class="text-decoration-none text-muted">Manajemen Layanan</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Tambah Layanan</li>
        </ol>
    </nav>

    <!-- Flash message -->
    <?php Flasher::flash(); ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Header Banner -->
        <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-25 p-3 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-plus-circle-fill fs-3 text-white"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Tambah Layanan Baru</h4>
                        <p class="text-white-50 mb-0 small">Daftarkan paket jasa teknis baru ke dalam katalog sistem.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/operator/services" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="<?= BASEURL; ?>/operator/storeService" method="post" enctype="multipart/form-data">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Layanan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" required placeholder="Contoh: Pasang CCTV" autofocus>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold">Kategori Layanan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-grid text-muted"></i>
                            </span>
                            <select class="form-select border-start-0" id="category_id" name="category_id">
                                <option value="">Pilih Kategori...</option>
                                <?php foreach ($data['categories'] as $category) : ?>
                                    <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi Lengkap</label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Jelaskan detail perbaikan, cakupan jasa, atau garansi..."></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="estimated_price" class="form-label fw-semibold">Estimasi Biaya / Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" class="form-control border-start-0" id="estimated_price" name="estimated_price" step="1000" placeholder="Contoh: 200000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="estimated_time" class="form-label fw-semibold">Estimasi Waktu Pengerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-clock text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="estimated_time" name="estimated_time" placeholder="Misal: 1-2 Hari, atau 3 Jam">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold">Gambar Layanan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-image text-muted"></i>
                        </span>
                        <input class="form-control border-start-0" type="file" id="image" name="image" accept="image/*">
                    </div>
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, atau GIF (Maks. 2MB).</small>
                </div>

                <!-- Status Aktif Switch -->
                <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="form-check form-switch d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 ms-0 mt-0" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            <span class="fw-bold d-block">Status Layanan Aktif</span>
                            <span class="text-muted small">Layanan aktif akan langsung ditampilkan ke katalog pelanggan.</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/operator/services" class="btn btn-light rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
