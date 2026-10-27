<?php
declare(strict_types=1);

require_once __DIR__ . '/env_loader.php';

/*
|--------------------------------------------------------------------------
| Application settings
|--------------------------------------------------------------------------
*/

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Aden');

$APP_ENV = getenv('APP_ENV') ?: 'local';
$APP_DEBUG = filter_var(
    getenv('APP_DEBUG') ?: 'true',
    FILTER_VALIDATE_BOOLEAN
);

if (!$APP_DEBUG) {
    ini_set('display_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

/*
|--------------------------------------------------------------------------
| Database settings
|--------------------------------------------------------------------------
*/

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3307';
$dbname = getenv('DB_NAME') ?: 'cms_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false
    ? getenv('DB_PASS')
    : '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

/*
|--------------------------------------------------------------------------
| PDO options
|--------------------------------------------------------------------------
*/

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_STRINGIFY_FETCHES => false,
];

/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
*/

try {
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(500);

    exit('تعذر الاتصال بقاعدة البيانات. تحقق من إعدادات الخادم.');
}

/*
|--------------------------------------------------------------------------
| Safe HTML output helper
|--------------------------------------------------------------------------
*/

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| User access control helper
|--------------------------------------------------------------------------
|
| session_start() must be called before using this function.
|
*/

if (!function_exists('hasAccess')) {
    function hasAccess(string $required_role): bool
    {
        if (
            !isset($_SESSION['user_id'])
            || !isset($_SESSION['role'])
        ) {
            return false;
        }

        if ($_SESSION['role'] === 'manager') {
            return true;
        }

        return $_SESSION['role'] === $required_role;
    }
}