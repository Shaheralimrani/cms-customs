<?php
declare(strict_types=1);

class UserManagement
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function usernameExists(
        string $username,
        ?int $excludeUserId = null
    ): bool {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = :username';
        $params = [':username' => $username];

        if ($excludeUserId !== null) {
            $sql .= ' AND user_id != :user_id';
            $params[':user_id'] = $excludeUserId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function emailExists(
        string $email,
        ?int $excludeUserId = null
    ): bool {
        if ($email === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = [':email' => $email];

        if ($excludeUserId !== null) {
            $sql .= ' AND user_id != :user_id';
            $params[':user_id'] = $excludeUserId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, full_name, username, email, role
             FROM users
             WHERE user_id = :user_id
             LIMIT 1'
        );

        $stmt->execute([':user_id' => $userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO users
                (full_name, username, email, password_hash, role)
                VALUES
                (:full_name, :username, :email, :password_hash, :role)';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':full_name' => trim($data['full_name']),
            ':username' => trim($data['username']),
            ':email' => trim((string) ($data['email'] ?? '')),
            ':password_hash' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),
            ':role' => $data['role']
        ]);
    }

    public function update(int $userId, array $data): void
    {
        $params = [
            ':user_id' => $userId,
            ':full_name' => trim($data['full_name']),
            ':username' => trim($data['username']),
            ':email' => trim((string) ($data['email'] ?? '')),
            ':role' => $data['role']
        ];

        $password = (string) ($data['password'] ?? '');

        if ($password !== '') {
            $sql = 'UPDATE users
                    SET full_name = :full_name,
                        username = :username,
                        email = :email,
                        password_hash = :password_hash,
                        role = :role
                    WHERE user_id = :user_id';

            $params[':password_hash'] = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
        } else {
            $sql = 'UPDATE users
                    SET full_name = :full_name,
                        username = :username,
                        email = :email,
                        role = :role
                    WHERE user_id = :user_id';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function findLowestManagerId(): ?int
    {
        $stmt = $this->pdo->query(
            "SELECT user_id
             FROM users
             WHERE role = 'manager'
             ORDER BY user_id ASC
             LIMIT 1"
        );

        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function delete(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM users WHERE user_id = :user_id'
        );

        $stmt->execute([':user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('المستخدم غير موجود أو سبق حذفه.');
        }
    }
}