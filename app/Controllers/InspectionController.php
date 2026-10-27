<?php

require_once __DIR__ . '/../Services/InspectionService.php';

class InspectionController
{
    private InspectionService $service;

    public function __construct(PDO $pdo)
    {
        $this->service = new InspectionService($pdo);
    }

    public function index(string $search = ''): array
    {
        return [
            'declarations' => $this->service
                ->getDeclarations($search),
            'search' => $search
        ];
    }

    public function process(
        array $data,
        int $inspectorId
    ): array {
        return $this->service->processInspection(
            $data,
            $inspectorId
        );
    }

    public function history(string $declarationId): array
    {
        return $this->service->getInspectionHistory(
            $declarationId
        );
    }
}