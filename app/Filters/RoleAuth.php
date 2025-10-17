<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    /**
     * Task 4: Role-Based Authorization Filter
     * 
     * This filter checks if the user has the appropriate role to access specific routes:
     * - Admins can access /admin/* routes
     * - Teachers can access /teacher/* routes
     * - Students can access /student/* routes and /announcements
     * 
     * If access is denied, redirects to /announcements with error message
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the current URI path
        $uri = $request->getUri()->getPath();
        
        // Get user session data
        $session = session();
        $userRole = strtolower($session->get('role') ?? '');
        $isLoggedIn = $session->get('logged_in');

        // If user is not logged in, redirect to login page
        if (!$isLoggedIn) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        // Check role-based access permissions
        
        // Admin: Can access any /admin/* routes
        if (strpos($uri, '/admin') === 0 || strpos($uri, 'admin/') !== false) {
            if ($userRole !== 'admin') {
                return redirect()->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }

        // Teacher: Can only access /teacher/* routes
        if (strpos($uri, '/teacher') === 0 || strpos($uri, 'teacher/') !== false) {
            if ($userRole !== 'teacher') {
                return redirect()->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }

        // Student: Can access /student/* routes and /announcements
        if (strpos($uri, '/student') === 0 || strpos($uri, 'student/') !== false) {
            if ($userRole !== 'student') {
                return redirect()->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }

        // Allow the request to continue
        return null;
    }

    /**
     * After filter (not used for this task)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

