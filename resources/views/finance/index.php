<?php
declare(strict_types=1);
?>

<!-- ======================================== -->
<!-- القسم المالي -->
<!-- ======================================== -->

<div class="welcome-panel">

    <div>
        <h3>
            <i class="fa-solid fa-calculator ms-2"></i>
            القسم المالي واحتساب الرسوم
        </h3>

        <p>
            احتساب التعريفات الجمركية والضرائب وإصدار الفواتير.
        </p>
    </div>

    <div class="welcome-icon">
        <i class="fa-solid fa-wallet"></i>
    </div>

</div>

<!-- ======================================== -->
<!-- رسائل الأخطاء -->
<!-- ======================================== -->

<?php if (!empty($errors)): ?>

    <div class="alert alert-danger mb-4" role="alert">

        <ul class="mb-0">

            <?php foreach ($errors as $error): ?>

                <li><?= e($error) ?></li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>

<!-- ======================================== -->
<!-- رسالة النجاح -->
<!-- ======================================== -->

<?php if (!empty($success)): ?>

    <div class="alert alert-success mb-4" role="alert">

        <i class="fa-solid fa-circle-check ms-1"></i>

        <?= e($success) ?>

    </div>

<?php endif; ?>

<!-- ======================================== -->
<!-- روابط القسم المالي -->
<!-- ======================================== -->

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

    <div class="section-heading mb-0">

        <h3>إدارة الرسوم الجمركية</h3>

        <p class="text-muted mb-0 mt-2">
            البيانات الجمركية التي تنتظر الاحتساب المالي.
        </p>

    </div>

    <a
        href="payment.php"
        class="btn-primary-custom"
    >

        <i class="fa-solid fa-money-bill-transfer"></i>

        تحصيل المدفوعات

    </a>

</div>

<!-- ======================================== -->
<!-- البحث -->
<!-- ======================================== -->

<div class="panel-card mb-4">

    <div class="panel-body">

        <form
            method="GET"
            action="finance.php"
            class="row g-3 align-items-end"
        >

            <div class="col-12 col-md-8">

                <label
                    for="financeSearch"
                    class="form-label"
                >
                    البحث برقم البيان أو اسم الشركة
                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </span>

                    <input
                        type="text"
                        id="financeSearch"
                        name="search"
                        class="form-control"
                        placeholder="أدخل رقم البيان أو اسم الشركة"
                        value="<?= e($search) ?>"
                    >

                </div>

            </div>

            <div class="col-12 col-md-4 d-flex gap-2">

                <button
                    type="submit"
                    class="btn-primary-custom flex-grow-1"
                >

                    <i class="fa-solid fa-magnifying-glass"></i>

                    بحث

                </button>

                <?php if ($search !== ''): ?>

                    <a
                        href="finance.php"
                        class="btn-outline-custom"
                    >

                        <i class="fa-solid fa-xmark"></i>

                        إلغاء

                    </a>

                <?php endif; ?>

            </div>

        </form>

    </div>

</div>

<!-- ======================================== -->
<!-- البيانات بانتظار الاحتساب المالي -->
<!-- ======================================== -->

<div class="panel-card mb-4">

    <div class="panel-heading">

        <div>

            <h3>
                <i class="fa-solid fa-file-invoice-dollar ms-2"></i>
                البيانات بانتظار الاحتساب المالي
            </h3>

            <p>
                مراجعة البيانات المؤهلة لإصدار الفواتير.
            </p>

        </div>

        <span class="status-badge status-pending">

            <?= count($declarations) ?>

            بيان

        </span>

    </div>

    <div class="panel-body">

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th scope="col">رقم البيان</th>

                        <th scope="col">الشركة</th>

                        <th scope="col">نوع البضاعة</th>

                        <th scope="col">بلد المنشأ</th>

                        <th scope="col">القيمة المصرح بها</th>

                        <th scope="col" class="text-center">
                            الإجراء
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($declarations)): ?>

                        <?php foreach ($declarations as $declaration): ?>

                            <tr>

                                <td>

                                    <strong class="text-primary">

                                        <?= e($declaration['declaration_id'] ?? '') ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= e($declaration['company_name'] ?? '') ?>

                                </td>

                                <td>

                                    <?= e($declaration['shipment_type'] ?? '') ?>

                                </td>

                                <td>

                                    <?= e($declaration['origin_country'] ?? '') ?>

                                </td>

                                <td>

                                    <strong>

                                        <?= number_format(
                                            (float) ($declaration['declared_value'] ?? 0),
                                            2
                                        ) ?>

                                        $

                                    </strong>

                                </td>

                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn-primary-custom"
                                        data-bs-toggle="modal"
                                        data-bs-target="#financeModal"
                                        data-declaration-id="<?= e($declaration['declaration_id'] ?? '') ?>"
                                        data-declared-value="<?= e($declaration['declared_value'] ?? 0) ?>"
                                    >

                                        <i class="fa-solid fa-calculator"></i>

                                        احتساب الرسوم

                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i class="fa-solid fa-circle-check fa-2x mb-3"></i>

                                    <p class="mb-0">
                                        لا توجد بيانات بانتظار الاحتساب المالي.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ======================================== -->
