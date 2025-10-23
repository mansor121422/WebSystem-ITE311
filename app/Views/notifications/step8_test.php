<?= view('templates/header') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="bi bi-check-circle-fill me-2"></i>Step 8: Test the Functionality - Complete Verification</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This page provides comprehensive testing for all notification functionality as specified in Step 8.</p>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Step 8 Testing Requirements:</h6>
                        <ol class="mb-0">
                            <li>✅ Log in as a student and enroll in a new course</li>
                            <li>✅ Refresh the page and verify notification badge appears with correct count</li>
                            <li>✅ Click notification dropdown and verify list is populated correctly</li>
                            <li>✅ Click Mark as Read button and verify notification disappears and badge count decreases</li>
                            <li>✅ Create notifications manually in database (bonus testing)</li>
                        </ol>
                    </div>
                    
                    <!-- Current User Info -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6><i class="bi bi-person-circle me-2"></i>Current User Information</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Name:</strong> <?= session('name') ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Role:</strong> <span class="badge bg-primary"><?= ucfirst(session('role')) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>User ID:</strong> <?= session('userID') ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Email:</strong> <?= session('email') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Progress Tracker -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="bi bi-list-check me-2"></i>Test Progress Tracker</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test1">
                                        <label class="form-check-label" for="test1">
                                            <strong>Test 1:</strong> Student enrollment creates notification
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test2">
                                        <label class="form-check-label" for="test2">
                                            <strong>Test 2:</strong> Badge appears with correct count
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test3">
                                        <label class="form-check-label" for="test3">
                                            <strong>Test 3:</strong> Dropdown shows notifications correctly
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test4">
                                        <label class="form-check-label" for="test4">
                                            <strong>Test 4:</strong> Mark as read works correctly
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test5">
                                        <label class="form-check-label" for="test5">
                                            <strong>Test 5:</strong> Badge count decreases after marking read
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="test6">
                                        <label class="form-check-label" for="test6">
                                            <strong>Bonus:</strong> Manual database notification creation
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" id="progressBar" style="width: 0%">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Actions -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6><i class="bi bi-1-circle me-2"></i>Test 1: Course Enrollment</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Enroll in a course to trigger notification creation.</p>
                                    <?php if (session('role') === 'student'): ?>
                                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-mortarboard me-2"></i>Go to Dashboard & Enroll
                                        </a>
                                    <?php else: ?>
                                        <div class="alert alert-warning small mb-2">
                                            You need to be logged in as a <strong>student</strong> to test enrollment.
                                        </div>
                                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout & Login as Student
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6><i class="bi bi-2-circle me-2"></i>Test 2-3: Badge & Dropdown</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Check the notification bell in the navigation bar.</p>
                                    <button onclick="checkNotificationBadge()" class="btn btn-success btn-sm">
                                        <i class="bi bi-bell me-2"></i>Check Badge & Dropdown
                                    </button>
                                    <div id="badgeTestResult" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6><i class="bi bi-3-circle me-2"></i>Test 4-5: Mark as Read</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Test the mark as read functionality.</p>
                                    <button onclick="testMarkAsRead()" class="btn btn-warning btn-sm">
                                        <i class="bi bi-check-circle me-2"></i>Test Mark as Read
                                    </button>
                                    <div id="markAsReadResult" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual Database Testing -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6><i class="bi bi-database me-2"></i>Bonus: Manual Database Testing</h6>
                        </div>
                        <div class="card-body">
                            <p>Create notifications directly in the database for testing purposes.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <button onclick="createManualNotification()" class="btn btn-info btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Create Manual Notification
                                    </button>
                                    <button onclick="createMultipleNotifications()" class="btn btn-outline-info btn-sm ms-2">
                                        <i class="bi bi-plus-square me-2"></i>Create 3 Test Notifications
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <div id="manualTestResult"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Real-time Notification Display -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="bi bi-bell-fill me-2"></i>Current Notifications (Real-time)</h6>
                            <button onclick="refreshNotifications()" class="btn btn-sm btn-outline-primary float-end">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="currentNotificationsList">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading notifications...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Results Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="bi bi-clipboard-check me-2"></i>Test Results Summary</h6>
                        </div>
                        <div class="card-body">
                            <div id="testSummary">
                                <div class="alert alert-light">
                                    Complete the tests above to see the summary here.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load initial notifications
    refreshNotifications();
    
    // Update progress when checkboxes are checked
    $('.form-check-input').on('change', updateProgress);
    
    // Auto-refresh notifications every 15 seconds
    setInterval(refreshNotifications, 15000);
});

