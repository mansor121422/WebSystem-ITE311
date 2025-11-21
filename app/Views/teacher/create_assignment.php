<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="assignment-form-page">
    <div class="form-header">
        <h2><i class="fas fa-file-alt"></i> Create New Assignment</h2>
        <a href="<?= base_url('teacher/assignments') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Assignments
        </a>
    </div>

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

    <div class="form-container">
        <form method="POST" action="<?= base_url('teacher/create-assignment') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="course_id">Course <span class="required">*</span></label>
                <select id="course_id" name="course_id" required>
                    <option value="">Choose a course...</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>>
                            <?= esc($course['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Assignment Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" 
                       value="<?= old('title') ?>" required minlength="3" maxlength="255"
                       placeholder="Enter assignment title">
            </div>

            <div class="form-group">
                <label for="instruction">Instruction <span class="required">*</span></label>
                <textarea id="instruction" name="instruction" rows="5" required
                          placeholder="Provide instructions for students on how to complete this assignment"><?= old('instruction') ?></textarea>
            </div>

            <div class="form-group">
                <label for="question">Question <span class="required">*</span></label>
                <textarea id="question" name="question" rows="8" required
                          placeholder="Enter the assignment question(s)"><?= old('question') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="datetime-local" id="due_date" name="due_date" 
                           value="<?= old('due_date') ?>">
                </div>

                <div class="form-group">
                    <label for="max_score">Maximum Score</label>
                    <input type="number" id="max_score" name="max_score" 
                           value="<?= old('max_score', 100) ?>" min="1" step="0.01"
                           placeholder="100">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Create Assignment
                </button>
                <a href="<?= base_url('teacher/assignments') ?>" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .assignment-form-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .form-header h2 {
        color: #2c3e50;
        font-size: 1.75rem;
        margin: 0;
        font-weight: 600;
    }

    .form-header h2 i {
        color: #3498db;
        margin-right: 0.5rem;
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
        transition: all 0.2s;
    }

    .back-link:hover {
        background: #f8f9fa;
        border-color: #3498db;
        color: #3498db;
    }

    .flash-message {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .flash-message.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .flash-message.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .form-container {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .required {
        color: #e74c3c;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="datetime-local"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.2s;
        background: white;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
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
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
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

    .btn-secondary {
        background: white;
        color: #6c757d;
        border: 1px solid #e9ecef;
    }

    .btn-secondary:hover {
        background: #f8f9fa;
        border-color: #ddd;
        color: #2c3e50;
    }

    @media (max-width: 768px) {
        .assignment-form-page {
            padding: 1rem 0.5rem;
        }

        .form-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .form-container {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
<?= $this->endSection() ?>

