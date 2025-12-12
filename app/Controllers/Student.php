<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Models\QuizModel;
use App\Models\QuizQuestionModel;
use App\Models\QuizSubmissionModel;

class Student extends BaseController
{
    protected $assignmentModel;
    protected $submissionModel;
    protected $enrollmentModel;
    protected $notificationModel;
    protected $courseModel;
    protected $quizModel;
    protected $quizQuestionModel;
    protected $quizSubmissionModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
        $this->courseModel = new CourseModel();
        $this->quizModel = new QuizModel();
        $this->quizQuestionModel = new QuizQuestionModel();
        $this->quizSubmissionModel = new QuizSubmissionModel();
    }

    /**
     * View enrolled courses
     */
    public function courses()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        $studentId = session('userID');

        // Get all courses the student is enrolled in
        $enrollments = $this->enrollmentModel->where('user_id', $studentId)
                                             ->where('status', 'active')
                                             ->findAll();

        // Hardcoded course data (matching the system)
        $courseData = [
            1 => ['title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python', 'duration' => 480],
            2 => ['title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals', 'duration' => 360],
            3 => ['title' => 'Database Management', 'description' => 'SQL and database design principles', 'duration' => 600],
            4 => ['title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving', 'duration' => 720]
        ];

        // Format enrolled courses data
        $enrolledCourses = [];
        foreach ($enrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (isset($courseData[$courseId])) {
                $course = $courseData[$courseId];
                
                // Check if enrollment is expired
                $isExpired = $this->enrollmentModel->isEnrollmentExpired($enrollment);
                $daysRemaining = null;
                
                if (!empty($enrollment['semester_end_date']) && !$isExpired) {
                    $now = new \DateTime();
                    $endDate = new \DateTime($enrollment['semester_end_date']);
                    $diff = $now->diff($endDate);
                    $daysRemaining = $diff->days;
                }
                
                $enrolledCourses[] = [
                    'id' => $courseId,
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'instructor' => 'Teacher User',
                    'duration' => $course['duration'] . ' minutes',
                    'enrollment_date' => $enrollment['enrollment_date'],
                    'semester_end_date' => $enrollment['semester_end_date'] ?? null,
                    'status' => $isExpired ? 'expired' : ($enrollment['status'] ?? 'active'),
                    'progress' => $enrollment['progress'] ?? 0,
                    'days_remaining' => $daysRemaining
                ];
            }
        }

        $data = [
            'title' => 'My Courses - Student Dashboard',
            'courses' => $enrolledCourses
        ];

        return view('student/courses', $data);
    }

    /**
     * View pending enrollment requests
     */
    public function pendingEnrollments()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        $studentId = session('userID');

        // Get all pending enrollments
        $pendingEnrollments = $this->enrollmentModel->where('user_id', $studentId)
                                                    ->where('status', 'pending')
                                                    ->orderBy('enrollment_date', 'DESC')
                                                    ->findAll();

        // Hardcoded course data
        $courseData = [
            1 => ['title' => 'Introduction to Programming', 'description' => 'Learn the fundamentals of programming with Python', 'duration' => 480],
            2 => ['title' => 'Web Development Basics', 'description' => 'HTML, CSS, and JavaScript fundamentals', 'duration' => 360],
            3 => ['title' => 'Database Management', 'description' => 'SQL and database design principles', 'duration' => 600],
            4 => ['title' => 'Data Structures & Algorithms', 'description' => 'Advanced programming concepts and problem solving', 'duration' => 720]
        ];

        // Format pending enrollments
        $enrollmentRequests = [];
        foreach ($pendingEnrollments as $enrollment) {
            $courseId = $enrollment['course_id'];
            if (isset($courseData[$courseId])) {
                $course = $courseData[$courseId];
                $enrollmentRequests[] = [
                    'id' => $enrollment['id'],
                    'course_id' => $courseId,
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'instructor' => 'Teacher User',
                    'duration' => $course['duration'] . ' minutes',
                    'enrollment_date' => $enrollment['enrollment_date'],
                    'status' => $enrollment['status']
                ];
            }
        }

        $data = [
            'title' => 'Pending Enrollment Requests - Student Dashboard',
            'enrollments' => $enrollmentRequests
        ];

        return view('student/pending_enrollments', $data);
    }

    /**
     * Accept enrollment request
     */
    public function acceptEnrollment($enrollmentId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can accept enrollments.'
            ])->setStatusCode(403);
        }

        if (!$enrollmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment ID is required.'
            ])->setStatusCode(400);
        }

        $studentId = session('userID');
        $enrollment = $this->enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found.'
            ])->setStatusCode(404);
        }

        // Verify the enrollment belongs to the student and is pending
        if ($enrollment['user_id'] != $studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        if ($enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This enrollment request is no longer pending.'
            ])->setStatusCode(400);
        }

        // Update enrollment status to active
        $updateData = [
            'status' => 'active',
            'enrollment_date' => date('Y-m-d H:i:s') // Update enrollment date to now
        ];

        // Set semester end date (4 months from now)
        $semesterEndDate = new \DateTime();
        $semesterEndDate->modify('+4 months');
        $updateData['semester_end_date'] = $semesterEndDate->format('Y-m-d H:i:s');

        try {
            if ($this->enrollmentModel->update($enrollmentId, $updateData)) {
                // Create success notification
                $course = $this->enrollmentModel->select('courses.title')
                                                ->join('courses', 'courses.id = enrollments.course_id')
                                                ->where('enrollments.id', $enrollmentId)
                                                ->first();
                $courseTitle = $course ? $course['title'] : "Course";
                $message = "You have accepted the enrollment request for '{$courseTitle}'. Welcome to the course!";
                $this->notificationModel->createNotification($studentId, $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Enrollment accepted successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to accept enrollment.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Accept enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Request enrollment in a course (sends to teacher for approval)
     */
    public function requestEnrollment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can request enrollment.'
            ])->setStatusCode(403);
        }

        $studentId = session('userID');
        $courseId = $this->request->getPost('course_id');
        $schoolYear = $this->request->getPost('school_year');
        $semester = $this->request->getPost('semester');

        // Validate input
        if (empty($courseId) || empty($schoolYear) || empty($semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID, School Year, and Semester are required.'
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

        // Validate course has same school year and semester
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
                'message' => 'You already have an active or pending enrollment for this course in this semester.'
            ])->setStatusCode(400);
        }

        // Check for schedule conflicts
        $scheduleConflict = $this->enrollmentModel->checkStudentScheduleConflict($studentId, $courseId, $schoolYear, $semester);
        if ($scheduleConflict && isset($scheduleConflict['conflict']) && $scheduleConflict['conflict']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Schedule conflict detected: ' . $scheduleConflict['message']
            ])->setStatusCode(400);
        }

        // Create enrollment request
        $enrollmentData = [
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'progress' => 0.00,
            'school_year' => $schoolYear,
            'semester' => $semester,
            'requested_by' => 'student'
        ];

        try {
            $result = $this->enrollmentModel->enrollUser($enrollmentData);
            
            if ($result) {
                // Notify teacher about the enrollment request
                $teacherId = $course['instructor_id'];
                $studentName = session('name');
                $courseTitle = $course['title'];
                $message = "New enrollment request from {$studentName} for course '{$courseTitle}' ({$course['course_code']}).";
                $this->notificationModel->createNotification($teacherId, $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Enrollment request sent! Waiting for teacher approval.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create enrollment request.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Enrollment request error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject enrollment request
     */
    public function rejectEnrollment($enrollmentId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can reject enrollments.'
            ])->setStatusCode(403);
        }

        if (!$enrollmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment ID is required.'
            ])->setStatusCode(400);
        }

        $studentId = session('userID');
        $enrollment = $this->enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found.'
            ])->setStatusCode(404);
        }

        // Verify the enrollment belongs to the student and is pending
        if ($enrollment['user_id'] != $studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        if ($enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This enrollment request is no longer pending.'
            ])->setStatusCode(400);
        }

        // Update enrollment status to rejected
        try {
            if ($this->enrollmentModel->update($enrollmentId, ['status' => 'rejected'])) {
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
     * View all assignments for enrolled courses
     */
    public function assignments()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        $studentId = session('userID');

        // Expire old enrollments first
        $this->enrollmentModel->expireOldEnrollments();

        // Get all active (non-expired) courses the student is enrolled in
        $activeEnrollments = $this->enrollmentModel->getActiveEnrollments($studentId);
        
        // Also get pending enrollments (students should see assignments even if enrollment is pending approval)
        $pendingEnrollments = $this->enrollmentModel->getPendingEnrollments($studentId);
        
        // Combine both active and pending enrollments
        $allEnrollments = array_merge($activeEnrollments ?? [], $pendingEnrollments ?? []);
        
        // Get unique course IDs from all enrollments, filter out null/empty values
        $courseIds = array_filter(array_unique(array_column($allEnrollments, 'course_id')), function($id) {
            return !empty($id);
        });

        // Get all assignments for enrolled courses (both active and pending)
        $assignments = [];
        if (!empty($courseIds)) {
            $assignments = $this->assignmentModel->select('assignments.*, courses.title as course_title')
                                                  ->join('courses', 'courses.id = assignments.course_id')
                                                  ->whereIn('assignments.course_id', $courseIds)
                                                  ->where('assignments.is_published', true)
                                                  ->orderBy('assignments.created_at', 'DESC')
                                                  ->findAll();
        }

        // Get submission status for each assignment
        foreach ($assignments as &$assignment) {
            $submission = $this->submissionModel->getSubmission($assignment['id'], $studentId);
            $assignment['submission'] = $submission;
            $assignment['has_submitted'] = $submission !== null;
        }

        $data = [
            'title' => 'My Assignments - Student Dashboard',
            'assignments' => $assignments
        ];

        return view('student/assignments', $data);
    }

    /**
     * View a specific assignment
     */
    public function viewAssignment($assignmentId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$assignmentId) {
            session()->setFlashdata('error', 'Assignment ID is required.');
            return redirect()->to('/student/assignments');
        }

        $studentId = session('userID');

        // Get assignment with details
        $assignment = $this->assignmentModel->getAssignmentWithDetails($assignmentId);

        if (!$assignment) {
            session()->setFlashdata('error', 'Assignment not found.');
            return redirect()->to('/student/assignments');
        }

        // Check if student is enrolled in the course
        $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled($studentId, $assignment['course_id']);
        if (!$isEnrolled) {
            session()->setFlashdata('error', 'You must be enrolled in this course to view the assignment.');
            return redirect()->to('/student/assignments');
        }

        // Get existing submission if any
        $submission = $this->submissionModel->getSubmission($assignmentId, $studentId);

        $data = [
            'title' => 'View Assignment - Student Dashboard',
            'assignment' => $assignment,
            'submission' => $submission,
            'has_submitted' => $submission !== null
        ];

        return view('student/view_assignment', $data);
    }

    /**
     * Submit an assignment
     */
    public function submitAssignment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can submit assignments.'
            ])->setStatusCode(403);
        }

        $assignmentId = $this->request->getPost('assignment_id');
        $submissionText = $this->request->getPost('submission_text');
        $studentId = session('userID');

        // Validate input
        if (empty($assignmentId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment ID is required.'
            ])->setStatusCode(400);
        }

        // Check if assignment exists and student is enrolled
        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assignment not found.'
            ])->setStatusCode(404);
        }

        $isEnrolled = $this->enrollmentModel->isAlreadyEnrolled($studentId, $assignment['course_id']);
        if (!$isEnrolled) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be enrolled in this course to submit assignments.'
            ])->setStatusCode(403);
        }

        // Check if already submitted
        if ($this->submissionModel->hasSubmitted($assignmentId, $studentId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You have already submitted this assignment.'
            ]);
        }

        // Handle file upload if provided
        $filePath = null;
        $fileName = null;
        $file = $this->request->getFile('submission_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate file size (10MB limit)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File size exceeds 10MB limit.'
                ])->setStatusCode(400);
            }

            // Check file extension
            $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];
            $fileExtension = strtolower($file->getClientExtension());
            if (!in_array($fileExtension, $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)
                ])->setStatusCode(400);
            }

            // Create upload directory
            $uploadPath = WRITEPATH . 'uploads/assignments/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $fileName = $file->getClientName();
            $newFileName = $file->getRandomName();

            // Move file
            if ($file->move($uploadPath, $newFileName)) {
                $filePath = $uploadPath . $newFileName;
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to upload file.'
                ]);
            }
        }

        // Validate that either text or file is provided
        if (empty($submissionText) && empty($filePath)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please provide either text submission or upload a file.'
            ])->setStatusCode(400);
        }

        // Create submission
        $submissionData = [
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'submission_text' => $submissionText,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = $this->submissionModel->insert($submissionData);

            if ($result) {
                // Notify teacher
                $teacherId = $assignment['created_by'];
                $studentName = session('name');
                $message = "New submission from {$studentName} for assignment '{$assignment['title']}'.";
                $this->notificationModel->createNotification($teacherId, $message);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment submitted successfully!'
                ]);
            } else {
                // Delete uploaded file if database insert fails
                if ($filePath && file_exists($filePath)) {
                    unlink($filePath);
                }

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to submit assignment.'
                ]);
            }
        } catch (\Exception $e) {
            // Delete uploaded file if error occurs
            if ($filePath && file_exists($filePath)) {
                unlink($filePath);
            }

            log_message('error', 'Submission error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
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
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$submissionId) {
            return redirect()->back()->with('error', 'Submission ID is required.');
        }

        $studentId = session('userID');
        $submission = $this->submissionModel->find($submissionId);

        if (!$submission || $submission['student_id'] != $studentId) {
            return redirect()->back()->with('error', 'Submission not found or access denied.');
        }

        if (empty($submission['file_path']) || !file_exists($submission['file_path'])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($submission['file_path'], null)
                              ->setFileName($submission['file_name']);
    }

    /**
     * View all quizzes for enrolled courses
     */
    public function quizzes()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        $studentId = session('userID');

        // Expire old enrollments first
        $this->enrollmentModel->expireOldEnrollments();

        // Get all active and pending enrollments
        $activeEnrollments = $this->enrollmentModel->getActiveEnrollments($studentId);
        $pendingEnrollments = $this->enrollmentModel->getPendingEnrollments($studentId);
        $allEnrollments = array_merge($activeEnrollments ?? [], $pendingEnrollments ?? []);
        
        $courseIds = array_filter(array_unique(array_column($allEnrollments, 'course_id')), function($id) {
            return !empty($id);
        });

        // Get all quizzes for enrolled courses
        $quizzes = [];
        if (!empty($courseIds)) {
            $quizzes = $this->quizModel->select('quizzes.*, courses.title as course_title')
                                       ->join('courses', 'courses.id = quizzes.course_id')
                                       ->whereIn('quizzes.course_id', $courseIds)
                                       ->where('quizzes.is_published', true)
                                       ->orderBy('quizzes.created_at', 'DESC')
                                       ->findAll();
        }

        // Get submission status for each quiz
        foreach ($quizzes as &$quiz) {
            $submission = $this->quizSubmissionModel->getSubmission($quiz['id'], $studentId);
            $quiz['has_submitted'] = $submission !== null;
            $quiz['attempt_count'] = $this->quizSubmissionModel->getAttemptCount($quiz['id'], $studentId);
            $quiz['max_attempts'] = $quiz['max_attempts'] ?? 1;
            $quiz['can_attempt'] = $quiz['attempt_count'] < $quiz['max_attempts'];
        }

        $data = [
            'title' => 'My Quizzes - Student Dashboard',
            'quizzes' => $quizzes
        ];

        return view('student/quizzes', $data);
    }

    /**
     * View a specific quiz
     */
    public function viewQuiz($quizId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$quizId) {
            session()->setFlashdata('error', 'Quiz ID is required.');
            return redirect()->to('/student/quizzes');
        }

        $studentId = session('userID');
        $quiz = $this->quizModel->getQuizWithDetails($quizId);
        
        if (!$quiz) {
            session()->setFlashdata('error', 'Quiz not found.');
            return redirect()->to('/student/quizzes');
        }

        // Check if student is enrolled in the course
        $enrollment = $this->enrollmentModel->where('user_id', $studentId)
                                           ->where('course_id', $quiz['course_id'])
                                           ->whereIn('status', ['active', 'pending'])
                                           ->first();
        
        if (!$enrollment) {
            session()->setFlashdata('error', 'You are not enrolled in this course.');
            return redirect()->to('/student/quizzes');
        }

        // Get submission info
        $submission = $this->quizSubmissionModel->getSubmission($quizId, $studentId);
        $attemptCount = $this->quizSubmissionModel->getAttemptCount($quizId, $studentId);
        $canAttempt = $attemptCount < ($quiz['max_attempts'] ?? 1);

        $data = [
            'title' => 'View Quiz - Student Dashboard',
            'quiz' => $quiz,
            'submission' => $submission,
            'attemptCount' => $attemptCount,
            'canAttempt' => $canAttempt
        ];

        return view('student/view_quiz', $data);
    }

    /**
     * Take a quiz
     */
    public function takeQuiz($quizId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$quizId) {
            session()->setFlashdata('error', 'Quiz ID is required.');
            return redirect()->to('/student/quizzes');
        }

        $studentId = session('userID');
        $quiz = $this->quizModel->getQuizWithDetails($quizId);
        
        if (!$quiz) {
            session()->setFlashdata('error', 'Quiz not found.');
            return redirect()->to('/student/quizzes');
        }

        // Check if student is enrolled
        $enrollment = $this->enrollmentModel->where('user_id', $studentId)
                                           ->where('course_id', $quiz['course_id'])
                                           ->whereIn('status', ['active', 'pending'])
                                           ->first();
        
        if (!$enrollment) {
            session()->setFlashdata('error', 'You are not enrolled in this course.');
            return redirect()->to('/student/quizzes');
        }

        // Check attempt limit
        $attemptCount = $this->quizSubmissionModel->getAttemptCount($quizId, $studentId);
        if ($attemptCount >= ($quiz['max_attempts'] ?? 1)) {
            session()->setFlashdata('error', 'You have reached the maximum number of attempts for this quiz.');
            return redirect()->to('/student/view-quiz/' . $quizId);
        }

        // Get questions
        $questions = $this->quizQuestionModel->getQuestionsByQuiz($quizId, $quiz['randomize_questions'] ?? false);
        
        if (empty($questions)) {
            session()->setFlashdata('error', 'This quiz has no questions yet.');
            return redirect()->to('/student/view-quiz/' . $quizId);
        }

        // Create or get in-progress submission
        $submission = $this->quizSubmissionModel->where('quiz_id', $quizId)
                                                ->where('user_id', $studentId)
                                                ->where('status', 'in_progress')
                                                ->first();

        if (!$submission) {
            $nextAttempt = $this->quizSubmissionModel->getNextAttemptNumber($quizId, $studentId);
            $submissionData = [
                'user_id' => $studentId,
                'quiz_id' => $quizId,
                'attempt_number' => $nextAttempt,
                'total_questions' => count($questions),
                'status' => 'in_progress',
                'started_at' => date('Y-m-d H:i:s'),
            ];
            $submissionId = $this->quizSubmissionModel->insert($submissionData);
            $submission = $this->quizSubmissionModel->find($submissionId);
        }

        // Decode options for questions
        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
        }

        $data = [
            'title' => 'Take Quiz - Student Dashboard',
            'quiz' => $quiz,
            'questions' => $questions,
            'submission' => $submission
        ];

        return view('student/take_quiz', $data);
    }

    /**
     * Submit quiz answers
     */
    public function submitQuiz($submissionId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$submissionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Submission ID is required.'
            ])->setStatusCode(400);
        }

        $studentId = session('userID');
        $submission = $this->quizSubmissionModel->find($submissionId);

        if (!$submission || $submission['user_id'] != $studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Submission not found or access denied.'
            ])->setStatusCode(403);
        }

        if ($submission['status'] === 'completed') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This quiz has already been submitted.'
            ])->setStatusCode(400);
        }

        // Get quiz and questions
        $quiz = $this->quizModel->find($submission['quiz_id']);
        $questions = $this->quizQuestionModel->getQuestionsByQuiz($submission['quiz_id']);

        // Get answers from POST
        $answers = $this->request->getPost('answers');
        $timeTaken = $this->request->getPost('time_taken');

        if (empty($answers) || !is_array($answers)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No answers provided.'
            ])->setStatusCode(400);
        }

        // Grade the quiz
        $correctAnswers = 0;
        $totalQuestions = count($questions);
        $gradedAnswers = [];

        foreach ($questions as $question) {
            $questionId = $question['id'];
            $studentAnswer = $answers[$questionId] ?? null;
            $isCorrect = false;

            // Check answer based on question type
            if ($question['question_type'] === 'multiple_choice') {
                $isCorrect = ($studentAnswer == $question['correct_answer']);
            } elseif ($question['question_type'] === 'true_false') {
                $isCorrect = (strtolower($studentAnswer) == strtolower($question['correct_answer']));
            } elseif ($question['question_type'] === 'short_answer') {
                // For short answer, do case-insensitive comparison
                $isCorrect = (strtolower(trim($studentAnswer)) == strtolower(trim($question['correct_answer'])));
            }

            if ($isCorrect) {
                $correctAnswers++;
            }

            $gradedAnswers[$questionId] = [
                'student_answer' => $studentAnswer,
                'correct_answer' => $question['correct_answer'],
                'is_correct' => $isCorrect,
                'points' => $isCorrect ? $question['points'] : 0
            ];
        }

        // Calculate score percentage
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // Update submission
        $updateData = [
            'submission_data' => json_encode($gradedAnswers),
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'time_taken' => !empty($timeTaken) ? (int)$timeTaken : null,
            'status' => 'completed',
            'submitted_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->quizSubmissionModel->update($submissionId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Quiz submitted successfully!',
                'score' => round($score, 2),
                'correct' => $correctAnswers,
                'total' => $totalQuestions,
                'redirect' => base_url('student/quiz-result/' . $submissionId)
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to submit quiz.'
            ])->setStatusCode(500);
        }
    }

    /**
     * View quiz result
     */
    public function viewQuizResult($submissionId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'student') {
            return redirect()->to('/dashboard');
        }

        if (!$submissionId) {
            session()->setFlashdata('error', 'Submission ID is required.');
            return redirect()->to('/student/quizzes');
        }

        $studentId = session('userID');
        $submission = $this->quizSubmissionModel->find($submissionId);

        if (!$submission || $submission['user_id'] != $studentId) {
            session()->setFlashdata('error', 'Submission not found or access denied.');
            return redirect()->to('/student/quizzes');
        }

        $quiz = $this->quizModel->getQuizWithDetails($submission['quiz_id']);
        $questions = $this->quizQuestionModel->getQuestionsByQuiz($submission['quiz_id']);
        $submissionData = json_decode($submission['submission_data'], true);

        // Decode options for questions
        foreach ($questions as &$question) {
            if (!empty($question['options'])) {
                $question['options'] = json_decode($question['options'], true);
            }
            // Add student answer and grading info
            if (!empty($submissionData[$question['id']])) {
                $question['student_answer'] = $submissionData[$question['id']]['student_answer'] ?? null;
                $question['correct_answer'] = $submissionData[$question['id']]['correct_answer'] ?? null;
                $question['is_correct'] = $submissionData[$question['id']]['is_correct'] ?? false;
            }
        }

        $data = [
            'title' => 'Quiz Result - Student Dashboard',
            'quiz' => $quiz,
            'submission' => $submission,
            'questions' => $questions,
            'showCorrectAnswers' => $quiz['show_correct_answers'] ?? true
        ];

        return view('student/quiz_result', $data);
    }
}

