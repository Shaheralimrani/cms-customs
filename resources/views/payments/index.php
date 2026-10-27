<?php
declare(strict_types=1);
?>

<!-- عنوان القسم -->
<section class="welcome-panel mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="welcome-icon">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>

            <div>
                <h3 class="mb-2">تحصيل الرسوم الجمركية</h3>
                <p class="mb-0">
                    استلام المبالغ المستحقة وتأكيد سداد الفاتورة الجمركية.
                </p>
            </div>
        </div>

        <a href="finance.php" class="btn btn-outline-custom">
            <i class="fa-solid fa-calculator me-1"></i>
            احتساب الفواتير
        </a>
    </div>
</section>

<!-- رسائل الأخطاء -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger rounded-3" role="alert">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= e((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- رسالة النجاح -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success rounded-3" role="alert">
        <i class="fa-solid fa-circle-check me-1"></i>
        <?= e((string) $success) ?>
    </div>
<?php endif; ?>

<!-- البحث عن الفواتير -->
<section class="panel-card mb-4">
    <div class="panel-heading">
        <h5 class="mb-0">
            <i class="fa-solid fa-magnifying-glass me-2"></i>
            البحث عن فاتورة
        </h5>
    </div>

    <div class="panel-body">
        <form method="GET" action="payment.php" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label for="paymentSearch" class="form-label">
                    رقم الفاتورة أو البيان أو اسم الشركة
                </label>

                <input
                    type="text"
                    id="paymentSearch"
                    name="search"
                    class="form-control"
                    placeholder="أدخل رقم الفاتورة أو البيان أو اسم الشركة..."
                    value="<?= e($search) ?>"
                >
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    بحث
                </button>

                <?php if ($search !== ''): ?>
                    <a href="payment.php" class="btn btn-outline-custom">
                        إلغاء
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<!-- الفواتير بانتظار السداد -->
<section class="panel-card mb-4">
    <div class="panel-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            الفواتير بانتظار السداد
        </h5>

        <span class="status-badge status-pending">
            <i class="fa-solid fa-clock me-1"></i>
            قيد التحصيل
        </span>
    </div>

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>رقم البيان</th>
                        <th>الشركة</th>
                        <th>الإجمالي المستحق</th>
                        <th class="text-center">الإجراء</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td class="fw-bold">
                                    #<?= (int) $invoice['invoice_id'] ?>
                                </td>

                                <td class="fw-bold">
                                    <?= e((string) $invoice['declaration_id']) ?>
                                </td>

                                <td>
                                    <?= e((string) $invoice['company_name']) ?>
                                </td>

                                <td class="fw-bold">
                                    $<?= number_format(
                                        (float) $invoice['total_amount'],
                                        2
                                    ) ?>
                                </td>

                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary-custom"
                                        data-bs-toggle="modal"
                                        data-bs-target="#paymentModal"
                                        data-invoice-id="<?= (int) $invoice['invoice_id'] ?>"
                                        data-declaration-id="<?= e((string) $invoice['declaration_id']) ?>"
                                        data-company="<?= e((string) $invoice['company_name']) ?>"
                                        data-total="<?= e((string) $invoice['total_amount']) ?>"
                                    >
                                        <i class="fa-solid fa-cash-register me-1"></i>
                                        تحصيل المبلغ
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa-solid fa-circle-check fs-3 d-block mb-3"></i>

                                <strong>لا توجد فواتير بانتظار السداد.</strong>

                                <p class="text-muted mb-0 mt-2">
                                    ستظهر هنا الفواتير المستحقة للتحصيل.
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- نافذة تأكيد تحصيل الرسوم -->
<div
    class="modal fade"
    id="paymentModal"
    tabindex="-1"
    aria-labelledby="paymentModalTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="paymentModalTitle">
                    <i class="fa-solid fa-money-check-dollar me-2"></i>
                    تأكيد تحصيل الرسوم
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="إغلاق"
                ></button>
            </div>

            <form
                action="Backend_controller/process_payment.php"
                method="POST"
                id="paymentForm"
            >
                <div class="modal-body">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
                    >

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modalInvoiceId" class="form-label">
                                رقم الفاتورة
                            </label>

                            <input
                                type="text"
                                id="modalInvoiceId"
                                class="form-control"
                                readonly
                            >

                            <input
                                type="hidden"
                                name="invoice_id"
                                id="invoiceId"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="modalDeclarationId" class="form-label">
                                رقم البيان الجمركي
                            </label>

                            <input
                                type="text"
                                id="modalDeclarationId"
                                class="form-control"
                                readonly
                            >
                        </div>

                        <div class="col-12">
                            <label for="modalCompany" class="form-label">
                                الشركة / المستورد
                            </label>

                            <input
                                type="text"
                                id="modalCompany"
                                class="form-control"
                                readonly
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="modalTotal" class="form-label">
                                المبلغ المستحق
                            </label>

                            <input
                                type="text"
                                id="modalTotal"
                                class="form-control fw-bold"
                                readonly
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="amountPaid" class="form-label">
                                المبلغ المدفوع
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="amount_paid"
                                id="amountPaid"
                                class="form-control fw-bold"
                                required
                            >
                        </div>

                        <div class="col-12">
                            <label for="paymentMethod" class="form-label">
                                طريقة السداد
                            </label>

                            <select
                                name="payment_method"
                                id="paymentMethod"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    -- اختر طريقة السداد --
                                </option>

                                <option value="cash">
                                    نقدي (Cash)
                                </option>

                                <option value="bank_transfer">
                                    تحويل بنكي (Bank Transfer)
                                </option>

                                <option value="cheque">
                                    شيك (Cheque)
                                </option>
                            </select>
                        </div>

                        <div id="bankDetailsFields" class="col-12 d-none">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label
                                        for="bankName"
                                        class="form-label"
                                        id="bankNameLabel"
                                    >
                                        اسم البنك
                                    </label>

                                    <input
                                        type="text"
                                        name="bank_name"
                                        id="bankName"
                                        class="form-control"
                                        maxlength="150"
                                        placeholder="مثال: بنك الكريمي"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label
                                        for="referenceNumber"
                                        class="form-label"
                                        id="referenceLabel"
                                    >
                                        رقم مرجعي / حوالة / شيك
                                    </label>

                                    <input
                                        type="text"
                                        name="reference_number"
                                        id="referenceNumber"
                                        class="form-control"
                                        maxlength="150"
                                        placeholder="أدخل الرقم"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                يجب أن يكون المبلغ المدفوع مساويًا للمبلغ المستحق بالكامل.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline-custom"
                        data-bs-dismiss="modal"
                    >
                        إلغاء
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary-custom"
                        id="submitPayment"
                    >
                        <i class="fa-solid fa-check-double me-1"></i>
                        تأكيد الدفع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>