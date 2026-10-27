<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../app/Helpers/DeclarationValidator.php';
require_once __DIR__ . '/../app/Helpers/DeclarationNumberGenerator.php';
require_once __DIR__ . '/../app/Helpers/LicenseNumberGenerator.php';
require_once __DIR__ . '/../app/Helpers/FileUploader.php';

require_once __DIR__ . '/../app/Models/ImporterBroker.php';
require_once __DIR__ . '/../app/Models/Declaration.php';

require_once __DIR__ . '/../app/Services/DeclarationService.php';
require_once __DIR__ . '/../app/Controllers/DeclarationController.php';

// التحقق من تسجيل الدخول وصلاحية الوسيط.
if (
    !isset($_SESSION['user_id'], $_SESSION['role'])
    || $_SESSION['role'] !== 'broker'
) {
    header('Location: ../login.php');
    exit;
}

// يجب أن يكون الطلب POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../declaration_new.php');
    exit;
}

// يجب أن يوفر config.php اتصال PDO في المتغير $pdo.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Database connection $pdo is missing in config/config.php.');

    $_SESSION['errors'] = [
        'تعذر الاتصال بقاعدة البيانات. تحقق من إعدادات الاتصال.',
    ];

    header('Location: ../declaration_new.php');
    exit;
}

try {
    $validator = new DeclarationValidator();

    $projectRoot = dirname(__DIR__);

    $uploader = new FileUploader(
        $projectRoot,
        'uploads/declarations',
        5242880
    );

    $declarationNumberGenerator = new DeclarationNumberGenerator();
    $licenseNumberGenerator = new LicenseNumberGenerator();

    $importerBroker = new ImporterBroker($pdo);

    $declarationModel = new Declaration($pdo);

    $declarationService = new DeclarationService(
        $pdo,
        $declarationModel
    );

    $controller = new DeclarationController(
        $pdo,
        $validator,
        $uploader,
        $declarationNumberGenerator,
        $licenseNumberGenerator,
        $importerBroker,
        $declarationService
    );

    $result = $controller->process($_POST, $_FILES);

    if ($result['success']) {
        $_SESSION['declaration_success'] = [
            'declaration_id' => $result['declaration_id'],
            'license_number' => $result['license_number'],
            'items_count' => $result['items_count'],
        ];

        unset($_SESSION['errors']);

        header('Location: ../declaration_new.php');
        exit;
    }

    $_SESSION['errors'] = $result['errors'] ?? [
        'تعذر معالجة البيان.',
    ];

    unset($_SESSION['declaration_success']);

    header('Location: ../declaration_new.php');
    exit;
} catch (Throwable $e) {
    error_log(
        'Declaration endpoint error: ' . $e->getMessage()
    );

    $_SESSION['errors'] = [
        'حدث خطأ غير متوقع أثناء معالجة البيان.',
    ];

    header('Location: ../declaration_new.php');
    exit;
}