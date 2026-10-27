<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Controllers/ReleasePermitController.php';

// ========================================
// 1. التحقق من تسجيل الدخول
// ========================================

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// ========================================
// 2. التحقق من الصلاحية
// ========================================

$role = (string) $_SESSION['role'];

if ($role !== 'manager') {
    http_response_code(403);
    exit('هذه الصفحة متاحة للمدير فقط.');
}

// ========================================
// 3. تجهيز CSRF Token
// ========================================

if (
    empty($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token'])
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

// ========================================
// 4. بيانات المستخدم
// ========================================

$fullName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المدير';

$roleName = 'مدير النظام';

// ========================================
// 5. إعداد القائمة الجانبية
// ========================================

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
    [
        'title' => 'معاينة البيانات الجمركية',
        'url' => 'inspection.php',
        'icon' => 'fa-clipboard-check',
    ],
    [
        'title' => 'موافقات المدير',
        'url' => 'inspection_approvals.php',
        'icon' => 'fa-user-check',
    ],
    [
        'title' => 'أذونات الإفراج الجمركي',
        'url' => 'release_permits.php',
        'icon' => 'fa-file-circle-check',
    ],
];

// ========================================
// 6. رسائل النجاح والأخطاء
// ========================================

$errors = $_SESSION['release_errors'] ?? [];
$success = $_SESSION['release_success'] ?? '';

unset(
    $_SESSION['release_errors'],
    $_SESSION['release_success']
);

if (!is_array($errors)) {
    $errors = [];
}

// ========================================
// 7. تحميل بيانات أذونات الإفراج
// ========================================

$permits = [];
$readyForRelease = [];

try {
    $controller = new ReleasePermitController($pdo);

    $data = $controller->index();

    $permits = $data['permits'] ?? [];
    $readyForRelease = $data['ready_for_release'] ?? [];

} catch (Throwable $e) {
    error_log(
        'Release permits page error: ' . $e->getMessage()
    );

    $errors[] = 'تعذر تحميل بيانات أذونات الإفراج الجمركي. حاول مرة أخرى.';
}

// ========================================
// 8. إعداد عناوين الصفحة
// ========================================

$pageTitle = 'إدارة أذونات الإفراج الجمركي';

$pageHeading = 'إدارة أذونات الإفراج الجمركي';

$pageSubtitle = 'إصدار أذونات الإفراج للبيانات التي اكتملت إجراءات دفع فواتيرها';

// ========================================
// 9. تجهيز محتوى الصفحة
// ========================================

if (!function_exists('releaseEscape')) {
    function releaseEscape($value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

ob_start();

require __DIR__ . '/resources/views/release_permits/index.php';

$pageContent = ob_get_clean();

// ========================================
// 10. ملفات JavaScript الخاصة بالصفحة
// ========================================

$extraCss = [];

$extraJs = [
    'assets/js/release_permits.js',
];

// ========================================
// 11. عرض الصفحة داخل القالب المشترك
// ========================================

require __DIR__ . '/resources/views/layouts/dashboard.php';