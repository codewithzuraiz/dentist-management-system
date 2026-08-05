<?php
// Dentists API - CRUD operations for dentists
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
$dentistId = $_GET['id'] ?? $_POST['id'] ?? null;
$dentistService = new DentistService($database);

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'name';
        $sortOrder = $_GET['sortOrder'] ?? 'asc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($dentistService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($dentistId) {
            echo json_encode($dentistService->find($dentistId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dentist ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $dentistId = $dentistService->create($data);
            echo json_encode(['success' => true, 'id' => $dentistId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($dentistId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $dentistService->update($dentistId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dentist ID required']);
        }
        break;

    case 'delete':
        if ($dentistId) {
            $result = $dentistService->delete($dentistId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dentist ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
