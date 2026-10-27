<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../app/Helpers/dashboard_view_helpers.php';

$pageTitle    = 'لوحة التحكم | نظام الجمارك';
$pageHeading  = 'لوحة التحكم';
$pageSubtitle = 'مرحبًا بك في نظام الإدارة الجمركية';

$roleName = getRoleName($role);

ob_start();
?>

<?php require __DIR__ . '/partials/welcome.php'; ?>

<?php if ($role === 'manager'): ?>

    <?php require __DIR__ . '/partials/stats/manager.php'; ?>
    <?php require __DIR__ . '/partials/recent_declarations.php'; ?>

<?php elseif ($role === 'inspector'): ?>

    <?php require __DIR__ . '/partials/stats/inspector.php'; ?>
    <?php require __DIR__ . '/partials/quick_actions/inspector.php'; ?>

<?php elseif ($role === 'accountant'): ?>

    <?php require __DIR__ . '/partials/stats/accountant.php'; ?>
    <?php require __DIR__ . '/partials/quick_actions/accountant.php'; ?>

<?php elseif ($role === 'broker'): ?>

    <?php require __DIR__ . '/partials/quick_actions/broker.php'; ?>

<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
$pageContent = ob_get_clean();

require __DIR__ . '/../layouts/dashboard.php';