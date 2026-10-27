<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Controllers/InspectionController.php';

// ========================================
// 1. التحقق من تسجيل الدخول
// ========================================

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// تعريف الدور قبل استخدامه.
$role = (string) $_SESSION['role'];

// ========================================
// 2. التحقق من الصلاحيات
// ========================================

// السماح للمفتش والمدير بالوصول.
if (!hasAccess('inspector')) {
    http_response_code(403);
    exit('ليس لديك صلاحية الوصول إلى صفحة المعاينة.');
}

// ========================================
// 3. بيانات المستخدم
// ========================================

$fullName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المستخدم';

$roleNames = [
    'manager'   => 'مدير النظام',
    'broker'    => 'المخلص الجمركي',
    'inspector' => 'المفتش الجمركي',
];

$roleName = $roleNames[$role] ?? 'مستخدم';

// ========================================
// 4. إعداد القائمة الجانبية
// ========================================

$menuItems = [
    [
        'title' => 'الرئيسية',
        'url'   => 'dashboard.php',
        'icon'  => 'fa-house',
    ],
    [
        'title' => 'معاينة البيانات الجمركية',
        'url'   => 'inspection.php',
        'icon'  => 'fa-clipboard-check',
    ],
];

// رابط إنشاء البيان للمدير.
if ($role === 'manager') {
    $menuItems[] = [
        'title' => 'إنشاء بيان جمركي',
        'url'   => 'declaration_new.php',
        'icon'  => 'fa-file-circle-plus',
    ];
}

// ========================================
// 5. تحميل بيانات المعاينة
// ========================================

$search = trim((string) ($_GET['search'] ?? ''));

try {
    $controller = new InspectionController($pdo);

    $data = $controller->index($search);

    $declarations = $data['declarations'] ?? [];
    $search = $data['search'] ?? '';

} catch (Throwable $e) {
    error_log(
        'Inspection page error: ' . $e->getMessage()
    );

    http_response_code(500);
    exit('حدث خطأ أثناء تحميل صفحة المعاينة.');
}

// ========================================
// 6. إعداد عناوين الصفحة
// ========================================

$pageTitle = 'معاينة البيانات الجمركية';

$pageHeading = 'معاينة البيانات الجمركية';

$pageSubtitle = 'مراجعة الشحنات وتسجيل نتائج التفتيش';

// ========================================
// 7. تجهيز محتوى صفحة المعاينة
// ========================================

ob_start();

require __DIR__ . '/resources/views/inspections/index.php';

$pageContent = ob_get_clean();

// ========================================
// 8. ملفات CSS وJavaScript الخاصة بالمعاينة
// ========================================

$extraCss = [
    'assets/css/inspections.css',
];

$extraJs = [
    'assets/js/inspections.js',
];

// ========================================
// 9. عرض الصفحة داخل القالب المشترك
// ========================================

require __DIR__ . '/resources/views/layouts/dashboard.php';