<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Helpers/AuthValidator.php';

class AuthService
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    /**
     * التحقق من بيانات الحساب وإنشاء جلسة المستخدم.
     */
    public function authenticate(array $input): array
    {
        // أولًا: التحقق من الحقول.
        $validated = AuthValidator::validateLogin($input);

        if (!$validated['valid']) {
            return [
                'success' => false,
                'error' => $validated['error']
            ];
        }

        // ثانيًا: البحث باستخدام اسم المستخدم والبريد معًا.
        $user = $this->userModel->findByUsernameAndEmail(
            $validated['username'],
            $validated['email']
        );

        if (!$user) {
            return [
                'success' => false,
                'error' => 'بيانات تسجيل الدخول غير صحيحة.'
            ];
        }

        // ثالثًا: التحقق من كلمة المرور المشفرة دون تغيير آليته.
        if (!password_verify(
            $validated['password'],
            $user['password_hash']
        )) {
            return [
                'success' => false,
                'error' => 'اسم المستخدم أو كلمة المرور غير صحيحة.'
            ];
        }

        // رابعًا: التأكد من أن الدور المختار يطابق دور الحساب.
        if ($validated['role'] !== $user['role']) {
            return [
                'success' => false,
                'error' => 'الدور الوظيفي المختار لا يطابق حساب المستخدم.'
            ];
        }

        // خامسًا: التأكد من أن الدور معتمد في النظام.
        $allowedRoles = [
            'manager',
            'broker',
            'inspector',
            'accountant'
        ];

        if (!in_array($user['role'], $allowedRoles, true)) {
            return [
                'success' => false,
                'error' => 'الدور الوظيفي لهذا الحساب غير معتمد.'
            ];
        }

        // سادسًا: تجديد معرّف الجلسة بعد نجاح تسجيل الدخول.
        session_regenerate_id(true);

        // سابعًا: حفظ بيانات المستخدم في الجلسة.
        $_SESSION['user_id'] = (int) $user['user_id'];

        $_SESSION['full_name'] = (string) (
            $user['full_name'] ?? ''
        );

        $_SESSION['username'] = (string) $user['username'];

        $_SESSION['role'] = (string) $user['role'];

        // إنشاء رمز حماية للنماذج التي تستخدم CSRF.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return [
            'success' => true,
            'role' => $user['role']
        ];
    }
}