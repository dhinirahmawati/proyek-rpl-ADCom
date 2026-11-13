<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-5 text-center">
                    <h2 class="fw-bold mb-3 text-warning">Dashboard Operator</h2>
                    <p class="text-muted lead">Pusat kendali pesanan masuk. Tugaskan teknisi dan kelola status layanan.</p>
                    
                    <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?= BASEURL; ?>/operator/orders" class="btn btn-warning btn-lg rounded-pill px-4 text-dark fw-bold">
                            <i class="bi bi-cart"></i> Kelola Pesanan Masuk
                        </a>
                        <a href="<?= BASEURL; ?>/operator/services" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                            <i class="bi bi-tools"></i> Layanan
                        </a>
                        <a href="<?= BASEURL; ?>/operator/technicians" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                            <i class="bi bi-person-badge"></i> Teknisi
                        </a>
                        <a href="<?= BASEURL; ?>/operator/reviews" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-star"></i> Semua Ulasan
                        </a>
                        <a href="<?= BASEURL; ?>/aiConfig" class="btn btn-outline-info btn-lg rounded-pill px-4">
                            <i class="bi bi-robot"></i> Konfigurasi AI
                        </a>
                        <a href="<?= BASEURL; ?>/chatHistory" class="btn btn-outline-danger btn-lg rounded-pill px-4 fw-semibold">
                            <i class="bi bi-chat-dots-fill me-1"></i> Riwayat Chat &amp; Keluhan
                        </a>
                        <a href="<?= BASEURL; ?>/report" class="btn btn-outline-success btn-lg rounded-pill px-4">
                            <i class="bi bi-graph-up"></i> Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
