<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="quiz-form-page">
    <div class="form-header">
        <h2><i class="fas fa-question-circle"></i> Create New Quiz</h2>
        <a href="<?= base_url('teacher/quizzes') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Quizzes
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
        <form method="POST" action="<?= base_url('teacher/create-quiz') ?>" id="quizForm">
            <?= csrf_field() ?>

            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                
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
                    <label for="title">Quiz Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" 
                           value="<?= old('title') ?>" required minlength="3" maxlength="255"
                           placeholder="Enter quiz title">
                </div>

                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="3"
                              placeholder="Enter instructions for students (optional)"><?= old('instructions') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-cog"></i> Quiz Settings</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="time_limit">Time Limit (minutes)</label>
                        <input type="number" id="time_limit" name="time_limit" 
                               value="<?= old('time_limit') ?>" min="1"
                               placeholder="Leave empty for no limit">
                        <small>Leave empty for unlimited time</small>
                    </div>

                    <div class="form-group">
                        <label for="max_attempts">Max Attempts</label>
                        <input type="number" id="max_attempts" name="max_attempts" 
                               value="<?= old('max_attempts', 1) ?>" min="1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="passing_score">Passing Score (%)</label>
                        <input type="number" id="passing_score" name="passing_score" 
                               value="<?= old('passing_score', 70) ?>" min="0" max="100" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_published" value="1" <?= old('is_published') ? 'checked' : '' ?>>
                            Publish immediately
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_correct_answers" value="1" <?= old('show_correct_answers', true) ? 'checked' : '' ?>>
                            Show correct answers after submission
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="randomize_questions" value="1" <?= old('randomize_questions') ? 'checked' : '' ?>>
                            Randomize question order
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-question"></i> Questions</h3>
                    <button type="button" class="btn-add-question" onclick="addQuestion()">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                </div>
                
                <div id="questions-container">
                    <!-- Questions will be added here dynamically -->
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Create Quiz
                </button>
                <a href="<?= base_url('teacher/quizzes') ?>" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questions-container');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-item';
    questionDiv.id = `question-${questionCount}`;
    
    questionDiv.innerHTML = `
        <div class="question-header">
            <h4>Question ${questionCount}</h4>
            <button type="button" class="btn-remove-question" onclick="removeQuestion(${questionCount})">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
        
        <div class="form-group">
            <label>Question Text <span class="required">*</span></label>
            <textarea name="questions[${questionCount}][question_text]" rows="3" required
                      placeholder="Enter the question"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Question Type <span class="required">*</span></label>
                <select name="questions[${questionCount}][question_type]" class="question-type-select" onchange="toggleOptions(${questionCount})" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Points</label>
                <input type="number" name="questions[${questionCount}][points]" value="1.00" min="0" step="0.01">
            </div>
        </div>
        
        <div class="form-group options-group" id="options-${questionCount}">
            <label>Options (for Multiple Choice) <span class="required">*</span></label>
            <div class="options-container" id="options-container-${questionCount}">
                <div class="option-item">
                    <input type="text" name="questions[${questionCount}][options][]" placeholder="Option 1" required>
                    <input type="radio" name="questions[${questionCount}][correct_option]" value="0" required>
                    <label>Correct</label>
                </div>
                <div class="option-item">
                    <input type="text" name="questions[${questionCount}][options][]" placeholder="Option 2" required>
                    <input type="radio" name="questions[${questionCount}][correct_option]" value="1">
                    <label>Correct</label>
                </div>
            </div>
            <button type="button" class="btn-add-option" onclick="addOption(${questionCount})">
                <i class="fas fa-plus"></i> Add Option
            </button>
        </div>
        
        <div class="form-group correct-answer-group" id="correct-answer-${questionCount}" style="display:none;">
            <label>Correct Answer <span class="required">*</span></label>
            <div id="correct-answer-input-${questionCount}">
                <!-- Will be populated based on question type -->
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
    toggleOptions(questionCount);
}

function removeQuestion(id) {
    const questionDiv = document.getElementById(`question-${id}`);
    if (questionDiv) {
        questionDiv.remove();
        updateQuestionNumbers();
    }
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        const header = question.querySelector('.question-header h4');
        if (header) {
            header.textContent = `Question ${index + 1}`;
        }
    });
}

function toggleOptions(questionId) {
    const questionDiv = document.getElementById(`question-${questionId}`);
    const typeSelect = questionDiv.querySelector('.question-type-select');
    const questionType = typeSelect.value;
    const optionsGroup = document.getElementById(`options-${questionId}`);
    const correctAnswerGroup = document.getElementById(`correct-answer-${questionId}`);
    const correctAnswerInput = document.getElementById(`correct-answer-input-${questionId}`);
    
    if (questionType === 'multiple_choice') {
        optionsGroup.style.display = 'block';
        correctAnswerGroup.style.display = 'none';
    } else {
        optionsGroup.style.display = 'none';
        correctAnswerGroup.style.display = 'block';
        
        if (questionType === 'true_false') {
            correctAnswerInput.innerHTML = `
                <select name="questions[${questionId}][correct_answer]" required>
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>
            `;
        } else if (questionType === 'short_answer') {
            correctAnswerInput.innerHTML = `
                <input type="text" name="questions[${questionId}][correct_answer]" required
                       placeholder="Enter the correct answer">
            `;
        }
    }
}

function addOption(questionId) {
    const container = document.getElementById(`options-container-${questionId}`);
    const optionCount = container.children.length;
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-item';
    optionDiv.innerHTML = `
        <input type="text" name="questions[${questionId}][options][]" placeholder="Option ${optionCount + 1}" required>
        <input type="radio" name="questions[${questionId}][correct_option]" value="${optionCount}">
        <label>Correct</label>
        <button type="button" class="btn-remove-option" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(optionDiv);
}

