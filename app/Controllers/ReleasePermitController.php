<?php

require_once __DIR__ . '/../Models/ReleasePermit.php';
require_once __DIR__ . '/../Services/ReleasePermitService.php';

class ReleasePermitController
{
    private ReleasePermit $model;
    private ReleasePermitService $service;

    public function __construct(PDO $pdo)
    {
        $this->model = new ReleasePermit($pdo);
        $this->service = new ReleasePermitService($pdo);
    }

    public function index(): array
    {
        return [
            'permits' => $this->model->getAllPermits(),
            'ready_for_release' => $this->model->getReadyForRelease()
        ];
    }
    /**
 * جلب بيانات إذن الإفراج للطباعة.
 */
public function print(string $permitId): ?array
{
    return $this->model->getPermitForPrint($permitId);
}

    public function issue(
        array $data,
        int $userId
    ): array {
        return $this->service->issuePermit(
            $data,
            $userId
        );
    }
}