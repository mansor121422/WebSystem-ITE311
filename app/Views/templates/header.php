<?php
// Get current user role and login status with strict checking
$isLoggedIn = false;
$userRole = '';

// Strict session validation - all conditions must be met
$loggedIn = session('logged_in');
$userID = session('userID');
$role = session('role');

// Only consider user logged in if ALL session data is present and valid
if ($loggedIn === true && !empty($userID) && !empty($role) && is_numeric($userID)) {
    $isLoggedIn = true;
    $userRole = $role;
}

$currentPage = $page ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LMS' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .navbar-custom {
            background-color: #2c3e50;
            border-bottom: 1px solid #34495e;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-custom .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .navbar-custom .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .navbar-custom .nav-link:hover {
            color: #f1f5f9 !important;
        }
        
        .notification-dropdown {
            min-width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            border-bottom: 1px solid #eee;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
        }
        
        .notification-message {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification-bell {
            position: relative;
            font-size: 1.2rem;
        }
        
        .dropdown-header-custom {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .no-notifications {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .mark-read-btn {
            font-size: 0.7rem;
            padding: 2px 6px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <strong>LMS</strong>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'student'): ?>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'courses' ? 'active' : '' ?>" href="<?= base_url('courses') ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'my-courses' ? 'active' : '' ?>" href="<?= base_url('student/courses') ?>">My Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('student/assignments') ?>">Assignments</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a></li>
                    <?php elseif ($userRole === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'courses' ? 'active' : '' ?>" href="<?= base_url('courses') ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a></li>
                    <?php elseif ($userRole === 'instructor' || $userRole === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/assignments') ?>">Assignments</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'courses' ? 'active' : '' ?>" href="<?= base_url('courses') ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="<?= base_url('about') ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="<?= base_url('contact') ?>">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
                <?php endif; ?>
            </ul>
            
            <!-- Right side navigation -->
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                    <!-- Notification Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle notification-bell" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-danger notification-badge" id="notificationBadge" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                            <li class="dropdown-header-custom d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-bell me-2"></i>Notifications</span>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="manualRefreshNotifications()" title="Refresh notifications">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn" style="display: none;" title="Mark all as read">
                                        <i class="bi bi-check-all"></i>
                                    </button>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notificationsList">
                                <li class="no-notifications">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p>No notifications yet</p>
                                </li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="<?= base_url('notifications/page') ?>">View All Notifications</a></li>
                        </ul>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= ucfirst($userRole) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('settings') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
    </div>
</nav>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    <?php if ($isLoggedIn): ?>
    // Step 6: Initialize notification system on page load
    console.log('🔔 Initializing notification system...');
    loadNotifications();
    
    // Step 6: Set interval to fetch notifications every 60 seconds for real-time updates
    const notificationInterval = setInterval(function() {
        console.log('🔄 Auto-refreshing notifications...');
        loadNotifications(true); // Pass true for auto-refresh
    }, 60000); // 60 seconds as specified
    
    // Store interval ID for potential cleanup
    window.notificationInterval = notificationInterval;
    
    // Mark all as read button
    $('#markAllReadBtn').on('click', function() {
        markAllAsRead();
    });
    
    // Add loading indicator when dropdown is opened
    $('#notificationDropdown').on('show.bs.dropdown', function() {
        showNotificationLoading();
        loadNotifications();
    });
    
    // Add visibility change handler to refresh when tab becomes active
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            console.log('👁️ Page became visible, refreshing notifications...');
            loadNotifications();
        }
    });
    
    console.log('✅ Notification system initialized successfully');
    <?php endif; ?>
});

// Load notifications via AJAX
function loadNotifications(isAutoRefresh = false) {
    // Add loading indicator for manual refreshes
    if (!isAutoRefresh) {
        showNotificationLoading();
    }
    
    // Add timestamp for debugging
    const timestamp = new Date().toLocaleTimeString();
    console.log(`📡 Loading notifications at ${timestamp}${isAutoRefresh ? ' (auto-refresh)' : ''}`);
    
    $.get('<?= base_url('/notifications') ?>', function(data) {
        if (data.success) {
            updateNotificationBadge(data.unreadCount);
            updateNotificationsList(data.notifications);
            
            // Show success indicator for manual refreshes
            if (!isAutoRefresh) {
                console.log('✅ Notifications loaded successfully');
                showUpdateIndicator();
            }
            
            // Store last update time
            window.lastNotificationUpdate = new Date();
            
        } else {
            console.error('❌ Server returned error:', data.message);
            showNotificationError('Failed to load notifications: ' + data.message);
        }
    }).fail(function(xhr, status, error) {
        console.error('❌ AJAX request failed:', error);
        showNotificationError('Network error loading notifications');
    }).always(function() {
        // Hide loading indicator
        hideNotificationLoading();
    });
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = $('#notificationBadge');
    const markAllBtn = $('#markAllReadBtn');
    
    if (count > 0) {
        badge.text(count > 99 ? '99+' : count).show();
        markAllBtn.show();
    } else {
        badge.hide();
        markAllBtn.hide();
    }
}

