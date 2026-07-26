<?php include 'header.php'; ?>

<div class="grin-page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <?php echo htmlspecialchars($adminEmail); ?></p>
</div>

<!-- Stats Cards -->
<div class="grin-stats-grid">
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-blue"><i class="bx bx-calendar-check"></i></div>
        <div class="grin-stat-info">
            <h3>24</h3>
            <p>Total Appointments</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-green"><i class="bx bx-user"></i></div>
        <div class="grin-stat-info">
            <h3>18</h3>
            <p>Total Patients</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-orange"><i class="bx bxs-dental"></i></div>
        <div class="grin-stat-info">
            <h3>6</h3>
            <p>Active Dentists</p>
        </div>
    </div>
    <div class="grin-stat-card">
        <div class="grin-stat-icon bg-purple"><i class="bx bx-dollar"></i></div>
        <div class="grin-stat-info">
            <h3>$12,450</h3>
            <p>Revenue This Month</p>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="grin-row">
    <!-- Recent Appointments -->
    <div class="grin-card grin-col-7">
        <div class="grin-card-header">
            <h2><i class="bx bx-calendar"></i> Recent Appointments</h2>
        </div>
        <div class="grin-card-body">
            <table class="grin-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sarah Johnson</td>
                        <td>Dental Implant</td>
                        <td>Jul 25, 2026</td>
                        <td><span class="grin-badge grin-badge-success">Confirmed</span></td>
                    </tr>
                    <tr>
                        <td>Mike Peters</td>
                        <td>Root Canal</td>
                        <td>Jul 24, 2026</td>
                        <td><span class="grin-badge grin-badge-warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td>Emma Wilson</td>
                        <td>Whitening</td>
                        <td>Jul 23, 2026</td>
                        <td><span class="grin-badge grin-badge-success">Confirmed</span></td>
                    </tr>
                    <tr>
                        <td>James Brown</td>
                        <td>General Checkup</td>
                        <td>Jul 22, 2026</td>
                        <td><span class="grin-badge grin-badge-danger">Cancelled</span></td>
                    </tr>
                    <tr>
                        <td>Lisa Davis</td>
                        <td>Cosmetic Dentistry</td>
                        <td>Jul 21, 2026</td>
                        <td><span class="grin-badge grin-badge-success">Confirmed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grin-card grin-col-5">
        <div class="grin-card-header">
            <h2><i class="bx bx-bolt"></i> Quick Actions</h2>
        </div>
        <div class="grin-card-body">
            <div class="grin-quick-actions">
                <a href="appointments.php" class="grin-action-btn">
                    <i class="bx bx-plus-circle"></i>
                    <span>New Appointment</span>
                </a>
                <a href="patients.php" class="grin-action-btn">
                    <i class="bx bx-user-plus"></i>
                    <span>Add Patient</span>
                </a>
                <a href="dentists.php" class="grin-action-btn">
                    <i class="bx bx-dental"></i>
                    <span>Manage Dentists</span>
                </a>
                <a href="services.php" class="grin-action-btn">
                    <i class="bx bx-cog"></i>
                    <span>Manage Services</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
