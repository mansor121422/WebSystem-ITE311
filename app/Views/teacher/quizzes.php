<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-question-circle"></i> My Quizzes
                </h2>
                <a href="<?= base_url('teacher/create-quiz') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Quiz
                </a>
            </div>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(empty($quizzes)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No quizzes created yet</h5>
                        <p class="text-muted">Create your first quiz to get started.</p>
                        <a href="<?= base_url('teacher/create-quiz') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Quiz
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($quizzes as $quiz): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?= esc($quiz['title']) ?></h5>
                                    <span class="badge bg-primary"><?= esc($quiz['course_title']) ?></span>
                                </div>
                                <div class="card-body">
                                    <?php if(!empty($quiz['description'])): ?>
                                        <p class="card-text"><?= esc($quiz['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Created: <?= date('M d, Y', strtotime($quiz['created_at'])) ?>
                                        </small>
                                    </div>
                                    
                                    <?php if(!empty($quiz['time_limit'])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> Time Limit: <?= $quiz['time_limit'] ?> minutes
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-redo"></i> Max Attempts: <?= $quiz['max_attempts'] ?>
                                        </small>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-percent"></i> Passing Score: <?= $quiz['passing_score'] ?>%
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('teacher/quiz-submissions/' . $quiz['id']) ?>" class="btn btn-primary btn-sm">
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


