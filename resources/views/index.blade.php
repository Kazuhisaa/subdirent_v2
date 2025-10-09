<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SubdiRent</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <!-- Your Custom CSS -->
  <link rel="stylesheet" href="{{ asset('../css/homepage.css') }}">
</head>
<body>

<!-- 🌐 Navbar -->
<nav class="navbar navbar-expand-lg py-3 shadow-sm">
  <div class="container d-flex align-items-center justify-content-between">
    <a class="navbar-brand d-flex align-items-center me-4" href="#">
      <div class="temp-logo me-2"></div>
    </a>

    <div class="d-none d-lg-flex align-items-center flex-grow-1">
      <ul class="navbar-nav gap-4 ms-2">
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('units') }}">Units</a></li>
        <!-- 👇 Updated Contact link to scroll to footer -->
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#contact">Contact</a></li>
      </ul>
    </div>



    <div class="d-flex align-items-center gap-3">

        <!-- Login modal trigger -->
        @guest
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="btn btn-link text-decoration-none">
                Login
            </a>
        @endguest

       
        @include('admin.login') 

        <!-- Get Started button -->
        <a href="#" class="btn btn-primary fw-semibold px-3 rounded-pill">Get Started</a>

        <!-- Logout button -->
        @auth
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-decoration-none">Logout</button>
            </form>
        @endauth
    </div>

</nav>

<!-- 🚗 HERO SECTION -->
<section class="hero">
  <div class="hero-text">
    <h1>SubdiRent</h1>
    <p>Subdirent is a property management platform designed to make renting easier for tenants and property owners. Manage tenants, payments, and units — all in one place.</p>
    <a href="#" class="btn btn-primary btn-lg rounded-pill px-4 py-2">Get Started</a>
  </div>

  <!-- 🔹 Image Slider -->
  <div class="slider">
    <img src="https://picsum.photos/id/1005/600/400" class="active" alt="Slide 1">
    <img src="https://picsum.photos/id/1011/600/400" alt="Slide 2">
    <img src="https://picsum.photos/id/1025/600/400" alt="Slide 3">
    <button class="slider-btn prev"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-btn next"><i class="fas fa-chevron-right"></i></button>
  </div>
</section>

<!-- FEATURES -->
<section class="features py-5 text-center">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <h5 class="fw-bold">Faster approvals</h5>
        <p>Streamlined tenant screening and digital forms.</p>
      </div>
      <div class="col-md-4">
        <h5 class="fw-bold">Clear payments</h5>
        <p>Automated reminders and unified receipts.</p>
      </div>
      <div class="col-md-4">
        <h5 class="fw-bold">Reliable records</h5>
        <p>All unit activity in a single timeline.</p>
      </div>
    </div>
  </div>
</section>

<!-- 👇 FOOTER with ID for scroll target -->
<footer id="contact" class="py-5 border-top">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-3">
        <div class="d-flex align-items-center mb-2">
          <div class="logo-circle me-2"></div>
          <span class="fw-bold fs-5">Subdirent</span>
        </div>
        <p class="small text-muted">Subdirent is a property management platform designed to make renting easier for tenants and property owners.</p>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Our Location</h6>
        <p class="small text-muted mb-1">Pueblo de Oro Development Corporation<br>17th Floor Robinsons Summit Center,<br>6783 Ayala Avenue, Makati City 1226,<br>Philippines</p>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">About Us</h6>
        <p class="small text-muted">We simplify tenant management, unit booking, and payments all in one place.</p>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold mb-2">Contact Us</h6>
        <p class="small mb-1"><strong>Head Office:</strong> +63 (2) 8790-2200</p>
        <p class="small"><strong>Cebu:</strong> +63 (32) 888-6146</p>
      </div>
    </div>
    <div class="d-flex justify-content-between pt-4 mt-4 border-top small text-muted">
      <p class="mb-0">© 2025 Subdirent. All rights reserved.</p>
      <p class="mb-0"><a href="#" class="text-muted text-decoration-none me-3">Terms</a> <a href="#" class="text-muted text-decoration-none">Privacy</a></p>
    </div>
  </div>
</footer>

<script>
  // Simple Image Slider
  const slides = document.querySelectorAll('.slider img');
  const prev = document.querySelector('.prev');
  const next = document.querySelector('.next');
  let index = 0;

  function showSlide(i) {
    slides.forEach(s => s.classList.remove('active'));
    slides[i].classList.add('active');
  }

  next.addEventListener('click', () => {
    index = (index + 1) % slides.length;
    showSlide(index);
  });

  prev.addEventListener('click', () => {
    index = (index - 1 + slides.length) % slides.length;
    showSlide(index);
  });

  // Auto-slide
  setInterval(() => {
    index = (index + 1) % slides.length;
    showSlide(index);
  }, 5000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
