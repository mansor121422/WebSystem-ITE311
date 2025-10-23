<?= view('templates/header') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-bell-fill me-2"></i>Notification System Demo</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This page demonstrates the Bootstrap + jQuery notification system with <strong>Step 6: Trigger Notification Updates</strong>.</p>
                    
                    <div class="alert alert-success">
                        <h6><i class="bi bi-check-circle me-2"></i>Step 6 Features Implemented:</h6>
                        <ul class="mb-0">
                            <li>✅ <strong>Page Load Initialization:</strong> Notifications load automatically when page loads</li>
                            <li>✅ <strong>60-Second Auto-Refresh:</strong> Real-time updates every 60 seconds</li>
                            <li>✅ <strong>Manual Refresh:</strong> Click refresh button in dropdown</li>
                            <li>✅ <strong>Visual Indicators:</strong> Loading spinners and success animations</li>
                            <li>✅ <strong>Error Handling:</strong> Retry buttons and error messages</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>How to Test Step 6:</h6>
                        <ol>
                            <li>Open browser console to see <strong>initialization logs</strong></li>
                            <li>Look at the <strong>bell icon</strong> in the navigation bar</li>
                            <li>Click the bell to see the <strong>notification dropdown</strong></li>
                            <li>Use the <strong>refresh button</strong> in the dropdown header</li>
                            <li>Wait 60 seconds to see <strong>auto-refresh</strong> in console</li>
                            <li>Switch browser tabs and come back to see <strong>visibility refresh</strong></li>
                        </ol>
                    </div>
                    
                    <!-- Real-time Status Display -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="bi bi-activity me-2"></i>Real-time Status</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Last Update:</small><br>
                                    <span id="lastUpdateTime" class="badge bg-secondary">Loading...</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Next Auto-refresh:</small><br>
                                    <span id="nextRefreshTime" class="badge bg-info">60 seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Create Test Notifications</h6>
                                    <a href="<?= base_url('notifications/create-test') ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Create Test Notification
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Test Step 6 Features</h6>
                                    <button onclick="runStep6Tests()" class="btn btn-warning">
                                        <i class="bi bi-play-circle me-2"></i>Run Step 6 Tests
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>View All Notifications</h6>
                                    <a href="<?= base_url('notifications/page') ?>" class="btn btn-success">
                                        <i class="bi bi-list-ul me-2"></i>View All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5><i class="bi bi-gear me-2"></i>Features Implemented:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Bootstrap 5 UI with responsive design
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    jQuery AJAX for real-time updates
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Notification badge with count
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Dropdown menu with notifications list
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Mark as read functionality
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Auto-refresh every 30 seconds
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Toast notifications for feedback
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Bootstrap alert styling
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>API Endpoints:</h6>
                        <ul class="mb-0">
                            <li><code>GET /notifications</code> - Get notifications JSON</li>
                            <li><code>POST /notifications/mark_read/{id}</code> - Mark as read</li>
                            <li><code>POST /notifications/mark-all-as-read</code> - Mark all as read</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Demo-specific JavaScript for Step 6
$(document).ready(function() {
    console.log('🎯 Step 6 Demo: Initializing...');
    
    // Show a welcome toast
    setTimeout(function() {
        showNotificationToast('Step 6: Notification system loaded successfully!', 'success');
    }, 1000);
    
    // Update status display every second
    setInterval(updateStatusDisplay, 1000);
    
    // Initial status update
    updateStatusDisplay();
    
    // Add click tracking for demo
    $('.btn').on('click', function() {
        const btnText = $(this).text().trim();
        console.log('🖱️ Button clicked:', btnText);
    });
    
    // Add console logging for Step 6 demonstration
    console.log('📋 Step 6 Features Active:');
    console.log('   ✅ Page Load Initialization: $(document).ready()');
    console.log('   ✅ 60-Second Auto-Refresh: setInterval(loadNotifications, 60000)');
    console.log('   ✅ Manual Refresh: Click refresh button in dropdown');
    console.log('   ✅ Visual Indicators: Loading spinners and animations');
    console.log('   ✅ Error Handling: Retry buttons and error messages');
});

// Update the real-time status display
function updateStatusDisplay() {
    // Update last update time
    const lastUpdateElement = $('#lastUpdateTime');
    if (window.lastNotificationUpdate) {
        const timeSince = getTimeSinceLastUpdate();
        lastUpdateElement.text(timeSince).removeClass('bg-secondary').addClass('bg-success');
    } else {
        lastUpdateElement.text('Not yet loaded').removeClass('bg-success').addClass('bg-secondary');
    }
    
    // Calculate next refresh time (60 seconds from last update)
    const nextRefreshElement = $('#nextRefreshTime');
    if (window.lastNotificationUpdate) {
        const now = new Date();
        const nextRefresh = new Date(window.lastNotificationUpdate.getTime() + 60000);
        const timeUntilRefresh = Math.max(0, Math.floor((nextRefresh - now) / 1000));
        
        if (timeUntilRefresh > 0) {
            nextRefreshElement.text(`${timeUntilRefresh} seconds`).removeClass('bg-warning').addClass('bg-info');
        } else {
            nextRefreshElement.text('Refreshing now...').removeClass('bg-info').addClass('bg-warning');
        }
    } else {
        nextRefreshElement.text('60 seconds').removeClass('bg-warning').addClass('bg-info');
    }
}

// Test function to manually trigger Step 6 features
function testStep6Features() {
    console.log('🧪 Testing Step 6 Features:');
    
    // Test 1: Manual refresh
    console.log('   Test 1: Manual refresh');
    manualRefreshNotifications();
    
    // Test 2: Show loading indicator
    setTimeout(function() {
        console.log('   Test 2: Loading indicator');
        showNotificationLoading();
        
        // Test 3: Show update indicator
        setTimeout(function() {
            console.log('   Test 3: Update indicator');
            showUpdateIndicator();
            loadNotifications();
        }, 2000);
    }, 1000);
}

// Add test button functionality
function runStep6Tests() {
    testStep6Features();
    showNotificationToast('Running Step 6 tests - check console!', 'success');
}
</script>
