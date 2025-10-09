<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Course extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
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
                'message' => 'You must be logged in to enroll in courses.'
            ]);
        }

        // Check if user is a student (only students can enroll)
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can enroll in courses.'
            ]);
        }

        // Get course_id from POST request
        $courseId = $this->request->getPost('course_id');
        $userId = session('userID');
        
        // Validate user ID
        if (empty($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User session invalid. Please logout and login again.'
            ]);
        }
        
        // Validate course_id
        if (empty($courseId) || !is_numeric($courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID provided.'
            ]);
        }
        
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
            1 => ['id' => 1, 'title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with hands-on exercises and real-world projects.'],
            2 => ['id' => 2, 'title' => 'Web Development Basics', 'description' => 'Master HTML, CSS, and JavaScript to build responsive and interactive websites.'],
            3 => ['id' => 3, 'title' => 'Database Management', 'description' => 'Learn SQL, database design, and data management best practices.'],
            4 => ['id' => 4, 'title' => 'Mobile App Development', 'description' => 'Create mobile applications using modern frameworks and development tools.'],
            5 => ['id' => 5, 'title' => 'Cybersecurity Fundamentals', 'description' => 'Understand security threats, vulnerabilities, and protection strategies.'],
            6 => ['id' => 6, 'title' => 'Data Science and Analytics', 'description' => 'Explore data analysis, machine learning, and statistical modeling techniques.']
        ];
        
        // Check if course exists
        if (!isset($availableCourses[$courseId])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
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
            
            $insertQuery = $db->query("
                INSERT INTO enrollments (user_id, course_id, enrollment_date, created_at, updated_at) 
                VALUES (?, ?, ?, NOW(), NOW())
            ", [$userId, $courseId, $enrollmentData['enrollment_date']]);
            
            if ($db->affectedRows() > 0) {
                log_message('info', "Enrollment successful!");
                
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
}
