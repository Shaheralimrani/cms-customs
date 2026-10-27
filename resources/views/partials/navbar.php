<?php
/*
 * File: includes/navbar.php
 * الشريط العلوي للصفحات العامة
 */

$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

$publicNavItems = [
    [
        'title' => 'الرئيسية',
        'url' => 'index.php',
        'icon' => 'fa-house',
    ],
    [
        'title' => 'تتبع الشحنات',
        'url' => 'track.php',
        'icon' => 'fa-magnifying-glass-location',
    ],
    [
        'title' => 'دليل التعريفة',
        'url' => 'tariffs.php',
        'icon' => 'fa-book-bookmark',
    ],
];
?>

<nav class="navbar navbar-expand-lg cms-navbar">
    <div class="container-fluid px-3 px-lg-4">

        <a class="navbar-brand d-flex align-items-center"
           href="index.php">

            <span class="brand-symbol">
                <i class="fa-solid fa-ship"></i>
            </span>

            <span>
                مصلحة الجمارك
                <small class="d-block fw-normal"
                       style="font-size: 10px; color: #d5e2f1;">
                    البوابة الرقمية
                </small>
            </span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#cmsNavbar"
            aria-controls="cmsNavbar"
            aria-expanded="false"
            aria-label="فتح قائمة التنقل">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="cmsNavbar">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-3 mt-lg-0 gap-1">

                <?php foreach ($publicNavItems as $item): ?>
                    <li class="nav-item">

                        <a
                            class="nav-link <?= $currentPage === $item['url'] ? 'active' : '' ?>"
                            href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= $currentPage === $item['url'] ? 'aria-current="page"' : '' ?>>

                            <i class="fa-solid <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?> ms-1"></i>

                            <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>

                        </a>

                    </li>
                <?php endforeach; ?>

                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fa-solid fa-gauge-high ms-1"></i>
                            لوحة التحكم
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fa-solid fa-right-to-bracket ms-1"></i>
                            تسجيل الدخول
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>