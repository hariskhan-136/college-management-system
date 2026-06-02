<?php
require_once 'layout_header.php';

$student_id = $_SESSION['user_id'];

// Fetch results
try {
    $stmt = $pdo->prepare("
        SELECT r.marks_obtained, r.total_marks, r.grade, r.created_at, c.name as course_name, c.code as course_code 
        FROM results r 
        JOIN courses c ON r.course_id = c.id 
        WHERE r.student_id = ? 
        ORDER BY c.name ASC
    ");
    $stmt->execute([$student_id]);
    $results = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $results = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">My Results</h2>
    <button class="btn btn-outline-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Print Result</button>
</div>

<div class="card glass-card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Course</th>
                        <th>Marks Obtained</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th class="pe-4">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($results) > 0): ?>
                        <?php foreach($results as $r): 
                            $percentage = ($r['total_marks'] > 0) ? round(($r['marks_obtained'] / $r['total_marks']) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary"><?= htmlspecialchars($r['course_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($r['course_code']) ?></small>
                            </td>
                            <td class="align-middle fw-medium"><?= $r['marks_obtained'] ?></td>
                            <td class="align-middle text-muted"><?= $r['total_marks'] ?></td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="me-2"><?= $percentage ?>%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?= ($percentage >= 50) ? 'bg-success' : 'bg-danger' ?>" role="progressbar" style="width: <?= $percentage ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="pe-4 align-middle">
                                <?php 
                                    $badgeClass = 'bg-success';
                                    if(in_array($r['grade'], ['C','D'])) $badgeClass = 'bg-warning text-dark';
                                    if($r['grade'] == 'F') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?> px-3 fs-6 rounded-pill"><?= $r['grade'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-clipboard-list fa-3x mb-3 opacity-50 d-block"></i> No results published yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
