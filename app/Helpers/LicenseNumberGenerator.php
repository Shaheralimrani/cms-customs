<?php
declare(strict_types=1);

class LicenseNumberGenerator
{
    public function generate(PDO $pdo): string
    {
        $check = $pdo->prepare(
            'SELECT client_id
             FROM importers_brokers
             WHERE license_number = :license_number
             LIMIT 1'
        );

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $year = date('Y');

            $random = strtoupper(bin2hex(random_bytes(4)));

            $licenseNumber = 'LIC-' . $year . '-' . $random;

            $check->execute([
                ':license_number' => $licenseNumber,
            ]);

            if ($check->fetchColumn() === false) {
                return $licenseNumber;
            }
        }

        throw new RuntimeException('تعذر إنشاء رقم ترخيص فريد.');
    }
}