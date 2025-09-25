<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card">
    <h1 class="text-center">About Our LMS System</h1>
    <p class="text-center">Empowering education through innovative technology and comprehensive learning solutions</p>
    
    <h2>Our Mission</h2>
    <p>We are dedicated to revolutionizing education by providing a comprehensive Learning Management System that connects students, instructors, and administrators in a seamless digital learning environment.</p>
    
    <h2>Key Features</h2>
    <ul>
        <li><strong>Innovation:</strong> Cutting-edge technology to enhance learning experiences</li>
        <li><strong>Community:</strong> Building strong learning communities and connections</li>
        <li><strong>Accessibility:</strong> Easy-to-use interface for all users</li>
        <li><strong>Flexibility:</strong> Adaptable to different learning styles and needs</li>
    </ul>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('contact') ?>" class="btn btn-primary">Get in Touch</a>
        <a href="<?= base_url('/') ?>" class="btn">Back to Home</a>
    </div>
</div>
<?= $this->endSection() ?>
