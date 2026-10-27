<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Helpers/UserManagementHelper.php';

// التحقق من تسجيل الدخول.
if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// إدارة المستخدمين متاحة للمدير فقط.
$role = (string) $_SESSION['role'];

if ($role !== 'manager') {
    http_response_code(403);
    exit('هذه الصفحة متاحة للمدير فقط.');
}

// رسائل العمليات السابقة.
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);

// المستخدم المراد تعديله.
$editUser = null;

$editId = filter_input(
    INPUT_GET,
    'edit',
    FILTER_VALIDATE_INT
);

if ($editId !== null && $editId !== false && $editId > 0) {
    $stmt = $pdo->prepare(
        'SELECT user_id, full_name, username, email, role
         FROM users
         WHERE user_id = ?
         LIMIT 1'
    );

    $stmt->execute([$editId]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if (!$editUser) {
        $error = 'المستخدم المطلوب تعديله غير موجود.';
    }
}

// جلب المستخدمين.
try {
    $stmt = $pdo->query(
        'SELECT user_id, full_name, username, email, role, created_at
         FROM users
         ORDER BY user_id DESC'
    );

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    error_log(
        'User management listing error: ' .
        $exception->getMessage()
    );

    http_response_code(500);
    exit('تعذر تحميل المستخدمين. تحقق من سجل أخطاء PHP.');
}

// الأدوار المتاحة.
$roles = [
    'manager' => 'مدير النظام',
    'broker' => 'مخلص جمركي',
    'inspector' => 'مفتش جمركي',
    'accountant' => 'محاسب',
];

// بيانات المستخدم.
$fullName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المستخدم';

$roleName = 'مدير النظام';

// روابط القائمة الجانبية.
$menuItems = [
    [
        'title' => 'لوحة التحكم',
        'url' => 'dashboard.php',
        'icon' => 'fa-chart-line',
    ],
    [
        'title' => 'إنشاء معاملة',
        'url' => 'declaration_new.php',
        'icon' => 'fa-file-circle-plus',
    ],
    [
        'title' => 'المعاينة الجمركية',
        'url' => 'inspection.php',
        'icon' => 'fa-magnifying-glass',
    ],
    [
        'title' => 'مراجعة المعاينات',
        'url' => 'inspection_approvals.php',
        'icon' => 'fa-clipboard-check',
    ],
    [
        'title' => 'المالية والفواتير',
        'url' => 'finance.php',
        'icon' => 'fa-file-invoice-dollar',
    ],
    [
        'title' => 'المدفوعات',
        'url' => 'payment.php',
        'icon' => 'fa-money-bill-wave',
    ],
    [
        'title' => 'تصاريح الإفراج',
        'url' => 'release_permits.php',
        'icon' => 'fa-stamp',
    ],
    [
        'title' => 'إدارة المستخدمين',
        'url' => 'auth_users.php',
        'icon' => 'fa-users-gear',
    ],
];

// عناوين الصفحة.
$pageTitle = 'إدارة المستخدمين';
$pageHeading = 'إدارة المستخدمين';
$pageSubtitle = 'إضافة الحسابات وتعديلها وإدارة صلاحياتها';

// تجهيز محتوى الصفحة.
ob_start();

require __DIR__ . '/resources/views/users/index.php';

$pageContent = ob_get_clean();

// ملفات CSS وJavaScript الخاصة بالصفحة.
$extraCss = [
    'assets/css/pages/user-management.css',
];

$extraJs = [];

// عرض الصفحة داخل القالب المشترك.
require __DIR__ . '/resources/views/layouts/dashboard.php';