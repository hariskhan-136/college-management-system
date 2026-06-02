<?php
require_once 'layout_header.php';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $name = sanitize($_POST['name']);
            $teacher_id = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;
            
            $stmt = $pdo->prepare("INSERT INTO classes (name, teacher_id) VALUES (?, ?)");
            $stmt->execute([$name, $teacher_id]);
            setFlashMessage('success', 'Class added successfully!');
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $name = sanitize($_POST['name']);
            $teacher_id = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;
            
            $stmt = $pdo->prepare("UPDATE classes SET name = ?, teacher_id = ? WHERE id = ?");
            $stmt->execute([$name, $teacher_id, $id]);
            setFlashMessage('success', 'Class updated successfully!');
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Class deleted successfully!');
        }
        redirect('admin/classes.php');
    } catch (PDOException $e) {
        setFlashMessage('danger', 'Database error: ' . $e->getMessage());
    }
}

// Fetch all classes with advisor name
$stmt = $pdo->query("SELECT c.*, u.name as teacher_name FROM classes c LEFT JOIN users u ON c.teacher_id = u.id ORDER BY c.name ASC");
$classes = $stmt->fetchAll();

// Fetch teachers for dropdowns
$teacherStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'teacher' AND status = 'active' ORDER BY name ASC");
$teachers = $teacherStmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manage Classes</h2>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addClassModal">
        <i class="fas fa-plus me-2"></i> Add Class
    </button>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Class Name</th>
                        <th>Class Advisor</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($classes) > 0): ?>
                        <?php foreach($classes as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($c['name']) ?></td>
                            <td>
                                <?php if($c['teacher_name']): ?>
                                    <span class="badge bg-info text-dark"><i class="fas fa-chalkboard-teacher me-1"></i> <?= htmlspecialchars($c['teacher_name']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editClass(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', '<?= $c['teacher_id'] ?: '' ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="classes.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this class?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No classes found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Add Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="classes.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                  <label class="form-label">Class Name</label>
                  <input type="text" name="name" class="form-control" required placeholder="e.g. BSCS Year 1">
              </div>
              <div class="mb-3">
                  <label class="form-label">Assign Advisor (Optional)</label>
                  <select name="teacher_id" class="form-select">
                      <option value="">-- Unassigned --</option>
                      <?php foreach($teachers as $t): ?>
                          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Save Class</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Edit Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="classes.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" id="edit_id">
              <div class="mb-3">
                  <label class="form-label">Class Name</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Assign Advisor (Optional)</label>
                  <select name="teacher_id" id="edit_teacher_id" class="form-select">
                      <option value="">-- Unassigned --</option>
                      <?php foreach($teachers as $t): ?>
                          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Update Class</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editClass(id, name, teacher_id) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_teacher_id').value = teacher_id;
    var modal = new bootstrap.Modal(document.getElementById('editClassModal'));
    modal.show();
}
</script>

<?php require_once 'layout_footer.php'; ?>
