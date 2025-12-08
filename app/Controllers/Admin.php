<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Admin Dashboard
     * Task 3: Display admin-specific dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to access the dashboard.');
            return redirect()->to('/login');
        }

        // Check if user is an admin
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        // Prepare data for view
        $data = [
            'title' => 'Admin Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ]
        ];

        // Load admin dashboard view
        return view('admin_dashboard', $data);
    }

    /**
     * User Management - List all users
     */
    public function users()
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        // Get role filter if any
        $roleFilter = $this->request->getGet('role');
        
        // Get all users
        $query = $this->userModel->orderBy('created_at', 'DESC');
        
        // Filter users by role if provided
        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }
        
        $users = $query->findAll();

        $data = [
            'title' => 'User Management - Admin',
            'users' => $users,
            'roleFilter' => $roleFilter,
            'currentUserId' => session('userID')
        ];

        return view('admin/users', $data);
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        // Load form helper for set_value()
        helper('form');

        if ($this->request->getMethod() === 'POST') {
            // Get form data
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $role = $this->request->getPost('role');

            // Validate required fields
            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                session()->setFlashdata('error', 'All fields are required.');
                return redirect()->back()->withInput();
            }

            // 1. Name Field Validation - Check for invalid characters
            if (preg_match('/[\/\\\\"\'<>{}[\]!@#$%\^&*]/', $name)) {
                session()->setFlashdata('error', '🔴 Name contains invalid characters.');
                return redirect()->back()->withInput();
            }
            
            // Additional check: Name should contain at least one letter
            if (!preg_match('/[a-zA-Z]/', $name)) {
                session()->setFlashdata('error', '🔴 Please enter a valid name (letters and spaces only).');
                return redirect()->back()->withInput();
            }

            // 2. Email Field Validation
            if (preg_match('/[\/\'"\\\\,;<>]/', $email)) {
                session()->setFlashdata('error', '🔴 Please enter a valid email address.');
                return redirect()->back()->withInput();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                session()->setFlashdata('error', '🔴 Invalid email format.');
                return redirect()->back()->withInput();
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                session()->setFlashdata('error', 'Email already exists. Please use a different email.');
                return redirect()->back()->withInput();
            }

            // 3. Password Field Validation
            if (preg_match('/["\'\\\\\/<>]/', $password)) {
                session()->setFlashdata('error', '🔴 Password must not include special characters like / \ " \' < >');
                return redirect()->back()->withInput();
            }

            if (strlen($password) < 8) {
                session()->setFlashdata('error', '🔴 Password must be at least 8 characters and contain letters and numbers.');
                return redirect()->back()->withInput();
            }

            if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                session()->setFlashdata('error', '🔴 Password must be at least 8 characters and contain letters and numbers.');
                return redirect()->back()->withInput();
            }

            // Validate role
            if (!in_array($role, ['admin', 'teacher', 'student'])) {
                session()->setFlashdata('error', 'Invalid role selected.');
                return redirect()->back()->withInput();
            }

            // Hash password and create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            try {
                $result = $this->userModel->insert($userData);
                
                if ($result) {
                    session()->setFlashdata('success', 'User created successfully!');
                    return redirect()->to('/admin/users');
                } else {
                    session()->setFlashdata('error', 'Failed to create user.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error creating user: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => 'Create User - Admin'
        ];

        return view('admin/create_user', $data);
    }

    /**
     * Edit user
     */
    public function editUser($id = null)
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        // Load form helper for set_value()
        helper('form');

        if (!$id) {
            session()->setFlashdata('error', 'User ID is required.');
            return redirect()->to('/admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to('/admin/users');
        }

        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $role = $this->request->getPost('role');

            // Validate required fields
            if (empty($name) || empty($email) || empty($role)) {
                session()->setFlashdata('error', 'Name, email, and role are required.');
                return redirect()->back()->withInput();
            }

            // 1. Name Field Validation
            if (preg_match('/[\/\\\\"\'<>{}[\]!@#$%\^&*]/', $name)) {
                session()->setFlashdata('error', '🔴 Name contains invalid characters.');
                return redirect()->back()->withInput();
            }
            
            if (!preg_match('/[a-zA-Z]/', $name)) {
                session()->setFlashdata('error', '🔴 Please enter a valid name (letters and spaces only).');
                return redirect()->back()->withInput();
            }

            // 2. Email Field Validation
            if (preg_match('/[\/\'"\\\\,;<>]/', $email)) {
                session()->setFlashdata('error', '🔴 Please enter a valid email address.');
                return redirect()->back()->withInput();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                session()->setFlashdata('error', '🔴 Invalid email format.');
                return redirect()->back()->withInput();
            }

            // Check if email already exists (excluding current user)
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $id) {
                session()->setFlashdata('error', 'Email already exists. Please use a different email.');
                return redirect()->back()->withInput();
            }

            // 3. Password Field Validation (only if password is provided)
            if (!empty($password)) {
                if (preg_match('/["\'\\\\\/<>]/', $password)) {
                    session()->setFlashdata('error', '🔴 Password must not include special characters like / \ " \' < >');
                    return redirect()->back()->withInput();
                }

                if (strlen($password) < 8) {
                    session()->setFlashdata('error', '🔴 Password must be at least 8 characters and contain letters and numbers.');
                    return redirect()->back()->withInput();
                }

                if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                    session()->setFlashdata('error', '🔴 Password must be at least 8 characters and contain letters and numbers.');
                    return redirect()->back()->withInput();
                }
            }

            // Validate role
            if (!in_array($role, ['admin', 'teacher', 'student'])) {
                session()->setFlashdata('error', 'Invalid role selected.');
                return redirect()->back()->withInput();
            }

            // Prepare update data
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password only if provided
            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            try {
                if ($this->userModel->update($id, $updateData)) {
                    session()->setFlashdata('success', 'User updated successfully!');
                    return redirect()->to('/admin/users');
                } else {
                    session()->setFlashdata('error', 'Failed to update user.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error updating user: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => 'Edit User - Admin',
            'user' => $user
        ];

        return view('admin/edit_user', $data);
    }

    /**
     * Delete user (soft delete by setting status)
     */
    public function deleteUser($id = null)
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            session()->setFlashdata('error', 'User ID is required.');
            return redirect()->to('/admin/users');
        }

        // Prevent deleting yourself
        if ($id == session('userID')) {
            session()->setFlashdata('error', 'You cannot delete your own account.');
            return redirect()->to('/admin/users');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to('/admin/users');
        }

        try {
            if ($this->userModel->delete($id)) {
                session()->setFlashdata('success', 'User deleted successfully!');
            } else {
                session()->setFlashdata('error', 'Failed to delete user.');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error deleting user: ' . $e->getMessage());
        }

        return redirect()->to('/admin/users');
    }
}

