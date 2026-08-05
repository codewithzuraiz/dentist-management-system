<?php
// Treatments API - CRUD operations for treatments
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
$treatmentId = $_GET['id'] ?? $_POST['id'] ?? null;
$treatmentService = new TreatmentService($database);

$treatmentService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'name';
        $sortOrder = $_GET['sortOrder'] ?? 'asc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($treatmentService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($treatmentId) {
            echo json_encode($treatmentService->find($treatmentId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Treatment ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $treatmentId = $treatmentService->create($data);
            echo json_encode(['success' => true, 'id' => $treatmentId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($treatmentId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $treatmentService->update($treatmentId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Treatment ID required']);
        }
        break;

    case 'delete':
        if ($treatmentId) {
            $result = $treatmentService->delete($treatmentId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Treatment ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
