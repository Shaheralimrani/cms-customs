<?php
declare(strict_types=1);

require_once __DIR__ . '/../Services/InspectionApprovalService.php';

class InspectionApprovalController
{
    private InspectionApprovalService $service;

    public function __construct(PDO $pdo)
    {
        $this->service = new InspectionApprovalService($pdo);
    }

    public function index(): array
    {
        return [
            'approvals' => $this->service->getPendingApprovals()
        ];
    }

    public function process(array $data, int $managerId): array
    {
        return $this->service->processDecision($data, $managerId);
    }
}