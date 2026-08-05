<?php include 'header.php';

$stats = [
    'total_dentists' => 0, 'total_patients' => 0, 'today_appointments' => 0,
    'pending_appointments' => 0, 'completed_treatments' => 0, 'total_revenue' => 0,
    'pending_payments' => 0, 'new_patients_this_month' => 0
];
$appointmentOverview = ['completed' => 0, 'pending' => 0, 'cancelled' => 0, 'no_show' => 0];
$commonTreatments = [];
$recentActivities = [];
$upcomingAppointments = [];
$revenueSummary = ['today_rev' => 0, 'week_rev' => 0, 'month_rev' => 0, 'year_rev' => 0, 'pending_amt' => 0];

// Trend data
$prevMonthDentists = 0; $prevMonthPatients = 0; $prevMonthTreatments = 0;
$yesterdayAppts = 0; $yesterdayPending = 0;
$prevMonthNewPatients = 0;

try {
    $s = $pdo->query("SELECT COUNT(*) as c FROM dentists WHERE status='active'")->fetch();
    $stats['total_dentists'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM dentists WHERE status='active' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)")->fetch();
    $prevMonthDentists = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active'")->fetch();
    $stats['total_patients'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)")->fetch();
    $prevMonthPatients = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE DATE(appointment_date)=CURDATE() AND status!='cancelled'")->fetch();
    $stats['today_appointments'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE DATE(appointment_date)=DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status!='cancelled'")->fetch();
    $yesterdayAppts = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='pending'")->fetch();
    $stats['pending_appointments'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='pending' AND DATE(created_at)=DATE_SUB(CURDATE(), INTERVAL 1 DAY)")->fetch();
    $yesterdayPending = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='completed' AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch();
    $stats['completed_treatments'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='completed' AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND appointment_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch();
    $prevMonthTreatments = $s['c'];

    $s = $pdo->query("SELECT COALESCE(SUM(total_amount),0) as t FROM invoices WHERE status IN ('paid','partially_paid')")->fetch();
    $stats['total_revenue'] = $s['t'];

    $s = $pdo->query("SELECT COALESCE(SUM(total_amount - paid_amount),0) as t FROM invoices WHERE status IN ('unpaid','partially_paid','overdue')")->fetch();
    $stats['pending_payments'] = $s['t'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active' AND MONTH(registration_date)=MONTH(CURRENT_DATE()) AND YEAR(registration_date)=YEAR(CURRENT_DATE())")->fetch();
    $stats['new_patients_this_month'] = $s['c'];
    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active' AND MONTH(registration_date)=MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(registration_date)=YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))")->fetch();
    $prevMonthNewPatients = $s['c'];

    $rows = $pdo->query("SELECT status, COUNT(*) as count FROM appointments WHERE appointment_date=CURDATE() GROUP BY status")->fetchAll();
    foreach ($rows as $r) { $appointmentOverview[$r['status']] = $r['count']; }

    $commonTreatments = $pdo->query("SELECT t.name, COUNT(*) as count FROM invoice_items ii JOIN treatments t ON ii.treatment_id=t.id JOIN invoices i ON ii.invoice_id=i.id WHERE i.status IN ('paid','partially_paid') GROUP BY t.name ORDER BY count DESC LIMIT 6")->fetchAll();

    $recentActivities = $pdo->query("SELECT a.*, u.name as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id=u.id ORDER BY a.created_at DESC LIMIT 6")->fetchAll();

    $upcomingAppointments = $pdo->query("SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id=p.id JOIN users u_patient ON p.user_id=u_patient.id JOIN dentists d ON a.dentist_id=d.id JOIN users u_dentist ON d.user_id=u_dentist.id JOIN treatments t ON a.treatment_id=t.id WHERE a.status IN ('confirmed','pending') AND a.appointment_date >= CURDATE() ORDER BY a.appointment_date, a.start_time LIMIT 8")->fetchAll();

    $today = date('Y-m-d');
    $rev = $pdo->prepare("SELECT (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE DATE(created_at)=? AND status IN ('paid','partially_paid')) as today_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 7 DAY) AND status IN ('paid','partially_paid')) as week_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 30 DAY) AND status IN ('paid','partially_paid')) as month_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 365 DAY) AND status IN ('paid','partially_paid')) as year_rev, (SELECT COALESCE(SUM(total_amount - paid_amount),0) FROM invoices WHERE status IN ('unpaid','partially_paid','overdue')) as pending_amt");
    $rev->execute([$today, $today, $today, $today]);
    $revenueSummary = $rev->fetch();
} catch (Exception $e) { }

$totalAppt = array_sum($appointmentOverview);

function calcTrend($current, $previous) {
    if ($previous == 0) return $current > 0 ? ['+100%', 'up'] : ['0%', 'neutral'];
    $pct = round((($current - $previous) / $previous) * 100);
    $dir = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'neutral');
    $sign = $pct > 0 ? '+' : '';
    return [$sign . $pct . '%', $dir];
}

$tDentists = calcTrend($stats['total_dentists'], $prevMonthDentists);
$tPatients = calcTrend($stats['total_patients'], $prevMonthPatients);
$tTodayAppts = calcTrend($stats['today_appointments'], $yesterdayAppts);
$tPendingAppts = calcTrend($stats['pending_appointments'], $yesterdayPending);
$tTreatments = calcTrend($stats['completed_treatments'], $prevMonthTreatments);
$tRevenue = calcTrend($stats['total_revenue'], 0);
$tPending = calcTrend($stats['pending_payments'], 0);
$tNewPatients = calcTrend($stats['new_patients_this_month'], $prevMonthNewPatients);
?>

<!-- Page Header -->
<div class="grin-page-header">
        <div class="grin-page-header-content">
            <h1>Dashboard</h1>
            <p class="grin-subtitle">Welcome back, Admin! Here's what's happening at your clinic today.</p>
        </div>
</div>

<!-- Statistics Cards -->
<div class="grin-stats-grid">
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-blue"><i class="bx bxs-user"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['total_dentists']; ?></span>
            </div>
            <p>Total Dentists</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tDentists[1]; ?>"><?php echo $tDentists[1] === 'up' ? '↑' : ($tDentists[1] === 'down' ? '↓' : '—'); ?> <?php echo $tDentists[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-green"><i class="bx bx-user"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['total_patients']; ?></span>
            </div>
            <p>Total Patients</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tPatients[1]; ?>"><?php echo $tPatients[1] === 'up' ? '↑' : ($tPatients[1] === 'down' ? '↓' : '—'); ?> <?php echo $tPatients[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-orange"><i class="bx bx-calendar-check"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['today_appointments']; ?></span>
            </div>
            <p>Today's Appointments</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tTodayAppts[1]; ?>"><?php echo $tTodayAppts[1] === 'up' ? '↑' : ($tTodayAppts[1] === 'down' ? '↓' : '—'); ?> <?php echo $tTodayAppts[0]; ?></span>
                <span style="color:#94a3b8"> vs yesterday</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-purple"><i class="bx bx-time"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['pending_appointments']; ?></span>
            </div>
            <p>Pending Appointments</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tPendingAppts[1]; ?>"><?php echo $tPendingAppts[1] === 'up' ? '↑' : ($tPendingAppts[1] === 'down' ? '↓' : '—'); ?> <?php echo $tPendingAppts[0]; ?></span>
                <span style="color:#94a3b8"> vs yesterday</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-teal"><i class="bx bx-heart"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['completed_treatments']; ?></span>
            </div>
            <p>Completed Treatments (30d)</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tTreatments[1]; ?>"><?php echo $tTreatments[1] === 'up' ? '↑' : ($tTreatments[1] === 'down' ? '↓' : '—'); ?> <?php echo $tTreatments[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-indigo"><i class="bx bx-dollar-circle"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-currency">PKR</span>
                <span class="grin-stat-number"><?php echo number_format($stats['total_revenue'], 0); ?></span>
            </div>
            <p>Total Revenue</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tRevenue[1]; ?>"><?php echo $tRevenue[1] === 'up' ? '↑' : ($tRevenue[1] === 'down' ? '↓' : '—'); ?> <?php echo $tRevenue[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-red"><i class="bx bx-money"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-currency">PKR</span>
                <span class="grin-stat-number"><?php echo number_format($stats['pending_payments'], 0); ?></span>
            </div>
            <p>Pending Payments</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tPending[1]; ?>"><?php echo $tPending[1] === 'up' ? '↑' : ($tPending[1] === 'down' ? '↓' : '—'); ?> <?php echo $tPending[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>

    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-pink"><i class="bx bx-user-plus"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['new_patients_this_month']; ?></span>
            </div>
            <p>New Patients This Month</p>
            <div class="grin-stat-trend">
                <span class="trend-<?php echo $tNewPatients[1]; ?>"><?php echo $tNewPatients[1] === 'up' ? '↑' : ($tNewPatients[1] === 'down' ? '↓' : '—'); ?> <?php echo $tNewPatients[0]; ?></span>
                <span style="color:#94a3b8"> vs last month</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grin-content-grid">
    <!-- Revenue Chart -->
    <div class="grin-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-bar-chart"></i> Monthly Revenue Overview</h2>
            <div class="grin-chart-controls">
                <div class="grin-filter-group">
                    <button class="grin-filter-btn active" data-period="7">7 Days</button>
                    <button class="grin-filter-btn" data-period="30">30 Days</button>
                    <button class="grin-filter-btn" data-period="90">6 Months</button>
                    <button class="grin-filter-btn" data-period="365">12 Months</button>
                </div>
            </div>
        </div>
        <div class="grin-card-body">
            <div class="grin-chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Appointments Overview -->
    <div class="grin-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-calendar"></i> Appointments Overview</h2>
            <div class="grin-chart-controls">
                <div class="grin-filter-group">
                    <button class="grin-filter-btn active">Today</button>
                </div>
            </div>
        </div>
        <div class="grin-card-body">
            <div class="grin-appointment-summary">
                <div class="grin-appointment-total">
                    <span class="grin-appointment-count"><?php echo $totalAppt; ?></span>
                    <span class="grin-appointment-label">Today's Appointments</span>
                </div>
            </div>
            <div class="grin-appointment-charts">
                <div class="grin-donut-chart-container">
                    <canvas id="appointmentsDonutChart"></canvas>
                </div>
                <div class="grin-appointment-stats">
                    <?php
                    $colors = ['completed' => 'bg-green', 'confirmed' => 'bg-blue', 'pending' => 'bg-blue', 'cancelled' => 'bg-orange', 'no_show' => 'bg-red'];
                    $labels = ['completed' => 'Completed', 'confirmed' => 'Confirmed', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'no_show' => 'No Show'];
                    foreach ($appointmentOverview as $key => $count):
                        $pct = $totalAppt > 0 ? round(($count / $totalAppt) * 100) : 0;
                    ?>
                    <div class="grin-appointment-stat-item">
                        <div class="grin-stat-indicator <?php echo $colors[$key] ?? 'bg-blue'; ?>"></div>
                        <div class="grin-stat-info">
                            <span class="grin-stat-value"><?php echo $count; ?></span>
                            <span class="grin-stat-label"><?php echo $labels[$key] ?? ucfirst($key); ?></span>
                            <span class="grin-stat-percent"><?php echo $pct; ?>%</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grin-card" style="margin-top:20px;">
    <div class="grin-card-header">
        <h2><i class="bx bx-bolt"></i> Quick Actions</h2>
    </div>
    <div class="grin-card-body">
        <div class="grin-quick-actions-grid">
            <a href="appointments.php" class="grin-quick-action-btn">
                <div class="grin-action-icon"><i class="bx bx-calendar-plus"></i></div>
                <span>Add Appointment</span>
                <small>Schedule new appointment</small>
            </a>
            <a href="patients.php" class="grin-quick-action-btn">
                <div class="grin-action-icon"><i class="bx bx-user-plus"></i></div>
                <span>Add Patient</span>
                <small>Register new patient</small>
            </a>
            <a href="dentists.php" class="grin-quick-action-btn">
                <div class="grin-action-icon"><i class="bx bxs-user-plus"></i></div>
                <span>Add Dentist</span>
                <small>Add new dentist</small>
            </a>
            <a href="services.php" class="grin-quick-action-btn">
                <div class="grin-action-icon"><i class="bx bx-plus-circle"></i></div>
                <span>Add Service</span>
                <small>Add new service</small>
            </a>
            <a href="reports.php" class="grin-quick-action-btn">
                <div class="grin-action-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
                <span>View Reports</span>
                <small>Check clinic reports</small>
            </a>
        </div>
    </div>
</div>

<script>
var chartRevenueData = <?php
    try {
        $revData = $pdo->query("SELECT DATE_FORMAT(created_at, '%d %b') as month, SUM(total_amount) as revenue FROM invoices WHERE status IN ('paid','partially_paid') AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY) GROUP BY DATE(created_at) ORDER BY created_at")->fetchAll();
        echo json_encode($revData);
    } catch (Exception $e) { echo '[]'; }
?>;
var chartApptData = <?php echo json_encode($appointmentOverview); ?>;
</script>
<script src="dashboard.js"></script>

<?php include 'footer.php'; ?>
