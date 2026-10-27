<?php
declare(strict_types=1);

class Dashboard
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * عدد المعاملات التي أُنشئت اليوم.
     */
    public function getTodayDeclarationsCount(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM declarations
            WHERE DATE(created_at) = CURDATE()
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * عدد المعاملات التي وصلت إلى مراحل متقدمة.
     */
    public function getProcessedDeclarationsCount(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM declarations
            WHERE status IN ('inspected', 'invoiced', 'paid', 'released')
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * عدد المعاملات التي تنتظر المعاينة.
     */
    public function getPendingInspectionsCount(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM declarations
            WHERE status = 'new'
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * إجمالي المدفوعات المسجلة اليوم.
     */
    public function getTodayRevenue(): float
    {
        $sql = "
            SELECT COALESCE(SUM(amount_paid), 0)
            FROM payments
            WHERE DATE(payment_date) = CURDATE()
        ";

        return (float) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * عدد المعاملات التي لديها معاينة بالمسار الأحمر
     * ولم تصل إلى حالة الإفراج.
     */
    public function getHeldDeclarationsCount(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM declarations d
            WHERE d.status NOT IN ('paid', 'released')
              AND EXISTS (
                  SELECT 1
                  FROM inspections i
                  WHERE i.declaration_id = d.declaration_id
                    AND i.route_color = 'red'
              )
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * آخر المعاملات المسجلة.
     */
    public function getRecentDeclarations(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        $sql = "
            SELECT
                d.declaration_id,
                d.status,
                d.created_at,
                d.origin_country,
                d.shipment_type,
                d.container_number,
                b.company_name,

                (
                    SELECT inv.total_amount
                    FROM invoices inv
                    WHERE inv.declaration_id = d.declaration_id
                    ORDER BY inv.invoice_id DESC
                    LIMIT 1
                ) AS total_amount,

                (
                    SELECT i.route_color
                    FROM inspections i
                    WHERE i.declaration_id = d.declaration_id
                    ORDER BY i.inspected_at DESC, i.inspection_id DESC
                    LIMIT 1
                ) AS latest_route_color

            FROM declarations d

            LEFT JOIN importers_brokers b
                ON b.client_id = d.client_id

            ORDER BY d.created_at DESC, d.declaration_id DESC
            LIMIT {$limit}
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}