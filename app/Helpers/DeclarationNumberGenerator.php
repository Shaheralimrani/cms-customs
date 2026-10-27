<?php
declare(strict_types=1);

class DeclarationNumberGenerator
{
    public function generate(PDO $pdo): string
    {
        $check = $pdo->prepare(
            'SELECT declaration_id
             FROM declarations
             WHERE declaration_id = :declaration_id
             LIMIT 1'
        );

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $year = date('Y');

            $random = strtoupper(bin2hex(random_bytes(4)));

            $declarationId = 'DEC-' . $year . '-' . $random;

            $check->execute([
                ':declaration_id' => $declarationId,
            ]);

            if ($check->fetchColumn() === false) {
                return $declarationId;
            }
        }

        throw new RuntimeException('تعذر إنشاء رقم بيان فريد.');
    }
}