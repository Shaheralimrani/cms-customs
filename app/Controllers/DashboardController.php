<?php
declare(strict_types=1);

require_once __DIR__ . '/../Services/DashboardService.php';

class DashboardController
{
    private DashboardService $dashboardService;

    public function __construct(PDO $pdo)
    {
        $this->dashboardService = new DashboardService($pdo);
    }

    public function index(string $role): array
    {
        return $this->dashboardService->getDashboardData($role);
    }
}