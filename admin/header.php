<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db_connect.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminEmail = $_SESSION['user_email'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminId = $_SESSION['user_id'] ?? 0;

$quickStats = ['appointments_today' => 0, 'revenue_today' => 0];
$notifications = [];
$unreadCount = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM appointments WHERE DATE(appointment_date) = CURDATE() AND status != 'cancelled'");
    $quickStats['appointments_today'] = $stmt->fetch()['cnt'];

    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as rev FROM invoices WHERE DATE(created_at) = CURDATE() AND status IN ('paid','partially_paid')");
    $quickStats['revenue_today'] = $stmt->fetch()['rev'];

    if ($adminId) {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$adminId]);
        $notifications = $stmt->fetchAll();
        $unreadCount = count(array_filter($notifications, fn($n) => $n['is_read'] === 'no'));
    }
} catch (Exception $e) {
    // Silently fail - DB might not be set up yet
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin - <?php echo APP_NAME; ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/boxicons.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <link href="../assets/images/favicon.png" rel="icon" type="image/png" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="grin-admin-wrapper">
        <aside class="grin-sidebar" id="grinSidebar">
            <div class="grin-sidebar-header">
                <img src="../assets/images/logo-2.png" alt="Logo" />
                <span><?php echo APP_NAME; ?></span>
            </div>
            <nav class="grin-sidebar-nav">
                <a href="dashboard.php" class="grin-nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bx bxs-dashboard"></i> Dashboard
                </a>
                <a href="appointments.php" class="grin-nav-item <?php echo $currentPage === 'appointments' ? 'active' : ''; ?>">
                    <i class="bx bxs-calendar"></i> Appointments
                </a>
                <a href="patients.php" class="grin-nav-item <?php echo $currentPage === 'patients' ? 'active' : ''; ?>">
                    <i class="bx bxs-user"></i> Patients
                </a>
                <a href="dentists.php" class="grin-nav-item <?php echo $currentPage === 'dentists' ? 'active' : ''; ?>">
                    <i class="bx bxs-dental"></i> Dentists
                </a>
                <a href="services.php" class="grin-nav-item <?php echo $currentPage === 'services' ? 'active' : ''; ?>">
                    <i class="bx bxs-heart"></i> Services
                </a>
                <div class="grin-nav-divider"></div>
                <a href="../index.php" target="_blank" class="grin-nav-item">
                    <i class="bx bx-globe"></i> View Website
                </a>
                <a href="../logout.php" class="grin-nav-item grin-nav-logout">
                    <i class="bx bx-log-out"></i> Logout
                </a>
            </nav>
        </aside>

        <div class="grin-admin-main">
            <header class="grin-topbar">
                <button class="grin-sidebar-toggle" id="grinSidebarToggle">
                    <i class="bx bx-menu"></i>
                </button>
                <div class="grin-topbar-left">
                    <h1 class="grin-page-title"><?php echo ucfirst($currentPage); ?></h1>
                </div>
                <div class="grin-topbar-right">
                    <div class="grin-quick-stats">
                        <div class="grin-quick-stat">
                            <i class="bx bx-calendar-check"></i>
                            <span class="grin-stat-number"><?php echo $quickStats['appointments_today']; ?></span>
                            <span class="grin-stat-label">Today</span>
                        </div>
                        <div class="grin-quick-stat">
                            <i class="bx bx-money"></i>
                            <span class="grin-stat-number"><?php echo formatCurrency($quickStats['revenue_today']); ?></span>
                            <span class="grin-stat-label">Revenue</span>
                        </div>
                    </div>
                    <div class="grin-notification-wrapper">
                        <button class="grin-notification-btn" id="notificationBtn">
                            <i class="bx bx-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                            <span class="grin-notification-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="grin-notification-dropdown" id="notificationDropdown">
                            <div class="grin-notification-header">
                                <h3>Notifications</h3>
                                <?php if ($unreadCount > 0): ?>
                                <a href="#" class="grin-mark-all-read" onclick="markAllRead(event)">Mark all as read</a>
                                <?php endif; ?>
                            </div>
                            <div class="grin-notification-list">
                                <?php if (empty($notifications)): ?>
                                <div class="grin-notification-empty">No notifications</div>
                                <?php else: ?>
                                <?php foreach ($notifications as $notif): ?>
                                <a href="<?php echo $notif['related_entity_type'] ? '../admin/' . $notif['related_entity_type'] . 's.php' : '#'; ?>" class="grin-notification-item <?php echo $notif['is_read'] === 'no' ? 'unread' : ''; ?>">
                                    <div class="grin-notification-avatar <?php echo $notif['is_read'] === 'no' ? 'bg-blue' : 'bg-green'; ?>">
                                        <i class="bx bx-<?php echo $notif['type'] === 'appointment' ? 'calendar' : ($notif['type'] === 'payment' ? 'money' : 'bell'); ?>"></i>
                                    </div>
                                    <div class="grin-notification-content">
                                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                        <span class="grin-notification-time"><?php echo timeAgo($notif['created_at']); ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="grin-admin-profile">
                        <div class="grin-admin-avatar">
                            <img src="../assets/images/logo-2.png" alt="Admin Profile" />
                        </div>
                        <div class="grin-admin-info">
                            <span class="grin-admin-name"><?php echo htmlspecialchars($adminEmail); ?></span>
                            <span class="grin-admin-role">Administrator</span>
                        </div>
                        <button class="grin-profile-dropdown-btn" id="profileDropdownBtn">
                            <i class="bx bx-chevron-down"></i>
                        </button>
                        <div class="grin-profile-dropdown" id="profileDropdown">
                            <a href="#" class="grin-dropdown-item"><i class="bx bx-user"></i> Profile</a>
                            <a href="#" class="grin-dropdown-item"><i class="bx bx-cog"></i> Settings</a>
                            <div class="grin-dropdown-divider"></div>
                            <a href="../logout.php" class="grin-dropdown-item"><i class="bx bx-log-out"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="grin-admin-content">
