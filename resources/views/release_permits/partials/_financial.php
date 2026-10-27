<!-- البيانات المالية -->

<section class="section">

    <h3 class="section-title">
        رابعًا: بيانات الفاتورة والسداد
    </h3>

    <div class="info-grid">

        <div class="info-item">
            <span class="info-label">رقم الفاتورة</span>

            <span class="info-value">
                <?= permitEscape(
                    $invoiceId !== null ? '#' . $invoiceId : '—'
                ) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">رقم عملية الدفع</span>

            <span class="info-value">
                <?= permitEscape('#' . $paymentId) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">طريقة الدفع</span>

            <span class="info-value">
                <?= permitEscape($paymentMethod) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">تاريخ الدفع</span>

            <span class="info-value ltr">
                <?= permitEscape($paymentDate) ?>
            </span>
        </div>

    </div>

    <table class="financial-table">

        <thead>
            <tr>
                <th>البند</th>
                <th>المبلغ</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td>الرسوم الجمركية</td>

                <td>
                    <?= permitEscape(
                        $dutyAmount !== null
                            ? permitFormatNumber($dutyAmount)
                            : '—'
                    ) ?>
                </td>
            </tr>

            <tr>
                <td>الضرائب</td>

                <td>
                    <?= permitEscape(
                        $taxAmount !== null
                            ? permitFormatNumber($taxAmount)
                            : '—'
                    ) ?>
                </td>
            </tr>

            <tr>
                <td>إجمالي الفاتورة</td>

                <td>
                    <?= permitEscape(
                        $invoiceTotal !== null
                            ? permitFormatNumber($invoiceTotal)
                            : '—'
                    ) ?>
                </td>
            </tr>

            <tr class="total-row">
                <td>إجمالي المبلغ المدفوع</td>

                <td>
                    <?= permitEscape(
                        $paymentAmount !== null
                            ? permitFormatNumber($paymentAmount)
                            : '—'
                    ) ?>
                </td>
            </tr>

        </tbody>

    </table>

</section>