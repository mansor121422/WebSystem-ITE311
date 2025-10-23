<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function register()
    {
        helper(['form']);
        
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Get form data
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $password_confirm = $this->request->getPost('password_confirm');

            // Simple validation
            if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
                session()->setFlashdata('error', 'All fields are required.');
                return view('auth/register');
            }

            if ($password !== $password_confirm) {
                session()->setFlashdata('error', 'Passwords do not match.');
                return view('auth/register');
            }

            // Enhanced password validation
            if (strlen($password) < 8) {
                session()->setFlashdata('error', 'Password must be at least 8 characters.');
                return view('auth/register');
            }

            if (!preg_match('/[A-Z]/', $password)) {
                session()->setFlashdata('error', 'Password must contain at least one uppercase letter.');
                return view('auth/register');
            }

            if (!preg_match('/[a-z]/', $password)) {
                session()->setFlashdata('error', 'Password must contain at least one lowercase letter.');
                return view('auth/register');
            }

            if (!preg_match('/[0-9]/', $password)) {
                session()->setFlashdata('error', 'Password must contain at least one number.');
                return view('auth/register');
            }

            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                session()->setFlashdata('error', 'Password must contain at least one special character.');
                return view('auth/register');
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Save user data to database
            $userModel = new \App\Models\UserModel();
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];



            try {
                $result = $userModel->insert($userData);
                
                if ($result) {
                    // Step 7: Create welcome notification for new user
                    try {
                        $notificationModel = new \App\Models\NotificationModel();
                        $welcomeMessage = "Welcome to our Learning Management System, {$name}! Start exploring courses and announcements.";
                        $notificationModel->createNotification($result, $welcomeMessage);
                        log_message('info', "Welcome notification created for new user: {$name}");
                    } catch (\Exception $e) {
                        // Don't fail registration if notification creation fails
                        log_message('error', "Failed to create welcome notification: " . $e->getMessage());
                    }
                    
                    // Set flash message and redirect to login
                    session()->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to('/login');
                } else {
                    // Debug: Show the error
                    $errors = $userModel->errors();
                    session()->setFlashdata('error', 'Registration failed. Errors: ' . json_encode($errors));
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Registration failed: ' . $e->getMessage());
            }
        }

        // Load the registration view
        return view('auth/register');
    }

    public function login()
    {
        helper(['form']);
        
        // Check if form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            // Get form data
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Simple validation
            if (empty($email) || empty($password)) {
                session()->setFlashdata('error', 'Email and password are required.');
                return view('auth/login');
            }

            // Check database for user
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();

            // Check if user was found
            if (!$user) {
                session()->setFlashdata('error', 'Invalid credentials.');
                return view('auth/login');
            }

            // Check password verification
            if (!password_verify($password, $user['password'])) {
                session()->setFlashdata('error', 'Invalid credentials.');
                return view('auth/login');
            }

            // Credentials are correct, create session
            $sessionData = [
                'userID' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'logged_in' => true,
                'last_activity' => time()
            ];
            
            session()->set($sessionData);

            // Task 3: Role-based redirection for enhanced user experience
            session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
            
            // Redirect based on user role
            $role = strtolower($user['role']);
            
            if ($role === 'student') {
                // Students redirected to announcements
                return redirect()->to('/announcements');
            } elseif ($role === 'teacher') {
                // Teachers redirected to teacher dashboard
                return redirect()->to('/teacher/dashboard');
            } elseif ($role === 'admin') {
                // Admins redirected to admin dashboard
                return redirect()->to('/admin/dashboard');
            } else {
                // Default fallback (if role not recognized)
                return redirect()->to('/dashboard');
            }
        }

        // For GET requests, just load the login view
        return view('auth/login');
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Redirect to homepage
        return redirect()->to('/');
    }

    public function clearSession()
    {
        // Destroy the current session completely
        session()->destroy();
        
        // Clear any remaining session data
        $_SESSION = [];
        
        // Regenerate session ID
        session_regenerate_id(true);
        
        // Redirect to homepage with success message
        session()->setFlashdata('success', 'Session cleared successfully.');
        return redirect()->to('/');
    }

    public function dashboard()
    {
        try {
            // Check if user is logged in and has valid session
            if (!session()->get('logged_in') || !session()->get('userID') || !session()->get('role')) {
                // Clear any existing session data
                session()->destroy();
                session()->setFlashdata('error', 'You must be logged in to access the dashboard.');
                return redirect()->to('/login');
            }

        // Check for session timeout (optional - 30 minutes)
        $lastActivity = session()->get('last_activity');
        if ($lastActivity && (time() - $lastActivity > 1800)) { // 30 minutes
            session()->destroy();
            session()->setFlashdata('error', 'Your session has expired. Please login again.');
            return redirect()->to('/login');
        }

        // Update last activity time
        session()->set('last_activity', time());

        $userModel = new \App\Models\UserModel();
        $role = strtolower(session('role') ?? '');
        
        // Fetch role-specific data from database
        $data = [
            'role' => $role,
            'user' => [
                'id' => session('userID'),
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role'),
            ],
        ];

        // Role-specific data fetching
        if ($role === 'admin') {
            $data['totalUsers'] = 3; // Placeholder
            $data['totalCourses'] = 4; // Placeholder
            $data['recentUsers'] = []; // Placeholder
        } elseif ($role === 'teacher') {
            $data['myCourses'] = ['Math 101', 'Science 202']; // Placeholder
            $data['pendingAssignments'] = 5; // Placeholder
            $data['totalStudents'] = 1; // Placeholder
        } elseif ($role === 'student') {
            // Get actual enrolled courses from database
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
            ", [session('userID')]);
            
            $enrollments = $enrolledQuery->getResultArray();
            
            // Format enrolled courses data
            $enrolledCourses = [];
            foreach ($enrollments as $enrollment) {
                $courseId = $enrollment['course_id'];
                if (isset($courseData[$courseId])) {
                    $course = $courseData[$courseId];
                    $enrolledCourses[] = [
                        'id' => $courseId,
                        'course_title' => $course['title'],
                        'course_description' => $course['description'],
                        'course_instructor' => 'Teacher User',
                        'course_duration' => $course['duration'] . ' minutes',
                        'enrollment_date' => $enrollment['enrollment_date'],
                        'status' => $enrollment['status'] ?? 'active',
                        'progress' => $enrollment['progress'] ?? 0
                    ];
                }
            }
            
            $data['enrolledCourses'] = $enrolledCourses;
            
            // Use hardcoded available courses
            $data['availableCourses'] = [
                [
                    'id' => 1,
                    'title' => 'Introduction to Programming',
                    'description' => 'Learn the fundamentals of programming with Python',
                    'instructor' => 'Teacher User',
                    'duration' => '480 minutes'
                ],
                [
                    'id' => 2,
                    'title' => 'Web Development Basics',
                    'description' => 'HTML, CSS, and JavaScript fundamentals',
                    'instructor' => 'Teacher User',
                    'duration' => '360 minutes'
                ],
                [
                    'id' => 3,
                    'title' => 'Database Management',
                    'description' => 'SQL and database design principles',
                    'instructor' => 'Teacher User',
                    'duration' => '600 minutes'
                ],
                [
                    'id' => 4,
                    'title' => 'Data Structures & Algorithms',
                    'description' => 'Advanced programming concepts and problem solving',
                    'instructor' => 'Teacher User',
                    'duration' => '720 minutes'
                ]
            ];
            
            $data['upcomingDeadlines'] = ['Assignment 1 (Oct 1)', 'Quiz 2 (Oct 5)']; // Placeholder
            $data['completedAssignments'] = 3; // Placeholder
        }

        return view('auth/dashboard', $data);
        
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            
            // Return a simple error page
            return view('errors/html/error_exception', [
                'message' => 'An error occurred while loading the dashboard. Please try again.',
                'code' => 500
            ]);
        }
    }
}
