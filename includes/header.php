<?php
// includes/header.php
require_once 'config.php';
require_once 'functions.php';

// Get current page name for active nav link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Management System</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<?php if(!str_contains($_SERVER['PHP_SELF'], 'login.php') && !str_contains($_SERVER['PHP_SELF'], 'admin/') && !str_contains($_SERVER['PHP_SELF'], 'student/') && !str_contains($_SERVER['PHP_SELF'], 'teacher/')): ?>
<!-- Public Navbar -->
<nav class="navbar navbar-expand-lg custom-navbar sticky-top glass-card" style="border-radius: 0; border-top: none; border-left: none; border-right: none;">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?>index.php">
        <i class="fas fa-graduation-cap me-2"></i>EduCore
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'index.php') ? 'active text-primary' : '' ?>" href="<?= BASE_URL ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'about.php') ? 'active text-primary' : '' ?>" href="<?= BASE_URL ?>about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page == 'contact.php') ? 'active text-primary' : '' ?>" href="<?= BASE_URL ?>contact.php">Contact</a>
        </li>
        <li class="nav-item ms-3">
          <button id="theme-toggle-btn" class="theme-toggle"><i class="fas fa-moon"></i></button>
        </li>
        <li class="nav-item ms-3">
            <?php if(isLoggedIn()): ?>
                <a class="btn btn-primary-custom" href="<?= BASE_URL . $_SESSION['role'] ?>/index.php">Dashboard</a>
            <?php else: ?>
                <a class="btn btn-primary-custom" href="<?= BASE_URL ?>login.php">Login <i class="fas fa-sign-in-alt ms-1"></i></a>
            <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<!-- Flash Messages Container -->
<div class="container mt-3">
    <?php displayFlashMessage(); ?>
</div>
