<!-- ملخص المحاسب -->
<div class="section-heading">
    <div>
        <h3>الملخص المالي</h3>
        <p>ملخص المدفوعات المسجلة اليوم</p>
    </div>
</div>

<div class="stats-grid">

    <?= statCard(
        'مدفوعات اليوم',
        number_format((float) $stats['today_revenue'], 2),
        'fa-money-bill-wave',
        'بحسب سجلات المدفوعات'
    ) ?>

</div>