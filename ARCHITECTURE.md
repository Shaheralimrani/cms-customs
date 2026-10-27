ARCHITECTURE.md — بنية نظام إدارة الجمارك

1. Project Overview

نظام إدارة جمارك مبني باستخدام Plain PHP لإدارة سير المعاملات الجمركية، بدءًا من بيانات البيان والمستورد، مرورًا بالمعاينة والفواتير والتحصيل، وصولًا إلى تصاريح الإفراج. يتضمن النظام واجهات لإدارة المستخدمين ولوحة التحكم، وتختلف العمليات المتاحة حسب دور المستخدم.

يفصل المشروع بين استقبال طلبات HTTP، وتنسيق عمليات التطبيق، والخدمات، والوصول إلى البيانات، وعرض الواجهات. يوضح هذا الملف بنية المشروع ومسؤوليات طبقاته واتجاهات الاعتماد بينها.

Tech Stack

التقنية| الاستخدام
PHP 8.0+| منطق الخادم ومعالجة طلبات HTTP
MySQL / MariaDB| تخزين بيانات النظام
PDO| الاتصال بقاعدة البيانات وتنفيذ الاستعلامات في Models
Vanilla JavaScript| التفاعلات في المتصفح وطلبات AJAX/fetch
Vanilla CSS| تنسيق الواجهات
HTML| بناء صفحات العرض والنماذج
XAMPP| بيئة تطوير محلية لتشغيل Apache وPHP وMySQL
Composer| لا يعتمد المشروع على Composer autoload
PHP Namespaces| غير مستخدمة حاليًا

---

2. Directory Structure

تمثل الشجرة التالية بنية المشروع الحالية. توضح التعليقات المجلدات المخصصة التي لا تحتوي ملفات تنفيذية.

