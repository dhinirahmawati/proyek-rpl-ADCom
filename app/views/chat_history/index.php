<div class="container-fluid px-3 px-lg-4 py-4">
    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="<?= BASEURL; ?>/<?= $data['userRole']; ?>" class="text-decoration-none text-muted">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Riwayat Chatbot &amp; Keluhan</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1 text-dark tracking-tight">
                <i class="bi bi-chat-dots-fill text-primary me-2"></i>Riwayat Chatbot &amp; Monitoring Keluhan
            </h2>
            <p class="text-muted mb-0 small">
                Monitoring interaksi pelanggan dengan AI Assistant, identifikasi kendala teknis perangkat, dan tindak lanjuti permintaan secara cepat.
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="<?= BASEURL; ?>/<?= $data['userRole']; ?>" class="btn btn-outline-secondary rounded-pill px-3 btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <?php if ($data['userRole'] === 'operator') : ?>
                <a href="<?= BASEURL; ?>/operator/orders" class="btn btn-warning rounded-pill px-3 btn-sm shadow-sm fw-bold text-dark">
                    <i class="bi bi-cart me-1"></i> Kelola Pesanan
                </a>
            <?php elseif ($data['userRole'] === 'technician') : ?>
                <a href="<?= BASEURL; ?>/technician/jobs" class="btn btn-info rounded-pill px-3 btn-sm shadow-sm fw-bold text-white">
                    <i class="bi bi-tools me-1"></i> Tugas Lapangan
                </a>
            <?php elseif ($data['userRole'] === 'admin') : ?>
                <a href="<?= BASEURL; ?>/aiConfig" class="btn btn-primary rounded-pill px-3 btn-sm shadow-sm fw-bold">
                    <i class="bi bi-gear-fill me-1"></i> Pengaturan AI
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flash Alert -->
    <div class="row">
        <div class="col-12">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Percakapan -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Percakapan</span>
                        <div class="icon-shape icon-shape-sm bg-gradient-primary shadow-primary text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-chat-quote-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($data['stats']['total'] ?? 0); ?></h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Sesi konsultasi tercatat</small>
                </div>
            </div>
        </div>

        <!-- Keluhan Terdeteksi -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-elevate border-start border-danger border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-danger small fw-bold">Keluhan Terdeteksi</span>
                        <div class="icon-shape icon-shape-sm bg-gradient-danger shadow-danger text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger"><?= number_format($data['stats']['complaints'] ?? 0); ?></h3>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0" style="font-size: 11px;">
                        Perlu Perhatian Tim
                    </span>
                </div>
            </div>
        </div>

        <!-- Konsultasi Hari Ini -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Hari Ini</span>
                        <div class="icon-shape icon-shape-sm bg-gradient-info shadow-info text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-info"><?= number_format($data['stats']['today'] ?? 0); ?></h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Aktivitas 24 jam terakhir</small>
                </div>
            </div>
        </div>

        <!-- Telah Ditindaklanjuti -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-elevate">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Ditindaklanjuti</span>
                        <div class="icon-shape icon-shape-sm bg-gradient-success shadow-success text-white rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success"><?= number_format($data['stats']['followed_up'] ?? 0); ?></h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Ditangani Operator / Teknisi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="<?= BASEURL; ?>/chatHistory" method="GET" class="row g-3 align-items-center" id="filterForm">
                <!-- Type Filter Tabs -->
                <div class="col-12 col-md-auto mb-2 mb-md-0">
                    <div class="btn-group w-100 w-md-auto" role="group">
                        <a href="<?= BASEURL; ?>/chatHistory?type=all&status=<?= $data['status']; ?>&date=<?= $data['dateFilter']; ?>&q=<?= urlencode($data['keyword']); ?>" 
                           class="btn btn-sm <?= ($data['filterType'] === 'all') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            Semua (<?= $data['stats']['total'] ?? 0; ?>)
                        </a>
                        <a href="<?= BASEURL; ?>/chatHistory?type=complaints&status=<?= $data['status']; ?>&date=<?= $data['dateFilter']; ?>&q=<?= urlencode($data['keyword']); ?>" 
                           class="btn btn-sm <?= ($data['filterType'] === 'complaints') ? 'btn-danger' : 'btn-outline-danger'; ?>">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Keluhan (<?= $data['stats']['complaints'] ?? 0; ?>)
                        </a>
                        <a href="<?= BASEURL; ?>/chatHistory?type=general&status=<?= $data['status']; ?>&date=<?= $data['dateFilter']; ?>&q=<?= urlencode($data['keyword']); ?>" 
                           class="btn btn-sm <?= ($data['filterType'] === 'general') ? 'btn-info text-white' : 'btn-outline-info'; ?>">
                            <i class="bi bi-lightbulb-fill me-1"></i> Konsultasi Umum
                        </a>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-6 col-md-auto">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($data['filterType']); ?>">
                    <select name="status" class="form-select form-select-sm rounded-pill" onchange="document.getElementById('filterForm').submit()">
                        <option value="all" <?= ($data['status'] === 'all') ? 'selected' : ''; ?>>Semua Status</option>
                        <option value="new" <?= ($data['status'] === 'new') ? 'selected' : ''; ?>>Baru</option>
                        <option value="followed_up" <?= ($data['status'] === 'followed_up') ? 'selected' : ''; ?>>Ditindaklanjuti</option>
                        <option value="closed" <?= ($data['status'] === 'closed') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-6 col-md-auto">
                    <select name="date" class="form-select form-select-sm rounded-pill" onchange="document.getElementById('filterForm').submit()">
                        <option value="all" <?= ($data['dateFilter'] === 'all') ? 'selected' : ''; ?>>Semua Waktu</option>
                        <option value="today" <?= ($data['dateFilter'] === 'today') ? 'selected' : ''; ?>>Hari Ini</option>
                        <option value="week" <?= ($data['dateFilter'] === 'week') ? 'selected' : ''; ?>>Minggu Ini</option>
                        <option value="month" <?= ($data['dateFilter'] === 'month') ? 'selected' : ''; ?>>Bulan Ini</option>
                    </select>
                </div>

                <!-- Keyword Search -->
                <div class="col-12 col-md flex-grow-1">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control rounded-start-pill px-3" 
                               placeholder="Cari keluhan (cth: mati, laptop, cctv, lambat, nama)..." 
                               value="<?= htmlspecialchars($data['keyword']); ?>">
                        <button type="submit" class="btn btn-primary rounded-end-pill px-3">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($data['keyword']) || $data['filterType'] !== 'all' || $data['status'] !== 'all' || $data['dateFilter'] !== 'all') : ?>
                            <a href="<?= BASEURL; ?>/chatHistory" class="btn btn-outline-secondary btn-sm ms-2 rounded-pill px-3" title="Reset Filter">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Conversations Table / Feed -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-transparent py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-bold text-dark">
                <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Percakapan &amp; Keluhan
                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2 px-2 py-1" style="font-size: 11px;">
                    <?= count($data['conversations']); ?> Percakapan
                </span>
            </div>
            <div class="small text-muted">
                Klik tombol <strong>Lihat Transkrip</strong> untuk membaca detail dialog customer &amp; AI
            </div>
        </div>

        <?php if (empty($data['conversations'])) : ?>
            <!-- Empty State -->
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-4 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-chat-heart fs-1"></i>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Belum Ada Percakapan Sesuai Filter</h5>
                <p class="text-muted small mb-3">
                    <?php if (!empty($data['keyword'])) : ?>
                        Tidak ditemukan percakapan dengan kata kunci "<strong><?= htmlspecialchars($data['keyword']); ?></strong>".
                    <?php else : ?>
                        Percakapan baru dari pelanggan atau tamu web akan otomatis tercatat dan muncul di halaman ini.
                    <?php endif; ?>
                </p>
                <a href="<?= BASEURL; ?>/chatHistory" class="btn btn-sm btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang Semua Data
                </a>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 250px;">Pelanggan / Pengguna</th>
                            <th>Topik &amp; Keluhan Utama</th>
                            <th style="width: 130px;" class="text-center">Tipe &amp; Pesan</th>
                            <th style="width: 140px;" class="text-center">Status</th>
                            <th style="width: 140px;">Waktu Update</th>
                            <th style="width: 150px;" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['conversations'] as $c) : 
                            $displayName = !empty($c['user_name']) ? $c['user_name'] : (!empty($c['guest_name']) ? $c['guest_name'] : 'Pengunjung Web');
                            $userRole = !empty($c['user_role']) ? $c['user_role'] : 'guest';
                            $avatarInitial = strtoupper(substr($displayName, 0, 1));
                            $isComplaint = !empty($c['is_complaint']);
                            
                            $statusBadge = [
                                'new' => ['class' => 'bg-danger text-white', 'text' => 'Baru / Menunggu', 'icon' => 'bi-bell-fill'],
                                'followed_up' => ['class' => 'bg-info text-white', 'text' => 'Ditindaklanjuti', 'icon' => 'bi-arrow-repeat'],
                                'closed' => ['class' => 'bg-success text-white', 'text' => 'Selesai', 'icon' => 'bi-check-circle-fill']
                            ][$c['status']] ?? ['class' => 'bg-secondary text-white', 'text' => $c['status'], 'icon' => 'bi-circle'];
                        ?>
                        <tr>
                            <!-- Customer Column -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle <?= $isComplaint ? 'bg-danger text-white' : 'bg-primary text-white'; ?> d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                                        <?= $avatarInitial; ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                            <?= htmlspecialchars($displayName); ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <?php if ($userRole === 'customer') : ?>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0" style="font-size: 10px;">
                                                    Pelanggan
                                                </span>
                                            <?php elseif ($userRole === 'guest') : ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0" style="font-size: 10px;">
                                                    Tamu Web
                                                </span>
                                            <?php else : ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-0" style="font-size: 10px;">
                                                    <?= ucfirst($userRole); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($c['user_email'])) : ?>
                                                <small class="text-muted text-truncate d-none d-lg-inline" style="font-size: 11px; max-width: 110px;">
                                                    &bull; <?= htmlspecialchars($c['user_email']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Topic & Initial Complaint -->
                            <td>
                                <div class="mb-1">
                                    <span class="fw-bold text-dark me-1" style="font-size: 0.92rem;">
                                        <?= htmlspecialchars($c['title'] ?? 'Konsultasi Layanan'); ?>
                                    </span>
                                    <?php if ($isComplaint) : ?>
                                        <span class="badge bg-danger rounded-pill px-2 py-0" style="font-size: 10px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Keluhan
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 420px; font-style: italic;">
                                    <?php if (!empty($c['first_question'])) : ?>
                                        &ldquo;<?= htmlspecialchars(mb_substr($c['first_question'], 0, 80)) . (mb_strlen($c['first_question']) > 80 ? '...' : ''); ?>&rdquo;
                                    <?php else : ?>
                                        <span class="text-muted opacity-75">Percakapan dimulai</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Type & Messages Count -->
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-chat-left-dots me-1"></i><?= (int)($c['message_count'] ?? 0); ?> Pesan
                                </span>
                            </td>

                            <!-- Status Column with Quick Update -->
                            <td class="text-center">
                                <form action="<?= BASEURL; ?>/chatHistory/updateStatus" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $c['id']; ?>">
                                    <select name="status" class="form-select form-select-sm rounded-pill fw-semibold border-0 text-center shadow-none <?= $statusBadge['class']; ?>" style="font-size: 11.5px; padding-left: 10px; padding-right: 20px; cursor: pointer;" onchange="this.form.submit()" title="Ganti status penanganan">
                                        <option value="new" class="bg-white text-dark" <?= ($c['status'] === 'new') ? 'selected' : ''; ?>>Baru</option>
                                        <option value="followed_up" class="bg-white text-dark" <?= ($c['status'] === 'followed_up') ? 'selected' : ''; ?>>Ditindaklanjuti</option>
                                        <option value="closed" class="bg-white text-dark" <?= ($c['status'] === 'closed') ? 'selected' : ''; ?>>Selesai</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Timestamp -->
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <?= date('d M Y', strtotime($c['updated_at'])); ?>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <?= date('H:i', strtotime($c['updated_at'])); ?> WIB
                                </div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-center pe-4">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" 
                                            onclick="openTranscriptModal(<?= $c['id']; ?>)" 
                                            title="Lihat Dialog Lengkap">
                                        <i class="bi bi-eye-fill"></i>
                                        <span class="d-none d-xl-inline">Transkrip</span>
                                    </button>

                                    <?php if ($data['userRole'] === 'admin') : ?>
                                        <a href="<?= BASEURL; ?>/chatHistory/delete/<?= $c['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center" 
                                           style="width: 32px; height: 32px;"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat percakapan ini?')" 
                                           title="Hapus Percakapan">
                                            <i class="bi bi-trash-fill" style="font-size: 13px;"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DETAIL TRANSKRIP PERCAKAPAN CHATBOT               -->
<!-- ======================================================== -->
<div class="modal fade" id="chatTranscriptModal" tabindex="-1" aria-labelledby="chatTranscriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="chatTranscriptModalLabel">
                            Transkrip Percakapan Chatbot
                        </h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-white text-primary rounded-pill px-2 py-0" id="modalCustomerName" style="font-size: 11px;">
                                Memuat...
                            </span>
                            <span id="modalComplaintBadge" class="badge bg-danger rounded-pill px-2 py-0 d-none" style="font-size: 11px;">
                                ⚠️ Keluhan Teknis
                            </span>
                            <small class="text-white-50" id="modalTimeBadge" style="font-size: 11px;"></small>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body (Dialogue Bubbles) -->
            <div class="modal-body p-4" style="background-color: #f8f9fe; min-height: 380px; max-height: 60vh; overflow-y: auto;" id="modalChatBody">
                <div class="text-center py-5 text-muted" id="modalLoadingSpinner">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Memuat percakapan...</span>
                    </div>
                    <div class="small">Mengambil riwayat percakapan...</div>
                </div>
                <div id="modalChatThread" class="d-flex flex-column gap-3">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Modal Footer (Status actions & WhatsApp follow-up) -->
            <div class="modal-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center border-top">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold">Ubah Status:</span>
                    <form action="<?= BASEURL; ?>/chatHistory/updateStatus" method="POST" id="modalStatusForm" class="d-inline">
                        <input type="hidden" name="id" id="modalConvIdInput" value="">
                        <select name="status" id="modalStatusSelect" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="new">Baru / Menunggu</option>
                            <option value="followed_up">Ditindaklanjuti</option>
                            <option value="closed">Selesai / Ditutup</option>
                        </select>
                    </form>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <a href="#" id="modalWaBtn" class="btn btn-sm btn-success rounded-pill px-3 fw-bold d-none" target="_blank">
                        <i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-elevate {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-elevate:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}

/* Chat Transcript Bubble Styles */
.transcript-bubble {
    max-width: 82%;
    padding: 12px 16px;
    border-radius: 18px;
    line-height: 1.5;
    font-size: 13.5px;
    word-break: break-word;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.transcript-bubble-user {
    background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%);
    color: #ffffff;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}
.transcript-bubble-user .bubble-header {
    color: rgba(255, 255, 255, 0.85);
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 4px;
}
.transcript-bubble-ai {
    background-color: #ffffff;
    color: #2b2f38;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.transcript-bubble-ai .bubble-header {
    color: #5e72e4;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 4px;
}

[data-bs-theme="dark"] #chatTranscriptModal .modal-content {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}
[data-bs-theme="dark"] #modalChatBody {
    background-color: #0f172a !important;
}
[data-bs-theme="dark"] #chatTranscriptModal .modal-footer {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .transcript-bubble-ai {
    background-color: #1e293b !important;
    color: #e2e8f0 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}
</style>

<script>
function formatMarkdownText(text) {
    if (!text) return '';
    let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    formatted = formatted.replace(/\n/g, '<br>');
    return formatted;
}

function openTranscriptModal(conversationId) {
    const modalEl = document.getElementById('chatTranscriptModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const spinner = document.getElementById('modalLoadingSpinner');
    const chatThread = document.getElementById('modalChatThread');
    const customerName = document.getElementById('modalCustomerName');
    const complaintBadge = document.getElementById('modalComplaintBadge');
    const timeBadge = document.getElementById('modalTimeBadge');
    const convIdInput = document.getElementById('modalConvIdInput');
    const statusSelect = document.getElementById('modalStatusSelect');
    const waBtn = document.getElementById('modalWaBtn');

    // Reset view
    spinner.classList.remove('d-none');
    chatThread.innerHTML = '';
    customerName.textContent = 'Memuat...';
    complaintBadge.classList.add('d-none');
    timeBadge.textContent = '';
    convIdInput.value = conversationId;
    waBtn.classList.add('d-none');

    // Fetch conversation detail via AJAX
    fetch('<?= BASEURL; ?>/chatHistory/detail/' + conversationId)
        .then(res => res.json())
        .then(data => {
            spinner.classList.add('d-none');
            if (data.status === 'success' && data.conversation) {
                const conv = data.conversation;
                const name = conv.user_name || conv.guest_name || 'Pengunjung Web';
                customerName.textContent = name;

                if (conv.is_complaint == 1) {
                    complaintBadge.classList.remove('d-none');
                }

                timeBadge.textContent = new Date(conv.updated_at).toLocaleString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                if (statusSelect) {
                    statusSelect.value = conv.status || 'new';
                }

                // Messages thread
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const bubble = document.createElement('div');
                        const isUser = (msg.sender === 'user');
                        bubble.className = 'transcript-bubble ' + (isUser ? 'transcript-bubble-user' : 'transcript-bubble-ai');

                        const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        const senderTitle = isUser ? `<i class="bi bi-person-fill me-1"></i> ${name}` : `<i class="bi bi-robot me-1"></i> Asisten AI ADCom`;

                        bubble.innerHTML = `
                            <div class="bubble-header d-flex justify-content-between align-items-center">
                                <span>${senderTitle}</span>
                                <span class="opacity-75" style="font-size: 10px;">${time}</span>
                            </div>
                            <div class="bubble-content">${formatMarkdownText(msg.message)}</div>
                        `;
                        chatThread.appendChild(bubble);
                    });
                } else {
                    chatThread.innerHTML = '<div class="text-center text-muted py-4 small">Belum ada pesan dalam percakapan ini.</div>';
                }

                // Scroll to bottom
                const modalBody = document.getElementById('modalChatBody');
                modalBody.scrollTop = modalBody.scrollHeight;
            } else {
                chatThread.innerHTML = '<div class="alert alert-danger py-2 px-3 small">Gagal memuat transkrip percakapan.</div>';
            }
        })
        .catch(err => {
            console.error('Transcript error:', err);
            spinner.classList.add('d-none');
            chatThread.innerHTML = '<div class="alert alert-danger py-2 px-3 small">Terjadi kesalahan koneksi saat memuat dialog.</div>';
        });
}
</script>
