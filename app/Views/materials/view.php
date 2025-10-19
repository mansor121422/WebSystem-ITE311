<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="materials-view-page">
    <div class="page-header mb-4">
        <h2>
            <i class="fas fa-file-alt"></i> Course Materials
        </h2>
        <p class="text-muted"><?= esc($course_title) ?></p>
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

    <!-- Materials List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-folder-open"></i> Available Materials
            </h5>
            <?php if(in_array(session('role'), ['admin', 'teacher'])): ?>
                <a href="<?= base_url('materials/upload/' . $course_id) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Upload Material
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (!empty($materials)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-file"></i> File Name</th>
                                <th><i class="fas fa-calendar"></i> Upload Date</th>
                                <th class="text-center"><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            // Get file extension
                                            $extension = pathinfo($material['file_name'], PATHINFO_EXTENSION);
                                            $iconClass = 'fa-file';
                                            
                                            // Set icon based on file type
                                            switch(strtolower($extension)) {
                                                case 'pdf':
                                                    $iconClass = 'fa-file-pdf text-danger';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $iconClass = 'fa-file-word text-primary';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $iconClass = 'fa-file-excel text-success';
                                                    break;
                                                case 'ppt':
                                                case 'pptx':
                                                    $iconClass = 'fa-file-powerpoint text-warning';
                                                    break;
                                                case 'zip':
                                                    $iconClass = 'fa-file-zipper text-secondary';
                                                    break;
                                                case 'txt':
                                                    $iconClass = 'fa-file-lines text-muted';
                                                    break;
                                            }
                                            ?>
                                            <i class="fas <?= $iconClass ?> fa-2x me-3"></i>
                                            <span><?= esc($material['file_name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($material['created_at']): ?>
                                            <?= date('M d, Y - h:i A', strtotime($material['created_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Download Button -->
                                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" 
                                               class="btn btn-sm btn-success" 
                                               title="Download">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            
                                            <!-- Delete Button (Only for admin/teacher) -->
                                            <?php if(in_array(session('role'), ['admin', 'teacher'])): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-material-btn" 
                                                        data-material-id="<?= $material['id'] ?>"
                                                        data-material-name="<?= esc($material['file_name']) ?>"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No materials have been uploaded for this course yet.</p>
                    <?php if(in_array(session('role'), ['admin', 'teacher'])): ?>
                        <a href="<?= base_url('materials/upload/' . $course_id) ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Upload First Material
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-3">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<style>
    .materials-view-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header h2 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<script>
    // Delete material functionality
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-material-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const materialId = this.getAttribute('data-material-id');
                const materialName = this.getAttribute('data-material-name');
                
                if (confirm(`Are you sure you want to delete "${materialName}"? This action cannot be undone.`)) {
                    // Send AJAX request to delete
                    fetch('<?= base_url('materials/delete/') ?>' + materialId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to show updated list
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the material.');
                    });
                }
            });
        });
    });
</script>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

