<?php require_once 'includes/header.php'; ?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-card overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-5 bg-primary text-white p-5 d-flex flex-column justify-content-center">
                        <h3 class="fw-bold mb-4">Get in Touch</h3>
                        <p class="mb-4">Have questions about EduCore? Fill out the form and our team will get back to you within 24 hours.</p>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span>123 Education Lane, Tech City</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <span>+1 (555) 123-4567</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="icon bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span>support@educore.com</span>
                        </div>
                    </div>
                    <div class="col-md-7 p-5">
                        <h4 class="fw-bold mb-4">Send us a Message</h4>
                        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Form submitted successfully!');">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100">Send Message <i class="fas fa-paper-plane ms-2"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
