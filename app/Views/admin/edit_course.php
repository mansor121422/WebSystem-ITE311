<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="edit-course-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fas fa-edit"></i> Edit Course
            </h2>
            <p class="page-subtitle">Update course information</p>
        </div>
        <a href="<?= base_url('courses') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="<?= base_url('admin/courses/update/' . $course['id']) ?>" class="course-form">
            <?= csrf_field() ?>

            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                
                <div class="form-group">
                    <label for="title" class="required">Course Title</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           value="<?= old('title', $course['title']) ?>"
                           placeholder="e.g., Introduction to Programming"
                           required>
                    <small class="form-text">Enter a descriptive title for the course (3-200 characters)</small>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="4"
                              placeholder="Enter course description..."><?= old('description', $course['description'] ?? '') ?></textarea>
                    <small class="form-text">Provide a detailed description of the course</small>
                </div>

                <div class="form-group">
                    <label for="instructor_id">Instructor</label>
                    <select id="instructor_id" name="instructor_id" class="form-control">
                        <option value="">Select Instructor</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= old('instructor_id', $course['instructor_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text">Assign a teacher to this course</small>
                </div>

                <div class="form-group">
                    <label for="max_students">Maximum Students</label>
                    <input type="number" 
                           id="max_students" 
                           name="max_students" 
                           class="form-control" 
                           value="<?= old('max_students', $course['max_students'] ?? '') ?>"
                           placeholder="e.g., 30"
                           min="1"
                           max="1000">
                    <small class="form-text">Maximum number of students allowed in this course</small>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-code"></i> Course Details</h3>
                
                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" 
                           id="course_code" 
                           name="course_code" 
                           class="form-control" 
                           value="<?= old('course_code', $course['course_code'] ?? '') ?>"
                           placeholder="e.g., CS101, ITE311"
                           maxlength="50">
                    <small class="form-text">Enter the course code (e.g., CS101)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="school_year" class="required">School Year</label>
                        <select id="school_year" name="school_year" class="form-control" required>
                            <option value="">Select School Year</option>
                            <?php
                            // Generate school years starting from 2025-2026
                            $currentYear = date('Y');
                            $startYear = 2025;
                            $endYear = $startYear + 10; // Show next 10 years
                            
                            for ($year = $startYear; $year <= $endYear; $year++) {
                                $schoolYear = $year . '-' . ($year + 1);
                                $selected = old('school_year', $course['school_year'] ?? '') == $schoolYear ? 'selected' : '';
                                echo '<option value="' . $schoolYear . '" ' . $selected . '>' . $schoolYear . '</option>';
                            }
                            ?>
                        </select>
                        <small class="form-text">Select the school year</small>
                    </div>

                    <div class="form-group">
                        <label for="semester" class="required">Semester</label>
                        <select id="semester" name="semester" class="form-control" required>
                            <option value="">Select Semester</option>
                            <option value="1st" <?= old('semester', $course['semester'] ?? '') == '1st' ? 'selected' : '' ?>>1st Semester</option>
                            <option value="2nd" <?= old('semester', $course['semester'] ?? '') == '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                            <option value="Summer" <?= old('semester', $course['semester'] ?? '') == 'Summer' ? 'selected' : '' ?>>Summer</option>
                        </select>
                        <small class="form-text">Select the semester</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-calendar-alt"></i> Schedule Information</h3>
                
                <div class="form-group">
                    <label for="schedule_day">Schedule Days</label>
                    <div class="schedule-days-checkboxes">
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $selectedDays = [];
                        if (!empty($course['schedule_day'])) {
                            // Handle both comma-separated string and array
                            if (is_string($course['schedule_day'])) {
                                $selectedDays = array_map('trim', explode(',', $course['schedule_day']));
                            } else {
                                $selectedDays = $course['schedule_day'];
                            }
                        }
                        $oldDays = old('schedule_day', $selectedDays);
                        if (is_string($oldDays)) {
                            $oldDays = array_map('trim', explode(',', $oldDays));
                        }
                        
                        foreach ($days as $day) {
                            $checked = in_array($day, $oldDays) ? 'checked' : '';
                            echo '<div class="day-checkbox-item">';
                            echo '<input type="checkbox" id="schedule_day_' . strtolower($day) . '" name="schedule_day[]" value="' . $day . '" ' . $checked . '>';
                            echo '<label for="schedule_day_' . strtolower($day) . '">' . $day . '</label>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <small class="form-text">Select one or more days for the course schedule</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="schedule_time_start">Start Time</label>
                        <input type="time" 
                               id="schedule_time_start" 
                               name="schedule_time_start" 
                               class="form-control" 
                               value="<?= old('schedule_time_start', $course['schedule_time_start'] ?? '') ?>">
                        <small class="form-text">Course start time (HH:MM format)</small>
                    </div>

                    <div class="form-group">
                        <label for="schedule_time_end">End Time</label>
                        <input type="time" 
                               id="schedule_time_end" 
                               name="schedule_time_end" 
                               class="form-control" 
                               value="<?= old('schedule_time_end', $course['schedule_time_end'] ?? '') ?>">
                        <small class="form-text">Course end time (HH:MM format)</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Course
                </button>
                <a href="<?= base_url('courses') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.edit-course-page {
    padding: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.page-header h2 {
    margin: 0;
    color: #333;
    font-size: 28px;
}

.page-header h2 i {
    margin-right: 10px;
    color: #007bff;
}

.page-subtitle {
    color: #666;
    margin: 5px 0 0 0;
    font-size: 14px;
}

.form-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 30px;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e0e0e0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    color: #333;
    font-size: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.form-section h3 i {
    margin-right: 8px;
    color: #007bff;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-control[multiple] {
    min-height: 150px;
    padding: 8px;
}

.form-control[multiple] option {
    padding: 8px;
    margin: 2px 0;
}

.form-control[multiple] option:checked {
    background: #007bff;
    color: white;
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    color: white;
}

.btn i {
    margin-right: 8px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert i {
    margin-right: 8px;
}

.schedule-days-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f8f9fa;
}

.day-checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.day-checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #007bff;
}

.day-checkbox-item label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    color: #333;
    user-select: none;
}

.day-checkbox-item:hover label {
    color: #007bff;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
</style>

<?= $this->endSection() ?>

