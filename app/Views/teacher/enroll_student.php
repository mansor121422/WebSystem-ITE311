<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-user-plus"></i> Enroll Student in Course
            </h2>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form id="enrollForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id" class="form-label">Select Student</label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Choose a student...</option>
                                    <?php foreach($students as $student): ?>
                                        <option value="<?= $student['id'] ?>">
                                            <?= esc($student['name']) ?> (<?= esc($student['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Select Course</label>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">Choose a course...</option>
                                    <?php foreach($courses as $course): ?>
                                        <option value="<?= $course['id'] ?>">
                                            <?= esc($course['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Enroll Student
                            </button>
                            <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>

                    <div id="enrollResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#enrollForm').on('submit', function(e) {
        e.preventDefault();
        
        const studentId = $('#student_id').val();
        const courseId = $('#course_id').val();
        
        if (!studentId || !courseId) {
            alert('Please select both student and course.');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');

        $.post('<?= base_url('teacher/enroll-student') ?>', {
            student_id: studentId,
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                $('#enrollResult').html(`
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ${response.message}
                        ${response.student ? '<br><strong>Student:</strong> ' + response.student.name + ' (' + response.student.email + ')' : ''}
                    </div>
                `);
                $('#enrollForm')[0].reset();
            } else {
                $('#enrollResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> ${response.message}
                    </div>
                `);
            }
        }, 'json').fail(function(xhr) {
            const response = xhr.responseJSON || {};
            $('#enrollResult').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ${response.message || 'An error occurred. Please try again.'}
                </div>
            `);
        }).always(function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Enroll Student');
        });
    });
});
</script>
<?= $this->endSection() ?>

