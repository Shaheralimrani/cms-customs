<!-- إحصائيات المعاين -->
<div class="section-heading">
    <div>
        <h3>ملخص المعاينة</h3>
        <p>المعاملات التي تحتاج إلى متابعة</p>
    </div>
</div>

<div class="stats-grid">

    <?= statCard(
        'بانتظار المعاينة',
        number_format((float) $stats['pending_inspections']),
        'fa-magnifying-glass',
        'المعاملات الجديدة'
    ) ?>

    <?= statCard(
        'معاملات بالمسار الأحمر',
        number_format((float) $stats['held_declarations']),
        'fa-triangle-exclamation',
        'تحتاج إلى متابعة'
    ) ?>

</div>