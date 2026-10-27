<?php
declare(strict_types=1);

if (!function_exists('statCard')) {
    function statCard(
        string $title,
        string $value,
        string $icon,
        string $description
    ): string {
        ob_start();
        ?>
        <article class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid <?= e($icon) ?>"></i>
            </div>
            <div class="stat-content">
                <div class="stat-title"><?= e($title) ?></div>
                <div class="stat-value"><?= e($value) ?></div>
                <?php if ($description !== ''): ?>
                    <div class="stat-description"><?= e($description) ?></div>
                <?php endif; ?>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel(string $status): string
    {
        $statusNames = [
            'new' => 'جديدة',
            'inspected' => 'تمت المعاينة',
            'invoiced' => 'تم إصدار الفاتورة',
            'paid' => 'تم السداد',
            'released' => 'تم الإفراج',
            'inspection_review' => 'مراجعة المعاينة',
            'pending_manager_approval' => 'بانتظار موافقة المدير',
        ];
        return $statusNames[$status] ?? $status;
    }
}

if (!function_exists('getStatusClass')) {
    function getStatusClass(string $status): string
    {
        $statusClasses = [
            'new' => 'status-new',
            'inspected' => 'status-inspected',
            'invoiced' => 'status-invoiced',
            'paid' => 'status-paid',
            'released' => 'status-released',
            'inspection_review' => 'status-review',
            'pending_manager_approval' => 'status-pending',
        ];
        return $statusClasses[$status] ?? 'status-new';
    }
}

if (!function_exists('getRoleName')) {
    function getRoleName(string $role): string
    {
        $roleNames = [
            'manager' => 'مدير النظام',
            'broker' => 'المخلّص الجمركي',
            'inspector' => 'المعاين الجمركي',
            'accountant' => 'المحاسب',
        ];
        return $roleNames[$role] ?? 'مستخدم';
    }
}

if (!function_exists('buildDashboardMenu')) {
    function buildDashboardMenu(string $role): array
    {
        $menuItems = [];

        switch ($role) {
            case 'manager':
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
                break;

            case 'broker':
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
                ];
                break;

            case 'inspector':
                $menuItems = [
                    [
                        'title' => 'لوحة التحكم',
                        'url' => 'dashboard.php',
                        'icon' => 'fa-chart-line',
                    ],
                    [
                        'title' => 'المعاينة الجمركية',
                        'url' => 'inspection.php',
                        'icon' => 'fa-magnifying-glass',
                    ],
                ];
                break;

            case 'accountant':
                $menuItems = [
                    [
                        'title' => 'لوحة التحكم',
                        'url' => 'dashboard.php',
                        'icon' => 'fa-chart-line',
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
                ];
                break;
        }

        return $menuItems;
    }
}