// Update notifications list
function updateNotificationsList(notifications) {
    const container = $('#notificationsList');
    
    if (notifications.length === 0) {
        container.html(`
            <li class="no-notifications">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p>No notifications yet</p>
            </li>
        `);
        return;
    }
    
    let html = '';
    notifications.forEach(function(notification) {
        const alertClass = notification.is_read ? 'alert-secondary' : 'alert-info';
        const readButton = notification.is_read ? '' : `
            <button class="btn btn-sm btn-outline-success mark-read-btn" onclick="markAsRead(${notification.id})">
                <i class="bi bi-check"></i> Mark Read
            </button>
        `;
        
        html += `
            <li class="notification-item ${notification.is_read ? '' : 'unread'}">
                <div class="alert ${alertClass} mb-0">
                    <div class="notification-message">${notification.message}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="notification-time text-muted">${notification.time_ago}</small>
                        ${readButton}
                    </div>
                </div>
            </li>
        `;
    });
    
    container.html(html);
}

// Mark single notification as read
function markAsRead(notificationId) {
    $.post('<?= base_url('/notifications/mark_read/') ?>' + notificationId, function(data) {
        if (data.success) {
            // Reload notifications to update the display
            loadNotifications();
            
            // Show success message (optional)
            showNotificationToast('Notification marked as read', 'success');
        } else {
            showNotificationToast('Failed to mark notification as read', 'error');
        }
    }).fail(function() {
        showNotificationToast('Error marking notification as read', 'error');
    });
}

// Mark all notifications as read
function markAllAsRead() {
    $.post('<?= base_url('notifications/mark-all-as-read') ?>', function(data) {
        if (data.success) {
            loadNotifications();
            showNotificationToast('All notifications marked as read', 'success');
        } else {
            showNotificationToast('Failed to mark all notifications as read', 'error');
        }
    }).fail(function() {
        showNotificationToast('Error marking notifications as read', 'error');
    });
}

// Show loading indicator in notification dropdown
function showNotificationLoading() {
    const container = $('#notificationsList');
    container.html(`
        <li class="notification-item text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="text-muted">Loading notifications...</span>
        </li>
    `);
}

// Hide loading indicator
function hideNotificationLoading() {
    // Loading will be replaced by actual notifications or empty state
}

// Show error in notification dropdown
function showNotificationError(message) {
    const container = $('#notificationsList');
    container.html(`
        <li class="notification-item">
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="loadNotifications()">
                    <i class="bi bi-arrow-clockwise"></i> Retry
                </button>
            </div>
        </li>
    `);
}

// Show visual indicator that notifications were updated
function showUpdateIndicator() {
    const bell = $('#notificationDropdown i.bi-bell');
    
    // Add a subtle animation to indicate update
    bell.addClass('text-success');
    setTimeout(function() {
        bell.removeClass('text-success');
    }, 1000);
}

// Show toast notification
function showNotificationToast(message, type) {
    const toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
    const toastHtml = `
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Create toast container if it doesn't exist
    if ($('#toastContainer').length === 0) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    const $toast = $(toastHtml);
    $('#toastContainer').append($toast);
    
    const toast = new bootstrap.Toast($toast[0]);
    toast.show();
    
    // Remove toast after it's hidden
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Add manual refresh button functionality
function manualRefreshNotifications() {
    console.log('🔄 Manual refresh triggered');
    loadNotifications();
    showNotificationToast('Refreshing notifications...', 'success');
}

// Get time since last update (for debugging)
function getTimeSinceLastUpdate() {
    if (window.lastNotificationUpdate) {
        const now = new Date();
        const diff = Math.floor((now - window.lastNotificationUpdate) / 1000);
        return `${diff} seconds ago`;
    }
    return 'Never';
}
</script>
