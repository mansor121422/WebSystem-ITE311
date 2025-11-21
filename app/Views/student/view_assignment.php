<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="assignment-view-page">
    <div class="page-header">
        <a href="<?= base_url('student/assignments') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
        <div>
            <h2><?= esc($assignment['title']) ?></h2>
            <p class="course-name">Course: <?= esc($assignment['course_title']) ?></p>
        </div>
    </div>

    <div class="assignment-details-card">
        <h3>Assignment Details</h3>
        
        <?php if(!empty($assignment['description'])): ?>
            <div class="detail-section">
                <label>Instruction:</label>
                <div class="content-box">
                    <?= nl2br(esc($assignment['description'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($assignment['instructions'])): ?>
            <div class="detail-section">
                <label>Question:</label>
                <div class="content-box">
                    <?= nl2br(esc($assignment['instructions'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="detail-row">
            <div class="detail-item">
                <label>Due Date:</label>
                <p><?= !empty($assignment['due_date']) ? date('M d, Y H:i', strtotime($assignment['due_date'])) : 'No due date' ?></p>
            </div>
            <div class="detail-item">
                <label>Maximum Score:</label>
                <p><?= $assignment['max_score'] ?> points</p>
            </div>
        </div>
    </div>

    <?php if($has_submitted): ?>
        <div class="submission-card">
            <h3>Your Submission</h3>
            
            <?php if($submission['status'] == 'graded'): ?>
                <div class="status-alert success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Graded</strong>
                        <?php if($submission['score'] !== null): ?>
                            <p>Score: <strong><?= $submission['score'] ?> / <?= $assignment['max_score'] ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if(!empty($submission['feedback'])): ?>
                    <div class="detail-section">
                        <label>Feedback:</label>
                        <div class="content-box">
                            <?= nl2br(esc($submission['feedback'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="status-alert info">
                    <i class="fas fa-clock"></i>
                    <span>Submitted - Pending Review</span>
                </div>
            <?php endif; ?>

            <?php if(!empty($submission['submission_text'])): ?>
                <div class="detail-section">
                    <label>Your Submission Text:</label>
                    <div class="content-box">
                        <?= nl2br(esc($submission['submission_text'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!empty($submission['file_name'])): ?>
                <div class="detail-section">
                    <label>Submitted File:</label>
                    <a href="<?= base_url('student/download-submission/' . $submission['id']) ?>" class="file-link">
                        <i class="fas fa-download"></i> <?= esc($submission['file_name']) ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="submission-date">
                <small>Submitted on: <?= date('M d, Y H:i', strtotime($submission['submitted_at'])) ?></small>
            </div>
        </div>
    <?php else: ?>
        <div class="submission-card">
            <h3>Submit Assignment</h3>
            <form id="submitForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">

                <div class="form-group">
                    <label for="submission_text">Submission Text</label>
                    <textarea id="submission_text" name="submission_text" rows="8" 
                              placeholder="Enter your submission text here..."></textarea>
                    <small>You can provide text submission, upload a file, or both.</small>
                </div>

                <div class="form-group">
                    <label for="submission_file">Upload File (Optional)</label>
                    <input type="file" id="submission_file" name="submission_file" 
                           accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                    <small>Allowed formats: PDF, DOC, DOCX, TXT, ZIP, RAR (Max: 10MB)</small>
                </div>

                <div id="submitResult"></div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<style>
    .assignment-view-page {
        max-width: 900px;
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

    .course-name {
        color: #6c757d;
        margin: 0;
    }

    .assignment-details-card,
    .submission-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .assignment-details-card h3,
    .submission-card h3 {
        color: #2c3e50;
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }

    .detail-section {
        margin-bottom: 1.5rem;
    }

    .detail-section label {
        display: block;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .content-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        color: #2c3e50;
        line-height: 1.6;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .detail-item label {
        display: block;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .detail-item p {
        color: #6c757d;
        margin: 0;
    }

    .status-alert {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }

    .status-alert.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .status-alert.info {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    .status-alert i {
        font-size: 1.25rem;
    }

    .file-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #e3f2fd;
        color: #1976d2;
        text-decoration: none;
        border-radius: 6px;
        border: 1px solid #bbdefb;
        transition: all 0.2s;
    }

    .file-link:hover {
        background: #bbdefb;
        color: #1565c0;
    }

    .submission-date {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
        color: #6c757d;
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

    .form-group textarea,
    .form-group input[type="file"] {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.2s;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 150px;
    }

    .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group small {
        display: block;
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .form-actions {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    #submitResult {
        margin-bottom: 1rem;
    }

    .result-message {
        padding: 1rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .result-message.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .result-message.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .detail-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
$(document).ready(function() {
    $('#submitForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

        // Add CSRF token to form data
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url('student/submit-assignment') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#submitResult').html(`
                        <div class="result-message success">
                            <i class="fas fa-check-circle"></i>
                            <span>${response.message}</span>
                        </div>
                    `);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#submitResult').html(`
                        <div class="result-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>${response.message}</span>
                        </div>
                    `);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Assignment');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                $('#submitResult').html(`
                    <div class="result-message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>${response.message || 'An error occurred. Please try again.'}</span>
                    </div>
                `);
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Assignment');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
