<?php
// Dashboard API - Real-time data for the admin dashboard
header('Content-Type: application/json');

require_once __DIR__ . '/../app/app.php';

function handleCors() {
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        exit(0);
    }
}

handleCors();

$action = $_GET['action'] ?? $_POST['action'] ?? 'getStats';

$dashboardService = new DashboardService($database);

switch ($action) {
    case 'getStats':
        echo json_encode($dashboardService->getDashboardStats());
        break;

    case 'getMonthlyRevenue':
        $period = $_GET['period'] ?? '365';
        echo json_encode($dashboardService->getMonthlyRevenueData($period));
        break;

    case 'getAppointmentsOverview':
        echo json_encode($dashboardService->getAppointmentsOverview());
        break;

    case 'getMostCommonTreatments':
        $limit = $_GET['limit'] ?? 6;
        echo json_encode($dashboardService->getMostCommonTreatments($limit));
        break;

    case 'getRecentActivities':
        $limit = $_GET['limit'] ?? 6;
        echo json_encode($dashboardService->getRecentActivities($limit));
        break;

    case 'getUpcomingAppointments':
        $limit = $_GET['limit'] ?? 5;
        echo json_encode($dashboardService->getUpcomingAppointments($limit));
        break;

    case 'getRevenueSummary':
        echo json_encode($dashboardService->getRevenueSummary());
        break;

    case 'getCompletedTreatmentsCount':
        echo json_encode(['count' => $dashboardService->getCompletedTreatmentsCount()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
