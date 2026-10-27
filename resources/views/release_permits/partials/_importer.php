<!-- بيانات المستورد أو المخلص -->

<section class="section">

    <h3 class="section-title">
        ثانيًا: بيانات المستورد أو المخلص الجمركي
    </h3>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">اسم الشركة</span>

            <span class="info-value">
                <?= permitEscape($companyName) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">رقم الترخيص</span>

            <span class="info-value ltr">
                <?= permitEscape($companyLicense) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">رقم الهاتف</span>

            <span class="info-value ltr">
                <?= permitEscape($companyPhone) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">البريد الإلكتروني</span>

            <span class="info-value ltr">
                <?= permitEscape($companyEmail) ?>
            </span>
        </div>

    </div>

</section>