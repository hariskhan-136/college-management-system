<?php require_once 'includes/header.php'; ?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-5">
                <h2 class="fw-bold mb-4 text-center">About EduCore</h2>
                <p class="lead text-muted text-center mb-5">EduCore is a premium College Management System built to simplify educational administration.</p>
                
                <h4 class="fw-bold mb-3"><i class="fas fa-bullseye text-primary me-2"></i> Our Mission</h4>
                <p class="text-muted mb-4">To provide educational institutions with a reliable, scalable, and beautifully designed platform that reduces administrative overhead and enhances the learning experience for students.</p>

                <h4 class="fw-bold mb-3"><i class="fas fa-laptop-code text-primary me-2"></i> Technology Stack</h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent text-muted"><i class="fab fa-php text-primary me-2"></i> PHP 8.x (PDO) for robust backend logic.</li>
                    <li class="list-group-item bg-transparent text-muted"><i class="fas fa-database text-primary me-2"></i> MySQL for secure data management.</li>
                    <li class="list-group-item bg-transparent text-muted"><i class="fab fa-bootstrap text-primary me-2"></i> Bootstrap 5 for responsive design.</li>
                    <li class="list-group-item bg-transparent text-muted"><i class="fab fa-js text-primary me-2"></i> JavaScript & Chart.js for interactive UI.</li>
                </ul>

                <div class="text-center mt-5">
                    <a href="<?= BASE_URL ?>contact.php" class="btn btn-outline-primary rounded-pill px-4">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
