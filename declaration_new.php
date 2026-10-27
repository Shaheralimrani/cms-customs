<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';

// التحقق من تسجيل الدخول.
if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// الأدوار المسموح لها بإنشاء البيان.
$allowedRoles = ['manager', 'broker'];

$role = (string) $_SESSION['role'];

if (!in_array($role, $allowedRoles, true)) {
    http_response_code(403);
    exit('ليس لديك صلاحية الوصول إلى هذه الصفحة.');
}

// رسائل النجاح والأخطاء القادمة من صفحة المعالجة.
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['declaration_success'] ?? null;

unset($_SESSION['errors'], $_SESSION['declaration_success']);

if (!is_array($errors)) {
    $errors = [];
}

// اسم المستخدم والدور.
$fullName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المستخدم';

$roleNames = [
    'manager' => 'مدير النظام',
    'broker' => 'المخلص الجمركي',
];

$roleName = $roleNames[$role] ?? 'مستخدم';

// روابط القائمة الجانبية.
$menuItems = [
    [
        'title' => 'الرئيسية',
        'url' => 'dashboard.php',
        'icon' => 'fa-house',
    ],
    [
        'title' => 'إنشاء بيان جمركي',
        'url' => 'declaration_new.php',
        'icon' => 'fa-file-circle-plus',
    ],
];

// عنوان الصفحة.
$pageTitle = 'إنشاء بيان جمركي';
$pageHeading = 'إنشاء بيان جمركي جديد';
$pageSubtitle = 'إدخال بيانات الشركة والشحنة والأصناف والمستندات';

// دالة حماية النصوص عند عرضها داخل HTML.
if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

// تجهيز محتوى الصفحة.
ob_start();

require __DIR__ . '/resources/views/declarations/create.php';

$pageContent = ob_get_clean();

// استدعاء التخطيط الداخلي المشترك.
require __DIR__ . '/resources/views/layouts/dashboard.php';