<?php
$baseUrl = 'release_permits.php';
?>

<div class="container-fluid px-0">

    <!-- عنوان الصفحة -->

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-2">
                <i class="fa-solid fa-file-circle-check me-2"></i>
                إدارة أذونات الإفراج الجمركي
            </h4>

            <p class="text-secondary mb-0">
                إصدار الأذونات للبيانات التي اكتملت إجراءات دفع فواتيرها.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">

            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                <i class="fa-solid fa-user-shield me-1"></i>
                مدير النظام
            </span>

        </div>

    </div>


    <!-- رسائل النجاح -->

    <?php if (!empty($success)): ?>

        <div class="alert alert-success rounded-3" role="alert">

            <i class="fa-solid fa-circle-check me-2"></i>

            <?= releaseEscape($success) ?>

        </div>

    <?php endif; ?>


    <!-- رسائل الأخطاء -->

    <?php if (!empty($errors)): ?>

        <div class="alert alert-danger rounded-3" role="alert">

            <div class="fw-bold mb-2">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                تعذر تنفيذ العملية
            </div>

            <ul class="mb-0">

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?= releaseEscape($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <!-- ================================== -->
    <!-- البيانات الجاهزة للإفراج -->
    <!-- ================================== -->

    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-header bg-transparent border-0 px-3 px-md-4 pt-4">

            <h5 class="fw-bold mb-1">
                <i class="fa-solid fa-boxes-stacked me-2"></i>
                البيانات الجاهزة للإفراج
            </h5>

            <p class="text-secondary small mb-0 mt-2">
                البيانات التي سُددت فواتيرها ولم يصدر لها إذن إفراج بعد.
            </p>

        </div>

        <div class="card-body px-3 px-md-4">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>رقم البيان</th>
                            <th>الشركة</th>
                            <th>نوع الشحنة</th>
                            <th>إجمالي الفاتورة</th>
                            <th>المبلغ المدفوع</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الدفع</th>
                            <th>الإجراء</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($readyForRelease)): ?>

                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">

                                    <i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>

                                    لا توجد بيانات جاهزة للإفراج حاليًا.

                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($readyForRelease as $row): ?>

                                <tr>

                                    <td class="fw-semibold">
                                        <?= releaseEscape($row['declaration_id']) ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($row['company_name'] ?? '—') ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($row['shipment_type'] ?? '—') ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <?= number_format((float) $row['total_amount'], 2) ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <?= number_format((float) $row['payment_amount'], 2) ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($row['payment_method'] ?? '—') ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <?= releaseEscape($row['payment_date'] ?? '—') ?>
                                    </td>

                                    <td class="text-nowrap">

                                        <button
                                            type="button"
                                            class="btn btn-success btn-sm issue-permit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#issuePermitModal"
                                            data-declaration-id="<?= releaseEscape($row['declaration_id']) ?>"
                                            data-payment-id="<?= (int) $row['payment_id'] ?>"
                                        >
                                            <i class="fa-solid fa-file-circle-check me-1"></i>
                                            إصدار إذن الإفراج
                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ================================== -->
    <!-- سجل أذونات الإفراج الصادرة -->
    <!-- ================================== -->

    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-header bg-transparent border-0 px-3 px-md-4 pt-4">

            <h5 class="fw-bold mb-1">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>
                سجل أذونات الإفراج الصادرة
            </h5>

            <p class="text-secondary small mb-0 mt-2">
                عرض الأذونات السابقة وإمكانية فتحها وطباعتها.
            </p>

        </div>

        <div class="card-body px-3 px-md-4">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>رقم الإذن</th>
                            <th>رقم البيان</th>
                            <th>الشركة</th>
                            <th>نوع الشحنة</th>
                            <th>المبلغ المدفوع</th>
                            <th>أصدره</th>
                            <th>تاريخ الإصدار</th>
                            <th>الإجراء</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($permits)): ?>

                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">

                                    <i class="fa-solid fa-folder-open fa-2x d-block mb-2"></i>

                                    لم تصدر أذونات إفراج حتى الآن.

                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($permits as $permit): ?>

                                <tr>

                                    <td class="fw-semibold">
                                        <?= releaseEscape($permit['permit_id']) ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($permit['declaration_id']) ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($permit['company_name'] ?? '—') ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($permit['shipment_type'] ?? '—') ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <?= number_format((float) $permit['payment_amount'], 2) ?>
                                    </td>

                                    <td>
                                        <?= releaseEscape($permit['issuer_name'] ?? '—') ?>
                                    </td>

                                    <td class="text-nowrap">
                                        <?= releaseEscape($permit['issued_at']) ?>
                                    </td>

                                    <td class="text-nowrap">

                                        <a
                                            href="print_release_permit.php?permit_id=<?= rawurlencode($permit['permit_id']) ?>"
                                            class="btn btn-primary btn-sm"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <i class="fa-solid fa-print me-1"></i>
                                            عرض وطباعة
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- ================================== -->
<!-- نافذة تأكيد إصدار إذن الإفراج -->
<!-- ================================== -->

<div
    class="modal fade"
    id="issuePermitModal"
    tabindex="-1"
    aria-labelledby="issuePermitModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <form
                action="Backend_controller/process_release_permit.php"
                method="POST"
            >

                <div class="modal-header">

                    <h5
                        class="modal-title fw-bold"
                        id="issuePermitModalLabel"
                    >
                        <i class="fa-solid fa-file-circle-check me-2"></i>
                        تأكيد إصدار إذن الإفراج
                    </h5>

                    <button
                        type="button"
                        class="btn-close ms-0"
                        data-bs-dismiss="modal"
                        aria-label="إغلاق"
                    ></button>

                </div>


                <div class="modal-body">

                    <p class="mb-3">
                        هل أنت متأكد من إصدار إذن الإفراج لهذا البيان؟
                    </p>


                    <div class="mb-3">

                        <label
                            for="permitDeclarationId"
                            class="form-label"
                        >
                            رقم البيان الجمركي
                        </label>

                        <input
                            type="text"
                            name="declaration_id"
                            id="permitDeclarationId"
                            class="form-control"
                            readonly
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label
                            for="permitPaymentId"
                            class="form-label"
                        >
                            رقم عملية الدفع
                        </label>

                        <input
                            type="number"
                            name="payment_id"
                            id="permitPaymentId"
                            class="form-control"
                            readonly
                            required
                        >

                    </div>


                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= releaseEscape($csrfToken) ?>"
                    >


                    <div class="alert alert-warning mb-0">

                        <i class="fa-solid fa-triangle-exclamation me-1"></i>

                        بعد نجاح الإصدار ستتغير حالة البيان إلى
                        <strong>released</strong>.

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        إلغاء
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        <i class="fa-solid fa-check me-1"></i>
                        تأكيد إصدار الإذن
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>