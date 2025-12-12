<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-upload"></i> Upload Course Material</h4>
    </div>
                <div class="card-body">

    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i>
            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

                    <!-- Direct Upload Form -->
                    <form method="POST" action="<?= base_url('materials/do_upload') ?>" enctype="multipart/form-data" id="uploadForm">
                        
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course ID</label>
                            <input type="text" class="form-control" id="course_id" name="course_id" value="<?= esc($course_id ?? '') ?>" readonly>
                </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="file" name="file" required accept=".pdf,.doc,.docx,.ppt,.pptx">
                            <div class="form-text">Allowed types: PDF, DOC, DOCX, PPT, PPTX. Max size: 10MB</div>
                </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="uploadBtn">
                        <i class="fas fa-upload"></i> Upload Material
                    </button>
                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('file');
    const uploadBtn = document.getElementById('uploadBtn');
    
    // File size validation
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('File size exceeds 10MB limit. Please select a smaller file.');
                this.value = '';
                return;
            }
            console.log('File selected:', file.name, 'Size:', file.size);
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        const courseId = document.getElementById('course_id').value;
        
        if (!file) {
            alert('Please select a file first!');
            e.preventDefault();
            return false;
        }
        
        if (!courseId) {
            alert('Course ID is missing!');
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        uploadBtn.disabled = true;
        
        console.log('Form submitting with file:', file.name, 'Course ID:', courseId);
        return true;
    });
    });
</script>

<?= $this->endSection() ?>