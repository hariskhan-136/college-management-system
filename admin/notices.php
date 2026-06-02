<?php
require_once 'layout_header.php';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $target = $_POST['target_audience'];
            $author_id = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("INSERT INTO notices (title, content, author_id, target_audience) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $author_id, $target]);
            setFlashMessage('success', 'Notice posted successfully!');
            
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Notice deleted successfully!');
        }
        redirect('admin/notices.php');
    } catch (PDOException $e) {
        setFlashMessage('danger', 'Database error: ' . $e->getMessage());
    }
}

// Fetch all notices
$stmt = $pdo->query("SELECT n.*, u.name as author_name FROM notices n JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC");
$notices = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Notice Board</h2>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
        <i class="fas fa-bullhorn me-2"></i> Post Notice
    </button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card glass-card border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush border-radius-custom">
                    <?php if(count($notices) > 0): ?>
                        <?php foreach($notices as $n): ?>
                        <div class="list-group-item bg-transparent p-4 border-bottom border-secondary-subtle">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold text-primary mb-0"><?= htmlspecialchars($n['title']) ?></h5>
                                <form action="notices.php" method="POST" onsubmit="return confirm('Delete this notice?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-times fa-lg"></i></button>
                                </form>
                            </div>
                            <p class="mb-3 text-muted"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
                            <div class="d-flex align-items-center small text-muted">
                                <span class="me-3"><i class="fas fa-user-edit me-1"></i> <?= htmlspecialchars($n['author_name']) ?></span>
                                <span class="me-3"><i class="fas fa-clock me-1"></i> <?= date('F j, Y g:i A', strtotime($n['created_at'])) ?></span>
                                <span><i class="fas fa-eye me-1"></i> Audience: 
                                    <?php 
                                    if($n['target_audience'] == 'All') echo '<span class="badge bg-secondary">All</span>';
                                    elseif($n['target_audience'] == 'Teachers') echo '<span class="badge bg-info">Teachers Only</span>';
                                    else echo '<span class="badge bg-primary">Students Only</span>';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>No notices posted yet.</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content glass-card">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Post New Notice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="notices.php" method="POST">
          <div class="modal-body">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                  <label class="form-label fw-medium">Notice Title</label>
                  <input type="text" name="title" class="form-control" required placeholder="Enter a catchy title...">
              </div>
              <div class="mb-3">
                  <label class="form-label fw-medium">Target Audience</label>
                  <select name="target_audience" class="form-select" required>
                      <option value="All">All (Teachers & Students)</option>
                      <option value="Teachers">Teachers Only</option>
                      <option value="Students">Students Only</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-medium">Content</label>
                  <textarea name="content" class="form-control" rows="6" required placeholder="Write the notice content here..."></textarea>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom"><i class="fas fa-paper-plane me-2"></i>Post Notice</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'layout_footer.php'; ?>
