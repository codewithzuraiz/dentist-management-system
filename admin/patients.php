<?php
include 'header.php';

$message = '';
$messageType = '';

// Handle Add Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, phone, password, status) VALUES (4, ?, ?, ?, ?, 'active')");
            $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], password_hash($_POST['phone'], PASSWORD_DEFAULT)]);
            $userId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO patients (user_id, gender, date_of_birth, blood_group, address, emergency_contact_name, emergency_contact_phone, allergies, medical_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId, $_POST['gender'] ?? 'male', $_POST['date_of_birth'] ?? null,
                $_POST['blood_group'] ?? null, $_POST['address'] ?? null,
                $_POST['emergency_contact_name'] ?? null, $_POST['emergency_contact_phone'] ?? null,
                $_POST['allergies'] ?? null, $_POST['medical_conditions'] ?? null
            ]);
            $pdo->commit();
            $message = 'Patient added successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'edit') {
        try {
            $patientId = $_POST['patient_id'];
            $stmt = $pdo->prepare("SELECT user_id FROM patients WHERE id=?");
            $stmt->execute([$patientId]);
            $patient = $stmt->fetch();

            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
            $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], $patient['user_id']]);

            $stmt = $pdo->prepare("UPDATE patients SET gender=?, date_of_birth=?, blood_group=?, address=?, emergency_contact_name=?, emergency_contact_phone=?, allergies=?, medical_conditions=? WHERE id=?");
            $stmt->execute([
                $_POST['gender'], $_POST['date_of_birth'] ?? null, $_POST['blood_group'] ?? null,
                $_POST['address'] ?? null, $_POST['emergency_contact_name'] ?? null,
                $_POST['emergency_contact_phone'] ?? null, $_POST['allergies'] ?? null,
                $_POST['medical_conditions'] ?? null, $patientId
            ]);
            $message = 'Patient updated successfully!';
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
        $stmt = $pdo->prepare("SELECT user_id FROM patients WHERE id=?");
        $stmt->execute([$_POST['delete_id']]);
        $p = $stmt->fetch();
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id=?");
        $stmt->execute([$_POST['delete_id']]);
        if ($p) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$p['user_id']]);
        }
        $message = 'Patient deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch patients with search
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];
if ($search) {
    $where .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM patients p JOIN users u ON p.user_id=u.id WHERE $where");
$countStmt->execute($params);
$totalRows = $countStmt->fetch()['c'];
$totalPages = ceil($totalRows / $perPage);

$stmt = $pdo->prepare("SELECT p.*, u.name as name, u.email, u.phone FROM patients p JOIN users u ON p.user_id=u.id WHERE $where ORDER BY u.name ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$patients = $stmt->fetchAll();

$editPatient = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT p.*, u.name as name, u.email, u.phone FROM patients p JOIN users u ON p.user_id=u.id WHERE p.id=?");
    $stmt->execute([$_GET['edit']]);
    $editPatient = $stmt->fetch();
}
?>

<?php if ($message): ?>
<div class="grin-alert grin-alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="grin-page-header">
    <div class="grin-page-header-content">
        <h1>Patients</h1>
        <p>Manage registered patients</p>
    </div>
    <div class="grin-header-actions">
        <button class="grin-btn grin-btn-primary" onclick="showAddModal('addPatientModal')">
            <i class="bx bx-user-plus"></i> Add Patient
        </button>
    </div>
</div>

<div class="grin-card">
    <div class="grin-card-header">
        <h2><i class="bx bx-user"></i> All Patients (<?php echo $totalRows; ?>)</h2>
        <div class="grin-card-header-actions">
            <form method="GET" class="grin-filter-form">
                <div class="grin-search-wrapper">
                    <input type="text" name="search" class="grin-search-input" placeholder="Search patients..." value="<?php echo htmlspecialchars($search); ?>" />
                    <i class="bx bx-search grin-search-icon"></i>
                </div>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Registered</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                    <tr><td colspan="8" class="grin-empty-state">No patients found</td></tr>
                    <?php else: ?>
                    <?php foreach ($patients as $i => $pt): ?>
                    <tr>
                        <td><?php echo $offset + $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($pt['name']); ?></td>
                        <td><?php echo htmlspecialchars($pt['email']); ?></td>
                        <td><?php echo htmlspecialchars($pt['phone']); ?></td>
                        <td><?php echo ucfirst($pt['gender']); ?></td>
                        <td><?php echo formatDate($pt['registration_date']); ?></td>
                        <td><?php echo getStatusBadge($pt['status']); ?></td>
                        <td>
                            <div class="grin-action-btns">
                                <a href="?edit=<?php echo $pt['id']; ?>" class="grin-action-btn-small" title="Edit"><i class="bx bx-edit"></i></a>
                                <button class="grin-action-btn-small grin-btn-danger" title="Delete" onclick="deleteRecord('patients.php', <?php echo $pt['id']; ?>)"><i class="bx bx-trash"></i></button>
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
            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" class="grin-pagination-btn"><i class="bx bx-left-arrow-alt"></i></a>
            <?php endif; ?>
            <?php for ($p = max(1, $page-2); $p <= min($totalPages, $page+2); $p++): ?>
            <a href="?page=<?php echo $p; ?>&search=<?php echo urlencode($search); ?>" class="grin-pagination-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" class="grin-pagination-btn"><i class="bx bx-right-arrow-alt"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Patient Modal -->
<div class="grin-modal" id="addPatientModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Add New Patient</h2>
            <button class="grin-modal-close" onclick="hideModal('addPatientModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add" />
            <div class="grin-modal-body">
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Full Name *</label><input type="text" name="name" required /></div>
                    <div class="grin-form-group"><label>Email *</label><input type="email" name="email" required /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Phone *</label><input type="text" name="phone" required /></div>
                    <div class="grin-form-group"><label>Gender</label>
                        <select name="gender"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" /></div>
                    <div class="grin-form-group"><label>Blood Group</label>
                        <select name="blood_group"><option value="">Select</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select>
                    </div>
                </div>
                <div class="grin-form-group"><label>Address</label><textarea name="address" rows="2"></textarea></div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Emergency Contact Name</label><input type="text" name="emergency_contact_name" /></div>
                    <div class="grin-form-group"><label>Emergency Contact Phone</label><input type="text" name="emergency_contact_phone" /></div>
                </div>
                <div class="grin-form-group"><label>Allergies</label><textarea name="allergies" rows="2"></textarea></div>
                <div class="grin-form-group"><label>Medical Conditions</label><textarea name="medical_conditions" rows="2"></textarea></div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('addPatientModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Add Patient</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Patient Modal -->
<div class="grin-modal <?php echo $editPatient ? 'show' : ''; ?>" id="editPatientModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Edit Patient</h2>
            <button class="grin-modal-close" onclick="hideModal('editPatientModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="patient_id" value="<?php echo $editPatient['id'] ?? ''; ?>" />
            <div class="grin-modal-body">
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Full Name *</label><input type="text" name="name" required value="<?php echo htmlspecialchars($editPatient['name'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Email *</label><input type="email" name="email" required value="<?php echo htmlspecialchars($editPatient['email'] ?? ''); ?>" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Phone *</label><input type="text" name="phone" required value="<?php echo htmlspecialchars($editPatient['phone'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Gender</label>
                        <select name="gender">
                            <option value="male" <?php echo ($editPatient['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($editPatient['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($editPatient['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" value="<?php echo $editPatient['date_of_birth'] ?? ''; ?>" /></div>
                    <div class="grin-form-group"><label>Blood Group</label>
                        <select name="blood_group"><option value="">Select</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option <?php echo ($editPatient['blood_group'] ?? '') === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grin-form-group"><label>Address</label><textarea name="address" rows="2"><?php echo htmlspecialchars($editPatient['address'] ?? ''); ?></textarea></div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Emergency Contact Name</label><input type="text" name="emergency_contact_name" value="<?php echo htmlspecialchars($editPatient['emergency_contact_name'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Emergency Contact Phone</label><input type="text" name="emergency_contact_phone" value="<?php echo htmlspecialchars($editPatient['emergency_contact_phone'] ?? ''); ?>" /></div>
                </div>
                <div class="grin-form-group"><label>Allergies</label><textarea name="allergies" rows="2"><?php echo htmlspecialchars($editPatient['allergies'] ?? ''); ?></textarea></div>
                <div class="grin-form-group"><label>Medical Conditions</label><textarea name="medical_conditions" rows="2"><?php echo htmlspecialchars($editPatient['medical_conditions'] ?? ''); ?></textarea></div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('editPatientModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Update Patient</button>
            </div>
        </form>
    </div>
</div>

<?php if ($editPatient): ?>
<script>document.getElementById('editPatientModal').classList.add('show');</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
