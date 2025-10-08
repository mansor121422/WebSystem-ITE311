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

    <!-- Enrollment JavaScript -->
    <script>
    $(document).ready(function() {
        // Handle enrollment button clicks
        $('.enroll-btn').on('click', function() {
            const courseId = $(this).data('course-id');
            const courseTitle = $(this).data('course-title');
            const button = $(this);
            
            // Disable button and show loading state
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
            
            // Make AJAX request to enroll
            $.ajax({
                url: '<?= base_url('course/enroll') ?>',
                type: 'POST',
                data: {
                    course_id: courseId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showAlert('success', response.message);
                        
                        // Update button to show enrolled state
                        button.removeClass('btn-primary').addClass('btn-success');
                        button.html('<i class="fas fa-check"></i> Enrolled');
                        
                        // Optionally reload the page to show updated enrolled courses
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        // Show error message
                        showAlert('danger', response.message);
                        
                        // Reset button
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-plus"></i> Enroll Now');
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    showAlert('danger', 'An error occurred while enrolling. Please try again.');
                    
                    // Reset button
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-plus"></i> Enroll Now');
                }
            });
        });
        
        // Function to show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
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
    });
    </script>
</body>
</html>
