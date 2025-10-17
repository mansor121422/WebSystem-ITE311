<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="announcement-create-page">
    <div class="page-header mb-4">
        <h2>
            <i class="fas fa-plus-circle"></i> Create New Announcement
        </h2>
        <p class="text-muted">Share important information with all users</p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('announcements/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group mb-3">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading"></i> Title *
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="title" 
                        name="title" 
                        placeholder="Enter announcement title"
                        value="<?= old('title') ?>"
                        required
                        maxlength="255"
                    >
                    <small class="text-muted">Maximum 255 characters</small>
                </div>

                <div class="form-group mb-4">
                    <label for="content" class="form-label">
                        <i class="fas fa-align-left"></i> Content *
                    </label>
                    <textarea 
                        class="form-control" 
                        id="content" 
                        name="content" 
                        rows="8"
                        placeholder="Enter announcement content"
                        required
                    ><?= old('content') ?></textarea>
                    <small class="text-muted">Provide detailed information about the announcement</small>
                </div>

                <div class="form-actions" style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Publish Announcement
                    </button>
                    <a href="<?= base_url('announcements') ?>" class="btn" style="background-color: #6c757d; color: white;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-eye"></i> Preview
            </h5>
            <div id="preview-container" class="preview-box">
                <h4 id="preview-title" class="text-muted">Title will appear here</h4>
                <p id="preview-content" class="text-muted">Content will appear here</p>
            </div>
        </div>
    </div>
</div>

<style>
    .announcement-create-page {
        max-width: 800px;
        margin: 0 auto;
    }

    .page-header h2 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-label {
        font-weight: bold;
        color: #2c3e50;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .preview-box {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }

    #preview-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    #preview-content {
        line-height: 1.8;
        white-space: pre-wrap;
    }

    .form-actions {
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column !important;
        }
        
        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    // Live preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const previewTitle = document.getElementById('preview-title');
        const previewContent = document.getElementById('preview-content');

        function updatePreview() {
            const title = titleInput.value.trim();
            const content = contentInput.value.trim();

            previewTitle.textContent = title || 'Title will appear here';
            previewTitle.className = title ? 'text-dark' : 'text-muted';

            previewContent.textContent = content || 'Content will appear here';
            previewContent.className = content ? 'text-dark' : 'text-muted';
        }

        titleInput.addEventListener('input', updatePreview);
        contentInput.addEventListener('input', updatePreview);
    });
</script>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

