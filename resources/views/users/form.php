<div class="card">

    <div class="card-header">
        <?= $editUser
            ? 'تعديل بيانات المستخدم'
            : 'إضافة مستخدم جديد'
        ?>
    </div>

    <div class="card-body">

      <form action="Backend_controller/user_management_controller.php" method="POST">

            <input
                type="hidden"
                name="action"
                value="<?= $editUser ? 'update' : 'add' ?>"
            >

            <?php if ($editUser): ?>
                <input
                    type="hidden"
                    name="user_id"
                    value="<?= (int) $editUser['user_id'] ?>"
                >
            <?php endif; ?>

            <div class="row g-3">

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">الاسم الكامل</label>

                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        maxlength="100"
                        required
                        value="<?= userManagementEscape(
                            $editUser['full_name'] ?? ''
                        ) ?>"
                    >
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">اسم المستخدم</label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        maxlength="50"
                        required
                        value="<?= userManagementEscape(
                            $editUser['username'] ?? ''
                        ) ?>"
                    >
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">البريد الإلكتروني</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        maxlength="100"
                        required
                        value="<?= userManagementEscape(
                            $editUser['email'] ?? ''
                        ) ?>"
                    >
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">
                        كلمة المرور

                        <?php if ($editUser): ?>
                            <small class="text-muted">
                                (اختياري عند التعديل)
                            </small>
                        <?php endif; ?>
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                        <?= $editUser ? '' : 'required' ?>
                    >

                    <?php if ($editUser): ?>
                        <small class="text-muted">
                            اترك الحقل فارغًا للاحتفاظ بكلمة المرور الحالية.
                        </small>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label">الدور الوظيفي</label>

                    <select
                        name="role"
                        class="form-select"
                        required
                    >
                        <option value="">اختر الدور</option>

                        <?php foreach ($roles as $roleKey => $roleName): ?>
                            <option
                                value="<?= userManagementEscape($roleKey) ?>"
                                <?= (
                                    ($editUser['role'] ?? '') === $roleKey
                                ) ? 'selected' : '' ?>
                            >
                                <?= userManagementEscape($roleName) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">

                    <button type="submit" class="btn btn-primary">
                        <?= $editUser
                            ? 'حفظ التعديلات'
                            : 'إضافة المستخدم'
                        ?>
                    </button>

                    <?php if ($editUser): ?>
                        <a
                            href="auth_users.php"
                            class="btn btn-secondary"
                        >
                            إلغاء التعديل
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </form>

    </div>

</div>