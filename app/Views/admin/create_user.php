<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="create-user-page">
    <div class="page-header">
        <a href="<?= base_url('admin/users') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <h2>
            <i class="fas fa-user-plus"></i> Create New User
        </h2>
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

    <div class="form-container">
        <form method="post" action="<?= base_url('admin/users/create') ?>" class="user-form">
            <div class="form-group">
                <label for="name" class="form-label">
                    <i class="fas fa-user"></i> Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="<?= set_value('name') ?>" 
                       pattern="[a-zA-Z\s]+"
                       title="Only letters and spaces are allowed. No numbers or special characters."
                       required>
                <small class="form-text">Letters and spaces only. No numbers, special characters, or symbols.</small>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email <span class="required">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       value="<?= set_value('email') ?>" 
                       required>
                <small class="form-text">Enter a valid email address.</small>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password <span class="required">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       required>
                <small class="form-text">Minimum 8 characters with letters and numbers. No / \ " ' < > characters.</small>
            </div>

            <div class="form-group">
                <label for="role" class="form-label">
                    <i class="fas fa-user-tag"></i> Role <span class="required">*</span>
                </label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="admin" <?= set_value('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="teacher" <?= set_value('role') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                    <option value="student" <?= set_value('role') == 'student' ? 'selected' : '' ?>>Student</option>
                </select>
                <small class="form-text">Select the user's role in the system.</small>
            </div>

            <div class="form-actions">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Real-time validation for name field - only allow letters and spaces
document.getElementById('name').addEventListener('input', function(e) {
    // Remove any characters that are not letters or spaces
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

// Prevent pasting invalid characters
document.getElementById('name').addEventListener('paste', function(e) {
    e.preventDefault();
    const paste = (e.clipboardData || window.clipboardData).getData('text');
    // Only allow letters and spaces
    const cleaned = paste.replace(/[^a-zA-Z\s]/g, '');
    this.value = cleaned;
});

// Validate on form submission
document.querySelector('.user-form').addEventListener('submit', function(e) {
    const nameField = document.getElementById('name');
    const nameValue = nameField.value.trim();
    
    // Check if name is empty or contains only spaces
    if (!nameValue || nameValue.length === 0) {
        e.preventDefault();
        alert('Name is required and must contain at least one letter.');
        nameField.focus();
        return false;
    }
    
    // Check if name contains only letters and spaces
    if (!/^[a-zA-Z\s]+$/.test(nameValue)) {
        e.preventDefault();
        alert('Name must contain only letters and spaces. No numbers or special characters allowed.');
        nameField.focus();
        return false;
    }
    
    // Check for multiple consecutive spaces
    if (/\s{2,}/.test(nameValue)) {
        e.preventDefault();
        alert('Name cannot contain multiple consecutive spaces.');
        nameField.focus();
        return false;
    }
});
</script>

<style>
    .create-user-page {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        text-decoration: none;
        margin-bottom: 1rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #3498db;
    }

    .page-header h2 {
        color: #2c3e50;
        font-size: 1.75rem;
        margin: 0;
        font-weight: 600;
    }

    .page-header h2 i {
        color: #e74c3c;
        margin-right: 0.5rem;
    }

    .form-container {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .user-form {
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .form-label i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .required {
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-text {
        display: block;
        margin-top: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
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
</style>

<?= $this->endSection() ?>

