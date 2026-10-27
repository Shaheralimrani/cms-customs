<?php

declare(strict_types=1);

require_once __DIR__ . '/../Helpers/PaymentValidator.php';
require_once __DIR__ . '/../Services/PaymentService.php';

class PaymentController
{
    private PaymentValidator $validator;
    private PaymentService $service;

    public function __construct(PDO $pdo)
    {
        $this->validator = new PaymentValidator();
        $this->service = new PaymentService($pdo);
    }

    /**
     * تنفيذ طلب الدفع.
     */
    public function process(
        array $data,
        int $accountantId
    ): array {
        $validation = $this->validator->validate($data);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        try {
            $result = $this->service->processPayment(
                $validation['data'],
                $accountantId
            );

            return $result;
        } catch (DomainException $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        } catch (Throwable $e) {
            error_log(
                'Payment processing error: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'errors' => [
                    'تعذر إكمال عملية الدفع. تحقق من البيانات أو سجل النظام.'
                ]
            ];
        }
    }
}