<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/PaymentController.php';

/**
 * تخزين رسائل الخطأ وإعادة المستخدم إلى صفحة الدفع.
 */
function redirectPaymentWithErrors(array $errors): never
{
    $_SESSION['payment_errors'] = $errors;

    header('Location: ../payment.php');
    exit;
}

/*
 * التحقق من تسجيل الدخول وصلاحية المحاسب.
 */
if (
    !isset($_SESSION['user_id'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'accountant'
) {
    $_SESSION['payment_errors'] = [
        'يجب تسجيل الدخول بحساب محاسب مخول لإجراء الدفع.'
    ];

    header('Location: ../login.php');
    exit;
}

/*
 * السماح بطلبات POST فقط.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../payment.php');
    exit;
}

/*
 * التحقق من CSRF.
 */
$sessionToken = $_SESSION['csrf_token'] ?? '';
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !is_string($submittedToken) ||
    $submittedToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    redirectPaymentWithErrors([
        'انتهت صلاحية الطلب أو تعذر التحقق منه. حدّث الصفحة وحاول مجددًا.'
    ]);
}

/*
 * تنفيذ عملية الدفع.
 */
$accountantId = (int) $_SESSION['user_id'];

try {
    $controller = new PaymentController($pdo);

    $result = $controller->process(
        $_POST,
        $accountantId
    );

    if (!$result['success']) {
        redirectPaymentWithErrors(
            $result['errors'] ?? [
                'تعذر إكمال عملية الدفع.'
            ]
        );
    }

    $_SESSION['payment_success'] =
        'تم تحصيل المبلغ بنجاح. '
        . 'رقم عملية الدفع: #'
        . (int) $result['payment_id']
        . ' - المبلغ: $'
        . number_format((float) $result['amount'], 2);

    header('Location: ../payment.php');
    exit;
} catch (Throwable $e) {
    error_log(
        'Payment endpoint error: ' . $e->getMessage()
    );

    redirectPaymentWithErrors([
        'حدث خطأ غير متوقع أثناء الدفع. راجع سجل الأخطاء.'
    ]);
}