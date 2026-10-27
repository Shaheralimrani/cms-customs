<div class="user-management">

    <?php if ($success !== ''): ?>
        <div class="alert alert-success" role="alert">
            <?= userManagementEscape($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= userManagementEscape($error) ?>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/form.php'; ?>

    <?php require __DIR__ . '/table.php'; ?>

</div>