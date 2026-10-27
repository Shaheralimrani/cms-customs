<?php
declare(strict_types=1);

/**
 * Environment loader with safe defaults.
 * Does not overwrite variables already provided by the environment.
 */

if (!defined('DEFAULT_VALUES')) {
    define('DEFAULT_VALUES', [
        'APP_ENV'      => 'local',
        'APP_DEBUG'    => 'true',
        'APP_NAME'     => 'نظام الجمارك',
        'APP_TIMEZONE' => 'Asia/Aden',
        'DB_HOST'      => 'localhost',
        'DB_PORT'      => '3307',
        'DB_NAME'      => 'cms_db',
        'DB_USER'      => 'root',
        'DB_PASS'      => '',
        'DB_CHARSET'   => 'utf8mb4',
    ]);
}

$envFile = __DIR__ . '/../.env';

/**
 * Read .env only when it exists and is readable.
 */
if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES);

    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);

            // Ignore empty lines and full-line comments.
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Split at the first "=" only.
            $separator = strpos($line, '=');

            if ($separator === false) {
                continue;
            }

            $key = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));

            // Accept valid environment variable names only.
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                continue;
            }

            // Do not overwrite values already supplied by the environment.
            $existing = getenv($key);

            if ($existing !== false) {
                $_ENV[$key] = $existing;
                continue;
            }

            // Remove inline comments outside quoted values.
            $quote = null;
            $valueLength = strlen($value);

            for ($i = 0; $i < $valueLength; $i++) {
                $char = $value[$i];

                if (($char === '"' || $char === "'") &&
                    ($i === 0 || $value[$i - 1] !== '\\')) {
                    if ($quote === null) {
                        $quote = $char;
                    } elseif ($quote === $char) {
                        $quote = null;
                    }

                    continue;
                }

                if ($char === '#' && $quote === null) {
                    if ($i === 0 || ctype_space($value[$i - 1])) {
                        $value = rtrim(substr($value, 0, $i));
                        break;
                    }
                }
            }

            // Remove matching surrounding quotes.
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = substr($value, -1);

                if (($first === '"' || $first === "'") && $last === $first) {
                    $value = substr($value, 1, -1);
                } elseif ($first === '"' || $first === "'") {
                    // Ignore malformed quoted values.
                    continue;
                }
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Apply defaults only when the environment variable is absent.
 * Existing environment values always take priority.
 */
foreach (DEFAULT_VALUES as $key => $defaultValue) {
    $existing = getenv($key);

    if ($existing === false) {
        putenv($key . '=' . $defaultValue);
        $_ENV[$key] = $defaultValue;
    } else {
        $_ENV[$key] = $existing;
    }
}