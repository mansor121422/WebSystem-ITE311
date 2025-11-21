<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="submissions-page">
    <div class="page-header">
        <a href="<?= base_url('teacher/assignments') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
        <div>
            <h2>
                <i class="fas fa-clipboard-list"></i> Submissions for: <?= esc($assignment['title']) ?>
            </h2>
            <p class="course-info">Course: <?= esc($assignment['course_title']) ?></p>
        </div>
    </div>

    <?php if(empty($submissions)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox fa-3x"></i>
            <h3>No submissions yet</h3>
            <p>Students haven't submitted this assignment yet.</p>
        </div>
    <?php else: ?>
        <div class="submissions-table-card">
            <table class="submissions-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($submissions as $submission): ?>
                        <tr>
                            <td><?= esc($submission['student_name']) ?></td>
                            <td><?= esc($submission['student_email']) ?></td>
                            <td>
                                <?php if($submission['submitted_at']): ?>
                                    <?= date('M d, Y H:i', strtotime($submission['submitted_at'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not submitted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'submitted';
                                if($submission['status'] == 'graded') $statusClass = 'graded';
                                ?>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= ucfirst($submission['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($submission['score'] !== null): ?>
                                    <strong><?= $submission['score'] ?></strong> / <?= $assignment['max_score'] ?>
                                <?php else: ?>
                                    <span class="text-muted">Not graded</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn-view" 
                                        onclick="viewSubmission(<?= $submission['id'] ?>)">
                                    <i class="fas fa-eye"></i> View & Grade
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Submission View Modal -->
<div id="submissionModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-large">
        <div class="modal-header">
            <h3 id="modalStudentName">View Submission</h3>
            <button type="button" class="modal-close" onclick="closeSubmissionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="submissionContent">
                <!-- Submission content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    .submissions-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    .page-header {
        margin-bottom: 1.5rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        color: #6c757d;
        text-decoration: none;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .back-link:hover {
        background: #f8f9fa;
        border-color: #3498db;
        color: #3498db;
    }

    .page-header h2 {
        color: #2c3e50;
        font-size: 1.75rem;
        margin: 0.5rem 0;
        font-weight: 600;
    }

    .page-header h2 i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .course-info {
        color: #6c757d;
        margin: 0;
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

    .submissions-table-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .submissions-table {
        width: 100%;
        border-collapse: collapse;
    }

    .submissions-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .submissions-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .submissions-table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        color: #2c3e50;
    }

    .submissions-table tbody tr:hover {
        background: #f8f9fa;
    }

    .submissions-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-badge.submitted {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.graded {
        background: #d4edda;
        color: #155724;
    }

    .text-muted {
        color: #6c757d;
    }

    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-view:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-content-large {
        background: white;
        border-radius: 8px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        color: #2c3e50;
        font-size: 1.5rem;
        margin: 0;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #2c3e50;
    }

    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }

    .submission-section {
        margin-bottom: 2rem;
    }

    .submission-section label {
        display: block;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .submission-content-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1.25rem;
        color: #2c3e50;
        line-height: 1.8;
        min-height: 100px;
        max-height: 400px;
        overflow-y: auto;
    }

    .submission-content-box.empty {
        color: #6c757d;
        font-style: italic;
    }

    .file-download-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: #e3f2fd;
        color: #1976d2;
        text-decoration: none;
        border-radius: 6px;
        border: 1px solid #bbdefb;
        transition: all 0.2s;
        margin-top: 0.5rem;
    }

    .file-download-link:hover {
        background: #bbdefb;
        color: #1565c0;
    }

    .grading-form {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        color: #2c3e50;
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-group input[type="number"],
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .btn-primary,
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-primary {
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: white;
        color: #6c757d;
        border: 1px solid #e9ecef;
    }

    .btn-secondary:hover {
        background: #f8f9fa;
        border-color: #ddd;
    }

    @media (max-width: 768px) {
        .submissions-table {
            font-size: 0.85rem;
        }

        .submissions-table th,
        .submissions-table td {
            padding: 0.75rem 0.5rem;
        }

        .modal-content-large {
            max-width: 100%;
            max-height: 100vh;
            border-radius: 0;
        }
    }
</style>

<script>
// Store submission data
const submissionsData = <?= json_encode($submissions) ?>;
const assignmentData = <?= json_encode($assignment) ?>;

function viewSubmission(submissionId) {
    const submission = submissionsData.find(s => s.id == submissionId);
    if (!submission) return;

    // Update modal title
    document.getElementById('modalStudentName').textContent = 
        `View Submission - ${submission.student_name}`;

    // Build submission content
    let content = `
        <div class="submission-section">
            <label>Student Information</label>
            <div class="submission-content-box">
                <strong>Name:</strong> ${submission.student_name}<br>
                <strong>Email:</strong> ${submission.student_email}<br>
                <strong>Submitted:</strong> ${submission.submitted_at ? new Date(submission.submitted_at).toLocaleString() : 'Not submitted'}
            </div>
        </div>

        <div class="submission-section">
            <label>Submission Text</label>
            <div class="submission-content-box ${!submission.submission_text ? 'empty' : ''}">
                ${submission.submission_text ? submission.submission_text.replace(/\n/g, '<br>') : '<em>No text submission provided</em>'}
            </div>
        </div>
    `;

    if (submission.file_name) {
        content += `
            <div class="submission-section">
                <label>Submitted File</label>
                <a href="<?= base_url('teacher/download-submission/') ?>${submission.id}" 
                   class="file-download-link" target="_blank">
                    <i class="fas fa-download"></i> ${submission.file_name}
                </a>
            </div>
        `;
    }

    // Add grading form
    content += `
        <div class="grading-form">
            <h4 style="color: #2c3e50; margin-bottom: 1rem;">Grade Submission</h4>
            <form id="gradeForm${submission.id}">
                <div class="form-group">
                    <label for="score${submission.id}">Score (out of ${assignmentData.max_score})</label>
                    <input type="number" 
                           id="score${submission.id}" 
                           name="score" 
                           min="0" 
                           max="${assignmentData.max_score}" 
                           step="0.01"
                           value="${submission.score || ''}"
                           placeholder="Enter score">
                </div>

                <div class="form-group">
                    <label for="feedback${submission.id}">Feedback</label>
                    <textarea id="feedback${submission.id}" 
                              name="feedback" 
                              rows="5"
                              placeholder="Provide feedback to the student...">${submission.feedback || ''}</textarea>
                </div>

                <input type="hidden" name="submission_id" value="${submission.id}">
                <?= csrf_field() ?>
            </form>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeSubmissionModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn-primary" onclick="gradeSubmission(${submission.id})">
                    <i class="fas fa-save"></i> Save Grade
                </button>
            </div>
        </div>
    `;

    document.getElementById('submissionContent').innerHTML = content;
    document.getElementById('submissionModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSubmissionModal() {
    document.getElementById('submissionModal').style.display = 'none';
    document.body.style.overflow = '';
}

function gradeSubmission(submissionId) {
    const form = document.getElementById('gradeForm' + submissionId);
    const formData = new FormData(form);
    
    const submitBtn = form.closest('.grading-form').querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('<?= base_url('teacher/grade-submission') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSubmissionModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Close modal when clicking outside
document.getElementById('submissionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeSubmissionModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSubmissionModal();
    }
});
</script>
<?= $this->endSection() ?>
