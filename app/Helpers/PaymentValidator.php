<?php

declare(strict_types=1);

class PaymentValidator
{
    private const ALLOWED_METHODS = [
        'cash',
        'bank_transfer',
        'cheque'
    ];

    public function validate(array $data): array
    {
        $errors = [];

        $invoiceId = filter_var(
            $data['invoice_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $amountRaw = trim((string) ($data['amount_paid'] ?? ''));

        $paymentMethod = trim(
            (string) ($data['payment_method'] ?? '')
        );

        $bankName = trim(
            (string) ($data['bank_name'] ?? '')
        );

        $referenceNumber = trim(
            (string) ($data['reference_number'] ?? '')
        );

        if ($invoiceId === false || $invoiceId <= 0) {
            $errors[] = 'رقم الفاتورة غير صحيح.';
        }

        if (
            $amountRaw === '' ||
            !is_numeric($amountRaw) ||
            !is_finite((float) $amountRaw) ||
            (float) $amountRaw <= 0
        ) {
            $errors[] = 'المبلغ المدفوع يجب أن يكون رقمًا أكبر من صفر.';
        }

        if (!in_array(
            $paymentMethod,
            self::ALLOWED_METHODS,
            true
        )) {
            $errors[] = 'طريقة الدفع غير صحيحة.';
        }

        if (in_array(
            $paymentMethod,
            ['bank_transfer', 'cheque'],
            true
        )) {
            if ($bankName === '') {
                $errors[] = 'يجب إدخال اسم البنك.';
            }

            if ($referenceNumber === '') {
                $errors[] = 'يجب إدخال الرقم المرجعي أو رقم الشيك.';
            }
        }

        if (strlen($bankName) > 150) {
            $errors[] = 'اسم البنك طويل جدًا.';
        }

        if (strlen($referenceNumber) > 150) {
            $errors[] = 'الرقم المرجعي طويل جدًا.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => [
                'invoice_id' => $invoiceId === false
                    ? 0
                    : (int) $invoiceId,

                'amount_paid' => is_numeric($amountRaw)
                    ? (float) $amountRaw
                    : 0.0,

                'payment_method' => $paymentMethod,
                'bank_name' => $bankName,
                'reference_number' => $referenceNumber
            ]
        ];
    }
}