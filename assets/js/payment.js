'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const paymentModal = document.getElementById('paymentModal');

    if (!paymentModal) {
        return;
    }

    const paymentForm = document.getElementById('paymentForm');
    const paymentMethod = document.getElementById('paymentMethod');

    const bankDetailsFields = document.getElementById(
        'bankDetailsFields'
    );

    const bankNameLabel = document.getElementById(
        'bankNameLabel'
    );

    const referenceLabel = document.getElementById(
        'referenceLabel'
    );

    const bankNameInput = document.getElementById('bankName');

    const referenceInput = document.getElementById(
        'referenceNumber'
    );

    const amountPaid = document.getElementById('amountPaid');

    const submitButton = document.getElementById('submitPayment');

    function resetBankFields() {
        bankDetailsFields.classList.add('d-none');

        bankNameInput.required = false;
        referenceInput.required = false;

        bankNameInput.value = '';
        referenceInput.value = '';
    }

    paymentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const invoiceId = button.getAttribute('data-invoice-id');
        const declarationId = button.getAttribute(
            'data-declaration-id'
        );

        const company = button.getAttribute('data-company');

        const total = Number(
            button.getAttribute('data-total')
        );

        document.getElementById('modalInvoiceId').value =
            '#' + invoiceId;

        document.getElementById('invoiceId').value = invoiceId;

        document.getElementById('modalDeclarationId').value =
            declarationId;

        document.getElementById('modalCompany').value = company;

        document.getElementById('modalTotal').value =
            '$ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        amountPaid.value = total.toFixed(2);

        paymentMethod.value = '';

        resetBankFields();

        if (submitButton) {
            submitButton.disabled = false;
        }
    });

    paymentMethod.addEventListener('change', function () {
        const method = this.value;

        resetBankFields();

        if (method === 'bank_transfer') {
            bankDetailsFields.classList.remove('d-none');

            bankNameLabel.textContent =
                'اسم البنك المحول إليه';

            referenceLabel.textContent =
                'رقم الحوالة المرجعي (إلزامي)';

            bankNameInput.required = true;
            referenceInput.required = true;
        }

        if (method === 'cheque') {
            bankDetailsFields.classList.remove('d-none');

            bankNameLabel.textContent =
                'اسم البنك المسحوب عليه الشيك';

            referenceLabel.textContent =
                'رقم الشيك (إلزامي)';

            bankNameInput.required = true;
            referenceInput.required = true;
        }
    });

    /*
     * منع النقر المتكرر على زر الدفع أثناء إرسال النموذج.
     * الحماية الأساسية من تكرار الدفع يجب أن تكون في الخادم وقاعدة البيانات.
     */
    paymentForm.addEventListener('submit', function () {
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'جارٍ تسجيل الدفع...';
        }
    });
});