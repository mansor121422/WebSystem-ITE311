# 🔔 Notifications API Guide

## Overview
This guide explains how to use the Notifications API endpoints for AJAX calls in your LMS system.

## API Endpoints

### 1. GET /notifications
**Purpose**: Get current user's notifications and unread count  
**Method**: GET  
**URL**: `/notifications`  
**Controller**: `Notifications::get()`

#### Response Format:
```json
{
    "success": true,
    "unreadCount": 3,
    "notifications": [
        {
            "id": 1,
            "message": "You have been enrolled in a new course",
            "is_read": false,
            "created_at": "2025-10-24 03:30:00",
            "time_ago": "5 minutes ago"
        }
    ]
}
```

#### JavaScript Example:
```javascript
fetch('/notifications', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => response.json())
.then(data => {
    console.log('Unread count:', data.unreadCount);
    console.log('Notifications:', data.notifications);
});
```

### 2. POST /notifications/mark_read/{id}
**Purpose**: Mark a specific notification as read  
**Method**: POST  
**URL**: `/notifications/mark_read/{id}`  
**Controller**: `Notifications::mark_as_read($id)`

#### Response Format:
```json
{
    "success": true,
    "message": "Notification marked as read."
}
```

#### JavaScript Example:
```javascript
fetch('/notifications/mark_read/123', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Notification marked as read');
    }
});
```

## Routes Configuration

Add these routes to `app/Config/Routes.php`:

```php
// API Routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
```

## Testing the API

### 1. API Test Page
Visit: `/notifications/api-test`

This page provides:
- Live API testing interface
- Real-time notifications display
- Interactive mark-as-read functionality
- API response logging

### 2. Manual Testing

#### Test GET API:
```bash
curl -X GET "http://localhost/ITE311-MALIK/notifications" \
     -H "Content-Type: application/json" \
     -H "X-Requested-With: XMLHttpRequest"
```

#### Test POST API:
```bash
curl -X POST "http://localhost/ITE311-MALIK/notifications/mark_read/1" \
     -H "Content-Type: application/json" \
     -H "X-Requested-With: XMLHttpRequest"
```

## Integration Examples

### 1. Real-time Notification Badge Update
```javascript
function updateNotificationBadge() {
    fetch('/notifications')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.unreadCount > 0) {
                badge.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}

// Update every 30 seconds
setInterval(updateNotificationBadge, 30000);
```

### 2. Notification Dropdown
```javascript
function loadNotificationDropdown() {
    fetch('/notifications')
        .then(response => response.json())
        .then(data => {
            const dropdown = document.getElementById('notification-dropdown');
            let html = '';
            
            data.notifications.forEach(notification => {
                html += `
                    <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                         onclick="markAsRead(${notification.id})">
                        <p>${notification.message}</p>
                        <small>${notification.time_ago}</small>
                    </div>
                `;
            });
            
            dropdown.innerHTML = html;
        });
}

function markAsRead(id) {
    fetch(`/notifications/mark_read/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotificationDropdown(); // Refresh
            updateNotificationBadge(); // Update badge
        }
    });
}
```

## Error Handling

### Common Error Responses:
```json
{
    "success": false,
    "message": "Please login to continue.",
    "unreadCount": 0,
    "notifications": []
}
```

### JavaScript Error Handling:
```javascript
fetch('/notifications')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            console.error('API Error:', data.message);
            return;
        }
        // Handle success
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
```

## Security Features

1. **Authentication Check**: All endpoints verify user login
2. **Authorization**: Users can only access their own notifications
3. **CSRF Protection**: Built into CodeIgniter 4
4. **Input Validation**: Notification ID validation
5. **Error Logging**: Failed requests are logged

## Performance Tips

1. **Caching**: Consider caching notification counts
2. **Pagination**: Limit notifications returned (default: 10)
3. **Polling Interval**: Don't poll too frequently (recommended: 30s)
4. **Batch Operations**: Use mark all as read for bulk operations

## Additional Endpoints

### Mark All as Read:
```php
$routes->post('notifications/mark-all-as-read', 'Notifications::markAllAsRead');
```

### Create Test Notification:
```php
$routes->get('notifications/create-test', 'Notifications::createTest');
```

## Database Schema

```sql
CREATE TABLE notifications (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Next Steps

1. Implement real-time notifications using WebSockets
2. Add notification categories/types
3. Implement push notifications
4. Add notification preferences
5. Create notification templates

---

**Ready to use!** Your notifications API is fully functional and ready for integration.
