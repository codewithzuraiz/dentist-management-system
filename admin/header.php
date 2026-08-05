<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db_connect.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminEmail = $_SESSION['user_email'] ?? 'admin@grin.com';
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
} catch (Exception $e) { }
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
        <!-- Sidebar -->
        <aside class="grin-sidebar" id="grinSidebar">
            <div class="grin-sidebar-header">
                <img src="../assets/images/logo-2.png" alt="Logo" />
                <div class="grin-sidebar-brand">
                    <span class="brand-name">Grin</span>
                    <span class="brand-sub">Dental Clinic</span>
                </div>
            </div>
            <nav class="grin-sidebar-nav">
                <a href="dashboard.php" class="grin-nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bx bxs-dashboard"></i> <span>Dashboard</span>
                </a>
                <a href="appointments.php" class="grin-nav-item <?php echo $currentPage === 'appointments' ? 'active' : ''; ?>">
                    <i class="bx bxs-calendar"></i> <span>Appointments</span>
                </a>
                <a href="patients.php" class="grin-nav-item <?php echo $currentPage === 'patients' ? 'active' : ''; ?>">
                    <i class="bx bxs-user"></i> <span>Patients</span>
                </a>
                <a href="dentists.php" class="grin-nav-item <?php echo $currentPage === 'dentists' ? 'active' : ''; ?>">
                    <i class="bx bxs-user"></i> <span>Dentists</span>
                </a>
                <a href="services.php" class="grin-nav-item <?php echo $currentPage === 'services' ? 'active' : ''; ?>">
                    <i class="bx bxs-heart"></i> <span>Services</span>
                </a>
                <div class="grin-nav-divider"></div>
                <a href="../index.php" target="_blank" class="grin-nav-item">
                    <i class="bx bx-globe"></i> <span>View Website</span>
                </a>
                <a href="reports.php" class="grin-nav-item <?php echo $currentPage === 'reports' ? 'active' : ''; ?>">
                    <i class="bx bx-bar-chart-alt-2"></i> <span>Reports</span>
                </a>
                <a href="settings.php" class="grin-nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                    <i class="bx bx-cog"></i> <span>Settings</span>
                </a>
                <a href="../logout.php" class="grin-nav-item grin-nav-logout">
                    <i class="bx bx-log-out"></i> <span>Logout</span>
                </a>
            </nav>
            <div class="grin-sidebar-banner">
                <p>Delivering</p>
                <div class="banner-title">Healthy Smiles</div>
                <div class="banner-title banner-accent">Every Day</div>
                <img src="../assets/images/logo-2.png" alt="Tooth Mascot" style="opacity:0.9; margin-top:8px;" />
            </div>
        </aside>

        <!-- Main Content -->
        <div class="grin-admin-main">
            <!-- Top Bar -->
            <header class="grin-topbar">
                <button class="grin-sidebar-toggle" id="grinSidebarToggle">
                    <i class="bx bx-menu"></i>
                </button>
                <br>
                <div class="grin-topbar-left">
                    <h1 class="grin-page-title"><?php echo ucfirst($currentPage); ?></h1>
                </div>
                <div class="grin-topbar-right">
                    <div class="grin-quick-stats">
                        <div class="grin-quick-stat">
                            <div class="stat-icon blue"><i class="bx bx-calendar"></i></div>
                            <div class="stat-content">
                                <span class="stat-value"><?php echo date('d M Y'); ?></span>
                            </div>
                        </div>
                        <div class="grin-quick-stat">
                            <div class="stat-icon green"><i class="bx bx-money"></i></div>
                            <div class="stat-content">
                                <span class="stat-value">PKR <?php echo number_format($quickStats['revenue_today'], 0); ?></span>
                                <span class="stat-label">Revenue</span>
                            </div>
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
                                <a href="#" class="grin-notification-item <?php echo $notif['is_read'] === 'no' ? 'unread' : ''; ?>">
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
                            <img src="../assets/images/logo-2.png" alt="Admin" />
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

            <!-- Page Content -->
            <div class="grin-admin-content">
