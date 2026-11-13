<?php

class CSRF {
    /**
     * Generate a new CSRF token and store it in session
     *
     * @return string
     */
    public static function generate() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Output a hidden input field with the CSRF token
     *
     * @return string
     */
    public static function input() {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Verify the CSRF token from a POST request
     *
     * @return bool
     */
    public static function verify() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
                return false;
            }
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                return false;
            }
        }
        return true;
    }
}
