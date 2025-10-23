<?= view('templates/header') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="bi bi-bell-fill me-2"></i>Step 7: Generate Test Notifications - Demo</h4>
                </div>
                <div class="card-body">
                    <p class="lead">This page demonstrates automatic notification generation when various events occur in the LMS.</p>
                    
                    <div class="alert alert-success">
                        <h6><i class="bi bi-check-circle me-2"></i>Step 7 Events That Generate Notifications:</h6>
                        <ul class="mb-0">
                            <li>✅ <strong>Course Enrollment:</strong> Students get notified when they enroll in courses</li>
                            <li>✅ <strong>Material Upload:</strong> Students get notified when new materials are uploaded to their courses</li>
                            <li>✅ <strong>New Announcements:</strong> All students get notified when announcements are posted</li>
                            <li>✅ <strong>User Registration:</strong> New users get welcome notifications</li>
                        </ul>
                    </div>
                    
                    <!-- Test Actions -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="bi bi-mortarboard me-2"></i>Test Course Enrollment Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Enroll in a course to receive an enrollment notification.</p>
                                    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Go to Dashboard & Enroll
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="bi bi-file-earmark me-2"></i>Test Material Upload Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Upload materials to notify enrolled students.</p>
                                    <?php if (in_array(session('role'), ['admin', 'teacher'])): ?>
                                        <a href="<?= base_url('materials/upload') ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-upload me-2"></i>Upload Material
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Admin/Teacher access required</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="bi bi-megaphone me-2"></i>Test Announcement Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">Create announcements to notify all students.</p>
                                    <?php if (session('role') === 'admin'): ?>
                                        <a href="<?= base_url('announcements/create') ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-plus-circle me-2"></i>Create Announcement
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Admin access required</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="bi bi-person-plus me-2"></i>Test Registration Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small">New users get welcome notifications on registration.</p>
                                    <a href="<?= base_url('register') ?>" class="btn btn-info btn-sm" target="_blank">
                                        <i class="bi bi-person-plus me-2"></i>Test Registration
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Notifications Display -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="bi bi-list-ul me-2"></i>Your Current Notifications</h6>
                        </div>
                        <div class="card-body">
                            <div id="currentNotifications">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading notifications...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 7 Implementation Details -->
                    <div class="accordion" id="implementationAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    <i class="bi bi-code-slash me-2"></i>Step 7 Implementation Details
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#implementationAccordion">
                                <div class="accordion-body">
                                    <h6>Modified Controllers:</h6>
                                    <ul>
                                        <li><strong>Course.php:</strong> Creates notifications on successful enrollment</li>
                                        <li><strong>Materials.php:</strong> Notifies enrolled students when materials are uploaded</li>
                                        <li><strong>Announcement.php:</strong> Notifies all students when announcements are posted</li>
                                        <li><strong>Auth.php:</strong> Creates welcome notifications for new registrations</li>
                                    </ul>
                                    
                                    <h6>Notification Messages:</h6>
                                    <ul>
                                        <li><strong>Enrollment:</strong> "You have been successfully enrolled in '[Course Name]'. Welcome to the course!"</li>
                                        <li><strong>Material Upload:</strong> "New material '[File Name]' has been uploaded to your course '[Course Name]'. Check it out now!"</li>
                                        <li><strong>Announcement:</strong> "New announcement posted: '[Title]'. Check it out in the announcements section!"</li>
                                        <li><strong>Registration:</strong> "Welcome to our Learning Management System, [Name]! Start exploring courses and announcements."</li>
                                    </ul>
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
    // Load current notifications
    loadCurrentNotifications();
    
    // Refresh notifications every 10 seconds on this test page
    setInterval(loadCurrentNotifications, 10000);
});

function loadCurrentNotifications() {
    $.get('<?= base_url('/notifications') ?>', function(data) {
        const container = $('#currentNotifications');
        
        if (data.success && data.notifications.length > 0) {
            let html = '';
            data.notifications.forEach(function(notification) {
                const badgeClass = notification.is_read ? 'bg-secondary' : 'bg-primary';
                const statusText = notification.is_read ? 'Read' : 'Unread';
                
                html += `
                    <div class="alert alert-${notification.is_read ? 'secondary' : 'info'} mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${notification.message}</strong>
                                <br>
                                <small class="text-muted">${notification.time_ago}</small>
                            </div>
                            <span class="badge ${badgeClass}">${statusText}</span>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
        } else {
            container.html(`
                <div class="alert alert-light text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mb-0">No notifications yet. Try the test actions above!</p>
                </div>
            `);
        }
    }).fail(function() {
        $('#currentNotifications').html(`
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed to load notifications. Please refresh the page.
            </div>
        `);
    });
}
</script>
