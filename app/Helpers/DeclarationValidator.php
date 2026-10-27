<?php
declare(strict_types=1);

class DeclarationValidator
{
    public function validate(array $post): array
    {
        $errors = [];

        $data = [
            'company_name'      => trim((string)($post['company_name'] ?? '')),
            'phone'             => trim((string)($post['phone'] ?? '')),
            'email'             => trim((string)($post['email'] ?? '')),
            'existing_client_id'=> trim((string)($post['existing_client_id'] ?? '')),
            'shipment_type'     => trim((string)($post['shipment_type'] ?? '')),
            'origin_country'    => trim((string)($post['origin_country'] ?? '')),
            'total_weight'      => trim((string)($post['total_weight'] ?? '')),
            'packages_count'    => trim((string)($post['packages_count'] ?? '')),
            'declared_value'    => trim((string)($post['declared_value'] ?? '')),
            'port_of_entry'     => trim((string)($post['port_of_entry'] ?? 'منفذ الشحن البري / البحري')),
            'container_number'  => trim((string)($post['container_number'] ?? '')),
            'driver_name'       => trim((string)($post['driver_name'] ?? '')),
        ];

        if ($data['company_name'] === '') {
            $errors[] = 'اسم الشركة مطلوب.';
        }

        if (
            $data['existing_client_id'] !== ''
            && (
                !ctype_digit($data['existing_client_id'])
                || (int)$data['existing_client_id'] < 1
            )
        ) {
            $errors[] = 'معرّف الشركة غير صحيح.';
        }

        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صحيح.';
        }

        if ($data['shipment_type'] === '') {
            $errors[] = 'نوع الشحنة مطلوب.';
        }

        if ($data['origin_country'] === '') {
            $errors[] = 'بلد المنشأ مطلوب.';
        }

        if (
            $data['total_weight'] === ''
            || !is_numeric($data['total_weight'])
            || (float)$data['total_weight'] <= 0
        ) {
            $errors[] = 'الوزن الإجمالي يجب أن يكون رقمًا أكبر من صفر.';
        }

        if (
            $data['packages_count'] === ''
            || !ctype_digit($data['packages_count'])
            || (int)$data['packages_count'] < 1
        ) {
            $errors[] = 'عدد الطرود يجب أن يكون عددًا صحيحًا لا يقل عن 1.';
        }

        if (
            $data['declared_value'] === ''
            || !is_numeric($data['declared_value'])
            || (float)$data['declared_value'] < 0
        ) {
            $errors[] = 'القيمة المصرح بها يجب أن تكون رقمًا لا يقل عن صفر.';
        }

        $descriptions = $post['item_desc'] ?? [];
        $quantities   = $post['item_qty'] ?? [];
        $values       = $post['item_val'] ?? [];

        if (!is_array($descriptions)) {
            $descriptions = [];
        }

        if (!is_array($quantities)) {
            $quantities = [];
        }

        if (!is_array($values)) {
            $values = [];
        }

        $rowCount = max(
            count($descriptions),
            count($quantities),
            count($values)
        );

        $items = [];

        for ($i = 0; $i < $rowCount; $i++) {
            $description = trim((string)($descriptions[$i] ?? ''));
            $quantity    = trim((string)($quantities[$i] ?? ''));
            $value       = trim((string)($values[$i] ?? ''));

            // تجاهل الصف الفارغ بالكامل.
            if ($description === '' && $quantity === '' && $value === '') {
                continue;
            }

            $rowIsValid = true;

            if ($description === '') {
                $errors[] = 'وصف الصنف مطلوب في الصف رقم ' . ($i + 1) . '.';
                $rowIsValid = false;
            }

            if (
                $quantity === ''
                || !ctype_digit($quantity)
                || (int)$quantity < 1
            ) {
                $errors[] = 'كمية الصنف يجب أن تكون عددًا صحيحًا موجبًا في الصف رقم ' . ($i + 1) . '.';
                $rowIsValid = false;
            }

            if (
                $value === ''
                || !is_numeric($value)
                || (float)$value < 0
            ) {
                $errors[] = 'قيمة الصنف يجب أن تكون رقمًا لا يقل عن صفر في الصف رقم ' . ($i + 1) . '.';
                $rowIsValid = false;
            }

            if ($rowIsValid) {
                $items[] = [
                    'description'   => $description,
                    'quantity'      => (int)$quantity,
                    'declared_value'=> (float)$value,
                ];
            }
        }

        if (count($items) === 0) {
            $errors[] = 'يجب إضافة صنف واحد على الأقل ببيانات صحيحة.';
        }

        return [
            'errors' => array_values(array_unique($errors)),
            'data'   => $data,
            'items'  => $items,
            'valid_items_count' => count($items),
        ];
    }
}