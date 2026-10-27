<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/Dashboard.php';

class DashboardService
{
    private Dashboard $dashboard;

    public function __construct(PDO $pdo)
    {
        $this->dashboard = new Dashboard($pdo);
    }

    public function getDashboardData(string $role): array
    {
        $data = [
            'role' => $role,
            'stats' => [],
            'recent_declarations' => [],
        ];

        switch ($role) {
            case 'manager':
                $data['stats'] = [
                    'today_declarations' =>
                        $this->dashboard->getTodayDeclarationsCount(),

                    'processed_declarations' =>
                        $this->dashboard->getProcessedDeclarationsCount(),

                    'pending_inspections' =>
                        $this->dashboard->getPendingInspectionsCount(),

                    'today_revenue' =>
                        $this->dashboard->getTodayRevenue(),

                    'held_declarations' =>
                        $this->dashboard->getHeldDeclarationsCount(),
                ];

                $data['recent_declarations'] =
                    $this->dashboard->getRecentDeclarations(10);

                break;

            case 'broker':
                // لا نعرض بيانات جميع العملاء أو إحصائياتهم هنا.
                $data['stats'] = [];

                break;

            case 'inspector':
                $data['stats'] = [
                    'pending_inspections' =>
                        $this->dashboard->getPendingInspectionsCount(),

                    'held_declarations' =>
                        $this->dashboard->getHeldDeclarationsCount(),
                ];

                break;

            case 'accountant':
                $data['stats'] = [
                    'today_revenue' =>
                        $this->dashboard->getTodayRevenue(),
                ];

                break;

            default:
                throw new InvalidArgumentException(
                    'الدور الوظيفي غير معتمد.'
                );
        }

        return $data;
    }
}