CMS/
├── .env.example
├── .gitignore
├── README.md
├── ARCHITECTURE.md
├── LICENSE
├── composer.json
├── auth_users.php
├── dashboard.php
├── declaration_new.php
├── finance.php
├── inspection.php
├── inspection_approvals.php
├── login.php
├── logout.php
├── payment.php
├── print_release_permit.php
└── release_permits.php
│
├── config/
│   ├── config.php
│   └── env_loader.php
│
├── Backend_controller/
│   ├── auth_controller.php
│   ├── process_declaration.php
│   ├── process_finance.php
│   ├── process_inspection.php
│   ├── process_inspection_approval.php
│   ├── process_payment.php
│   ├── process_release_permit.php
│   ├── search_company.php
│   └── user_management_controller.php
│
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── DeclarationController.php
│   │   ├── FinanceController.php
│   │   ├── InspectionApprovalController.php
│   │   ├── InspectionController.php
│   │   ├── PaymentController.php
│   │   ├── ReleasePermitController.php
│   │   └── UserManagementController.php
│   ├── Helpers/
│   │   ├── AuthValidator.php
│   │   ├── DeclarationNumberGenerator.php
│   │   ├── DeclarationValidator.php
│   │   ├── FileUploader.php
│   │   ├── FinanceValidator.php
│   │   ├── InspectionValidator.php
│   │   ├── LicenseNumberGenerator.php
│   │   ├── PaymentValidator.php
│   │   ├── ReleasePermitPrintHelper.php
│   │   ├── ReleasePermitValidator.php
│   │   ├── UserManagementHelper.php
│   │   ├── UserManagementValidator.php
│   │   └── dashboard_view_helpers.php
│   ├── Models/
│   │   ├── Dashboard.php
│   │   ├── Declaration.php
│   │   ├── Finance.php
│   │   ├── ImporterBroker.php
│   │   ├── Inspection.php
│   │   ├── InspectionApproval.php
│   │   ├── Payment.php
│   │   ├── ReleasePermit.php
│   │   ├── User.php
│   │   └── UserManagement.php
│   └── Services/
│       ├── AuthService.php
│       ├── DashboardService.php
│       ├── DeclarationService.php
│       ├── FinanceService.php
│       ├── InspectionApprovalService.php
│       ├── InspectionService.php
│       ├── PaymentService.php
│       ├── ReleasePermitService.php
│       └── UserManagementService.php
│
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.php
│       ├── declarations/
│       │   └── create.php
│       ├── dashboard/
│       │   ├── index.php
│       │   └── partials/
│       │       ├── welcome.php
│       │       ├── recent_declarations.php
│       │       ├── footer.php
│       │       ├── stats/
│       │       │   ├── manager.php
│       │       │   ├── inspector.php
│       │       │   └── accountant.php
│       │       └── quick_actions/
│       │           ├── inspector.php
│       │           ├── accountant.php
│       │           └── broker.php
│       ├── finance/
│       │   └── index.php
│       ├── inspections/
│       │   ├── index.php
│       │   └── pending_approvals.php
│       ├── layouts/
│       │   ├── app.php
│       │   └── dashboard.php
│       ├── partials/
│       │   ├── footer.php
│       │   └── navbar.php
│       ├── payments/
│       │   └── index.php
│       ├── release_permits/
│       │   ├── index.php
│       │   ├── print.php
│       │   └── partials/
│       │       ├── _decision.php
│       │       ├── _declaration.php
│       │       ├── _financial.php
│       │       ├── _footer.php
│       │       ├── _importer.php
│       │       ├── _inspection.php
│       │       ├── _permit_header.php
│       │       ├── _signatures.php
│       │       └── _toolbar.php
│       └── users/
│           ├── form.php
│           ├── index.php
│           └── table.php
│
├── includes/
│   └── dashboard_sidebar.php
│
├── assets/
│   ├── css/
│   │   ├── pages/
│   │   │   ├── declaration-create.css
│   │   │   ├── release-permit-print.css
│   │   │   └── user-management.css
│   │   ├── dashboard.css
│   │   ├── declaration.css
│   │   ├── inspections.css
│   │   ├── login.css
│   │   ├── public.css
│   │   └── style.css
│   └── js/
│       ├── pages/
│       │   └── declaration-create.js
│       ├── dashboard.js
│       ├── declaration.js
│       ├── finance.js
│       ├── inspections.js
│       ├── payment.js
│       └── release_permits.js
│
├── database/
│   └── seeds/
│       ├── .gitkeep
│       └── README.md
│
├── storage/
│   └── logs/
│       └── .gitkeep
│
├── tests/
│   ├── .gitkeep
│   └── README.md
│
└── uploads/
    └── declarations/

مسؤولية كل مجلد

المجلد| المسؤولية
"app/"| كود التطبيق المنظم إلى Controllers وServices وModels وHelpers
"app/Controllers/"| تنسيق حالات الاستخدام وربط التحقق بالخدمات
"app/Services/"| تنفيذ قواعد العمل وتنسيق عمليات المجال
"app/Models/"| استعلامات قاعدة البيانات وعمليات القراءة والكتابة
"app/Helpers/"| التحقق وتوليد الأرقام ورفع الملفات والمهام المساعدة
"Backend_controller/"| استقبال طلبات HTTP والتحقق من الطلب وإعداد الاستجابة أو إعادة التوجيه
"resources/views/"| قوالب HTML/PHP لعرض الصفحات والنماذج والمخرجات
"resources/views/partials/" وما شابه| أجزاء عرض يعاد استخدامها داخل صفحات أكبر
"assets/"| ملفات الواجهة الثابتة، مثل CSS وJavaScript
"config/"| إعداد الاتصال وتهيئة التطبيق وتحميل متغيرات البيئة
"includes/"| أجزاء PHP مشتركة للواجهة، مثل الشريط الجانبي
"database/seeds/"| مساحة مخصصة لملفات البيانات الأولية، وتتضمن ".gitkeep" و"README.md"
"tests/"| مساحة مخصصة للاختبارات، وتتضمن ".gitkeep" و"README.md"
"storage/logs/"| مساحة مخصصة لسجلات التشغيل، وتتضمن ".gitkeep"
"uploads/"| الملفات المرفوعة، ومنها مستندات البيانات الجمركية
جذر المشروع| صفحات الدخول ونقاط الدخول الرئيسية، مثل "dashboard.php" و"payment.php"
".env.example"| نموذج لمتغيرات البيئة المطلوبة
"composer.json"| بيانات إعداد Composer؛ التحميل التلقائي غير مستخدم حاليًا