<!-- نافذة احتساب الرسوم -->
<!-- ======================================== -->

<div
    class="modal fade"
    id="financeModal"
    tabindex="-1"
    aria-labelledby="financeModalTitle"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title fw-bold"
                    id="financeModalTitle"
                >

                    <i class="fa-solid fa-calculator ms-2"></i>

                    احتساب الرسوم الجمركية

                </h5>

                <button
                    type="button"
                    class="btn-close ms-0"
                    data-bs-dismiss="modal"
                    aria-label="إغلاق"
                ></button>

            </div>

            <form
                action="Backend_controller/process_finance.php"
                method="POST"
                id="financeForm"
            >

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrfToken) ?>"
                    >

                    <div class="row g-3">

                        <!-- رقم البيان -->

                        <div class="col-12 col-md-6">

                            <label
                                for="modalDeclarationId"
                                class="form-label"
                            >
                                رقم البيان الجمركي
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="modalDeclarationId"
                                readonly
                            >

                            <input
                                type="hidden"
                                name="declaration_id"
                                id="declarationId"
                                required
                            >

                        </div>

                        <!-- القيمة المصرح بها -->

                        <div class="col-12 col-md-6">

                            <label
                                for="modalDeclaredValue"
                                class="form-label"
                            >
                                القيمة المصرح بها
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="modalDeclaredValue"
                                readonly
                            >

                        </div>

                        <!-- HS Code -->

                        <div class="col-12">

                            <label
                                for="hsCode"
                                class="form-label"
                            >
                                البند الجمركي الدولي HS Code
                            </label>

                            <select
                                name="hs_code"
                                id="hsCode"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- اختر HS Code --
                                </option>

                                <?php foreach ($tariffs as $tariff): ?>

                                    <option
                                        value="<?= e($tariff['hs_code'] ?? '') ?>"
                                        data-customs-rate="<?= e($tariff['customs_duty_rate'] ?? 0) ?>"
                                        data-tax-rate="<?= e($tariff['tax_rate'] ?? 0) ?>"
                                    >

                                        <?= e($tariff['hs_code'] ?? '') ?>

                                        -

                                        <?= e($tariff['category_name'] ?? '') ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- نسبة الرسوم الجمركية -->

                        <div class="col-12 col-md-6">

                            <label
                                for="customsRate"
                                class="form-label"
                            >
                                نسبة الرسوم الجمركية
                            </label>

                            <input
                                type="text"
                                id="customsRate"
                                class="form-control"
                                readonly
                            >

                        </div>

                        <!-- نسبة الضريبة -->

                        <div class="col-12 col-md-6">

                            <label
                                for="taxRate"
                                class="form-label"
                            >
                                نسبة الضريبة
                            </label>

                            <input
                                type="text"
                                id="taxRate"
                                class="form-control"
                                readonly
                            >

                        </div>

                        <!-- ملخص الاحتساب -->

                        <div class="col-12">

                            <div class="cms-card">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <span class="text-muted">
                                        الرسوم الجمركية
                                    </span>

                                    <strong id="dutyAmount">
                                        0.00
                                    </strong>

                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <span class="text-muted">
                                        الضريبة
                                    </span>

                                    <strong id="taxAmount">
                                        0.00
                                    </strong>

                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">

                                    <span class="fw-bold">
                                        الإجمالي
                                    </span>

                                    <strong
                                        class="text-primary fs-5"
                                        id="totalAmount"
                                    >
                                        0.00
                                    </strong>

                                </div>

                                <p class="text-muted small mt-3 mb-0">

                                    هذه معاينة تقديرية؛ الحساب النهائي يتم على الخادم.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-outline-custom"
                        data-bs-dismiss="modal"
                    >

                        إلغاء

                    </button>

                    <button
                        type="submit"
                        class="btn-primary-custom"
                        id="submitFinance"
                    >

                        <i class="fa-solid fa-file-invoice-dollar"></i>

                        اعتماد وإصدار الفاتورة

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>