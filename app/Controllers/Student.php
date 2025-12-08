<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Student extends BaseController
{
    protected $assignmentModel;
    protected $submissionModel;
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
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
        $enrollments = $this->enrollmentModel->getActiveEnrollments($studentId);

        $courseIds = array_column($enrollments, 'course_id');

        // Get all assignments for enrolled courses
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
}

