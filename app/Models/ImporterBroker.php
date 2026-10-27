<?php
declare(strict_types=1);

class ImporterBroker
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $clientId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT client_id, company_name, license_number, phone, email
             FROM importers_brokers
             WHERE client_id = :client_id
             LIMIT 1'
        );

        $stmt->execute([
            ':client_id' => $clientId,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function findByCompanyName(string $companyName): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT client_id, company_name, license_number, phone, email
             FROM importers_brokers
             WHERE company_name = :company_name
             LIMIT 1'
        );

        $stmt->execute([
            ':company_name' => $companyName,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function updateContact(
        int $clientId,
        string $phone,
        string $email
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE importers_brokers
             SET phone = :phone,
                 email = :email
             WHERE client_id = :client_id'
        );

        $stmt->execute([
            ':phone' => $phone,
            ':email' => $email,
            ':client_id' => $clientId,
        ]);
    }

    public function create(
        string $companyName,
        string $licenseNumber,
        string $phone,
        string $email
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO importers_brokers
                (company_name, license_number, phone, email)
             VALUES
                (:company_name, :license_number, :phone, :email)'
        );

        $stmt->execute([
            ':company_name' => $companyName,
            ':license_number' => $licenseNumber,
            ':phone' => $phone,
            ':email' => $email,
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}