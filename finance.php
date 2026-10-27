<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Models/Finance.php';

// ========================================
// 1. التحقق من تسجيل الدخول والصلاحيات
// ========================================

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = (string) $_SESSION['role'];

if (!in_array($role, ['manager', 'accountant'], true)) {
    http_response_code(403);
    exit('هذه الصفحة متاحة للمدير والمحاسب فقط.');
}

// ========================================
// 2. بيانات المستخدم
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
// 3. تجهيز القائمة الجانبية
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
// 4. تجهيز CSRF Token
// ========================================

if (
    empty($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token'])
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

// ========================================
// 5. رسائل النجاح والأخطاء
// ========================================

$errors = $_SESSION['finance_errors'] ?? [];
$success = $_SESSION['finance_success'] ?? '';

unset(
    $_SESSION['finance_errors'],
    $_SESSION['finance_success']
);

if (!is_array($errors)) {
    $errors = [];
}

// ========================================
// 6. تحميل البيانات المالية
// ========================================

$search = trim((string) ($_GET['search'] ?? ''));

$declarations = [];
$tariffs = [];

try {
    $financeModel = new Finance($pdo);

    $declarations = $financeModel->getDeclarationsForFinance($search);
    $tariffs = $financeModel->getTariffs();

} catch (Throwable $e) {
    error_log('finance.php: ' . $e->getMessage());

    $errors[] = 'تعذر تحميل بيانات القسم المالي. حاول مرة أخرى.';
}

// ========================================
// 7. عناوين الصفحة
// ========================================

$pageTitle = 'القسم المالي واحتساب الرسوم';

$pageHeading = 'القسم المالي واحتساب الرسوم';

$pageSubtitle = 'احتساب التعريفات الجمركية والضرائب وإصدار الفواتير';

// ========================================
// 8. تجهيز محتوى الصفحة
// ========================================

ob_start();

require __DIR__ . '/resources/views/finance/index.php';

$pageContent = ob_get_clean();

// ========================================
// 9. ملفات CSS وJavaScript الخاصة بالصفحة
// ========================================

$extraCss = [];

$extraJs = [
    'assets/js/finance.js',
];

// ========================================
// 10. عرض الصفحة داخل القالب المشترك
// ========================================

require __DIR__ . '/resources/views/layouts/dashboard.php';