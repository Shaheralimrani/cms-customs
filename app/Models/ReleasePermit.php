<?php

class ReleasePermit
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * جلب جميع أذونات الإفراج السابقة.
     */
    public function getAllPermits(): array
    {
        $sql = "
            SELECT
                rp.permit_id,
                rp.declaration_id,
                rp.payment_id,
                rp.issued_at,
                d.shipment_type,
                d.status,
                ib.company_name,
                u.username AS issuer_name,
                p.amount_paid AS payment_amount,
                p.payment_method
            FROM release_permits rp
            INNER JOIN declarations d
                ON d.declaration_id = rp.declaration_id
            LEFT JOIN importers_brokers ib
                ON ib.client_id = d.client_id
            INNER JOIN users u
                ON u.user_id = rp.issued_by
            INNER JOIN payments p
                ON p.payment_id = rp.payment_id
            ORDER BY rp.issued_at DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * جلب بيانات إذن الإفراج المطلوبة لمستند الطباعة.
     */
    public function getPermitForPrint(string $permitId): ?array
    {
        $sql = "
            SELECT
                rp.permit_id,
                rp.declaration_id,
                rp.payment_id,
                rp.issued_at,

                d.shipment_type,
                d.origin_country,
                d.total_weight,
                d.packages_count,
                d.declared_value,
                d.container_number,
                d.status,

                ib.company_name,
                ib.license_number,
                ib.phone,
                ib.email,

                u.username AS issuer_name,
                u.role AS issuer_role,

                p.amount_paid AS payment_amount,
                p.payment_method,
                p.payment_date,

                inv.invoice_id,
                inv.total_amount AS invoice_total,
                inv.duty_amount,
                inv.tax_amount,

                i.route_color,
                i.inspected_at

            FROM release_permits rp

            INNER JOIN declarations d
                ON d.declaration_id = rp.declaration_id

            LEFT JOIN importers_brokers ib
                ON ib.client_id = d.client_id

            INNER JOIN users u
                ON u.user_id = rp.issued_by

            INNER JOIN payments p
                ON p.payment_id = rp.payment_id

            LEFT JOIN invoices inv
                ON inv.invoice_id = p.invoice_id
               AND inv.declaration_id = d.declaration_id

            LEFT JOIN inspections i
                ON i.inspection_id = (
                    SELECT i2.inspection_id
                    FROM inspections i2
                    WHERE i2.declaration_id = d.declaration_id
                    ORDER BY
                        i2.inspected_at DESC,
                        i2.inspection_id DESC
                    LIMIT 1
                )

            WHERE rp.permit_id = :permit_id

            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':permit_id' => $permitId
        ]);

        $permit = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permit ?: null;
    }

    /**
     * جلب البيانات التي دفعت فواتيرها ولم يصدر لها إذن إفراج.
     */
    public function getReadyForRelease(): array
    {
        $sql = "
            SELECT
                d.declaration_id,
                d.shipment_type,
                d.status,
                ib.company_name,
                i.total_amount,
                p.payment_id,
                p.amount_paid AS payment_amount,
                p.payment_method,
                p.payment_date
            FROM declarations d
            INNER JOIN invoices i
                ON i.declaration_id = d.declaration_id
            INNER JOIN payments p
                ON p.invoice_id = i.invoice_id
            LEFT JOIN importers_brokers ib
                ON ib.client_id = d.client_id
            LEFT JOIN release_permits rp
                ON rp.declaration_id = d.declaration_id
            WHERE d.status = 'paid'
              AND rp.permit_id IS NULL
            ORDER BY p.payment_date DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * التحقق من أهلية البيان وإقفال سجله أثناء المعاملة.
     */
    public function findEligibleForUpdate(
        string $declarationId,
        int $paymentId
    ): ?array {
        $sql = "
            SELECT
                d.declaration_id,
                d.status,
                i.invoice_id,
                i.total_amount,
                p.payment_id,
                p.amount_paid AS payment_amount
            FROM declarations d
            INNER JOIN invoices i
                ON i.declaration_id = d.declaration_id
            INNER JOIN payments p
                ON p.invoice_id = i.invoice_id
            WHERE d.declaration_id = :declaration_id
              AND p.payment_id = :payment_id
              AND d.status = 'paid'
            LIMIT 1
            FOR UPDATE
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':declaration_id' => $declarationId,
            ':payment_id' => $paymentId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * التحقق من وجود إذن سابق للبيان.
     */
    public function permitExists(string $declarationId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM release_permits
            WHERE declaration_id = :declaration_id
            LIMIT 1
        ");

        $stmt->execute([
            ':declaration_id' => $declarationId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * إنشاء إذن الإفراج.
     */
    public function createPermit(
        string $permitId,
        string $declarationId,
        int $paymentId,
        int $issuedBy
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO release_permits (
                permit_id,
                declaration_id,
                payment_id,
                issued_by
            ) VALUES (
                :permit_id,
                :declaration_id,
                :payment_id,
                :issued_by
            )
        ");

        return $stmt->execute([
            ':permit_id' => $permitId,
            ':declaration_id' => $declarationId,
            ':payment_id' => $paymentId,
            ':issued_by' => $issuedBy
        ]);
    }

    /**
     * تغيير حالة البيان إلى released.
     */
    public function markReleased(string $declarationId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE declarations
            SET status = 'released'
            WHERE declaration_id = :declaration_id
              AND status = 'paid'
        ");

        $stmt->execute([
            ':declaration_id' => $declarationId
        ]);

        return $stmt->rowCount() === 1;
    }

    /**
     * تسجيل العملية في سجل التدقيق.
     */
    public function createAuditLog(
        string $declarationId,
        int $userId,
        string $action
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO audit_logs (
                declaration_id,
                user_id,
                action_taken
            ) VALUES (
                :declaration_id,
                :user_id,
                :action_taken
            )
        ");

        return $stmt->execute([
            ':declaration_id' => $declarationId,
            ':user_id' => $userId,
            ':action_taken' => $action
        ]);
    }
}