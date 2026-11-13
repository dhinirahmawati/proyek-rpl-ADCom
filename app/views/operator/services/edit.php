<div class="container py-4" style="max-width: 820px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/services" class="text-decoration-none text-muted">Manajemen Layanan</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Edit Layanan</li>
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
                        <i class="bi bi-gear-fill fs-3 text-white"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Edit Layanan</h4>
                        <p class="text-white-50 mb-0 small">Perbarui detail paket jasa, tarif estimasi, dan status layanan.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/operator/services" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="<?= BASEURL; ?>/operator/updateService" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $data['service']['id']; ?>">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Layanan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" 
                                   value="<?= htmlspecialchars($data['service']['name']); ?>" required placeholder="Contoh: Service Komputer">
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
                                    <option value="<?= $category['id']; ?>" <?= ($data['service']['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi Lengkap</label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Jelaskan detail perbaikan, lingkup pengerjaan, atau garansi..."><?= htmlspecialchars($data['service']['description']); ?></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="estimated_price" class="form-label fw-semibold">Estimasi Biaya / Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" class="form-control border-start-0" id="estimated_price" name="estimated_price" 
                                   value="<?= $data['service']['estimated_price']; ?>" step="1000" placeholder="Contoh: 150000">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="estimated_time" class="form-label fw-semibold">Estimasi Waktu Pengerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-clock text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="estimated_time" name="estimated_time" 
                                   value="<?= htmlspecialchars($data['service']['estimated_time']); ?>" placeholder="Misal: 1-2 Hari, atau 3 Jam">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold">Gambar / Ikon Layanan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-image text-muted"></i>
                        </span>
                        <input class="form-control border-start-0" type="file" id="image" name="image" accept="image/*">
                    </div>
                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah gambar layanan.</small>

                    <?php if (!empty($data['service']['image'])) : ?>
                        <div class="mt-2 p-2 card bg-light border-0 d-inline-block">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= BASEURL; ?>/uploads/services/<?= $data['service']['image']; ?>" alt="Current Image" width="70" class="img-thumbnail rounded">
                                <small class="text-muted">Gambar aktif saat ini</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status Aktif Switch dengan Ikon Indikator -->
                <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div id="serviceStatusIconBox" class="rounded-circle p-2 d-flex align-items-center justify-content-center <?= $data['service']['is_active'] ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'; ?>" style="width: 44px; height: 44px;">
                                <i id="serviceStatusIcon" class="bi <?= $data['service']['is_active'] ? 'bi-check-circle-fill' : 'bi-slash-circle-fill'; ?> fs-4"></i>
                            </div>
                            <div>
                                <label class="form-check-label fw-bold d-block mb-0 cursor-pointer" for="is_active">
                                    Status Layanan: 
                                    <span id="serviceStatusLabel" class="badge <?= $data['service']['is_active'] ? 'bg-success' : 'bg-danger'; ?> rounded-pill ms-1">
                                        <i class="bi <?= $data['service']['is_active'] ? 'bi-check-circle' : 'bi-slash-circle'; ?> me-1"></i><?= $data['service']['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                    </span>
                                </label>
                                <small class="text-muted d-block mt-1">Layanan aktif akan tampil di katalog publik dan siap dipesan pelanggan.</small>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" id="is_active" name="is_active" value="1" <?= $data['service']['is_active'] ? 'checked' : ''; ?> onchange="updateServiceStatus(this)">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/operator/services" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Update Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateServiceStatus(checkbox) {
    const iconBox = document.getElementById('serviceStatusIconBox');
    const icon = document.getElementById('serviceStatusIcon');
    const label = document.getElementById('serviceStatusLabel');
    if (checkbox.checked) {
        iconBox.className = 'rounded-circle p-2 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success';
        icon.className = 'bi bi-check-circle-fill fs-4';
        label.className = 'badge bg-success rounded-pill ms-1';
        label.innerHTML = '<i class="bi bi-check-circle me-1"></i>Aktif';
    } else {
        iconBox.className = 'rounded-circle p-2 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger';
        icon.className = 'bi bi-slash-circle-fill fs-4';
        label.className = 'badge bg-danger rounded-pill ms-1';
        label.innerHTML = '<i class="bi bi-slash-circle me-1"></i>Non-Aktif';
    }
}
</script>
