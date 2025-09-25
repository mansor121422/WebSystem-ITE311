<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card">
    <h2 class="text-center">Dashboard</h2>
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <div class="text-center">
        <h3>Hello, <?= session('name') ?>!</h3>
        <p><strong>Email:</strong> <?= session('email') ?></p>
        <p><strong>Role:</strong> <?= ucfirst(session('role')) ?></p>
    </div>
    
    <!-- Role-based Dashboard Content -->
    <?php $userRole = session('role'); ?>
    
    <?php if($userRole === 'student'): ?>
        <!-- Student Dashboard -->
        <div class="mt-4">
            <h3>Student Dashboard</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;">
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>My Courses</h4>
                    <p>View and access your enrolled courses</p>
                    <a href="#" class="btn btn-primary">View Courses</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Assignments</h4>
                    <p>Check your pending assignments</p>
                    <a href="#" class="btn btn-primary">View Assignments</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Grades</h4>
                    <p>View your grades and progress</p>
                    <a href="#" class="btn btn-primary">View Grades</a>
                </div>
            </div>
        </div>
        
    <?php elseif($userRole === 'instructor'): ?>
        <!-- Instructor Dashboard -->
        <div class="mt-4">
            <h3>Instructor Dashboard</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;">
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>My Courses</h4>
                    <p>Manage your courses and content</p>
                    <a href="#" class="btn btn-primary">Manage Courses</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Students</h4>
                    <p>View and manage your students</p>
                    <a href="#" class="btn btn-primary">View Students</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Create Content</h4>
                    <p>Create lessons, quizzes, and assignments</p>
                    <a href="#" class="btn btn-primary">Create Content</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Analytics</h4>
                    <p>View course analytics and reports</p>
                    <a href="#" class="btn btn-primary">View Analytics</a>
                </div>
            </div>
        </div>
        
    <?php elseif($userRole === 'admin'): ?>
        <!-- Admin Dashboard -->
        <div class="mt-4">
            <h3>Administrator Dashboard</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;">
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>User Management</h4>
                    <p>Manage users, roles, and permissions</p>
                    <a href="#" class="btn btn-primary">Manage Users</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>System Settings</h4>
                    <p>Configure system settings and preferences</p>
                    <a href="#" class="btn btn-primary">System Settings</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Reports</h4>
                    <p>Generate system reports and analytics</p>
                    <a href="#" class="btn btn-primary">View Reports</a>
                </div>
                <div class="card" style="background-color: #f8f9fa;">
                    <h4>Course Management</h4>
                    <p>Oversee all courses and content</p>
                    <a href="#" class="btn btn-primary">Manage All Courses</a>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Default Dashboard for unknown roles -->
        <div class="mt-4">
            <h3>Welcome to the LMS System</h3>
            <p>Your account is being processed. Please contact an administrator if you need assistance.</p>
        </div>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="<?= site_url('logout') ?>" class="btn btn-primary">Logout</a>
    </div>
</div>
<?= $this->endSection() ?>
