<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect($_SESSION['role'] . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, password, role, status FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                if ($user['status'] == 'inactive') {
                    $error = "Your account is inactive. Please contact the administrator.";
                } elseif ($password == $user['password']) {
                    // Password is correct, set sessions
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect to respective dashboard
                    redirect($user['role'] . '/index.php');
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "System error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduCore</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="glass-card auth-card text-center position-relative">
        <a href="index.php" class="position-absolute top-0 start-0 m-3 text-muted" style="font-size: 1.5rem;"><i class="fas fa-arrow-left"></i></a>
        
        <div class="auth-logo shadow-sm mb-4">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h3 class="fw-bold mb-2">Welcome Back</h3>
        <p class="text-muted mb-4">Sign in to your account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php displayFlashMessage(); ?>

        <form action="login.php" method="POST" class="text-start">
            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required placeholder="admin@college.com">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-medium">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" required placeholder="admin123">
                </div>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                </div>
                <a href="#" class="text-decoration-none text-primary">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 py-2">Login <i class="fas fa-sign-in-alt ms-2"></i></button>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
