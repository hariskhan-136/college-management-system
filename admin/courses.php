<?php
require_once 'layout_header.php';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $name = sanitize($_POST['name']);
            $code = sanitize($_POST['code']);
            $department_id = $_POST['department_id'];
            $credits = $_POST['credits'];
            
            $stmt = $pdo->prepare("INSERT INTO courses (name, code, department_id, credits) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $code, $department_id, $credits]);
            setFlashMessage('success', 'Course added successfully!');
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $name = sanitize($_POST['name']);
            $code = sanitize($_POST['code']);
            $department_id = $_POST['department_id'];
            $credits = $_POST['credits'];
            
            $stmt = $pdo->prepare("UPDATE courses SET name = ?, code = ?, department_id = ?, credits = ? WHERE id = ?");
            $stmt->execute([$name, $code, $department_id, $credits, $id]);
            setFlashMessage('success', 'Course updated successfully!');
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Course deleted successfully!');
        }
        redirect('admin/courses.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlashMessage('danger', 'Error: Course code must be unique.');
        } else {
            setFlashMessage('danger', 'Database error: ' . $e->getMessage());
        }
    }
}

// Fetch all courses with department names
$stmt = $pdo->query("SELECT c.*, d.name as dept_name FROM courses c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.name ASC");
$courses = $stmt->fetchAll();

// Fetch departments for dropdowns
$deptStmt = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC");
$departments = $deptStmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manage Courses</h2>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="fas fa-plus me-2"></i> Add Course
    </button>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Department</th>
                        <th>Credits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($courses) > 0): ?>
                        <?php foreach($courses as $course): ?>
                        <tr>
                            <td><?= $course['id'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($course['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($course['code']) ?></span></td>
                            <td><?= htmlspecialchars($course['dept_name']) ?></td>
                            <td><?= $course['credits'] ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editCourse(<?= $course['id'] ?>, '<?= htmlspecialchars(addslashes($course['name'])) ?>', '<?= htmlspecialchars(addslashes($course['code'])) ?>', <?= $course['department_id'] ?>, <?= $course['credits'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="courses.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $course['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No courses found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Add Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="courses.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                  <label class="form-label">Course Name</label>
                  <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Course Code</label>
                  <input type="text" name="code" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Department</label>
                  <select name="department_id" class="form-select" required>
                      <option value="">Select Department</option>
                      <?php foreach($departments as $d): ?>
                          <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label">Credits</label>
                  <input type="number" name="credits" class="form-control" required min="1" max="10">
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Save Course</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Edit Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="courses.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" id="edit_id">
              <div class="mb-3">
                  <label class="form-label">Course Name</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Course Code</label>
                  <input type="text" name="code" id="edit_code" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Department</label>
                  <select name="department_id" id="edit_department_id" class="form-select" required>
                      <?php foreach($departments as $d): ?>
                          <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label">Credits</label>
                  <input type="number" name="credits" id="edit_credits" class="form-control" required min="1" max="10">
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Update Course</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editCourse(id, name, code, dept_id, credits) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_department_id').value = dept_id;
    document.getElementById('edit_credits').value = credits;
    var modal = new bootstrap.Modal(document.getElementById('editCourseModal'));
    modal.show();
}
</script>

<?php require_once 'layout_footer.php'; ?>
