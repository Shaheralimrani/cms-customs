<?php

declare(strict_types=1);

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * البحث عن المستخدم باستخدام اسم المستخدم والبريد الإلكتروني.
     */
    public function findByUsernameAndEmail(
        string $username,
        string $email
    ): ?array {
        $sql = "
            SELECT
                user_id,
                full_name,
                username,
                password_hash,
                role
            FROM users
            WHERE username = :username
              AND email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}