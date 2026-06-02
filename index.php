<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section py-5 my-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 text-center text-lg-start">
                <h1 class="display-4 fw-bold mb-3">Welcome to <span class="text-primary">EduCore</span></h1>
                <p class="lead text-muted mb-4">A complete, modern, and professional College Management System designed to streamline administrative tasks, manage student records, and facilitate seamless communication between teachers and students.</p>
                <div>
                    <a href="<?= BASE_URL ?>login.php" class="btn btn-primary-custom btn-lg me-3">Get Started</a>
                    <a href="<?= BASE_URL ?>about.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- Using a generic unsplash image as placeholder -->
                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="College Campus" class="img-fluid rounded-4 shadow-lg glass-card p-2">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light rounded-top-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose EduCore?</h2>
            <p class="text-muted">Premium features designed for modern educational institutions.</p>
        </div>
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card glass-card h-100 text-center border-0 p-4">
                    <div class="card-body">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h5 class="card-title fw-bold">Secure Access</h5>
                        <p class="card-text text-muted">Role-based access control ensuring data privacy for Admins, Teachers, and Students.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card glass-card h-100 text-center border-0 p-4">
                    <div class="card-body">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="card-title fw-bold">Advanced Analytics</h5>
                        <p class="card-text text-muted">Comprehensive dashboards with real-time statistics and visual charts for better decision-making.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card glass-card h-100 text-center border-0 p-4">
                    <div class="card-body">
                        <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h5 class="card-title fw-bold">Fast & Responsive</h5>
                        <p class="card-text text-muted">A fully responsive, glassmorphism UI that looks stunning on mobile, tablet, and desktop devices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