---

3. The Two-Layer Request Model

يوجد في المشروع مستويان لمعالجة الطلبات، لكل منهما مسؤولية واضحة.

3.1 "Backend_controller/" — HTTP Request Handlers

هذه الملفات هي نقاط معالجة طلبات HTTP. تستقبل عادةً طلبات "POST" أو "GET" من النماذج أو JavaScript، وتتعامل مع تفاصيل HTTP، مثل:

- بدء الجلسة عند الحاجة باستخدام "session_start()".
- التحقق من تسجيل الدخول والدور والصلاحيات.
- التحقق من رمز CSRF.
- قراءة "$_POST" أو "$_GET" وبيانات الطلب.
- استدعاء Controller من "app/Controllers/".
- وضع رسائل النجاح أو الخطأ في Session.
- إعادة التوجيه باستخدام "header('Location: ...')" أو إرسال استجابة JSON.

تستخدم هذه الملفات غالبًا "snake_case"، مثل "process_payment.php"، لأنها ملفات PHP إجرائية وليست أسماء Classes.

من يستدعيها؟ نماذج HTML من "resources/views/" أو صفحات الجذر، وقد تستدعيها ملفات JavaScript عبر "fetch" أو AJAX.

حدود المسؤولية: لا تحتوي هذه الملفات على قواعد عمل؛ فتلك مسؤولية Controllers وServices. كما لا تنفذ استعلامات SQL مباشرة؛ فالوصول إلى البيانات مسؤولية Models.

3.2 "app/Controllers/" — Business Orchestration

هذه الملفات Classes مثل "PaymentController" و"InspectionController". تنسق حالة الاستخدام، وتقوم عادةً بما يلي:

- استدعاء Validator من "app/Helpers/" للتحقق من صحة بيانات المجال.
- استدعاء Service لتنفيذ العملية.
- تحويل نتيجة التنفيذ إلى نتيجة يمكن للـ Handler التعامل معها.
- تمرير الاعتماديات، مثل كائن "PDO"، إلى الطبقات التي تحتاجها.

تستخدم أسماء Classes نمط "PascalCase"، مثل "PaymentController".

لا تتعامل هذه الطبقة مباشرةً مع تفاصيل HTTP مثل "$_POST" أو "header()" أو إعادة توجيه المتصفح. تستدعي ملفات "Backend_controller/" هذه الـ Controllers، بينما تستدعي بعض صفحات الجذر Controllers مباشرةً في حالات محددة.

3.3 لماذا الفصل ضروري؟

المبدأ هو Separation of Concerns: استقبال الطلب ليس هو نفسه تنفيذ قاعدة العمل، وكلاهما ليس هو نفسه الوصول إلى قاعدة البيانات.

- تغيير طريقة استقبال الطلب لا يتطلب إعادة كتابة قواعد الدفع أو المعاينة.
- اختبار Service أسهل عندما لا يعتمد على "$_POST" أو جلسة المتصفح.
- يمكن إضافة واجهة REST API مستقبلًا لتستدعي Services نفسها، مع إنشاء طبقة HTTP/API منفصلة بدل نسخ قواعد العمل.
- لا توجد REST API مستقلة ضمن البنية الحالية؛ إنها إمكانية توسع مستقبلية.

---

4. Full Request Flow

يوضح المثال التالي التدفق المعماري المعتاد لعملية دفع من النموذج إلى قاعدة البيانات.

المستخدم
   |
   v
resources/views/payments/index.php
   |
   | POST (form action)
   v
Backend_controller/process_payment.php
   |
   |-- Session / Role / CSRF / Request Checks
   |
   v
app/Controllers/PaymentController.php
   |
   |-- app/Helpers/PaymentValidator.php
   |
   v
app/Services/PaymentService.php
   |
   |-- قواعد الدفع + Transaction
   |
   v
app/Models/Payment.php
   |
   |-- PDO / Prepared Statements / SQL
   v
