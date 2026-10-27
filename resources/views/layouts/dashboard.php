<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| القالب المشترك للنظام الداخلي
|--------------------------------------------------------------------------
| يستقبل:
| $fullName
| $roleName
| $menuItems
| $pageTitle
| $pageHeading
| $pageSubtitle
| $pageContent
| $extraCss
| $extraJs
|--------------------------------------------------------------------------
*/

// ========================================
// 1. دالة حماية النصوص
// ========================================

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

// ========================================
// 2. القيم الافتراضية
// ========================================

$currentPage = basename(
    $_SERVER['PHP_SELF'] ?? 'dashboard.php'
);

$pageTitle = $pageTitle ?? 'نظام الإدارة الجمركية CMS';

$pageHeading = $pageHeading ?? 'لوحة التحكم';

$pageSubtitle = $pageSubtitle
    ?? 'مرحبًا بك في نظام الإدارة الجمركية';

$fullName = $fullName
    ?? $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'المستخدم';

$roleName = $roleName ?? 'مستخدم';

$menuItems = $menuItems ?? [];

$pageContent = $pageContent ?? '';

$extraCss = $extraCss ?? [];

$extraJs = $extraJs ?? [];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($pageTitle) ?></title>

    <!-- Bootstrap RTL -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <!-- التنسيق العام للوحة التحكم -->
    <link
        rel="stylesheet"
        href="assets/css/dashboard.css"
    >

    <!-- تنسيق صفحة إنشاء البيان -->
    <link
        rel="stylesheet"
        href="assets/css/declaration.css"
    >

    <!-- ملفات CSS الإضافية حسب الصفحة -->
    <?php foreach ($extraCss as $cssFile): ?>

        <link
            rel="stylesheet"
            href="<?= e($cssFile) ?>"
        >

    <?php endforeach; ?>

</head>

<body>

<div class="dashboard-layout">

    <!-- ================================== -->
    <!-- القائمة الجانبية -->
    <!-- ================================== -->

    <aside class="sidebar" id="sidebar">

        <!-- شعار النظام -->
        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div>
                <h1>CMS</h1>
                <p>نظام الإدارة الجمركية</p>
            </div>

        </div>

        <!-- بيانات المستخدم -->
        <div class="sidebar-user">

            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="user-info">

                <strong><?= e($fullName) ?></strong>

                <span><?= e($roleName) ?></span>

            </div>

        </div>

        <!-- روابط القائمة -->
        <nav
            class="sidebar-menu"
            aria-label="القائمة الرئيسية"
        >

            <div class="menu-heading">
                القائمة الرئيسية
            </div>

            <?php foreach ($menuItems as $item): ?>

                <?php
                $itemUrl = (string) ($item['url'] ?? '');

                $itemTitle = (string) ($item['title'] ?? '');

                $itemIcon = (string) (
                    $item['icon'] ?? 'fa-circle'
                );

                $isActive = (
                    basename($itemUrl) === $currentPage
                );
                ?>

                <a
                    class="menu-item <?= $isActive ? 'active' : '' ?>"
                    href="<?= e($itemUrl) ?>"
                    <?= $isActive
                        ? 'aria-current="page"'
                        : '' ?>
                >

                    <i class="fa-solid <?= e($itemIcon) ?>"></i>

                    <span><?= e($itemTitle) ?></span>

                </a>

            <?php endforeach; ?>

        </nav>

        <!-- تسجيل الخروج -->
        <div class="sidebar-bottom">

            <a
                class="menu-item logout-link"
                href="logout.php"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>تسجيل الخروج</span>

            </a>

        </div>

    </aside>

    <!-- ================================== -->
    <!-- المحتوى الرئيسي -->
    <!-- ================================== -->

    <main class="main-content">

        <!-- الشريط العلوي -->
        <header class="topbar">

            <button
                type="button"
                class="mobile-menu-button"
                id="mobileMenuButton"
                aria-label="فتح القائمة"
                aria-controls="sidebar"
                aria-expanded="false"
            >

                <i class="fa-solid fa-bars"></i>

            </button>

            <div class="topbar-title">

                <h2><?= e($pageHeading) ?></h2>

                <p><?= e($pageSubtitle) ?></p>

            </div>

            <div class="topbar-user">

                <span><?= e($roleName) ?></span>

                <i class="fa-solid fa-circle-user"></i>

            </div>

        </header>

        <!-- محتوى الصفحة -->
        <section class="page-content">

            <?= $pageContent ?>

        </section>

    </main>

</div>

<!-- Bootstrap -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

<!-- وظائف القائمة الجانبية -->
<script src="assets/js/dashboard.js"></script>

<!-- ملفات JavaScript الإضافية حسب الصفحة -->
<?php foreach ($extraJs as $jsFile): ?>

    <script
        src="<?= e($jsFile) ?>"
        defer
    ></script>

<?php endforeach; ?>

</body>
</html>