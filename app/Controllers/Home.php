<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * Loads the homepage
     */
    public function index()
    {
        $session = session();
        $data = [
            'title' => 'Home - LMS System',
            'page' => 'home',
            'isLoggedIn' => $session->get('logged_in')
        ];
        
        return view('index', $data);
    }

    /**
     * Loads the about page
     */
    public function about()
    {
        $session = session();
        $data = [
            'title' => 'About Us - LMS System',
            'page' => 'about',
            'isLoggedIn' => $session->get('logged_in')
        ];
        
        return view('about', $data);
    }

    /**
     * Loads the contact page
     */
    public function contact()
    {
        $session = session();
        $data = [
            'title' => 'Contact Us - LMS System',
            'page' => 'contact',
            'isLoggedIn' => $session->get('logged_in')
        ];
        
        return view('contact', $data);
    }
}
