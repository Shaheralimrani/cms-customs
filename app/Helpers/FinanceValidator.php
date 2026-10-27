<?php

class FinanceValidator
{
    /**
     * التحقق من بيانات طلب إصدار الفاتورة.
     */
    public function validate(array $data): array
    {
        $errors = [];

        $declarationId = trim((string) ($data['declaration_id'] ?? ''));
        $hsCode = trim((string) ($data['hs_code'] ?? ''));

        if ($declarationId === '') {
            $errors[] = 'رقم البيان الجمركي مطلوب.';
        } elseif (strlen($declarationId) > 30) {
            $errors[] = 'رقم البيان الجمركي غير صالح.';
        }

        if ($hsCode === '') {
            $errors[] = 'يجب اختيار HS Code.';
        } elseif (strlen($hsCode) > 50) {
            $errors[] = 'رمز HS Code غير صالح.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => [
                'declaration_id' => $declarationId,
                'hs_code' => $hsCode
            ]
        ];
    }
}
?>