<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Controllers/InspectionApprovalController.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'manager') {
    http_response_code(403);
    exit('هذه الصفحة متاحة للمدير فقط.');
}

$role = (string) $_SESSION['role'];
$fullName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'المدير';
$roleName = 'مدير النظام';

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

$menuItems = [
    [
        'title' => 'الرئيسية',
        'url' => 'dashboard.php',
        'icon' => 'fa-house'
    ],
    [
        'title' => 'إنشاء بيان جمركي',
        'url' => 'declaration_new.php',
        'icon' => 'fa-file-circle-plus'
    ],
    [
        'title' => 'معاينة البيانات الجمركية',
        'url' => 'inspection.php',
        'icon' => 'fa-clipboard-check'
    ],
    [
        'title' => 'موافقات المدير',
        'url' => 'inspection_approvals.php',
        'icon' => 'fa-user-check'
    ]
];

try {
    $controller = new InspectionApprovalController($pdo);
    $data = $controller->index();
    $approvals = $data['approvals'] ?? [];
} catch (Throwable $e) {
    error_log('Inspection approvals page error: ' . $e->getMessage());

    http_response_code(500);
    exit('تعذر تحميل قائمة الموافقات. راجع سجل الأخطاء.');
}

$pageTitle = 'موافقات المدير';
$pageHeading = 'موافقات المدير';
$pageSubtitle = 'مراجعة البيانات المحالة من المفتش واتخاذ القرار المناسب';

ob_start();
require __DIR__ . '/resources/views/inspections/pending_approvals.php';
$pageContent = ob_get_clean();

$extraCss = ['assets/css/inspections.css'];
$extraJs = [];

require __DIR__ . '/resources/views/layouts/dashboard.php';