<?php
// Invoices API - CRUD operations for invoices
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
$invoiceId = $_GET['id'] ?? $_POST['id'] ?? null;
$invoiceService = new InvoiceService($database);

$invoiceService->db = $database->getConnection();

if ($action === 'updateStatus' && $invoiceId) {
    $invoiceService->updateStatus($invoiceId);
    echo json_encode(['success' => true]);
    exit;
}

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'created_at';
        $sortOrder = $_GET['sortOrder'] ?? 'desc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($invoiceService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($invoiceId) {
            echo json_encode($invoiceService->find($invoiceId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invoice ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $invoiceId = $invoiceService->create($data);
            echo json_encode(['success' => true, 'id' => $invoiceId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($invoiceId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $invoiceService->update($invoiceId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invoice ID required']);
        }
        break;

    case 'delete':
        if ($invoiceId) {
            $result = $invoiceService->delete($invoiceId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invoice ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
