document.addEventListener('DOMContentLoaded', function () {
    /*
     * إضافة أصناف الشحنة وحذفها.
     */

    const itemsContainer = document.getElementById('items_container');
    const addItemButton = document.getElementById('add_item');

    if (itemsContainer && addItemButton) {
        addItemButton.addEventListener('click', function () {
            const row = document.createElement('div');

            row.className = 'item-row';

            row.innerHTML = `
                <div class="field">
                    <label>
                        وصف الصنف <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="item_desc[]"
                        required
                    >
                </div>

                <div class="field">
                    <label>
                        الكمية <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        name="item_qty[]"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <div class="field">
                    <label>
                        القيمة <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        name="item_val[]"
                        min="0"
                        step="any"
                        required
                    >
                </div>

                <button
                    type="button"
                    class="btn btn-danger remove-item"
                >
                    <i class="fa-solid fa-trash"></i>
                    حذف
                </button>
            `;

            itemsContainer.appendChild(row);
        });

        itemsContainer.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-item');

            if (!button) {
                return;
            }

            const rows = itemsContainer.querySelectorAll('.item-row');

            if (rows.length <= 1) {
                alert('يجب الاحتفاظ بصنف واحد على الأقل.');
                return;
            }

            button.closest('.item-row').remove();
        });
    }

    /*
     * البحث عن الشركات المسجلة.
     */

    const companyInput = document.getElementById('company_name');
    const clientIdInput = document.getElementById('existing_client_id');
    const resultsContainer = document.getElementById('company_results');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');

    // إذا لم تكن عناصر البحث موجودة، لا نكمل هذا الجزء.
    if (
        !companyInput ||
        !clientIdInput ||
        !resultsContainer ||
        !phoneInput ||
        !emailInput
    ) {
        return;
    }

    let searchTimer = null;
    let activeRequest = null;

    companyInput.addEventListener('input', function () {
        // عند تعديل الاسم، يجب إعادة اختيار الشركة من نتائج البحث.
        clientIdInput.value = '';

        resultsContainer.replaceChildren();
        resultsContainer.hidden = true;

        clearTimeout(searchTimer);

        const query = companyInput.value.trim();

        // لا نبحث قبل إدخال حرفين على الأقل.
        if (query.length < 2) {
            return;
        }

        searchTimer = setTimeout(async function () {
            if (activeRequest) {
                activeRequest.abort();
            }

            activeRequest = new AbortController();

            try {
                const response = await fetch(
                    'Backend_controller/search_company.php?q='
                        + encodeURIComponent(query),
                    {
                        signal: activeRequest.signal,
                        headers: {
                            Accept: 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    return;
                }

                const companies = await response.json();

                if (!Array.isArray(companies)) {
                    return;
                }

                resultsContainer.replaceChildren();

                companies.forEach(function (company) {
                    const option = document.createElement('div');

                    option.className = 'company-result';

                    // textContent يمنع تفسير اسم الشركة على أنه HTML.
                    option.textContent = company.company_name || '';

                    option.addEventListener('click', function () {
                        companyInput.value = company.company_name || '';
                        clientIdInput.value = company.client_id || '';
                        phoneInput.value = company.phone || '';
                        emailInput.value = company.email || '';

                        resultsContainer.replaceChildren();
                        resultsContainer.hidden = true;
                    });

                    resultsContainer.appendChild(option);
                });

                resultsContainer.hidden =
                    resultsContainer.childElementCount === 0;

            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Company search failed.');
                }
            }
        }, 250);
    });

    // إغلاق نتائج البحث عند النقر خارج حقل الشركة.
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.company-results')) {
            resultsContainer.hidden = true;
        }
    });
});