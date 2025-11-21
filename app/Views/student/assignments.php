<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="student-assignments-page">
    <h2 class="page-title">
        <i class="fas fa-file-alt"></i> My Assignments
    </h2>

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

    <?php if(empty($assignments)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt fa-3x"></i>
            <h3>No assignments available</h3>
            <p>You don't have any assignments for your enrolled courses yet.</p>
        </div>
    <?php else: ?>
        <div class="assignments-grid">
            <?php foreach($assignments as $assignment): ?>
                <div class="assignment-card">
                    <div class="assignment-header">
                        <h3><?= esc($assignment['title']) ?></h3>
                        <span class="course-badge"><?= esc($assignment['course_title']) ?></span>
                    </div>
                    <div class="assignment-body">
                        <?php if(!empty($assignment['description'])): ?>
                            <p class="assignment-preview"><?= esc(substr($assignment['description'], 0, 100)) ?><?= strlen($assignment['description']) > 100 ? '...' : '' ?></p>
                        <?php endif; ?>
                        
                        <div class="assignment-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Posted: <?= date('M d, Y', strtotime($assignment['created_at'])) ?></span>
                            </div>
                            
                            <?php if(!empty($assignment['due_date'])): ?>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <i class="fas fa-star"></i>
                                <span>Max Score: <?= $assignment['max_score'] ?></span>
                            </div>
                        </div>

                        <?php if($assignment['has_submitted']): ?>
                            <div class="submission-status">
                                <?php if($assignment['submission']['status'] == 'graded'): ?>
                                    <div class="status-badge graded">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Graded</span>
                                        <?php if($assignment['submission']['score'] !== null): ?>
                                            <strong> • <?= $assignment['submission']['score'] ?>/<?= $assignment['max_score'] ?></strong>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="status-badge pending">
                                        <i class="fas fa-clock"></i>
                                        <span>Pending Review</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="assignment-footer">
                        <a href="<?= base_url('student/view-assignment/' . $assignment['id']) ?>" class="btn-view">
                            <i class="fas fa-eye"></i>
                            <?= $assignment['has_submitted'] ? 'View Submission' : 'View & Submit' ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .student-assignments-page {
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
    }

    .assignments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .assignment-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
        overflow: hidden;
    }

    .assignment-card:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .assignment-header {
        padding: 1.25rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .assignment-header h3 {
        color: #2c3e50;
        font-size: 1.1rem;
        margin: 0;
        font-weight: 600;
        flex: 1;
    }

    .course-badge {
        padding: 0.25rem 0.75rem;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .assignment-body {
        padding: 1.25rem;
    }

    .assignment-preview {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .assignment-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .meta-item i {
        color: #3498db;
        width: 20px;
    }

    .submission-status {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status-badge.graded {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.pending {
        background: #d1ecf1;
        color: #0c5460;
    }

    .assignment-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-view {
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

    .btn-view:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
        color: white;
    }

    @media (max-width: 768px) {
        .assignments-grid {
            grid-template-columns: 1fr;
        }

        .assignment-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
<?= $this->endSection() ?>
