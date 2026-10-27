<?php

declare(strict_types=1);

require_once __DIR__ . '/../Services/AuthService.php';

class AuthController
{
    private AuthService $authService;

    public function __construct(PDO $pdo)
    {
        $this->authService = new AuthService($pdo);
    }

    /**
     * تنفيذ عملية تسجيل الدخول.
     */
    public function login(array $input): array
    {
        return $this->authService->authenticate($input);
    }
}