<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="courses-page">
    <div class="container-fluid">
        <h2 class="page-title">
            <i class="fas fa-book"></i> All Courses
        </h2>

        <!-- Step 4: Search Form with Bootstrap Styling -->
        <div class="search-section mb-4">
            <div class="card">
                <div class="card-body">
                    <form id="searchForm" method="GET" action="<?= base_url('courses/search') ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label for="searchInput" class="form-label">
                                    <i class="fas fa-search"></i> Search Courses
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg" 
                                    id="searchInput" 
                                    name="q" 
                                    placeholder="Search by course title or description..." 
                                    value="<?= esc($search_term ?? '') ?>"
                                    autocomplete="off"
                                >
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
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
        </div>
    </div>
</div>

<style>
    .courses-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
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

    // Step 5: Instant client-side filtering as user types
    $searchInput.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        // Get current course cards (including dynamically added ones)
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
    });

    // Step 5: Server-side search via AJAX on form submit
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        const searchTerm = $searchInput.val().trim();
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

<?= $this->endSection() ?>

