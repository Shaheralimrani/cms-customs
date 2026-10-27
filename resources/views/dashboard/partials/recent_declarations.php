<!-- أحدث المعاملات -->
<section class="data-panel">

    <div class="panel-heading">
        <div>
            <h3>أحدث المعاملات</h3>
            <p>آخر المعاملات المسجلة في النظام</p>
        </div>
        <span class="panel-count">
            <?= count($recentDeclarations) ?> معاملات
        </span>
    </div>

    <div class="table-responsive">
        <table class="table dashboard-table">
            <thead>
                <tr>
                    <th>رقم المعاملة</th>
                    <th>الشركة</th>
                    <th>بلد المنشأ</th>
                    <th>نوع الشحنة</th>
                    <th>الحاوية</th>
                    <th>المسار</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>

            <?php if (empty($recentDeclarations)): ?>

                <tr>
                    <td colspan="9" class="empty-state">
                        لا توجد معاملات مسجلة حتى الآن.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($recentDeclarations as $row): ?>

                    <?php
                    $status      = (string) ($row['status'] ?? '');
                    $statusLabel = getStatusLabel($status);
                    $statusClass = getStatusClass($status);

                    $routeColor = strtolower((string) ($row['latest_route_color'] ?? ''));

                    $routeNames = [
                        'red'    => 'أحمر',
                        'yellow' => 'أصفر',
                        'green'  => 'أخضر',
                    ];

                    $routeLabel = $routeNames[$routeColor] ?? 'غير محدد';
                    ?>

                    <tr>
                        <td>#<?= e($row['declaration_id'] ?? '') ?></td>

                        <td><?= e(!empty($row['company_name']) ? $row['company_name'] : 'غير محدد') ?></td>

                        <td><?= e(!empty($row['origin_country']) ? $row['origin_country'] : 'غير محدد') ?></td>

                        <td><?= e(!empty($row['shipment_type']) ? $row['shipment_type'] : 'غير محدد') ?></td>

                        <td><?= e(!empty($row['container_number']) ? $row['container_number'] : '—') ?></td>

                        <td>
                            <span class="route-badge route-<?= e($routeColor !== '' ? $routeColor : 'unknown') ?>">
                                <?= e($routeLabel) ?>
                            </span>
                        </td>

                        <td>
                            <?php if (isset($row['total_amount']) && $row['total_amount'] !== null): ?>
                                <?= number_format((float) $row['total_amount'], 2) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="status-badge <?= e($statusClass) ?>">
                                <?= e($statusLabel) ?>
                            </span>
                        </td>

                        <td><?= e($row['created_at'] ?? '') ?></td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>
        </table>
    </div>

</section>