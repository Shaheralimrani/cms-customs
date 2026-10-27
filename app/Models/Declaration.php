<?php
declare(strict_types=1);

class Declaration
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): string
    {
        $sql = '
            INSERT INTO declarations (
                declaration_id,
                client_id,
                origin_country,
                shipment_type,
                total_weight,
                packages_count,
                declared_value,
                port_of_entry,
                container_number,
                driver_name,
                invoice_file,
                origin_cert_file,
                health_cert_file
            ) VALUES (
                :declaration_id,
                :client_id,
                :origin_country,
                :shipment_type,
                :total_weight,
                :packages_count,
                :declared_value,
                :port_of_entry,
                :container_number,
                :driver_name,
                :invoice_file,
                :origin_cert_file,
                :health_cert_file
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':declaration_id' => $data['declaration_id'],
            ':client_id' => $data['client_id'],
            ':origin_country' => $data['origin_country'],
            ':shipment_type' => $data['shipment_type'],
            ':total_weight' => $data['total_weight'],
            ':packages_count' => $data['packages_count'],
            ':declared_value' => $data['declared_value'],
            ':port_of_entry' => $data['port_of_entry'],
            ':container_number' => $data['container_number'],
            ':driver_name' => $data['driver_name'],
            ':invoice_file' => $data['invoice_file'],
            ':origin_cert_file' => $data['origin_cert_file'],
            ':health_cert_file' => $data['health_cert_file'],
        ]);

        return (string)$data['declaration_id'];
    }

    public function addItems(
        string $declarationId,
        array $items
    ): void {
        $sql = '
            INSERT INTO declaration_items (
                declaration_id,
                item_description,
                quantity,
                declared_value
            ) VALUES (
                :declaration_id,
                :item_description,
                :quantity,
                :declared_value
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        foreach ($items as $item) {
            $stmt->execute([
                ':declaration_id' => $declarationId,
                ':item_description' => $item['description'],
                ':quantity' => $item['quantity'],
                ':declared_value' => $item['declared_value'],
            ]);
        }
    }

    public function findById(string $declarationId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM declarations
             WHERE declaration_id = :declaration_id
             LIMIT 1'
        );

        $stmt->execute([
            ':declaration_id' => $declarationId,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }
}