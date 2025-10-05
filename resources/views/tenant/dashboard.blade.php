{{-- resources/views/tenant/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'SubdiRent Tenant')</title>

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
    <nav id="tenant-sidebar" class="vh-100">
      <div class="sidebar-brand p-3">
        <h5 class="mb-0">SubdiRent</h5>
        <small>TENANT</small>
      </div>

      <ul class="nav flex-column px-2">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">My Bookings</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Payments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Contracts</a>
        </li>

        <li class="nav-divider mt-3 mb-1 text-uppercase small px-2">Support</li>
        <li class="nav-item px-2">
          <a class="nav-link" href="#">Maintenance Requests</a>
        </li>

        <li class="mt-auto p-3">
          <a class="nav-link" href="{{ route('logout') }}">Logout</a>
        </li>
      </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 tenant-content">
      <!-- Topbar -->
      <header class="tenant-header d-flex justify-content-between align-items-center px-4 py-2">
        <div>
          <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-2">☰</button>
          <span class="h6 mb-0">@yield('page-title','Tenant Dashboard')</span>
        </div>
        <div class="d-flex align-items-center">
          <div class="me-3">
            <small class="text-muted">{{ Auth::user()->name ?? 'Tenant' }}</small>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-4">
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="tenant-card">
              <div class="tenant-card-title">Upcoming Payments</div>
              <h2>₱2,500</h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="tenant-card">
              <div class="tenant-card-title">Active Bookings</div>
              <h2>1</h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="tenant-card">
              <div class="tenant-card-title">Contracts</div>
              <h2>2</h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="tenant-card">
              <div class="tenant-card-title">Maintenance Requests</div>
              <h2>0</h2>
            </div>
          </div>
        </div>

        <div class="tenant-card">
          <h5>Recent Payments</h5>
          <p>(Payment history table placeholder)</p>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.getElementById('toggleSidebar')?.addEventListener('click', function () {
      document.getElementById('tenant-sidebar').classList.toggle('collapsed');
    });
  </script>
</body>
</html>
