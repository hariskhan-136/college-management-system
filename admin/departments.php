<?php
require_once 'layout_header.php';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $name = sanitize($_POST['name']);
            $code = sanitize($_POST['code']);
            
            $stmt = $pdo->prepare("INSERT INTO departments (name, code) VALUES (?, ?)");
            $stmt->execute([$name, $code]);
            setFlashMessage('success', 'Department added successfully!');
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $name = sanitize($_POST['name']);
            $code = sanitize($_POST['code']);
            
            $stmt = $pdo->prepare("UPDATE departments SET name = ?, code = ? WHERE id = ?");
            $stmt->execute([$name, $code, $id]);
            setFlashMessage('success', 'Department updated successfully!');
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Department deleted successfully!');
        }
        redirect('admin/departments.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlashMessage('danger', 'Error: Department code must be unique or department is currently in use.');
        } else {
            setFlashMessage('danger', 'Database error: ' . $e->getMessage());
        }
    }
}

// Fetch all departments
$stmt = $pdo->query("SELECT * FROM departments ORDER BY name ASC");
$departments = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manage Departments</h2>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        <i class="fas fa-plus me-2"></i> Add Department
    </button>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($departments) > 0): ?>
                        <?php foreach($departments as $dept): ?>
                        <tr>
                            <td><?= $dept['id'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($dept['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($dept['code']) ?></span></td>
                            <td><?= date('M j, Y', strtotime($dept['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editDept(<?= $dept['id'] ?>, '<?= htmlspecialchars(addslashes($dept['name'])) ?>', '<?= htmlspecialchars(addslashes($dept['code'])) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="departments.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No departments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Add Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="departments.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                  <label class="form-label">Department Name</label>
                  <input type="text" name="name" class="form-control" required placeholder="e.g. Computer Science">
              </div>
              <div class="mb-3">
                  <label class="form-label">Department Code</label>
                  <input type="text" name="code" class="form-control" required placeholder="e.g. CS">
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Save Department</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editDeptModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Edit Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="departments.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" id="edit_id">
              <div class="mb-3">
                  <label class="form-label">Department Name</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Department Code</label>
                  <input type="text" name="code" id="edit_code" class="form-control" required>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Update Department</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editDept(id, name, code) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    var modal = new bootstrap.Modal(document.getElementById('editDeptModal'));
    modal.show();
}
</script>

<?php require_once 'layout_footer.php'; ?>
