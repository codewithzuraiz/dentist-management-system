<?php
/**
 * Auth Routes
 * POST auth/register - Register new user
 * POST auth/login - Login
 * POST auth/logout - Logout
 * POST auth/forgot-password - Reset password
 * GET auth/profile - Get profile
 * PUT auth/profile - Update profile
 */

switch ($action) {
    case 'register':
        $result = $auth->register($input);
        ApiResponse::success($result, "Registration successful");
        break;

    case 'login':
        $result = $auth->login(
            $input['email'] ?? '',
            $input['password'] ?? '',
            $input['device_info'] ?? []
        );
        ApiResponse::success($result, "Login successful");
        break;

    case 'logout':
        $auth->logout($token);
        ApiResponse::success(null, "Logged out successfully");
        break;

    case 'forgot-password':
        if (empty($input['email'])) {
            ApiResponse::error("Email is required");
        }
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        $userRow = $stmt->fetch();
        if ($userRow) {
            // In production, send email with reset link
            // For now, just return success
        }
        ApiResponse::success(null, "If email exists, a reset link has been sent");
        break;

    case 'profile':
        if ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT u.id, u.name, u.email, u.phone, u.profile_image, u.status, r.name as role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
            $stmt->execute([$user['user_id']]);
            $profile = $stmt->fetch();

            if ($user['role'] === 'dentist') {
                $stmt = $pdo->prepare("SELECT * FROM dentists WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                $profile['dentist_info'] = $stmt->fetch();
            } elseif ($user['role'] === 'patient') {
                $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                $profile['patient_info'] = $stmt->fetch();
            }

            ApiResponse::success($profile);
        } elseif ($method === 'PUT') {
            $allowed = ['name', 'phone', 'profile_image'];
            $updates = [];
            $params = [];
            foreach ($allowed as $field) {
                if (isset($input[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }
            if (!empty($updates)) {
                $params[] = $user['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            ApiResponse::success(null, "Profile updated");
        }
        break;

    default:
        ApiResponse::error("Invalid auth action", 404);
}
