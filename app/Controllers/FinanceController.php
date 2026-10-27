<?php

require_once __DIR__ . '/../Helpers/FinanceValidator.php';
require_once __DIR__ . '/../Services/FinanceService.php';

class FinanceController
{
    private FinanceValidator $validator;
    private FinanceService $service;

    public function __construct(PDO $pdo)
    {
        $this->validator = new FinanceValidator();
        $this->service = new FinanceService($pdo);
    }

    /**
     * معالجة طلب إصدار فاتورة.
     */
    public function issueInvoice(
        array $data,
        int $accountantId
    ): array {
        // 1. التحقق من المدخلات.
        $validation = $this->validator->validate($data);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        $validatedData = $validation['data'];

        // 2. تنفيذ العملية المالية.
        try {
            $result = $this->service->issueInvoice(
                $validatedData['declaration_id'],
                $validatedData['hs_code'],
                $accountantId
            );

            return $result;

        } catch (DomainException $e) {
            // أخطاء متوقعة مثل عدم وجود البيان
            // أو عدم أهليته لإصدار الفاتورة.
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];

        } catch (Throwable $e) {
            // تسجيل التفاصيل في سجل PHP بدل كشفها للمستخدم.
            error_log(
                'FinanceController::issueInvoice: ' .
                $e->getMessage()
            );

            return [
                'success' => false,
                'errors' => [
                    'حدث خطأ أثناء إصدار الفاتورة. راجع سجل أخطاء PHP.'
                ]
            ];
        }
    }
}
?>