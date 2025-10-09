<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="text-center">
    <h1>Welcome to LMS System</h1>
    <p>A simple Learning Management System for education</p>
    
    <div class="mt-3">
        <a href="<?= base_url('about') ?>" class="btn">About</a>
        <a href="<?= base_url('contact') ?>" class="btn">Contact</a>
    </div>
</div>
<?= $this->endSection() ?>