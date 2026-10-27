<?php

require_once __DIR__ . '/../Models/ReleasePermit.php';
require_once __DIR__ . '/../Helpers/ReleasePermitValidator.php';

class ReleasePermitService
{
    private PDO $pdo;
    private ReleasePermit $model;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new ReleasePermit($pdo);
    }

    public function issuePermit(
        array $data,
        int $userId
    ): array {
        $errors = ReleasePermitValidator::validate($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        if ($userId < 1) {
            return [
                'success' => false,
                'errors' => ['المستخدم غير صالح.']
            ];
        }

        $declarationId = trim(
            (string) $data['declaration_id']
        );

        $paymentId = (int) $data['payment_id'];

        try {
            $this->pdo->beginTransaction();

            $eligible = $this->model->findEligibleForUpdate(
                $declarationId,
                $paymentId
            );

            if (!$eligible) {
                throw new RuntimeException(
                    'البيان غير مؤهل للإفراج أو أن عملية الدفع لا تخص فاتورته.'
                );
            }

            if ($this->model->permitExists($declarationId)) {
                throw new RuntimeException(
                    'سبق إصدار إذن إفراج لهذا البيان.'
                );
            }

            $invoiceTotal = round(
                (float) $eligible['total_amount'],
                2
            );

            $paymentAmount = round(
                (float) $eligible['payment_amount'],
                2
            );

            if (abs($invoiceTotal - $paymentAmount) > 0.01) {
                throw new RuntimeException(
                    'المبلغ المدفوع لا يطابق إجمالي الفاتورة.'
                );
            }

            $permitId = 'PERMIT-' .
                date('Y') . '-' .
                strtoupper(bin2hex(random_bytes(4)));

            $this->model->createPermit(
                $permitId,
                $declarationId,
                $paymentId,
                $userId
            );

            if (!$this->model->markReleased($declarationId)) {
                throw new RuntimeException(
                    'تعذر تحديث حالة البيان إلى released.'
                );
            }

            $action = sprintf(
                'تم إصدار إذن الإفراج رقم %s للبيان %s باستخدام عملية الدفع رقم %d.',
                $permitId,
                $declarationId,
                $paymentId
            );

            $this->model->createAuditLog(
                $declarationId,
                $userId,
                $action
            );

            $this->pdo->commit();

            return [
                'success' => true,
                'permit_id' => $permitId,
                'declaration_id' => $declarationId
            ];

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log(
                'Release permit error: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }
}