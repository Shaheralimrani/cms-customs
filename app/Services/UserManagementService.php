<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/UserManagement.php';
require_once __DIR__ . '/../Helpers/UserManagementValidator.php';

class UserManagementService
{
    private UserManagement $model;
    private UserManagementValidator $validator;

    public function __construct(PDO $pdo)
    {
        $this->model = new UserManagement($pdo);
        $this->validator = new UserManagementValidator();
    }

    public function add(array $data): array
    {
        $errors = $this->validator->validateAdd($data);

        if ($errors) {
            return $this->failure($errors);
        }

        $username = trim($data['username']);
        $email = trim((string) ($data['email'] ?? ''));

        if ($this->model->usernameExists($username)) {
            return $this->failure(['اسم المستخدم مستخدم بالفعل.']);
        }

        if ($this->model->emailExists($email)) {
            return $this->failure(['البريد الإلكتروني مستخدم بالفعل.']);
        }

        $this->model->create($data);

        return [
            'success' => true,
            'message' => 'تمت إضافة المستخدم بنجاح.'
        ];
    }

    public function update(array $data): array
    {
        $errors = $this->validator->validateUpdate($data);

        if ($errors) {
            return $this->failure($errors);
        }

        $userId = (int) $data['user_id'];

        if (!$this->model->findById($userId)) {
            return $this->failure(['المستخدم المطلوب تعديله غير موجود.']);
        }

        $username = trim($data['username']);
        $email = trim((string) ($data['email'] ?? ''));

        if ($this->model->usernameExists($username, $userId)) {
            return $this->failure(['اسم المستخدم مستخدم بالفعل.']);
        }

        if ($this->model->emailExists($email, $userId)) {
            return $this->failure(['البريد الإلكتروني مستخدم بالفعل.']);
        }

        $this->model->update($userId, $data);

        return [
            'success' => true,
            'message' => 'تم تعديل بيانات المستخدم بنجاح.'
        ];
    }

    public function delete(array $data, int $currentUserId): array
    {
        $errors = $this->validator->validateDelete($data);

        if ($errors) {
            return $this->failure($errors);
        }

        $userId = (int) $data['user_id'];

        if ($userId === $currentUserId) {
            return $this->failure([
                'لا يمكنك حذف الحساب الذي تستخدمه حاليًا.'
            ]);
        }

        if (!$this->model->findById($userId)) {
            return $this->failure(['المستخدم المطلوب حذفه غير موجود.']);
        }

        $protectedManagerId = $this->model->findLowestManagerId();

        if ($protectedManagerId !== null && $userId === $protectedManagerId) {
            return $this->failure([
                'لا يمكن حذف حساب المدير الأساسي.'
            ]);
        }

        $this->model->delete($userId);

        return [
            'success' => true,
            'message' => 'تم حذف المستخدم بنجاح.'
        ];
    }

    private function failure(array $errors): array
    {
        return [
            'success' => false,
            'errors' => $errors
        ];
    }
}