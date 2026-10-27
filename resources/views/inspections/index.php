<?php
declare(strict_types=1);

$declarations = $declarations ?? [];
$search = $search ?? '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

$successMessage = $_SESSION['inspection_success'] ?? null;
$errorMessages = $_SESSION['inspection_errors'] ?? [];

unset(
    $_SESSION['inspection_success'],
    $_SESSION['inspection_errors']
);

if (!is_array($errorMessages)) {
    $errorMessages = [];
}

if (!function_exists('inspectionStatusLabel')) {
    function inspectionStatusLabel(string $status): string
    {
        $statuses = [
            'new' => 'بانتظار المعاينة',
            'inspection_review' => 'يحتاج إلى معاينة إضافية',
            'pending_manager_approval' => 'بانتظار موافقة المدير',
            'inspected' => 'اجتاز المعاينة',
        ];

        return $statuses[$status] ?? $status;
    }
}

if (!function_exists('inspectionRouteLabel')) {
    function inspectionRouteLabel(?string $color): string
    {
        $colors = [
            'green' => 'أخضر',
            'yellow' => 'أصفر',
            'red' => 'أحمر',
        ];

        return $colors[$color ?? ''] ?? 'لم تتم المعاينة';
    }
}

if (!function_exists('inspectionRouteClass')) {
    function inspectionRouteClass(?string $color): string
    {
        $classes = [
            'green' => 'route-green',
            'yellow' => 'route-yellow',
            'red' => 'route-red',
        ];

        return $classes[$color ?? ''] ?? 'route-unknown';
    }
}
?>

<div class="inspection-container">

    <header class="inspection-page-header">

        <div>
            <h1 class="page-title">معاينة البيانات الجمركية</h1>

            <p class="page-description">
                يمكنك إعادة معاينة البيان إذا كان يحتاج إلى مراجعة إضافية.
                البيانات الموقوفة بانتظار المدير لا تظهر في هذه القائمة.
            </p>
        </div>

        <div class="inspection-header-icon" aria-hidden="true">
            <i class="fa-solid fa-clipboard-check"></i>
        </div>

    </header>

    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= e($successMessage) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="إغلاق"
            ></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="alert alert-danger alert-dismissible fade show">

            <?php foreach ($errorMessages as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="إغلاق"
            ></button>

        </div>
    <?php endif; ?>

    <!-- البحث -->
    <section class="inspection-panel">

        <form
            method="GET"
            action="inspection.php"
            class="inspection-search-form"
        >

            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="ابحث باسم الشركة أو رقم البيان"
                    value="<?= e($search) ?>"
                    aria-label="البحث باسم الشركة أو رقم البيان"
                >
            </div>

            <button type="submit" class="btn btn-primary">
                بحث
            </button>

            <a href="inspection.php" class="btn btn-light">
                عرض الكل
            </a>

        </form>

    </section>

    <!-- جدول البيانات -->
    <section class="inspection-panel">

        <div class="inspection-table-heading">
            <div>
                <h2>البيانات التي تحتاج إلى معاينة</h2>

                <p>
                    عدد البيانات المعروضة:
                    <strong><?= count($declarations) ?></strong>
                </p>
            </div>
        </div>

        <div class="table-responsive">

            <table class="table table-hover inspection-table">

                <thead>
                    <tr>
                        <th>رقم البيان</th>
                        <th>اسم الشركة</th>
                        <th>رقم الترخيص</th>
                        <th>بلد المنشأ</th>
                        <th>نوع الشحنة</th>
                        <th>الوزن</th>
                        <th>عدد الطرود</th>
                        <th>القيمة المعلنة</th>
                        <th>منفذ الدخول</th>
                        <th>رقم الحاوية</th>
                        <th>اسم السائق</th>
                        <th>حالة البيان</th>
                        <th>آخر مسار</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($declarations)): ?>

                    <tr>
                        <td colspan="14" class="empty-message">
                            <i class="fa-solid fa-folder-open"></i>
                            <p>لا توجد بيانات تحتاج إلى المعاينة حاليًا.</p>
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($declarations as $declaration): ?>

                        <?php
                        $status = (string) (
                            $declaration['status'] ?? 'new'
                        );

                        $statusClass = $status === 'inspection_review'
                            ? 'status-review'
                            : 'status-new';

                        $routeColor = $declaration['route_color'] ?? null;
                        $declarationId = (string) (
                            $declaration['declaration_id'] ?? ''
                        );
                        ?>

                        <tr>

                            <td><?= e($declarationId) ?></td>

                            <td>
                                <?= e($declaration['company_name'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['license_number'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['origin_country'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['shipment_type'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['total_weight'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['packages_count'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['declared_value'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['port_of_entry'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['container_number'] ?? '') ?>
                            </td>

                            <td>
                                <?= e($declaration['driver_name'] ?? '') ?>
                            </td>

                            <td>
                                <span class="status-badge <?= e($statusClass) ?>">
                                    <?= e(inspectionStatusLabel($status)) ?>
                                </span>
                            </td>

                            <td>
                                <span class="route-badge <?= e(
                                    inspectionRouteClass($routeColor)
                                ) ?>">
                                    <?= e(inspectionRouteLabel($routeColor)) ?>
                                </span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm open-inspection-modal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#inspectionModal"
                                    data-declaration-id="<?= e($declarationId) ?>"
                                >
                                    <?= $status === 'inspection_review'
                                        ? 'إعادة المعاينة'
                                        : 'معاينة' ?>
                                </button>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

<!-- نافذة تسجيل المعاينة -->
<div
    class="modal fade"
    id="inspectionModal"
    tabindex="-1"
    aria-labelledby="inspectionModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="Backend_controller/process_inspection.php"
            >

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="inspectionModalLabel">
                        تسجيل المعاينة
                    </h2>

                    <button
                        type="button"
                        class="btn-close ms-0"
                        data-bs-dismiss="modal"
                        aria-label="إغلاق"
                    ></button>
                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrfToken) ?>"
                    >

                    <div class="mb-3">
                        <label for="declaration_id" class="form-label">
                            رقم البيان
                        </label>

                        <input
                            type="text"
                            id="declaration_id"
                            name="declaration_id"
                            class="form-control"
                            readonly
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="route_color" class="form-label">
                            مسار المعاينة
                        </label>

                        <select
                            name="route_color"
                            id="route_color"
                            class="form-select"
                            required
                        >
                            <option value="">اختر المسار</option>

                            <option value="green">
                                أخضر — اجتاز المعاينة
                            </option>

                            <option value="yellow">
                                أصفر — يحتاج إلى مراجعة إضافية
                            </option>

                            <option value="red">
                                أحمر — إيقاف وإحالة إلى المدير
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="inspection_notes" class="form-label">
                            ملاحظات المعاينة
                        </label>

                        <textarea
                            name="inspection_notes"
                            id="inspection_notes"
                            class="form-control"
                            rows="5"
                            maxlength="5000"
                            required
                            placeholder="اكتب نتائج المعاينة والملاحظات..."
                        ></textarea>
                    </div>

                    <div class="alert alert-warning mb-0">
                        عند اختيار المسار الأحمر، سيُوقف البيان ويُحال إلى
                        المدير للمراجعة والموافقة.
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

                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk"></i>
                        حفظ المعاينة
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>