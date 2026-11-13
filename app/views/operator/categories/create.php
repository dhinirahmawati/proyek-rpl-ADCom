<div class="container py-4" style="max-width: 680px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator" class="text-decoration-none text-muted"><i class="bi bi-house-door"></i> Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/operator/categories" class="text-decoration-none text-muted">Manajemen Kategori</a></li>
            <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Tambah Kategori</li>
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
                        <i class="bi bi-folder-plus fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Tambah Kategori Baru</h4>
                        <p class="text-white-50 mb-0 small">Buat kategori baru untuk mengelompokkan layanan.</p>
                    </div>
                </div>
                <a href="<?= BASEURL; ?>/operator/categories" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="<?= BASEURL; ?>/operator/storeCategory" method="post">
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-tag text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="name" name="name" required placeholder="Misal: Networking">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Penjelasan singkat mengenai kategori ini"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= BASEURL; ?>/operator/categories" class="btn btn-light rounded-pill px-4">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
