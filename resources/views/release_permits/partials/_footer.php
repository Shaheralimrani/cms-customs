<!-- تذييل الصفحة -->

<footer class="footer">

    <p>
        رقم الإذن:
        <span class="ltr">
            <?= permitEscape($permitNumber) ?>
        </span>
    </p>

    <p>
        رقم البيان:
        <span class="ltr">
            <?= permitEscape($declarationId) ?>
        </span>
    </p>

    <p>
        تم إنشاء هذا المستند من نظام إدارة البيانات الجمركية.
    </p>

    <p>
        تاريخ الطباعة:
        <?= date('Y-m-d H:i') ?>
    </p>

</footer>