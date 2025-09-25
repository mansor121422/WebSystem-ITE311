<?php
// Get current user role and login status
$isLoggedIn = session('logged_in') ?? false;
$userRole = session('role') ?? '';
$currentPage = $page ?? '';
?>

<?php if ($isLoggedIn): ?>
<!-- Logged-in User Header -->
<nav class="navbar navbar-logged-in">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
            <strong>LMS</strong>
        </a>
        
        <ul class="navbar-nav navbar-nav-center">
            <li><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li><a class="nav-link" href="<?= base_url('courses') ?>">Courses</a></li>
            <li><a class="nav-link" href="<?= base_url('assignments') ?>">Assignments</a></li>
            <li><a class="nav-link" href="<?= base_url('grades') ?>">Grades</a></li>
        </ul>
    </div>
</nav>

<style>
.navbar-logged-in {
    background-color: #2c3e50;
    border-bottom: 1px solid #34495e;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar-logged-in .navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff;
    text-decoration: none;
}

.navbar-logged-in .navbar-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.navbar-logged-in .navbar-nav-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.navbar-logged-in .nav-link {
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.navbar-logged-in .nav-link:hover {
    color: #f1f5f9;
    background-color: rgba(255, 255, 255, 0.1);
}

.navbar-logged-in .nav-link.active {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.2);
}

.navbar-logged-in .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}
</style>

<?php else: ?>
<!-- Guest Navigation Bar -->
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