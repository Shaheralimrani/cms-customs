<?php

require_once __DIR__ . '/../Models/Inspection.php';
require_once __DIR__ . '/../Helpers/InspectionValidator.php';

class InspectionService
{
    private PDO $pdo;

    private Inspection $model;

    private InspectionValidator $validator;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new Inspection($pdo);
        $this->validator = new InspectionValidator();
    }

    public function getDeclarations(string $search = ''): array
    {
        return $this->model->getDeclarations($search);
    }

    public function getInspectionHistory(
        string $declarationId
    ): array {
        return $this->model->getInspectionHistory(
            $declarationId
        );
    }

    public function processInspection(
        array $data,
        int $inspectorId
    ): array {
        $errors = $this->validator->validate($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode(' ', $errors)
            ];
        }

        $declarationId = trim(
            (string) $data['declaration_id']
        );

        $notes = trim(
            (string) $data['inspection_notes']
        );

        $routeColor = trim(
            (string) $data['route_color']
        );

        try {
            $this->pdo->beginTransaction();

            $declaration = $this->model
                ->findDeclarationForUpdate($declarationId);

            if (!$declaration) {
                throw new DomainException(
                    'البيان غير موجود.'
                );
            }

            $currentStatus = $declaration['status'];

            /*
             * يسمح بالمعاينة الجديدة أو الإعادة للمراجعة.
             * لا يسمح بمعاينة بيان ينتظر قرار المدير.
             */
            $allowedStatuses = [
                'new',
                'inspection_review'
            ];

            if (!in_array(
                $currentStatus,
                $allowedStatuses,
                true
            )) {
                throw new DomainException(
                    'لا يمكن معاينة البيان في حالته الحالية.'
                );
            }

            /*
             * نضيف سجلًا جديدًا في كل مرة.
             * لا نمنع المعاينات السابقة.
             */
            $this->model->createInspection(
                $declarationId,
                $inspectorId,
                $notes,
                $routeColor
            );

            if ($routeColor === 'green') {
                $newStatus = 'inspected';

                $message =
                    'اجتاز البيان المعاينة ويمكنه الانتقال للمرحلة التالية.';

            } elseif ($routeColor === 'yellow') {
                $newStatus = 'inspection_review';

                $message =
                    'تم تسجيل المسار الأصفر. يمكن إعادة معاينة البيان بعد المراجعة.';

            } else {
                $newStatus = 'pending_manager_approval';

                $message =
                    'تم إيقاف البيان وإحالته إلى المدير للمراجعة والموافقة.';
            }

            $this->model->updateDeclarationStatus(
                $declarationId,
                $newStatus
            );

            $this->model->createAuditLog(
                $declarationId,
                $inspectorId,
                'Inspection: ' . $routeColor .
                '; status changed to ' . $newStatus
            );

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => $message
            ];

        }  catch (Throwable $e) {
if ($this->pdo->inTransaction()) {
$this->pdo->rollBack();
}

error_log(
    'InspectionService error: ' .
    $e->getMessage() .
    ' in ' . $e->getFile() .
    ' on line ' . $e->getLine()
);

if ($e instanceof DomainException) {
    return [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

return [
    'success' => false,
    'message' => 'خطأ تشخيصي: ' . $e->getMessage()
];

}
    }
}