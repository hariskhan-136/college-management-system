<?php
require_once 'layout_header.php';

$teacher_id = $_SESSION['user_id'];
$class_id = $_GET['class_id'] ?? '';
$course_id = $_GET['course_id'] ?? '';

// Fetch assigned classes
$classStmt = $pdo->prepare("SELECT id, name FROM classes WHERE teacher_id = ? ORDER BY name ASC");
$classStmt->execute([$teacher_id]);
$classes = $classStmt->fetchAll();

// Fetch courses for the dropdown
$courseStmt = $pdo->query("SELECT id, name, code FROM courses ORDER BY name ASC");
$courses = $courseStmt->fetchAll();

$students = [];
if ($class_id && $course_id) {
    // Fetch students enrolled in this class and course, along with existing results
    $stuStmt = $pdo->prepare("
        SELECT u.id, u.name, r.marks_obtained, r.total_marks, r.grade 
        FROM users u 
        JOIN enrollments e ON u.id = e.student_id 
        LEFT JOIN results r ON u.id = r.student_id AND r.course_id = ? 
        WHERE e.class_id = ? AND e.course_id = ? AND u.role = 'student' AND u.status = 'active'
        ORDER BY u.name ASC
    ");
    $stuStmt->execute([$course_id, $class_id, $course_id]);
    $students = $stuStmt->fetchAll();
}

// Handle Form Submission for Results
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_results'])) {
    $post_class_id = $_POST['class_id'];
    $post_course_id = $_POST['course_id'];
    $marks_data = $_POST['marks_obtained'] ?? [];
    $total_data = $_POST['total_marks'] ?? [];

    try {
        $pdo->beginTransaction();
        
        // Delete existing records to overwrite
        $delStmt = $pdo->prepare("DELETE FROM results WHERE course_id = ? AND student_id IN (SELECT student_id FROM enrollments WHERE class_id = ? AND course_id = ?)");
        $delStmt->execute([$post_course_id, $post_class_id, $post_course_id]);
        
        $insStmt = $pdo->prepare("INSERT INTO results (student_id, course_id, marks_obtained, total_marks, grade, recorded_by) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($marks_data as $student_id => $obtained) {
            $total = $total_data[$student_id] ?? 100;
            if ($obtained !== '' && $total > 0) {
                // Calculate Grade
                $percentage = ($obtained / $total) * 100;
                $grade = 'F';
                if ($percentage >= 90) $grade = 'A+';
                elseif ($percentage >= 80) $grade = 'A';
                elseif ($percentage >= 70) $grade = 'B';
                elseif ($percentage >= 60) $grade = 'C';
                elseif ($percentage >= 50) $grade = 'D';

                $insStmt->execute([$student_id, $post_course_id, $obtained, $total, $grade, $teacher_id]);
            }
        }
        
        $pdo->commit();
        setFlashMessage('success', 'Results saved successfully!');
        redirect("teacher/results.php?class_id=$post_class_id&course_id=$post_course_id");
    } catch (PDOException $e) {
        $pdo->rollBack();
        setFlashMessage('danger', 'Failed to save results: ' . $e->getMessage());
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Upload Results / Marks</h2>
</div>

<div class="card glass-card border-0 mb-4">
    <div class="card-body">
        <form action="results.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-medium">Select Class</label>
                <select name="class_id" class="form-select" required>
                    <option value="">-- Choose Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($class_id == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-medium">Select Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Choose Course --</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($course_id == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['code'] . ' - ' . $c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-custom w-100">Load Students</button>
            </div>
        </form>
    </div>
</div>

<?php if ($class_id && $course_id): ?>
<div class="card glass-card border-0">
    <div class="card-body">
        <?php if(count($students) > 0): ?>
            <form action="results.php" method="POST">
                <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">
                <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
                
                <div class="table-responsive mb-4">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Marks Obtained</th>
                                <th>Total Marks</th>
                                <th>Calculated Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $stu): ?>
                            <tr>
                                <td class="fw-medium align-middle"><?= htmlspecialchars($stu['name']) ?></td>
                                <td style="width: 200px;">
                                    <input type="number" step="0.01" name="marks_obtained[<?= $stu['id'] ?>]" class="form-control" value="<?= htmlspecialchars($stu['marks_obtained'] ?? '') ?>" placeholder="e.g. 85.5">
                                </td>
                                <td style="width: 200px;">
                                    <input type="number" step="0.01" name="total_marks[<?= $stu['id'] ?>]" class="form-control" value="<?= htmlspecialchars($stu['total_marks'] ?? '100') ?>">
                                </td>
                                <td class="align-middle">
                                    <?php if(!empty($stu['grade'])): ?>
                                        <span class="badge bg-primary px-3 fs-6"><?= $stu['grade'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Pending...</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info py-2">
                    <i class="fas fa-info-circle me-2"></i> Grades are calculated automatically based on the percentage (A+, A, B, C, D, F). Leave "Marks Obtained" empty to skip a student.
                </div>
                
                <div class="text-end">
                    <button type="submit" name="submit_results" class="btn btn-primary-custom px-5">
                        <i class="fas fa-save me-2"></i> Save Results
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-file-excel fa-3x mb-3 opacity-50"></i>
                <h5>No students are enrolled in this class for the selected course.</h5>
                <p>Please ensure students are enrolled before assigning marks.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'layout_footer.php'; ?>
