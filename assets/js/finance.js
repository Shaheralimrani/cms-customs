(function () {
    'use strict';

    const financeModal = document.getElementById('financeModal');
    const hsCodeSelect = document.getElementById('hsCode');
    const financeForm = document.getElementById('financeForm');
    const submitButton = document.getElementById('submitFinance');

    financeModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const declarationId =
            button.getAttribute('data-declaration-id') || '';

        const declaredValue =
            Number(button.getAttribute('data-declared-value')) || 0;

        document.getElementById('modalDeclarationId').value =
            declarationId;

        document.getElementById('declarationId').value =
            declarationId;

        document.getElementById('modalDeclaredValue').value =
            declaredValue.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // إعادة اختيار البند من البداية عند فتح بيان جديد.
        hsCodeSelect.selectedIndex = 0;

        calculateFinance();
    });

    hsCodeSelect.addEventListener('change', calculateFinance);

    function calculateFinance() {
        const option = hsCodeSelect.options[hsCodeSelect.selectedIndex];

        const customsRate =
            Number(option?.dataset.customsRate) || 0;

        const taxRate =
            Number(option?.dataset.taxRate) || 0;

        const valueText = document
            .getElementById('modalDeclaredValue')
            .value
            .replace(/,/g, '');

        const declaredValue = Number(valueText) || 0;

        const duty = Math.round(
            (declaredValue * customsRate / 100 + Number.EPSILON) * 100
        ) / 100;

        const tax = Math.round(
            (declaredValue * taxRate / 100 + Number.EPSILON) * 100
        ) / 100;

        const total = Math.round(
            (duty + tax + Number.EPSILON) * 100
        ) / 100;

        document.getElementById('customsRate').value =
            customsRate + ' %';

        document.getElementById('taxRate').value =
            taxRate + ' %';

        document.getElementById('dutyAmount').textContent =
            duty.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('taxAmount').textContent =
            tax.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        document.getElementById('totalAmount').textContent =
            total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    }

    // منع الضغط المتكرر على زر الإصدار أثناء إرسال الطلب.
    financeForm.addEventListener('submit', function (event) {
        if (!financeForm.checkValidity()) {
            event.preventDefault();
            financeForm.reportValidity();
            return;
        }

        submitButton.disabled = true;

        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ إصدار الفاتورة...';
    });

})();