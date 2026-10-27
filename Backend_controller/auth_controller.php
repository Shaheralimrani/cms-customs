<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$controller = new AuthController($pdo);
$result = $controller->login($_POST);

if (!$result['success']) {
    $_SESSION['login_error'] = $result['error'];

    header('Location: ../login.php');
    exit;
}

// توجيه جميع الأدوار إلى لوحة التحكم.
header('Location: ../dashboard.php');
exit;