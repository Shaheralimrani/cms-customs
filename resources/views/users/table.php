<div class="card">

    <div class="card-header d-flex justify-content-between
                align-items-center flex-wrap gap-2">

        <span>المستخدمون المسجلون</span>

        <span class="badge bg-primary">
            العدد: <?= count($users) ?>
        </span>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead>
                    <tr>
                        <th>المعرف</th>
                        <th>الاسم الكامل</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($users)): ?>

                    <tr>
                        <td colspan="7" class="text-center">
                            لا يوجد مستخدمون مسجلون.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr>
                            <td><?= (int) $user['user_id'] ?></td>

                            <td>
                                <?= userManagementEscape($user['full_name']) ?>
                            </td>

                            <td>
                                <?= userManagementEscape($user['username']) ?>
                            </td>

                            <td>
                                <?= userManagementEscape($user['email']) ?>
                            </td>

                            <td>
                                <span class="role-badge">
                                    <?= userManagementEscape(
                                        $roles[$user['role']] ?? $user['role']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= userManagementEscape(
                                    $user['created_at'] ?? ''
                                ) ?>
                            </td>

                            <td>
                                <div class="action-buttons">

                                    <a
                                        href="auth_users.php?edit=<?= (int) $user['user_id'] ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        تعديل
                                    </a>

                                    <?php if (
                                        (int) $user['user_id']
                                        !== (int) $_SESSION['user_id']
                                    ): ?>

                                       <form action="Backend_controller/user_management_controller.php" method="POST"
      onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?= (int) $user['user_id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                حذف
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        <button
                                            class="btn btn-sm btn-secondary"
                                            disabled
                                        >
                                            حسابك الحالي
                                        </button>

                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>