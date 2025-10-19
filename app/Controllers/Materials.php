<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use CodeIgniter\HTTP\ResponseInterface;

class Materials extends BaseController
{
    protected $materialModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
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
        if (!session()->has('user_id')) {
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
        if (!session()->has('user_id')) {
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
        // Check if user is logged in
        if (!session()->has('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        if (!$material_id) {
            return redirect()->back()->with('error', 'Material ID is required.');
        }

        // Get material record
        $material = $this->materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // TODO: Add enrollment check - verify if user is enrolled in the course
        // For now, allow admin, teacher, and student roles to download
        $userRole = session()->get('role');
        if (!in_array($userRole, ['admin', 'teacher', 'student'])) {
            return redirect()->back()->with('error', 'You do not have permission to download this material.');
        }

        // Check if file exists
        if (!file_exists($material['file_path'])) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        // Download the file
        return $this->response->download($material['file_path'], null)->setFileName($material['file_name']);
    }
}

