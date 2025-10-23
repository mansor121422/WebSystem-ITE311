<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\NotificationModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;
    protected $notificationModel;
    protected $notificationData;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');
        $this->notificationModel = new NotificationModel();
        
        // Load notification count for all views if user is logged in
        $this->loadNotificationData();
    }

    /**
     * Load notification data for the current user and make it available to all views
     * 
     * @return void
     */
    protected function loadNotificationData()
    {
        $notificationData = [
            'unreadNotificationCount' => 0,
            'recentNotifications' => []
        ];

        // Check if user is logged in
        if ($this->session->get('logged_in') && $this->session->get('userID')) {
            $userId = $this->session->get('userID');
            
            try {
                // Get unread notification count
                $notificationData['unreadNotificationCount'] = $this->notificationModel->getUnreadCount($userId);
                
                // Get recent notifications (limit 5)
                $notificationData['recentNotifications'] = $this->notificationModel->getNotificationsForUser($userId, 5);
                
            } catch (\Exception $e) {
                // Log error but don't break the application
                log_message('error', 'Failed to load notification data: ' . $e->getMessage());
            }
        }

        // Store notification data in a property for access by child controllers
        $this->notificationData = $notificationData;
    }

    /**
     * Get notification data for views
     * 
     * @return array
     */
    protected function getNotificationData()
    {
        return $this->notificationData ?? [
            'unreadNotificationCount' => 0,
            'recentNotifications' => []
        ];
    }

    /**
     * Helper method to create a notification for a user
     * 
     * @param int $userId
     * @param string $message
     * @return bool
     */
    protected function createNotification($userId, $message)
    {
        try {
            return $this->notificationModel->createNotification($userId, $message);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create notification: ' . $e->getMessage());
            return false;
        }
    }
}
