<!-- رأس المستند -->

<header class="document-header">

    <div class="header-side">

        <strong>الجمارك</strong>

        نظام إدارة البيانات الجمركية

    </div>

    <div class="header-center">

        <h1>إذن الإفراج الجمركي</h1>

        <p>
            Customs Release Permit
        </p>

    </div>

    <div class="header-side">

        <strong>مستند رسمي</strong>

        نسخة للطباعة والحفظ

    </div>

</header>


<!-- بيانات الإذن الأساسية -->

<section class="permit-banner">

    <h2>إذن إفراج عن شحنة جمركية</h2>

    <p>
        تم إصدار هذا الإذن بعد تسجيل سداد الفاتورة الجمركية
        واعتماد الإفراج عن البيان.
    </p>

    <div class="permit-number">
        <?= permitEscape($permitNumber) ?>
    </div>

    <div>
        <span class="info-label">تاريخ الإصدار:</span>

        <?= permitEscape($issuedAt) ?>
    </div>

    <div>
        <span class="status-badge">
            <?= $permit['status'] === 'released'
                ? 'تم الإفراج'
                : 'حالة البيان: ' . permitEscape($permit['status']) ?>
        </span>
    </div>

</section>