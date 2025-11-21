<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Handle course enrollment via AJAX
     */
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.',
                'error_code' => 'UNAUTHORIZED'
            ])->setStatusCode(401);
        }

        // Check if user is a student (only students can enroll)
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can enroll in courses.',
                'error_code' => 'FORBIDDEN'
            ])->setStatusCode(403);
        }

        // Get course_id from POST request
        $courseId = $this->request->getPost('course_id');
        
        // SECURITY: Use session user ID only - never trust client-supplied user ID
        $userId = session('userID');
        
        // Validate user ID from session
        if (empty($userId) || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User session invalid. Please logout and login again.',
                'error_code' => 'INVALID_SESSION'
            ])->setStatusCode(401);
        }
        
        // Validate course_id (INPUT VALIDATION)
        if (empty($courseId) || !is_numeric($courseId) || $courseId < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID provided.',
                'error_code' => 'INVALID_INPUT'
            ])->setStatusCode(400);
        }
        
        // Convert to integer to prevent SQL injection
        $courseId = (int) $courseId;
        $userId = (int) $userId;
        
        // Verify user exists in database
        $db = \Config\Database::connect();
        $userExists = $db->table('users')->where('id', $userId)->countAllResults();
        if ($userExists == 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found. Please logout and login again.'
            ]);
        }

        // Define available courses (matching the hardcoded ones in Auth controller)
        $availableCourses = [
            1 => ['id' => 1, 'title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python'],
            2 => ['id' => 2, 'title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals'],
            3 => ['id' => 3, 'title' => 'Database Management', 'description' => 'SQL and database design principles'],
            4 => ['id' => 4, 'title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving']
        ];
        
        // INPUT VALIDATION: Check if course exists
        if (!isset($availableCourses[$courseId])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.',
                'error_code' => 'COURSE_NOT_FOUND'
            ])->setStatusCode(404);
        }
        
        $course = $availableCourses[$courseId];

        // Check if user is already enrolled using direct database query
        $db = \Config\Database::connect();
        $enrollmentQuery = $db->query("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?", [$userId, $courseId]);
        if ($enrollmentQuery->getNumRows() > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Log the user_id being used
        log_message('info', "Attempting to enroll: User ID = {$userId}, Course ID = {$courseId}");
        
        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        // Insert enrollment record using direct database query
        try {
            log_message('info', "About to insert: user_id={$userId}, course_id={$courseId}");
            
            // First verify that the course exists in the courses table
            $courseExistsQuery = $db->query("SELECT id FROM courses WHERE id = ?", [$courseId]);
            if ($courseExistsQuery->getNumRows() == 0) {
                // If course doesn't exist in database, create it temporarily
                log_message('warning', "Course {$courseId} not found in database, creating it...");
                
                $courseInsertQuery = $db->query("
                    INSERT IGNORE INTO courses (id, title, description, created_at, updated_at) 
                    VALUES (?, ?, ?, NOW(), NOW())
                ", [$courseId, $course['title'], $course['description']]);
                
                if ($db->affectedRows() > 0) {
                    log_message('info', "Course {$courseId} created successfully");
                } else {
                    log_message('info', "Course {$courseId} already exists or insert failed");
                }
            }
            
            $insertQuery = $db->query("
                INSERT INTO enrollments (user_id, course_id, enrollment_date, status, progress, created_at, updated_at) 
                VALUES (?, ?, ?, 'active', 0.00, NOW(), NOW())
            ", [$userId, $courseId, $enrollmentData['enrollment_date']]);
            
            if ($db->affectedRows() > 0) {
                log_message('info', "Enrollment successful!");
                
                // Step 7: Create notification for successful enrollment
                try {
                    $notificationMessage = "You have been successfully enrolled in '{$course['title']}'. Welcome to the course!";
                    $notificationCreated = $this->notificationModel->createNotification($userId, $notificationMessage);
                    
                    if ($notificationCreated) {
                        log_message('info', "Enrollment notification created for user {$userId}");
                    } else {
                        log_message('warning', "Failed to create enrollment notification for user {$userId}");
                    }
                } catch (\Exception $e) {
                    // Don't fail the enrollment if notification creation fails
                    log_message('error', "Notification creation failed: " . $e->getMessage());
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Successfully enrolled in ' . $course['title'] . '!',
                    'course' => [
                        'id' => $course['id'],
                        'title' => $course['title'],
                        'description' => $course['description']
                    ],
                    'csrf_token' => csrf_hash()
                ]);
            } else {
                log_message('error', "Insert failed - no rows affected");
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to enroll in course. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', "Enrollment exception: " . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while enrolling: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all available courses for enrollment
     */
    public function getAvailableCourses()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view courses.'
            ]);
        }

        $userId = session('userID');
        $courses = $this->courseModel->getCoursesNotEnrolledByUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * Display enrollment fix test page
     * 
     * @return mixed
     */
    public function enrollmentFixTest()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view the enrollment fix test page.');
        }

        return view('enrollment_fix_test');
    }

    /**
     * Get user's enrolled courses
     */
    public function getUserEnrollments()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view enrollments.'
            ]);
        }

        $userId = session('userID');
        
        // Hardcoded course data
        $courseData = [
            1 => ['title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python', 'duration' => 480],
            2 => ['title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals', 'duration' => 360],
            3 => ['title' => 'Database Management', 'description' => 'SQL and database design principles', 'duration' => 600],
            4 => ['title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving', 'duration' => 720]
        ];
        
        // Get enrolled courses from database
        $db = \Config\Database::connect();
        $enrolledQuery = $db->query("
            SELECT course_id, enrollment_date, status, progress
            FROM enrollments
            WHERE user_id = ?
            ORDER BY enrollment_date DESC
        ", [$userId]);
        
        $enrollments = $enrolledQuery->getResultArray();
        
        // Format enrolled courses data
        $enrolledCourses = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (isset($courseData[$courseId])) {
                $course = $courseData[$courseId];
                $enrolledCourses[] = [
                    'id' => $courseId,
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'instructor' => 'Teacher User',
                    'duration' => $course['duration'] . ' minutes',
                    'enrollment_date' => $enrollment['enrollment_date'],
                    'status' => $enrollment['status'],
                    'progress' => $enrollment['progress']
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'enrollments' => $enrolledCourses
        ]);
    }

    /**
     * Display course details (for future use)
     */
    public function view($courseId)
    {
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to view courses.');
            return redirect()->to('/login');
        }

        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('/dashboard');
        }

        $data = [
            'course' => $course,
            'title' => $course['title']
        ];

        return view('course/view', $data);
    }

    /**
     * Search courses
     * Step 2: Search controller method that accepts GET or POST requests
     * Uses CodeIgniter's Query Builder with LIKE queries
     * Returns JSON for AJAX requests or renders view for regular requests
     */
    public function search()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You must be logged in to search courses.'
                ])->setStatusCode(401);
            }
            session()->setFlashdata('error', 'You must be logged in to search courses.');
            return redirect()->to('/login');
        }

        // Get search term from GET or POST
        $searchTerm = $this->request->getGet('q') ?? $this->request->getPost('q') ?? '';
        $searchTerm = trim($searchTerm);

        // Use CodeIgniter's Query Builder to search courses table with LIKE queries
        $db = \Config\Database::connect();
        $builder = $db->table('courses');

        if (!empty($searchTerm)) {
            // Search in title and description using LIKE
            $builder->groupStart()
                    ->like('title', $searchTerm)
                    ->orLike('description', $searchTerm)
                    ->groupEnd();
        }

        // Get all courses (or filtered by search term)
        $courses = $builder->orderBy('title', 'ASC')->get()->getResultArray();

        // Format courses data (matching the hardcoded structure)
        $courseData = [
            1 => ['title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python', 'duration' => 480],
            2 => ['title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals', 'duration' => 360],
            3 => ['title' => 'Database Management', 'description' => 'SQL and database design principles', 'duration' => 600],
            4 => ['title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving', 'duration' => 720]
        ];

        // Format results
        $formattedCourses = [];
        foreach ($courses as $course) {
            $courseId = $course['id'];
            // Use database data if available, otherwise use hardcoded data
            if (isset($courseData[$courseId])) {
                $formattedCourses[] = [
                    'id' => $courseId,
                    'title' => $course['title'] ?? $courseData[$courseId]['title'],
                    'description' => $course['description'] ?? $courseData[$courseId]['description'],
                    'instructor' => 'Teacher User',
                    'duration' => ($courseData[$courseId]['duration'] ?? 0) . ' minutes'
                ];
            } else {
                // If course exists in DB but not in hardcoded data, use DB data
                $formattedCourses[] = [
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'description' => $course['description'] ?? '',
                    'instructor' => 'Teacher User',
                    'duration' => '0 minutes'
                ];
            }
        }

        // If no courses in database, use hardcoded courses and filter them
        if (empty($courses)) {
            foreach ($courseData as $id => $course) {
                if (empty($searchTerm) || 
                    stripos($course['title'], $searchTerm) !== false || 
                    stripos($course['description'], $searchTerm) !== false) {
                    $formattedCourses[] = [
                        'id' => $id,
                        'title' => $course['title'],
                        'description' => $course['description'],
                        'instructor' => 'Teacher User',
                        'duration' => $course['duration'] . ' minutes'
                    ];
                }
            }
        }

        // Add enrollment button HTML for students (for AJAX responses)
        $userRole = strtolower(session('role') ?? '');
        if ($userRole === 'student' && $this->request->isAJAX()) {
            foreach ($formattedCourses as &$course) {
                $course['enrollButton'] = '<button class="btn btn-primary enroll-btn" ' .
                    'data-course-id="' . $course['id'] . '" ' .
                    'data-course-title="' . esc($course['title']) . '">' .
                    '<i class="fas fa-plus"></i> Enroll Now</button>';
            }
        }

        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'courses' => $formattedCourses,
                'count' => count($formattedCourses),
                'search_term' => $searchTerm
            ]);
        }

        // Render view for regular requests
        $data = [
            'title' => 'Search Courses',
            'courses' => $formattedCourses,
            'search_term' => $searchTerm,
            'page' => 'courses'
        ];

        return view('courses/index', $data);
    }

    /**
     * Display all courses with search interface
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to view courses.');
            return redirect()->to('/login');
        }

        // Get all courses
        $courses = $this->courseModel->getAvailableCourses();

        // Format courses data
        $courseData = [
            1 => ['title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python', 'duration' => 480],
            2 => ['title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals', 'duration' => 360],
            3 => ['title' => 'Database Management', 'description' => 'SQL and database design principles', 'duration' => 600],
            4 => ['title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving', 'duration' => 720]
        ];

        // Format results
        $formattedCourses = [];
        foreach ($courses as $course) {
            $courseId = $course['id'];
            if (isset($courseData[$courseId])) {
                $formattedCourses[] = [
                    'id' => $courseId,
                    'title' => $course['title'] ?? $courseData[$courseId]['title'],
                    'description' => $course['description'] ?? $courseData[$courseId]['description'],
                    'instructor' => 'Teacher User',
                    'duration' => ($courseData[$courseId]['duration'] ?? 0) . ' minutes'
                ];
            }
        }

        // If no courses in database, use hardcoded courses
        if (empty($formattedCourses)) {
            foreach ($courseData as $id => $course) {
                $formattedCourses[] = [
                    'id' => $id,
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'instructor' => 'Teacher User',
                    'duration' => $course['duration'] . ' minutes'
                ];
            }
        }

        $data = [
            'title' => 'All Courses',
            'courses' => $formattedCourses,
            'search_term' => '',
            'page' => 'courses'
        ];

        return view('courses/index', $data);
    }
}
