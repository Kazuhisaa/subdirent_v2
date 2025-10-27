<nav class="navbar py-3 shadow-sm bg-white">
  <div class="container d-flex align-items-center justify-content-between flex-wrap">

    <!-- LEFT SIDE: Brand + Nav Links -->
    <div class="d-flex align-items-center flex-wrap gap-3">
      <!-- Brand -->
      <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
        <img src="{{ asset('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png') }}"
             alt="SubdiRent Icon" class="brand-icon">
        <img src="{{ asset('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png') }}"
             alt="SubdiRent Text" class="brand-text">
      </a>

      <!-- Nav Links (beside logo) -->
      <ul class="navbar-nav d-flex flex-row align-items-center gap-4 mb-0">
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('public.units') }}">Units</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#footer">Contact</a></li>
      </ul>
    </div>

    <!-- RIGHT SIDE: Login Button -->
    <div class="d-flex align-items-center mt-2 mt-md-0">
      <button
        type="button"
        class="btn btn-login-nav fw-semibold px-3 rounded-pill"
        data-bs-toggle="modal"
        data-bs-target="#loginModal">
        <i class="fas fa-user-circle"></i> <span>LOG IN</span>
      </button>
    </div>

  </div>
</nav>


<style>
/* Brand logo sizing */
.brand-icon {
  height: 34px;
  width: auto;
  object-fit: contain;
  transition: transform 0.3s ease;
}
.brand-text {
  height: 30px;
  width: auto;
  object-fit: contain;
  margin-left: 8px;
  transition: transform 0.3s ease;
}

/* Navbar base */
.navbar {
  position: sticky;
  top: 0;
  z-index: 1030;
  transition: box-shadow 0.3s ease;
}
.navbar.scrolled {
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Hover effect */
.navbar-brand:hover .brand-icon,
.navbar-brand:hover .brand-text {
  transform: scale(1.05);
}

/* Navbar links */
.nav-item .nav-link {
  color: #0A2540;
  transition: color 0.3s ease;
  font-size: 0.95rem;
}
.nav-item .nav-link:hover {
  color: #08355f;
}

/* Login Button */
.btn-login-nav {
  background-color: transparent;
  border: 1px solid transparent;
  color: #0A2540;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}
.btn-login-nav:hover {
  background-color: #055981ff;
  border-color: #CDEEFF;
  color: white;
}

/* Container size */
.navbar .container {
  max-width: 1200px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .navbar .container {
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
  }

  .navbar-nav {
    justify-content: center;
    flex-wrap: wrap;
  }

  .btn-login-nav {
    margin-top: 0.5rem;
  }
}

@media (max-width: 576px) {
  .brand-icon { height: 28px; }
  .brand-text { height: 24px; margin-left: 6px; }
  .nav-item .nav-link { font-size: 0.9rem; }
}

</style>
<script>
    window.addEventListener("scroll", () => {
        const navbar = document.querySelector(".navbar");
        navbar.classList.toggle("scrolled", window.scrollY > 20);
    });
</script>