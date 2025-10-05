<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SubdiRent | Automated Parking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    :root {
      /* 🎨 BLUE COLOR PALETTE */
      --blue-900: #0A2540;
      --blue-800: #0D3B66;
      --blue-700: #145DA0;
      --blue-600: #1E81CE;
      --blue-500: #2A9DF4;
      --blue-400: #5AB8F0;
      --blue-300: #9FD8F7;
      --blue-200: #CDEEFF;
      --blue-100: #EAF8FF;

      /* 🌈 Gradient Presets */
      --gradient-horizontal: linear-gradient(90deg, var(--blue-700), var(--blue-600), var(--blue-500));
      --gradient-diagonal: linear-gradient(135deg, var(--blue-700), var(--blue-600), var(--blue-500));
    }

    body {
      font-family: "Segoe UI", sans-serif;
      color: var(--blue-900);
      background-color: var(--blue-100);
    }

    /* Navbar */
    .navbar {
      background-color: #fff;
      border-bottom: 1px solid var(--blue-200);
    }
    .navbar-nav .nav-link {
      color: var(--blue-900);
      font-weight: 500;
      letter-spacing: 0.4px;
      transition: 0.3s;
    }
    .navbar-nav .nav-link:hover { color: var(--blue-600); }

    /* Logo */
    .temp-logo {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background-color: var(--blue-900);
    }

    /* Hero */
    .hero {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 100px 10%;
      min-height: 80vh;
    }
    .hero-text {
      max-width: 520px;
      animation: fadeIn 1s ease;
    }
    .hero-text h1 {
      font-size: 3rem;
      font-weight: 800;
      color: var(--blue-900);
    }
    .hero-text p {
      color: var(--blue-700);
      font-size: 1.1rem;
      margin: 20px 0 30px;
      line-height: 1.7;
    }

    /* Button */
    .btn-primary {
      background: var(--gradient-diagonal);
      border: none;
      transition: 0.3s;
    }
    .btn-primary:hover { filter: brightness(1.1); }

    /* 🔵 IMAGE SLIDER */
    .slider {
      position: relative;
      width: 420px;
      height: 300px;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .slider img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: none;
    }
    .slider img.active { display: block; animation: fadeIn 0.7s ease; }

    .slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.8);
      color: var(--blue-700);
      border: none;
      font-size: 1.5px;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      cursor: pointer;
      transition: 0.3s;
    }
    .slider-btn:hover { background: var(--blue-500); color: black; }
    .slider-btn.prev { left: 3px; }
    .slider-btn.next { right: 3px; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 992px) {
      .hero {
        flex-direction: column;
        text-align: center;
      }
      .slider {
        margin-top: 40px;
        width: 90%;
        height: 220px;
      }
    }

    /* Footer */
    footer {
      background-color: #fff;
      color: var(--blue-900);
    }

    footer h6 {
      color: var(--blue-900);
    }

    footer a {
      color: var(--blue-700);
      text-decoration: none;
    }

    footer a:hover {
      color: var(--blue-500);
    }

    footer .text-muted {
      color: var(--blue-600) !important;
    }

    .logo-circle {
      width: 28px;
      height: 28px;
      background-color: var(--blue-900);
      border-radius: 50%;
    }
    html {
  scroll-behavior: smooth;
}
  </style>
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
