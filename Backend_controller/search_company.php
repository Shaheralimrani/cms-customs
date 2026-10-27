<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

function respond(array $data, int $status = 200): void
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

// السماح للأدوار المسموح لها بإنشاء البيانات بالبحث.
$allowedRoles = ['broker', 'manager'];

if (
    !isset($_SESSION['user_id'], $_SESSION['role'])
    || !in_array($_SESSION['role'], $allowedRoles, true)
) {
    respond([
        'error' => 'unauthorized',
        'message' => 'الجلسة منتهية أو ليس لديك صلاحية البحث.'
    ], 403);
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Company search: PDO connection is unavailable.');

    respond([
        'error' => 'database_connection',
        'message' => 'اتصال قاعدة البيانات غير متاح.'
    ], 500);
}

$query = trim((string) ($_GET['q'] ?? ''));

if (mb_strlen($query, 'UTF-8') < 2) {
    respond([]);
}

try {
    $sql = '
        SELECT client_id, company_name, phone, email
        FROM importers_brokers
        WHERE company_name LIKE :company_name
        ORDER BY company_name
        LIMIT 10
    ';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':company_name' => '%' . $query . '%',
    ]);

    respond($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Throwable $e) {
    error_log('Company search failed: ' . $e->getMessage());

    respond([
        'error' => 'search_failed',
        'message' => 'حدث خطأ أثناء البحث. راجع سجل أخطاء PHP.'
    ], 500);
}