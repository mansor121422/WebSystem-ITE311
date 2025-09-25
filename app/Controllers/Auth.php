<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]'
            ];
            
            if ($this->validate($rules)) {
                // Split the name into first_name and last_name
                $nameParts = explode(' ', trim($this->request->getPost('name')), 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                
                // Generate username from email
                $email = $this->request->getPost('email');
                $username = explode('@', $email)[0];
                
                $data = [
                    'username' => $username,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student',
                    'status' => 'active'
                ];
                
                // Save user to database
                if ($model->insert($data)) {
                    $session->setFlashdata('success', 'Registration successful. Please login.');
                    return redirect()->to('/login');
                } else {
                    // Get the last error for debugging
                    $errors = $model->errors();
                    $errorMessage = 'Registration failed. ';
                    if (!empty($errors)) {
                        $errorMessage .= implode(', ', $errors);
                    } else {
                        $errorMessage .= 'Please try again.';
                    }
                    $session->setFlashdata('error', $errorMessage);
                }
            }
        }
        
        echo view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];
            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                $user = $model->where('email', $email)->first();
                if ($user && password_verify($password, $user['password'])) {
                    // Create full name from first_name and last_name
                    $fullName = trim($user['first_name'] . ' ' . $user['last_name']);
                    
                    $session->set([
                        'user_id' => $user['id'],
                        'user_name' => $fullName,
                        'user_email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => true
                    ]);
                    $session->setFlashdata('success', 'Welcome, ' . $fullName . '!');
                    return redirect()->to('/dashboard');
                } else {
                    $session->setFlashdata('error', 'Invalid login credentials.');
                }
            }
        }
        echo view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        echo view('dashboard');
    }
}
