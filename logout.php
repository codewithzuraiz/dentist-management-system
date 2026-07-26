<?php
session_start();

$role = $_SESSION['user_role'] ?? 'user';
$isAdmin = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) || ($role === 'admin');

session_unset();
session_destroy();

if ($isAdmin) {
    header('Location: ../index.php');
} else {
    header('Location: index.php');
}
exit;
