<?php
$isAdminUser = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
$reqUrl = $_GET['url'] ?? '';
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$isAdminArea = $isAdminUser && (
    strpos($reqUrl, 'admin') === 0 || 
    strpos($reqUrl, 'report') === 0 || 
    strpos($reqUrl, 'aiConfig') === 0 ||
    strpos($reqUrl, 'chatHistory') === 0 ||
    stripos($reqUri, '/admin') !== false ||
    stripos($reqUri, '/report') !== false ||
    stripos($reqUri, '/aiConfig') !== false ||
    stripos($reqUri, '/chatHistory') !== false
);

$isActivePage = function($pattern) use ($reqUrl, $reqUri) {
    if (is_array($pattern)) {
        foreach ($pattern as $p) {
            if (stripos($reqUrl, $p) !== false || stripos($reqUri, $p) !== false) return true;
        }
        return false;
    }
    return (stripos($reqUrl, $pattern) !== false || stripos($reqUri, $pattern) !== false);
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul']; ?></title>
    <!-- Dark Mode Initializer (Prevents FOUC / Flashing) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('adcom_theme') || 'light';
            let appliedTheme = savedTheme;
            if (savedTheme === 'auto') {
                appliedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', appliedTheme);

            <?php if ($isAdminArea) : ?>
            const savedSidebar = localStorage.getItem('adcom_admin_sidebar');
            if (savedSidebar === 'collapsed' && window.innerWidth >= 992) {
                document.documentElement.classList.add('admin-sidebar-collapsed');
            }
            <?php endif; ?>
        })();
    </script>
    <!-- Google Fonts: Open Sans (Argon standard) & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Nucleo Icons (Argon Dashboard 2 standard) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/argon-dashboard/2.0.4/css/nucleo-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/argon-dashboard/2.0.4/css/nucleo-svg.min.css" rel="stylesheet">

    <style>
        :root {
            --argon-primary: #5e72e4;
            --argon-secondary: #8392ab;
            --argon-success: #2dce89;
            --argon-info: #11cdef;
            --argon-warning: #fb6340;
            --argon-danger: #f5365c;
            --argon-dark: #172b4d;
            --argon-font: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        body {
            font-family: var(--argon-font);
            background-color: #f8f9fe;
            color: #525f7f;
            transition: background-color 0.25s ease, color 0.25s ease;
            letter-spacing: -0.015em;
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: var(--argon-font);
            letter-spacing: -0.025em;
            color: #32325d;
        }

        /* Argon Signature Gradients */
        .bg-gradient-primary {
            background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%) !important;
        }
        .bg-gradient-dark {
            background: linear-gradient(310deg, #141727 0%, #3a416f 100%) !important;
        }
        .bg-gradient-info {
            background: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%) !important;
        }
        .bg-gradient-success {
            background: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%) !important;
        }
        .bg-gradient-warning {
            background: linear-gradient(310deg, #f53939 0%, #fbcf33 100%) !important;
        }
        .bg-gradient-danger {
            background: linear-gradient(310deg, #ea0606 0%, #ff667c 100%) !important;
        }

        /* Argon Icon Shapes */
        .icon-shape {
            width: 48px;
            height: 48px;
            background-position: center;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            vertical-align: middle;
            transition: all 0.25s ease;
        }
        .icon-shape i {
            color: #ffffff;
            font-size: 1.25rem;
        }
        .icon-shape-sm {
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
        }
        .icon-shape-sm i {
            font-size: 1rem;
        }
        .icon-shape-lg {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
        }
        .icon-shape-lg i {
            font-size: 1.75rem;
        }

        /* Glowing Drop Shadows for Icons */
        .shadow-primary {
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(94, 114, 228, .4) !important;
        }
        .shadow-info {
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(33, 212, 253, .4) !important;
        }
        .shadow-success {
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(45, 206, 137, .4) !important;
        }
        .shadow-warning {
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(251, 99, 64, .4) !important;
        }
        .shadow-danger {
            box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(245, 54, 92, .4) !important;
        }

        /* Argon Card Styling */
        .card {
            border: 0;
            border-radius: 1rem;
            background-color: #ffffff;
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.25s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.25s ease;
        }

        /* Argon Buttons */
        .btn {
            font-weight: 600;
            letter-spacing: -0.015em;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary {
            background-image: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 7px -1px rgba(0, 0, 0, 0.11), 0 2px 4px -1px rgba(0, 0, 0, 0.07);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            color: #ffffff !important;
        }
        .btn-outline-primary {
            border-color: #5e72e4 !important;
            color: #5e72e4 !important;
        }
        .btn-outline-primary:hover {
            background-image: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            transform: translateY(-1px);
        }
        .btn-white {
            background-color: #ffffff !important;
            color: #344767 !important;
            border: none !important;
            box-shadow: 0 4px 7px -1px rgba(0, 0, 0, 0.11), 0 2px 4px -1px rgba(0, 0, 0, 0.07);
        }
        .btn-white:hover {
            background-color: #f6f9fc !important;
            color: #5e72e4 !important;
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        /* Argon Navbar Glassmorphism */
        .argon-navbar {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: all 0.25s ease;
            z-index: 1030;
        }
        /* Navbar in Light Mode */
        [data-bs-theme="light"] .argon-navbar {
            background: rgba(255, 255, 255, 0.88) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.04);
        }
        [data-bs-theme="light"] .argon-navbar .navbar-brand {
            color: #172b4d !important;
        }
        [data-bs-theme="light"] .argon-navbar .nav-link {
            color: #525f7f !important;
            font-weight: 600;
        }
        [data-bs-theme="light"] .argon-navbar .nav-link:hover,
        [data-bs-theme="light"] .argon-navbar .nav-link.active {
            color: #5e72e4 !important;
        }
        [data-bs-theme="light"] .argon-navbar #quickThemeBtn {
            color: #525f7f !important;
        }

        /* Hero Section (Argon Curved Gradient Header) */
        .hero-section {
            background: linear-gradient(310deg, #5e72e4 0%, #825ee4 50%, #5e72e4 100%);
            color: white;
            padding: 90px 0 110px 0;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            margin-bottom: 30px;
            box-shadow: 0 12px 35px rgba(94, 114, 228, 0.25);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 65%);
            pointer-events: none;
        }

        .service-card {
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07) !important;
        }

        /* Argon Dark Mode Custom Enhancements (Deep Slate & Navy) */
        [data-bs-theme="dark"] body {
            background-color: #0f172a !important; /* Argon Deep Slate */
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] h1, 
        [data-bs-theme="dark"] h2, 
        [data-bs-theme="dark"] h3, 
        [data-bs-theme="dark"] h4, 
        [data-bs-theme="dark"] h5, 
        [data-bs-theme="dark"] h6 {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .argon-navbar {
            background: rgba(15, 23, 42, 0.88) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.25);
        }
        [data-bs-theme="dark"] .argon-navbar .navbar-brand {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .argon-navbar .nav-link {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .argon-navbar .nav-link:hover,
        [data-bs-theme="dark"] .argon-navbar .nav-link.active {
            color: #825ee4 !important;
        }
        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.07) !important;
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #162032 !important;
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] .table-light {
            background-color: #162032 !important;
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .table {
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.08);
        }
        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(255, 255, 255, 0.035);
            color: #ffffff;
        }
        [data-bs-theme="dark"] .modal-content {
            background-color: #1e293b;
            color: #e6edf3;
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-bs-theme="dark"] .form-control {
            background-color: #162032;
            border-color: rgba(255, 255, 255, 0.15);
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .form-control:focus {
            background-color: #1e293b;
            border-color: #5e72e4;
            color: #ffffff;
        }
        [data-bs-theme="dark"] .input-group-text {
            background-color: #162032;
            border-color: rgba(255, 255, 255, 0.15);
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4) !important;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .border {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        [data-bs-theme="dark"] .btn-white {
            background-color: #2b3952 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .btn-white:hover {
            background-color: #3b4c6b !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .hero-section {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }
        [data-bs-theme="dark"] .theme-option-card {
            background-color: #162032;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        [data-bs-theme="dark"] .theme-option-card.selected-theme {
            border-color: #5e72e4 !important;
            background-color: rgba(94, 114, 228, 0.15) !important;
        }
        [data-bs-theme="dark"] .adcom-bubble-ai {
            background-color: #1e293b !important;
            color: #e6edf3 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        [data-bs-theme="dark"] #adcomChatWindow {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }
        [data-bs-theme="dark"] #adcomChatBox {
            background-color: #0f172a !important;
        }
        [data-bs-theme="dark"] #adcomChatWindow .card-footer {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* =================================================== */
        /* ADMIN COLLAPSIBLE SIDEBAR DASHBOARD                */
        /* =================================================== */
        .admin-sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1045;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(130, 140, 160, 0.25);
            border-radius: 4px;
        }

        .admin-sidebar .sidebar-header {
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .admin-sidebar .sidebar-profile {
            margin: 0.5rem 1rem 0.85rem 1rem;
            padding: 0.85rem 1rem;
            border-radius: 0.85rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .status-indicator-dot {
            width: 8px;
            height: 8px;
            background-color: #2dce89;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(45, 206, 137, 0.35);
        }

        .admin-sidebar .sidebar-nav {
            flex-grow: 1;
            padding: 0 0.85rem 1.5rem 0.85rem;
        }

        .admin-sidebar .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.75rem 0.6rem 0.3rem 0.6rem;
            margin-top: 0.4rem;
        }

        .admin-sidebar .nav-link-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            margin-bottom: 0.25rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .admin-sidebar .nav-icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .admin-sidebar .nav-label {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Light Mode Sidebar */
        [data-bs-theme="light"] .admin-sidebar {
            background: #ffffff;
            border-right: 1px solid rgba(0, 0, 0, 0.07);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        [data-bs-theme="light"] .admin-sidebar .sidebar-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        [data-bs-theme="light"] .admin-sidebar .sidebar-header .text-brand-title {
            color: #172b4d;
        }
        [data-bs-theme="light"] .admin-sidebar .sidebar-profile {
            background: #f8f9fe;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        [data-bs-theme="light"] .admin-sidebar .sidebar-profile .text-name-title {
            color: #172b4d;
        }
        [data-bs-theme="light"] .admin-sidebar .sidebar-heading {
            color: #8392ab;
        }
        [data-bs-theme="light"] .admin-sidebar .nav-link-item {
            color: #525f7f;
        }
        [data-bs-theme="light"] .admin-sidebar .nav-link-item .nav-icon-wrapper {
            background: rgba(94, 114, 228, 0.08);
        }
        [data-bs-theme="light"] .admin-sidebar .nav-link-item:hover {
            background: #f6f9fc;
            color: #5e72e4;
        }
        [data-bs-theme="light"] .admin-sidebar .nav-link-item:hover .nav-icon-wrapper {
            background: #5e72e4;
            color: #ffffff !important;
        }

        /* Dark Mode Sidebar */
        [data-bs-theme="dark"] .admin-sidebar {
            background: #111c44;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }
        [data-bs-theme="dark"] .admin-sidebar .sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        [data-bs-theme="dark"] .admin-sidebar .sidebar-header .text-brand-title {
            color: #ffffff;
        }
        [data-bs-theme="dark"] .admin-sidebar .sidebar-profile {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        [data-bs-theme="dark"] .admin-sidebar .sidebar-profile .text-name-title {
            color: #ffffff;
        }
        [data-bs-theme="dark"] .admin-sidebar .sidebar-heading {
            color: #64748b;
        }
        [data-bs-theme="dark"] .admin-sidebar .nav-link-item {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .admin-sidebar .nav-link-item .nav-icon-wrapper {
            background: rgba(255, 255, 255, 0.06);
        }
        [data-bs-theme="dark"] .admin-sidebar .nav-link-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }
        [data-bs-theme="dark"] .admin-sidebar .nav-link-item:hover .nav-icon-wrapper {
            background: #5e72e4;
            color: #ffffff !important;
        }

        /* Active Nav Link (Both modes) */
        .admin-sidebar .nav-link-item.active {
            background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(94, 114, 228, 0.4);
        }
        .admin-sidebar .nav-link-item.active .nav-icon-wrapper {
            background: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
        }

        /* Floating Re-open Tab (Desktop when collapsed) */
        .admin-sidebar-floating-tab {
            position: fixed;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            z-index: 1040;
            background: linear-gradient(310deg, #5e72e4 0%, #825ee4 100%);
            color: #ffffff;
            border: none;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            padding: 10px 8px;
            cursor: pointer;
            box-shadow: 2px 4px 12px rgba(94, 114, 228, 0.35);
            display: none;
            transition: all 0.2s ease;
        }
        .admin-sidebar-floating-tab:hover {
            padding-left: 12px;
            box-shadow: 3px 6px 16px rgba(94, 114, 228, 0.5);
        }

        /* Desktop Layout Shifts */
        @media (min-width: 992px) {
            body.admin-sidebar-layout:not(.admin-sidebar-collapsed) .argon-navbar,
            body.admin-sidebar-layout:not(.admin-sidebar-collapsed) main.admin-main-content,
            body.admin-sidebar-layout:not(.admin-sidebar-collapsed) footer#tentang-kami {
                margin-left: 270px;
                transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }
            body.admin-sidebar-layout.admin-sidebar-collapsed .admin-sidebar {
                transform: translateX(-100%);
            }
            body.admin-sidebar-layout.admin-sidebar-collapsed .argon-navbar,
            body.admin-sidebar-layout.admin-sidebar-collapsed main.admin-main-content,
            body.admin-sidebar-layout.admin-sidebar-collapsed footer#tentang-kami {
                margin-left: 0;
                transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }
            body.admin-sidebar-layout.admin-sidebar-collapsed .admin-sidebar-floating-tab {
                display: block;
            }
            .admin-sidebar-backdrop {
                display: none !important;
            }
        }

        /* Mobile / Tablet (< 992px) */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                z-index: 1060;
                box-shadow: 0 0 35px rgba(0, 0, 0, 0.5);
            }
            body.admin-sidebar-layout.admin-sidebar-mobile-open .admin-sidebar {
                transform: translateX(0);
            }
            .admin-sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 1050;
            }
            body.admin-sidebar-layout.admin-sidebar-mobile-open .admin-sidebar-backdrop {
                display: block;
            }
        }
    </style>
</head>
<body class="<?= $isAdminArea ? 'admin-sidebar-layout' : ''; ?>">
<?php if ($isAdminArea) : ?>
<script>
    if (document.documentElement.classList.contains('admin-sidebar-collapsed')) {
        document.body.classList.add('admin-sidebar-collapsed');
    }
</script>

<!-- Floating Re-open Button for collapsed sidebar on desktop -->
<button id="adminSidebarFloatingToggle" class="admin-sidebar-floating-tab shadow-sm" title="Buka Sidebar Admin" type="button" onclick="openAdminSidebar()">
    <i class="bi bi-chevron-right"></i>
</button>

<!-- Admin Sidebar Dashboard -->
<aside id="adminSidebar" class="admin-sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <a href="<?= BASEURL; ?>/admin" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="icon-shape icon-shape-sm bg-gradient-primary shadow-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="ni ni-laptop-86 text-white" style="font-size: 1rem;"></i>
            </div>
            <div>
                <span class="fw-bold tracking-tight text-brand-title">ADCom</span>
                <span class="badge bg-gradient-primary text-white rounded-pill px-2 py-0 ms-1 text-xs" style="font-size: 9px; font-weight: 700;">ADMIN</span>
            </div>
        </a>
        <button type="button" class="btn btn-sm btn-link text-muted p-1 border-0" id="adminSidebarCloseBtn" onclick="closeAdminSidebar()" title="Tutup Menu Sidebar">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Profile Box -->
    <div class="sidebar-profile">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-weight: 700; font-size: 1rem; flex-shrink: 0;">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="overflow-hidden">
                <div class="fw-bold text-truncate text-name-title" style="font-size: 0.88rem;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></div>
                <div class="d-flex align-items-center gap-1">
                    <span class="status-indicator-dot"></span>
                    <small class="text-muted" style="font-size: 0.72rem;">Kepala Sekolah</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-nav">
        <!-- Main Navigation -->
        <div class="sidebar-heading">Navigasi Utama</div>
        <?php 
        $isDashboard = ($isActivePage('admin') && !$isActivePage(['admin/users', 'admin/createUser', 'admin/editUser', 'admin/authSettings']));
        ?>
        <a href="<?= BASEURL; ?>/admin" class="nav-link-item <?= $isDashboard ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper text-primary">
                <i class="bi bi-speedometer2"></i>
            </div>
            <span class="nav-label">Dashboard Utama</span>
        </a>

        <?php $isReport = $isActivePage('report'); ?>
        <a href="<?= BASEURL; ?>/report" class="nav-link-item <?= $isReport ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper text-info">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <span class="nav-label">Laporan & Monitoring</span>
        </a>

        <!-- User Management -->
        <div class="sidebar-heading">Manajemen Pengguna</div>
        <?php $isUsers = $isActivePage(['admin/users', 'admin/editUser']); ?>
        <a href="<?= BASEURL; ?>/admin/users" class="nav-link-item <?= $isUsers ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper text-success">
                <i class="bi bi-people-fill"></i>
            </div>
            <span class="nav-label">Data Pengguna</span>
        </a>

        <?php $isCreateUser = $isActivePage('admin/createUser'); ?>
        <a href="<?= BASEURL; ?>/admin/createUser" class="nav-link-item <?= $isCreateUser ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper" style="color: #6f42c1;">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <span class="nav-label">Tambah Pengguna</span>
        </a>

        <!-- System & Configuration -->
        <div class="sidebar-heading">Sistem & Konfigurasi</div>
        <?php $isAiConfig = $isActivePage('aiConfig'); ?>
        <a href="<?= BASEURL; ?>/aiConfig" class="nav-link-item <?= $isAiConfig ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper text-primary">
                <i class="bi bi-robot"></i>
            </div>
            <span class="nav-label">Konfigurasi AI</span>
            <span class="badge bg-primary text-white rounded-pill px-2 py-0 ms-auto" style="font-size: 9px;">Gemini</span>
        </a>

        <?php $isChatHistory = $isActivePage('chatHistory'); ?>
        <a href="<?= BASEURL; ?>/chatHistory" class="nav-link-item <?= $isChatHistory ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper" style="color: #11cdef;">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <span class="nav-label">Riwayat Chat &amp; Keluhan</span>
            <span class="badge bg-danger text-white rounded-pill px-2 py-0 ms-auto" style="font-size: 9px;">Keluhan</span>
        </a>

        <?php $isAuth = $isActivePage('admin/authSettings'); ?>
        <a href="<?= BASEURL; ?>/admin/authSettings" class="nav-link-item <?= $isAuth ? 'active' : ''; ?>">
            <div class="nav-icon-wrapper text-warning">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <span class="nav-label">Autentikasi & OTP</span>
            <span class="badge bg-warning text-dark rounded-pill px-2 py-0 ms-auto" style="font-size: 9px;">WA</span>
        </a>

        <!-- Public Navigation -->
        <div class="sidebar-heading">Akses Publik</div>
        <a href="<?= BASEURL; ?>/service" class="nav-link-item">
            <div class="nav-icon-wrapper text-secondary">
                <i class="bi bi-grid-fill"></i>
            </div>
            <span class="nav-label">Katalog Layanan</span>
        </a>

        <a href="<?= BASEURL; ?>/" class="nav-link-item">
            <div class="nav-icon-wrapper text-secondary">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <span class="nav-label">Beranda Web</span>
        </a>

        <!-- Settings & Session -->
        <div class="sidebar-heading">Pengaturan Akun</div>
        <a href="#" class="nav-link-item" data-bs-toggle="modal" data-bs-target="#settingsThemeModal">
            <div class="nav-icon-wrapper text-warning">
                <i class="bi bi-palette-fill"></i>
            </div>
            <span class="nav-label">Pengaturan Tema</span>
        </a>

        <a href="<?= BASEURL; ?>/auth/logout" class="nav-link-item text-danger">
            <div class="nav-icon-wrapper text-danger">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <span class="nav-label fw-bold">Keluar (Logout)</span>
        </a>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-3 border-top text-center mt-auto">
        <small class="text-muted" style="font-size: 11px;">ADCom &bull; Unit Bisnis Siswa TKJ</small>
    </div>
</aside>
<?php endif; ?>

<header>
<nav class="navbar navbar-expand-lg argon-navbar sticky-top">
  <div class="container<?= $isAdminArea ? '-fluid px-3 px-lg-4' : ''; ?>">
    <div class="d-flex align-items-center">
        <?php if ($isAdminArea) : ?>
            <button class="btn btn-outline-primary btn-sm me-2 d-inline-flex align-items-center gap-1 rounded-3 px-2 py-1 shadow-sm" 
                    id="adminSidebarToggleBtn" 
                    type="button" 
                    onclick="toggleAdminSidebar()" 
                    title="Buka / Tutup Sidebar Dashboard">
                <i class="bi bi-layout-sidebar-inset fs-6"></i>
                <span class="d-none d-sm-inline fw-semibold small" id="adminSidebarToggleText">Sidebar</span>
            </button>
        <?php endif; ?>
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASEURL; ?>">
            <div class="icon-shape icon-shape-sm bg-gradient-primary shadow-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="ni ni-laptop-86 text-white" style="font-size: 1rem;"></i>
            </div>
            <span class="fw-bold tracking-tight">ADCom <span class="badge bg-gradient-primary text-white rounded-pill px-2 py-1 ms-1 text-xs" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">JASA TKJ</span></span>
        </a>
    </div>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link px-3 active" href="<?= BASEURL; ?>">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3" href="<?= BASEURL; ?>/service">Layanan Kami</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3" href="<?= BASEURL; ?>/ai" onclick="if(window.toggleAdcomChat){ toggleAdcomChat(); return false; }">
            <i class="bi bi-robot text-primary me-1"></i>AI Assistant
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3" href="<?= BASEURL; ?>#tentang-kami">Tentang Kami</a>
        </li>
        <li class="nav-item ms-lg-3 mt-3 mt-lg-0 d-flex align-items-center">
          <!-- Quick Theme Switcher Button -->
          <button class="btn btn-link position-relative p-0 border-0 me-3" 
                  type="button" 
                  id="quickThemeBtn" 
                  onclick="toggleQuickTheme()" 
                  title="Ganti Mode Gelap / Terang">
              <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
          </button>

          <?php if (isset($_SESSION['user_id'])) : ?>
            
            <!-- Notification Bell -->
            <div class="dropdown me-3">
              <button class="btn btn-link position-relative p-0 border-0" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell-fill fs-5 text-secondary"></i>
                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                  0
                </span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                <li><div class="dropdown-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Notifikasi</span>
                    <button class="btn btn-sm btn-link text-decoration-none" id="markAllReadBtn" style="font-size: 0.8rem;">Tandai semua dibaca</button>
                </div></li>
                <li><hr class="dropdown-divider"></li>
                <div id="notificationList">
                    <!-- Notifications will be loaded here via JS -->
                    <li><span class="dropdown-item text-center text-muted small">Memuat...</span></li>
                </div>
              </ul>
            </div>

            <div class="dropdown">
              <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-3" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i> <?= $_SESSION['user_name']; ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="<?= BASEURL; ?>/<?= $_SESSION['user_role']; ?>"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Saya</a></li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') : ?>
                  <li><a class="dropdown-item" href="<?= BASEURL; ?>/admin/users"><i class="bi bi-people me-2 text-primary"></i>Manajemen Pengguna</a></li>
                  <li><a class="dropdown-item" href="<?= BASEURL; ?>/admin/authSettings"><i class="bi bi-shield-lock me-2 text-warning"></i>Konfigurasi Autentikasi</a></li>
                  <li><a class="dropdown-item" href="<?= BASEURL; ?>/report"><i class="bi bi-graph-up me-2 text-info"></i>Laporan & Monitoring</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'operator'])) : ?>
                  <li><a class="dropdown-item" href="<?= BASEURL; ?>/aiConfig"><i class="bi bi-robot me-2 text-primary"></i>Konfigurasi AI</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'operator', 'technician'])) : ?>
                  <li><a class="dropdown-item" href="<?= BASEURL; ?>/chatHistory"><i class="bi bi-chat-dots-fill me-2 text-info"></i>Riwayat Chat &amp; Keluhan</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsThemeModal"><i class="bi bi-palette me-2 text-warning"></i>Pengaturan Tema</a></li>
                <li><a class="dropdown-item text-danger" href="<?= BASEURL; ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </div>
          <?php else : ?>
            <button class="btn btn-link me-2 p-0 border-0" type="button" data-bs-toggle="modal" data-bs-target="#settingsThemeModal" title="Pengaturan Tema">
                <i class="bi bi-palette fs-5 text-secondary"></i>
            </button>
            <a class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" href="<?= BASEURL; ?>/auth/login">Login / Daftar</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

</header>

<main class="flex-shrink-0 min-vh-100 <?= $isAdminArea ? 'admin-main-content' : ''; ?>">
<!-- Container for global flasher -->
<?php if (isset($_SESSION['flash']) && $_SERVER['REQUEST_URI'] == '/ADCom/public/') : ?>
<div class="container mt-3">
    <?php Flasher::flash(); ?>
</div>
<?php endif; ?>
