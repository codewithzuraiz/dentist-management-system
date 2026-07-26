<?php
require_once 'config.php';
session_start();

// If already logged in, redirect based on role
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// Handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($action === 'login') {
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'admin';
            echo json_encode(['status' => 'redirect', 'url' => 'admin/dashboard.php']);
            exit;
        } elseif (isset($_SESSION['users'][$email])) {
            $user = $_SESSION['users'][$email];
            if (password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                echo json_encode(['status' => 'redirect', 'url' => 'index.php']);
                exit;
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password!']);
        exit;
    } elseif ($action === 'signup') {
        if (empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill all fields!']);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format!']);
        } elseif (strlen($password) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters!']);
        } elseif ($email === ADMIN_EMAIL) {
            echo json_encode(['status' => 'error', 'message' => 'This email is reserved for admin!']);
        } elseif (isset($_SESSION['users'][$email])) {
            echo json_encode(['status' => 'error', 'message' => 'Account already exists with this email!']);
        } else {
            $_SESSION['users'][$email] = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            echo json_encode(['status' => 'redirect', 'url' => 'index.php']);
        }
        exit;
    }
}

// Fallback: redirect to login page
header('Location: index.php');
exit;
