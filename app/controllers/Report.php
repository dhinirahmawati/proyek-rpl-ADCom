<?php

class Report extends Controller {
    public function __construct() {
        AuthGuard::checkLogin();
        if (!in_array($_SESSION['user_role'], ['admin', 'operator'])) {
            Flasher::setFlash('Akses Ditolak', 'Anda tidak memiliki hak akses', 'danger');
            header('Location: ' . BASEURL . '/' . $_SESSION['user_role']);
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Laporan dan Monitoring';
        
        // Determine filter dates
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'bulan';
        $start_date = '';
        $end_date = '';

        if ($filter == 'hari') {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        } elseif ($filter == 'minggu') {
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
        } elseif ($filter == 'bulan') {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } elseif ($filter == 'tahun') {
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
        } elseif ($filter == 'custom') {
            $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
            $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        }

        $data['current_filter'] = $filter;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Fetch Data from ReportModel
        $reportModel = $this->model('ReportModel');
        
        $data['summary'] = $reportModel->getOrdersSummary($start_date, $end_date);
        $data['revenue'] = $reportModel->getTotalRevenue($start_date, $end_date);
        
        $data['by_status'] = $reportModel->getOrdersByStatus($start_date, $end_date);
        $data['by_service'] = $reportModel->getOrdersByService($start_date, $end_date);
        
        $data['trend_orders'] = $reportModel->getOrdersTrend($start_date, $end_date);
        $data['trend_revenue'] = $reportModel->getRevenueTrend($start_date, $end_date);
        
        $data['tech_performance'] = $reportModel->getTechnicianPerformance($start_date, $end_date);
        $data['service_ratings'] = $reportModel->getServiceRatings($start_date, $end_date);
        $data['top_services'] = $reportModel->getTopServices($start_date, $end_date, 5);

        $this->view('templates/header', $data);
        $this->view('report/index', $data);
        $this->view('templates/footer');
    }
}
