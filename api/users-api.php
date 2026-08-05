<?php
// Users API - CRUD operations for users with authentication
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
$userId = $_GET['id'] ?? $_POST['id'] ?? null;
$userService = new User($database);

$userService->db = $database->getConnection();

switch ($action) {
    case 'getAll':
        $filters = $_GET;
        unset($filters['action']);
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sortBy'] ?? 'name';
        $sortOrder = $_GET['sortOrder'] ?? 'asc';
        $limit = $_GET['limit'] ?? null;
        $offset = $_GET['offset'] ?? null;
        echo json_encode($userService->getAll($filters, $search, $sortBy, $sortOrder, $limit, $offset));
        break;

    case 'getById':
        if ($userId) {
            echo json_encode($userService->find($userId));
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User ID required']);
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $userId = $userService->create($data);
            echo json_encode(['success' => true, 'id' => $userId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
        }
        break;

    case 'update':
        if ($userId) {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $result = $userService->update($userId, $data);
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User ID required']);
        }
        break;

    case 'delete':
        if ($userId) {
            $result = $userService->delete($userId);
            echo json_encode(['success' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User ID required']);
        }
        break;

    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data && isset($data['email']) && isset($data['password'])) {
            $user = $userService->findByEmail($data['email']);
            if ($user && password_verify($data['password'], $user['password'])) {
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];

                $userService->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password required']);
        }
        break;

    case 'logout':
        session_start();
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    case 'checkSession':
        session_start();
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo json_encode(['loggedIn' => true, 'userId' => $_SESSION['user_id'], 'role' => $_SESSION['user_role'], 'email' => $_SESSION['user_email'], 'name' => $_SESSION['user_name']]);
        } else {
            echo json_encode(['loggedIn' => false]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
