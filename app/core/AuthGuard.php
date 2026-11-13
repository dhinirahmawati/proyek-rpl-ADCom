<?php

class AuthGuard {
    public static function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            Flasher::setFlash('Harap login terlebih dahulu', 'untuk mengakses halaman ini', 'danger');
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public static function checkRole($role) {
        self::checkLogin();
        
        $hasAccess = is_array($role) 
            ? in_array($_SESSION['user_role'], $role) 
            : ($_SESSION['user_role'] === $role);

        if (!$hasAccess) {
            Flasher::setFlash('Akses Ditolak', 'Anda tidak memiliki hak akses untuk halaman ini', 'danger');
            
            // Redirect back to their respective dashboard
            $userRole = $_SESSION['user_role'] ?? 'auth/login';
            header('Location: ' . BASEURL . '/' . $userRole);
            exit;
        }
    }

    public static function guestOnly() {
        if (isset($_SESSION['user_id'])) {
            // Already logged in, redirect to their dashboard
            $userRole = $_SESSION['user_role'];
            header('Location: ' . BASEURL . '/' . $userRole);
            exit;
        }
    }
}
