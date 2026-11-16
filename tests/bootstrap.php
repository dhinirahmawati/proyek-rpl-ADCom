<?php

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start mock session for testing
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Ensure testing constants are defined
if (!defined('BASEURL')) {
    define('BASEURL', 'http://localhost/ADCom');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'adcom_db');
}
if (!defined('ENV_MODE')) {
    define('ENV_MODE', 'testing');
}
if (!defined('AI_API_KEY')) {
    define('AI_API_KEY', '');
}
if (!defined('WHATSAPP_PROVIDER')) {
    define('WHATSAPP_PROVIDER', 'mock');
    define('WHATSAPP_API_URL', 'https://mock.adcom.id');
    define('WHATSAPP_API_TOKEN', 'mock_token');
    define('WHATSAPP_SENDER_ID', '');
}

// Require Core Classes
require_once __DIR__ . '/../app/core/Env.php';
require_once __DIR__ . '/../app/core/CSRF.php';
require_once __DIR__ . '/../app/core/Flasher.php';

// Require Services
require_once __DIR__ . '/../app/services/WhatsApp/WhatsAppServiceInterface.php';
require_once __DIR__ . '/../app/services/WhatsApp/Adapters/MockWhatsAppAdapter.php';
require_once __DIR__ . '/../app/services/WhatsApp/WhatsAppService.php';
require_once __DIR__ . '/../app/services/AIService.php';

// Require TestCase Shim
require_once __DIR__ . '/TestCaseShim.php';
