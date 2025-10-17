<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="admin-dashboard-page">
    <div class="welcome-card">
        <div class="welcome-header">
            <h1>
                <i class="fas fa-user-shield"></i>
                Welcome, Admin!
            </h1>
            <p class="welcome-subtitle">Administrator Dashboard - <?= esc($user['name']) ?></p>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-info">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="info-content">
                    <h3>Your Information</h3>
                    <p><strong>Name:</strong> <?= esc($user['name']) ?></p>
                    <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
                    <p><strong>Role:</strong> <?= ucfirst(esc($user['role'])) ?></p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="info-content">
                    <h3>Quick Access</h3>
                    <div class="quick-links">
                        <a href="<?= base_url('announcements') ?>" class="btn btn-primary">
                            <i class="fas fa-bullhorn"></i> View Announcements
                        </a>
                        <a href="<?= base_url('announcements/create') ?>" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Announcement
                        </a>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">
                            <i class="fas fa-th-large"></i> Full Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-message">
            <div class="message-box admin-message">
                <i class="fas fa-crown"></i>
                <p>Welcome to the Administrator Dashboard! You have full access to manage users, courses, announcements, and system settings.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-dashboard-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .welcome-card {
        background: white;
        border-radius: 12px;
        padding: 3rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .welcome-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #e9ecef;
    }

    .welcome-header h1 {
        color: #2c3e50;
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .welcome-header i {
        color: #e74c3c;
        margin-right: 1rem;
    }

    .welcome-subtitle {
        color: #6c757d;
        font-size: 1.2rem;
        margin: 0;
    }

    .dashboard-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 2rem;
        border-left: 4px solid #e74c3c;
        display: flex;
        gap: 1.5rem;
    }

    .info-icon {
        font-size: 3rem;
        color: #e74c3c;
    }

    .info-content {
        flex: 1;
    }

    .info-content h3 {
        color: #2c3e50;
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }

    .info-content p {
        color: #555;
        margin: 0.5rem 0;
        line-height: 1.6;
    }

    .quick-links {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .quick-links .btn {
        width: 100%;
        text-align: left;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dashboard-message {
        margin-top: 2rem;
    }

    .message-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .message-box.admin-message {
        background: #f8d7da;
        border-color: #e74c3c;
    }

    .message-box.admin-message i {
        color: #e74c3c;
    }

    .message-box i {
        font-size: 1.5rem;
        color: #ffc107;
    }

    .message-box p {
        margin: 0;
        color: #2c3e50;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 2rem 1.5rem;
        }

        .welcome-header h1 {
            font-size: 2rem;
        }

        .dashboard-info {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .info-card {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

