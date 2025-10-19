<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="material-upload-page">
    <div class="page-header mb-4">
        <h2>
            <i class="fas fa-cloud-upload-alt"></i> Upload Course Material
        </h2>
        <p class="text-muted">Upload files and resources for your course</p>
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

    <?php if(isset($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('materials/upload') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Hidden field for course ID -->
                <input type="hidden" name="course_id" value="<?= esc($course_id ?? '') ?>">
                
                <div class="form-group mb-3">
                    <label for="course_id_display" class="form-label">
                        <i class="fas fa-book"></i> Course ID
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="course_id_display" 
                        value="<?= esc($course_id ?? '') ?>"
                        readonly
                        disabled
                    >
                    <small class="text-muted">The course this material will be uploaded to</small>
                </div>

                <div class="form-group mb-4">
                    <label for="file" class="form-label">
                        <i class="fas fa-file-upload"></i> Select File *
                    </label>
                    <input 
                        type="file" 
                        class="form-control" 
                        id="file" 
                        name="file"
                        required
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.txt"
                    >
                    <small class="text-muted">
                        Allowed file types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, TXT<br>
                        Maximum file size: 10 MB
                    </small>
                </div>

                <!-- File Preview Section -->
                <div id="file-preview" class="file-preview-box mb-4" style="display: none;">
                    <h6>
                        <i class="fas fa-info-circle"></i> Selected File:
                    </h6>
                    <p class="mb-1">
                        <strong>Name:</strong> <span id="file-name"></span>
                    </p>
                    <p class="mb-0">
                        <strong>Size:</strong> <span id="file-size"></span>
                    </p>
                </div>

                <div class="form-actions" style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Material
                    </button>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn" style="background-color: #6c757d; color: white;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- File Upload Guidelines -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="fas fa-lightbulb"></i> Upload Guidelines
            </h5>
            <ul class="guidelines-list">
                <li><i class="fas fa-check text-success"></i> Ensure your file name is descriptive and relevant to the course content</li>
                <li><i class="fas fa-check text-success"></i> Verify that the file is not corrupted and can be opened properly</li>
                <li><i class="fas fa-check text-success"></i> Keep file sizes reasonable to ensure faster downloads for students</li>
                <li><i class="fas fa-check text-success"></i> Use PDF format for documents whenever possible for better compatibility</li>
                <li><i class="fas fa-check text-success"></i> Compress large files using ZIP format to reduce file size</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .material-upload-page {
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

    .file-preview-box {
        background-color: #e8f4f8;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }

    .file-preview-box h6 {
        color: #2c3e50;
        margin-bottom: 0.75rem;
    }

    .guidelines-list {
        list-style: none;
        padding-left: 0;
    }

    .guidelines-list li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .guidelines-list li:last-child {
        border-bottom: none;
    }

    .guidelines-list i {
        margin-right: 0.5rem;
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
    // File preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Display file information
                fileName.textContent = file.name;
                
                // Format file size
                const size = file.size;
                let sizeStr;
                if (size < 1024) {
                    sizeStr = size + ' bytes';
                } else if (size < 1024 * 1024) {
                    sizeStr = (size / 1024).toFixed(2) + ' KB';
                } else {
                    sizeStr = (size / (1024 * 1024)).toFixed(2) + ' MB';
                }
                fileSize.textContent = sizeStr;
                
                // Show preview box
                filePreview.style.display = 'block';
                
                // Check file size (10 MB limit)
                const maxSize = 10 * 1024 * 1024; // 10 MB in bytes
                if (size > maxSize) {
                    alert('File size exceeds the maximum limit of 10 MB. Please select a smaller file.');
                    fileInput.value = '';
                    filePreview.style.display = 'none';
                }
            } else {
                filePreview.style.display = 'none';
            }
        });
    });
</script>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

