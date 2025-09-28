<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Welcome - SubdiRent</title>

  @vite([
      'resources/bootstrap/css/bootstrap.css',
      'resources/css/app.css',
      'resources/bootstrapjs/js/bootstrap.bundle.js'
  ])
</head>
<body class="bg-light">

  <!-- NAVBAR -->
  <header class="bg-dark text-white">
    <nav class="container d-flex justify-content-between align-items-center py-3">
      <h5 class="mb-0">SubdiRent</h5>
      <div>
        <a href="{{ url('/') }}" class="text-white me-3">Home</a>
        <a href="#about" class="text-white me-3">About</a>
        <a href="#rooms" class="text-white me-3">Rooms</a>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
      </div>
    </nav>
  </header>

  <!-- HERO -->
  <section class="text-center py-5 bg-white">
    <div class="container">
      <h1 class="display-5">Find Your Perfect Unit</h1>
      <p class="lead">Safe, affordable, and convenient subdivision rentals.</p>
      <a href="#rooms" class="btn btn-primary">Rent Now</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about" class="container py-5">
    <h2 class="mb-3">About Us</h2>
    <p>We make renting subdivision units simple and transparent. Browse available properties and connect with trusted landlords.</p>
  </section>

  <!-- ROOMS -->
  <section id="rooms" class="container py-5">
    <h2 class="mb-4">Available Units</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="/images/sample1.jpg" class="card-img-top" alt="Unit 1">
          <div class="card-body">
            <h5 class="card-title">Unit A</h5>
            <p class="card-text">2 Bedroom, 1 Bath, near the clubhouse.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="/images/sample2.jpg" class="card-img-top" alt="Unit 2">
          <div class="card-body">
            <h5 class="card-title">Unit B</h5>
            <p class="card-text">1 Bedroom, modern design, with balcony.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; {{ date('Y') }} SubdiRent. All rights reserved.</p>
  </footer>
</body>
</html>
