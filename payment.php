<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Models/Payment.php';

// ========================================
// 1. التحقق من تسجيل الدخول
// ========================================

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// ========================================
// 2. التحقق من الصلاحيات
// ========================================

$role = (string) $_SESSION['role'];

if (!in_array($role, ['manager', 'accountant'], true)) {
    http_response_code(403);
    exit('هذه الصفحة متاحة للمدير والمحاسب فقط.');
}

// ========================================
// 3. بيانات المستخدم
// ========================================

$fullName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المستخدم';

$roleNames = [
    'manager' => 'مدير النظام',
    'accountant' => 'المحاسب الجمركي',
];

$roleName = $roleNames[$role] ?? 'مستخدم';

// ========================================
// 4. إعداد القائمة الجانبية
// ========================================

$menuItems = [
    [
        'title' => 'الرئيسية',
        'url' => 'dashboard.php',
        'icon' => 'fa-house',
    ],
    [
        'title' => 'القسم المالي',
        'url' => 'finance.php',
        'icon' => 'fa-calculator',
    ],
    [
        'title' => 'تحصيل المدفوعات',
        'url' => 'payment.php',
        'icon' => 'fa-money-bill-transfer',
    ],
];

// ========================================
// 5. إعداد CSRF ورسائل العمليات
// ========================================

if (
    empty($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token'])
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = $_SESSION['payment_errors'] ?? [];
$success = $_SESSION['payment_success'] ?? '';

unset(
    $_SESSION['payment_errors'],
    $_SESSION['payment_success']
);

if (!is_array($errors)) {
    $errors = [];
}

// ========================================
// 6. تحميل الفواتير المعلقة
// ========================================

$search = trim((string) ($_GET['search'] ?? ''));

if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}

$invoices = [];

try {
    $paymentModel = new Payment($pdo);
    $invoices = $paymentModel->getPendingInvoices($search);
} catch (Throwable $e) {
    error_log(
        'Unable to load pending payments: ' . $e->getMessage()
    );

    $errors[] = 'تعذر تحميل الفواتير. تحقق من اتصال قاعدة البيانات.';
}

// ========================================
// 7. إعداد عناوين الصفحة
// ========================================

$pageTitle = 'تحصيل الرسوم الجمركية';

$pageHeading = 'تحصيل الرسوم الجمركية';

$pageSubtitle = 'استلام المبالغ المستحقة وتأكيد سداد الفواتير الجمركية';

// ========================================
// 8. تجهيز محتوى الصفحة
// ========================================

ob_start();

require __DIR__ . '/resources/views/payments/index.php';

$pageContent = ob_get_clean();

// ========================================
// 9. ملفات CSS وJavaScript الخاصة بالصفحة
// ========================================

$extraCss = [];

$extraJs = [
    'assets/js/payment.js',
];

// ========================================
// 10. عرض الصفحة داخل القالب المشترك
// ========================================

require __DIR__ . '/resources/views/layouts/dashboard.php';