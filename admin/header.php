<?php
require_once __DIR__ . '/../config.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminEmail = $_SESSION['user_email'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/boxicons.min.css" rel="stylesheet" />
    <link href="styles.css" rel="stylesheet" />
    <link href="../assets/images/favicon.png" rel="icon" type="image/png" />
</head>
<body>
    <div class="grin-admin-wrapper">
        <!-- Sidebar -->
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
                <a href="../index.php" class="grin-nav-item">
                    <i class="bx bx-globe"></i> View Website
                </a>
                <a href="../logout.php" class="grin-nav-item grin-nav-logout">
                    <i class="bx bx-log-out"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="grin-admin-main">
            <!-- Top Bar -->
            <header class="grin-topbar">
                <button class="grin-sidebar-toggle" id="grinSidebarToggle">
                    <i class="bx bx-menu"></i>
                </button>
                <div class="grin-topbar-right">
                    <span class="grin-admin-user">
                        <i class="bx bx-user-circle"></i> <?php echo htmlspecialchars($adminEmail); ?>
                    </span>
                </div>
            </header>

            <!-- Page Content -->
            <div class="grin-admin-content">
