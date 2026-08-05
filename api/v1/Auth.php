<?php
/**
 * API Authentication - Token Based
 * Handles login, register, token generation/verification
 */

class ApiAuth {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Generate a random API token
     */
    public function generateToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Register a new user (patient or dentist)
     */
    public function register($data) {
        // Validate required fields
        $required = ['name', 'email', 'phone', 'password', 'role'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                ApiResponse::error("Missing field: $field");
            }
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            ApiResponse::error("Email already registered");
        }

        // Determine role ID
        $roleMap = ['patient' => 4, 'dentist' => 3];
        $roleId = $roleMap[$data['role']] ?? null;
        if (!$roleId) {
            ApiResponse::error("Invalid role");
        }

        // Create user
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (role_id, name, email, phone, password, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$roleId, $data['name'], $data['email'], $data['phone'], $password]);
        $userId = $this->db->lastInsertId();

        // Create patient or dentist record
        if ($data['role'] === 'patient') {
            $stmt = $this->db->prepare("INSERT INTO patients (user_id, gender, date_of_birth, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $data['gender'] ?? 'male', $data['date_of_birth'] ?? null, $data['address'] ?? null]);
        } elseif ($data['role'] === 'dentist') {
            $stmt = $this->db->prepare("INSERT INTO dentists (user_id, specialization, qualification) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $data['specialization'] ?? 'General', $data['qualification'] ?? '']);
        }

        // Generate token
        return $this->createToken($userId, $data);
    }

    /**
     * Login user and return token
     */
    public function login($email, $password, $deviceInfo = []) {
        if (empty($email) || empty($password)) {
            ApiResponse::error("Email and password required");
        }

        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ? AND u.status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            ApiResponse::error("Invalid email or password", 401);
        }

        // Update last login
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        return $this->createToken($user['id'], [
            'role' => $user['role_name'],
            'device_info' => $deviceInfo
        ]);
    }

    /**
     * Create and store token
     */
    private function createToken($userId, $data) {
        $token = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_DAYS . ' days'));
        $deviceType = $data['device_info']['type'] ?? 'android';
        $deviceToken = $data['device_info']['token'] ?? null;
        $deviceName = $data['device_info']['name'] ?? null;

        // Revoke old tokens for this device
        if ($deviceToken) {
            $stmt = $this->db->prepare("UPDATE api_tokens SET status = 'revoked' WHERE user_id = ? AND device_token = ?");
            $stmt->execute([$userId, $deviceToken]);
        }

        $stmt = $this->db->prepare("INSERT INTO api_tokens (user_id, token, device_type, device_token, device_name, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $token, $deviceType, $deviceToken, $deviceName, $expiresAt]);

        // Get user info
        $stmt = $this->db->prepare("SELECT u.id, u.name, u.email, u.phone, u.profile_image, r.name as role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        return [
            'token' => $token,
            'expires_at' => $expiresAt,
            'user' => $user
        ];
    }

    /**
     * Verify token and return user
     */
    public function verifyToken($token) {
        if (empty($token)) {
            ApiResponse::unauthorized("Token required");
        }

        $stmt = $this->db->prepare("SELECT t.*, u.id as uid, u.name, u.email, u.phone, u.profile_image, u.status as user_status, r.name as role FROM api_tokens t JOIN users u ON t.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE t.token = ? AND t.status = 'active' AND t.expires_at > NOW()");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();

        if (!$tokenData) {
            ApiResponse::unauthorized("Invalid or expired token");
        }

        if ($tokenData['user_status'] !== 'active') {
            ApiResponse::unauthorized("Account is suspended");
        }

        // Update last activity
        $stmt = $this->db->prepare("UPDATE api_tokens SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$tokenData['id']]);

        return [
            'user_id' => $tokenData['uid'],
            'name' => $tokenData['name'],
            'email' => $tokenData['email'],
            'phone' => $tokenData['phone'],
            'profile_image' => $tokenData['profile_image'],
            'role' => $tokenData['role']
        ];
    }

    /**
     * Logout - revoke token
     */
    public function logout($token) {
        $stmt = $this->db->prepare("UPDATE api_tokens SET status = 'revoked' WHERE token = ?");
        $stmt->execute([$token]);
        return true;
    }

    /**
     * Get user's dentist/patient ID
     */
    public function getEntityId($userId, $role) {
        if ($role === 'dentist') {
            $stmt = $this->db->prepare("SELECT id FROM dentists WHERE user_id = ?");
        } else {
            $stmt = $this->db->prepare("SELECT id FROM patients WHERE user_id = ?");
        }
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
}
