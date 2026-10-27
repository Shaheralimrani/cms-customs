<?php

declare(strict_types=1);

class Payment
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * جلب الفواتير التي تنتظر السداد.
     */
    public function getPendingInvoices(string $search = ''): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.declaration_id,
                i.hs_code,
                i.duty_amount,
                i.tax_amount,
                i.total_amount,
                i.created_at,
                b.company_name
            FROM invoices AS i
            INNER JOIN declarations AS d
                ON i.declaration_id = d.declaration_id
            INNER JOIN importers_brokers AS b
                ON d.client_id = b.client_id
            WHERE d.status = 'invoiced'
              AND NOT EXISTS (
                  SELECT 1
                  FROM payments AS p
                  WHERE p.invoice_id = i.invoice_id
              )
        ";

        $params = [];

        if ($search !== '') {
            $sql .= "
                AND (
                    CAST(i.invoice_id AS CHAR) LIKE ?
                    OR i.declaration_id LIKE ?
                    OR b.company_name LIKE ?
                )
            ";

            $term = '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY i.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * جلب فاتورة وقفل سجلها أثناء معاملة الدفع.
     *
     * يجب استدعاء هذه الدالة داخل Transaction.
     */
    public function findInvoiceForUpdate(int $invoiceId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                i.invoice_id,
                i.declaration_id,
                i.total_amount,
                d.status
            FROM invoices AS i
            INNER JOIN declarations AS d
                ON i.declaration_id = d.declaration_id
            WHERE i.invoice_id = ?
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([$invoiceId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * التحقق من وجود دفعة مسجلة للفاتورة.
     */
    public function paymentExists(int $invoiceId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT payment_id
            FROM payments
            WHERE invoice_id = ?
            LIMIT 1
        ");

        $stmt->execute([$invoiceId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * تسجيل عملية الدفع.
     */
    public function createPayment(
        int $invoiceId,
        int $accountantId,
        float $amount,
        string $method,
        ?string $bankName,
        ?string $referenceNumber
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (
                invoice_id,
                accountant_id,
                amount_paid,
                payment_method,
                bank_name,
                reference_number
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $invoiceId,
            $accountantId,
            $amount,
            $method,
            $bankName,
            $referenceNumber
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * تغيير حالة البيان إلى مدفوع.
     */
    public function markDeclarationAsPaid(
        string $declarationId
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE declarations
            SET status = 'paid'
            WHERE declaration_id = ?
              AND status = 'invoiced'
        ");

        $stmt->execute([$declarationId]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException(
                'تعذر تحديث حالة البيان إلى مدفوع.'
            );
        }
    }

    /**
     * تسجيل العملية في سجل التدقيق.
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
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $declarationId,
            $userId,
            $action
        ]);
    }
}