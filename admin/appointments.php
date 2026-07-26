<?php include 'header.php'; ?>

<div class="grin-page-header">
    <h1>Appointments</h1>
    <p>Manage all patient appointments</p>
</div>

<div class="grin-card">
    <div class="grin-card-header">
        <h2><i class="bx bx-calendar"></i> All Appointments</h2>
    </div>
    <div class="grin-card-body">
        <table class="grin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Country</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Sarah Johnson</td>
                    <td>sarah@email.com</td>
                    <td>+1 555 0123</td>
                    <td>Dental Implant</td>
                    <td>Jul 25, 2026</td>
                    <td>Finland</td>
                    <td><span class="grin-badge grin-badge-success">Confirmed</span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Mike Peters</td>
                    <td>mike@email.com</td>
                    <td>+44 7700 9001</td>
                    <td>Root Canal</td>
                    <td>Jul 24, 2026</td>
                    <td>Denmark</td>
                    <td><span class="grin-badge grin-badge-warning">Pending</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Emma Wilson</td>
                    <td>emma@email.com</td>
                    <td>+47 123 456</td>
                    <td>Whitening</td>
                    <td>Jul 23, 2026</td>
                    <td>Norway</td>
                    <td><span class="grin-badge grin-badge-success">Confirmed</span></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>James Brown</td>
                    <td>james@email.com</td>
                    <td>+32 456 789</td>
                    <td>General Checkup</td>
                    <td>Jul 22, 2026</td>
                    <td>Belgium</td>
                    <td><span class="grin-badge grin-badge-danger">Cancelled</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
