<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="student-quizzes-page">
    <h2 class="page-title">
        <i class="fas fa-question-circle"></i> My Quizzes
    </h2>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="flash-message success">
            <i class="fas fa-check-circle"></i>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="flash-message error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php if(empty($quizzes)): ?>
        <div class="empty-state">
            <i class="fas fa-question-circle fa-3x"></i>
            <h3>No quizzes available</h3>
            <p>You don't have any quizzes for your enrolled courses yet.</p>
        </div>
    <?php else: ?>
        <div class="quizzes-grid">
            <?php foreach($quizzes as $quiz): ?>
                <div class="quiz-card">
                    <div class="quiz-header">
                        <h3><?= esc($quiz['title']) ?></h3>
                        <span class="course-badge"><?= esc($quiz['course_title']) ?></span>
                    </div>
                    <div class="quiz-body">
                        <?php if(!empty($quiz['description'])): ?>
                            <p class="quiz-preview"><?= esc(substr($quiz['description'], 0, 100)) ?><?= strlen($quiz['description']) > 100 ? '...' : '' ?></p>
                        <?php endif; ?>
                        
                        <div class="quiz-meta">
                            <?php if(!empty($quiz['time_limit'])): ?>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Time Limit: <?= $quiz['time_limit'] ?> minutes</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <i class="fas fa-redo"></i>
                                <span>Max Attempts: <?= $quiz['max_attempts'] ?></span>
                            </div>
                            
                            <div class="meta-item">
                                <i class="fas fa-percent"></i>
                                <span>Passing Score: <?= $quiz['passing_score'] ?>%</span>
                            </div>
                            
                            <?php if($quiz['has_submitted']): ?>
                                <div class="meta-item">
                                    <i class="fas fa-check"></i>
                                    <span>Attempts: <?= $quiz['attempt_count'] ?>/<?= $quiz['max_attempts'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if($quiz['has_submitted']): ?>
                            <div class="status-badge completed">
                                <i class="fas fa-check-circle"></i>
                                <span>Completed</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="quiz-footer">
                        <?php if($quiz['can_attempt'] || !$quiz['has_submitted']): ?>
                            <a href="<?= base_url('student/take-quiz/' . $quiz['id']) ?>" class="btn-view">
                                <i class="fas fa-play"></i> Take Quiz
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('student/view-quiz/' . $quiz['id']) ?>" class="btn-view">
                                <i class="fas fa-eye"></i> View Quiz
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .student-quizzes-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    .page-title {
        color: #2c3e50;
        font-size: 1.75rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quizzes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .quiz-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .quiz-header {
        padding: 1.25rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .quiz-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .course-badge {
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
    }

    .quiz-body {
        padding: 1.25rem;
    }

    .quiz-preview {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .quiz-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #666;
    }

    .meta-item i {
        color: #667eea;
        width: 16px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge.completed {
        background: #d4edda;
        color: #155724;
    }

    .quiz-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-view {
        display: inline-block;
        padding: 10px 20px;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: background 0.3s;
        width: 100%;
        text-align: center;
    }

    .btn-view:hover {
        background: #5568d3;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .empty-state i {
        color: #ccc;
        margin-bottom: 1rem;
    }

    .flash-message {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .flash-message.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .flash-message.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
<?= $this->endSection() ?>


