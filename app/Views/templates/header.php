<?php
// Get current user role and login status
$isLoggedIn = session('logged_in') ?? false;
$currentPage = $page ?? '';
?>

<?php if (!$isLoggedIn): ?>
<!-- Navigation Bar - Only show for guests (not logged in) -->
<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            LMS System
        </a>
        
        <ul class="navbar-nav">
            <li><a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
            <li><a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
            <li><a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
            <li><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
            <li><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
        </ul>
    </div>
</nav>
<?php endif; ?>