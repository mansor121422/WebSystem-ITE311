<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LMS System' ?></title>
    
    <!-- Simple CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .navbar {
            background-color: #2c3e50;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav-link {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background-color: #34495e;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #34495e;
        }
        
        .btn-primary {
            background-color: #2c3e50;
        }
        
        .btn-success {
            background-color: #2c3e50;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 2rem;
            margin: 1rem 0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .navbar-nav {
                gap: 1rem;
            }
        }

        /* Simple Dashboard Styles */
        .student-dashboard-simple,
        .admin-dashboard-simple,
        .teacher-dashboard-simple {
            text-align: center;
            padding: 2rem 0;
        }

        .student-dashboard-simple h2,
        .admin-dashboard-simple h2,
        .teacher-dashboard-simple h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #2c3e50;
        }

        /* Teacher Dashboard Specific Styles */
        .teacher-content {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .welcome-message {
            text-align: left;
            margin-bottom: 2rem;
        }

        .welcome-message h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .welcome-message p {
            color: #6c757d;
            line-height: 1.6;
            margin: 0;
        }

        .logout-section {
            text-align: center;
        }

        /* Student Dashboard Styles */
        .student-dashboard .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .student-dashboard .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .student-dashboard .progress {
            background-color: #e9ecef;
        }

        .student-dashboard .progress-bar {
            background-color: #28a745;
            transition: width 0.3s ease;
        }

        .student-dashboard .badge {
            font-size: 0.75rem;
        }

        .student-dashboard .enroll-btn {
            transition: all 0.2s ease;
        }

        .student-dashboard .enroll-btn:hover {
            transform: scale(1.05);
        }

        .student-dashboard .enroll-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Ensure enroll button is clickable */
        .enroll-btn {
            cursor: pointer !important;
            pointer-events: auto !important;
        }
        
        .enroll-btn:hover {
            background-color: #1a252f !important;
            transform: translateY(-1px);
        }

        .student-dashboard h3 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .student-dashboard .fas {
            margin-right: 0.5rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .dashboard-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .dashboard-card h4 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .dashboard-card p {
            color: #6c757d;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .dashboard-card .btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                padding: 0 0.5rem;
            }
            
            .dashboard-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Dynamic Navigation Bar -->
    <?= $this->include('templates/header') ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> LMS System. All rights reserved.</p>
    </footer>

    <!-- jQuery (Load first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Enrollment JavaScript -->
    <script>
    $(document).ready(function() {
        console.log('Document ready - jQuery loaded successfully');
        console.log('Found enroll buttons:', $('.enroll-btn').length);
        
        // Clear any existing error messages on page load
        $('.alert-danger').fadeOut();
        
        // Load enrolled courses from database on page load
        loadEnrolledCourses();
        
        // Handle enrollment button clicks
        $(document).on('click', '.enroll-btn', function(e) {
            console.log('Enroll button clicked!');
            e.preventDefault();
            
            const courseId = $(this).data('course-id');
            const courseTitle = $(this).data('course-title');
            const button = $(this);
            const courseCard = button.closest('.col-md-6, .col-lg-4');
            
            console.log('Course ID:', courseId, 'Course Title:', courseTitle);
            
            // Disable button and show loading state
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
            
            // Use $.post() to send the course_id to the /course/enroll URL
            console.log('Sending AJAX request to:', '<?= base_url('course/enroll') ?>');
            console.log('Course ID:', courseId);
            
            $.post('<?= base_url('course/enroll') ?>', {
                course_id: courseId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(response) {
                console.log('Enrollment response:', response);
                if (response.success) {
                    // Display success message
                    showAlert('success', response.message);
                    
                    // Hide or disable the Enroll button for that course
                    button.removeClass('btn-primary').addClass('btn-success');
                    button.html('<i class="fas fa-check"></i> Enrolled');
                    button.prop('disabled', true);
                    
                    // Update the Enrolled Courses list dynamically without reloading the page
                    addToEnrolledCourses(courseId, courseTitle, response.enrollment_date);
                    
                    // Update stats
                    updateStats();
                    
                    // Remove the course from available courses section
                    courseCard.fadeOut(500, function() {
                        $(this).remove();
                        // Check if no more courses available
                        if ($('.student-dashboard .mb-5').last().find('.col-md-6, .col-lg-4').length === 0) {
                            $('.student-dashboard .mb-5').last().html(`
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> All available courses have been enrolled!
                                </div>
                            `);
                        }
                    });
                    
                } else {
                    // Show error message
                    showAlert('danger', response.message);
                    
                    // Reset button
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-plus"></i> Enroll Now');
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Enrollment error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response status:', xhr.status);
                // Show error message
                showAlert('danger', 'An error occurred while enrolling. Please try again.');
                
                // Reset button
                button.prop('disabled', false);
                button.html('<i class="fas fa-plus"></i> Enroll Now');
            });
        });
        
        // Function to show Bootstrap alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert at the top of the content
            $('.card').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Function to add course to enrolled courses section dynamically
        function addToEnrolledCourses(courseId, courseTitle, enrollmentDate) {
            const enrolledCoursesContainer = $('.student-dashboard .mb-5').first();
            
            // Get course details from the original course card
            const originalCard = $(`.enroll-btn[data-course-id="${courseId}"]`).closest('.col-md-6, .col-lg-4');
            const courseDescription = originalCard.find('.card-text').text();
            const courseInstructor = originalCard.find('small:contains("Instructor")').text().replace('Instructor ', '');
            const courseDuration = originalCard.find('small:contains("Duration")').text().replace('Duration ', '');
            
            const newCourseHtml = `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">${courseTitle}</h5>
                            <p class="card-text text-muted">${courseDescription}</p>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> ${courseInstructor}
                                </small>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> ${courseDuration}
                                </small>
                            </div>
                            <div class="mb-2">
                                <small class="text-success">
                                    <i class="fas fa-calendar"></i> Enrolled: ${new Date(enrollmentDate).toLocaleDateString()}
                                </small>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted">Progress: 0%</small>
                        </div>
                        <div class="card-footer">
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            `;
            
            // Check if enrolled courses section exists and has courses
            if (enrolledCoursesContainer.length && enrolledCoursesContainer.find('.row').length) {
                // Check if there's an alert message (no courses enrolled yet)
                if (enrolledCoursesContainer.find('.alert').length > 0) {
                    // Replace the alert with the course
                    enrolledCoursesContainer.find('.alert').replaceWith(`<div class="row">${newCourseHtml}</div>`);
                } else {
                    // Add to existing row
                    enrolledCoursesContainer.find('.row').append(newCourseHtml);
                }
            } else {
                // If no enrolled courses section exists, create it
                const enrolledSectionHtml = `
                    <div class="mb-5">
                        <h3 class="mb-3">
                            <i class="fas fa-book-open"></i> My Enrolled Courses
                        </h3>
                        <div class="row">
                            ${newCourseHtml}
                        </div>
                    </div>
                `;
                
                // Insert before available courses section
                $('.student-dashboard h3').first().parent().after(enrolledSectionHtml);
            }
        }
        
        // Function to update stats dynamically
        function updateStats() {
            const enrolledCount = $('.student-dashboard .mb-5').first().find('.col-md-6, .col-lg-4').length;
            const availableCount = $('.student-dashboard .mb-5').last().find('.col-md-6, .col-lg-4').length;
            
            // Update enrolled courses count
            $('.student-dashboard .row.mb-4 .col-md-4:first .card-title').text(enrolledCount);
            
            // Update available courses count
            $('.student-dashboard .row.mb-4 .col-md-4:last .card-title').text(availableCount);
        }
        
        // Function to load enrolled courses from database
        function loadEnrolledCourses() {
            $.get('<?= base_url('course/enrollments') ?>', function(response) {
                if (response.success && response.enrollments && response.enrollments.length > 0) {
                    console.log('Loaded enrolled courses:', response.enrollments);
                    
                    const enrolledContainer = $('.student-dashboard .mb-5').first();
                    const enrolledCourseIds = [];
                    
                    // Clear "no courses" message if exists
                    enrolledContainer.find('.alert').remove();
                    
                    // Create row if it doesn't exist
                    if (enrolledContainer.find('.row').length === 0) {
                        enrolledContainer.append('<div class="row"></div>');
                    }
                    
                    // Clear existing enrolled courses
                    enrolledContainer.find('.row').html('');
                    
                    // Add each enrolled course
                    response.enrollments.forEach(function(course) {
                        enrolledCourseIds.push(course.id);
                        
                        const courseHtml = `
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">${course.title}</h5>
                                        <p class="card-text text-muted">${course.description}</p>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> ${course.instructor}
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> ${course.duration}
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="fas fa-calendar"></i> Enrolled: ${new Date(course.enrollment_date).toLocaleDateString()}
                                            </small>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: ${course.progress}%" 
                                                 aria-valuenow="${course.progress}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">Progress: ${course.progress}%</small>
                                    </div>
                                    <div class="card-footer">
                                        <span class="badge bg-success">${course.status}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        enrolledContainer.find('.row').append(courseHtml);
                    });
                    
                    // Remove enrolled courses from available courses
                    enrolledCourseIds.forEach(function(courseId) {
                        $(`.enroll-btn[data-course-id="${courseId}"]`).closest('.col-md-6, .col-lg-4').remove();
                    });
                    
                    // Update stats
                    updateStats();
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Failed to load enrolled courses:', error);
            });
        }
    });
    </script>
</body>
</html>