MySQL / MariaDB
   |
   v
نتيجة العملية تعود عبر الطبقات
   |
   v
Backend_controller/process_payment.php
   |
   |-- Session Message + HTTP Redirect
   v
payment.php
   |
   v
resources/views/payments/index.php

مسؤولية كل مرحلة

1. View: يعرض النموذج ويجمع مدخلات المستخدم.
2. HTTP Handler: يفحص الجلسة والصلاحيات وCSRF، ثم يوجه العملية.
3. Controller: ينسق التحقق وتنفيذ حالة الاستخدام.
4. Helper/Validator: يتحقق من شكل البيانات وصحتها قبل تنفيذ العملية.
5. Service: يطبق تسلسل قواعد العمل، مثل التحقق من حالة الفاتورة ومنع السداد المكرر.
6. Model: ينفذ استعلامات قاعدة البيانات باستخدام PDO.
7. HTTP Handler: يترجم النتيجة إلى رسالة أو إعادة توجيه مناسبة للمتصفح.

---

5. Layer Responsibilities

Layer| Folder| Responsibility| Knows HTTP?| Knows DB?
HTTP| "Backend_controller/"| استقبال الطلب، وفحوص الجلسة والصلاحيات وCSRF، والاستجابة والتوجيه| نعم| لا ينفذ SQL مباشرة
Entry Pages| جذر المشروع، مثل "payment.php"| نقاط دخول للصفحات وتجهيز بعض البيانات وعرض الواجهة| نعم| قد يحمّل Config أو Model
Business Orchestration| "app/Controllers/"| تنسيق Validator وService وإرجاع نتيجة العملية| لا| عبر Services، دون SQL مباشر
Business Logic| "app/Services/"| قواعد العمل وتنسيق عمليات المجال| لا| عبر Models وPDO
Data Access| "app/Models/"| قراءة وكتابة البيانات وتنفيذ SQL| لا| نعم
Validation / Helpers| "app/Helpers/"| التحقق من المدخلات ومهام مساعدة مثل توليد المعرفات ورفع الملفات| لا يتعامل مع HTTP مباشرة| لا ينفذ SQL عادةً
Presentation| "resources/views/"| HTML/PHP للعرض والنماذج وأجزاء الواجهة| جزئيًا عبر form/actions| لا ينفذ SQL
Shared UI| "includes/" و"resources/views/partials/"| أجزاء عرض مشتركة لتجنب تكرار HTML| جزئيًا| لا ينفذ SQL
Configuration| "config/"| إعداد PDO وتعريف الإعدادات العامة| ليس طبقة HTTP| نعم، يهيئ اتصال قاعدة البيانات
Static Frontend| "assets/"| CSS وJavaScript وملفات الواجهة الثابتة| JavaScript يتعامل مع HTTP| لا يتصل بقاعدة البيانات مباشرة

"config/" — الاتصال والإعدادات

- "config/config.php" يضبط اتصال قاعدة البيانات وينشئ كائن PDO في المتغير "$pdo"، ويحدد خيارات مثل "PDO::ATTR_ERRMODE" و"PDO::ATTR_DEFAULT_FETCH_MODE".
- يحتوي أيضًا على الدالتين العامتين "e()" للترميز الآمن للنصوص عند عرضها في HTML، و"hasAccess()" لفحص الدور بناءً على Session.
- "config/env_loader.php" مسؤول عن تحميل متغيرات البيئة. يجب تحميل إعدادات البيئة قبل استخدام القيم التي تعتمد عليها "config.php".
- احتفظ ببيانات اعتماد الإنتاج في إعدادات البيئة المناسبة، ولا تضعها في المستودع.

"app/Helpers/" — الفرق عن Models

- Helper: يساعد في التحقق أو تنفيذ مهمة مساندة، مثل "PaymentValidator.php" أو "DeclarationNumberGenerator.php".
- Model: يمثل طبقة الوصول إلى البيانات ويحتوي عمليات SQL المرتبطة بكيان أو مجموعة بيانات، مثل "Payment.php".
- تبقى استعلامات SQL في Models؛ لا تنقلها إلى Helpers لمجرد أن العملية صغيرة.

