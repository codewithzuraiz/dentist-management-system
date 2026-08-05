<?php
// Notifications API - CRUD operations for notifications
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
$notificationId = $_GET['id'] ?? $_POST['id'] ?? null;
$notificationService = new NotificationService($database);

$notificationService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'created_at';
        $sortOrder = $_GET['sortOrder'] ?? 'desc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($notificationService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($notificationId) {
            echo json_encode($notificationService->find($notificationId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $notificationId = $notificationService->create($data);
            echo json_encode(['success' => true, 'id' => $notificationId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($notificationId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $notificationService->update($notificationId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
        }
        break;

    case 'markAsRead':
        if ($notificationId) {
            $notificationService->markAsRead($notificationId);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
        }
        break;

    case 'send':
        if ($notificationId) {
            $notificationService->send($notificationId);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
        }
        break;

    case 'delete':
        if ($notificationId) {
            $result = $notificationService->delete($notificationId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
