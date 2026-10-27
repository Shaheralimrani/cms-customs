<?php

class ReleasePermitValidator
{
    public static function validate(
        array $data
    ): array {
        $errors = [];

        $declarationId = trim(
            (string) ($data['declaration_id'] ?? '')
        );

        $paymentId = filter_var(
            $data['payment_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $declarationId === '' ||
            strlen($declarationId) > 30
        ) {
            $errors[] = 'رقم البيان الجمركي غير صالح.';
        }

        if ($paymentId === false || $paymentId < 1) {
            $errors[] = 'رقم عملية الدفع غير صالح.';
        }

        return $errors;
    }
}