"app/Services/" — الفرق عن Controllers

- Controller: ينسق الخطوات ويحدد أي Validator أو Service يستدعي.
- Service: ينفذ سير العمل وقواعد المجال، مثل التحقق من حالة الفاتورة ومنع الدفع المكرر وإدارة Transaction.
- Model: ينفذ الاستعلامات والتغييرات على السجلات.
- حافظ على هذه الحدود عند إضافة الميزات أو تعديلها.

"includes/" — الأجزاء المشتركة

يحتوي "includes/dashboard_sidebar.php" على جزء واجهة مشترك يمكن تضمينه في أكثر من صفحة لتقليل تكرار HTML. توجد أيضًا أجزاء مشتركة في "resources/views/partials/" و"resources/views/layouts/". عند تعديل جزء مشترك، راجع الصفحات التي تستخدمه لتجنب التأثير غير المقصود على الواجهات الأخرى.

"resources/views/" — علاقتها بالـ Controllers

تستقبل Views البيانات التي جهزتها صفحة الدخول أو طبقة التطبيق، ثم تعرض HTML والنماذج. يمكن للنموذج إرسال طلب إلى Handler، ويمكن لملف JavaScript إرسال طلب إلى عنوان محدد. لا تضع قواعد العمل أو استعلامات SQL داخل Views. تتضمن بعض Views عمليات "require" لتحميل partials أو Helpers؛ راجع طريقة استخدامها قبل نقلها أو حذفها.

---

6. Conventions

النمط| الاستخدام في المشروع| مثال
"snake_case"| ملفات PHP الإجرائية ومعالجات الطلبات| "process_payment.php"
"PascalCase"| أسماء Classes| "PaymentController"، "PaymentService"
"camelCase"| أسماء Methods والمتغيرات وفق أسلوب PHP المعتاد| "processPayment()"
أسماء مجلدات واضحة| تجميع الملفات حسب المسؤولية| "Controllers/"، "Models/"، "Services/"
"require_once"| تحميل ملفات PHP يدويًا ومنع تضمين الملف نفسه أكثر من مرة في الطلب| "require_once __DIR__ . '/../config/config.php';"

لا يستخدم المشروع Namespaces أو Composer autoload حاليًا؛ لذلك يعتمد تحميل الأصناف على مسارات "require_once" الصريحة. عند إضافة اعتماد جديد، حدّد مسار التحميل وترتيبه قبل استخدام الصنف. وجود "composer.json" لا يعني أن Composer autoload مفعّل.

---

7. Adding a New Feature — خمس خطوات

1. "resources/views/" — الواجهة: أنشئ أو عدّل صفحة العرض والنموذج، وحدد "action" وطريقة HTTP المناسبة.
2. "Backend_controller/" — Handler: أضف ملف معالجة يستقبل الطلب ويتحقق من الجلسة والصلاحيات وCSRF ويتعامل مع الاستجابة.
3. "app/Controllers/" — التنسيق: أنشئ أو حدّث Controller ينسق التحقق وتنفيذ حالة الاستخدام.
4. "app/Services/" — منطق العمل: ضع قواعد المجال وتسلسل العملية هنا، بما في ذلك Transaction عند الحاجة.
5. "app/Models/" — البيانات: أضف استعلامات SQL المطلوبة باستخدام PDO وPrepared Statements.

قبل اعتبار الميزة مكتملة، حدّث JavaScript الذي يحتاج إلى endpoint، وتحقق من رسائل الخطأ والصلاحيات، واختبر المسارات الناجحة والفاشلة، وراجع الملفات التي تعتمد على الأصناف الجديدة. لا تضع SQL أو قواعد العمل داخل View.

---

8. Known Trade-offs

