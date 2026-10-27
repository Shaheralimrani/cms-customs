<!-- بيانات الفحص -->

<section class="section">

    <h3 class="section-title">
        ثالثًا: بيانات الفحص الجمركي
    </h3>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">نتيجة الفحص المسجلة</span>

            <span class="info-value">
                <?= permitEscape($routeColor) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">تاريخ الفحص الأخير</span>

            <span class="info-value ltr">
                <?= permitEscape($inspectionDate) ?>
            </span>
        </div>

    </div>

    <p style="font-size: 11px; color: #687582;">
        يعرض هذا القسم آخر سجل فحص مرتبط بالبيان، دون تكرار
        بيانات إذن الإفراج عند وجود عدة سجلات فحص.
    </p>

</section>