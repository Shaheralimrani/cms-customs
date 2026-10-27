<div class="declaration-page">

    <section class="declaration-card">

        <div class="declaration-header">
            <div>
                <h1>إنشاء بيان جمركي جديد</h1>
                <p>أدخل البيانات المطلوبة لإنشاء البيان الجمركي.</p>
            </div>

            <div class="declaration-header-icon">
                <i class="fa-solid fa-file-circle-plus"></i>
            </div>
        </div>

        <?php if ($success !== null && is_array($success)): ?>
            <div class="message success" role="status">
                <h2>
                    <i class="fa-solid fa-circle-check"></i>
                    تم حفظ البيان بنجاح
                </h2>

                <p>
                    <strong>رقم البيان:</strong>
                    <?= h($success['declaration_id'] ?? '') ?>
                </p>

                <p>
                    <strong>رقم الترخيص:</strong>
                    <?= h($success['license_number'] ?? '') ?>
                </p>

                <p>
                    <strong>عدد الأصناف:</strong>
                    <?= (int) ($success['items_count'] ?? 0) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="message error" role="alert">
                <strong>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    يرجى معالجة الأخطاء التالية:
                </strong>

                <?php foreach ($errors as $error): ?>
                    <p><?= h($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form
            id="declarationForm"
            action="Backend_controller/process_declaration.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <!-- بيانات الشركة -->
            <section class="form-section">

                <div class="section-heading">
                    <span class="section-number">1</span>

                    <div>
                        <h2>بيانات الشركة</h2>
                        <p>بيانات الشركة صاحبة الشحنة.</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="field company-results">
                        <label for="company_name">
                            اسم الشركة <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            autocomplete="off"
                            required
                        >

                        <input
                            type="hidden"
                            id="existing_client_id"
                            name="existing_client_id"
                        >

                        <p class="hint">
                            إذا كانت الشركة مسجلة مسبقًا، اخترها من نتائج البحث.
                        </p>

                        <div
                            id="company_results"
                            class="company-results-list"
                            hidden
                        ></div>
                    </div>

                    <div class="field">
                        <label for="phone">رقم الهاتف</label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            autocomplete="tel"
                        >
                    </div>

                    <div class="field">
                        <label for="email">البريد الإلكتروني</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            autocomplete="email"
                        >
                    </div>

                </div>
            </section>

            <!-- بيانات الشحنة -->
            <section class="form-section">

                <div class="section-heading">
                    <span class="section-number">2</span>

                    <div>
                        <h2>بيانات الشحنة</h2>
                        <p>المعلومات الأساسية الخاصة بالشحنة.</p>
                    </div>
                </div>

                <div class="form-grid">

                    <div class="field">
                        <label for="shipment_type">
                            نوع الشحنة <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="shipment_type"
                            name="shipment_type"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="origin_country">
                            بلد المنشأ <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="origin_country"
                            name="origin_country"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="total_weight">
                            الوزن الإجمالي <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="total_weight"
                            name="total_weight"
                            min="0.001"
                            step="any"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="packages_count">
                            عدد الطرود <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="packages_count"
                            name="packages_count"
                            min="1"
                            step="1"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="declared_value">
                            القيمة المصرح بها <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="declared_value"
                            name="declared_value"
                            min="0"
                            step="any"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="port_of_entry">منفذ الدخول</label>

                        <input
                            type="text"
                            id="port_of_entry"
                            name="port_of_entry"
                            value="منفذ الشحن البري / البحري"
                        >
                    </div>

                    <div class="field">
                        <label for="container_number">رقم الحاوية</label>

                        <input
                            type="text"
                            id="container_number"
                            name="container_number"
                        >
                    </div>

                    <div class="field">
                        <label for="driver_name">اسم السائق</label>

                        <input
                            type="text"
                            id="driver_name"
                            name="driver_name"
                        >
                    </div>

                </div>
            </section>

            <!-- أصناف الشحنة -->
            <section class="form-section">

                <div class="section-heading">
                    <span class="section-number">3</span>

                    <div>
                        <h2>أصناف الشحنة</h2>
                        <p>أضف الأصناف الموجودة داخل الشحنة.</p>
                    </div>
                </div>

                <div id="items_container">

                    <div class="item-row">

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

                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-secondary"
                    id="add_item"
                >
                    <i class="fa-solid fa-plus"></i>
                    إضافة صنف
                </button>

            </section>

            <!-- المستندات -->
            <section class="form-section">

                <div class="section-heading">
                    <span class="section-number">4</span>

                    <div>
                        <h2>المستندات</h2>
                        <p>أرفق المستندات المتوفرة الخاصة بالشحنة.</p>
                    </div>
                </div>

                <div class="upload-notice">
                    <i class="fa-solid fa-circle-info"></i>

                    الملفات اختيارية. الأنواع المسموحة PDF وJPG وJPEG وPNG،
                    بحد أقصى 5 ميجابايت لكل ملف.
                </div>

                <div class="form-grid">

                    <div class="field">
                        <label for="invoice_file">الفاتورة</label>

                        <input
                            type="file"
                            id="invoice_file"
                            name="invoice_file"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                    </div>

                    <div class="field">
                        <label for="origin_cert_file">شهادة المنشأ</label>

                        <input
                            type="file"
                            id="origin_cert_file"
                            name="origin_cert_file"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                    </div>

                    <div class="field">
                        <label for="health_cert_file">الشهادة الصحية</label>

                        <input
                            type="file"
                            id="health_cert_file"
                            name="health_cert_file"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                    </div>

                </div>
            </section>

            <!-- أزرار النموذج -->
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    حفظ البيان
                </button>

                <a href="dashboard.php" class="btn btn-light">
                    إلغاء
                </a>

            </div>

        </form>

    </section>

</div>