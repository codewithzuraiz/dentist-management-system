<?php
/**
 * GRIN DENTAL - Mobile App API
 * Entry point for all API requests
 * 
 * Usage: POST/GET to api/v1/index.php?endpoint=...
 * Headers: Authorization: Bearer {token}
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load dependencies
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../app/Database.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/Auth.php';

// Database connection
$database = new Database();
$pdo = $database->getConnection();

// Auth instance
$auth = new ApiAuth($pdo);

// Parse request
$endpoint = $_GET['endpoint'] ?? $_REQUEST['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Get token from header
$token = null;
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

// Verify token for protected routes
$user = null;
$protectedEndpoints = ['dentist/', 'patient/', 'chat/', 'upload/', 'notifications/'];
$isProtected = false;
foreach ($protectedEndpoints as $prefix) {
    if (strpos($endpoint, $prefix) === 0) {
        $isProtected = true;
        break;
    }
}

if ($isProtected) {
    if (!$token) {
        ApiResponse::unauthorized("Authorization token required");
    }
    $user = $auth->verifyToken($token);
}

// Route requests
$parts = explode('/', trim($endpoint, '/'));
$resource = $parts[0] ?? '';
$action = $parts[1] ?? '';

switch ($resource) {
    // =====================================================
    // AUTH ROUTES
    // =====================================================
    case 'auth':
        require __DIR__ . '/routes/auth.php';
        break;

    // =====================================================
    // DENTIST APP ROUTES
    // =====================================================
    case 'dentist':
        require __DIR__ . '/routes/dentist.php';
        break;

    // =====================================================
    // PATIENT APP ROUTES
    // =====================================================
    case 'patient':
        require __DIR__ . '/routes/patient.php';
        break;

    // =====================================================
    // CHAT ROUTES
    // =====================================================
    case 'chat':
        require __DIR__ . '/routes/chat.php';
        break;

    // =====================================================
    // UPLOAD ROUTES
    // =====================================================
    case 'upload':
        require __DIR__ . '/routes/upload.php';
        break;

    // =====================================================
    // NOTIFICATIONS ROUTES
    // =====================================================
    case 'notifications':
        require __DIR__ . '/routes/notifications.php';
        break;

    // =====================================================
    // DOCS
    // =====================================================
    case 'docs':
        require __DIR__ . '/docs.php';
        break;

    default:
        ApiResponse::error("Unknown endpoint: $endpoint", 404, [
            'available_endpoints' => [
                'auth/login', 'auth/register', 'auth/logout',
                'dentist/dashboard', 'dentist/appointments', 'dentist/patients',
                'dentist/patient/{id}/history', 'dentist/treatment-notes',
                'dentist/prescription', 'dentist/treatment-plans', 'dentist/schedule',
                'dentist/chat', 'dentist/notifications', 'dentist/profile',
                'patient/dashboard', 'patient/dentists', 'patient/appointments',
                'patient/medical-records', 'patient/prescriptions',
                'patient/payments', 'patient/chat', 'patient/notifications',
                'chat/conversations', 'chat/messages',
                'upload/file', 'upload/image',
                'docs'
            ]
        ]);
}
