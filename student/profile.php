<?php
require_once 'layout_header.php';

$student_id = $_SESSION['user_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    
    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $password, $student_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $student_id]);
        }
        
        $_SESSION['name'] = $name; // Update session
        setFlashMessage('success', 'Profile updated successfully!');
        redirect('student/profile.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlashMessage('danger', 'Email is already in use by another account.');
        } else {
            setFlashMessage('danger', 'Error: ' . $e->getMessage());
        }
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$user = $stmt->fetch();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">My Profile</h2>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card glass-card border-0 text-center">
            <div class="card-body py-5">
                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($user['profile_pic']) ?>" class="rounded-circle mb-3 shadow" width="120" height="120">
                <h4 class="fw-bold"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
                <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">Student</span>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card glass-card border-0">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold">Edit Profile Details</h5>
            </div>
            <div class="card-body">
                <form action="profile.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">New Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary-custom px-4">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
