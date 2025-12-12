<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="enrollment-requests-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fas fa-user-plus"></i> Enrollment Requests
            </h2>
            <p class="page-subtitle">Review and manage student enrollment requests</p>
        </div>
        <a href="<?= base_url('teacher/enroll-student') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Force Enroll Student
        </a>
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

    <?php if(empty($requests)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox fa-3x"></i>
            <h3>No Pending Requests</h3>
            <p>There are no pending enrollment requests at this time.</p>
        </div>
    <?php else: ?>
        <div class="requests-table-section">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i> Pending Enrollment Requests
                </h3>
                <span class="request-count"><?= count($requests) ?> request(s)</span>
            </div>
            <div class="table-responsive">
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Course Code</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Schedule</th>
                            <th>Requested Date</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requests as $request): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($request['student_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= esc($request['student_name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($request['student_email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= esc($request['course_title'] ?? 'Course #' . $request['course_id']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= esc($request['course_code'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= esc($request['school_year'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge badge-secondary"><?= esc($request['semester'] ?? 'N/A') ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($request['schedule_day']) && !empty($request['schedule_time_start'])): ?>
                                        <small>
                                            <strong><?= esc($request['schedule_day']) ?></strong><br>
                                            <?= date('h:i A', strtotime($request['schedule_time_start'])) ?> - 
                                            <?= date('h:i A', strtotime($request['schedule_time_end'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($request['enrollment_date']) ? date('M d, Y H:i', strtotime($request['enrollment_date'])) : 'N/A' ?></td>
                                <td>
                                    <?php if(isset($request['teacher_name'])): ?>
                                        <?= esc($request['teacher_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-sm approve-btn" 
                                                data-enrollment-id="<?= $request['id'] ?>"
                                                data-student-name="<?= esc($request['student_name']) ?>"
                                                data-course-title="<?= esc($request['course_title']) ?>">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-danger btn-sm reject-btn" 
                                                data-enrollment-id="<?= $request['id'] ?>"
                                                data-student-name="<?= esc($request['student_name']) ?>"
                                                data-course-title="<?= esc($request['course_title']) ?>">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Handle approve button click
    $(document).on('click', '.approve-btn', function() {
        const $btn = $(this);
        const enrollmentId = $btn.data('enrollment-id');
        const studentName = $btn.data('student-name');
        const courseTitle = $btn.data('course-title');
        
        if (!confirm(`Are you sure you want to approve the enrollment request from ${studentName} for "${courseTitle}"?`)) {
            return;
        }
        
        // Disable button
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');
        
        // Make AJAX request
        $.post('<?= base_url('teacher/enrollment-requests/approve/') ?>' + enrollmentId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                alert(response.message || 'Enrollment approved successfully!');
                // Remove the row
                $btn.closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    // Check if table is empty
                    if ($('.requests-table tbody tr').length === 0) {
                        location.reload();
                    }
                });
            } else {
                alert(response.message || 'Failed to approve enrollment.');
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
            }
        }, 'json').fail(function(xhr) {
            const response = xhr.responseJSON || {};
            alert(response.message || 'An error occurred. Please try again.');
            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
        });
    });
    
    // Handle reject button click
    $(document).on('click', '.reject-btn', function() {
        const $btn = $(this);
        const enrollmentId = $btn.data('enrollment-id');
        const studentName = $btn.data('student-name');
        const courseTitle = $btn.data('course-title');
        
        if (!confirm(`Are you sure you want to reject the enrollment request from ${studentName} for "${courseTitle}"?`)) {
            return;
        }
        
        // Disable button
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
        
        // Make AJAX request
        $.post('<?= base_url('teacher/enrollment-requests/reject/') ?>' + enrollmentId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                alert(response.message || 'Enrollment request rejected.');
                // Remove the row
                $btn.closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    // Check if table is empty
                    if ($('.requests-table tbody tr').length === 0) {
                        location.reload();
                    }
                });
            } else {
                alert(response.message || 'Failed to reject enrollment.');
                $btn.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
            }
        }, 'json').fail(function(xhr) {
            const response = xhr.responseJSON || {};
            alert(response.message || 'An error occurred. Please try again.');
            $btn.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
        });
    });
});
</script>

<style>
    .enrollment-requests-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-header h2 {
        color: #2c3e50;
        font-size: 2rem;
        margin: 0 0 0.5rem 0;
        font-weight: 700;
    }

    .page-header h2 i {
        color: #3498db;
        margin-right: 0.75rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
        font-size: 1rem;
    }

    .requests-table-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .table-header h3 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.25rem;
    }

    .request-count {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .requests-table {
        width: 100%;
        border-collapse: collapse;
    }

    .requests-table thead {
        background: #f8f9fa;
    }

    .requests-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #dee2e6;
    }

    .requests-table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .requests-table tbody tr:hover {
        background: #f8f9fa;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #3498db;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        white-space: nowrap;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-state i {
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #495057;
        margin: 1rem 0 0.5rem 0;
    }

    .empty-state p {
        color: #6c757d;
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

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .requests-table {
            font-size: 0.9rem;
        }

        .requests-table th,
        .requests-table td {
            padding: 0.75rem 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }
</style>

<?= $this->endSection() ?>

