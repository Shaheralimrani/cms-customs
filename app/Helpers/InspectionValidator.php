<?php

class InspectionValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        $declarationId = trim(
            (string) ($data['declaration_id'] ?? '')
        );

        $notes = trim(
            (string) ($data['inspection_notes'] ?? '')
        );

        $routeColor = trim(
            (string) ($data['route_color'] ?? '')
        );

        if ($declarationId === '') {
            $errors[] = 'رقم البيان مطلوب.';
        }

        if ($notes === '') {
            $errors[] = 'ملاحظات المعاينة مطلوبة.';
        } elseif (mb_strlen($notes, 'UTF-8') > 5000) {
            $errors[] = 'الملاحظات يجب ألا تتجاوز 5000 حرف.';
        }

        $allowedColors = [
            'green',
            'yellow',
            'red'
        ];

        if (!in_array($routeColor, $allowedColors, true)) {
            $errors[] = 'لون المعاينة غير صحيح.';
        }

        return $errors;
    }
}