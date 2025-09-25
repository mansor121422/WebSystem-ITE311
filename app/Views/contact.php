<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card">
    <h1 class="text-center">Contact Us</h1>
    <p class="text-center">Get in touch with our team for any questions or support</p>
    
    <h2>Contact Information</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1rem 0;">
        <div>
            <h3>Address</h3>
            <p>
                Alabel<br>
                Sarangani province<br>
                Philippines
            </p>
        </div>
        
        <div>
            <h3>Phone</h3>
            <p>
                +63 123 456 7890<br>
                +63 987 654 3210<br>
                Mon-Fri: 9AM-6PM
            </p>
        </div>
        
        <div>
            <h3>Email</h3>
            <p>
                info@lmssystem.com<br>
                support@lmssystem.com<br>
                sales@lmssystem.com
            </p>
        </div>
    </div>
    
    <h2>Send us a Message</h2>
    <form>
        <div class="form-group">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" required>
        </div>
        
        <div class="form-group">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" required>
        </div>
        
        <div class="form-group">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" required>
        </div>
        
        <div class="form-group">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" rows="5" required></textarea>
        </div>
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
    </form>
    
    <div class="text-center mt-3">
        <a href="<?= base_url('/') ?>" class="btn">Back to Home</a>
        <a href="<?= base_url('/about') ?>" class="btn">About Us</a>
    </div>
</div>
<?= $this->endSection() ?>
