<nav class="navbar navbar-expand-lg py-3 shadow-sm bg-white">
  <div class="container d-flex align-items-center justify-content-between">
    <a class="navbar-brand d-flex align-items-center me-4" href="{{ route('home') }}">
      <div class="temp-logo me-2" style="width:34px;height:34px;border-radius:50%;background-color:#0A2540;"></div>
      <span class="fw-bold">SubdiRent</span>
    </a>

    <div class="d-none d-lg-flex align-items-center flex-grow-1">
      <ul class="navbar-nav gap-4 ms-2">
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('public.units') }}">Units</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#contact">Contact</a></li>
      </ul>
    </div>

    <div class="d-flex align-items-center gap-3">
      <a href="#" class="text-dark fw-semibold text-uppercase small text-decoration-none">Sign In</a>
      <button 
  type="button" 
  class="btn btn-primary fw-semibold px-3 rounded-pill"
  data-bs-toggle="modal" 
  data-bs-target="#loginModal">
  LOG IN
</button>
    </div>
  </div>
</nav>
