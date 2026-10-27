<?php
declare(strict_types=1);

class UserManagementValidator
{
    private array $roles = [
        'manager',
        'broker',
        'inspector',
        'accountant'
    ];

    public function validateAdd(array $data): array
    {
        $errors = $this->validateCommon($data);

        $password = (string) ($data['password'] ?? '');

        if ($password === '') {
            $errors[] = 'كلمة المرور مطلوبة.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب ألا تقل عن 8 أحرف.';
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = $this->validateCommon($data);

        $userId = filter_var(
            $data['user_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($userId === false || $userId === null || $userId <= 0) {
            $errors[] = 'رقم المستخدم غير صحيح.';
        }

        $password = (string) ($data['password'] ?? '');

        if ($password !== '' && strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب ألا تقل عن 8 أحرف.';
        }

        return $errors;
    }

    public function validateDelete(array $data): array
    {
        $userId = filter_var(
            $data['user_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($userId === false || $userId === null || $userId <= 0) {
            return ['رقم المستخدم غير صحيح.'];
        }

        return [];
    }

    private function validateCommon(array $data): array
    {
        $errors = [];

        $fullName = trim((string) ($data['full_name'] ?? ''));
        $username = trim((string) ($data['username'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $role = trim((string) ($data['role'] ?? ''));

        if ($fullName === '') {
            $errors[] = 'الاسم الكامل مطلوب.';
        }

        if ($username === '') {
            $errors[] = 'اسم المستخدم مطلوب.';
        } elseif (strlen($username) > 100) {
            $errors[] = 'اسم المستخدم طويل جدًا.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صحيح.';
        }

        if (!in_array($role, $this->roles, true)) {
            $errors[] = 'نوع الحساب غير صحيح.';
        }

        return $errors;
    }
}