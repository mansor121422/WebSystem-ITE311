<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="user-management-page">
    <div class="page-header">
        <div>
            <h2>
                <i class="fas fa-users"></i> User Management
            </h2>
            <p class="page-subtitle">Manage users, roles, and permissions</p>
        </div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="get" action="<?= base_url('admin/users') ?>" class="filter-form">
            <div class="filter-group">
                <label for="role_filter" class="filter-label">
                    <i class="fas fa-filter"></i> Filter by Role:
                </label>
                <select name="role" id="role_filter" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Users</option>
                    <option value="admin" <?= (!empty($roleFilter) && $roleFilter === 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="teacher" <?= (!empty($roleFilter) && $roleFilter === 'teacher') ? 'selected' : '' ?>>Teacher</option>
                    <option value="student" <?= (!empty($roleFilter) && $roleFilter === 'student') ? 'selected' : '' ?>>Student</option>
                </select>
            </div>
            <?php if(!empty($roleFilter)): ?>
                <a href="<?= base_url('admin/users') ?>" class="clear-filter-btn" title="Clear Filter">
                    Clear Filter
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-section">
        <div class="table-header">
            <h3>
                <?php if(!empty($roleFilter)): ?>
                    <?= ucfirst($roleFilter) ?> Users
                <?php else: ?>
                    All Users
                <?php endif; ?>
            </h3>
            <span class="user-count"><?= count($users) ?> user(s)</span>
        </div>

        <?php if(empty($users)): ?>
            <div class="empty-state">
                <i class="fas fa-users fa-3x"></i>
                <h3>No users found</h3>
                <p><?= !empty($roleFilter) ? 'No users found with the selected role.' : 'Get started by creating a new user.' ?></p>
                <?php if(empty($roleFilter)): ?>
                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First User
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <span><?= esc($user['name']) ?></span>
                                    </div>
                                </td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= strtolower($user['role']) ?>">
                                        <?= ucfirst(esc($user['role'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if($user['id'] == $currentUserId): ?>
                                            <span class="badge badge-you">You</span>
                                            <span class="action-icon disabled" title="Cannot edit yourself">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                               class="action-btn action-btn-edit" 
                                               title="Edit User">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>
                                            <a href="#" 
                                               class="action-btn action-btn-delete delete-user-btn" 
                                               title="Delete User"
                                               data-user-id="<?= $user['id'] ?>"
                                               data-user-name="<?= esc($user['name']) ?>"
                                               data-user-email="<?= esc($user['email']) ?>"
                                               onclick="showDeleteModal(<?= $user['id'] ?>, <?= json_encode($user['name']) ?>, <?= json_encode($user['email']) ?>); return false;">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Deleted Users Section -->
    <?php if (!empty($deletedUsers)): ?>
        <div class="table-section deleted-users-section">
            <div class="table-header">
                <h3>
                    <i class="fas fa-trash"></i> Deleted Users
                </h3>
                <span class="user-count"><?= count($deletedUsers) ?> deleted user(s)</span>
            </div>
            <div class="table-responsive">
                <table class="users-table deleted-users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($deletedUsers as $user): ?>
                            <tr class="deleted-row">
                                <td><?= esc($user['id']) ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar deleted-avatar">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <span class="deleted-name"><?= esc($user['name']) ?></span>
                                    </div>
                                </td>
                                <td class="deleted-email"><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= strtolower($user['role']) ?>">
                                        <?= ucfirst(esc($user['role'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($user['deleted_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="post" action="<?= base_url('admin/users/restore/' . $user['id']) ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="action-btn action-btn-restore" 
                                                    title="Restore User"
                                                    onclick="return confirm('Are you sure you want to restore this user?')">
                                                <i class="fas fa-undo"></i>
                                                <span>Restore</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h5 class="delete-modal-title">
                <i class="fas fa-exclamation-triangle"></i> Confirm Deletion
            </h5>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="delete-modal-body">
            <p>Are you sure you want to delete this user? The user will be moved to the deleted users section and can be restored later.</p>
            <div class="delete-warning-box">
                <strong>User Details:</strong>
                <ul>
                    <li><strong>Name:</strong> <span id="deleteUserName"></span></li>
                    <li><strong>Email:</strong> <span id="deleteUserEmail"></span></li>
                </ul>
            </div>
            <p class="delete-warning-text">
                <i class="fas fa-info-circle"></i> The user will be hidden from the active users list but can be restored at any time.
            </p>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <form id="deleteForm" method="post" action="" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Yes, Delete User
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Ensure functions are available globally
function showDeleteModal(userId, userName, userEmail) {
    console.log('showDeleteModal called with:', userId, userName, userEmail);
    
    try {
        // Update user details in modal
        const nameElement = document.getElementById('deleteUserName');
        const emailElement = document.getElementById('deleteUserEmail');
        const formElement = document.getElementById('deleteForm');
        const modalElement = document.getElementById('deleteModal');
        
        if (!nameElement || !emailElement || !formElement || !modalElement) {
            console.error('Modal elements not found!');
            alert('Error: Modal elements not found. Please refresh the page.');
            return false;
        }
        
        nameElement.textContent = userName;
        emailElement.textContent = userEmail;
        formElement.action = '<?= base_url('admin/users/delete/') ?>' + userId;
        
        // Show the modal
        modalElement.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        console.log('Modal should be visible now');
        return false;
    } catch (error) {
        console.error('Error showing delete modal:', error);
        alert('Error showing delete confirmation. Please try again.');
        return false;
    }
}

function closeDeleteModal() {
    try {
        const modalElement = document.getElementById('deleteModal');
        if (modalElement) {
            modalElement.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
    } catch (error) {
        console.error('Error closing modal:', error);
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (modal && event.target === modal) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('deleteModal');
        if (modal && modal.style.display === 'flex') {
            closeDeleteModal();
        }
    }
});

// Make sure functions are available when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Delete modal functions loaded');
    
    // Add event listeners to all delete buttons as backup
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userEmail = this.getAttribute('data-user-email');
            
            if (userId && userName && userEmail) {
                showDeleteModal(userId, userName, userEmail);
            } else {
                console.error('Missing data attributes on delete button');
            }
            return false;
        });
    });
    
    console.log('Delete buttons initialized:', deleteButtons.length);
});
</script>

<style>
    .user-management-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-header h2 {
        color: #2c3e50;
        font-size: 2rem;
        margin: 0 0 0.5rem 0;
        font-weight: 700;
    }

    .page-header h2 i {
        color: #e74c3c;
        margin-right: 0.75rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
        font-size: 1rem;
    }

    .filter-section {
        margin-bottom: 1.5rem;
        background: #f8f9fa;
        padding: 1.25rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        max-width: 400px;
    }

    .filter-label {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .filter-label i {
        color: #3498db;
        font-size: 1rem;
    }

    .filter-select {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        color: #2c3e50;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 12px;
        padding-right: 2.5rem;
    }

    .filter-select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .filter-select:hover {
        border-color: #3498db;
    }

    .clear-filter-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .clear-filter-btn:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .table-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .table-header h3 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.25rem;
    }

    .user-count {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background: #f8f9fa;
    }

    .users-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #dee2e6;
    }

    .users-table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .users-table tbody tr:hover {
        background: #f8f9fa;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #3498db;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .role-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .role-admin {
        background: #fee;
        color: #c00;
    }

    .role-teacher {
        background: #efe;
        color: #060;
    }

    .role-student {
        background: #eef;
        color: #006;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        white-space: nowrap;
    }

    .action-btn i {
        font-size: 1rem;
    }

    .action-btn span {
        font-size: 0.9rem;
    }

    .action-btn-edit {
        background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        color: #ffffff;
        border: 1px solid #1976D2;
    }

    .action-btn-edit:hover {
        background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
    }

    .action-btn-edit:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(33, 150, 243, 0.3);
    }

    .action-btn-delete {
        background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        color: #ffffff;
        border: 1px solid #d32f2f;
    }

    .action-btn-delete:hover {
        background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
    }

    .action-btn-delete:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(244, 67, 54, 0.3);
    }

    .action-btn-restore {
        background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
        color: #ffffff;
        border: 1px solid #388e3c;
    }

    .action-btn-restore:hover {
        background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    }

    .action-btn-restore:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .action-icon.disabled {
        background: #f5f5f5;
        color: #9e9e9e;
        cursor: not-allowed;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .badge-you {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: #fff3cd;
        color: #856404;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state i {
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: #495057;
        margin: 1rem 0 0.5rem 0;
    }

    /* Deleted Users Section */
    .deleted-users-section {
        margin-top: 2rem;
        border-top: 2px solid #e9ecef;
        padding-top: 2rem;
    }

    .deleted-users-section .table-header h3 {
        color: #6c757d;
    }

    .deleted-users-section .table-header h3 i {
        color: #dc3545;
        margin-right: 0.5rem;
    }

    .deleted-users-table tbody tr {
        opacity: 0.7;
        background: #f8f9fa;
    }

    .deleted-users-table tbody tr:hover {
        opacity: 0.9;
        background: #e9ecef;
    }

    .deleted-row .deleted-name,
    .deleted-row .deleted-email {
        text-decoration: line-through;
        color: #6c757d;
    }

    .deleted-avatar {
        background: #6c757d !important;
        opacity: 0.7;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    /* Delete Modal Styles */
    .delete-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .delete-modal-content {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        animation: slideDown 0.3s;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .delete-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .delete-modal-title {
        margin: 0;
        color: #2c3e50;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .delete-modal-title i {
        color: #f44336;
        font-size: 1.5rem;
    }

    .delete-modal-close {
        background: none;
        border: none;
        font-size: 2rem;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .delete-modal-close:hover {
        background: #f5f5f5;
        color: #2c3e50;
    }

    .delete-modal-body {
        padding: 1.5rem;
    }

    .delete-modal-body p {
        margin-bottom: 1rem;
        color: #495057;
        line-height: 1.6;
    }

    .delete-warning-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 6px;
        padding: 1rem;
        margin: 1rem 0;
    }

    .delete-warning-box strong {
        color: #856404;
        display: block;
        margin-bottom: 0.5rem;
    }

    .delete-warning-box ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.5rem;
        color: #856404;
    }

    .delete-warning-box li {
        margin: 0.25rem 0;
    }

    .delete-warning-text {
        color: #f44336 !important;
        font-weight: 500;
        margin-top: 1rem;
        margin-bottom: 0 !important;
    }

    .delete-warning-text i {
        margin-right: 0.5rem;
    }

    .delete-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-danger {
        background: #f44336;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-danger:hover {
        background: #d32f2f;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .users-table {
            font-size: 0.9rem;
        }

        .users-table th,
        .users-table td {
            padding: 0.75rem 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
            gap: 0.5rem;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .action-btn span {
            font-size: 0.85rem;
        }

        .delete-modal-content {
            width: 95%;
            margin: 1rem;
        }

        .delete-modal-header,
        .delete-modal-body,
        .delete-modal-footer {
            padding: 1rem;
        }
    }
</style>

<?= $this->endSection() ?>

