<?php
declare(strict_types=1);

class FileUploader
{
    private string $uploadDirectory;
    private string $relativeDirectory;
    private int $maxBytes;

    public function __construct(
        string $uploadDirectory,
        string $relativeDirectory = 'uploads/declarations',
        int $maxBytes = 5242880
    ) {
        $this->uploadDirectory = rtrim($uploadDirectory, DIRECTORY_SEPARATOR);
        $this->relativeDirectory = trim(
            str_replace('\\', '/', $relativeDirectory),
            '/'
        );
        $this->maxBytes = $maxBytes;
    }

    public function upload(array $file): array
    {
        if (!isset($file['error'])) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'بيانات الملف غير مكتملة.',
            ];
        }

        $errorCode = (int)$file['error'];

        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => true,
                'path' => null,
                'error' => null,
            ];
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => null,
                'error' => $this->uploadErrorMessage($errorCode),
            ];
        }

        $temporaryPath = (string)($file['tmp_name'] ?? '');
        $originalName = (string)($file['name'] ?? '');
        $fileSize = (int)($file['size'] ?? 0);

        if ($temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'الملف المرفوع غير صالح.',
            ];
        }

        if ($fileSize <= 0 || $fileSize > $this->maxBytes) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
            ];
        }

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'امتداد الملف غير مسموح به.',
            ];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($temporaryPath);

        $allowedMimeTypes = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
        ];

        if (
            !is_string($mimeType)
            || !in_array(
                $mimeType,
                $allowedMimeTypes[$extension],
                true
            )
        ) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'محتوى الملف لا يتوافق مع نوعه.',
            ];
        }

        $absoluteDirectory = $this->uploadDirectory
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $this->relativeDirectory
            );

        if (
            !is_dir($absoluteDirectory)
            && !mkdir($absoluteDirectory, 0755, true)
            && !is_dir($absoluteDirectory)
        ) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'تعذر إنشاء مجلد رفع الملفات.',
            ];
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;

        $destination = $absoluteDirectory
            . DIRECTORY_SEPARATOR
            . $newFilename;

        if (!move_uploaded_file($temporaryPath, $destination)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'تعذر حفظ الملف المرفوع.',
            ];
        }

        return [
            'success' => true,
            'path' => $this->relativeDirectory . '/' . $newFilename,
            'error' => null,
        ];
    }

    public function delete(string $relativePath): void
    {
        $prefix = $this->relativeDirectory . '/';

        if (strpos($relativePath, $prefix) !== 0) {
            return;
        }

        $filename = substr($relativePath, strlen($prefix));

        // اسم ملف فقط، لمنع الخروج من مجلد الرفع.
        if (
            $filename === ''
            || basename($filename) !== $filename
            || $filename === '.'
            || $filename === '..'
        ) {
            return;
        }

        $absoluteDirectory = $this->uploadDirectory
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $this->relativeDirectory
            );

        $fullPath = $absoluteDirectory
            . DIRECTORY_SEPARATOR
            . $filename;

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private function uploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'حجم الملف أكبر من الحد المسموح.';

            case UPLOAD_ERR_PARTIAL:
                return 'لم يتم رفع الملف بالكامل.';

            case UPLOAD_ERR_NO_TMP_DIR:
                return 'مجلد الملفات المؤقتة غير متوفر.';

            case UPLOAD_ERR_CANT_WRITE:
                return 'تعذر كتابة الملف على القرص.';

            case UPLOAD_ERR_EXTENSION:
                return 'تم إيقاف رفع الملف بواسطة إعداد في الخادم.';

            default:
                return 'حدث خطأ أثناء رفع الملف.';
        }
    }
}