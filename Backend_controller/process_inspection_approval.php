<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/InspectionApprovalController.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['role'] !== 'manager') {
    http_response_code(403);
    exit('هذه العملية متاحة للمدير فقط.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../inspection_approvals.php');
    exit;
}

$sessionToken = $_SESSION['csrf_token'] ?? '';
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($sessionToken) ||
    !is_string($submittedToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    $_SESSION['approval_errors'] = [
        'انتهت صلاحية الطلب أو رمز الحماية غير صحيح. حدّث الصفحة وحاول مرة أخرى.'
    ];

    header('Location: ../inspection_approvals.php');
    exit;
}

try {
    $controller = new InspectionApprovalController($pdo);

    $result = $controller->process(
        $_POST,
        (int) $_SESSION['user_id']
    );

    if (!empty($result['success'])) {
        $_SESSION['approval_success'] =
            $result['message'] ?? 'تم تنفيذ القرار بنجاح.';

        unset($_SESSION['approval_errors']);
    } else {
        $_SESSION['approval_errors'] = $result['errors']
            ?? [$result['message'] ?? 'تعذر تنفيذ القرار.'];

        unset($_SESSION['approval_success']);
    }
} catch (Throwable $e) {
    error_log('Process inspection approval error: ' . $e->getMessage());

    $_SESSION['approval_errors'] = [
        'حدث خطأ أثناء تنفيذ القرار. تحقق من البيانات وسجل الأخطاء.'
    ];

    unset($_SESSION['approval_success']);
}

header('Location: ../inspection_approvals.php');
exit;