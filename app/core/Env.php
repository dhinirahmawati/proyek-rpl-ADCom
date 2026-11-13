<?php

class Env {
    private static $loaded = false;
    private static $variables = [];

    /**
     * Load .env file from root directory
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__, 2) . '/.env';
        }

        if (file_exists($path) && is_readable($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }

                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Strip surrounding quotes if present
                    if (
                        (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                        (str_starts_with($value, "'") && str_ends_with($value, "'"))
                    ) {
                        $value = substr($value, 1, -1);
                    }

                    self::$variables[$key] = $value;
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("{$key}={$value}");
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable safely with fallback
     */
    public static function get($key, $default = null) {
        self::load();

        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        $envVal = getenv($key);
        if ($envVal !== false) {
            return $envVal;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return $default;
    }

    /**
     * Mask sensitive strings for safe dashboard display
     * Example: "abc12345xyz" -> "••••••••5xyz"
     */
    public static function mask($secret, $visibleSuffix = 4) {
        if (empty($secret) || $secret === 'ISI_TOKEN_DI_ENV' || $secret === 'ISI_API_KEY_DI_ENV') {
            return 'Belum Dikonfigurasi';
        }

        $len = strlen($secret);
        if ($len <= $visibleSuffix) {
            return str_repeat('•', 8);
        }

        $suffix = substr($secret, -$visibleSuffix);
        return str_repeat('•', 8) . $suffix;
    }

    /**
     * Safely update or write environment variables in .env file
     */
    public static function setMany(array $values): bool {
        $path = dirname(__DIR__, 2) . '/.env';
        if (!file_exists($path)) {
            touch($path);
        }

        $content = file_get_contents($path);
        foreach ($values as $key => $value) {
            $value = trim((string)$value);
            $pattern = "/^" . preg_quote($key, '/') . "=.*$/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        return (bool)file_put_contents($path, $content);
    }
}
