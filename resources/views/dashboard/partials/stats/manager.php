<!-- إحصائيات المدير -->
<div class="section-heading">
    <div>
        <h3>ملخص النظام</h3>
        <p>نظرة عامة على المعاملات والعمليات المالية</p>
    </div>
</div>

<div class="stats-grid">

    <?= statCard(
        'معاملات اليوم',
        number_format((float) $stats['today_declarations']),
        'fa-file-circle-plus',
        'المعاملات المنشأة اليوم'
    ) ?>

    <?= statCard(
        'المعاملات المنجزة',
        number_format((float) $stats['processed_declarations']),
        'fa-circle-check',
        'معاينة أو فوترة أو سداد أو إفراج'
    ) ?>

    <?= statCard(
        'بانتظار المعاينة',
        number_format((float) $stats['pending_inspections']),
        'fa-magnifying-glass',
        'المعاملات الجديدة'
    ) ?>

    <?= statCard(
        'مدفوعات اليوم',
        number_format((float) $stats['today_revenue'], 2),
        'fa-money-bill-wave',
        'بحسب سجلات المدفوعات'
    ) ?>

    <?= statCard(
        'معاملات بالمسار الأحمر',
        number_format((float) $stats['held_declarations']),
        'fa-triangle-exclamation',
        'تحتاج إلى متابعة'
    ) ?>

</div>