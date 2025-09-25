<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LMS System' ?></title>
    
    <!-- Simple CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .navbar {
            background-color: #2c3e50;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav-link {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background-color: #34495e;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #34495e;
        }
        
        .btn-primary {
            background-color: #2c3e50;
        }
        
        .btn-success {
            background-color: #2c3e50;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 2rem;
            margin: 1rem 0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .navbar-nav {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                LMS System
            </a>
            
            <ul class="navbar-nav">
                <li><a class="nav-link <?= ($page ?? '') === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
                <li><a class="nav-link <?= ($page ?? '') === 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
                <li><a class="nav-link <?= ($page ?? '') === 'contact' ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
                <?php if (session()->get('isLoggedIn')): ?>
                    <li><a class="nav-link" href="<?= base_url('logout') ?>" style="color: #e74c3c;">Logout</a></li>
                <?php else: ?>
                    <li><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> LMS System. All rights reserved.</p>
    </footer>
</body>
</html>
