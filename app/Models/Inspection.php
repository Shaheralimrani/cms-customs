<?php

class Inspection
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * جلب البيانات التي تحتاج إلى المعاينة.
     * يشمل ذلك البيانات الجديدة والبيانات المحالة
     * للمراجعة الإضافية، بشرط ألا تكون موقوفة بانتظار المدير.
     */
    public function getDeclarations(string $search = ''): array
    {
        $sql = "
            SELECT
                d.declaration_id,
                d.client_id,
                d.origin_country,
                d.shipment_type,
                d.total_weight,
                d.packages_count,
                d.declared_value,
                d.port_of_entry,
                d.container_number,
                d.driver_name,
                d.status,
                d.created_at,
                b.company_name,
                b.license_number,
                (
                    SELECT i.route_color
                    FROM inspections i
                    WHERE i.declaration_id = d.declaration_id
                    ORDER BY i.inspection_id DESC
                    LIMIT 1
                ) AS route_color
            FROM declarations d
            INNER JOIN importers_brokers b
                ON b.client_id = d.client_id
            WHERE d.status IN ('new', 'inspection_review')
        ";

        $params = [];

        if ($search !== '') {
            $sql .= "
                AND (
                    b.company_name LIKE :company
                    OR d.declaration_id LIKE :declaration
                )
            ";

            $params['company'] = '%' . $search . '%';
            $params['declaration'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * جلب بيان مع قفله أثناء المعالجة.
     */
    public function findDeclarationForUpdate(
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

    /**
     * جلب آخر معاينة للبيان.
     */
    public function getLatestInspection(
        string $declarationId
    ): ?array {
        $stmt = $this->pdo->prepare("
            SELECT
                inspection_id,
                declaration_id,
                inspector_id,
                inspection_notes,
                route_color
            FROM inspections
            WHERE declaration_id = :id
            ORDER BY inspection_id DESC
            LIMIT 1
        ");

        $stmt->execute(['id' => $declarationId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * تسجيل معاينة جديدة.
     * لا نحذف ولا نحدّث المعاينات السابقة.
     */
    public function createInspection(
        string $declarationId,
        int $inspectorId,
        string $notes,
        string $routeColor
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO inspections (
                declaration_id,
                inspector_id,
                inspection_notes,
                route_color
            )
            VALUES (
                :declaration_id,
                :inspector_id,
                :notes,
                :route_color
            )
        ");

        $stmt->execute([
            'declaration_id' => $declarationId,
            'inspector_id' => $inspectorId,
            'notes' => $notes,
            'route_color' => $routeColor
        ]);
    }

    /**
     * تحديث حالة البيان.
     */
    public function updateDeclarationStatus(
        string $declarationId,
        string $status
    ): void {
        $allowedStatuses = [
            'new',
            'inspection_review',
            'pending_manager_approval',
            'inspected'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'حالة البيان غير مسموح بها.'
            );
        }

        $stmt = $this->pdo->prepare("
            UPDATE declarations
            SET status = :status
            WHERE declaration_id = :id
        ");

        $stmt->execute([
            'status' => $status,
            'id' => $declarationId
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException(
                'تعذر تحديث حالة البيان.'
            );
        }
    }

    /**
     * إضافة سجل إلى سجل التدقيق.
     */
    public function createAuditLog(
        string $declarationId,
        int $userId,
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
            'user_id' => $userId,
            'action' => $action
        ]);
    }

    /**
     * جلب سجل المعاينات السابقة.
     */
    public function getInspectionHistory(
        string $declarationId
    ): array {
        $stmt = $this->pdo->prepare("
            SELECT
                inspection_id,
                declaration_id,
                inspector_id,
                inspection_notes,
                route_color
            FROM inspections
            WHERE declaration_id = :id
            ORDER BY inspection_id DESC
        ");

        $stmt->execute(['id' => $declarationId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}