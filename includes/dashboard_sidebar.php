<?php
// includes/dashboard_sidebar.php
// هذا الشريط مخصص للوحة التحكم فقط ويستخدم دالة hasAccess للتحكم بالصلاحيات
?>

<div class="glass-card rounded-4 p-3 h-100">
    <h5 class="text-white fw-bold mb-4 text-center border-bottom border-secondary pb-2">
        <i class="fa-solid fa-layer-group me-2"></i> القائمة الرئيسية
    </h5>
    
    <ul class="nav flex-column gap-2">
        
        <!-- 1. رابط تقديم البيان (يظهر للمخلص والمدير) -->
        <?php if (hasAccess('broker')): ?>
            <li class="nav-item">
                <a href="declaration_new.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-file-circle-plus text-info me-2"></i> تقديم بيان جمركي
                </a>
            </li>
        <?php endif; ?>

        <!-- 2. رابط المعاينة (يظهر للمفتش والمدير) -->
        <?php if (hasAccess('inspector')): ?>
            <li class="nav-item">
                <a href="inspection.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-magnifying-glass-chart text-warning me-2"></i> المعاينة والفرز
                </a>
            </li>
        <?php endif; ?>

        <!-- 3. روابط المالية (تظهر للمحاسب والمدير) -->
        <?php if (hasAccess('accountant')): ?>
            <li class="nav-item">
                <a href="finance.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-calculator text-success me-2"></i> القسم المالي والرسوم
                </a>
            </li>
            <li class="nav-item">
                <a href="payment.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-money-bill-transfer text-success me-2"></i> تحصيل المدفوعات
                </a>
            </li>
        <?php endif; ?>
          <!-- ✅ 4. رابط أذونات الإفراج (يظهر للمدير فقط) -->
        <?php if (hasAccess('manager')): ?>
            <li class="nav-item">
                <a href="release_permits.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-file-signature text-success me-2"></i> أذونات الإفراج
                </a>
            </li>
            <?php endif; ?>

        <!-- 4. رابط إدارة المستخدمين (يظهر للمدير فقط) -->
        <?php if (hasAccess('manager')): ?>
            <li class="nav-item mt-3 pt-3 border-top border-secondary">
                <a href="manage_users.php" class="nav-link text-light rounded-3 p-2 hover-bg">
                    <i class="fa-solid fa-users-gear text-danger me-2"></i> إدارة المستخدمين
                </a>
            </li>
        <?php endif; ?>

        <!-- 5. زر تسجيل الخروج (يظهر للجميع) -->
        <li class="nav-item mt-4 pt-3 border-top border-secondary">
            <a href="login.php" class="nav-link text-light rounded-3 p-2 hover-bg text-danger">
                <i class="fa-solid fa-right-from-bracket me-2"></i> تسجيل الخروج
            </a>
        </li>
        
    </ul>
</div>