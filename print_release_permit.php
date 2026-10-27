<?php

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Controllers/ReleasePermitController.php';

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'manager'
) {
    header('Location: login.php');
    exit;
}

$permitId = trim((string) ($_GET['permit_id'] ?? ''));

if ($permitId === '' || strlen($permitId) > 30) {
    http_response_code(400);
    exit('رقم إذن الإفراج غير صالح.');
}

$controller = new ReleasePermitController($pdo);
$permit = $controller->print($permitId);

if (!$permit) {
    http_response_code(404);
    exit('إذن الإفراج غير موجود.');
}

require __DIR__ . '/resources/views/release_permits/print.php';
