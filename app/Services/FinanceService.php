<?php

require_once __DIR__ . '/../Models/Finance.php';

class FinanceService
{
    private PDO $pdo;
    private Finance $finance;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->finance = new Finance($pdo);
    }

    /**
     * احتساب الرسوم وإصدار الفاتورة.
     */
    public function issueInvoice(
        string $declarationId,
        string $hsCode,
        int $accountantId
    ): array {
        if ($this->pdo->inTransaction()) {
            throw new RuntimeException(
                'توجد معاملة قاعدة بيانات مفتوحة مسبقًا.'
            );
        }

        try {
            $this->pdo->beginTransaction();

            // 1. جلب البيان وقفل سجله.
            $declaration = $this->finance
                ->findDeclarationForUpdate($declarationId);

            if (!$declaration) {
                throw new DomainException(
                    'البيان الجمركي غير موجود.'
                );
            }

            // 2. لا نقبل إلا البيانات المفحوصة والمؤهلة ماليًا.
            if ($declaration['status'] !== 'inspected') {
                throw new DomainException(
                    'لا يمكن إصدار فاتورة لهذا البيان. يجب أن تكون حالته inspected.'
                );
            }

            // 3. التحقق من القيمة المصرح بها.
            $declaredValue = (float) $declaration['declared_value'];

            if (!is_finite($declaredValue) || $declaredValue <= 0) {
                throw new DomainException(
                    'القيمة المصرح بها غير صحيحة.'
                );
            }

            // 4. جلب التعريفة من قاعدة البيانات.
            $tariff = $this->finance->findTariff($hsCode);

            if (!$tariff) {
                throw new DomainException(
                    'رمز HS Code غير موجود في جدول التعريفات الجمركية.'
                );
            }

            $customsRate = (float) $tariff['customs_duty_rate'];
            $taxRate = (float) $tariff['tax_rate'];

            // 5. التحقق من صحة النسب.
            if (
                !is_finite($customsRate) ||
                !is_finite($taxRate) ||
                $customsRate < 0 ||
                $taxRate < 0 ||
                $customsRate > 100 ||
                $taxRate > 100
            ) {
                throw new DomainException(
                    'نسب الرسوم أو الضرائب في جدول التعريفات غير صحيحة.'
                );
            }

            // 6. منع إصدار فاتورة ثانية للبيان نفسه.
            if ($this->finance->invoiceExists($declarationId)) {
                throw new DomainException(
                    'تم إصدار فاتورة لهذا البيان مسبقًا.'
                );
            }

            // 7. الحساب الحقيقي على الخادم.
            $dutyAmount = round(
                $declaredValue * $customsRate / 100,
                2
            );

            $taxAmount = round(
                $declaredValue * $taxRate / 100,
                2
            );

            $totalAmount = round(
                $dutyAmount + $taxAmount,
                2
            );

            // 8. إنشاء الفاتورة.
            $invoiceId = $this->finance->createInvoice(
                $declarationId,
                $hsCode,
                $dutyAmount,
                $taxAmount,
                $totalAmount
            );

            // 9. تغيير الحالة.
            $this->finance->markAsInvoiced($declarationId);

            // 10. تسجيل العملية.
            $this->finance->logInvoice(
                $declarationId,
                $accountantId,
                $hsCode,
                $dutyAmount,
                $taxAmount,
                $totalAmount
            );

            // 11. اعتماد جميع التغييرات.
            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'تم احتساب الرسوم وإصدار الفاتورة بنجاح.',
                'invoice_id' => $invoiceId,
                'declaration_id' => $declarationId,
                'hs_code' => $hsCode,
                'duty_amount' => $dutyAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount
            ];

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
?>