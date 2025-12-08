<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="pending-enrollments-page">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-clock"></i> Pending Enrollment Requests
        </h2>
        <p class="page-subtitle">Review and respond to course enrollment requests from your teachers</p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(empty($enrollments)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox fa-3x"></i>
            <h3>No Pending Requests</h3>
            <p>You don't have any pending enrollment requests at the moment.</p>
            <a href="<?= base_url('student/courses') ?>" class="btn-primary">
                <i class="fas fa-book"></i> View My Courses
            </a>
        </div>
    <?php else: ?>
        <div class="enrollments-list">
            <?php foreach($enrollments as $enrollment): ?>
                <div class="enrollment-card">
                    <div class="enrollment-header">
                        <div class="course-info">
                            <h3><?= esc($enrollment['title']) ?></h3>
                            <span class="status-badge pending">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        </div>
                    </div>
                    
                    <div class="enrollment-body">
                        <p class="course-description"><?= esc($enrollment['description']) ?></p>
                        
                        <div class="enrollment-details">
                            <div class="detail-item">
                                <i class="fas fa-user"></i>
                                <span>Instructor: <?= esc($enrollment['instructor']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>Duration: <?= esc($enrollment['duration']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>Requested: <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="enrollment-actions">
                        <button onclick="acceptEnrollment(<?= $enrollment['id'] ?>)" 
                                class="btn-accept">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button onclick="rejectEnrollment(<?= $enrollment['id'] ?>)" 
                                class="btn-reject">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function acceptEnrollment(enrollmentId) {
    if (!confirm('Are you sure you want to accept this enrollment request?')) {
        return;
    }

    fetch('<?= base_url('student/enrollments/accept/') ?>' + enrollmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Failed to accept enrollment.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function rejectEnrollment(enrollmentId) {
    if (!confirm('Are you sure you want to reject this enrollment request?')) {
        return;
    }

    fetch('<?= base_url('student/enrollments/reject/') ?>' + enrollmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Failed to reject enrollment.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>

<style>
    .pending-enrollments-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-title {
        color: #2c3e50;
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    .page-title i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
        font-size: 0.95rem;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-danger {
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
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #2c3e50;
        margin: 1rem 0 0.5rem 0;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .enrollments-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .enrollment-card {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .enrollment-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .enrollment-header {
        padding: 1.25rem;
        background: #fffbf0;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .course-info {
        flex: 1;
    }

    .course-info h3 {
        color: #2c3e50;
        font-size: 1.25rem;
        margin: 0 0 0.5rem 0;
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .enrollment-body {
        padding: 1.25rem;
    }

    .course-description {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .enrollment-details {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #495057;
        font-size: 0.9rem;
    }

    .detail-item i {
        color: #3498db;
        width: 20px;
    }

    .enrollment-actions {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn-accept,
    .btn-reject {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-accept {
        background: #28a745;
        color: white;
    }

    .btn-accept:hover {
        background: #218838;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    .btn-reject {
        background: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background: #c82333;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 768px) {
        .enrollment-actions {
            flex-direction: column;
        }

        .btn-accept,
        .btn-reject {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?= $this->endSection() ?>

