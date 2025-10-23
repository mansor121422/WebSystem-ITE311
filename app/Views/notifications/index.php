<?= view('templates/header') ?>

<div class="container mt-4">
    <style>
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .page-title {
            margin: 0;
            color: #2c3e50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-item.unread {
            background-color: #f8f9ff;
            border-left: 4px solid #3498db;
        }
        
        .notification-item:hover {
            background-color: #f5f5f5;
        }
        
        .notification-message {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .notification-time {
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #229954;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .unread-count {
            background-color: #e74c3c;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
        <div class="page-header">
            <h1 class="page-title">
                Notifications
                <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                    <span class="unread-count"><?= $unreadCount ?> unread</span>
                <?php endif; ?>
            </h1>
        </div>
        
        <div class="actions">
            <a href="<?= base_url('notifications/create-test') ?>" class="btn btn-primary">Create Test Notification</a>
            <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                <button onclick="markAllAsRead()" class="btn btn-success">Mark All as Read</button>
            <?php endif; ?>
        </div>
        
        <div class="notification-list">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>" 
                         onclick="markAsRead(<?= $notification['id'] ?>)">
                        <div class="notification-message">
                            <?= esc($notification['message']) ?>
                        </div>
                        <div class="notification-time">
                            <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>No notifications yet</h3>
                    <p>You'll see notifications here when there are updates for you.</p>
                    <a href="<?= base_url('notifications/create-test') ?>" class="btn btn-primary">Create Test Notification</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function markAsRead(notificationId) {
            fetch('<?= base_url('notifications/mark-as-read') ?>/' + notificationId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking the notification as read.');
            });
        }
        
        function markAllAsRead() {
            fetch('<?= base_url('notifications/mark-all-as-read') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking notifications as read.');
            });
        }
    </script>
</div>
