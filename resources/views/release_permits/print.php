<?php

require_once __DIR__ . '/../../../app/Helpers/ReleasePermitPrintHelper.php';

$permitNumber = $permit['permit_id'];
$declarationId = $permit['declaration_id'];
$companyName = $permit['company_name'] ?: 'غير محدد';
$issuerName = $permit['issuer_name'] ?: 'غير محدد';
$shipmentType = $permit['shipment_type'] ?: 'غير محدد';
$originCountry = $permit['origin_country'] ?: 'غير محدد';
$containerNumber = $permit['container_number'] ?: 'غير محدد';
$paymentMethod = $permit['payment_method'] ?: 'غير محدد';
$routeColor = $permit['route_color'] ?: 'غير محدد';
$issuedAt = permitFormatDate($permit['issued_at']);
$paymentDate = permitFormatDate($permit['payment_date']);
$inspectionDate = permitFormatDate($permit['inspected_at']);
$invoiceTotal = $permit['invoice_total'];
$paymentAmount = $permit['payment_amount'];
$dutyAmount = $permit['duty_amount'];
$taxAmount = $permit['tax_amount'];
$shipmentWeight = $permit['total_weight'];
$packagesCount = $permit['packages_count'];
$declaredValue = $permit['declared_value'];
$invoiceId = $permit['invoice_id'];
$paymentId = $permit['payment_id'];
$companyLicense = $permit['license_number'] ?: 'غير محدد';
$companyPhone = $permit['phone'] ?: 'غير محدد';
$companyEmail = $permit['email'] ?: 'غير محدد';
$issuerRole = $permit['issuer_role'] ?: 'manager';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إذن الإفراج الجمركي - <?= permitEscape($permitNumber) ?></title>
    <link rel="stylesheet" href="assets/css/pages/release-permit-print.css">
</head>
<body>
<?php require __DIR__ . '/partials/_toolbar.php'; ?>
<main class="document">
<?php require __DIR__ . '/partials/_permit_header.php'; ?>
<?php require __DIR__ . '/partials/_declaration.php'; ?>
<?php require __DIR__ . '/partials/_importer.php'; ?>
<?php require __DIR__ . '/partials/_inspection.php'; ?>
<?php require __DIR__ . '/partials/_financial.php'; ?>
<?php require __DIR__ . '/partials/_decision.php'; ?>
<?php require __DIR__ . '/partials/_signatures.php'; ?>
<?php require __DIR__ . '/partials/_footer.php'; ?>
</main>
</body>
</html>