<?php
require_once 'config.php';

// Start session
session_start();

// If already logged in, redirect to index
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($action === 'login') {
        // Admin login check
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'admin';
            header('Location: index.php');
            exit;
        } else {
            // Check if user exists in session (registered users)
            if (isset($_SESSION['users'][$email])) {
                $user = $_SESSION['users'][$email];
                if (password_verify($password, $user['password'])) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = 'user';
                    header('Location: index.php');
                    exit;
                }
            }
            $message = 'Invalid email or password!';
            $messageType = 'error';
        }
    } elseif ($action === 'signup') {
        // Validate inputs
        if (empty($email) || empty($password)) {
            $message = 'Please fill all fields!';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email format!';
            $messageType = 'error';
        } elseif (strlen($password) < 6) {
            $message = 'Password must be at least 6 characters!';
            $messageType = 'error';
        } elseif ($email === ADMIN_EMAIL) {
            $message = 'This email is reserved for admin!';
            $messageType = 'error';
        } else {
            // Check if user already exists
            if (isset($_SESSION['users'][$email])) {
                $message = 'Account already exists with this email!';
                $messageType = 'error';
            } else {
                // Create new user
                $_SESSION['users'][$email] = [
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Auto login
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/responsive.css" rel="stylesheet" />
    <title><?php echo APP_NAME; ?> - Login</title>
    <link href="assets/images/favicon.png" rel="icon" type="image/png" />
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        
        .auth-header img {
            width: 80px;
            margin-bottom: 15px;
        }
        
        .auth-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        .auth-header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
        }
        
        .auth-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
        }
        
        .auth-tab.active {
            color: #0077b6;
            border-bottom-color: #0077b6;
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0077b6;
            box-shadow: 0 0 0 3px rgba(0,119,182,0.1);
        }
        
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,119,182,0.4);
        }
        
        .message {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        
        .message.success {
            background: #efe;
            color: #060;
            border: 1px solid #cfc;
        }
        
        .admin-hint {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 13px;
            color: #666;
        }
        
        .admin-hint strong {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/images/logo-2.png" alt="Logo" />
                <h2><?php echo APP_NAME; ?></h2>
                <p>Dental Tourism Management System</p>
            </div>
            
            <div class="auth-body">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="auth-tabs">
                    <div class="auth-tab active" onclick="switchTab('login')">Login</div>
                    <div class="auth-tab" onclick="switchTab('signup')">Sign Up</div>
                </div>
                
                <!-- Login Form -->
                <form class="auth-form active" id="loginForm" method="POST">
                    <input type="hidden" name="action" value="login" />
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required />
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required />
                    </div>
                    
                    <button type="submit" class="btn-auth">Login</button>
                </form>
                
                <!-- Signup Form -->
                <form class="auth-form" id="signupForm" method="POST">
                    <input type="hidden" name="action" value="signup" />
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required />
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Create a password (min 6 chars)" required />
                    </div>
                    
                    <button type="submit" class="btn-auth">Create Account</button>
                </form>
                
                <div class="admin-hint">
                    <strong>Admin Login:</strong><br />
                    Email: admin@grin.com<br />
                    Password: admin123
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Update tabs
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            
            if (tab === 'login') {
                document.querySelectorAll('.auth-tab')[0].classList.add('active');
                document.getElementById('loginForm').classList.add('active');
            } else {
                document.querySelectorAll('.auth-tab')[1].classList.add('active');
                document.getElementById('signupForm').classList.add('active');
            }
        }
    </script>
</body>

</html>
