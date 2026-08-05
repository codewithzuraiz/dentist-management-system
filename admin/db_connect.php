<?php
$host = 'localhost';
$dbname = 'grin_dental';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Please ensure the database exists. Error: " . $e->getMessage());
}

function formatCurrency($amount) {
    return 'PKR ' . number_format((float)$amount, 0);
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d M Y', strtotime($date));
}

function formatTime($time) {
    if (!$time) return '-';
    return date('h:i A', strtotime($time));
}

function timeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function getStatusBadge($status) {
    $classes = [
        'active' => 'grin-badge-success',
        'confirmed' => 'grin-badge-success',
        'completed' => 'grin-badge-success',
        'paid' => 'grin-badge-success',
        'pending' => 'grin-badge-warning',
        'partially_paid' => 'grin-badge-warning',
        'inactive' => 'grin-badge-danger',
        'cancelled' => 'grin-badge-danger',
        'rejected' => 'grin-badge-danger',
        'overdue' => 'grin-badge-danger',
        'no_show' => 'grin-badge-danger',
        'unpaid' => 'grin-badge-warning',
        'on_leave' => 'grin-badge-info'
    ];
    $class = $classes[$status] ?? 'grin-badge-info';
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="grin-badge ' . $class . '">' . $label . '</span>';
}
