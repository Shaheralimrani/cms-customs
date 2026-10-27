document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('inspectionModal');

    if (!modal) {
        return;
    }

    const declarationIdInput =
        document.getElementById('declaration_id');

    const routeColorInput =
        document.getElementById('route_color');

    const notesInput =
        document.getElementById('inspection_notes');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const declarationId =
            button.getAttribute('data-declaration-id');

        declarationIdInput.value = declarationId || '';

        routeColorInput.value = '';
        notesInput.value = '';
    });

    // منع إرسال النموذج إذا لم يُحدد مسار التفتيش.
    const form = modal.querySelector('form');

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!routeColorInput.value) {
                event.preventDefault();

                routeColorInput.focus();

                alert('يرجى اختيار مسار المعاينة.');
            }
        });
    }
});