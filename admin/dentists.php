<?php
include 'header.php';

$message = '';
$messageType = '';

// Handle Add Dentist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, phone, password, status) VALUES (3, ?, ?, ?, ?, 'active')");
            $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], password_hash($_POST['phone'], PASSWORD_DEFAULT)]);
            $userId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO dentists (user_id, specialization, qualification, experience_years, consultation_fee, bio, status, working_days, working_start_time, working_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId, $_POST['specialization'], $_POST['qualification'] ?? null,
                $_POST['experience_years'] ?? 0, $_POST['consultation_fee'] ?? 0,
                $_POST['bio'] ?? null, 'active', $_POST['working_days'] ?? null,
                $_POST['working_start_time'] ?? null, $_POST['working_end_time'] ?? null
            ]);
            $pdo->commit();
            $message = 'Dentist added successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'edit') {
        try {
            $dentistId = $_POST['dentist_id'];
            $stmt = $pdo->prepare("SELECT user_id FROM dentists WHERE id=?");
            $stmt->execute([$dentistId]);
            $dentist = $stmt->fetch();

            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
            $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], $dentist['user_id']]);

            $stmt = $pdo->prepare("UPDATE dentists SET specialization=?, qualification=?, experience_years=?, consultation_fee=?, bio=?, status=?, working_days=?, working_start_time=?, working_end_time=? WHERE id=?");
            $stmt->execute([
                $_POST['specialization'], $_POST['qualification'] ?? null,
                $_POST['experience_years'] ?? 0, $_POST['consultation_fee'] ?? 0,
                $_POST['bio'] ?? null, $_POST['status'] ?? 'active',
                $_POST['working_days'] ?? null, $_POST['working_start_time'] ?? null,
                $_POST['working_end_time'] ?? null, $dentistId
            ]);
            $message = 'Dentist updated successfully!';
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
        $stmt = $pdo->prepare("SELECT user_id FROM dentists WHERE id=?");
        $stmt->execute([$_POST['delete_id']]);
        $d = $stmt->fetch();
        $stmt = $pdo->prepare("DELETE FROM dentists WHERE id=?");
        $stmt->execute([$_POST['delete_id']]);
        if ($d) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$d['user_id']]);
        }
        $message = 'Dentist deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch dentists
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];
if ($search) {
    $where .= " AND (u.name LIKE ? OR u.email LIKE ? OR d.specialization LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM dentists d JOIN users u ON d.user_id=u.id WHERE $where");
$countStmt->execute($params);
$totalRows = $countStmt->fetch()['c'];
$totalPages = ceil($totalRows / $perPage);

$stmt = $pdo->prepare("SELECT d.*, u.name as name, u.email, u.phone FROM dentists d JOIN users u ON d.user_id=u.id WHERE $where ORDER BY u.name ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$dentists = $stmt->fetchAll();

$editDentist = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT d.*, u.name as name, u.email, u.phone FROM dentists d JOIN users u ON d.user_id=u.id WHERE d.id=?");
    $stmt->execute([$_GET['edit']]);
    $editDentist = $stmt->fetch();
}
?>

<?php if ($message): ?>
<div class="grin-alert grin-alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="grin-page-header">
    <div class="grin-page-header-content">
        <h1>Dentists</h1>
        <p>Manage dental professionals</p>
    </div>
    <div class="grin-header-actions">
        <button class="grin-btn grin-btn-primary" onclick="showAddModal('addDentistModal')">
            <i class="bx bx-user-plus"></i> Add Dentist
        </button>
    </div>
</div>

