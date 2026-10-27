<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$successMessage = $_SESSION['approval_success'] ?? '';
$errorMessages = $_SESSION['approval_errors'] ?? [];

unset(
    $_SESSION['approval_success'],
    $_SESSION['approval_errors']
);

$csrfToken = $_SESSION['csrf_token'] ?? '';

if (!is_string($csrfToken) || $csrfToken === '') {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}

if (!function_exists('approval_e')) {
    function approval_e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
?>

<div class="container-fluid py-3">

    <?php if ($successMessage !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= approval_e($successMessage) ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="إغلاق"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>تعذر تنفيذ العملية:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ((array) $errorMessages as $error): ?>
                    <li><?= approval_e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-1">
                <i class="fa-solid fa-user-check text-primary me-2"></i>
                البيانات التي تحتاج إلى قرار المدير
            </h5>
            <p class="text-muted mb-0 small">
                تظهر هنا البيانات التي أحالها المفتش إلى المدير بعد اختيار المسار الأحمر.
            </p>
        </div>

        <div class="card-body">

            <?php if (empty($approvals)): ?>

                <div class="text-center py-5">
                    <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                    <h5>لا توجد بيانات معلقة للموافقة</h5>
                    <p class="text-muted mb-0">
                        ستظهر البيانات هنا عند إحالتها من المفتش.
                    </p>
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>رقم البيان</th>
                                <th>الشركة</th>
                                <th>بلد المنشأ</th>
                                <th>نوع الشحنة</th>
                                <th>الوزن</th>
                                <th>عدد الطرود</th>
                                <th>القيمة المصرح بها</th>
                                <th>نتيجة المعاينة</th>
                                <th>ملاحظات المفتش</th>
                                <th>قرار المدير</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($approvals as $approval): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= approval_e($approval['declaration_id'] ?? '') ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['company_name'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['origin_country'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['shipment_type'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['total_weight'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['packages_count'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <?= approval_e($approval['declared_value'] ?? 'غير محدد') ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            المسار الأحمر
                                        </span>
                                    </td>

                                    <td style="min-width: 180px; max-width: 280px; white-space: normal;">
                                        <?= nl2br(approval_e($approval['inspection_notes'] ?? 'لا توجد ملاحظات')) ?>
                                    </td>

                                    <td style="min-width: 280px;">
                                        <form method="POST"
                                              action="Backend_controller/process_inspection_approval.php"
                                              onsubmit="return confirm('هل أنت متأكد من تنفيذ القرار؟');">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?= approval_e($csrfToken) ?>">

                                            <input type="hidden"
                                                   name="declaration_id"
                                                   value="<?= approval_e($approval['declaration_id'] ?? '') ?>">

                                            <div class="mb-2">
                                                <label class="form-label">
                                                    القرار
                                                </label>

                                                <select name="decision"
                                                        class="form-select"
                                                        required>
                                                    <option value="">
                                                        اختر القرار
                                                    </option>

                                                    <option value="reinspect">
                                                        السماح بإعادة المعاينة
                                                    </option>

                                                    <option value="hold">
                                                        إبقاء البيان موقوفًا
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">
                                                    ملاحظات المدير
                                                </label>

                                                <textarea name="manager_notes"
                                                          class="form-control"
                                                          rows="2"
                                                          maxlength="5000"
                                                          placeholder="اكتب سبب القرار أو التعليمات..."></textarea>
                                            </div>

                                            <button type="submit"
                                                    class="btn btn-primary btn-sm w-100">
                                                <i class="fa-solid fa-check me-1"></i>
                                                تنفيذ القرار
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="mt-3">
        <a href="inspection.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-right me-1"></i>
            العودة إلى صفحة المعاينة
        </a>
    </div>

</div>