- Plain PHP: خيار خفيف ومناسب للتعلم والمشاريع التشغيلية، ويتطلب انضباطًا في تنظيم الطبقات ومعالجة الأخطاء والأمان والاختبارات.
- لا Composer autoload: التحميل يدوي حاليًا عبر "require_once"؛ لذلك من المهم توثيق الاعتماديات ومسارات الملفات.
- لا Namespaces: أسماء الأصناف بسيطة، لكن احتمال تصادم أسماء Classes أو Functions يزيد مع توسع المشروع.
- ملفات دخول في الجذر: تجعل الوصول المباشر للصفحات واضحًا، لكنها قد تؤدي إلى تكرار تهيئة الجلسة أو الاتصال إذا لم تُوحّد نقاط التهيئة.
- القرارات تعكس نطاق المشروع الحالي: يمكن مراجعة هذه الاختيارات عند التوسع أو النشر العام.

---

9. Future Improvements — Roadmap Only

هذه مقترحات مستقبلية، وليست تغييرات منفذة:

1. إعادة تسمية "Backend_controller/" إلى "handlers/": اسم أقصر يوضح أن المجلد يحتوي HTTP handlers. يتطلب ذلك تحديث جميع مراجع المسارات في HTML وJavaScript وPHP.
2. Composer PSR-4 اختياريًا: اعتماده عند الحاجة إلى autoload منظم، بعد وضع خطة انتقال واختبارات.
3. Unit Tests: إضافة اختبارات للـ Validators وServices، ثم اختبارات تكامل لعمليات قاعدة البيانات باستخدام بيئة اختبار منفصلة.
4. إعدادات البيئة: فصل إعدادات التطوير عن الإنتاج وإبقاء بيانات الاعتماد خارج المستودع.
5. توحيد نقاط التهيئة: دراسة bootstrap مركزي بعد حصر مراجع "require/include" والتحقق من ترتيب الاعتماديات.
6. تقوية التعامل مع الملفات المرفوعة: التحقق من النوع والحجم والاسم، ومنع تنفيذ الملفات داخل مجلدات الرفع، وتقييد الوصول المباشر حسب الحاجة.

---

10. Diagram: Folder Ownership

يوضح الجدول اتجاه الاعتماد المعماري الذي يجب الحفاظ عليه عند تطوير النظام.

From| Can Call| Should Not Call Directly
"Backend_controller/"| "app/Controllers/"، "config/"، Helpers اللازمة لفحص الطلب| Models لتنفيذ عمليات المجال مباشرة؛ Services لتجاوز Controller
"app/Controllers/"| "app/Services/"، Validators/Helpers| "Backend_controller/" أو Views أو "header()" أو "$_POST"
"app/Services/"| "app/Models/"، "app/Helpers/"| Controllers أو Handlers أو Views
"app/Models/"| اتصال PDO الممرر من الخارج| Services أو Controllers أو HTTP
"app/Helpers/"| وظائف مساعدة مستقلة| طبقات أعلى أو استعلامات SQL غير مبررة
"resources/views/"| Partial views وLayouts وبيانات العرض المتاحة| Controllers/Services/Models لتنفيذ منطق العمل من داخل قالب العرض
"includes/"| أجزاء العرض المشتركة والبيانات اللازمة للعرض| تنفيذ عمليات المجال أو SQL داخل جزء واجهة
"config/"| إعداد الاتصال وتعريف الإعدادات العامة| الاعتماد على Controllers أو Views

رسم اتجاه الاعتماد

HTTP Request
     |
     v
Backend_controller/
     |
     v
app/Controllers/
     |
     v
app/Services/
     |
     +-----------> app/Helpers/ (عند الحاجة)
     |
     v
app/Models/
     |
     v
PDO
     |
     v
MySQL / MariaDB


Page Entry / Controller Data
     |
     v
resources/views/
     |
     +-----------> resources/views/layouts/
     |
     +-----------> resources/views/partials/
     |
     +-----------> includes/ (الأجزاء المشتركة)


config/env_loader.php
     |
     v
config/config.php
     |
     v
تهيئة اتصال PDO والدوال العامة

قاعدة عملية للمراجعة

عند تتبع أي ميزة، ابدأ من نموذجها أو رابطها في View، ثم تتبع "action" أو عنوان "fetch"، وبعده Handler، ثم Controller، ثم Service، ثم Model. راجع مسارات "require_once" وحدد مسؤولية كل ملف من الكود الفعلي، وليس من اسمه فقط.