// Validate form before submission
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const questionItems = document.querySelectorAll('.question-item');
    
    if (questionItems.length === 0) {
        e.preventDefault();
        alert('Please add at least one question to the quiz.');
        return false;
    }
    
    // Validate each question
    let hasErrors = false;
    questionItems.forEach((item, index) => {
        const questionText = item.querySelector('textarea[name*="[question_text]"]').value;
        const questionType = item.querySelector('.question-type-select').value;
        
        if (!questionText.trim()) {
            hasErrors = true;
            alert(`Question ${index + 1} is missing question text.`);
            return;
        }
        
        if (questionType === 'multiple_choice') {
            const options = item.querySelectorAll('input[name*="[options][]"]');
            const hasCorrectOption = item.querySelector('input[name*="[correct_option]"]:checked');
            
            if (options.length < 2) {
                hasErrors = true;
                alert(`Question ${index + 1} needs at least 2 options.`);
                return;
            }
            
            if (!hasCorrectOption) {
                hasErrors = true;
                alert(`Question ${index + 1} needs a correct answer selected.`);
                return;
            }
        } else {
            const correctAnswer = item.querySelector('input[name*="[correct_answer]"], select[name*="[correct_answer]"]');
            if (!correctAnswer || !correctAnswer.value.trim()) {
                hasErrors = true;
                alert(`Question ${index + 1} needs a correct answer.`);
                return;
            }
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        return false;
    }
});
</script>

<style>
    .quiz-form-page {
        max-width: 1000px;
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
        color: #667eea;
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
        transition: all 0.3s;
    }

    .back-link:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .form-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h3 {
        color: #2c3e50;
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .required {
        color: #dc3545;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group small {
        display: block;
        margin-top: 0.25rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .btn-add-question {
        padding: 0.5rem 1rem;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-add-question:hover {
        background: #5568d3;
    }

    .question-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .question-header h4 {
        margin: 0;
        color: #2c3e50;
    }

    .btn-remove-question {
        padding: 0.25rem 0.75rem;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .options-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .option-item input[type="text"] {
        flex: 1;
    }

    .btn-add-option {
        padding: 0.5rem 1rem;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-remove-option {
        padding: 0.25rem 0.5rem;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e9ecef;
    }

    .btn-primary {
        padding: 0.75rem 1.5rem;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: #5a6268;
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

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
<?= $this->endSection() ?>

