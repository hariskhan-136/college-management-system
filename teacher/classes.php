<?php
require_once 'layout_header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch classes assigned to this teacher
try {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE teacher_id = ? ORDER BY name ASC");
    $stmt->execute([$teacher_id]);
    $classes = $stmt->fetchAll();
} catch (PDOException $e) {
    $classes = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">My Classes</h2>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Created At</th>
                        <th>Total Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($classes) > 0): ?>
                        <?php foreach($classes as $c): 
                            // Get enrolled count
                            $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE class_id = ?");
                            $cntStmt->execute([$c['id']]);
                            $enrollCount = $cntStmt->fetchColumn();
                        ?>
                        <tr>
                            <td class="fw-medium text-primary"><i class="fas fa-chalkboard me-2"></i><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                            <td><span class="badge bg-success rounded-pill px-3"><?= $enrollCount ?> Students</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">You have not been assigned to any classes yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
