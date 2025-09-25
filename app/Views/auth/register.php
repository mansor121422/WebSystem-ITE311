<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card">
    <h2 class="text-center">Register</h2>
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="post">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= set_value('name') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    
    <div class="text-center mt-3">
        <a href="<?= site_url('login') ?>">Already have an account? Login</a>
    </div>
</div>
<?= $this->endSection() ?>
