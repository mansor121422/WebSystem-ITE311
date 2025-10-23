<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications API Test - LMS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .api-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .response-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 10px;
            border: 1px solid #ddd;
            margin: 5px 0;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .notification-item.unread {
            border-left: 4px solid #3498db;
            background: #f0f8ff;
        }
        
        .badge {
            background-color: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>
    
    <div class="container">
        <h1>🔔 Notifications API Test Page</h1>
        <p>This page demonstrates the Notifications API endpoints for AJAX calls.</p>
        
        <!-- API Status -->
        <div class="api-section">
            <h3>📊 Current Status</h3>
            <p>Unread Count: <span id="unreadCount" class="badge">0</span></p>
            <button onclick="refreshNotifications()" class="btn btn-primary">🔄 Refresh Notifications</button>
            <button onclick="createTestNotification()" class="btn btn-success">➕ Create Test Notification</button>
        </div>
        
        <!-- GET /notifications API Test -->
        <div class="api-section">
            <h3>🔍 GET /notifications API Test</h3>
            <p>Tests the <code>Notifications::get()</code> method that returns JSON response.</p>
            <button onclick="testGetAPI()" class="btn btn-primary">Test GET API</button>
            <div id="getApiResponse" class="response-box" style="display: none;"></div>
        </div>
        
        <!-- Notifications List -->
        <div class="api-section">
            <h3>📋 Current Notifications</h3>
            <div id="notificationsList">
                <p>Click "Refresh Notifications" to load...</p>
            </div>
        </div>
        
        <!-- Mark as Read API Test -->
        <div class="api-section">
            <h3>✅ POST /notifications/mark_read/{id} API Test</h3>
            <p>Tests the <code>Notifications::mark_as_read($id)</code> method.</p>
            <input type="number" id="notificationIdInput" placeholder="Notification ID" class="form-control" style="padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px;">
            <button onclick="testMarkAsReadAPI()" class="btn btn-warning">Test Mark as Read API</button>
            <div id="markAsReadResponse" class="response-box" style="display: none;"></div>
        </div>
        
        <!-- Raw API Responses -->
        <div class="api-section">
            <h3>🔧 Raw API Response Log</h3>
            <div id="apiLog" class="response-box">API calls will be logged here...</div>
        </div>
    </div>
    
    <script>
        let currentNotifications = [];
        
        // Test GET /notifications API
        async function testGetAPI() {
            try {
                logAPI('GET /notifications', 'Calling...');
                
                const response = await fetch('<?= base_url('/notifications') ?>', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                document.getElementById('getApiResponse').style.display = 'block';
                document.getElementById('getApiResponse').textContent = JSON.stringify(data, null, 2);
                
                logAPI('GET /notifications', 'Success: ' + JSON.stringify(data));
                
                if (data.success) {
                    currentNotifications = data.notifications;
                    updateNotificationsList(data.notifications);
                    updateUnreadCount(data.unreadCount);
                }
                
            } catch (error) {
                logAPI('GET /notifications', 'Error: ' + error.message);
                document.getElementById('getApiResponse').style.display = 'block';
                document.getElementById('getApiResponse').textContent = 'Error: ' + error.message;
            }
        }
        
        // Test POST /notifications/mark_read/{id} API
        async function testMarkAsReadAPI() {
            const notificationId = document.getElementById('notificationIdInput').value;
            
            if (!notificationId) {
                alert('Please enter a notification ID');
                return;
            }
            
            try {
                logAPI('POST /notifications/mark_read/' + notificationId, 'Calling...');
                
                const response = await fetch('<?= base_url('/notifications/mark_read/') ?>' + notificationId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                document.getElementById('markAsReadResponse').style.display = 'block';
                document.getElementById('markAsReadResponse').textContent = JSON.stringify(data, null, 2);
                
                logAPI('POST /notifications/mark_read/' + notificationId, 'Response: ' + JSON.stringify(data));
                
                if (data.success) {
                    // Refresh notifications after marking as read
                    refreshNotifications();
                }
                
            } catch (error) {
                logAPI('POST /notifications/mark_read/' + notificationId, 'Error: ' + error.message);
                document.getElementById('markAsReadResponse').style.display = 'block';
                document.getElementById('markAsReadResponse').textContent = 'Error: ' + error.message;
            }
        }
        
        // Refresh notifications
        async function refreshNotifications() {
            await testGetAPI();
        }
        
        // Create test notification
        async function createTestNotification() {
            try {
                const response = await fetch('<?= base_url('notifications/create-test') ?>', {
                    method: 'GET'
                });
                
                if (response.redirected) {
                    // Refresh notifications after creating
                    setTimeout(refreshNotifications, 500);
                    logAPI('Create Test', 'Test notification created');
                }
                
            } catch (error) {
                logAPI('Create Test', 'Error: ' + error.message);
            }
        }
        
        // Update notifications list display
        function updateNotificationsList(notifications) {
            const container = document.getElementById('notificationsList');
            
            if (notifications.length === 0) {
                container.innerHTML = '<p>No notifications found.</p>';
                return;
            }
            
            let html = '';
            notifications.forEach(notification => {
                html += `
                    <div class="notification-item ${notification.is_read ? '' : 'unread'}" onclick="markNotificationAsRead(${notification.id})">
                        <strong>ID: ${notification.id}</strong> - ${notification.message}
                        <br>
                        <small>${notification.time_ago} | Status: ${notification.is_read ? 'Read' : 'Unread'}</small>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // Update unread count display
        function updateUnreadCount(count) {
            document.getElementById('unreadCount').textContent = count;
        }
        
        // Mark notification as read (click handler)
        function markNotificationAsRead(id) {
            document.getElementById('notificationIdInput').value = id;
            testMarkAsReadAPI();
        }
        
        // Log API calls
        function logAPI(endpoint, message) {
            const timestamp = new Date().toLocaleTimeString();
            const logElement = document.getElementById('apiLog');
            logElement.textContent += `[${timestamp}] ${endpoint}: ${message}\n`;
            logElement.scrollTop = logElement.scrollHeight;
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshNotifications, 30000);
        
        // Load notifications on page load
        window.onload = function() {
            refreshNotifications();
        };
    </script>
</body>
</html>
