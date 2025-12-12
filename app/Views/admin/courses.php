<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="course-management-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fas fa-book"></i> Course Management
            </h2>
            <p class="page-subtitle">Manage all courses in the system</p>
        </div>
        <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Course
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

    <?php if(empty($courses)): ?>
        <div class="empty-state">
            <i class="fas fa-book-open fa-3x"></i>
            <h3>No Courses Found</h3>
            <p>There are no courses in the system yet. Create your first course to get started.</p>
            <a href="<?= base_url('admin/courses/add') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add First Course
            </a>
        </div>
    <?php else: ?>
        <div class="table-section">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i> All Courses
                </h3>
                <span class="course-count"><?= count($courses) ?> course(s)</span>
            </div>
            <div class="table-responsive">
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Code</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Instructor</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Schedule</th>
                            <th>Enrollments</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($courses as $course): ?>
                            <tr>
                                <td><?= esc($course['id']) ?></td>
                                <td>
                                    <?php if(!empty($course['course_code'])): ?>
                                        <span class="badge badge-info"><?= esc($course['course_code']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($course['title']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= !empty($course['description']) ? esc(substr($course['description'], 0, 50)) . '...' : 'No description' ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if(!empty($course['instructor_name'])): ?>
                                        <div>
                                            <strong><?= esc($course['instructor_name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($course['school_year'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if(!empty($course['semester'])): ?>
                                        <span class="badge badge-secondary"><?= esc($course['semester']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($course['schedule_day']) && !empty($course['schedule_time_start'])): ?>
                                        <small>
                                            <strong><?= esc($course['schedule_day']) ?></strong><br>
                                            <?= date('h:i A', strtotime($course['schedule_time_start'])) ?> - 
                                            <?= date('h:i A', strtotime($course['schedule_time_end'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-success"><?= $course['enrollment_count'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <small><?= date('M d, Y', strtotime($course['created_at'])) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.course-management-page {
    padding: 20px;
    max-width: 1400px;
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

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 40px;
}

.empty-state i {
    color: #ccc;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    color: #666;
    margin-bottom: 30px;
}

.table-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.table-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.course-count {
    color: #666;
    font-size: 14px;
}

.table-responsive {
    overflow-x: auto;
}

.courses-table {
    width: 100%;
    border-collapse: collapse;
}

.courses-table thead {
    background: #f8f9fa;
}

.courses-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
}

.courses-table td {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: middle;
}

.courses-table tbody tr:hover {
    background: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-info {
    background: #17a2b8;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.badge-success {
    background: #28a745;
    color: white;
}

.text-muted {
    color: #6c757d;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    color: white;
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
</style>
<?= $this->endSection() ?>

