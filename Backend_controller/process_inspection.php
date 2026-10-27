<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/InspectionController.php';

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['inspector', 'manager'], true)
) {
    http_response_code(403);
    exit('غير مصرح لك بتنفيذ هذه العملية.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../inspection.php');
    exit;
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    $_SESSION['inspection_errors'] = [
        'الطلب غير صالح. أعد تحميل الصفحة.'
    ];

    header('Location: ../inspection.php');
    exit;
}

try {
    $controller = new InspectionController($pdo);

    $result = $controller->process(
        $_POST,
        (int) $_SESSION['user_id']
    );

    if ($result['success']) {
        $_SESSION['inspection_success'] = $result['message'];
        unset($_SESSION['inspection_errors']);
    } else {
        $_SESSION['inspection_errors'] = [
            $result['message']
        ];
        unset($_SESSION['inspection_success']);
    }

} catch (Throwable $e) {
    error_log(
        'process_inspection error: ' . $e->getMessage()
    );

    $_SESSION['inspection_errors'] = [
        'حدث خطأ داخلي أثناء معالجة المعاينة.'
    ];
}

header('Location: ../inspection.php');
exit;