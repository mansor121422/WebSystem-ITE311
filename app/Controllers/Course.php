<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->userModel = new UserModel();
    }

    /**
     * Handle course enrollment via AJAX
     * 
     * @return ResponseInterface JSON response
     */
    public function enroll()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.',
                'error_code' => 'NOT_LOGGED_IN'
            ])->setStatusCode(401);
        }

        // Check if request method is POST
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method. Only POST requests are allowed.',
                'error_code' => 'INVALID_METHOD'
            ])->setStatusCode(405);
        }

        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');

        // Validate course_id
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID provided.',
                'error_code' => 'INVALID_COURSE_ID'
            ])->setStatusCode(400);
        }

        // Get current user ID from session
        $user_id = session()->get('userID');

        // Validate user ID
        if (empty($user_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User session invalid. Please login again.',
                'error_code' => 'INVALID_USER_SESSION'
            ])->setStatusCode(401);
        }

        // Check if user is already enrolled in this course
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.',
                'error_code' => 'ALREADY_ENROLLED'
            ])->setStatusCode(409);
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'progress' => 0.00
        ];

        try {
            // Attempt to enroll the user
            $result = $this->enrollmentModel->enrollUser($enrollmentData);

            if ($result) {
                // Get user name for success message
                $user = $this->userModel->findById($user_id);
                $userName = $user['name'] ?? 'User';

                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Congratulations {$userName}! You have been successfully enrolled in the course.",
                    'enrollment_id' => $result,
                    'enrollment_date' => $enrollmentData['enrollment_date']
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to enroll in the course. Please try again.',
                    'error_code' => 'ENROLLMENT_FAILED'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            log_message('error', 'Course enrollment error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing your enrollment. Please try again later.',
                'error_code' => 'SYSTEM_ERROR'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get user's enrolled courses
     * 
     * @return ResponseInterface JSON response
     */
    public function getUserCourses()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view your courses.',
                'error_code' => 'NOT_LOGGED_IN'
            ])->setStatusCode(401);
        }

        $user_id = session()->get('userID');

        try {
            $enrollments = $this->enrollmentModel->getUserEnrollments($user_id);

            return $this->response->setJSON([
                'success' => true,
                'data' => $enrollments,
                'count' => count($enrollments)
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Get user courses error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve your courses.',
                'error_code' => 'SYSTEM_ERROR'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update enrollment progress
     * 
     * @return ResponseInterface JSON response
     */
    public function updateProgress()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to update progress.',
                'error_code' => 'NOT_LOGGED_IN'
            ])->setStatusCode(401);
        }

        // Check if request method is POST
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.',
                'error_code' => 'INVALID_METHOD'
            ])->setStatusCode(405);
        }

        $user_id = session()->get('userID');
        $course_id = $this->request->getPost('course_id');
        $progress = $this->request->getPost('progress');

        // Validate inputs
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.',
                'error_code' => 'INVALID_COURSE_ID'
            ])->setStatusCode(400);
        }

        if (!is_numeric($progress) || $progress < 0 || $progress > 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Progress must be a number between 0 and 100.',
                'error_code' => 'INVALID_PROGRESS'
            ])->setStatusCode(400);
        }

        try {
            $result = $this->enrollmentModel->updateEnrollmentProgress($user_id, $course_id, $progress);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Progress updated successfully.',
                    'progress' => $progress
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update progress.',
                    'error_code' => 'UPDATE_FAILED'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Update progress error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating progress.',
                'error_code' => 'SYSTEM_ERROR'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get course enrollment statistics
     * 
     * @return ResponseInterface JSON response
     */
    public function getStats()
    {
        // Set JSON response header
        $this->response->setContentType('application/json');

        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to view statistics.',
                'error_code' => 'NOT_LOGGED_IN'
            ])->setStatusCode(401);
        }

        $user_id = session()->get('userID');

        try {
            $stats = $this->enrollmentModel->getEnrollmentStats($user_id);

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Get stats error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve statistics.',
                'error_code' => 'SYSTEM_ERROR'
            ])->setStatusCode(500);
        }
    }
}
