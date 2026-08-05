<?php
include 'header.php';

$message = '';
$messageType = '';

// Handle Add Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, dentist_id, treatment_id, appointment_date, start_time, end_time, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['patient_id'], $_POST['dentist_id'], $_POST['treatment_id'],
                $_POST['appointment_date'], $_POST['start_time'], $_POST['end_time'],
                $_POST['status'] ?? 'pending', $_POST['notes'] ?? '', $_SESSION['user_id'] ?? 1
            ]);
            $message = 'Appointment created successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET patient_id=?, dentist_id=?, treatment_id=?, appointment_date=?, start_time=?, end_time=?, status=?, notes=? WHERE id=?");
            $stmt->execute([
                $_POST['patient_id'], $_POST['dentist_id'], $_POST['treatment_id'],
                $_POST['appointment_date'], $_POST['start_time'], $_POST['end_time'],
                $_POST['status'], $_POST['notes'] ?? '', $_POST['id']
            ]);
            $message = 'Appointment updated successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $message = 'Appointment deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch data for dropdowns
$patients = $pdo->query("SELECT p.id, u.name FROM patients p JOIN users u ON p.user_id=u.id WHERE p.status='active' ORDER BY u.name")->fetchAll();
$dentists = $pdo->query("SELECT d.id, u.name FROM dentists d JOIN users u ON d.user_id=u.id WHERE d.status='active' ORDER BY u.name")->fetchAll();
$treatments = $pdo->query("SELECT id, name FROM treatments WHERE status='active' ORDER BY name")->fetchAll();

// Fetch appointments with search and filter
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];