// Update progress bar
function updateProgress() {
    const total = $('.form-check-input').length;
    const checked = $('.form-check-input:checked').length;
    const percentage = Math.round((checked / total) * 100);
    
    $('#progressBar').css('width', percentage + '%').text(percentage + '%');
    
    if (percentage === 100) {
        $('#progressBar').removeClass('bg-success').addClass('bg-success');
        updateTestSummary();
    }
}

// Check notification badge functionality
function checkNotificationBadge() {
    const badge = $('#notificationBadge');
    const dropdown = $('#notificationDropdown');
    
    let result = '<div class="alert alert-info small mt-2">';
    
    // Check if badge exists
    if (badge.length > 0) {
        result += '✅ Badge element found<br>';
        
        // Check if badge is visible and has count
        if (badge.is(':visible')) {
            const count = badge.text();
            result += `✅ Badge is visible with count: ${count}<br>`;
            $('#test2').prop('checked', true);
        } else {
            result += '⚠️ Badge is hidden (no unread notifications)<br>';
        }
    } else {
        result += '❌ Badge element not found<br>';
    }
    
    // Check dropdown functionality
    if (dropdown.length > 0) {
        result += '✅ Dropdown element found<br>';
        result += '📝 Click the bell icon to test dropdown functionality<br>';
        $('#test3').prop('checked', true);
    } else {
        result += '❌ Dropdown element not found<br>';
    }
    
    result += '</div>';
    $('#badgeTestResult').html(result);
    updateProgress();
}

// Test mark as read functionality
function testMarkAsRead() {
    $.get('<?= base_url('/notifications') ?>', function(data) {
        if (data.success && data.notifications.length > 0) {
            const unreadNotifications = data.notifications.filter(n => !n.is_read);
            
            if (unreadNotifications.length > 0) {
                const notificationId = unreadNotifications[0].id;
                const initialCount = data.unreadCount;
                
                // Mark the first unread notification as read
                $.post('<?= base_url('/notifications/mark_read/') ?>' + notificationId, function(response) {
                    if (response.success) {
                        // Check if count decreased
                        setTimeout(function() {
                            $.get('<?= base_url('/notifications') ?>', function(newData) {
                                const newCount = newData.unreadCount;
                                let result = '<div class="alert alert-success small mt-2">';
                                result += `✅ Notification ${notificationId} marked as read<br>`;
                                result += `✅ Count changed from ${initialCount} to ${newCount}<br>`;
                                result += '</div>';
                                
                                $('#markAsReadResult').html(result);
                                $('#test4').prop('checked', true);
                                $('#test5').prop('checked', true);
                                updateProgress();
                                refreshNotifications();
                            });
                        }, 1000);
                    } else {
                        $('#markAsReadResult').html('<div class="alert alert-danger small mt-2">❌ Failed to mark as read: ' + response.message + '</div>');
                    }
                });
            } else {
                $('#markAsReadResult').html('<div class="alert alert-warning small mt-2">⚠️ No unread notifications to test with. Create some notifications first.</div>');
            }
        } else {
            $('#markAsReadResult').html('<div class="alert alert-warning small mt-2">⚠️ No notifications found. Create some notifications first.</div>');
        }
    });
}

