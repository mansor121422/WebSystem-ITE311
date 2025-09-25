<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Welcome to CodeIgniter with Bootstrap</h1>
        <p class="lead mb-4">A modern web application built with CodeIgniter 4 and Bootstrap 5</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= base_url('/register') ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-play-circle"></i> Get Started
            </a>
            <a href="<?= base_url('/about') ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-info-circle"></i> Learn More
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-6 fw-bold">Key Features</h2>
                <p class="lead text-muted">Discover what makes our application special</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 feature-card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-lightning-charge fs-3"></i>
                        </div>
                        <h5 class="card-title">Fast Performance</h5>
                        <p class="card-text text-muted">Built with CodeIgniter 4 for optimal speed and efficiency.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 feature-card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-phone fs-3"></i>
                        </div>
                        <h5 class="card-title">Responsive Design</h5>
                        <p class="card-text text-muted">Mobile-first approach with Bootstrap 5 for all devices.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 feature-card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <h5 class="card-title">Secure & Reliable</h5>
                        <p class="card-text text-muted">Enterprise-grade security with modern development practices.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="fw-bold mb-3">Ready to get started?</h3>
                <p class="text-muted mb-4">Join thousands of developers building amazing applications with CodeIgniter and Bootstrap.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= base_url('/contact') ?>" class="btn btn-primary">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-book"></i> View Documentation
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="bi bi-github"></i> GitHub
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap Components Demo -->
<section class="py-5">
    <div class="container">
        <h3 class="text-center mb-5">Bootstrap Components Demo</h3>
        
        <!-- Alerts -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <strong>Success!</strong> Bootstrap is successfully integrated!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        
        <!-- Progress Bars -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Loading Progress</h6>
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Skill Level</h6>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">90%</div>
                </div>
            </div>
        </div>
        
        <!-- Badges and Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="mb-3">Interactive Elements</h6>
                <button class="btn btn-primary me-2 mb-2">
                    Primary Button <span class="badge bg-light text-dark">New</span>
                </button>
                <button class="btn btn-secondary me-2 mb-2">
                    Secondary <span class="badge bg-warning text-dark">Hot</span>
                </button>
                <button class="btn btn-success me-2 mb-2">
                    Success <span class="badge bg-info">Info</span>
                </button>
                <button class="btn btn-danger me-2 mb-2">
                    Danger <span class="badge bg-dark">Dark</span>
                </button>
            </div>
        </div>
        
        <!-- Cards with different styles -->
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-star"></i> Featured
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-check-circle"></i> Verified
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="<?= base_url('/about') ?>" class="btn btn-success">Learn more</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-exclamation-triangle"></i> Warning
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Another card</h5>
                        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
                        <a href="<?= base_url('/contact') ?>" class="btn btn-warning">Get in Touch</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
