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
$routes->get('announcements/create', 'Announcement::create');
$routes->post('announcements/create', 'Announcement::create');
