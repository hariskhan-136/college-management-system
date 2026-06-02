<?php
require_once 'layout_header.php';

$student_id = $_SESSION['user_id'];

// Fetch quick stats
$stats = [
    'courses' => 0,
    'attendance_perc' => 100,
    'fee_status' => 'No Data'
];

try {
    // Enrolled courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $stats['courses'] = $stmt->fetchColumn();
    
    // Attendance %
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 WHEN status='Late' THEN 0.5 ELSE 0 END) as attended FROM attendance WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $att = $stmt->fetch();
    if ($att['total'] > 0) {
        $stats['attendance_perc'] = round(($att['attended'] / $att['total']) * 100);
    }
    
    // Fees
    $stmt = $pdo->prepare("SELECT status FROM fees WHERE student_id = ? ORDER BY due_date ASC LIMIT 1");
    $stmt->execute([$student_id]);
    $fee = $stmt->fetchColumn();
    if ($fee) $stats['fee_status'] = $fee;
    
} catch (PDOException $e) {}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> <?= date('F j, Y') ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                    <i class="fas fa-book fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Enrolled Courses</h6>
                    <h3 class="fw-bold mb-0"><?= $stats['courses'] ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Attendance</h6>
                    <h3 class="fw-bold mb-0"><?= $stats['attendance_perc'] ?>%</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle <?= ($stats['fee_status'] == 'Unpaid') ? 'bg-danger' : 'bg-info' ?> text-white d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                    <i class="fas fa-money-check-alt fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Fee Status</h6>
                    <h3 class="fw-bold mb-0"><?= $stats['fee_status'] ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card glass-card border-0 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="fas fa-bullhorn text-warning me-2"></i> Notice Board</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush border-radius-custom">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT n.*, u.name as author_name FROM notices n JOIN users u ON n.author_id = u.id WHERE n.target_audience IN ('All', 'Students') ORDER BY n.created_at DESC LIMIT 5");
                        $notices = $stmt->fetchAll();
                        
                        if (count($notices) > 0) {
                            foreach ($notices as $n) {
                                echo '<div class="list-group-item bg-transparent p-3 border-bottom border-secondary-subtle">
                                        <div class="d-flex w-100 justify-content-between mb-1">
                                            <h6 class="fw-bold text-primary mb-0">'.htmlspecialchars($n['title']).'</h6>
                                            <small class="text-muted">'.date('M j', strtotime($n['created_at'])).'</small>
                                        </div>
                                        <p class="mb-1 text-muted small">'.nl2br(htmlspecialchars($n['content'])).'</p>
                                        <small class="text-muted"><i class="fas fa-user-circle me-1"></i> '.htmlspecialchars($n['author_name']).'</small>
                                      </div>';
                            }
                        } else {
                            echo '<div class="text-center text-muted p-4"><i class="fas fa-inbox mb-2 fa-2x opacity-50"></i><p class="mb-0">No notices at this time.</p></div>';
                        }
                    } catch (PDOException $e) {}
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
