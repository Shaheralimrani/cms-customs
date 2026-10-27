<!-- بيانات البيان -->

<section class="section">

    <h3 class="section-title">
        أولًا: بيانات البيان الجمركي
    </h3>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">رقم البيان</span>

            <span class="info-value ltr">
                <?= permitEscape($declarationId) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">نوع الشحنة</span>

            <span class="info-value">
                <?= permitEscape($shipmentType) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">بلد المنشأ</span>

            <span class="info-value">
                <?= permitEscape($originCountry) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">رقم الحاوية</span>

            <span class="info-value ltr">
                <?= permitEscape($containerNumber) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">الوزن الإجمالي</span>

            <span class="info-value">
                <?= permitEscape(
                    $shipmentWeight !== null && $shipmentWeight !== ''
                        ? permitFormatNumber($shipmentWeight, 3)
                        : '—'
                ) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">عدد الطرود</span>

            <span class="info-value">
                <?= permitEscape(
                    $packagesCount !== null && $packagesCount !== ''
                        ? $packagesCount
                        : '—'
                ) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">القيمة المصرح بها</span>

            <span class="info-value">
                <?= permitEscape(
                    $declaredValue !== null && $declaredValue !== ''
                        ? permitFormatNumber($declaredValue)
                        : '—'
                ) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">حالة البيان</span>

            <span class="info-value">
                <?= permitEscape($permit['status']) ?>
            </span>
        </div>

    </div>

</section>