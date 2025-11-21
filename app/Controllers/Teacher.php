<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\NotificationModel;

class Teacher extends BaseController
{
    protected $enrollmentModel;
    protected $userModel;
    protected $courseModel;
    protected $assignmentModel;
    protected $submissionModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->notificationModel = new NotificationModel();
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

        // Prepare data for view
        $data = [
            'title' => 'Teacher Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ],
            'assignmentsCount' => $assignmentsCount,
            'pendingSubmissions' => $pendingSubmissions
        ];

        // Load teacher dashboard view
        return view('teacher_dashboard', $data);
    }

    /**
     * Enroll a student in a course
     */
    public function enrollStudent()
    {
        // Check if user is logged in and is a teacher
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
                'message' => 'Only teachers and admins can enroll students.'
            ])->setStatusCode(403);
        }

        // Get POST data
        $studentId = $this->request->getPost('student_id');
        $courseId = $this->request->getPost('course_id');

        // Validate input
        if (empty($studentId) || empty($courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student ID and Course ID are required.'
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

        // Check if already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student is already enrolled in this course.'
            ]);
        }

        // Enroll the student
        $enrollmentData = [
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'progress' => 0.00
        ];

        try {
            $result = $this->enrollmentModel->enrollUser($enrollmentData);
            
            if ($result) {
                // Create notification for student
                $course = $this->courseModel->find($courseId);
                $courseTitle = $course ? $course['title'] : "Course #{$courseId}";
                $message = "You have been enrolled in '{$courseTitle}' by your teacher.";
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
            log_message('error', 'Enrollment error: ' . $e->getMessage());
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
     * Notify all enrolled students about new assignment
     */
    protected function notifyEnrolledStudents($courseId, $assignmentTitle)
    {
        $db = \Config\Database::connect();
        $students = $db->query("
            SELECT DISTINCT user_id 
            FROM enrollments 
            WHERE course_id = ? AND status = 'active'
        ", [$courseId])->getResultArray();

        if (empty($students)) {
            return;
        }

        $course = $this->courseModel->find($courseId);
        $courseTitle = $course ? $course['title'] : "Course #{$courseId}";
        $message = "New assignment '{$assignmentTitle}' has been posted for {$courseTitle}. Check your assignments!";

        foreach ($students as $student) {
            try {
                $this->notificationModel->createNotification($student['user_id'], $message);
            } catch (\Exception $e) {
                log_message('error', "Failed to notify student {$student['user_id']}: " . $e->getMessage());
            }
        }
    }
}

