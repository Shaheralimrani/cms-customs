<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/UserManagementController.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// إدارة المستخدمين متاحة للمدير فقط
if (($_SESSION['role'] ?? '') !== 'manager') {
    http_response_code(403);
    exit('غير مصرح لك بتنفيذ هذه العملية.');
}

// استقبال طلبات POST فقط
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../auth_users.php');
    exit;
}

// إنشاء الـ Controller
$controller = new UserManagementController($pdo);

// تنفيذ العملية المطلوبة
$result = $controller->handle(
    $_POST,
    (int) $_SESSION['user_id']
);

// حفظ النتيجة في الجلسة
if (!empty($result['success'])) {
    $_SESSION['success'] = $result['message'] ?? 'تم تنفيذ العملية بنجاح.';
} else {
    $errors = $result['errors'] ?? ['تعذر تنفيذ العملية.'];
    $_SESSION['error'] = implode(' ', $errors);
}

// العودة إلى صفحة إدارة المستخدمين
header('Location: ../auth_users.php');
exit;