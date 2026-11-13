<div class="container-fluid px-3 px-lg-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-graph-up me-2"></i>Laporan dan Monitoring</h2>
        <a href="<?= BASEURL; ?>/<?= $_SESSION['user_role']; ?>" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4 rounded-4 bg-light">
        <div class="card-body p-3">
            <form action="<?= BASEURL; ?>/report" method="GET" class="row g-3 align-items-end" id="filterForm">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Periode Laporan</label>
                    <select name="filter" id="filterSelect" class="form-select">
                        <option value="hari" <?= $data['current_filter'] == 'hari' ? 'selected' : ''; ?>>Hari Ini</option>
                        <option value="minggu" <?= $data['current_filter'] == 'minggu' ? 'selected' : ''; ?>>Minggu Ini</option>
                        <option value="bulan" <?= $data['current_filter'] == 'bulan' ? 'selected' : ''; ?>>Bulan Ini</option>
                        <option value="tahun" <?= $data['current_filter'] == 'tahun' ? 'selected' : ''; ?>>Tahun Ini</option>
                        <option value="custom" <?= $data['current_filter'] == 'custom' ? 'selected' : ''; ?>>Kustom...</option>
                    </select>
                </div>
                
                <div class="col-md-3 custom-date" style="display: <?= $data['current_filter'] == 'custom' ? 'block' : 'none'; ?>;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $data['start_date']; ?>">
                </div>
                
                <div class="col-md-3 custom-date" style="display: <?= $data['current_filter'] == 'custom' ? 'block' : 'none'; ?>;">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $data['end_date']; ?>">
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </form>
            <div class="mt-2 text-muted small text-center">
                Menampilkan data dari: <strong><?= date('d M Y', strtotime($data['start_date'])); ?></strong> sampai <strong><?= date('d M Y', strtotime($data['end_date'])); ?></strong>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-cart3 display-4 opacity-50 mb-2"></i>
                    <h5 class="card-title">Total Pesanan</h5>
                    <h2 class="fw-bold mb-0"><?= $data['summary']['total_orders']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-check-circle display-4 opacity-50 mb-2"></i>
                    <h5 class="card-title">Pesanan Selesai</h5>
                    <h2 class="fw-bold mb-0"><?= $data['summary']['total_completed'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-hourglass-split display-4 opacity-50 mb-2"></i>
                    <h5 class="card-title">Pesanan Proses</h5>
                    <h2 class="fw-bold mb-0"><?= $data['summary']['total_pending'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-cash-stack display-4 opacity-50 mb-2"></i>
                    <h5 class="card-title">Estimasi Pendapatan</h5>
                    <h3 class="fw-bold mb-0 text-truncate">Rp <?= number_format($data['revenue'], 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white pt-3 pb-0 border-0">
                    <h5 class="fw-bold">Tren Pendapatan & Pesanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white pt-3 pb-0 border-0">
                    <h5 class="fw-bold">Pesanan Berdasarkan Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white pt-3 pb-0 border-0">
                    <h5 class="fw-bold">Pesanan Berdasarkan Layanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="serviceChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white pt-3 border-0">
                    <h5 class="fw-bold mb-0">Performa Teknisi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Teknisi</th>
                                    <th class="text-center">Ditugaskan</th>
                                    <th class="text-center">Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['tech_performance'])): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>
                                <?php else: ?>
                                    <?php foreach($data['tech_performance'] as $tech): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold"><?= htmlspecialchars($tech['name']); ?></td>
                                        <td class="text-center"><?= $tech['total_assigned']; ?></td>
                                        <td class="text-center text-success fw-bold"><?= $tech['total_completed']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white pt-3 border-0">
                    <h5 class="fw-bold mb-0">Layanan Paling Banyak Dipesan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Layanan</th>
                                    <th class="text-center">Total Pesanan</th>
                                    <th class="text-center">Rating Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['top_services'])): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>
                                <?php else: ?>
                                    <?php foreach($data['top_services'] as $svc): 
                                        // Cari rating untuk layanan ini
                                        $rating = 0;
                                        foreach($data['service_ratings'] as $sr) {
                                            if($sr['name'] == $svc['name']) {
                                                $rating = $sr['avg_rating'];
                                                break;
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="ps-3"><?= htmlspecialchars($svc['name']); ?></td>
                                        <td class="text-center fw-bold"><?= $svc['total_orders']; ?></td>
                                        <td class="text-center text-warning">
                                            <?= $rating > 0 ? number_format($rating, 1) . ' <i class="bi bi-star-fill"></i>' : '-'; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Handle Custom Date Filter Display
    document.getElementById('filterSelect').addEventListener('change', function() {
        const customDates = document.querySelectorAll('.custom-date');
        if (this.value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
        } else {
            customDates.forEach(el => el.style.display = 'none');
        }
    });

    // Chart Options Default
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // 1. Status Chart (Doughnut)
    const statusData = <?= json_encode($data['by_status']); ?>;
    const statusLabels = statusData.map(item => item.status.toUpperCase());
    const statusCounts = statusData.map(item => item.count);
    const statusColors = statusLabels.map(label => {
        if(label === 'COMPLETED') return '#198754';
        if(label === 'PENDING') return '#ffc107';
        if(label === 'PROCESSING') return '#0dcaf0';
        if(label === 'CANCELLED') return '#dc3545';
        return '#6c757d';
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });

    // 2. Service Chart (Bar)
    const serviceData = <?= json_encode($data['by_service']); ?>;
    const serviceLabels = serviceData.map(item => item.service_name);
    const serviceCounts = serviceData.map(item => item.count);

    new Chart(document.getElementById('serviceChart'), {
        type: 'bar',
        data: {
            labels: serviceLabels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: serviceCounts,
                backgroundColor: '#0d6efd',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // 3. Trend Chart (Line)
    // Combine orders and revenue by date
    const trendOrdersData = <?= json_encode($data['trend_orders']); ?>;
    const trendRevData = <?= json_encode($data['trend_revenue']); ?>;
    
    // Create unified date array
    const dateSet = new Set([
        ...trendOrdersData.map(item => item.date),
        ...trendRevData.map(item => item.date)
    ]);
    const trendDates = Array.from(dateSet).sort();
    
    const ordersMap = Object.fromEntries(trendOrdersData.map(item => [item.date, item.count]));
    const revMap = Object.fromEntries(trendRevData.map(item => [item.date, item.revenue]));

    const mappedOrders = trendDates.map(date => ordersMap[date] || 0);
    const mappedRev = trendDates.map(date => revMap[date] || 0);

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendDates,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: mappedRev,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Jumlah Pesanan',
                    data: mappedOrders,
                    borderColor: '#0d6efd',
                    backgroundColor: '#0d6efd',
                    yAxisID: 'y',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Jumlah Pesanan' },
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Pendapatan (Rp)' },
                    grid: { drawOnChartArea: false },
                    beginAtZero: true
                }
            }
        }
    });
</script>
