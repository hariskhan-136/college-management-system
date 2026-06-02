<?php
require_once 'layout_header.php';

// Fetch quick stats for teacher
$teacher_id = $_SESSION['user_id'];
$stats = [
    'classes' => 0,
    'students' => 0
];

try {
    // Assigned Classes Count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $stats['classes'] = $stmt->fetchColumn();
    
    // Students enrolled in those classes
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT e.student_id) 
        FROM enrollments e 
        JOIN classes c ON e.class_id = c.id 
        WHERE c.teacher_id = ?
    ");
    $stmt->execute([$teacher_id]);
    $stats['students'] = $stmt->fetchColumn();
} catch (PDOException $e) {}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Teacher Dashboard</h2>
    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> <?= date('F j, Y') ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-chalkboard fa-2x"></i>
                </div>
                <div>
                    <h5 class="text-muted mb-1">Assigned Classes</h5>
                    <h2 class="fw-bold mb-0"><?= $stats['classes'] ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
                <div>
                    <h5 class="text-muted mb-1">Total Students</h5>
                    <h2 class="fw-bold mb-0"><?= $stats['students'] ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Notice Board (Teacher & All) -->
    <div class="col-lg-12">
        <div class="card glass-card border-0 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="fas fa-bullhorn text-warning me-2"></i> Notice Board</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush border-radius-custom">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT n.*, u.name as author_name FROM notices n JOIN users u ON n.author_id = u.id WHERE n.target_audience IN ('All', 'Teachers') ORDER BY n.created_at DESC LIMIT 5");
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
