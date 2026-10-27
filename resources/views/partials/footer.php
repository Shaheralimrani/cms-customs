<?php
/*
 * File: includes/footer.php
 * التذييل الموحد للصفحات العامة
 */
?>

<footer class="cms-footer">
    <div class="container-fluid px-3 px-lg-4">

        <div class="row align-items-center g-4">

            <div class="col-12 col-lg-4 text-center text-lg-start">

                <div class="footer-brand">
                    <i class="fa-solid fa-ship ms-1"
                       style="color: #d4a64a;"></i>

                    نظام إدارة الجمارك CMS
                </div>

                <p class="footer-description mb-0">
                    الجمهورية اليمنية<br>
                    نظام إلكتروني لإدارة المعاملات الجمركية.
                </p>

            </div>

            <div class="col-12 col-lg-4">

                <nav class="footer-links"
                     aria-label="روابط التذييل">

                    <a href="index.php">الرئيسية</a>
                    <a href="track.php">تتبع الشحنات</a>
                    <a href="tariffs.php">دليل التعريفة</a>
                    <a href="login.php">تسجيل الدخول</a>

                </nav>

            </div>

            <div class="col-12 col-lg-4 text-center text-lg-end">

                <div class="footer-description">
                    <i class="fa-solid fa-shield-halved ms-1"></i>
                    نظام الإدارة الجمركية
                </div>

                <div class="footer-description">
                    واجهة إلكترونية لخدمة المستخدمين
                </div>

            </div>

        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y') ?>
            نظام إدارة الجمارك CMS.
            جميع الحقوق محفوظة.
        </div>

    </div>
</footer>