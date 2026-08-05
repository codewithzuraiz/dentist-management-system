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
$revenueSummary = ['today_revenue' => 0, 'week_revenue' => 0, 'month_revenue' => 0, 'year_revenue' => 0, 'pending_amount' => 0];

try {
    $s = $pdo->query("SELECT COUNT(*) as c FROM dentists WHERE status='active'")->fetch();
    $stats['total_dentists'] = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active'")->fetch();
    $stats['total_patients'] = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE DATE(appointment_date)=CURDATE() AND status!='cancelled'")->fetch();
    $stats['today_appointments'] = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='pending'")->fetch();
    $stats['pending_appointments'] = $s['c'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM appointments WHERE status='completed' AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch();
    $stats['completed_treatments'] = $s['c'];

    $s = $pdo->query("SELECT COALESCE(SUM(total_amount),0) as t FROM invoices WHERE status IN ('paid','partially_paid')")->fetch();
    $stats['total_revenue'] = $s['t'];

    $s = $pdo->query("SELECT COALESCE(SUM(total_amount - paid_amount),0) as t FROM invoices WHERE status IN ('unpaid','partially_paid','overdue')")->fetch();
    $stats['pending_payments'] = $s['t'];

    $s = $pdo->query("SELECT COUNT(*) as c FROM patients WHERE status='active' AND MONTH(registration_date)=MONTH(CURRENT_DATE()) AND YEAR(registration_date)=YEAR(CURRENT_DATE())")->fetch();
    $stats['new_patients_this_month'] = $s['c'];

    $rows = $pdo->query("SELECT status, COUNT(*) as count FROM appointments WHERE appointment_date=CURDATE() GROUP BY status")->fetchAll();
    foreach ($rows as $r) { $appointmentOverview[$r['status']] = $r['count']; }

    $commonTreatments = $pdo->query("SELECT t.name, COUNT(*) as count FROM invoice_items ii JOIN treatments t ON ii.treatment_id=t.id JOIN invoices i ON ii.invoice_id=i.id WHERE i.status IN ('paid','partially_paid') GROUP BY t.name ORDER BY count DESC LIMIT 6")->fetchAll();

    $recentActivities = $pdo->query("SELECT a.*, u.name as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id=u.id ORDER BY a.created_at DESC LIMIT 6")->fetchAll();

    $upcomingAppointments = $pdo->query("SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id=p.id JOIN users u_patient ON p.user_id=u_patient.id JOIN dentists d ON a.dentist_id=d.id JOIN users u_dentist ON d.user_id=u_dentist.id JOIN treatments t ON a.treatment_id=t.id WHERE a.status IN ('confirmed','pending') AND a.appointment_date >= CURDATE() ORDER BY a.appointment_date, a.start_time LIMIT 8")->fetchAll();

    $today = date('Y-m-d');
    $rev = $pdo->prepare("SELECT (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE DATE(created_at)=? AND status IN ('paid','partially_paid')) as today_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 7 DAY) AND status IN ('paid','partially_paid')) as week_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 30 DAY) AND status IN ('paid','partially_paid')) as month_rev, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE created_at >= DATE_SUB(?, INTERVAL 365 DAY) AND status IN ('paid','partially_paid')) as year_rev, (SELECT COALESCE(SUM(total_amount - paid_amount),0) FROM invoices WHERE status IN ('unpaid','partially_paid','overdue')) as pending_amt");
    $rev->execute([$today, $today, $today, $today]);
    $revenueSummary = $rev->fetch();
} catch (Exception $e) { /* DB not ready */ }

$totalAppt = array_sum($appointmentOverview);
?>

<div class="grin-page-header">
    <div class="grin-page-header-content">
        <h1>Dashboard</h1>
        <p class="grin-subtitle">Overview of your dental clinic</p>
    </div>
</div>

<div class="grin-stats-grid">
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-blue"><i class="bx bxs-tooth"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['total_dentists']; ?></span>
            </div>
            <p>Total Dentists</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-green"><i class="bx bx-user"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo number_format($stats['total_patients']); ?></span>
            </div>
            <p>Total Patients</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-orange"><i class="bx bx-calendar-check"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['today_appointments']; ?></span>
            </div>
            <p>Today's Appointments</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-purple"><i class="bx bx-time"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['pending_appointments']; ?></span>
            </div>
            <p>Pending Appointments</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-teal"><i class="bx bx-heart"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['completed_treatments']; ?></span>
            </div>
            <p>Completed Treatments (30d)</p>
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
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-pink"><i class="bx bx-user-plus"></i></div>
        <div class="grin-stat-info">
            <div class="grin-stat-value">
                <span class="grin-stat-number"><?php echo $stats['new_patients_this_month']; ?></span>
            </div>
            <p>New Patients This Month</p>
        </div>
    </div>
</div>

<div class="grin-content-grid">
    <div class="grin-card grin-chart-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-chart"></i> Monthly Revenue</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grin-card grin-appointment-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-pie-chart"></i> Appointments Overview</h2>
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
                    $apptColors = ['completed' => 'bg-green', 'pending' => 'bg-blue', 'cancelled' => 'bg-orange', 'no_show' => 'bg-red'];
                    $apptLabels = ['completed' => 'Completed', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'no_show' => 'No Show'];
                    foreach ($appointmentOverview as $key => $count):
                        $pct = $totalAppt > 0 ? round(($count / $totalAppt) * 100) : 0;
                    ?>
                    <div class="grin-appointment-stat-item">
                        <div class="grin-stat-indicator <?php echo $apptColors[$key]; ?>"></div>
                        <div class="grin-stat-info">
                            <span class="grin-stat-value"><?php echo $count; ?></span>
                            <span class="grin-stat-label"><?php echo $apptLabels[$key]; ?></span>
                            <span class="grin-stat-percent"><?php echo $pct; ?>%</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="grin-card grin-treatments-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-list-ul"></i> Most Common Treatments</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-treatments-list">
                <?php if (empty($commonTreatments)): ?>
                <p class="grin-empty-state">No treatment data yet</p>
                <?php else: ?>
                <?php $maxCount = !empty($commonTreatments) ? $commonTreatments[0]['count'] : 1; ?>
                <?php foreach ($commonTreatments as $idx => $t): ?>
                <div class="grin-treatment-item">
                    <div class="grin-treatment-rank"><?php echo $idx + 1; ?></div>
                    <div class="grin-treatment-name"><?php echo htmlspecialchars($t['name']); ?></div>
                    <div class="grin-treatment-count"><?php echo $t['count']; ?></div>
                    <div class="grin-treatment-bar">
                        <div class="grin-treatment-bar-fill" style="width: <?php echo $maxCount > 0 ? ($t['count'] / $maxCount) * 100 : 0; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grin-card grin-activities-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-time"></i> Recent Activities</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-activities-timeline">
                <?php if (empty($recentActivities)): ?>
                <p class="grin-empty-state">No recent activities</p>
                <?php else: ?>
                <?php foreach ($recentActivities as $act): ?>
                <div class="grin-activity-item">
                    <div class="grin-activity-icon <?php echo $act['action'] === 'create' ? 'bg-green' : ($act['action'] === 'payment' ? 'bg-orange' : 'bg-blue'); ?>">
                        <i class="bx bx-<?php echo $act['action'] === 'create' ? 'plus-circle' : ($act['action'] === 'payment' ? 'money' : 'edit'); ?>"></i>
                    </div>
                    <div class="grin-activity-content">
                        <p><strong><?php echo htmlspecialchars($act['user_name'] ?? 'System'); ?></strong> <?php echo htmlspecialchars($act['description'] ?? $act['action'] . ' ' . $act['entity_type']); ?></p>
                        <span class="grin-activity-time"><?php echo timeAgo($act['created_at']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grin-card grin-upcoming-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-calendar-event"></i> Upcoming Appointments</h2>
            <a href="appointments.php" class="grin-view-all">View All</a>
        </div>
        <div class="grin-card-body">
            <div class="grin-table-responsive">
                <table class="grin-table grin-upcoming-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Dentist</th>
                            <th>Treatment</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingAppointments)): ?>
                        <tr><td colspan="7" class="grin-empty-state">No upcoming appointments</td></tr>
                        <?php else: ?>
                        <?php foreach ($upcomingAppointments as $appt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                            <td>Dr. <?php echo htmlspecialchars($appt['dentist_name']); ?></td>
                            <td><?php echo htmlspecialchars($appt['treatment_name']); ?></td>
                            <td><?php echo formatDate($appt['appointment_date']); ?></td>
                            <td><?php echo formatTime($appt['start_time']); ?></td>
                            <td><?php echo getStatusBadge($appt['status']); ?></td>
                            <td>
                                <button class="grin-action-btn-small" title="View"><i class="bx bx-eye"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grin-card grin-revenue-summary">
        <div class="grin-card-header">
            <h2><i class="bx bx-money-stack"></i> Revenue Summary</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-revenue-grid">
                <div class="grin-revenue-item">
                    <div class="grin-revenue-label">Today</div>
                    <div class="grin-revenue-value"><?php echo formatCurrency($revenueSummary['today_rev']); ?></div>
                </div>
                <div class="grin-revenue-item">
                    <div class="grin-revenue-label">This Week</div>
                    <div class="grin-revenue-value"><?php echo formatCurrency($revenueSummary['week_rev']); ?></div>
                </div>
                <div class="grin-revenue-item">
                    <div class="grin-revenue-label">This Month</div>
                    <div class="grin-revenue-value"><?php echo formatCurrency($revenueSummary['month_rev']); ?></div>
                </div>
                <div class="grin-revenue-item">
                    <div class="grin-revenue-label">This Year</div>
                    <div class="grin-revenue-value"><?php echo formatCurrency($revenueSummary['year_rev']); ?></div>
                </div>
                <div class="grin-revenue-item pending">
                    <div class="grin-revenue-label">Pending</div>
                    <div class="grin-revenue-value"><?php echo formatCurrency($revenueSummary['pending_amt']); ?></div>
                    <div class="grin-revenue-badge">Due Soon</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grin-card grin-quick-actions-card">
        <div class="grin-card-header">
            <h2><i class="bx bx-bolt"></i> Quick Actions</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-quick-actions-grid">
                <a href="patients.php" class="grin-quick-action-btn"><div class="grin-action-icon"><i class="bx bx-user-plus"></i></div><span>Add Patient</span></a>
                <a href="dentists.php" class="grin-quick-action-btn"><div class="grin-action-icon"><i class="bx bx-dental"></i></div><span>Add Dentist</span></a>
                <a href="appointments.php" class="grin-quick-action-btn"><div class="grin-action-icon"><i class="bx bx-calendar-plus"></i></div><span>Create Appointment</span></a>
                <a href="services.php" class="grin-quick-action-btn"><div class="grin-action-icon"><i class="bx bx-plus-circle"></i></div><span>Add Treatment</span></a>
            </div>
        </div>
    </div>
</div>

<script>
var chartRevenueData = <?php
    try {
        $revData = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue FROM invoices WHERE status IN ('paid','partially_paid') AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY created_at")->fetchAll();
        echo json_encode($revData);
    } catch (Exception $e) { echo '[]'; }
?>;
var chartApptData = <?php echo json_encode($appointmentOverview); ?>;
</script>
<script src="dashboard.js"></script>

<?php include 'footer.php'; ?>
