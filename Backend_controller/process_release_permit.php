<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/ReleasePermitController.php';

function redirectToReleasePage(): void
{
    header('Location: ../release_permits.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| التحقق من تسجيل الدخول والصلاحية
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'manager'
) {
    header('Location: ../login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| السماح بطلب POST فقط
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

/*
|--------------------------------------------------------------------------
| التحقق من CSRF
|--------------------------------------------------------------------------
*/

$sessionToken = $_SESSION['csrf_token'] ?? '';
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($sessionToken) ||
    !is_string($submittedToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    $_SESSION['release_errors'] = [
        'انتهت صلاحية الطلب أو أن رمز الحماية غير صالح. حدّث الصفحة وحاول مجددًا.'
    ];

    redirectToReleasePage();
}

/*
|--------------------------------------------------------------------------
| تنفيذ إصدار إذن الإفراج
|--------------------------------------------------------------------------
*/

try {
    $controller = new ReleasePermitController($pdo);

    $result = $controller->issue(
        $_POST,
        (int) $_SESSION['user_id']
    );

    if (!$result['success']) {
        $_SESSION['release_errors'] = $result['errors'];

        redirectToReleasePage();
    }

    $_SESSION['release_success'] =
        'تم إصدار إذن الإفراج بنجاح: ' .
        $result['permit_id'];

    header(
        'Location: ../print_release_permit.php?permit_id=' .
        rawurlencode($result['permit_id'])
    );

    exit;

} catch (Throwable $e) {
    error_log(
        'Process release permit error: ' . $e->getMessage()
    );

    $_SESSION['release_errors'] = [
        'حدث خطأ أثناء إصدار إذن الإفراج. راجع سجل الأخطاء لمعرفة التفاصيل.'
    ];

    redirectToReleasePage();
}