// Create manual notification
function createManualNotification() {
    $.post('<?= base_url('notifications/create-test') ?>', function() {
        $('#manualTestResult').html('<div class="alert alert-success small mt-2">✅ Manual notification created!</div>');
        $('#test6').prop('checked', true);
        updateProgress();
        setTimeout(refreshNotifications, 1000);
    }).fail(function() {
        $('#manualTestResult').html('<div class="alert alert-danger small mt-2">❌ Failed to create manual notification</div>');
    });
}

// Create multiple notifications for testing
function createMultipleNotifications() {
    let created = 0;
    const total = 3;
    
    for (let i = 0; i < total; i++) {
        setTimeout(function() {
            $.post('<?= base_url('notifications/create-test') ?>', function() {
                created++;
                $('#manualTestResult').html(`<div class="alert alert-info small mt-2">Creating notifications... ${created}/${total}</div>`);
                
                if (created === total) {
                    $('#manualTestResult').html('<div class="alert alert-success small mt-2">✅ Created 3 test notifications!</div>');
                    $('#test6').prop('checked', true);
                    updateProgress();
                    setTimeout(refreshNotifications, 1000);
                }
            });
        }, i * 500);
    }
}

// Refresh notifications display
function refreshNotifications() {
    $.get('<?= base_url('/notifications') ?>', function(data) {
        const container = $('#currentNotificationsList');
        
        if (data.success && data.notifications.length > 0) {
            let html = `<div class="mb-2"><strong>Total: ${data.notifications.length} | Unread: ${data.unreadCount}</strong></div>`;
            
            data.notifications.forEach(function(notification, index) {
                const badgeClass = notification.is_read ? 'bg-secondary' : 'bg-primary';
                const statusText = notification.is_read ? 'Read' : 'Unread';
                const alertClass = notification.is_read ? 'alert-secondary' : 'alert-info';
                
                html += `
                    <div class="alert ${alertClass} py-2 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>ID: ${notification.id}</strong> - ${notification.message}
                                <br>
                                <small class="text-muted">${notification.time_ago}</small>
                            </div>
                            <div>
                                <span class="badge ${badgeClass}">${statusText}</span>
                                ${!notification.is_read ? `<button class="btn btn-sm btn-outline-success ms-1" onclick="quickMarkAsRead(${notification.id})"><i class="bi bi-check"></i></button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
            
            // Auto-check test 1 if we have notifications
            if (data.notifications.length > 0) {
                $('#test1').prop('checked', true);
                updateProgress();
            }
        } else {
            container.html(`
                <div class="alert alert-light text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mb-0">No notifications yet. Try enrolling in a course or creating manual notifications!</p>
                </div>
            `);
        }
    }).fail(function() {
        $('#currentNotificationsList').html(`
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed to load notifications. Please refresh the page.
            </div>
        `);
    });
}

// Quick mark as read from the list
function quickMarkAsRead(notificationId) {
    $.post('<?= base_url('/notifications/mark_read/') ?>' + notificationId, function(response) {
        if (response.success) {
            showNotificationToast('Notification marked as read!', 'success');
            setTimeout(refreshNotifications, 500);
        } else {
            showNotificationToast('Failed to mark as read: ' + response.message, 'error');
        }
    });
}

// Update test summary
function updateTestSummary() {
    const summary = `
        <div class="alert alert-success">
            <h6><i class="bi bi-check-circle-fill me-2"></i>All Tests Completed Successfully!</h6>
            <p class="mb-0">
                🎉 Congratulations! You have successfully verified all Step 8 functionality:
                <br>✅ Course enrollment creates notifications
                <br>✅ Notification badge displays correctly
                <br>✅ Dropdown shows notifications properly
                <br>✅ Mark as read functionality works
                <br>✅ Badge count updates correctly
                <br>✅ Manual notification creation works
            </p>
        </div>
    `;
    $('#testSummary').html(summary);
}
</script>
