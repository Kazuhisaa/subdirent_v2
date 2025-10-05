<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Available Units | Subdirent</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    :root {
      /* 🎨 BLUE COLOR PALETTE (from homepage) */
      --blue-900: #0A2540;
      --blue-800: #0D3B66;
      --blue-700: #145DA0;
      --blue-600: #1E81CE;
      --blue-500: #2A9DF4;
      --blue-400: #5AB8F0;
      --blue-300: #9FD8F7;
      --blue-200: #CDEEFF;
      --blue-100: #EAF8FF;

      /* 🌈 Gradient */
      --gradient-diagonal: linear-gradient(135deg, var(--blue-700), var(--blue-600), var(--blue-500));
    }

    body {
      font-family: "Segoe UI", sans-serif;
      color: var(--blue-900);
      background-color: var(--blue-100);
    }

    /* 🌐 NAVBAR (copied from homepage) */
    .navbar {
      background-color: #fff;
      border-bottom: 1px solid var(--blue-200);
    }

    .navbar-nav .nav-link {
      color: var(--blue-900);
      font-weight: 500;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      transition: 0.3s;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
      color: var(--blue-600);
    }

    .temp-logo {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background-color: var(--blue-900);
    }

    .btn-primary {
      background: var(--gradient-diagonal);
      border: none;
      transition: 0.3s;
      border-radius: 50px;
    }

    .btn-primary:hover {
      filter: brightness(1.1);
    }

    /* 🏠 SECTION TITLE */
    .section-header h2 {
      font-weight: 800;
      color: var(--blue-900);
    }

    .section-header p {
      color: var(--blue-700);
      font-size: 1rem;
    }

    /* 🧱 UNIT CARDS */
    .unit-card {
      border: none;
      border-radius: 18px;
      overflow: hidden;
      background-color: #fff;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .unit-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 28px rgba(0,0,0,0.12);
    }

    .unit-img {
      height: 210px;
      object-fit: cover;
    }

    .card-title {
      color: var(--blue-800);
      font-weight: 700;
    }

    .price {
      color: var(--blue-600);
      font-weight: 700;
      font-size: 1.05rem;
    }

    .badge-status {
      background-color: var(--blue-200);
      color: var(--blue-800);
      font-weight: 600;
      border-radius: 50px;
      font-size: 0.85rem;
      padding: 0.35rem 0.9rem;
    }

    /* Buttons */
    .btn-outline-primary {
      border-color: var(--blue-600);
      color: var(--blue-600);
      font-weight: 600;
      border-radius: 30px;
      transition: 0.3s;
    }

    .btn-outline-primary:hover {
      background: var(--gradient-diagonal);
      color: #fff;
      border: none;
    }

    .btn-primary {
      background: var(--gradient-diagonal);
      border: none;
      border-radius: 30px;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-primary:hover {
      filter: brightness(1.1);
    }

    .view-all {
      border-radius: 30px;
      padding: 0.55rem 1.3rem;
      font-weight: 600;
      border-color: var(--blue-600);
      color: var(--blue-600);
      background-color: #fff;
      transition: 0.3s;
    }

    .view-all:hover {
      background: var(--gradient-diagonal);
      color: #fff;
      border: none;
    }

    /* FOOTER */
    footer {
      background-color: #fff;
      color: var(--blue-900);
      border-top: 1px solid var(--blue-200);
      margin-top: 4rem;
      padding: 2rem 0;
    }

    footer .text-muted {
      color: var(--blue-600) !important;
    }
  </style>
</head>
<body>

  <!-- 🌐 NAVBAR (now identical to homepage) -->
  <nav class="navbar navbar-expand-lg py-3 shadow-sm">
    <div class="container d-flex align-items-center justify-content-between">
      <a class="navbar-brand d-flex align-items-center me-4" href="#">
        <div class="temp-logo me-2"></div>
      </a>

      <div class="d-none d-lg-flex align-items-center flex-grow-1">
        <ul class="navbar-nav gap-4 ms-2">
          <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-uppercase active" href="#">Units</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Contact</a></li>
        </ul>
      </div>

      <div class="d-flex align-items-center gap-3">
        <a href="#" class="text-dark fw-semibold text-uppercase small text-decoration-none">Sign In</a>
        <a href="#" class="btn btn-primary fw-semibold px-3 rounded-pill">Get Started</a>
      </div>
    </div>
  </nav>

  <!-- 🏡 AVAILABLE UNITS -->
  <section class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4 section-header">
        <div>
          <h2>Available Units</h2>
          <p>Browse a curated list of ready-to-rent properties.</p>
        </div>
        <button class="btn view-all">View all</button>
      </div>

      <div class="row g-4">
        <!-- 🔹 Unit Card -->
        <div class="col-md-6 col-lg-4">
          <div class="card unit-card">
            <img src="https://picsum.photos/600/400?random=1" class="unit-img" alt="Unit Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h5 class="card-title">Azure Breeze Villa</h5>
                  <p class="text-muted small mb-0">Phase 2</p>
                </div>
                <span class="badge badge-status">For Rent</span>
              </div>
              <p class="price mb-2">₱25,000.00/month</p>
              <p class="text-muted small mb-3">
                A coastal-inspired home designed for everyday relaxation. Experience open-plan comfort and natural airflow.
              </p>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary w-50">Reserve Unit</button>
                <button class="btn btn-primary w-50">Apply</button>
              </div>
            </div>
          </div>
        </div>

        <!-- 🔹 Duplicate Cards -->
        <div class="col-md-6 col-lg-4">
          <div class="card unit-card">
            <img src="https://picsum.photos/600/400?random=2" class="unit-img" alt="Unit Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h5 class="card-title">Palmview Residences</h5>
                  <p class="text-muted small mb-0">Block 5</p>
                </div>
                <span class="badge badge-status">For Rent</span>
              </div>
              <p class="price mb-2">₱20,000.00/month</p>
              <p class="text-muted small mb-3">
                Modern minimalist living with a serene community ambiance and close proximity to amenities.
              </p>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary w-50">Reserve Unit</button>
                <button class="btn btn-primary w-50">Apply</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card unit-card">
            <img src="https://picsum.photos/600/400?random=3" class="unit-img" alt="Unit Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h5 class="card-title">Hillcrest Haven</h5>
                  <p class="text-muted small mb-0">Phase 4</p>
                </div>
                <span class="badge badge-status">For Rent</span>
              </div>
              <p class="price mb-2">₱28,000.00/month</p>
              <p class="text-muted small mb-3">
                Spacious two-bedroom unit with private balcony views and modern interior finishings.
              </p>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary w-50">Reserve Unit</button>
                <button class="btn btn-primary w-50">Apply</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ⚪ FOOTER -->
  <footer>
    <div class="container text-center small text-muted">
      © 2025 Subdirent. All rights reserved.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