<div class="grin-card">
    <div class="grin-card-header">
        <h2><i class="bx bx-dental"></i> Our Dentists (<?php echo $totalRows; ?>)</h2>
        <div class="grin-card-header-actions">
            <form method="GET" class="grin-filter-form">
                <div class="grin-search-wrapper">
                    <input type="text" name="search" class="grin-search-input" placeholder="Search dentists..." value="<?php echo htmlspecialchars($search); ?>" />
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
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Experience</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dentists)): ?>
                    <tr><td colspan="8" class="grin-empty-state">No dentists found</td></tr>
                    <?php else: ?>
                    <?php foreach ($dentists as $i => $d): ?>
                    <tr>
                        <td><?php echo $offset + $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($d['name']); ?></td>
                        <td><?php echo htmlspecialchars($d['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($d['qualification'] ?? '-'); ?></td>
                        <td><?php echo $d['experience_years']; ?> yrs</td>
                        <td><?php echo formatCurrency($d['consultation_fee']); ?></td>
                        <td><?php echo getStatusBadge($d['status']); ?></td>
                        <td>
                            <div class="grin-action-btns">
                                <a href="?edit=<?php echo $d['id']; ?>" class="grin-action-btn-small" title="Edit"><i class="bx bx-edit"></i></a>
                                <button class="grin-action-btn-small grin-btn-danger" title="Delete" onclick="deleteRecord('dentists.php', <?php echo $d['id']; ?>)"><i class="bx bx-trash"></i></button>
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

<!-- Add Dentist Modal -->
<div class="grin-modal" id="addDentistModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Add New Dentist</h2>
            <button class="grin-modal-close" onclick="hideModal('addDentistModal')">&times;</button>
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
                    <div class="grin-form-group"><label>Specialization *</label><input type="text" name="specialization" required placeholder="e.g. Orthodontics" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Qualification</label><input type="text" name="qualification" placeholder="e.g. BDS, MDS" /></div>
                    <div class="grin-form-group"><label>Experience (years)</label><input type="number" name="experience_years" value="0" min="0" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Consultation Fee</label><input type="number" name="consultation_fee" value="0" min="0" step="100" /></div>
                    <div class="grin-form-group"><label>Working Days</label><input type="text" name="working_days" placeholder="e.g. Mon-Fri" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Start Time</label><input type="time" name="working_start_time" value="09:00" /></div>
                    <div class="grin-form-group"><label>End Time</label><input type="time" name="working_end_time" value="17:00" /></div>
                </div>
                <div class="grin-form-group"><label>Bio</label><textarea name="bio" rows="3"></textarea></div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('addDentistModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Add Dentist</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Dentist Modal -->
<div class="grin-modal <?php echo $editDentist ? 'show' : ''; ?>" id="editDentistModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Edit Dentist</h2>
            <button class="grin-modal-close" onclick="hideModal('editDentistModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="dentist_id" value="<?php echo $editDentist['id'] ?? ''; ?>" />
            <div class="grin-modal-body">
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Full Name *</label><input type="text" name="name" required value="<?php echo htmlspecialchars($editDentist['name'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Email *</label><input type="email" name="email" required value="<?php echo htmlspecialchars($editDentist['email'] ?? ''); ?>" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Phone *</label><input type="text" name="phone" required value="<?php echo htmlspecialchars($editDentist['phone'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Specialization *</label><input type="text" name="specialization" required value="<?php echo htmlspecialchars($editDentist['specialization'] ?? ''); ?>" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Qualification</label><input type="text" name="qualification" value="<?php echo htmlspecialchars($editDentist['qualification'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Experience (years)</label><input type="number" name="experience_years" value="<?php echo $editDentist['experience_years'] ?? 0; ?>" min="0" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Consultation Fee</label><input type="number" name="consultation_fee" value="<?php echo $editDentist['consultation_fee'] ?? 0; ?>" min="0" step="100" /></div>
                    <div class="grin-form-group"><label>Status</label>
                        <select name="status">
                            <option value="active" <?php echo ($editDentist['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editDentist['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="on_leave" <?php echo ($editDentist['status'] ?? '') === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                        </select>
                    </div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Working Days</label><input type="text" name="working_days" value="<?php echo htmlspecialchars($editDentist['working_days'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Start Time</label><input type="time" name="working_start_time" value="<?php echo $editDentist['working_start_time'] ?? ''; ?>" /></div>
                    <div class="grin-form-group"><label>End Time</label><input type="time" name="working_end_time" value="<?php echo $editDentist['working_end_time'] ?? ''; ?>" /></div>
                </div>
                <div class="grin-form-group"><label>Bio</label><textarea name="bio" rows="3"><?php echo htmlspecialchars($editDentist['bio'] ?? ''); ?></textarea></div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('editDentistModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Update Dentist</button>
            </div>
        </form>
    </div>
</div>

<?php if ($editDentist): ?>
<script>document.getElementById('editDentistModal').classList.add('show');</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
