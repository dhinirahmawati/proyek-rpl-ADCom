<?php
date_default_timezone_set('Asia/Jakarta');

define('BASEURL', 'http://localhost/ADCom');

// DB
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'adcom_db');

// App Settings
define('ENV_MODE', class_exists('Env') ? Env::get('ENV_MODE', 'development') : 'development'); // 'development' or 'production'

// AI Settings
define('AI_API_KEY', class_exists('Env') ? Env::get('AI_API_KEY', '') : '');

// WhatsApp API Settings
define('WHATSAPP_PROVIDER', class_exists('Env') ? Env::get('WHATSAPP_PROVIDER', 'mock') : 'mock');
define('WHATSAPP_API_URL', class_exists('Env') ? Env::get('WHATSAPP_API_URL', 'URL_API_DARI_PROVIDER') : 'URL_API_DARI_PROVIDER');
define('WHATSAPP_API_TOKEN', class_exists('Env') ? Env::get('WHATSAPP_API_TOKEN', 'ISI_TOKEN_DI_ENV') : 'ISI_TOKEN_DI_ENV');
define('WHATSAPP_SENDER_ID', class_exists('Env') ? Env::get('WHATSAPP_SENDER_ID', 'ID_PENGIRIM_JIKA_DIBUTUHKAN') : 'ID_PENGIRIM_JIKA_DIBUTUHKAN');