if ($search) {
    $where .= " AND (u_patient.name LIKE ? OR u_dentist.name LIKE ? OR t.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter) {
    $where .= " AND a.status = ?";
    $params[] = $statusFilter;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments a JOIN patients p ON a.patient_id=p.id JOIN users u_patient ON p.user_id=u_patient.id JOIN dentists d ON a.dentist_id=d.id JOIN users u_dentist ON d.user_id=u_dentist.id JOIN treatments t ON a.treatment_id=t.id WHERE $where");
$countStmt->execute($params);
$totalRows = $countStmt->fetch()['c'];
$totalPages = ceil($totalRows / $perPage);

$query = "SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id=p.id JOIN users u_patient ON p.user_id=u_patient.id JOIN dentists d ON a.dentist_id=d.id JOIN users u_dentist ON d.user_id=u_dentist.id JOIN treatments t ON a.treatment_id=t.id WHERE $where ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

// For edit modal
$editAppt = null;
if (isset($_GET['edit'])) {
    $editStmt = $pdo->prepare("SELECT * FROM appointments WHERE id=?");
    $editStmt->execute([$_GET['edit']]);
    $editAppt = $editStmt->fetch();
}
?>

<?php if ($message): ?>
<div class="grin-alert grin-alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="grin-page-header">
    <div class="grin-page-header-content">
        <h1>Appointments</h1>
        <p>Manage all patient appointments</p>
    </div>
    <div class="grin-header-actions">
        <button class="grin-btn grin-btn-primary" onclick="showAddModal('addAppointmentModal')">
            <i class="bx bx-plus"></i> New Appointment
        </button>
    </div>
</div>

<div class="grin-card">
    <div class="grin-card-header">
        <h2><i class="bx bx-calendar"></i> All Appointments (<?php echo $totalRows; ?>)</h2>
        <div class="grin-card-header-actions">
            <form method="GET" class="grin-filter-form">
                <div class="grin-search-wrapper">
                    <input type="text" name="search" class="grin-search-input" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" />
                    <i class="bx bx-search grin-search-icon"></i>
                </div>
                <select name="status" class="grin-status-filter" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="no_show" <?php echo $statusFilter === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                </select>
                <button type="submit" class="grin-btn grin-btn-sm">Search</button>
            </form>
        </div>
    </div>
    <div class="grin-card-body">
        <div class="grin-table-responsive">
            <table class="grin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Dentist</th>
                        <th>Treatment</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                    <tr><td colspan="8" class="grin-empty-state">No appointments found</td></tr>
                    <?php else: ?>
                    <?php foreach ($appointments as $i => $appt): ?>
                    <tr>
                        <td><?php echo $offset + $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                        <td>Dr. <?php echo htmlspecialchars($appt['dentist_name']); ?></td>
                        <td><?php echo htmlspecialchars($appt['treatment_name']); ?></td>
                        <td><?php echo formatDate($appt['appointment_date']); ?></td>
                        <td><?php echo formatTime($appt['start_time']); ?> - <?php echo formatTime($appt['end_time']); ?></td>
                        <td><?php echo getStatusBadge($appt['status']); ?></td>
                        <td>
                            <div class="grin-action-btns">
                                <a href="?edit=<?php echo $appt['id']; ?>" class="grin-action-btn-small" title="Edit"><i class="bx bx-edit"></i></a>
                                <button class="grin-action-btn-small grin-btn-danger" title="Delete" onclick="deleteRecord('appointments.php', <?php echo $appt['id']; ?>)"><i class="bx bx-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="grin-pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" class="grin-pagination-btn"><i class="bx bx-left-arrow-alt"></i></a>
            <?php endif; ?>
            <?php for ($p = max(1, $page-2); $p <= min($totalPages, $page+2); $p++): ?>
            <a href="?page=<?php echo $p; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" class="grin-pagination-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" class="grin-pagination-btn"><i class="bx bx-right-arrow-alt"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Appointment Modal -->
<div class="grin-modal" id="addAppointmentModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>New Appointment</h2>
            <button class="grin-modal-close" onclick="hideModal('addAppointmentModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add" />
            <div class="grin-modal-body">
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Patient</label>
                        <select name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php foreach ($patients as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grin-form-group">
                        <label>Dentist</label>
                        <select name="dentist_id" required>
                            <option value="">Select Dentist</option>
                            <?php foreach ($dentists as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Treatment</label>
                        <select name="treatment_id" required>
                            <option value="">Select Treatment</option>
                            <?php foreach ($treatments as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grin-form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Date</label>
                        <input type="date" name="appointment_date" required value="<?php echo date('Y-m-d'); ?>" />
                    </div>
                    <div class="grin-form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" required value="09:00" />
                    </div>
                    <div class="grin-form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" required value="10:00" />
                    </div>
                </div>
                <div class="grin-form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('addAppointmentModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Create Appointment</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div class="grin-modal <?php echo $editAppt ? 'show' : ''; ?>" id="editAppointmentModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Edit Appointment</h2>
            <button class="grin-modal-close" onclick="hideModal('editAppointmentModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id" value="<?php echo $editAppt['id'] ?? ''; ?>" />
            <div class="grin-modal-body">
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Patient</label>
                        <select name="patient_id" required>
                            <?php foreach ($patients as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($editAppt['patient_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grin-form-group">
                        <label>Dentist</label>
                        <select name="dentist_id" required>
                            <?php foreach ($dentists as $d): ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo ($editAppt['dentist_id'] ?? '') == $d['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Treatment</label>
                        <select name="treatment_id" required>
                            <?php foreach ($treatments as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo ($editAppt['treatment_id'] ?? '') == $t['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($t['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grin-form-group">
                        <label>Status</label>
                        <select name="status">
                            <?php foreach (['pending','confirmed','completed','cancelled','no_show'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo ($editAppt['status'] ?? '') === $s ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_',' ',$s)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group">
                        <label>Date</label>
                        <input type="date" name="appointment_date" required value="<?php echo $editAppt['appointment_date'] ?? ''; ?>" />
                    </div>
                    <div class="grin-form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" required value="<?php echo $editAppt['start_time'] ?? ''; ?>" />
                    </div>
                    <div class="grin-form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" required value="<?php echo $editAppt['end_time'] ?? ''; ?>" />
                    </div>
                </div>
                <div class="grin-form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?php echo htmlspecialchars($editAppt['notes'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('editAppointmentModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Update Appointment</button>
            </div>
        </form>
    </div>
</div>

<?php if ($editAppt): ?>
<script>document.getElementById('editAppointmentModal').classList.add('show');</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
