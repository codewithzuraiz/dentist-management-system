<?php
include 'header.php';

$message = '';
$messageType = '';

// Handle Add Treatment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO treatments (name, description, cost, duration_minutes, required_equipment, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([
                $_POST['name'], $_POST['description'] ?? null,
                $_POST['cost'], $_POST['duration_minutes'] ?? null,
                $_POST['required_equipment'] ?? null
            ]);
            $message = 'Treatment added successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE treatments SET name=?, description=?, cost=?, duration_minutes=?, required_equipment=?, status=? WHERE id=?");
            $stmt->execute([
                $_POST['name'], $_POST['description'] ?? null,
                $_POST['cost'], $_POST['duration_minutes'] ?? null,
                $_POST['required_equipment'] ?? null, $_POST['status'] ?? 'active',
                $_POST['id']
            ]);
            $message = 'Treatment updated successfully!';
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
        $stmt = $pdo->prepare("DELETE FROM treatments WHERE id=?");
        $stmt->execute([$_POST['delete_id']]);
        $message = 'Treatment deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Error: Cannot delete - treatment is referenced by existing records.';
        $messageType = 'error';
    }
}

// Fetch treatments
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];
if ($search) {
    $where .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM treatments WHERE $where");
$countStmt->execute($params);
$totalRows = $countStmt->fetch()['c'];
$totalPages = ceil($totalRows / $perPage);

$stmt = $pdo->prepare("SELECT * FROM treatments WHERE $where ORDER BY name ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$treatments = $stmt->fetchAll();

$editTreatment = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM treatments WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editTreatment = $stmt->fetch();
}
?>

<?php if ($message): ?>
<div class="grin-alert grin-alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<div class="grin-page-header">
    <div class="grin-page-header-content">
        <h1>Services / Treatments</h1>
        <p>Manage dental treatments and services</p>
    </div>
    <div class="grin-header-actions">
        <button class="grin-btn grin-btn-primary" onclick="showAddModal('addTreatmentModal')">
            <i class="bx bx-plus-circle"></i> Add Treatment
        </button>
    </div>
</div>

<div class="grin-card">
    <div class="grin-card-header">
        <h2><i class="bx bx-heart"></i> All Treatments (<?php echo $totalRows; ?>)</h2>
        <div class="grin-card-header-actions">
            <form method="GET" class="grin-filter-form">
                <div class="grin-search-wrapper">
                    <input type="text" name="search" class="grin-search-input" placeholder="Search treatments..." value="<?php echo htmlspecialchars($search); ?>" />
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
                        <th>Treatment Name</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($treatments)): ?>
                    <tr><td colspan="7" class="grin-empty-state">No treatments found</td></tr>
                    <?php else: ?>
                    <?php foreach ($treatments as $i => $t): ?>
                    <tr>
                        <td><?php echo $offset + $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($t['name']); ?></td>
                        <td><?php echo htmlspecialchars($t['description'] ?? '-'); ?></td>
                        <td><?php echo formatCurrency($t['cost']); ?></td>
                        <td><?php echo $t['duration_minutes'] ? $t['duration_minutes'] . ' min' : '-'; ?></td>
                        <td><?php echo getStatusBadge($t['status']); ?></td>
                        <td>
                            <div class="grin-action-btns">
                                <a href="?edit=<?php echo $t['id']; ?>" class="grin-action-btn-small" title="Edit"><i class="bx bx-edit"></i></a>
                                <button class="grin-action-btn-small grin-btn-danger" title="Delete" onclick="deleteRecord('services.php', <?php echo $t['id']; ?>)"><i class="bx bx-trash"></i></button>
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

<!-- Add Treatment Modal -->
<div class="grin-modal" id="addTreatmentModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Add New Treatment</h2>
            <button class="grin-modal-close" onclick="hideModal('addTreatmentModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add" />
            <div class="grin-modal-body">
                <div class="grin-form-group"><label>Treatment Name *</label><input type="text" name="name" required /></div>
                <div class="grin-form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Cost (PKR) *</label><input type="number" name="cost" required min="0" step="100" /></div>
                    <div class="grin-form-group"><label>Duration (minutes)</label><input type="number" name="duration_minutes" min="0" /></div>
                </div>
                <div class="grin-form-group"><label>Required Equipment</label><input type="text" name="required_equipment" /></div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('addTreatmentModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Add Treatment</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Treatment Modal -->
<div class="grin-modal <?php echo $editTreatment ? 'show' : ''; ?>" id="editTreatmentModal">
    <div class="grin-modal-content">
        <div class="grin-modal-header">
            <h2>Edit Treatment</h2>
            <button class="grin-modal-close" onclick="hideModal('editTreatmentModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id" value="<?php echo $editTreatment['id'] ?? ''; ?>" />
            <div class="grin-modal-body">
                <div class="grin-form-group"><label>Treatment Name *</label><input type="text" name="name" required value="<?php echo htmlspecialchars($editTreatment['name'] ?? ''); ?>" /></div>
                <div class="grin-form-group"><label>Description</label><textarea name="description" rows="3"><?php echo htmlspecialchars($editTreatment['description'] ?? ''); ?></textarea></div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Cost (PKR) *</label><input type="number" name="cost" required min="0" step="100" value="<?php echo $editTreatment['cost'] ?? ''; ?>" /></div>
                    <div class="grin-form-group"><label>Duration (minutes)</label><input type="number" name="duration_minutes" min="0" value="<?php echo $editTreatment['duration_minutes'] ?? ''; ?>" /></div>
                </div>
                <div class="grin-form-row">
                    <div class="grin-form-group"><label>Required Equipment</label><input type="text" name="required_equipment" value="<?php echo htmlspecialchars($editTreatment['required_equipment'] ?? ''); ?>" /></div>
                    <div class="grin-form-group"><label>Status</label>
                        <select name="status">
                            <option value="active" <?php echo ($editTreatment['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editTreatment['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="grin-modal-footer">
                <button type="button" class="grin-btn grin-btn-secondary" onclick="hideModal('editTreatmentModal')">Cancel</button>
                <button type="submit" class="grin-btn grin-btn-primary">Update Treatment</button>
            </div>
        </form>
    </div>
</div>

<?php if ($editTreatment): ?>
<script>document.getElementById('editTreatmentModal').classList.add('show');</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
