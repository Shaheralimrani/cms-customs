<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Helpers/dashboard_view_helpers.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = (string) $_SESSION['role'];
$allowedRoles = ['manager', 'broker', 'inspector', 'accountant'];

if (!in_array($role, $allowedRoles, true)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

try {
    $controller = new DashboardController($pdo);
    $dashboardData = $controller->index($role);
    $stats = $dashboardData['stats'];
    $recentDeclarations = $dashboardData['recent_declarations'];
} catch (Throwable $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    http_response_code(500);
    exit('حدث خطأ أثناء تحميل لوحة التحكم.');
}

$fullName = $_SESSION['full_name'] ?? 'المستخدم';
$roleName = getRoleName($role);
$menuItems = buildDashboardMenu($role);

require __DIR__ . '/resources/views/dashboard/index.php';