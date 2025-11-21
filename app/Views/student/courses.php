<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="student-courses-page">
    <h2 class="page-title">
        <i class="fas fa-book"></i> My Enrolled Courses
    </h2>

    <?php if(empty($courses)): ?>
        <div class="empty-state">
            <i class="fas fa-book-open fa-3x"></i>
            <h3>No Enrolled Courses</h3>
            <p>You haven't enrolled in any courses yet.</p>
            <a href="<?= base_url('dashboard') ?>" class="btn-primary">Browse Courses</a>
        </div>
    <?php else: ?>
        <div class="courses-grid">
            <?php foreach($courses as $course): ?>
                <div class="course-card">
                    <div class="course-header">
                        <h3><?= esc($course['title']) ?></h3>
                        <span class="course-status <?= $course['status'] ?>"><?= ucfirst($course['status']) ?></span>
                    </div>
                    <div class="course-body">
                        <p class="course-description"><?= esc($course['description']) ?></p>
                        <div class="course-info">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span><?= esc($course['instructor']) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span><?= esc($course['duration']) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Enrolled: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></span>
                            </div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Progress</span>
                                <span class="progress-percent"><?= number_format($course['progress'], 2) ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $course['progress'] ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="course-footer">
                        <a href="<?= base_url('materials/view/' . $course['id']) ?>" class="btn-action">
                            <i class="fas fa-eye"></i> View Materials
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .student-courses-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    .page-title {
        color: #2c3e50;
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-title i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }

    .empty-state i {
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .course-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
        overflow: hidden;
    }

    .course-card:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .course-header {
        padding: 1.25rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .course-header h3 {
        color: #2c3e50;
        font-size: 1.1rem;
        margin: 0;
        font-weight: 600;
    }

    .course-status {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .course-status.active {
        background: #d4edda;
        color: #155724;
    }

    .course-body {
        padding: 1.25rem;
    }

    .course-description {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .course-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .info-item i {
        color: #3498db;
        width: 20px;
    }

    .progress-section {
        margin-top: 1rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .progress-percent {
        font-weight: 600;
        color: #2c3e50;
    }

    .progress-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: #3498db;
        transition: width 0.3s;
    }

    .course-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
        color: white;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: #2980b9;
        color: white;
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>

