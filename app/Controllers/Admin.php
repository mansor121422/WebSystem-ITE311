<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CourseModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $courseModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
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

        // Get all courses for materials management
        $courses = $this->courseModel->orderBy('title', 'ASC')->findAll();
        
        // Prepare data for view
        $data = [
            'title' => 'Admin Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ],
            'courses' => $courses
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
        
        // Get all active (non-deleted) users
        $query = $this->userModel->orderBy('created_at', 'DESC');
        
        // Filter users by role if provided
        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }
        
        $users = $query->findAll();

        // Get deleted users separately
        $deletedUsersQuery = $this->userModel->getDeletedUsers();
        if (!empty($roleFilter)) {
            $deletedUsers = array_filter($deletedUsersQuery, function($user) use ($roleFilter) {
                return $user['role'] === $roleFilter;
            });
        } else {
            $deletedUsers = $deletedUsersQuery;
        }

        $data = [
            'title' => 'User Management - Admin',
            'users' => $users,
            'deletedUsers' => $deletedUsers,
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
     * Delete user (soft delete)
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
            // Soft delete - CodeIgniter will automatically set deleted_at
            if ($this->userModel->delete($id)) {
                session()->setFlashdata('success', 'User deleted successfully! You can restore them later if needed.');
            } else {
                session()->setFlashdata('error', 'Failed to delete user.');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error deleting user: ' . $e->getMessage());
        }

        return redirect()->to('/admin/users');
    }

    /**
     * Restore deleted user
     */
    public function restoreUser($id = null)
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

        // Get user with deleted records
        $user = $this->userModel->withDeleted()->find($id);
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to('/admin/users');
        }

        // Check if user is actually deleted
        if (empty($user['deleted_at'])) {
            session()->setFlashdata('error', 'User is not deleted.');
            return redirect()->to('/admin/users');
        }

        try {
            if ($this->userModel->restoreUser($id)) {
                session()->setFlashdata('success', 'User restored successfully!');
            } else {
                session()->setFlashdata('error', 'Failed to restore user.');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error restoring user: ' . $e->getMessage());
        }

        return redirect()->to('/admin/users');
    }

    /**
     * Create new course
     */
    public function createCourse()
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

        helper('form');

        if ($this->request->getMethod() === 'POST') {
            // Get form data
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $instructorId = $this->request->getPost('instructor_id');
            $courseCode = $this->request->getPost('course_code');
            $schoolYear = $this->request->getPost('school_year');
            $semester = $this->request->getPost('semester');
            $scheduleDay = $this->request->getPost('schedule_day');
            $scheduleTimeStart = $this->request->getPost('schedule_time_start');
            $scheduleTimeEnd = $this->request->getPost('schedule_time_end');
            $maxStudents = $this->request->getPost('max_students');

            // Validate required fields
            if (empty($title)) {
                session()->setFlashdata('error', 'Course title is required.');
                return redirect()->back()->withInput();
            }

            // Validate school year (required)
            if (empty($schoolYear)) {
                session()->setFlashdata('error', 'School year is required.');
                return redirect()->back()->withInput();
            }

            // Validate semester (required)
            if (empty($semester)) {
                session()->setFlashdata('error', 'Semester is required.');
                return redirect()->back()->withInput();
            }

            // Validate title length
            if (strlen($title) < 3 || strlen($title) > 200) {
                session()->setFlashdata('error', 'Course title must be between 3 and 200 characters.');
                return redirect()->back()->withInput();
            }

            // Validate instructor if provided
            if (!empty($instructorId)) {
                $instructor = $this->userModel->find($instructorId);
                if (!$instructor || $instructor['role'] !== 'teacher') {
                    session()->setFlashdata('error', 'Invalid instructor selected.');
                    return redirect()->back()->withInput();
                }
            }

            // Validate semester value
            if (!in_array($semester, ['1st', '2nd', 'Summer'])) {
                session()->setFlashdata('error', 'Invalid semester selected.');
                return redirect()->back()->withInput();
            }

            // Validate schedule day if provided
            if (!empty($scheduleDay) && !in_array($scheduleDay, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])) {
                session()->setFlashdata('error', 'Invalid schedule day selected.');
                return redirect()->back()->withInput();
            }

            // Validate time format if provided
            if (!empty($scheduleTimeStart) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $scheduleTimeStart)) {
                session()->setFlashdata('error', 'Invalid start time format. Use HH:MM format.');
                return redirect()->back()->withInput();
            }

            if (!empty($scheduleTimeEnd) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $scheduleTimeEnd)) {
                session()->setFlashdata('error', 'Invalid end time format. Use HH:MM format.');
                return redirect()->back()->withInput();
            }

            // Validate time range
            if (!empty($scheduleTimeStart) && !empty($scheduleTimeEnd)) {
                if (strtotime($scheduleTimeStart) >= strtotime($scheduleTimeEnd)) {
                    session()->setFlashdata('error', 'End time must be after start time.');
                    return redirect()->back()->withInput();
                }
            }

            // Check for duplicate course (if course_code, school_year, and semester are provided)
            if (!empty($courseCode) && !empty($schoolYear) && !empty($semester)) {
                $duplicate = $this->courseModel->checkDuplicateCourse($courseCode, $title, $schoolYear, $semester);
                if ($duplicate) {
                    session()->setFlashdata('error', 'A course with the same code, title, school year, and semester already exists.');
                    return redirect()->back()->withInput();
                }
            }

            // Check for schedule conflicts (if schedule information is provided)
            if (!empty($scheduleDay) && !empty($scheduleTimeStart) && !empty($scheduleTimeEnd) && !empty($schoolYear) && !empty($semester)) {
                // Check each selected day for conflicts
                $daysArray = explode(',', $scheduleDay);
                foreach ($daysArray as $day) {
                    $day = trim($day);
                    $conflicts = $this->courseModel->checkScheduleConflict($day, $scheduleTimeStart, $scheduleTimeEnd, $schoolYear, $semester);
                    if (!empty($conflicts)) {
                        session()->setFlashdata('error', 'Schedule conflict detected on ' . $day . '. Another course has the same schedule for this school year and semester.');
                        return redirect()->back()->withInput();
                    }
                }
            }

            // Prepare course data
            $courseData = [
                'title' => $title,
                'description' => $description ?? null,
                'instructor_id' => !empty($instructorId) ? $instructorId : null,
                'course_code' => !empty($courseCode) ? $courseCode : null,
                'school_year' => !empty($schoolYear) ? $schoolYear : null,
                'semester' => !empty($semester) ? $semester : null,
                'schedule_day' => !empty($scheduleDay) ? $scheduleDay : null,
                'schedule_time_start' => !empty($scheduleTimeStart) ? $scheduleTimeStart : null,
                'schedule_time_end' => !empty($scheduleTimeEnd) ? $scheduleTimeEnd : null,
            ];

            try {
                $result = $this->courseModel->insert($courseData);
                
                if ($result) {
                    session()->setFlashdata('success', 'Course created successfully!');
                    return redirect()->to('/courses');
                } else {
                    session()->setFlashdata('error', 'Failed to create course.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error creating course: ' . $e->getMessage());
            }
        }

        return redirect()->to('/courses');
    }

    /**
     * Edit course
     */
    public function editCourse($id = null)
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
            session()->setFlashdata('error', 'Course ID is required.');
            return redirect()->to('/courses');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('/courses');
        }

        // Get all teachers for instructor selection
        $teachers = $this->userModel->where('role', 'teacher')->findAll();

        $data = [
            'title' => 'Edit Course - Admin',
            'course' => $course,
            'teachers' => $teachers
        ];

        return view('admin/edit_course', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id = null)
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
            session()->setFlashdata('error', 'Course ID is required.');
            return redirect()->to('/courses');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('/courses');
        }

        helper('form');

        if ($this->request->getMethod() === 'POST') {
            // Get form data
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $instructorId = $this->request->getPost('instructor_id');
            $courseCode = $this->request->getPost('course_code');
            $schoolYear = $this->request->getPost('school_year');
            $semester = $this->request->getPost('semester');
            $scheduleDays = $this->request->getPost('schedule_day'); // Array of days
            $scheduleTimeStart = $this->request->getPost('schedule_time_start');
            $scheduleTimeEnd = $this->request->getPost('schedule_time_end');
            $maxStudents = $this->request->getPost('max_students');
            
            // Convert schedule days array to comma-separated string
            $scheduleDay = null;
            if (!empty($scheduleDays) && is_array($scheduleDays)) {
                $scheduleDay = implode(',', array_filter($scheduleDays));
            }

            // Validate required fields
            if (empty($title)) {
                session()->setFlashdata('error', 'Course title is required.');
                return redirect()->back()->withInput();
            }

            // Validate school year (required)
            if (empty($schoolYear)) {
                session()->setFlashdata('error', 'School year is required.');
                return redirect()->back()->withInput();
            }

            // Validate semester (required)
            if (empty($semester)) {
                session()->setFlashdata('error', 'Semester is required.');
                return redirect()->back()->withInput();
            }

            // Validate title length
            if (strlen($title) < 3 || strlen($title) > 200) {
                session()->setFlashdata('error', 'Course title must be between 3 and 200 characters.');
                return redirect()->back()->withInput();
            }

            // Validate instructor if provided
            if (!empty($instructorId)) {
                $instructor = $this->userModel->find($instructorId);
                if (!$instructor || $instructor['role'] !== 'teacher') {
                    session()->setFlashdata('error', 'Invalid instructor selected.');
                    return redirect()->back()->withInput();
                }
            }

            // Validate semester value
            if (!in_array($semester, ['1st', '2nd', 'Summer'])) {
                session()->setFlashdata('error', 'Invalid semester selected.');
                return redirect()->back()->withInput();
            }

            // Validate schedule days if provided
            if (!empty($scheduleDays) && is_array($scheduleDays)) {
                $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($scheduleDays as $day) {
                    if (!in_array($day, $validDays)) {
                        session()->setFlashdata('error', 'Invalid schedule day selected: ' . $day);
                        return redirect()->back()->withInput();
                    }
                }
            }

            // Validate time format if provided
            if (!empty($scheduleTimeStart) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $scheduleTimeStart)) {
                session()->setFlashdata('error', 'Invalid start time format. Use HH:MM format.');
                return redirect()->back()->withInput();
            }

            if (!empty($scheduleTimeEnd) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $scheduleTimeEnd)) {
                session()->setFlashdata('error', 'Invalid end time format. Use HH:MM format.');
                return redirect()->back()->withInput();
            }

            // Validate time range
            if (!empty($scheduleTimeStart) && !empty($scheduleTimeEnd)) {
                if (strtotime($scheduleTimeStart) >= strtotime($scheduleTimeEnd)) {
                    session()->setFlashdata('error', 'End time must be after start time.');
                    return redirect()->back()->withInput();
                }
            }

            // Check for duplicate course (if course_code, school_year, and semester are provided)
            if (!empty($courseCode) && !empty($schoolYear) && !empty($semester)) {
                $duplicate = $this->courseModel->checkDuplicateCourse($courseCode, $title, $schoolYear, $semester, $id);
                if ($duplicate) {
                    session()->setFlashdata('error', 'A course with the same code, title, school year, and semester already exists.');
                    return redirect()->back()->withInput();
                }
            }

            // Check for schedule conflicts (if schedule information is provided)
            if (!empty($scheduleDay) && !empty($scheduleTimeStart) && !empty($scheduleTimeEnd) && !empty($schoolYear) && !empty($semester)) {
                // Check each selected day for conflicts
                $daysArray = explode(',', $scheduleDay);
                foreach ($daysArray as $day) {
                    $day = trim($day);
                    $conflicts = $this->courseModel->checkScheduleConflict($day, $scheduleTimeStart, $scheduleTimeEnd, $schoolYear, $semester, $id);
                    if (!empty($conflicts)) {
                        session()->setFlashdata('error', 'Schedule conflict detected on ' . $day . '. Another course has the same schedule for this school year and semester.');
                        return redirect()->back()->withInput();
                    }
                }
            }

            // Validate max_students if provided
            if (!empty($maxStudents)) {
                $maxStudents = (int) $maxStudents;
                if ($maxStudents < 1 || $maxStudents > 1000) {
                    session()->setFlashdata('error', 'Maximum students must be between 1 and 1000.');
                    return redirect()->back()->withInput();
                }
            }

            // Prepare course data
            $courseData = [
                'title' => $title,
                'description' => $description ?? null,
                'instructor_id' => !empty($instructorId) ? $instructorId : null,
                'course_code' => !empty($courseCode) ? $courseCode : null,
                'school_year' => !empty($schoolYear) ? $schoolYear : null,
                'semester' => !empty($semester) ? $semester : null,
                'schedule_day' => !empty($scheduleDay) ? $scheduleDay : null,
                'schedule_time_start' => !empty($scheduleTimeStart) ? $scheduleTimeStart : null,
                'schedule_time_end' => !empty($scheduleTimeEnd) ? $scheduleTimeEnd : null,
                'max_students' => !empty($maxStudents) ? $maxStudents : null,
            ];

            try {
                if ($this->courseModel->update($id, $courseData)) {
                    session()->setFlashdata('success', 'Course updated successfully!');
                    return redirect()->to('/courses');
                } else {
                    session()->setFlashdata('error', 'Failed to update course.');
                }
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Error updating course: ' . $e->getMessage());
            }
        }

        // Get all teachers for instructor selection
        $teachers = $this->userModel->where('role', 'teacher')->findAll();

        $data = [
            'title' => 'Edit Course - Admin',
            'course' => $course,
            'teachers' => $teachers
        ];

        return view('admin/edit_course', $data);
    }

    /**
     * Delete course
     */
    public function deleteCourse($id = null)
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You must be logged in to delete courses.'
                ])->setStatusCode(401);
            }
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied. Administrators only.'
                ])->setStatusCode(403);
            }
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course ID is required.'
                ])->setStatusCode(400);
            }
            session()->setFlashdata('error', 'Course ID is required.');
            return redirect()->to('/courses');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course not found.'
                ])->setStatusCode(404);
            }
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('/courses');
        }

        // Check if course has enrollments
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->where('course_id', $id)->countAllResults();
        
        if ($enrollments > 0) {
            $message = 'Cannot delete course. There are ' . $enrollments . ' enrollment(s) for this course.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ])->setStatusCode(400);
            }
            session()->setFlashdata('error', $message);
            return redirect()->to('/courses');
        }

        try {
            if ($this->courseModel->delete($id)) {
                $message = 'Course deleted successfully!';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message
                    ]);
                }
                session()->setFlashdata('success', $message);
            } else {
                $message = 'Failed to delete course.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $message
                    ])->setStatusCode(500);
                }
                session()->setFlashdata('error', $message);
            }
        } catch (\Exception $e) {
            $message = 'Error deleting course: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ])->setStatusCode(500);
            }
            session()->setFlashdata('error', $message);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unexpected error occurred.'
            ])->setStatusCode(500);
        }
        return redirect()->to('/courses');
    }
}

