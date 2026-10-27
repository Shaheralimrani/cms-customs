document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.issue-permit-btn');

    const declarationInput = document.getElementById(
        'permitDeclarationId'
    );

    const paymentInput = document.getElementById(
        'permitPaymentId'
    );

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            declarationInput.value =
                button.dataset.declarationId || '';

            paymentInput.value =
                button.dataset.paymentId || '';
        });
    });
});