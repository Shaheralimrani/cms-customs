<?php

declare(strict_types=1);

class AuthValidator
{
    /**
     * التحقق من بيانات تسجيل الدخول.
     */
    public static function validateLogin(array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));

        $email = trim((string) ($input['email'] ?? ''));

        // لا نستخدم trim لكلمة المرور حتى لا نغيّر قيمتها.
        $password = (string) ($input['password'] ?? '');

        $role = trim((string) ($input['role'] ?? ''));

        if (
            $username === ''
            || $email === ''
            || $password === ''
            || $role === ''
        ) {
            return [
                'valid' => false,
                'error' => 'يرجى تعبئة جميع حقول تسجيل الدخول.'
            ];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return [
                'valid' => false,
                'error' => 'يرجى إدخال بريد إلكتروني صحيح.'
            ];
        }

        $allowedRoles = [
            'manager',
            'broker',
            'inspector',
            'accountant'
        ];

        if (!in_array($role, $allowedRoles, true)) {
            return [
                'valid' => false,
                'error' => 'الدور الوظيفي المحدد غير صالح.'
            ];
        }

        return [
            'valid' => true,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ];
    }
}