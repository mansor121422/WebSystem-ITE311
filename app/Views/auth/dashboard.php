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
        <div class="student-dashboard">
            <h2 class="text-center mb-4">Student Dashboard</h2>
            
            <!-- Enrolled Courses Section -->
            <div class="mb-5">
                <h3 class="mb-3">
                    <i class="fas fa-book-open"></i> My Enrolled Courses
                </h3>
                
                <?php if (!empty($enrolledCourses)): ?>
                    <div class="row">
                        <?php foreach ($enrolledCourses as $enrollment): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($enrollment['course_title']) ?></h5>
                                        <p class="card-text text-muted"><?= esc($enrollment['course_description']) ?></p>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= esc($enrollment['course_instructor']) ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <?= esc($enrollment['course_duration']) ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="fas fa-calendar"></i> Enrolled: <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                            </small>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $enrollment['progress'] ?>%" 
                                                 aria-valuenow="<?= $enrollment['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">Progress: <?= $enrollment['progress'] ?>%</small>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?= $enrollment['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($enrollment['status']) ?>
                                        </span>
                                        <a href="<?= base_url('materials/view/' . $enrollment['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file-alt"></i> View Materials
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You are not enrolled in any courses yet. 
                        Browse available courses below to get started!
                    </div>
                <?php endif; ?>
            </div>

            <!-- Available Courses Section -->
            <div class="mb-5">
                <h3 class="mb-3">
                    <i class="fas fa-plus-circle"></i> Available Courses
                </h3>
                
                <?php if (!empty($availableCourses)): ?>
                    <div class="row">
                        <?php foreach ($availableCourses as $course): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                        <p class="card-text text-muted"><?= esc($course['description']) ?></p>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= esc($course['instructor']) ?>
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <?= esc($course['duration']) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <button class="btn btn-primary btn-sm enroll-btn" 
                                                data-course-id="<?= $course['id'] ?>"
                                                data-course-title="<?= esc($course['title']) ?>">
                                            <i class="fas fa-plus"></i> Enroll Now
                                        </button>
                                        <a href="<?= base_url('materials/view/' . $course['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-file-alt"></i> Materials
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No courses are currently available for enrollment.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Stats Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= count($enrolledCourses) ?></h5>
                            <p class="card-text">Enrolled Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning"><?= $completedAssignments ?? 0 ?></h5>
                            <p class="card-text">Completed Assignments</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info"><?= count($availableCourses) ?></h5>
                            <p class="card-text">Available Courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif($userRole === 'instructor' || $userRole === 'teacher'): ?>
        <!-- Teacher Dashboard -->
        <div class="teacher-dashboard-simple">
            <div class="teacher-content">
                <div class="welcome-message">
                    <h3>Welcome to the LMS System</h3>
                    <p>Your account is being processed. Please contact an administrator if you need assistance.</p>
                </div>
                
                <div class="logout-section">
                    <a href="<?= site_url('logout') ?>" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
        
    <?php elseif($userRole === 'admin'): ?>
        <!-- Admin Dashboard -->
        <div class="admin-dashboard-simple">
            <h2>Administrator Dashboard</h2>
            
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h4>User Management</h4>
                    <p>Manage users, roles, and permissions</p>
                    <a href="#" class="btn btn-primary">Manage Users</a>
                </div>
                <div class="dashboard-card">
                    <h4>System Settings</h4>
                    <p>Configure system settings and preferences</p>
                    <a href="#" class="btn btn-primary">System Settings</a>
                </div>
                <div class="dashboard-card">
                    <h4>Reports</h4>
                    <p>Generate system reports and analytics</p>
                    <a href="#" class="btn btn-primary">View Reports</a>
                </div>
                <div class="dashboard-card">
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
    
    <?php if($userRole !== 'instructor' && $userRole !== 'teacher'): ?>
    <div class="text-center mt-4">
        <a href="<?= site_url('logout') ?>" class="btn btn-primary">Logout</a>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
