<?php
// Payments API - CRUD operations for payments
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
$paymentId = $_GET['id'] ?? $_POST['id'] ?? null;
$paymentService = new PaymentService($database);

$paymentService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'payment_date';
        $sortOrder = $_GET['sortOrder'] ?? 'desc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($paymentService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($paymentId) {
            echo json_encode($paymentService->find($paymentId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Payment ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $paymentId = $paymentService->create($data);
            echo json_encode(['success' => true, 'id' => $paymentId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($paymentId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $paymentService->update($paymentId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Payment ID required']);
        }
        break;

    case 'delete':
        if ($paymentId) {
            $result = $paymentService->delete($paymentId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Payment ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
