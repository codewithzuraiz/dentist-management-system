<?php
// Appointments API - CRUD operations for appointments
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

$action = $_GET['action'] ?? $_POST['action'] ?? 'getAll';
$appointmentId = $_GET['id'] ?? $_POST['id'] ?? null;
$appointmentService = new AppointmentService($database);

$appointmentService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'appointment_date';
        $sortOrder = $_GET['sortOrder'] ?? 'desc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($appointmentService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($appointmentId) {
            echo json_encode($appointmentService->find($appointmentId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $appointmentId = $appointmentService->create($data);
            echo json_encode(['success' => true, 'id' => $appointmentId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($appointmentId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $appointmentService->update($appointmentId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID required']);
        }
        break;

    case 'delete':
        if ($appointmentId) {
            $result = $appointmentService->delete($appointmentId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID required']);
        }
        break;

    case 'checkAvailability':
        $dentistId = $_GET['dentist_id'] ?? $_POST['dentist_id'];
        $appointmentDate = $_GET['date'] ?? $_POST['date'];
        $startTime = $_GET['start_time'] ?? $_POST['start_time'];
        $endTime = $_GET['end_time'] ?? $_POST['end_time'];
        
        if ($dentistId && $appointmentDate && $startTime && $endTime) {
            $available = $appointmentService->checkAvailability($dentistId, $appointmentDate, $startTime, $endTime);
            echo json_encode(['available' => $available]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
