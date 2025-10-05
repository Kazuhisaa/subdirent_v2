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
      <div class="sidebar-brand p-3">
        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i><span>SubdiRent</span></h5>
        <small><span>TENANT</span></small>
      </div>

      <ul class="nav flex-column px-2">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.home') ? 'active' : '' }}" href="{{ route('tenant.home') }}">
            <i class="bi bi-house-door"></i> <span>Dashboard</span>
          </a>
        </li>

        <li class="nav-divider mt-3 mb-1 text-uppercase small px-2"><span>My Account</span></li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-file-earmark-text"></i> <span>My Applications</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-calendar-check"></i> <span>My Bookings</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-credit-card"></i> <span>My Payments</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-tools"></i> <span>Maintenance Requests</span>
          </a>
        </li>
        <li class="nav-item mt-auto p-3">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
              <i class="bi bi-box-arrow-right"></i> Logout
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
