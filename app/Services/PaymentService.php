<?php

declare(strict_types=1);

require_once __DIR__ . '/../Models/Payment.php';

class PaymentService
{
    private PDO $pdo;
    private Payment $model;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new Payment($pdo);
    }

    /**
     * تنفيذ عملية دفع كاملة داخل Transaction.
     */
    public function processPayment(
        array $data,
        int $accountantId
    ): array {
        if ($accountantId <= 0) {
            throw new DomainException(
                'معرّف المحاسب غير صحيح.'
            );
        }

        if ($this->pdo->inTransaction()) {
            throw new RuntimeException(
                'توجد معاملة قاعدة بيانات مفتوحة مسبقًا.'
            );
        }

        try {
            $this->pdo->beginTransaction();

            $invoice = $this->model->findInvoiceForUpdate(
                (int) $data['invoice_id']
            );

            if (!$invoice) {
                throw new DomainException(
                    'الفاتورة غير موجودة.'
                );
            }

            if ($invoice['status'] !== 'invoiced') {
                throw new DomainException(
                    'حالة البيان لا تسمح بالسداد.'
                );
            }

            if (
                $this->model->paymentExists(
                    (int) $invoice['invoice_id']
                )
            ) {
                throw new DomainException(
                    'تم تسجيل سداد هذه الفاتورة مسبقًا.'
                );
            }

            $requiredAmount = round(
                (float) $invoice['total_amount'],
                2
            );

            $paidAmount = round(
                (float) $data['amount_paid'],
                2
            );

            if ($requiredAmount <= 0) {
                throw new DomainException(
                    'قيمة الفاتورة غير صالحة.'
                );
            }

            if ($paidAmount !== $requiredAmount) {
                throw new DomainException(
                    'المبلغ المدفوع يجب أن يساوي المبلغ المستحق: '
                    . number_format($requiredAmount, 2)
                );
            }

            $method = $data['payment_method'];

            $bankName = null;
            $referenceNumber = null;

            if ($method === 'bank_transfer' || $method === 'cheque') {
                $bankName = trim(
                    (string) $data['bank_name']
                );

                $referenceNumber = trim(
                    (string) $data['reference_number']
                );

                if (
                    $bankName === '' ||
                    $referenceNumber === ''
                ) {
                    throw new DomainException(
                        'بيانات البنك والرقم المرجعي مطلوبة.'
                    );
                }
            }

            $paymentId = $this->model->createPayment(
                (int) $invoice['invoice_id'],
                $accountantId,
                $requiredAmount,
                $method,
                $bankName,
                $referenceNumber
            );

            $this->model->markDeclarationAsPaid(
                (string) $invoice['declaration_id']
            );

            $methodNames = [
                'cash' => 'نقدي',
                'bank_transfer' => 'تحويل بنكي',
                'cheque' => 'شيك'
            ];

            $action = sprintf(
                'تم تحصيل الفاتورة رقم #%d - رقم الدفع: #%d - المبلغ: %s - الطريقة: %s',
                (int) $invoice['invoice_id'],
                $paymentId,
                number_format($requiredAmount, 2),
                $methodNames[$method]
            );

            if ($referenceNumber !== null) {
                $action .= ' - مرجع: ' . $referenceNumber;
            }

            $this->model->createAuditLog(
                (string) $invoice['declaration_id'],
                $accountantId,
                $action
            );

            $this->pdo->commit();

            return [
                'success' => true,
                'payment_id' => $paymentId,
                'invoice_id' => (int) $invoice['invoice_id'],
                'amount' => $requiredAmount,
                'message' => 'تم تحصيل المبلغ بنجاح.'
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}