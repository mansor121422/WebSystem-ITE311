<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="announcements-page">
    <div class="page-header mb-4">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>
                <i class="fas fa-bullhorn"></i> Announcements
            </h2>
            <?php if (isset($user['role']) && strtolower($user['role']) === 'admin'): ?>
                <a href="<?= base_url('announcements/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Announcement
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Announcements List -->
    <?php if (!empty($announcements) && is_array($announcements)): ?>
        <div class="announcements-list">
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card card mb-4">
                    <div class="card-body">
                        <div class="announcement-header mb-3">
                            <h4 class="announcement-title">
                                <i class="fas fa-megaphone text-primary"></i>
                                <?= esc($announcement['title']) ?>
                            </h4>
                            <div class="announcement-meta text-muted">
                                <small>
                                    <i class="fas fa-calendar-alt"></i>
                                    Posted on: <?= date('F j, Y, g:i A', strtotime($announcement['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="announcement-content">
                            <p><?= nl2br(esc($announcement['content'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Announcements Yet</h4>
                <p class="text-muted">There are no announcements to display at this time.</p>
                <?php if (isset($user['role']) && strtolower($user['role']) === 'admin'): ?>
                    <a href="<?= base_url('announcements/create') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Create First Announcement
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .announcements-page {
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header h2 {
        color: #2c3e50;
        margin: 0;
    }

    .announcement-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #3498db;
    }

    .announcement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .announcement-title {
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
    }

    .announcement-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .announcement-content {
        color: #555;
        line-height: 1.8;
        font-size: 1rem;
    }

    .announcement-content p {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .page-header > div {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .announcement-title {
            font-size: 1.25rem;
        }
    }
</style>

<!-- Font Awesome for icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?= $this->endSection() ?>

