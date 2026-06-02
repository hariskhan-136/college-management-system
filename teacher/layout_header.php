<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is a teacher
if (!isLoggedIn() || !hasRole('teacher')) {
    setFlashMessage('danger', 'Access Denied. You must be a teacher to access this area.');
    redirect('login.php');
}

// Get current page for active nav
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EduCore</title>
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

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4 class="fw-bold mb-0 text-white"><i class="fas fa-chalkboard-teacher text-success me-2"></i>Teacher</h4>
        </div>
        <ul class="list-unstyled components">
            <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                <a href="index.php"><i class="fas fa-home fa-fw me-2"></i> Dashboard</a>
            </li>
            <li class="<?= ($current_page == 'classes.php') ? 'active' : '' ?>">
                <a href="classes.php"><i class="fas fa-users-class fa-fw me-2"></i> My Classes</a>
            </li>
            <li class="<?= ($current_page == 'attendance.php') ? 'active' : '' ?>">
                <a href="attendance.php"><i class="fas fa-clipboard-list fa-fw me-2"></i> Attendance</a>
            </li>
            <li class="<?= ($current_page == 'results.php') ? 'active' : '' ?>">
                <a href="results.php"><i class="fas fa-file-alt fa-fw me-2"></i> Upload Marks</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="content" class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light glass-card mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-success shadow-sm">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center ms-auto">
                    <button id="theme-toggle-btn" class="theme-toggle me-3"><i class="fas fa-moon"></i></button>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= BASE_URL ?>uploads/default.png" alt="" width="32" height="32" class="rounded-circle me-2 border">
                            <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2 text-muted"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <?php displayFlashMessage(); ?>
