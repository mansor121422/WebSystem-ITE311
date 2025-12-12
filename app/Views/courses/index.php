<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="courses-page">
    <div class="container-fluid">
        <div class="page-header-section">
            <h2 class="page-title">
                <i class="fas fa-book"></i> All Courses
            </h2>
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

        <!-- Step 4: Search Form with Bootstrap Styling -->
        <div class="search-section mb-4">
            <div class="card">
                <div class="card-body">
                    <form id="searchForm" method="GET" action="<?= base_url('courses/search') ?>">
                        <div class="row g-3 align-items-end">
                            <div class="<?= isset($isAdmin) && $isAdmin ? 'col-md-10' : 'col-md-12' ?>">
                                <label for="searchInput" class="form-label">
                                    <i class="fas fa-search"></i> Search Courses
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg" 
                                    id="searchInput" 
                                    name="q" 
                                    placeholder="Search by course title, description, or code..." 
                                    value="<?= esc($search_term ?? '') ?>"
                                    autocomplete="off"
                                >
                            </div>
                            <?php if(isset($isAdmin) && $isAdmin): ?>
                            <div class="col-md-2">
                                <label class="form-label d-block">&nbsp;</label>
                                <a href="#" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                    <i class="fas fa-plus"></i> Add Course
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results Message -->
        <div id="searchMessage" class="alert alert-info" style="display: none;">
            <i class="fas fa-info-circle"></i> <span id="searchMessageText"></span>
        </div>

        <!-- Step 6: Courses Listing Structure -->
        <div id="coursesContainer">
            <?php if(empty($courses)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open fa-3x"></i>
                    <h3>No Courses Found</h3>
                    <p id="emptyMessage">
                        <?php if(!empty($search_term)): ?>
                            No courses match your search criteria. Try a different search term.
                        <?php else: ?>
                            No courses are currently available.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php if(isset($isAdmin) && $isAdmin): ?>
                    <!-- Admin Table View -->
                    <div class="table-section">
                        <div class="table-header">
                            <h3><i class="fas fa-list"></i> All Courses</h3>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="coursesTableBody">
                                    <?php foreach($courses as $course): ?>
                                        <tr data-course-title="<?= strtolower(esc($course['title'])) ?>" 
                                            data-course-description="<?= strtolower(esc($course['description'] ?? '')) ?>"
                                            data-course-code="<?= strtolower(esc($course['course_code'] ?? '')) ?>">
                                            <td><?= esc($course['id']) ?></td>
                                            <td>
                                                <?php if(!empty($course['course_code'])): ?>
                                                    <span class="badge badge-info"><?= esc($course['course_code']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= esc($course['title']) ?></strong></td>
                                            <td>
                                                <small class="text-muted" title="<?= esc($course['description'] ?? 'No description') ?>">
                                                    <?= !empty($course['description']) ? esc(substr($course['description'], 0, 40)) . '...' : 'No description' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if(!empty($course['instructor']) && $course['instructor'] !== 'Unassigned'): ?>
                                                    <div>
                                                        <strong><?= esc($course['instructor']) ?></strong>
                                                        <?php if(!empty($course['instructor_email'])): ?>
                                                            <br><small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                                        <?php endif; ?>
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
                                                        <?php 
                                                        // Handle multiple days (comma-separated)
                                                        $days = explode(',', $course['schedule_day']);
                                                        $days = array_map('trim', $days);
                                                        echo '<strong>' . esc(implode(', ', $days)) . '</strong><br>';
                                                        ?>
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
                                                <small><?= !empty($course['created_at']) ? date('M d, Y', strtotime($course['created_at'])) : 'N/A' ?></small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" 
                                                       class="btn btn-sm btn-warning edit-course-btn" 
                                                       data-course-id="<?= $course['id'] ?>"
                                                       title="Edit Course">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-course-btn" 
                                                            data-course-id="<?= $course['id'] ?>"
                                                            data-course-title="<?= esc($course['title']) ?>"
                                                            title="Delete Course">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Regular Card View -->
                    <div class="courses-grid" id="coursesGrid">
                        <?php foreach($courses as $course): ?>
                            <div class="course-card" data-course-title="<?= strtolower(esc($course['title'])) ?>" data-course-description="<?= strtolower(esc($course['description'])) ?>">
                                <div class="course-header">
                                    <h3><?= esc($course['title']) ?></h3>
                                </div>
                                <div class="course-body">
                                    <p class="course-description"><?= esc($course['description']) ?></p>
                                    <div class="course-info">
                                        <div class="info-item">
                                            <i class="fas fa-user"></i>
                                            <span><?= esc($course['instructor']) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?= esc($course['duration']) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="course-footer">
                                    <?php if(session('role') === 'student'): ?>
                                        <button class="btn btn-primary enroll-btn w-100" 
                                                data-course-id="<?= $course['id'] ?>" 
                                                data-course-title="<?= esc($course['title']) ?>">
                                            <i class="fas fa-plus"></i> Enroll Now
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('materials/view/' . $course['id']) ?>" class="btn btn-outline-primary <?= session('role') === 'student' ? 'w-100' : '' ?>">
                                        <i class="fas fa-file-alt"></i> Materials
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.courses-page {
    max-width: 100%;
    margin: 0 auto;
    padding: 1.5rem 1rem;
    overflow-x: hidden;
}

    .page-title {
        color: #2c3e50;
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-title i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .search-section {
        margin-bottom: 2rem;
    }

    .search-section .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .search-section .form-label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .search-section .form-label i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }

    .empty-state i {
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .course-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
        overflow: hidden;
        display: block;
    }

    .course-card.hidden {
        display: none;
    }

    .course-card:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .course-header {
        padding: 1.25rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .course-header h3 {
        color: #2c3e50;
        font-size: 1.1rem;
        margin: 0;
        font-weight: 600;
    }

    .course-body {
        padding: 1.25rem;
    }

    .course-description {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .course-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .info-item i {
        color: #3498db;
        width: 20px;
    }

    .course-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .course-footer .btn {
        flex: 1;
        min-width: 120px;
    }

    .course-footer .btn.w-100 {
        flex: 1 1 100%;
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Step 5: jQuery Script for Client-Side Filtering -->
<script>
$(document).ready(function() {
    const $searchInput = $('#searchInput');
    const $coursesGrid = $('#coursesGrid');
    const $courseCards = $('.course-card');
    const $searchMessage = $('#searchMessage');
    const $searchMessageText = $('#searchMessageText');
    const $emptyState = $('.empty-state');
    const $coursesContainer = $('#coursesContainer');

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Step 5: Instant client-side filtering as user types (responsive search)
    $searchInput.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        <?php if(isset($isAdmin) && $isAdmin): ?>
        // Admin: Filter table rows
        const $tableRows = $('#coursesTableBody tr');
        const $tableSection = $('.table-section');
        
        if (searchTerm === '') {
            // Show all courses if search is empty
            $tableRows.show();
            $searchMessage.hide();
            $('.course-count').text($tableRows.length + ' course(s)');
        } else {
            // Filter table rows client-side
            let visibleCount = 0;
            
            $tableRows.each(function() {
                const $row = $(this);
                const title = $row.data('course-title') || '';
                const description = $row.data('course-description') || '';
                const courseCode = $row.data('course-code') || '';
                
                if (title.includes(searchTerm) || description.includes(searchTerm) || courseCode.includes(searchTerm)) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });
            
            // Update course count
            $('.course-count').text(visibleCount + ' course(s)');
            
            // Show/hide message
            if (visibleCount === 0) {
                $searchMessage.removeClass('alert-info').addClass('alert-warning');
                $searchMessageText.html('<i class="fas fa-exclamation-triangle"></i> No courses match your search: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                $searchMessage.show();
            } else {
                $searchMessage.removeClass('alert-warning').addClass('alert-info');
                $searchMessageText.html('<i class="fas fa-check-circle"></i> Found <strong>' + visibleCount + '</strong> course(s) matching: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                $searchMessage.show();
            }
        }
        <?php else: ?>
        // Regular users: Filter course cards
        const $currentCourseCards = $('#coursesGrid .course-card');
        
        if (searchTerm === '') {
            // Show all courses if search is empty
            $currentCourseCards.removeClass('hidden');
            $searchMessage.hide();
            
            // Hide empty state if courses exist
            if ($currentCourseCards.length > 0) {
                $emptyState.hide();
            }
        } else {
            // Filter courses client-side
            let visibleCount = 0;
            
            $currentCourseCards.each(function() {
                const $card = $(this);
                const title = $card.data('course-title') || '';
                const description = $card.data('course-description') || '';
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    $card.removeClass('hidden');
                    visibleCount++;
                } else {
                    $card.addClass('hidden');
                }
            });
            
            // Show/hide empty state and message
            if (visibleCount === 0) {
                $searchMessage.removeClass('alert-info').addClass('alert-warning');
                $searchMessageText.html('<i class="fas fa-exclamation-triangle"></i> No courses match your search: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                $searchMessage.show();
                if ($emptyState.length === 0) {
                    // Create empty state if it doesn't exist
                    $coursesContainer.append(`
                        <div class="empty-state">
                            <i class="fas fa-book-open fa-3x"></i>
                            <h3>No Courses Found</h3>
                            <p>No courses match your search criteria. Try a different search term.</p>
                        </div>
                    `);
                } else {
                    $emptyState.show();
                }
            } else {
                $searchMessage.removeClass('alert-warning').addClass('alert-info');
                $searchMessageText.html('<i class="fas fa-check-circle"></i> Found <strong>' + visibleCount + '</strong> course(s) matching: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                $searchMessage.show();
                $emptyState.hide();
            }
        }
        <?php endif; ?>
    });

    // Step 5: Server-side search - optional fallback (real-time filtering is primary)
    // Real-time filtering is handled by the input handler above
    $('#searchForm').on('submit', function(e) {
        // Prevent default - real-time filtering handles everything
        e.preventDefault();
        
        // Just ensure the input event is triggered (filtering already happens on input)
        $searchInput.trigger('input');
        
        <?php if(isset($isAdmin) && $isAdmin): ?>
        // Admin: Real-time filtering is already active, no need for form submission
        return false;
        <?php else: ?>
        // For non-admin users, use AJAX for smoother experience
        e.preventDefault();
        
        const url = $(this).attr('action') + '?q=' + encodeURIComponent(searchTerm);
        
        // Show loading state
        $coursesContainer.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Searching...</p></div>');
        
        // Make AJAX request
        $.get(url, function(response) {
            if (response.success) {
                // Update URL without page reload
                window.history.pushState({}, '', url);
                
                // Render courses
                if (response.courses && response.courses.length > 0) {
                    let coursesHtml = '<div class="courses-grid" id="coursesGrid">';
                    
                    response.courses.forEach(function(course) {
                        coursesHtml += `
                            <div class="course-card" data-course-title="${course.title.toLowerCase()}" data-course-description="${course.description.toLowerCase()}">
                                <div class="course-header">
                                    <h3>${escapeHtml(course.title)}</h3>
                                </div>
                                <div class="course-body">
                                    <p class="course-description">${escapeHtml(course.description)}</p>
                                    <div class="course-info">
                                        <div class="info-item">
                                            <i class="fas fa-user"></i>
                                            <span>${escapeHtml(course.instructor)}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span>${escapeHtml(course.duration)}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="course-footer">
                                    ${course.enrollButton || ''}
                                    <a href="<?= base_url('materials/view/') ?>${course.id}" class="btn btn-outline-primary">
                                        <i class="fas fa-file-alt"></i> Materials
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    
                    coursesHtml += '</div>';
                    
                    $coursesContainer.html(coursesHtml);
                    
                    // Show success message
                    $searchMessage.removeClass('alert-warning').addClass('alert-info');
                    $searchMessageText.html('<i class="fas fa-check-circle"></i> Found <strong>' + response.count + '</strong> course(s) matching: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                    $searchMessage.show();
                    
                    // Re-initialize client-side filtering
                    initializeClientSideFiltering();
                } else {
                    // No results
                    $coursesContainer.html(`
                        <div class="empty-state">
                            <i class="fas fa-book-open fa-3x"></i>
                            <h3>No Courses Found</h3>
                            <p>No courses match your search criteria. Try a different search term.</p>
                        </div>
                    `);
                    
                    $searchMessage.removeClass('alert-info').addClass('alert-warning');
                    $searchMessageText.html('<i class="fas fa-exclamation-triangle"></i> No courses found matching: "<strong>' + escapeHtml(searchTerm) + '</strong>"');
                    $searchMessage.show();
                }
            } else {
                // Error
                $coursesContainer.html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${response.message || 'An error occurred while searching.'}
                    </div>
                `);
            }
        }, 'json').fail(function(xhr, status, error) {
            $coursesContainer.html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Failed to search courses. Please try again.
                </div>
            `);
        });
        <?php endif; ?>
    });

    // Re-initialize client-side filtering after AJAX updates
    function initializeClientSideFiltering() {
        // The input handler is already bound to $searchInput, so it will work automatically
        // Just trigger it once to apply current filter to newly loaded courses
        const currentSearchTerm = $searchInput.val().toLowerCase().trim();
        if (currentSearchTerm !== '') {
            // Small delay to ensure DOM is updated
            setTimeout(function() {
                $searchInput.trigger('input');
            }, 100);
        }
    }

    // Handle enrollment button clicks (if exists)
    $(document).on('click', '.enroll-btn', function() {
        const $btn = $(this);
        const courseId = $btn.data('course-id');
        const courseTitle = $btn.data('course-title');
        
        // Disable button to prevent double-clicking
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
        
        // Make enrollment request
        $.post('<?= base_url('course/enroll') ?>', {
            course_id: courseId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                // Show success message
                alert(response.message || 'Successfully enrolled in ' + courseTitle);
                
                // Remove the course card or update button
                $btn.closest('.course-card').fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                // Show error message
                alert(response.message || 'Failed to enroll in course');
                $btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Enroll Now');
            }
        }, 'json').fail(function(xhr) {
            const response = xhr.responseJSON || {};
            alert(response.message || 'An error occurred. Please try again.');
            $btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Enroll Now');
        });
    });
    
});
</script>

<?php if(isset($isAdmin) && $isAdmin): ?>
<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('admin/courses/create') ?>" id="addCourseForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-section">
                        <h6><i class="fas fa-info-circle"></i> Basic Information</h6>
                        
                        <div class="mb-3">
                            <label for="modal_title" class="form-label required">Course Title</label>
                            <input type="text" id="modal_title" name="title" class="form-control" 
                                   value="<?= old('title') ?>" placeholder="e.g., Introduction to Programming" required>
                            <small class="form-text text-muted">Enter a descriptive title for the course (3-200 characters)</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_description" class="form-label">Description</label>
                            <textarea id="modal_description" name="description" class="form-control" rows="3"
                                      placeholder="Enter course description..."><?= old('description') ?></textarea>
                            <small class="form-text text-muted">Provide a detailed description of the course</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_instructor_id" class="form-label">Instructor</label>
                            <select id="modal_instructor_id" name="instructor_id" class="form-control">
                                <option value="">Select Instructor (Optional)</option>
                                <?php foreach($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" <?= old('instructor_id') == $teacher['id'] ? 'selected' : '' ?>>
                                        <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Assign a teacher to this course (can be assigned later)</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_max_students" class="form-label">Maximum Students</label>
                            <input type="number" id="modal_max_students" name="max_students" class="form-control" 
                                   value="<?= old('max_students') ?>" placeholder="e.g., 30" min="1" max="1000">
                            <small class="form-text text-muted">Maximum number of students allowed in this course</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6><i class="fas fa-code"></i> Course Details</h6>
                        
                        <div class="mb-3">
                            <label for="modal_course_code" class="form-label">Course Code</label>
                            <input type="text" id="modal_course_code" name="course_code" class="form-control" 
                                   value="<?= old('course_code') ?>" placeholder="e.g., CS101, ITE311" maxlength="50">
                            <small class="form-text text-muted">Enter the course code (e.g., CS101)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_school_year" class="form-label required">School Year</label>
                                <select id="modal_school_year" name="school_year" class="form-control" required>
                                    <option value="">Select School Year</option>
                                    <?php
                                    // Generate school years starting from 2025-2026
                                    $startYear = 2025;
                                    $endYear = $startYear + 10; // Show next 10 years
                                    
                                    for ($year = $startYear; $year <= $endYear; $year++) {
                                        $schoolYear = $year . '-' . ($year + 1);
                                        $selected = old('school_year') == $schoolYear ? 'selected' : '';
                                        echo '<option value="' . $schoolYear . '" ' . $selected . '>' . $schoolYear . '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="form-text text-muted">Select the school year</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modal_semester" class="form-label required">Semester</label>
                                <select id="modal_semester" name="semester" class="form-control" required>
                                    <option value="">Select Semester</option>
                                    <option value="1st" <?= old('semester') == '1st' ? 'selected' : '' ?>>1st Semester</option>
                                    <option value="2nd" <?= old('semester') == '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                                    <option value="Summer" <?= old('semester') == 'Summer' ? 'selected' : '' ?>>Summer</option>
                                </select>
                                <small class="form-text text-muted">Select the semester</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6><i class="fas fa-calendar-alt"></i> Schedule Information</h6>
                        
                        <div class="mb-3">
                            <label for="modal_schedule_day" class="form-label">Schedule Days</label>
                            <div class="schedule-days-checkboxes">
                                <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $oldDays = old('schedule_day', []);
                                if (is_string($oldDays)) {
                                    $oldDays = array_map('trim', explode(',', $oldDays));
                                }
                                
                                foreach ($days as $day) {
                                    $checked = in_array($day, $oldDays) ? 'checked' : '';
                                    echo '<div class="day-checkbox-item">';
                                    echo '<input type="checkbox" id="modal_schedule_day_' . strtolower($day) . '" name="schedule_day[]" value="' . $day . '" ' . $checked . '>';
                                    echo '<label for="modal_schedule_day_' . strtolower($day) . '">' . $day . '</label>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <small class="form-text text-muted">Select one or more days for the course schedule</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_schedule_time_start" class="form-label">Start Time</label>
                                <input type="time" id="modal_schedule_time_start" name="schedule_time_start" 
                                       class="form-control" value="<?= old('schedule_time_start') ?>">
                                <small class="form-text text-muted">Course start time (HH:MM format)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modal_schedule_time_end" class="form-label">End Time</label>
                                <input type="time" id="modal_schedule_time_end" name="schedule_time_end" 
                                       class="form-control" value="<?= old('schedule_time_end') ?>">
                                <small class="form-text text-muted">Course end time (HH:MM format)</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.page-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h6 {
    color: #333;
    font-size: 16px;
    margin-bottom: 15px;
    font-weight: 600;
}

.form-section h6 i {
    margin-right: 8px;
    color: #007bff;
}

.required::after {
    content: " *";
    color: #dc3545;
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

.table-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 20px;
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
    overflow-x: visible;
    width: 100%;
}

.courses-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
}

.courses-table thead {
    background: #f8f9fa;
}

.courses-table th {
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
    white-space: nowrap;
}

.courses-table th:nth-child(1) { width: 5%; } /* ID */
.courses-table th:nth-child(2) { width: 8%; } /* Course Code */
.courses-table th:nth-child(3) { width: 15%; } /* Title */
.courses-table th:nth-child(4) { width: 20%; } /* Description */
.courses-table th:nth-child(5) { width: 12%; } /* Instructor */
.courses-table th:nth-child(6) { width: 8%; } /* School Year */
.courses-table th:nth-child(7) { width: 8%; } /* Semester */
.courses-table th:nth-child(8) { width: 10%; } /* Schedule */
.courses-table th:nth-child(9) { width: 7%; } /* Enrollments */
.courses-table th:nth-child(10) { width: 7%; } /* Created */
.courses-table th:nth-child(11) { width: 10%; } /* Actions */

.courses-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: middle;
    word-wrap: break-word;
}

.courses-table td:nth-child(4) { /* Description */
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
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

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-sm i {
    font-size: 12px;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
    border: none;
}

.btn-warning:hover {
    background: #e0a800;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
}

.btn-danger:hover {
    background: #c82333;
    color: white;
}
</style>


<script>
$(document).ready(function() {

    // Handle Delete button click
    $(document).on('click', '.delete-course-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const courseId = $btn.data('course-id');
        const courseTitle = $btn.data('course-title');
        
        if (!confirm('Are you sure you want to delete the course "' + courseTitle + '"?\n\nThis action cannot be undone. Courses with enrollments cannot be deleted.')) {
            return;
        }
        
        // Disable button to prevent double-clicking
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
        
        // Submit delete request via AJAX
        $.ajax({
            url: '<?= base_url('admin/courses/delete/') ?>' + courseId,
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                // Reload page to show updated list
                window.location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                alert(response.message || 'Failed to delete course. Please try again.');
                $btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
            }
        });
    });
});
</script>
<?php endif; ?>

<?= $this->endSection() ?>

