<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\NotificationModel;
use App\Models\QuizModel;
use App\Models\QuizQuestionModel;
use App\Models\QuizSubmissionModel;

class Teacher extends BaseController
{
    protected $enrollmentModel;
    protected $userModel;
    protected $courseModel;
    protected $assignmentModel;
    protected $submissionModel;
    protected $notificationModel;
    protected $quizModel;
    protected $quizQuestionModel;
    protected $quizSubmissionModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->notificationModel = new NotificationModel();
        $this->quizModel = new QuizModel();
        $this->quizQuestionModel = new QuizQuestionModel();
        $this->quizSubmissionModel = new QuizSubmissionModel();
    }

    /**
     * Teacher Dashboard
     * Task 3: Display teacher-specific dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to access the dashboard.');
            return redirect()->to('/login');
        }

        // Check if user is a teacher
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teachers only.');
            return redirect()->to('/dashboard');
        }

        // Get teacher's assignments count
        $teacherId = session('userID');
        $assignmentsCount = $this->assignmentModel->where('created_by', $teacherId)->countAllResults();
        
        // Get pending submissions count
        $pendingSubmissions = $this->submissionModel->select('assignment_submissions.*')
            ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
            ->where('assignments.created_by', $teacherId)
            ->where('assignment_submissions.status', 'submitted')
            ->countAllResults();

        // Get recent submissions (last 5)
        $recentSubmissions = $this->submissionModel->select('assignment_submissions.*, assignments.title as assignment_title, assignments.course_id, courses.title as course_title, users.name as student_name')
            ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
            ->join('courses', 'courses.id = assignments.course_id')
            ->join('users', 'users.id = assignment_submissions.student_id')
            ->where('assignments.created_by', $teacherId)
            ->orderBy('assignment_submissions.submitted_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Prepare data for view
        $data = [
            'title' => 'Teacher Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ],
            'assignmentsCount' => $assignmentsCount,
            'pendingSubmissions' => $pendingSubmissions,
            'recentSubmissions' => $recentSubmissions
        ];

        // Load teacher dashboard view
        return view('teacher_dashboard', $data);
    }

    /**
     * Force enroll a student in a course (bypass approval - for teachers and admins)
     */
    public function forceEnrollStudent()
    {
        // Check if user is logged in and is a teacher or admin
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only teachers and admins can force-enroll students.'
            ])->setStatusCode(403);
        }

        // Get POST data
        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');
        $schoolYear = $this->request->getPost('school_year');
        $semester = $this->request->getPost('semester');

        // Validate input
        if (empty($studentId) || empty($courseId) || empty($schoolYear) || empty($semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student ID, Course ID, School Year, and Semester are required.'
            ])->setStatusCode(400);
        }

        // Verify student exists and is a student
        $student = $this->userModel->find($studentId);
        if (!$student || strtolower($student['role']) !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid student ID.'
            ])->setStatusCode(400);
        }

        // Get course details
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Validate course school year and semester match
        if ($course['school_year'] !== $schoolYear || $course['semester'] !== $semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course school year and semester do not match.'
            ])->setStatusCode(400);
        }

        // Check for duplicate enrollment
        $duplicate = $this->enrollmentModel->checkDuplicateEnrollment($studentId, $courseId, $schoolYear, $semester);
        if ($duplicate) {
                return $this->response->setJSON([
                    'success' => false,
                'message' => 'Student already has an active or pending enrollment for this course in this semester.'
            ])->setStatusCode(400);
        }

        // Check for schedule conflicts
        $scheduleConflict = $this->enrollmentModel->checkStudentScheduleConflict($studentId, $courseId, $schoolYear, $semester);
        if ($scheduleConflict && isset($scheduleConflict['conflict']) && $scheduleConflict['conflict']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Schedule conflict detected: ' . $scheduleConflict['message'] . '. Please resolve the conflict before enrolling.'
            ])->setStatusCode(400);
        }

        // Create active enrollment (force-enroll)
        $enrollmentData = [
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'progress' => 0.00,
            'school_year' => $schoolYear,
            'semester' => $semester,
            'requested_by' => $userRole === 'admin' ? 'admin' : 'teacher',
            'approved_by' => session('userID'),
            'approved_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = $this->enrollmentModel->enrollUser($enrollmentData);
            
            if ($result) {
                // Notify student
                $courseTitle = $course['title'];
                $teacherName = session('name');
                $message = "You have been enrolled in '{$courseTitle}' ({$course['course_code']}) by {$teacherName}.";
                $this->notificationModel->createNotification($studentId, $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student enrolled successfully!',
                    'student' => [
                        'id' => $student['id'],
                        'name' => $student['name'],
                        'email' => $student['email']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to enroll student.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Force enroll error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View pending enrollment requests for teacher approval
     */
    public function enrollmentRequests()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $teacherId = session('userID');
        
        // Get pending requests
        try {
            if ($userRole === 'admin') {
                // Admin sees all pending requests
                $pendingRequests = $this->enrollmentModel->getPendingRequestsForAdmin();
            } else {
                // Teacher sees requests for their courses or unassigned courses
                $pendingRequests = $this->enrollmentModel->getPendingRequestsForTeacher($teacherId);
            }
            
            // If no results and user is admin, try fallback
            if (empty($pendingRequests) && $userRole === 'admin') {
                log_message('info', 'Admin: No results from getPendingRequestsForAdmin, trying fallback');
                $pendingRequests = $this->enrollmentModel->where('status', 'pending')->findAll();
                if (!empty($pendingRequests)) {
                    $this->_enrichEnrollmentRequests($pendingRequests);
                }
            }
            
            // If no results and user is teacher, try showing all pending (for debugging)
            if (empty($pendingRequests) && $userRole === 'teacher') {
                log_message('info', "Teacher {$teacherId}: No results from getPendingRequestsForTeacher, trying fallback");
                // Fallback: Get all pending enrollments for debugging
                $allPending = $this->enrollmentModel->where('status', 'pending')->findAll();
                if (!empty($allPending)) {
                    $this->_enrichEnrollmentRequests($allPending);
                    // Filter to show only relevant ones, or show all for now
                    $pendingRequests = $allPending;
                }
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching enrollment requests: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            // Fallback: Get all pending enrollments
            $pendingRequests = $this->enrollmentModel->where('status', 'pending')->findAll();
            
            // Enrich with user and course data
            if (!empty($pendingRequests)) {
                $this->_enrichEnrollmentRequests($pendingRequests);
            }
        }
        
        // Log for debugging
        log_message('info', 'Enrollment requests found: ' . count($pendingRequests ?? []));
        if (!empty($pendingRequests)) {
            log_message('info', 'First request: ' . json_encode($pendingRequests[0]));
        }

        $data = [
            'title' => 'Enrollment Requests - Teacher Dashboard',
            'requests' => $pendingRequests ?? []
        ];

        return view('teacher/enrollment_requests', $data);
    }
    
    /**
     * Helper method to enrich enrollment requests with user and course data
     */
    private function _enrichEnrollmentRequests(&$pendingRequests)
    {
        $userModel = new \App\Models\UserModel();
        foreach ($pendingRequests as &$request) {
            $student = $userModel->find($request['user_id']);
            $course = $this->courseModel->find($request['course_id']);
            
            $request['student_name'] = $student ? $student['name'] : 'Unknown';
            $request['student_email'] = $student ? $student['email'] : 'N/A';
            $request['course_title'] = $course ? $course['title'] : 'Course #' . $request['course_id'];
            $request['course_code'] = $course['course_code'] ?? 'N/A';
            $request['school_year'] = $request['school_year'] ?? ($course['school_year'] ?? 'N/A');
            $request['semester'] = $request['semester'] ?? ($course['semester'] ?? 'N/A');
            
            // Add teacher name if course has instructor
            if ($course && !empty($course['instructor_id'])) {
                $teacher = $userModel->find($course['instructor_id']);
                $request['teacher_name'] = $teacher ? $teacher['name'] : 'N/A';
            } else {
                $request['teacher_name'] = 'Unassigned';
            }
            
            // Add schedule info
            if ($course) {
                $request['schedule_day'] = $course['schedule_day'] ?? null;
                $request['schedule_time_start'] = $course['schedule_time_start'] ?? null;
                $request['schedule_time_end'] = $course['schedule_time_end'] ?? null;
            }
        }
    }

    /**
     * Approve enrollment request
     */
    public function approveEnrollment($enrollmentId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only teachers and admins can approve enrollments.'
            ])->setStatusCode(403);
        }

        if (!$enrollmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment ID is required.'
            ])->setStatusCode(400);
        }

        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment || $enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found or already processed.'
            ])->setStatusCode(404);
        }

        // Verify teacher owns the course (if not admin)
        if ($userRole === 'teacher') {
            $course = $this->courseModel->find($enrollment['course_id']);
            if (!$course || $course['instructor_id'] != session('userID')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only approve enrollments for your own courses.'
                ])->setStatusCode(403);
            }
        }

        // Check for schedule conflicts before approving (only if school_year and semester are set)
        if (!empty($enrollment['school_year']) && !empty($enrollment['semester'])) {
            $scheduleConflict = $this->enrollmentModel->checkStudentScheduleConflict(
                $enrollment['user_id'], 
                $enrollment['course_id'], 
                $enrollment['school_year'], 
                $enrollment['semester']
            );
            
            if ($scheduleConflict && isset($scheduleConflict['conflict']) && $scheduleConflict['conflict']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot approve: Schedule conflict detected - ' . $scheduleConflict['message']
                ])->setStatusCode(400);
            }
        }

        // Update enrollment to active
        $updateData = [
            'status' => 'active',
            'approved_by' => session('userID'),
            'approved_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->enrollmentModel->update($enrollmentId, $updateData)) {
                // Notify student
                $course = $this->courseModel->find($enrollment['course_id']);
                $courseTitle = $course ? $course['title'] : 'Course';
                $approverName = session('name');
                $message = "Your enrollment request for '{$courseTitle}' has been approved by {$approverName}.";
                $this->notificationModel->createNotification($enrollment['user_id'], $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Enrollment approved successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to approve enrollment.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Approve enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject enrollment request
     */
    public function rejectEnrollmentRequest($enrollmentId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only teachers and admins can reject enrollments.'
            ])->setStatusCode(403);
        }

        if (!$enrollmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment ID is required.'
            ])->setStatusCode(400);
        }

        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment || $enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found or already processed.'
            ])->setStatusCode(404);
        }

        // Verify teacher owns the course (if not admin)
        if ($userRole === 'teacher') {
            $course = $this->courseModel->find($enrollment['course_id']);
            if (!$course || $course['instructor_id'] != session('userID')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only reject enrollments for your own courses.'
                ])->setStatusCode(403);
            }
        }

        // Update enrollment to rejected
        try {
            if ($this->enrollmentModel->update($enrollmentId, ['status' => 'rejected'])) {
                // Notify student
                $course = $this->courseModel->find($enrollment['course_id']);
                $courseTitle = $course ? $course['title'] : 'Course';
                $rejectorName = session('name');
                $message = "Your enrollment request for '{$courseTitle}' has been rejected by {$rejectorName}.";
                $this->notificationModel->createNotification($enrollment['user_id'], $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Enrollment request rejected.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to reject enrollment.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Reject enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display enroll student page
     */
    public function enrollStudentPage()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        // Get all students
        $students = $this->userModel->where('role', 'student')->findAll();

        // Get all courses
        $courses = $this->courseModel->findAll();
        
        // If no courses in DB, use hardcoded courses
        if (empty($courses)) {
            $courses = [
                ['id' => 1, 'title' => 'Introduction to Programming'],
                ['id' => 2, 'title' => 'Web Development Basics'],
                ['id' => 3, 'title' => 'Database Management'],
                ['id' => 4, 'title' => 'Data Structures & Algorithms']
            ];
        }

        $data = [
            'title' => 'Enroll Student - Teacher Dashboard',
            'students' => $students,
            'courses' => $courses
        ];

        return view('teacher/enroll_student', $data);
    }

    /**
     * Search and view students
     */
    public function students()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $searchTerm = $this->request->getGet('q') ?? '';
        $students = [];

        if (!empty($searchTerm)) {
            // Search students by name or email
            $students = $this->userModel
                ->where('role', 'student')
                ->groupStart()
                    ->like('name', $searchTerm)
                    ->orLike('email', $searchTerm)
                ->groupEnd()
                ->orderBy('name', 'ASC')
                ->findAll();
        } else {
            // If no search term, show all students (or limit to recent ones)
            $students = $this->userModel
                ->where('role', 'student')
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        // Get enrollment information for each student
        foreach ($students as &$student) {
            $enrollments = $this->enrollmentModel
                ->where('user_id', $student['id'])
                ->findAll();
            
            $student['enrollment_count'] = count($enrollments);
            $student['active_enrollments'] = count(array_filter($enrollments, function($e) {
                return $e['status'] === 'active';
            }));
        }

        $data = [
            'title' => 'Search Students - Teacher Dashboard',
            'students' => $students,
            'search_term' => $searchTerm
        ];

        return view('teacher/students', $data);
    }

    /**
     * Create a new assignment
     */
    public function createAssignment()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $title = $this->request->getPost('title');
            $instruction = $this->request->getPost('instruction');
            $question = $this->request->getPost('question');
            $dueDate = $this->request->getPost('due_date');
            $maxScore = $this->request->getPost('max_score') ?? 100;

            // Validation
            if (empty($courseId) || empty($title) || empty($instruction) || empty($question)) {
                session()->setFlashdata('error', 'Course, Title, Instruction, and Question are required.');
                return redirect()->back()->withInput();
            }

            $assignmentData = [
                'course_id' => $courseId,
                'title' => $title,
                'description' => $instruction, // Store instruction in description field
                'instructions' => $question, // Store question in instructions field
                'due_date' => !empty($dueDate) ? $dueDate : null,
                'max_score' => $maxScore,
                'created_by' => session('userID'),
                'is_published' => true
            ];

            if ($this->assignmentModel->insert($assignmentData)) {
                // Notify all enrolled students
                $this->notifyEnrolledStudents($courseId, $title);
                
                session()->setFlashdata('success', 'Assignment created successfully!');
                return redirect()->to('/teacher/assignments');
            } else {
                session()->setFlashdata('error', 'Failed to create assignment.');
                return redirect()->back()->withInput();
            }
        }

        // GET request - show form
        $courses = $this->courseModel->findAll();
        if (empty($courses)) {
            $courses = [
                ['id' => 1, 'title' => 'Introduction to Programming'],
                ['id' => 2, 'title' => 'Web Development Basics'],
                ['id' => 3, 'title' => 'Database Management'],
                ['id' => 4, 'title' => 'Data Structures & Algorithms']
            ];
        }

        $data = [
            'title' => 'Create Assignment - Teacher Dashboard',
            'courses' => $courses
        ];

        return view('teacher/create_assignment', $data);
    }

    /**
     * View all assignments created by teacher
     */
    public function assignments()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $teacherId = session('userID');
        $assignments = $this->assignmentModel->getAssignmentsByTeacher($teacherId);

        $data = [
            'title' => 'My Assignments - Teacher Dashboard',
            'assignments' => $assignments
        ];

        return view('teacher/assignments', $data);
    }

    /**
     * View submissions for a specific assignment
     */
    public function viewSubmissions($assignmentId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        if (!$assignmentId) {
            session()->setFlashdata('error', 'Assignment ID is required.');
            return redirect()->to('/teacher/assignments');
        }

        $teacherId = session('userID');
        $assignment = $this->assignmentModel->getAssignmentWithDetails($assignmentId);

        if (!$assignment || $assignment['created_by'] != $teacherId) {
            session()->setFlashdata('error', 'Assignment not found or access denied.');
            return redirect()->to('/teacher/assignments');
        }

        $submissions = $this->submissionModel->getSubmissionsByAssignment($assignmentId);

        $data = [
            'title' => 'View Submissions - Teacher Dashboard',
            'assignment' => $assignment,
            'submissions' => $submissions
        ];

        return view('teacher/view_submissions', $data);
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        $submissionId = $this->request->getPost('submission_id');
        $score = $this->request->getPost('score');
        $feedback = $this->request->getPost('feedback');

        if (empty($submissionId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Submission ID is required.'
            ])->setStatusCode(400);
        }

        // Get submission and verify teacher owns the assignment
        $submission = $this->submissionModel->find($submissionId);
        if (!$submission) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Submission not found.'
            ])->setStatusCode(404);
        }

        $assignment = $this->assignmentModel->find($submission['assignment_id']);
        if (!$assignment || $assignment['created_by'] != session('userID')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        // Update submission
        $updateData = [
            'score' => !empty($score) ? $score : null,
            'feedback' => $feedback,
            'status' => 'graded',
            'graded_at' => date('Y-m-d H:i:s'),
            'graded_by' => session('userID')
        ];

        if ($this->submissionModel->update($submissionId, $updateData)) {
            // Notify student
            $message = "Your submission for '{$assignment['title']}' has been graded.";
            if (!empty($score)) {
                $message .= " Score: {$score}/{$assignment['max_score']}";
            }
            $this->notificationModel->createNotification($submission['student_id'], $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Submission graded successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to grade submission.'
            ]);
        }
    }

    /**
     * Download submission file
     */
    public function downloadSubmission($submissionId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        if (!$submissionId) {
            return redirect()->back()->with('error', 'Submission ID is required.');
        }

        $teacherId = session('userID');
        $submission = $this->submissionModel->find($submissionId);

        if (!$submission) {
            return redirect()->back()->with('error', 'Submission not found.');
        }

        // Verify teacher owns the assignment
        $assignment = $this->assignmentModel->find($submission['assignment_id']);
        if (!$assignment || $assignment['created_by'] != $teacherId) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (empty($submission['file_path']) || !file_exists($submission['file_path'])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($submission['file_path'], null)
                              ->setFileName($submission['file_name']);
    }

    /**
     * Notify all enrolled students about new assignment or quiz
     */
    protected function notifyEnrolledStudents($courseId, $itemTitle, $type = 'assignment')
    {
        $enrollments = $this->enrollmentModel->where('course_id', $courseId)
                                            ->whereIn('status', ['active', 'pending'])
                                            ->findAll();
        
        $students = array_map(function($enrollment) {
            return ['user_id' => $enrollment['user_id']];
        }, $enrollments);

        if (empty($students)) {
            return;
        }

        $course = $this->courseModel->find($courseId);
        $courseTitle = $course ? $course['title'] : "Course #{$courseId}";
        $itemType = $type === 'quiz' ? 'quiz' : 'assignment';
        $message = "New {$itemType} '{$itemTitle}' has been posted for {$courseTitle}. Check your {$itemType}s!";

        foreach ($students as $student) {
            try {
                $this->notificationModel->createNotification($student['user_id'], $message);
            } catch (\Exception $e) {
                log_message('error', "Failed to notify student {$student['user_id']}: " . $e->getMessage());
            }
        }
    }

    /**
     * View all quizzes created by teacher
     */
    public function quizzes()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $teacherId = session('userID');
        $quizzes = $this->quizModel->getQuizzesByTeacher($teacherId);

        $data = [
            'title' => 'My Quizzes - Teacher Dashboard',
            'quizzes' => $quizzes
        ];

        return view('teacher/quizzes', $data);
    }

    /**
     * Create a new quiz
     */
    public function createQuiz()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $teacherId = session('userID');

        // Get courses taught by this teacher
        $courses = $this->courseModel->where('instructor_id', $teacherId)->findAll();

        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $instructions = $this->request->getPost('instructions');
            $timeLimit = $this->request->getPost('time_limit');
            $maxAttempts = $this->request->getPost('max_attempts') ?? 1;
            $passingScore = $this->request->getPost('passing_score') ?? 70;
            $isPublished = $this->request->getPost('is_published') ? true : false;
            $showCorrectAnswers = $this->request->getPost('show_correct_answers') ? true : false;
            $randomizeQuestions = $this->request->getPost('randomize_questions') ? true : false;

            // Validation
            if (empty($courseId) || empty($title)) {
                session()->setFlashdata('error', 'Course and Title are required.');
                return redirect()->back()->withInput();
            }

            $quizData = [
                'course_id' => $courseId,
                'title' => $title,
                'description' => $description ?? null,
                'instructions' => $instructions ?? null,
                'time_limit' => !empty($timeLimit) ? (int)$timeLimit : null,
                'max_attempts' => (int)$maxAttempts,
                'passing_score' => (float)$passingScore,
                'is_published' => $isPublished,
                'show_correct_answers' => $showCorrectAnswers,
                'randomize_questions' => $randomizeQuestions,
            ];

            if ($quizId = $this->quizModel->insert($quizData)) {
                // Handle questions if provided
                $questions = $this->request->getPost('questions');
                if (!empty($questions) && is_array($questions)) {
                    $orderIndex = 0;
                    foreach ($questions as $question) {
                        if (!empty($question['question_text'])) {
                            $questionType = $question['question_type'] ?? 'multiple_choice';
                            $correctAnswer = '';
                            $options = null;
                            
                            // Process based on question type
                            if ($questionType === 'multiple_choice') {
                                // Get options array
                                $optionsArray = $question['options'] ?? [];
                                if (!empty($optionsArray) && is_array($optionsArray)) {
                                    $options = json_encode($optionsArray);
                                    // Get correct answer index
                                    $correctOptionIndex = $question['correct_option'] ?? null;
                                    if ($correctOptionIndex !== null && isset($optionsArray[$correctOptionIndex])) {
                                        $correctAnswer = (string)$correctOptionIndex;
                                    }
                                }
                            } else {
                                // For true/false and short_answer, use the correct_answer directly
                                $correctAnswer = $question['correct_answer'] ?? '';
                            }
                            
                            if (!empty($correctAnswer)) {
                                $questionData = [
                                    'quiz_id' => $quizId,
                                    'question_text' => $question['question_text'],
                                    'question_type' => $questionType,
                                    'options' => $options,
                                    'correct_answer' => $correctAnswer,
                                    'points' => !empty($question['points']) ? (float)$question['points'] : 1.00,
                                    'order_index' => $orderIndex++,
                                ];
                                $this->quizQuestionModel->insert($questionData);
                            }
                        }
                    }
                }

                // Notify enrolled students
                $this->notifyEnrolledStudents($courseId, $title, 'quiz');
                
                session()->setFlashdata('success', 'Quiz created successfully!');
                return redirect()->to('/teacher/quizzes');
            } else {
                session()->setFlashdata('error', 'Failed to create quiz.');
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Create Quiz - Teacher Dashboard',
            'courses' => $courses
        ];

        return view('teacher/create_quiz', $data);
    }

    /**
     * View quiz submissions
     */
    public function viewQuizSubmissions($quizId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        if (!$quizId) {
            session()->setFlashdata('error', 'Quiz ID is required.');
            return redirect()->to('/teacher/quizzes');
        }

        $quiz = $this->quizModel->getQuizWithDetails($quizId);
        if (!$quiz) {
            session()->setFlashdata('error', 'Quiz not found.');
            return redirect()->to('/teacher/quizzes');
        }

        $submissions = $this->quizSubmissionModel->getSubmissionsByQuiz($quizId);

        $data = [
            'title' => 'Quiz Submissions - Teacher Dashboard',
            'quiz' => $quiz,
            'submissions' => $submissions
        ];

        return view('teacher/quiz_submissions', $data);
    }

    /**
     * Grade a quiz submission
     */
    public function gradeQuizSubmission($submissionId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher' && $userRole !== 'admin') {
            return redirect()->to('/dashboard');
        }

        if (!$submissionId) {
            session()->setFlashdata('error', 'Submission ID is required.');
            return redirect()->back();
        }

        $submission = $this->quizSubmissionModel->find($submissionId);
        if (!$submission) {
            session()->setFlashdata('error', 'Submission not found.');
            return redirect()->back();
        }

        if ($this->request->getMethod() === 'POST') {
            $score = $this->request->getPost('score');
            
            if ($score !== null) {
                $updateData = [
                    'score' => (float)$score,
                ];
                
                if ($this->quizSubmissionModel->update($submissionId, $updateData)) {
                    session()->setFlashdata('success', 'Quiz submission graded successfully!');
                    return redirect()->to('/teacher/quiz-submissions/' . $submission['quiz_id']);
                } else {
                    session()->setFlashdata('error', 'Failed to grade submission.');
                }
            }
        }

        $quiz = $this->quizModel->find($submission['quiz_id']);
        $questions = $this->quizQuestionModel->getQuestionsByQuiz($submission['quiz_id']);
        $submissionData = json_decode($submission['submission_data'], true);
        $student = $this->userModel->find($submission['user_id']);

        $data = [
            'title' => 'Grade Quiz Submission - Teacher Dashboard',
            'submission' => $submission,
            'quiz' => $quiz,
            'questions' => $questions,
            'submissionData' => $submissionData,
            'student' => $student
        ];

        return view('teacher/grade_quiz_submission', $data);
    }

}

