<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="login-page">

<main class="login-container">
    <section class="login-card">

        <header class="login-header">
            <div class="login-brand-icon">
                <i class="fa-solid fa-ship"></i>
            </div>

            <h1>نظام إدارة الجمارك <span>CMS</span></h1>

            <p>منظومة الإفراج الجمركي وتتبع الشحنات</p>

            <div class="login-divider"></div>

            <h2>تسجيل الدخول</h2>

            <p class="login-description">
                أدخل بيانات حسابك للوصول إلى النظام
            </p>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger login-error" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars(
                    (string) $error,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <form
            action="Backend_controller/auth_controller.php"
            method="POST"
            id="loginForm"
        >
            <!-- الدور الوظيفي -->
            <div class="login-field">
                <label for="role" class="form-label">
                    <i class="fa-solid fa-user-gear"></i>
                    الصفة الوظيفية (الدور)
                </label>

                <select
                    class="form-select"
                    name="role"
                    id="role"
                    required
                >
                    <option value="" selected disabled>
                        -- اختر طبيعة حسابك --
                    </option>

                    <option value="broker">
                        مخلص جمركي / مكتب تخليص
                    </option>

                    <option value="inspector">
                        موظف معاينة وتفتيش
                    </option>

                    <option value="accountant">
                        موظف مالي / محاسب
                    </option>

                    <option value="manager">
                        مدير المنفذ / مشرف نظام
                    </option>
                </select>
            </div>

            <!-- اسم المستخدم -->
            <div class="login-field">
                <label for="username" class="form-label">
                    <i class="fa-solid fa-id-card"></i>
                    اسم المستخدم / الرقم الوظيفي
                </label>

                <div class="login-input-group">
                    <span class="login-input-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        placeholder="أدخل اسم المستخدم"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <!-- البريد الإلكتروني -->
            <div class="login-field">
                <label for="email" class="form-label">
                    <i class="fa-solid fa-envelope"></i>
                    البريد الإلكتروني
                </label>

                <div class="login-input-group">
                    <span class="login-input-icon">
                        <i class="fa-solid fa-at"></i>
                    </span>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="أدخل بريدك الإلكتروني"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="login-field login-password-field">
                <label for="password" class="form-label">
                    <i class="fa-solid fa-lock"></i>
                    كلمة المرور
                </label>

                <div class="login-input-group">
                    <span class="login-input-icon">
                        <i class="fa-solid fa-key"></i>
                    </span>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="*******"
                        autocomplete="current-password"
                        required
                    >
                </div>
            </div>

            <!-- زر تسجيل الدخول -->
            <button
                type="submit"
                class="btn login-submit"
                id="loginSubmit"
            >
                <span>دخول للنظام الجمركي</span>
                <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>

        <footer class="login-footer">
            <div class="login-footer-line"></div>

            <p>
                <i class="fa-solid fa-landmark"></i>
                الجمهورية اليمنية
            </p>

            <span>مصلحة الجمارك والمنافذ</span>
        </footer>

    </section>

    <div class="login-bottom-note">
        نظام الإدارة الجمركية <span>CMS</span>
    </div>
</main>

</body>
</html>