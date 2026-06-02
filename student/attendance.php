<?php
require_once 'layout_header.php';

$student_id = $_SESSION['user_id'];

// Fetch attendance records
try {
    $stmt = $pdo->prepare("
        SELECT a.date, a.status, c.name as class_name 
        FROM attendance a 
        JOIN classes c ON a.class_id = c.id 
        WHERE a.student_id = ? 
        ORDER BY a.date DESC
    ");
    $stmt->execute([$student_id]);
    $attendance = $stmt->fetchAll();
    
    // Calculate overall percentage
    $total = count($attendance);
    $attended = 0;
    foreach($attendance as $a) {
        if ($a['status'] == 'Present') $attended += 1;
        if ($a['status'] == 'Late') $attended += 0.5;
    }
    $percentage = ($total > 0) ? round(($attended / $total) * 100) : 100;
    
} catch (PDOException $e) {
    $attendance = [];
    $percentage = 0;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">My Attendance</h2>
    <div class="glass-card px-4 py-2 text-center rounded-pill border-primary">
        <h5 class="mb-0 text-primary fw-bold">Overall: <?= $percentage ?>%</h5>
    </div>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Class Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($attendance) > 0): ?>
                        <?php foreach($attendance as $a): ?>
                        <tr>
                            <td class="fw-medium"><i class="far fa-calendar-alt me-2 text-muted"></i><?= date('M j, Y', strtotime($a['date'])) ?></td>
                            <td><?= htmlspecialchars($a['class_name']) ?></td>
                            <td>
                                <?php if($a['status'] == 'Present'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Present</span>
                                <?php elseif($a['status'] == 'Absent'): ?>
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Absent</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Late</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">No attendance records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
