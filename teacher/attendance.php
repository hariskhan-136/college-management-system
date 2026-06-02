<?php
require_once 'layout_header.php';

$teacher_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');

// Fetch assigned classes
$classStmt = $pdo->prepare("SELECT id, name FROM classes WHERE teacher_id = ? ORDER BY name ASC");
$classStmt->execute([$teacher_id]);
$classes = $classStmt->fetchAll();

$students = [];
if ($class_id) {
    // Fetch students enrolled in the selected class
    $stuStmt = $pdo->prepare("
        SELECT u.id, u.name, a.status 
        FROM users u 
        JOIN enrollments e ON u.id = e.student_id 
        LEFT JOIN attendance a ON u.id = a.student_id AND a.class_id = e.class_id AND a.date = ?
        WHERE e.class_id = ? AND u.role = 'student' AND u.status = 'active'
        ORDER BY u.name ASC
    ");
    $stuStmt->execute([$date, $class_id]);
    $students = $stuStmt->fetchAll();
}

// Handle Form Submission for Attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $post_class_id = $_POST['class_id'];
    $post_date = $_POST['date'];
    $attendance_data = $_POST['attendance'] ?? []; // student_id => status

    try {
        $pdo->beginTransaction();
        
        // Delete existing records for this class and date to overwrite
        $delStmt = $pdo->prepare("DELETE FROM attendance WHERE class_id = ? AND date = ?");
        $delStmt->execute([$post_class_id, $post_date]);
        
        // Insert new records
        $insStmt = $pdo->prepare("INSERT INTO attendance (class_id, student_id, date, status, recorded_by) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($attendance_data as $student_id => $status) {
            $insStmt->execute([$post_class_id, $student_id, $post_date, $status, $teacher_id]);
        }
        
        $pdo->commit();
        setFlashMessage('success', 'Attendance recorded successfully!');
        redirect("teacher/attendance.php?class_id=$post_class_id&date=$post_date");
    } catch (PDOException $e) {
        $pdo->rollBack();
        setFlashMessage('danger', 'Failed to save attendance: ' . $e->getMessage());
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Mark Attendance</h2>
</div>

<div class="card glass-card border-0 mb-4">
    <div class="card-body">
        <form action="attendance.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-medium">Select Class</label>
                <select name="class_id" class="form-select" required>
                    <option value="">-- Choose Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($class_id == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Date</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-custom w-100">Load Students</button>
            </div>
        </form>
    </div>
</div>

<?php if ($class_id): ?>
<div class="card glass-card border-0">
    <div class="card-body">
        <?php if(count($students) > 0): ?>
            <form action="attendance.php" method="POST">
                <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
                
                <div class="table-responsive mb-4">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $stu): ?>
                            <tr>
                                <td class="fw-medium align-middle"><?= htmlspecialchars($stu['name']) ?></td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="radio" name="attendance[<?= $stu['id'] ?>]" value="Present" <?= ($stu['status'] == 'Present' || empty($stu['status'])) ? 'checked' : '' ?> required>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input bg-danger border-danger" type="radio" name="attendance[<?= $stu['id'] ?>]" value="Absent" <?= ($stu['status'] == 'Absent') ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input bg-warning border-warning" type="radio" name="attendance[<?= $stu['id'] ?>]" value="Late" <?= ($stu['status'] == 'Late') ? 'checked' : '' ?>>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-end">
                    <button type="submit" name="submit_attendance" class="btn btn-primary-custom px-5">
                        <i class="fas fa-save me-2"></i> Save Attendance
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i>
                <h5>No students found enrolled in this class.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'layout_footer.php'; ?>
