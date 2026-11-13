<style>
    .service-card {
        transition: transform 0.25s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.25s ease;
        height: 100%;
        border-radius: 1rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
    }
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
    }
    .service-img {
        height: 200px;
        object-fit: cover;
        width: 100%;
        background-color: #f8f9fe;
    }
    .service-title {
        font-weight: 700;
        color: #32325d;
        margin-bottom: 0.5rem;
    }
    .service-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #5e72e4;
    }
    .category-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.95);
        color: #172b4d;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    .hero-catalog {
        background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%);
        color: white;
        padding: 70px 0 80px 0;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        margin-bottom: 40px;
        box-shadow: 0 12px 35px rgba(94, 114, 228, 0.25);
    }
    .list-group-item.active {
        background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%) !important;
        border-color: transparent !important;
        box-shadow: 0 4px 10px rgba(94, 114, 228, 0.3) !important;
    }
</style>

<div class="hero-catalog text-center position-relative">
    <div class="container position-relative" style="z-index: 2;">
        <span class="badge bg-white text-primary rounded-pill px-3 py-2 text-xs fw-bold mb-2 shadow-sm">
            <i class="ni ni-shop me-1"></i> DAFTAR LAYANAN SISWA
        </span>
        <h1 class="display-4 fw-bolder text-white mb-2 tracking-tight">Katalog Layanan Jasa</h1>
        <p class="lead text-white opacity-8 mb-4 mx-auto" style="max-width: 650px;">Temukan solusi teknologi terbaik untuk perangkat dan jaringan Anda dengan jaminan pengerjaan teliti.</p>
        
        <form action="<?= BASEURL; ?>/service" method="GET" class="mt-4 mx-auto" style="max-width: 600px;">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden bg-white p-1">
                <?php if($data['current_category']): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($data['current_category']); ?>">
                <?php endif; ?>
                <input type="text" name="q" class="form-control border-0 px-4 bg-transparent" placeholder="Cari jasa (CCTV, Mikrotik, RAM, dll)..." value="<?= htmlspecialchars($data['keyword'] ?? ''); ?>">
                <button class="btn btn-primary rounded-pill px-4" type="submit"><i class="bi bi-search me-1"></i> Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?= BASEURL; ?>/service<?= $data['keyword'] ? '?q='.urlencode($data['keyword']) : '' ?>" 
                           class="list-group-item list-group-item-action border-0 <?= !$data['current_category'] ? 'active bg-primary text-white rounded' : '' ?>">
                            Semua Layanan
                        </a>
                        <?php foreach ($data['categories'] as $category) : ?>
                            <a href="<?= BASEURL; ?>/service?category=<?= $category['id']; ?><?= $data['keyword'] ? '&q='.urlencode($data['keyword']) : '' ?>" 
                               class="list-group-item list-group-item-action border-0 <?= $data['current_category'] == $category['id'] ? 'active bg-primary text-white rounded' : '' ?>">
                                <?= htmlspecialchars($category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Grid -->
        <div class="col-lg-9">
            <div class="row g-4">
                <?php if(empty($data['services'])): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Layanan tidak ditemukan</h4>
                        <p>Coba gunakan kata kunci lain atau ubah filter kategori.</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $catDefaults = [
                        'Networking' => 'lan_network.jpg',
                        'Hardware' => 'pc_service.jpg',
                        'Software' => 'software_os.jpg',
                        'Security' => 'cctv_installation.jpg'
                    ];
                    foreach ($data['services'] as $service) : 
                        $serviceImg = !empty($service['image']) ? $service['image'] : ($catDefaults[$service['category_name'] ?? ''] ?? null);
                    ?>
                        <div class="col-md-6 col-xl-4">
                            <a href="<?= BASEURL; ?>/service/detail/<?= $service['id']; ?>" class="text-decoration-none text-dark">
                                <div class="card service-card">
                                    <div class="position-relative overflow-hidden">
                                        <?php if (!empty($serviceImg)) : ?>
                                            <img src="<?= BASEURL; ?>/uploads/services/<?= $serviceImg; ?>" class="card-img-top service-img" alt="<?= htmlspecialchars($service['name']); ?>" loading="lazy">
                                        <?php else : ?>
                                            <div class="card-img-top service-img d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-tools" style="font-size: 4rem; opacity: 0.2;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="category-badge"><?= htmlspecialchars($service['category_name'] ?? 'Umum'); ?></span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="service-title"><?= htmlspecialchars($service['name']); ?></h5>
                                        <p class="card-text text-muted small flex-grow-1">
                                            <?= htmlspecialchars(strlen($service['description']) > 80 ? substr($service['description'], 0, 80) . '...' : $service['description']); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-end mt-3">
                                            <div>
                                                <small class="text-muted d-block">Mulai dari</small>
                                                <span class="service-price">Rp <?= number_format($service['estimated_price'], 0, ',', '.'); ?></span>
                                            </div>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> <?= htmlspecialchars($service['estimated_time'] ?? '-'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
