<?php
/**
 * File: resources/views/layouts/app.php
 * القالب العام للصفحات العامة في نظام إدارة الجمارك CMS.
 *
 * المتغيرات الاختيارية:
 * $pageTitle   عنوان الصفحة.
 * $pageContent محتوى الصفحة بعد تجهيزه.
 * $extraCss    ملفات CSS إضافية.
 * $extraJs     ملفات JavaScript إضافية.
 */

$pageTitle = $pageTitle ?? 'نظام إدارة الجمارك CMS';
$pageContent = $pageContent ?? '';

$extraCss = $extraCss ?? [];
$extraJs = $extraJs ?? [];

// تحديد المسار الأساسي للمشروع.
// المشروع يعمل داخل مجلد CMS على خادم Apache.
$baseUrl = '/CMS/';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?><!DOCTYPE html><html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<meta
    name="description"
    content="نظام إدارة الجمارك CMS - الجمهورية اليمنية"
>

<title>
    <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
</title>

<!-- Bootstrap RTL -->
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
>

<!-- Font Awesome -->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>

<!-- التنسيقات العامة -->
<link
    rel="stylesheet"
    href="<?= $baseUrl ?>assets/css/style.css"
>

<!-- تنسيقات الصفحات العامة -->
<link
    rel="stylesheet"
    href="<?= $baseUrl ?>assets/css/public.css"
>

<!-- ملفات CSS الخاصة بكل صفحة -->
<?php foreach ($extraCss as $cssFile): ?>
    <link
        rel="stylesheet"
        href="<?= htmlspecialchars($baseUrl . ltrim($cssFile, '/'), ENT_QUOTES, 'UTF-8') ?>"
    >
<?php endforeach; ?>

</head><body class="cms-public-page"><?php require __DIR__ . '/../partials/navbar.php'; ?>

<main class="cms-main-content">
    <?= $pageContent ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript الخاص بكل صفحة -->
<?php foreach ($extraJs as $jsFile): ?>
    <script
        src="<?= htmlspecialchars($baseUrl . ltrim($jsFile, '/'), ENT_QUOTES, 'UTF-8') ?>"
    ></script>
<?php endforeach; ?>

</body>
</html>