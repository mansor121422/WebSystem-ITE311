<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Learning Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/ITE311-MALIK/">LMS System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/ITE311-MALIK/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ITE311-MALIK/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/ITE311-MALIK/contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4">Contact Us</h1>
                <p class="lead">Get in touch with our team for support and inquiries</p>
                <hr class="my-4">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h2>Send us a Message</h2>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2>Contact Information</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Get in Touch</h5>
                        <p class="card-text">
                            <strong>Address:</strong><br>
                            Tuyan Integrated School<br>
                            Pulot, Tuyan<br>
                            Malapatan, Sarangani province
                        </p>
                        <p class="card-text">
                            <strong>Phone:</strong><br>
                            +1 (555) 123-4567
                        </p>
                        <p class="card-text">
                            <strong>Email:</strong><br>
                            mansor@lmssystem.com<br>
                            malik@lmssystem.com
                        </p>
                        <p class="card-text">
                            <strong>Business Hours:</strong><br>
                            Monday - Friday: 10:00 AM - 4:00 PM<br>
                            Saturday: 1:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 text-center">
                <h2>Need Immediate Help?</h2>
                <p>Our support team is available to assist you with any questions or technical issues.</p>
                <div class="d-grid gap-2 d-md-block">
                    <a href="/ITE311-MALIK/" class="btn btn-primary me-md-2">Back to Home</a>
                    <a href="/ITE311-MALIK/about" class="btn btn-outline-success">Learn More About Us</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p>&copy; 2024 Learning Management System. All rights reserved.</p>
            <p>
                <a href="/ITE311-MALIK/" class="text-white me-3">Home</a> |
                <a href="/ITE311-MALIK/about" class="text-white me-3">About</a> |
                <a href="/ITE311-MALIK/contact" class="text-white">Contact</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
