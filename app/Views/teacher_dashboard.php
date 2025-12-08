<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="teacher-dashboard-page">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="flash-message success">
            <i class="fas fa-check-circle"></i>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="flash-message error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Compact Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h2><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h2>
            <p class="user-info"><?= esc($user['name']) ?> • <?= esc($user['email']) ?></p>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('announcements') ?>" class="header-btn">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
        </div>
    </div>

    <!-- Statistics Cards - Compact -->
    <div class="stats-compact">
        <div class="stat-item">
            <div class="stat-icon-wrapper">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number"><?= $assignmentsCount ?? 0 ?></span>
                <span class="stat-label">Total Assignments</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrapper">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number"><?= $pendingSubmissions ?? 0 ?></span>
                <span class="stat-label">Pending Submissions</span>
            </div>
        </div>
    </div>

    <!-- Recent Submissions Section -->
    <div class="section-title">
        <i class="fas fa-file-upload"></i> New Submissions
    </div>
    <?php if(empty($recentSubmissions ?? [])): ?>
        <div class="empty-state-submissions">
            <i class="fas fa-inbox fa-2x"></i>
            <p>No new submissions.</p>
        </div>
    <?php else: ?>
        <div class="submissions-list">
            <?php foreach($recentSubmissions as $submission): ?>
                <div class="submission-item">
                    <div class="submission-info">
                        <div class="submission-header">
                            <h4><?= esc($submission['assignment_title']) ?></h4>
                            <span class="submission-status status-<?= strtolower($submission['status']) ?>">
                                <?= ucfirst(esc($submission['status'])) ?>
                            </span>
                        </div>
                        <div class="submission-details">
                            <span class="detail-item">
                                <i class="fas fa-user"></i> <?= esc($submission['student_name']) ?>
                            </span>
                            <span class="detail-item">
                                <i class="fas fa-book"></i> <?= esc($submission['course_title']) ?>
                            </span>
                            <span class="detail-item">
                                <i class="fas fa-clock"></i> <?= date('M d, Y H:i', strtotime($submission['submitted_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="submission-actions">
                        <a href="<?= base_url('teacher/view-submissions/' . $submission['assignment_id']) ?>" 
                           class="btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Quick Actions - Compact Grid -->
    <div class="section-title">
        <i class="fas fa-bolt"></i> Quick Actions
    </div>
    <div class="actions-compact">
        <a href="<?= base_url('teacher/enroll-student') ?>" class="action-item">
            <i class="fas fa-user-plus"></i>
            <span>Enroll Student</span>
        </a>
        <a href="<?= base_url('teacher/create-assignment') ?>" class="action-item">
            <i class="fas fa-file-alt"></i>
            <span>Create Assignment</span>
        </a>
        <a href="<?= base_url('teacher/assignments') ?>" class="action-item">
            <i class="fas fa-clipboard-list"></i>
            <span>My Assignments</span>
        </a>
    </div>

    <!-- Courses Section - Compact -->
    <div class="section-title">
        <i class="fas fa-book"></i> Course Materials
    </div>
    <div class="courses-compact">
        <?php
        $courses = [
            ['id' => 1, 'title' => 'Introduction to Programming', 'color' => '#3498db'],
            ['id' => 2, 'title' => 'Web Development Basics', 'color' => '#2ecc71'],
            ['id' => 3, 'title' => 'Database Management', 'color' => '#e67e22'],
            ['id' => 4, 'title' => 'Data Structures & Algorithms', 'color' => '#9b59b6']
        ];
        foreach($courses as $course): ?>
            <div class="course-item" style="border-left-color: <?= $course['color'] ?>;">
                <div class="course-title"><?= esc($course['title']) ?></div>
                <div class="course-buttons">
                    <a href="<?= base_url('materials/view/' . $course['id']) ?>" class="btn-icon" title="View Materials">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn-icon" title="Upload Material">
                        <i class="fas fa-upload"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .teacher-dashboard-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    /* Flash Messages */
    .flash-message {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .flash-message.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .flash-message.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .flash-message i {
        font-size: 1.1rem;
    }

    /* Compact Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .header-left h2 {
        color: #2c3e50;
        font-size: 1.75rem;
        margin: 0 0 0.25rem 0;
        font-weight: 600;
    }

    .header-left h2 i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .user-info {
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }

    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #3498db;
        border-radius: 6px;
        color: #3498db;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .header-btn:hover {
        background: #3498db;
        color: white;
    }

    /* Compact Statistics */
    .stats-compact {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-item {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid #3498db;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        border-left-color: #2980b9;
    }

    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e3f2fd;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .stat-icon-wrapper i {
        font-size: 1.75rem;
        color: #3498db;
    }

    .stat-details {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* Section Title */
    .section-title {
        color: #2c3e50;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .section-title i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    /* Compact Actions */
    .actions-compact {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .action-item {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid #3498db;
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: #2c3e50;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .action-item:hover {
        background: #f8f9fa;
        border-left-color: #2980b9;
        transform: translateX(4px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        color: #2c3e50;
    }

    .action-item i {
        font-size: 1.5rem;
        color: #3498db;
        width: 30px;
        text-align: center;
    }

    .action-item span {
        font-weight: 500;
        font-size: 0.95rem;
    }

    /* Compact Courses */
    .courses-compact {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .course-item {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid #3498db;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .course-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .course-title {
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95rem;
        flex: 1;
    }

    .course-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #e9ecef;
    }

    .btn-icon:first-child {
        background: #e3f2fd;
        color: #1976d2;
        border-color: #bbdefb;
    }

    .btn-icon:first-child:hover {
        background: #bbdefb;
        color: #1565c0;
    }

    .btn-icon:last-child {
        background: #e8f5e9;
        color: #388e3c;
        border-color: #c8e6c9;
    }

    .btn-icon:last-child:hover {
        background: #c8e6c9;
        color: #2e7d32;
    }

    /* Recent Submissions */
    .empty-state-submissions {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        color: #6c757d;
        margin-bottom: 2rem;
    }

    .empty-state-submissions i {
        color: #dee2e6;
        margin-bottom: 0.5rem;
    }

    .submissions-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .submission-item {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid #3498db;
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .submission-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .submission-info {
        flex: 1;
    }

    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .submission-header h4 {
        margin: 0;
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 600;
    }

    .submission-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-submitted {
        background: #fff3cd;
        color: #856404;
    }

    .status-graded {
        background: #d4edda;
        color: #155724;
    }

    .submission-details {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .detail-item i {
        color: #3498db;
        font-size: 0.75rem;
    }

    .submission-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-view {
        padding: 0.5rem 1rem;
        background: #3498db;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }

    .btn-view:hover {
        background: #2980b9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .teacher-dashboard-page {
            padding: 1rem 0.5rem;
        }

        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .header-left h2 {
            font-size: 1.5rem;
        }

        .stats-compact {
            grid-template-columns: 1fr;
        }

        .actions-compact {
            grid-template-columns: 1fr;
        }

        .courses-compact {
            grid-template-columns: 1fr;
        }

        .course-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .course-buttons {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

