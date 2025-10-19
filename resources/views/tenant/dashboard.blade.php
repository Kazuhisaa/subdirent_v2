<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'Tenant Dashboard')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  @vite([
      'resources/bootstrap/css/bootstrap.css',
      'resources/css/tenant.css',
      'resources/bootstrapjs/js/bootstrap.bundle.js',
      'resources/js/app.js'
  ])
</head>
<body>
  <div class="d-flex tenant-root">

    <!-- SIDEBAR -->
    <nav id="sidebar" class="vh-100 bg-white border-end">
      <div class="sidebar-brand p-3 border-bottom">
        <h5 class="mb-0 d-flex align-items-center">
          <i class="bi bi-house-heart me-2 text-teal"></i>
          <span class="fw-bold text-teal">SubdiRent</span>
        </h5>
        <small class="text-muted">TENANT PORTAL</small>
      </div>

      <ul class="nav flex-column px-2 py-3">
        <!-- Dashboard -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('tenant.home') ? 'active' : '' }}" href="{{ route('tenant.home') }}">
            <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
          </a>
        </li>

        <!-- Divider -->
        <li class="nav-divider mt-3 mb-1 text-uppercase small px-2 text-muted fw-bold">
          <span>My Account</span>
        </li>

        <!-- My Property -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('tenant.property') ? 'active' : '' }}" href="{{ route('tenant.property') }}">
            <i class="bi bi-building-check me-2"></i> <span>My Property</span>
          </a>
        </li>

        <!-- My Payments -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('tenant.payments') ? 'active' : '' }}" href="{{ route('tenant.payments') }}">
            <i class="bi bi-wallet2 me-2"></i> <span>My Payments</span>
          </a>
        </li>

        <!-- Maintenance Requests -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center" href="#">
            <i class="bi bi-tools me-2"></i> <span>Maintenance Requests</span>
          </a>
        </li>

        <!-- My Account -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center" href="#">
            <i class="bi bi-person-circle me-2"></i> <span>My Account</span>
          </a>
        </li>

        <!-- Property Search -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center" href="#">
            <i class="bi bi-search-heart me-2"></i> <span>Property Search</span>
          </a>
        </li>

        <!-- Support -->
        <li class="nav-item mb-2">
          <a class="nav-link d-flex align-items-center" href="#">
            <i class="bi bi-headset me-2"></i> <span>Support</span>
          </a>
        </li>

        <!-- Logout -->
        <li class="nav-item mt-auto p-3 border-top">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
              <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 min-vh-100 bg-light">
      <header class="d-flex justify-content-between align-items-center px-4 py-2 border-bottom bg-white">
        <div>
          <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-list"></i>
          </button>
          <span class="h6 mb-0">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center">
          <small class="text-muted">Welcome, {{ Auth::user()->name ?? 'Tenant' }}</small>
        </div>
      </header>

      <main class="p-4">
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    document.getElementById('toggleSidebar')?.addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
  </script>
</body>
</html>
