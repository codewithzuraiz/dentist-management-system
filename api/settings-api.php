<?php
// Settings API - Manage system settings
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
$settingId = 1; // Only one settings record
$settingService = new SystemSetting($database);

$settingService->db = $database->getConnection();

$settingService->id = $settingId;

switch ($action) {
    case 'getAll':
        echo json_encode($settingService->find($settingId));
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $result = $settingService->update($settingId, $data);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'getByKey':
        $key = $_GET['key'] ?? null;
        if ($key) {
            $setting = $settingService->find($settingId);
            echo json_encode(['value' => $setting[$key] ?? null]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Setting key required']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
