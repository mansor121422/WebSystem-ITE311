<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Materials extends BaseController
{
    protected $materialModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Display the file upload form and handle the file upload process
     * 
     * @param int $course_id
     * @return mixed
     */
    public function upload($course_id = null)
    {
        // Check if user is logged in and has appropriate role (admin/teacher)
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        $userRole = session()->get('role');
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to upload materials.');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->handleUpload();
        }

        // Display upload form
        $data = [
            'title' => 'Upload Material',
            'course_id' => $course_id
        ];

        return view('materials/upload', $data);
    }

    /**
     * Handle the file upload process
     * 
     * @return RedirectResponse
     */
    protected function handleUpload()
    {
        $courseId = $this->request->getPost('course_id');
        
        // Validate the input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_id' => 'required|numeric',
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,doc,docx,ppt,pptx,xls,xlsx,zip,txt]',
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Handle file upload
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getClientName();
            $newFileName = $file->getRandomName();
            
            // Create upload directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/materials/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Move file to upload directory
            $file->move($uploadPath, $newFileName);

            // Save file info to database
            $data = [
                'course_id' => $courseId,
                'file_name' => $fileName,
                'file_path' => $uploadPath . $newFileName,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->materialModel->insertMaterial($data)) {
                // Step 7: Create notifications for students enrolled in this course
                try {
                    $this->notifyStudentsOfNewMaterial($courseId, $fileName);
                } catch (\Exception $e) {
                    // Don't fail the upload if notification creation fails
                    log_message('error', "Failed to create material notifications: " . $e->getMessage());
                }
                
                return redirect()->back()->with('success', 'Material uploaded successfully!');
            } else {
                // Delete uploaded file if database insert fails
                unlink($uploadPath . $newFileName);
                return redirect()->back()->with('error', 'Failed to save material information.');
            }
        } else {
            return redirect()->back()->with('error', 'Failed to upload file.');
        }
    }

    /**
     * Delete a material record and the associated file
     * 
     * @param int $material_id
     * @return RedirectResponse
     */
    public function delete($material_id = null)
    {
        // Check if user is logged in and has appropriate role (admin/teacher)
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue.'
            ]);
        }

        $userRole = session()->get('role');
        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to delete materials.'
            ]);
        }

        if (!$material_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Material ID is required.'
            ]);
        }

        // Get material record
        $material = $this->materialModel->find($material_id);

        if (!$material) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Material not found.'
            ]);
        }

        // Delete the physical file
        if (file_exists($material['file_path'])) {
            unlink($material['file_path']);
        }

        // Delete the database record
        if ($this->materialModel->delete($material_id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Material deleted successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete material.'
            ]);
        }
    }

    /**
     * Handle file download for enrolled students
     * 
     * @param int $material_id
     * @return ResponseInterface|RedirectResponse
     */
    public function download($material_id = null)
    {
        // Step 1: Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        // Step 2: Validate material ID
        if (!$material_id) {
            return redirect()->back()->with('error', 'Material ID is required.');
        }

        // Step 3: Retrieve the file path from database using material_id
        $material = $this->materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Get user information
        $userId = session()->get('userID');
        $userRole = strtolower(session()->get('role'));

        // Step 4: Check enrollment and permissions
        // Admin and Teacher can download any material without enrollment check
        if (!in_array($userRole, ['admin', 'teacher'])) {
            // For students, verify they are enrolled in the course
            if ($userRole === 'student') {
                $db = \Config\Database::connect();
                
                // Check if student is enrolled in the course associated with this material
                $enrollmentQuery = $db->query("
                    SELECT id 
                    FROM enrollments 
                    WHERE user_id = ? AND course_id = ? AND status = 'active'
                    LIMIT 1
                ", [$userId, $material['course_id']]);
                
                $enrollment = $enrollmentQuery->getRowArray();
                
                if (!$enrollment) {
                    return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
                }
            } else {
                // Unknown role - deny access
                return redirect()->back()->with('error', 'You do not have permission to download this material.');
            }
        }

        // Step 5: Check if file exists on server
        if (!file_exists($material['file_path'])) {
            log_message('error', "Material file not found: {$material['file_path']} (Material ID: {$material_id})");
            return redirect()->back()->with('error', 'File not found on server. Please contact the administrator.');
        }

        // Step 6: Use CodeIgniter's Response class to force secure file download
        try {
            return $this->response->download($material['file_path'], null)
                                  ->setFileName($material['file_name']);
        } catch (\Exception $e) {
            log_message('error', "Download failed for material {$material_id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download file. Please try again.');
        }
    }

    /**
     * Display materials for a specific course
     * 
     * @param int $course_id
     * @return mixed
     */
    public function viewCourseMaterials($course_id = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        if (!$course_id) {
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        // TODO: Add enrollment check - verify if user is enrolled in the course (for students)
        // For now, allow all logged-in users to view materials
        
        // Get materials for the course
        $materials = $this->materialModel->getMaterialsByCourse($course_id);

        // Get course information (if CourseModel exists)
        $courseTitle = "Course #" . $course_id;
        if (class_exists('\App\Models\CourseModel')) {
            $courseModel = new \App\Models\CourseModel();
            $course = $courseModel->find($course_id);
            if ($course) {
                $courseTitle = $course['title'];
            }
        }

        $data = [
            'title' => 'Course Materials',
            'course_id' => $course_id,
            'course_title' => $courseTitle,
            'materials' => $materials
        ];

        return view('materials/view', $data);
    }

    /**
     * Step 7: Notify all students enrolled in a course about new material
     * 
     * @param int $courseId
     * @param string $fileName
     * @return void
     */
    protected function notifyStudentsOfNewMaterial($courseId, $fileName)
    {
        // Get course title (using hardcoded data for now)
        $courseData = [
            1 => 'Introduction to Programming',
            2 => 'Web Development Basics',
            3 => 'Database Management',
            4 => 'Data Structures & Algorithms'
        ];
        
        $courseTitle = $courseData[$courseId] ?? "Course #$courseId";
        
        // Get all students enrolled in this course
        $db = \Config\Database::connect();
        $enrolledStudents = $db->query("
            SELECT DISTINCT user_id 
            FROM enrollments 
            WHERE course_id = ? AND status = 'active'
        ", [$courseId])->getResultArray();
        
        if (empty($enrolledStudents)) {
            log_message('info', "No students enrolled in course {$courseId} to notify");
            return;
        }
        
        $notificationMessage = "New material '{$fileName}' has been uploaded to your course '{$courseTitle}'. Check it out now!";
        
        $notificationsCreated = 0;
        foreach ($enrolledStudents as $student) {
            try {
                $result = $this->notificationModel->createNotification($student['user_id'], $notificationMessage);
                if ($result) {
                    $notificationsCreated++;
                }
            } catch (\Exception $e) {
                log_message('error', "Failed to create notification for user {$student['user_id']}: " . $e->getMessage());
            }
        }
        
        log_message('info', "Created {$notificationsCreated} material notifications for course {$courseId}");
    }
}

