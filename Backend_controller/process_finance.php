<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/FinanceController.php';

// ========================================
// 1. التحقق من تسجيل الدخول والصلاحية
// ========================================

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role'])
) {
    $_SESSION['finance_errors'] = [
        'يجب تسجيل الدخول أولًا.'
    ];

    header('Location: ../login.php');
    exit;
}

if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['accountant', 'manager'], true)
) {
    http_response_code(403);
    exit('هذه العملية متاحة للمدير والمحاسب فقط.');
}

$accountantId = (int) $_SESSION['user_id'];

// ========================================
// 2. السماح بطلبات POST فقط
// ========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../finance.php');
    exit;
}

// ========================================
// 3. استدعاء Controller
// ========================================

try {
    $controller = new FinanceController($pdo);

    $result = $controller->issueInvoice(
        $_POST,
        $accountantId
    );

    if (!empty($result['success'])) {
        $_SESSION['finance_success'] =
            $result['message'] .
            ' رقم الفاتورة: ' .
            $result['invoice_id'] .
            '. إجمالي المبلغ: ' .
            number_format($result['total_amount'], 2) .
            ' $';

        unset($_SESSION['finance_errors']);

    } else {
        $_SESSION['finance_errors'] =
            $result['errors'] ??
            ['تعذر إصدار الفاتورة.'];

        unset($_SESSION['finance_success']);
    }

} catch (Throwable $e) {
    error_log(
        'process_finance.php: ' . $e->getMessage()
    );

    $_SESSION['finance_errors'] = [
        'حدث خطأ غير متوقع أثناء إصدار الفاتورة.'
    ];

    unset($_SESSION['finance_success']);
}

// ========================================
// 4. العودة إلى صفحة القسم المالي
// ========================================

header('Location: ../finance.php');
exit;
?>