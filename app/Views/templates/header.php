<?php
// Get current user role and login status with strict checking
$isLoggedIn = false;
$userRole = '';

// Strict session validation - all conditions must be met
$loggedIn = session('logged_in');
$userID = session('userID');
$role = session('role');

// Only consider user logged in if ALL session data is present and valid
if ($loggedIn === true && !empty($userID) && !empty($role) && is_numeric($userID)) {
    $isLoggedIn = true;
    $userRole = $role;
}

$currentPage = $page ?? '';
?>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="container <?= ($isLoggedIn && ($userRole === 'student' || $userRole === 'admin' || $userRole === 'instructor' || $userRole === 'teacher')) ? 'centered-nav' : '' ?>">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <strong>LMS</strong>
        </a>
        
        <ul class="navbar-nav">
            <?php if ($isLoggedIn): ?>
                <!-- Logged-in User Navigation -->
                <?php if ($userRole === 'student'): ?>
                    <!-- Student Navigation - Centered -->
                    <li><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li><a class="nav-link" href="<?= base_url('student/courses') ?>">Courses</a></li>
                    <li><a class="nav-link" href="<?= base_url('student/assignments') ?>">Assignments</a></li>
                    <li><a class="nav-link" href="<?= base_url('student/grades') ?>">Grades</a></li>
                    <li><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php elseif ($userRole === 'admin'): ?>
                    <!-- Admin Navigation - Centered -->
                    <li><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/users') ?>">Users</a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/courses') ?>">Courses</a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/reports') ?>">Reports</a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings') ?>">Settings</a></li>
                    <li><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php elseif ($userRole === 'instructor' || $userRole === 'teacher'): ?>
                    <!-- Teacher Navigation - Centered -->
                    <li><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li><a class="nav-link" href="<?= base_url('teacher/courses') ?>">Courses</a></li>
                    <li><a class="nav-link" href="<?= base_url('teacher/students') ?>">Students</a></li>
                    <li><a class="nav-link" href="<?= base_url('teacher/create') ?>">Create</a></li>
                    <li><a class="nav-link" href="<?= base_url('teacher/analytics') ?>">Lessons</a></li>
                    <li><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php else: ?>
                    <!-- Other roles navigation -->
                    <li><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
                <?php endif; ?>
            <?php else: ?>
                <!-- Guest Navigation -->
                <li><a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
                <li><a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
                <li><a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
                <li><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                <li><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
.navbar {
    background-color: #2c3e50;
    border-bottom: 1px solid #34495e;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar .navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff;
    text-decoration: none;
}

.navbar .navbar-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.navbar .nav-link {
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.navbar .nav-link:hover {
    color: #f1f5f9;
    background-color: rgba(255, 255, 255, 0.1);
}

.navbar .nav-link.active {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.2);
}

.navbar .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
}

/* Center navigation for students and admins */
.navbar .container.centered-nav {
    justify-content: center;
}

.navbar .container.centered-nav .navbar-brand {
    position: absolute;
    left: 20px;
}

@media (max-width: 768px) {
    .navbar .container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .navbar .navbar-nav {
        gap: 1rem;
    }
}
</style>
