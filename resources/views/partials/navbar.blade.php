<nav class="navbar navbar-expand-lg py-3 shadow-sm">
  <div class="container d-flex align-items-center justify-content-between">
    <a class="navbar-brand d-flex align-items-center me-4" href="#">
      <div class="temp-logo me-2"></div>
    </a>

    <div class="d-none d-lg-flex align-items-center flex-grow-1">
      <ul class="navbar-nav gap-4 ms-2">
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="{{ route('units') }}">Units</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold text-uppercase" href="#contact">Contact</a></li>
      </ul>
    </div>

    <div class="d-flex align-items-center gap-3">
      @guest
        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="btn btn-link text-decoration-none">
          Login
        </a>
      @endguest

      @include('admin.login')

      <a href="#" class="btn btn-primary fw-semibold px-3 rounded-pill">Get Started</a>

      @auth
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-link text-decoration-none">Logout</button>
        </form>
      @endauth
    </div>
  </div>
</nav>
