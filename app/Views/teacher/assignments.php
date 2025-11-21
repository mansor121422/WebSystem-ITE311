<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-file-alt"></i> My Assignments
                </h2>
                <a href="<?= base_url('teacher/create-assignment') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Assignment
                </a>
            </div>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(empty($assignments)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No assignments created yet</h5>
                        <p class="text-muted">Create your first assignment to get started.</p>
                        <a href="<?= base_url('teacher/create-assignment') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Assignment
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($assignments as $assignment): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?= esc($assignment['title']) ?></h5>
                                    <span class="badge bg-primary"><?= esc($assignment['course_title']) ?></span>
                                </div>
                                <div class="card-body">
                                    <?php if(!empty($assignment['description'])): ?>
                                        <p class="card-text"><?= esc($assignment['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Created: <?= date('M d, Y', strtotime($assignment['created_at'])) ?>
                                        </small>
                                    </div>
                                    
                                    <?php if(!empty($assignment['due_date'])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-star"></i> Max Score: <?= $assignment['max_score'] ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('teacher/view-submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View Submissions
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

