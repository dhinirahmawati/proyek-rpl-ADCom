<?php

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/Flasher.php';
require_once 'core/AuthGuard.php';
require_once 'core/CSRF.php';
require_once 'core/Env.php';
require_once 'config/config.php';

// Autoload models, services, and core classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/services/' . $class . '.php',
        __DIR__ . '/services/WhatsApp/' . $class . '.php',
        __DIR__ . '/services/WhatsApp/Adapters/' . $class . '.php',
        __DIR__ . '/core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

