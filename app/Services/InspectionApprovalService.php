<?php

require_once __DIR__ . '/../Models/InspectionApproval.php';

class InspectionApprovalService
{
    private PDO $pdo;

    private InspectionApproval $model;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new InspectionApproval($pdo);
    }

    public function getPendingApprovals(): array
    {
        return $this->model->getPendingApprovals();
    }

    public function processDecision(
        array $data,
        int $managerId
    ): array {
        $declarationId = trim(
            (string) ($data['declaration_id'] ?? '')
        );

        $decision = trim(
            (string) ($data['decision'] ?? '')
        );

        $managerNotes = trim(
            (string) ($data['manager_notes'] ?? '')
        );

        if ($declarationId === '') {
            return [
                'success' => false,
                'message' => 'رقم البيان مطلوب.'
            ];
        }

        if (!in_array(
            $decision,
            ['reinspect', 'hold'],
            true
        )) {
            return [
                'success' => false,
                'message' => 'قرار المدير غير صالح.'
            ];
        }

        if (mb_strlen($managerNotes, 'UTF-8') > 5000) {
            return [
                'success' => false,
                'message' => 'ملاحظات المدير طويلة جدًا.'
            ];
        }

        try {
            $this->pdo->beginTransaction();

            $declaration = $this->model->findForUpdate(
                $declarationId
            );

            if (!$declaration) {
                throw new DomainException(
                    'البيان غير موجود.'
                );
            }

            if (
                $declaration['status'] !==
                'pending_manager_approval'
            ) {
                throw new DomainException(
                    'هذا البيان لا ينتظر موافقة المدير.'
                );
            }

            if ($decision === 'reinspect') {
                /*
                 * المدير يسمح بإعادة المعاينة.
                 * لا يعني ذلك الإفراج عن الشحنة.
                 */
                $newStatus = 'inspection_review';

                $action = 'Manager approved reinspection';

                $message =
                    'وافق المدير على إعادة المعاينة.';

            } else {
                /*
                 * يبقى البيان موقوفًا.
                 */
                $newStatus = 'pending_manager_approval';

                $action = 'Manager kept declaration on hold';

                $message =
                    'تم تسجيل قرار الإبقاء على البيان موقوفًا.';
            }

            $this->model->updateStatus(
                $declarationId,
                $newStatus
            );

            $fullAction = $action;

            if ($managerNotes !== '') {
                $fullAction .= ' | Notes: ' . $managerNotes;
            }

            $this->model->createAuditLog(
                $declarationId,
                $managerId,
                $fullAction
            );

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($e instanceof DomainException) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            error_log(
                'InspectionApprovalService error: ' .
                $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'تعذر تسجيل قرار المدير.'
            ];
        }
    }
}