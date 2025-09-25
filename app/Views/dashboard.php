<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card">
    <h2 class="text-center">Dashboard</h2>
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <div class="text-center">
        <h3>Hello, <?= session('user_name') ?>!</h3>
        <p><strong>Email:</strong> <?= session('user_email') ?></p>
        <p><strong>Role:</strong> <?= session('role') ?></p>
        
        <div class="mt-3">
            <a href="<?= site_url('logout') ?>" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
