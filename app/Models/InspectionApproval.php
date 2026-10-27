<?php

class InspectionApproval
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * عرض البيانات الموقوفة بانتظار المدير.
     */
    public function getPendingApprovals(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                d.declaration_id,
                d.client_id,
                d.origin_country,
                d.shipment_type,
                d.total_weight,
                d.packages_count,
                d.declared_value,
                d.status,
                b.company_name,
                (
                    SELECT i.route_color
                    FROM inspections i
                    WHERE i.declaration_id = d.declaration_id
                    ORDER BY i.inspection_id DESC
                    LIMIT 1
                ) AS route_color,
                (
                    SELECT i.inspection_notes
                    FROM inspections i
                    WHERE i.declaration_id = d.declaration_id
                    ORDER BY i.inspection_id DESC
                    LIMIT 1
                ) AS inspection_notes,
                (
                    SELECT i.inspector_id
                    FROM inspections i
                    WHERE i.declaration_id = d.declaration_id
                    ORDER BY i.inspection_id DESC
                    LIMIT 1
                ) AS inspector_id
            FROM declarations d
            INNER JOIN importers_brokers b
                ON b.client_id = d.client_id
            WHERE d.status = 'pending_manager_approval'
            ORDER BY d.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findForUpdate(
        string $declarationId
    ): ?array {
        $stmt = $this->pdo->prepare("
            SELECT declaration_id, status
            FROM declarations
            WHERE declaration_id = :id
            FOR UPDATE
        ");

        $stmt->execute(['id' => $declarationId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

   public function updateStatus(
    string $declarationId,
    string $status
): void {
    $stmt = $this->pdo->prepare("
        UPDATE declarations
        SET status = :status
        WHERE declaration_id = :id
    ");

    $stmt->execute([
        'status' => $status,
        'id' => $declarationId
    ]);

    // نتحقق من وجود البيان، بدل الاعتماد على rowCount().
    $check = $this->pdo->prepare("
        SELECT 1
        FROM declarations
        WHERE declaration_id = :id
    ");

    $check->execute(['id' => $declarationId]);

    if ($check->fetchColumn() === false) {
        throw new RuntimeException(
            'البيان غير موجود عند محاولة تحديث حالته.'
        );
    }
}

    public function createAuditLog(
        string $declarationId,
        int $managerId,
        string $action
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO audit_logs (
                declaration_id,
                user_id,
                action_taken
            )
            VALUES (
                :declaration_id,
                :user_id,
                :action
            )
        ");

        $stmt->execute([
            'declaration_id' => $declarationId,
            'user_id' => $managerId,
            'action' => $action
        ]);
    }
}