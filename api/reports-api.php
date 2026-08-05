<?php
// Reports API - Generate various reports
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

$action = $_GET['action'] ?? $_POST['action'] ?? 'revenue';

$dashboardService = new DashboardService($database);

switch ($action) {
    case 'revenue':
        $period = $_GET['period'] ?? '30';
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-' . $period . ' days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue FROM invoices WHERE created_at BETWEEN ? AND ? AND status IN ('paid', 'partially_paid') GROUP BY DATE(created_at) ORDER BY created_at";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        $revenueData = $stmt->fetchAll();
        
        echo json_encode(['revenue' => $revenueData]);
        break;

    case 'appointments':
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT DATE(appointment_date) as date, status, COUNT(*) as count FROM appointments WHERE appointment_date BETWEEN ? AND ? GROUP BY DATE(appointment_date), status ORDER BY appointment_date";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $appointmentData = $stmt->fetchAll();
        
        echo json_encode(['appointments' => $appointmentData]);
        break;

    case 'patients':
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT DATE(registration_date) as date, COUNT(*) as count FROM patients WHERE registration_date BETWEEN ? AND ? GROUP BY DATE(registration_date) ORDER BY registration_date";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $patientData = $stmt->fetchAll();
        
        echo json_encode(['patients' => $patientData]);
        break;

    case 'treatments':
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT t.name, COUNT(*) as count, SUM(ii.subtotal) as revenue FROM invoice_items ii JOIN treatments t ON ii.treatment_id = t.id JOIN invoices i ON ii.invoice_id = i.id WHERE i.created_at BETWEEN ? AND ? AND i.status IN ('paid', 'partially_paid') GROUP BY t.name ORDER BY count DESC";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $treatmentData = $stmt->fetchAll();
        
        echo json_encode(['treatments' => $treatmentData]);
        break;

    case 'dentistPerformance':
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT d.id, d.name, d.specialization, COUNT(a.id) as appointments, COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed, SUM(i.total_amount) as revenue FROM dentists d LEFT JOIN appointments a ON d.id = a.dentist_id LEFT JOIN invoices i ON a.id = i.appointment_id WHERE a.appointment_date BETWEEN ? AND ? OR a.appointment_date IS NULL GROUP BY d.id ORDER BY revenue DESC";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $dentistData = $stmt->fetchAll();
        
        echo json_encode(['dentistPerformance' => $dentistData]);
        break;

    case 'paymentSummary':
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ? GROUP BY payment_method ORDER BY total DESC";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $paymentData = $stmt->fetchAll();
        
        echo json_encode(['paymentSummary' => $paymentData]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
