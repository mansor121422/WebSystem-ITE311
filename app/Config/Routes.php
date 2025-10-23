<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Auth::dashboard');
$routes->get('clear-session', 'Auth::clearSession');

// Course Routes
$routes->post('course/enroll', 'Course::enroll');
$routes->get('course/enrollments', 'Course::getUserEnrollments');
$routes->get('course/user-courses', 'Course::getUserCourses');
$routes->post('course/update-progress', 'Course::updateProgress');
$routes->get('course/stats', 'Course::getStats');

// Announcement Routes
$routes->get('announcements', 'Announcement::index');

// Notification Routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Additional notification routes
$routes->get('notifications/page', 'Notifications::index');
$routes->get('notifications/api-test', 'Notifications::apiTest');
$routes->get('notifications/demo', 'Notifications::demo');
$routes->get('notifications/step7-test', 'Notifications::step7Test');
$routes->get('notifications/step8-test', 'Notifications::step8Test');
$routes->get('enrollment-fix-test', 'Course::enrollmentFixTest');
$routes->post('notifications/mark-all-as-read', 'Notifications::markAllAsRead');
$routes->get('notifications/create-test', 'Notifications::createTest');

// Materials Routes
$routes->get('materials/view/(:num)', 'Materials::viewCourseMaterials/$1');
$routes->get('materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('materials/upload', 'Materials::upload');
$routes->post('materials/upload', 'Materials::upload');
$routes->post('materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->post('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');

// Task 4: Protected Routes with RoleAuth Filter

// Admin Routes - Protected by RoleAuth filter
$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('users', 'Admin::users'); // Future route
    $routes->get('courses', 'Admin::courses'); // Future route
    $routes->get('reports', 'Admin::reports'); // Future route
    $routes->get('settings', 'Admin::settings'); // Future route
    
    // Admin Material Upload Routes
    $routes->get('course/(:num)/upload', 'Materials::upload/$1');
    $routes->post('course/(:num)/upload', 'Materials::upload/$1');
});

// Admin Announcement Routes - Protected
$routes->group('announcements', ['filter' => 'roleauth'], function($routes) {
    $routes->get('create', 'Announcement::create');
    $routes->post('create', 'Announcement::create');
});

// Teacher Routes - Protected by RoleAuth filter
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    $routes->get('courses', 'Teacher::courses'); // Future route
    $routes->get('students', 'Teacher::students'); // Future route
    $routes->get('create', 'Teacher::create'); // Future route
    $routes->get('analytics', 'Teacher::analytics'); // Future route
});

// Student Routes - Protected by RoleAuth filter  
$routes->group('student', ['filter' => 'roleauth'], function($routes) {
    $routes->get('courses', 'Student::courses'); // Future route
    $routes->get('assignments', 'Student::assignments'); // Future route
    $routes->get('grades', 'Student::grades'); // Future route
});
