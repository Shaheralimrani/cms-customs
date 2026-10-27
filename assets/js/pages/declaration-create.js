"use strict";

document.addEventListener("DOMContentLoaded", () => {
const companyInput = document.getElementById("company_name");
const dropdown = document.getElementById("autocomplete-dropdown");
const companyFields = document.getElementById("company-fields");
const licenseDisplay = document.getElementById("license_display");
const phoneDisplay = document.getElementById("phone_display");
const emailDisplay = document.getElementById("email_display");
const existingClientId = document.getElementById("existing_client_id");
const itemsContainer = document.getElementById("items-container");
const addItemButton = document.getElementById("add-item-button");

if (
    !companyInput ||
    !dropdown ||
    !companyFields ||
    !licenseDisplay ||
    !phoneDisplay ||
    !emailDisplay ||
    !existingClientId ||
    !itemsContainer
) {
    return;
}

let searchTimeout = null;
let activeRequest = null;
let requestNumber = 0;

function clearCompanyDetails() {
    licenseDisplay.value = "";
    phoneDisplay.value = "";
    emailDisplay.value = "";
    existingClientId.value = "";
}

function showDropdownMessage(message) {
    dropdown.replaceChildren();

    const messageElement = document.createElement("div");
    messageElement.className = "autocomplete-message";
    messageElement.textContent = message;

    dropdown.appendChild(messageElement);
    dropdown.classList.remove("d-none");
}

function addCompanyResult(company) {
    const item = document.createElement("button");
    item.type = "button";
    item.className = "autocomplete-item w-100 text-start";
    item.setAttribute("dir", "rtl");

    const companyName = document.createElement("strong");
    companyName.textContent = company.company_name || "";

    const details = document.createElement("small");
    const license = document.createElement("span");

    license.textContent =
        `الترخيص: ${company.license_number || "غير متوفر"}`;

    details.appendChild(license);

    if (company.phone) {
        const phone = document.createElement("div");
        phone.textContent = `الهاتف: ${company.phone}`;
        details.appendChild(phone);
    }

    if (company.email) {
        const email = document.createElement("div");
        email.textContent = `البريد: ${company.email}`;
        details.appendChild(email);
    }

    item.append(companyName, details);

    item.addEventListener("click", () => {
        selectCompany(company);
    });

    dropdown.appendChild(item);
}

function selectCompany(company) {
    companyInput.value = company.company_name || "";
    licenseDisplay.value = company.license_number || "";
    phoneDisplay.value = company.phone || "";
    emailDisplay.value = company.email || "";
    existingClientId.value = company.client_id || "";

    dropdown.classList.add("d-none");
}

async function searchCompanies(query, currentRequest) {
    if (activeRequest) {
        activeRequest.abort();
    }

    activeRequest = new AbortController();

    try {
        const response = await fetch(
            `Backend_controller/search_company.php?q=${encodeURIComponent(query)}`,
            {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                },
                signal: activeRequest.signal
            }
        );

        if (!response.ok) {
            throw new Error("Company search request failed.");
        }

        const data = await response.json();

        // تجاهل نتائج الطلبات القديمة.
        if (currentRequest !== requestNumber) {
            return;
        }

        const companies = Array.isArray(data.companies)
            ? data.companies
            : [];

        dropdown.replaceChildren();

        if (companies.length === 0) {
            showDropdownMessage(
                "لم نعثر على شركة مطابقة. يمكنك متابعة إدخال بيانات الشركة الجديدة."
            );
            return;
        }

        companies.forEach(addCompanyResult);
        dropdown.classList.remove("d-none");

    } catch (error) {
        if (error.name === "AbortError") {
            return;
        }

        if (currentRequest !== requestNumber) {
            return;
        }

        showDropdownMessage(
            "تعذر البحث عن الشركات. تحقق من الاتصال وحاول مرة أخرى."
        );

        console.error("Company search error:", error);
    }
}

companyInput.addEventListener("input", () => {
    clearTimeout(searchTimeout);

    requestNumber++;

    if (activeRequest) {
        activeRequest.abort();
        activeRequest = null;
    }

    clearCompanyDetails();

    const query = companyInput.value.trim();

    if (query.length < 2) {
        companyFields.classList.add("d-none");
        dropdown.classList.add("d-none");
        return;
    }

    companyFields.classList.remove("d-none");

    licenseDisplay.value = "جارٍ البحث...";
    showDropdownMessage("جارٍ البحث عن الشركات...");

    const currentRequest = requestNumber;

    searchTimeout = setTimeout(() => {
        searchCompanies(query, currentRequest);
    }, 300);
});

document.addEventListener("click", (event) => {
    if (
        !companyInput.contains(event.target) &&
        !dropdown.contains(event.target)
    ) {
        dropdown.classList.add("d-none");
    }
});

function createItemRow() {
    const row = document.createElement("div");
    row.className = "row g-2 item-row mb-2 align-items-end";

    const descriptionColumn = document.createElement("div");
    descriptionColumn.className = "col-12 col-md-5";

    const descriptionLabel = document.createElement("label");
    descriptionLabel.className = "form-label text-light small";
    descriptionLabel.textContent = "وصف الصنف";

    const descriptionInput = document.createElement("input");
    descriptionInput.type = "text";
    descriptionInput.name = "item_desc[]";
    descriptionInput.className = "form-control rounded-3";
    descriptionInput.placeholder = "مثال: هواتف ذكية";
    descriptionInput.required = true;

    descriptionColumn.append(descriptionLabel, descriptionInput);

    const quantityColumn = document.createElement("div");
    quantityColumn.className = "col-6 col-md-3";

    const quantityLabel = document.createElement("label");
    quantityLabel.className = "form-label text-light small";
    quantityLabel.textContent = "الكمية";

    const quantityInput = document.createElement("input");
    quantityInput.type = "number";
    quantityInput.name = "item_qty[]";
    quantityInput.className = "form-control rounded-3";
    quantityInput.min = "1";
    quantityInput.step = "1";
    quantityInput.placeholder = "1";
    quantityInput.required = true;

    quantityColumn.append(quantityLabel, quantityInput);

    const valueColumn = document.createElement("div");
    valueColumn.className = "col-6 col-md-4";

    const valueLabel = document.createElement("label");
    valueLabel.className = "form-label text-light small";
    valueLabel.textContent = "قيمة الصنف ($)";

    const valueGroup = document.createElement("div");
    valueGroup.className = "d-flex gap-2";

    const valueInput = document.createElement("input");
    valueInput.type = "number";
    valueInput.name = "item_val[]";
    valueInput.className = "form-control rounded-3";
    valueInput.min = "0";
    valueInput.step = "0.01";
    valueInput.placeholder = "0.00";
    valueInput.required = true;

    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.className = "btn btn-outline-danger rounded-3";
    removeButton.setAttribute("aria-label", "حذف الصنف");
    removeButton.innerHTML = '<i class="fa-solid fa-trash" aria-hidden="true"></i>';

    removeButton.addEventListener("click", () => {
        row.remove();
    });

    valueGroup.append(valueInput, removeButton);
    valueColumn.append(valueLabel, valueGroup);

    row.append(
        descriptionColumn,
        quantityColumn,
        valueColumn
    );

    return row;
}

if (addItemButton) {
    addItemButton.addEventListener("click", () => {
        itemsContainer.appendChild(createItemRow());
    });
}

});