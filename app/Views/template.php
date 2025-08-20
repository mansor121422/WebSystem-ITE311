<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CODEIGNITER</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-success">
    <div class="container-fluid">
      <a class="navbar-brand text-white" href="#">CODEIGNITER</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link text-white" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#products">Products</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#services">Services</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#blog">Blog</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="#contact">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="container-fluid p-5 text-center" style="background-color: #a5d6a7;">
    <img src="https://via.placeholder.com/900x200?text=Eco+Banner" class="img-fluid mb-3" alt="Banner">
  <h1>Welcome to CodeIgniter</h1>
    <p>Your Partner in Sustainable Living</p>
    <p>We help you live eco-friendly!</p>
    <a href="#products" class="btn btn-primary">Shop Now</a>
  </div>

  <!-- Introduction -->
  <div class="container mt-5" id="about">
    <h2>About Us</h2>
    <p>GreenEarth Solutions is a company that sells eco-friendly products. We started in 2010 and want to help the planet. We care about sustainability and the environment.</p>
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/100" class="rounded mb-2" alt="icon1">
        <p>Eco Products</p>
      </div>
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/100" class="rounded mb-2" alt="icon2">
        <p>Green Living</p>
      </div>
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/100" class="rounded mb-2" alt="icon3">
        <p>Community</p>
      </div>
    </div>
  </div>

  <!-- Featured Products -->
  <div class="container mt-5" id="products">
    <h2>Featured Products</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="card mb-3">
          <img src="https://via.placeholder.com/200" class="card-img-top" alt="Product1">
          <div class="card-body">
            <h5 class="card-title">Reusable Bottle</h5>
            <p class="card-text">A bottle you can use again and again.</p>
            <p>$10.00</p>
            <a href="#" class="btn btn-success">View</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-3">
          <img src="https://via.placeholder.com/200" class="card-img-top" alt="Product2">
          <div class="card-body">
            <h5 class="card-title">Bamboo Toothbrush</h5>
            <p class="card-text">A toothbrush made from bamboo.</p>
            <p>$3.00</p>
            <a href="#" class="btn btn-success">View</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-3">
          <img src="https://via.placeholder.com/200" class="card-img-top" alt="Product3">
          <div class="card-body">
            <h5 class="card-title">Cotton Tote Bag</h5>
            <p class="card-text">A bag made from cotton.</p>
            <p>$5.00</p>
            <a href="#" class="btn btn-success">View</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Services Overview -->
  <div class="container mt-5" id="services">
    <h2>Our Services</h2>
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/80" class="mb-2" alt="service1">
        <p>Sustainability Consulting</p>
      </div>
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/80" class="mb-2" alt="service2">
        <p>Recycling Programs</p>
      </div>
      <div class="col-md-4 text-center">
        <img src="https://via.placeholder.com/80" class="mb-2" alt="service3">
        <p>Eco Workshops</p>
      </div>
    </div>
  </div>

  <!-- Testimonials -->
  <div class="container mt-5" id="testimonials">
    <h2>Testimonials</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="card p-2 mb-2">
          <p>"I love their products!"</p>
          <small>- Jane</small>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-2 mb-2">
          <p>"Helped my business go green."</p>
          <small>- Mark</small>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-2 mb-2">
          <p>"Great workshops!"</p>
          <small>- Lisa</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Blog Highlights -->
  <div class="container mt-5" id="blog">
    <h2>Blog</h2>
    <ul>
      <li><a href="#">How to Recycle at Home</a></li>
      <li><a href="#">Why Eco Products Matter</a></li>
      <li><a href="#">Tips for Green Living</a></li>
    </ul>
  </div>

  <!-- Newsletter Sign-Up -->
  <div class="container mt-5" id="newsletter">
    <h2>Newsletter</h2>
    <form>
      <input type="email" placeholder="Enter your email" class="form-control mb-2" style="max-width:300px;display:inline-block;">
      <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>
    <p>Get updates and tips!</p>
  </div>

  <!-- Footer -->
  <footer class="bg-success text-white text-center p-3 mt-5">
    <p>&copy; 2024 GreenEarth Solutions</p>
    <p>
      <a href="#about" class="text-white">About</a> |
      <a href="#products" class="text-white">Products</a> |
      <a href="#services" class="text-white">Services</a> |
      <a href="#blog" class="text-white">Blog</a> |
      <a href="#contact" class="text-white">Contact</a>
    </p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>