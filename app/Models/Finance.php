<?php

class Finance
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * جلب البيان مع قفل السجل أثناء المعاملة.
     */
    public function findDeclarationForUpdate(string $declarationId): ?array
    {
        $sql = "
            SELECT declaration_id, declared_value, status
            FROM declarations
            WHERE declaration_id = ?
            LIMIT 1
            FOR UPDATE
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$declarationId]);

        $declaration = $stmt->fetch(PDO::FETCH_ASSOC);

        return $declaration ?: null;
    }

    /**
     * جلب التعريفة باستخدام HS Code.
     */
    public function findTariff(string $hsCode): ?array
    {
        $sql = "
            SELECT
                hs_code,
                category_name,
                customs_duty_rate,
                tax_rate
            FROM tariff_schedule
            WHERE hs_code = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$hsCode]);

        $tariff = $stmt->fetch(PDO::FETCH_ASSOC);

        return $tariff ?: null;
    }

    /**
     * التحقق من وجود فاتورة سابقة للبيان.
     */
    public function invoiceExists(string $declarationId): bool
    {
        $sql = "
            SELECT invoice_id
            FROM invoices
            WHERE declaration_id = ?
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$declarationId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * إنشاء فاتورة جديدة.
     */
    public function createInvoice(
        string $declarationId,
        string $hsCode,
        float $dutyAmount,
        float $taxAmount,
        float $totalAmount
    ): int {
        $sql = "
            INSERT INTO invoices (
                declaration_id,
                hs_code,
                duty_amount,
                tax_amount,
                total_amount
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $declarationId,
            $hsCode,
            $dutyAmount,
            $taxAmount,
            $totalAmount
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * تحديث حالة البيان من inspected إلى invoiced.
     */
    public function markAsInvoiced(string $declarationId): void
    {
        $sql = "
            UPDATE declarations
            SET status = 'invoiced'
            WHERE declaration_id = ?
              AND status = 'inspected'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$declarationId]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException(
                'تعذر تحديث حالة البيان إلى invoiced.'
            );
        }
    }

    /**
     * تسجيل إصدار الفاتورة في سجل التدقيق.
     */
    public function logInvoice(
        string $declarationId,
        int $accountantId,
        string $hsCode,
        float $dutyAmount,
        float $taxAmount,
        float $totalAmount
    ): void {
        $action = sprintf(
            'تم إصدار فاتورة مالية - HS Code: %s - الرسوم: %s - الضريبة: %s - الإجمالي: %s',
            $hsCode,
            number_format($dutyAmount, 2, '.', ''),
            number_format($taxAmount, 2, '.', ''),
            number_format($totalAmount, 2, '.', '')
        );

        $sql = "
            INSERT INTO audit_logs (
                declaration_id,
                user_id,
                action_taken
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $declarationId,
            $accountantId,
            $action
        ]);
    }

    /**
 * جلب البيانات المؤهلة للاحتساب المالي مع البحث.
 */
public function getDeclarationsForFinance(string $search = ''): array
{
    $sql = "
        SELECT
            d.declaration_id,
            d.shipment_type,
            d.origin_country,
            d.declared_value,
            d.status,
            d.created_at,
            b.company_name
        FROM declarations AS d
        INNER JOIN importers_brokers AS b
            ON d.client_id = b.client_id
        WHERE d.status = 'inspected'
    ";

    $params = [];

    if ($search !== '') {
        $sql .= "
            AND (
                d.declaration_id LIKE ?
                OR b.company_name LIKE ?
            )
        ";

        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    $sql .= " ORDER BY d.created_at DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * جلب التعريفات الجمركية لعرضها في النافذة.
 */
public function getTariffs(): array
{
    $sql = "
        SELECT
            hs_code,
            category_name,
            customs_duty_rate,
            tax_rate
        FROM tariff_schedule
        ORDER BY hs_code ASC
    ";

    $stmt = $this->pdo->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>