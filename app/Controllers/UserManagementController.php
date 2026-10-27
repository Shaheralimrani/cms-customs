<?php
declare(strict_types=1);

require_once __DIR__ . '/../Services/UserManagementService.php';

class UserManagementController
{
    private UserManagementService $service;

    public function __construct(PDO $pdo)
    {
        $this->service = new UserManagementService($pdo);
    }

    public function handle(array $data, int $currentUserId): array
    {
        $action = trim((string) ($data['action'] ?? ''));

        try {
            switch ($action) {
                case 'add':
                    return $this->service->add($data);

                case 'update':
                    return $this->service->update($data);

                case 'delete':
                    return $this->service->delete($data, $currentUserId);

                default:
                    return [
                        'success' => false,
                        'errors' => ['الإجراء المطلوب غير صحيح.']
                    ];
            }
        } catch (PDOException $exception) {
            error_log(
                'User management database error: ' .
                $exception->getMessage()
            );

            return [
                'success' => false,
                'errors' => [
                    'حدث خطأ في قاعدة البيانات. تحقق من سجل أخطاء PHP.'
                ]
            ];
        } catch (Throwable $exception) {
            error_log(
                'User management error: ' .
                $exception->getMessage()
            );

            return [
                'success' => false,
                'errors' => [
                    $exception instanceof RuntimeException
                        ? $exception->getMessage()
                        : 'حدث خطأ أثناء تنفيذ العملية.'
                ]
            ];
        }
    }
}