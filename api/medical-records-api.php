<?php
// Medical Records API - CRUD operations for medical records
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
$medicalRecordId = $_GET['id'] ?? $_POST['id'] ?? null;
$medicalRecordService = new MedicalRecordService($database);

$medicalRecordService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'created_at';
        $sortOrder = $_GET['sortOrder'] ?? 'desc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($medicalRecordService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($medicalRecordId) {
            echo json_encode($medicalRecordService->find($medicalRecordId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Medical Record ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $medicalRecordId = $medicalRecordService->create($data);
            echo json_encode(['success' => true, 'id' => $medicalRecordId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($medicalRecordId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $medicalRecordService->update($medicalRecordId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Medical Record ID required']);
        }
        break;

    case 'delete':
        if ($medicalRecordId) {
            $result = $medicalRecordService->delete($medicalRecordId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Medical Record ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
