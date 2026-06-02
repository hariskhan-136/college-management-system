<?php if(!str_contains($_SERVER['PHP_SELF'], 'login.php') && !str_contains($_SERVER['PHP_SELF'], 'admin/') && !str_contains($_SERVER['PHP_SELF'], 'student/') && !str_contains($_SERVER['PHP_SELF'], 'teacher/')): ?>
<footer class="mt-auto py-4">
  <div class="container text-center">
    <div class="row">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>EduCore CMS</h5>
            <p class="text-muted small">Empowering education with modern technology.</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <h6 class="fw-bold">Quick Links</h6>
            <ul class="list-unstyled small">
                <li><a href="<?= BASE_URL ?>index.php" class="text-decoration-none text-muted">Home</a></li>
                <li><a href="<?= BASE_URL ?>about.php" class="text-decoration-none text-muted">About</a></li>
                <li><a href="<?= BASE_URL ?>contact.php" class="text-decoration-none text-muted">Contact</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h6 class="fw-bold">Follow Us</h6>
            <div>
                <a href="#" class="text-muted me-2"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="#" class="text-muted me-2"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" class="text-muted me-2"><i class="fab fa-linkedin fa-lg"></i></a>
                <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
            </div>
        </div>
    </div>
    <hr class="mt-4 mb-3 border-secondary">
    <p class="text-muted mb-0 small">&copy; <?= date('Y') ?> EduCore College Management System. All rights reserved.</p>
  </div>
</footer>
<?php endif; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js (Loaded globally but used mostly in dashboards) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
