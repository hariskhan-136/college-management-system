<?php
require_once 'layout_header.php';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $name = sanitize($_POST['name']);
            $email = sanitize($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            $status = $_POST['status'];
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role, $status]);
            setFlashMessage('success', 'User added successfully!');
            
        } elseif ($action == 'edit') {
            $id = $_POST['id'];
            $name = sanitize($_POST['name']);
            $email = sanitize($_POST['email']);
            $role = $_POST['role'];
            $status = $_POST['status'];
            
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $email, $password, $role, $status, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $status, $id]);
            }
            setFlashMessage('success', 'User updated successfully!');
            
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            if ($id == $_SESSION['user_id']) {
                setFlashMessage('warning', 'You cannot delete yourself.');
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                setFlashMessage('success', 'User deleted successfully!');
            }
        }
        redirect('admin/users.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlashMessage('danger', 'Error: Email already exists.');
        } else {
            setFlashMessage('danger', 'Database error: ' . $e->getMessage());
        }
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY role ASC, name ASC");
$users = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manage Users</h2>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-2"></i> Add User
    </button>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($u['profile_pic']) ?>" class="rounded-circle me-2 border" width="35" height="35">
                                    <span class="fw-medium"><?= htmlspecialchars($u['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php if($u['role'] == 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif($u['role'] == 'teacher'): ?>
                                    <span class="badge bg-success">Teacher</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Student</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($u['status'] == 'active'): ?>
                                    <span class="badge bg-success bg-opacity-75">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-75">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>', '<?= htmlspecialchars(addslashes($u['email'])) ?>', '<?= $u['role'] ?>', '<?= $u['status'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form action="users.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="users.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Email Address</label>
                  <input type="email" name="email" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" required minlength="6">
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="form-label">Role</label>
                      <select name="role" class="form-select" required>
                          <option value="student">Student</option>
                          <option value="teacher">Teacher</option>
                          <option value="admin">Admin</option>
                      </select>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label">Status</label>
                      <select name="status" class="form-select" required>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                      </select>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Save User</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="users.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" id="edit_id">
              <div class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Email Address</label>
                  <input type="email" name="email" id="edit_email" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label class="form-label">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                  <input type="password" name="password" class="form-control" minlength="6">
              </div>
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="form-label">Role</label>
                      <select name="role" id="edit_role" class="form-select" required>
                          <option value="student">Student</option>
                          <option value="teacher">Teacher</option>
                          <option value="admin">Admin</option>
                      </select>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label">Status</label>
                      <select name="status" id="edit_status" class="form-select" required>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                      </select>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary-custom">Update User</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editUser(id, name, email, role, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_status').value = status;
    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}
</script>

<?php require_once 'layout_footer.php'; ?>
