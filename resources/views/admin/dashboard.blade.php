{{-- resources/views/admin/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'SubdiRent Admin')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  @vite([
      'resources/bootstrap/css/bootstrap.css',
      'resources/css/admin.css',
      'resources/bootstrapjs/js/bootstrap.bundle.js',
      'resources/js/app.js'
  ])

</head>
<body>
  <div class="d-flex admin-root">

    <!-- SIDEBAR -->
    <nav id="sidebar" class="vh-100 bg-white border-end">
      <div class="sidebar-brand p-3">
        <h5 class="mb-0"><i class="bi bi-building me-2"></i><span>SubdiRent</span></h5>
        <small><span>ADMIN</span></small>
      </div>

        <ul class="nav flex-column px-2">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
          <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-divider mt-3 mb-1 text-uppercase small px-2"><span>Management</span></li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.rooms') }}">
          <i class="bi bi-house-door"></i> <span>Room Management</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.addroom') }}">
          <i class="bi bi-plus-square"></i> <span>Add Room</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.tenants') }}">
          <i class="bi bi-people"></i> <span>Tenants</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="bi bi-file-earmark-text"></i> <span>Applications</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.bookings','index') }}">
          <i class="bi bi-calendar-check"></i> <span>Bookings</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.payments') }}">
          <i class="bi bi-credit-card"></i> <span>Payments</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.maintenance') }}">
          <i class="bi bi-tools"></i> <span>Maintenance</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.contracts') }}">
          <i class="bi bi-file-earmark"></i> <span>Contracts</span>
        </a>
      </li>
      <li class="nav-divider mt-3 mb-1 text-uppercase small px-2"><span>Reports</span></li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.analytics') }}">
          <i class="bi bi-graph-up"></i> <span>Analytics</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.reports') }}">
          <i class="bi bi-bar-chart"></i> <span>Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.records') }}">
          <i class="bi bi-archive"></i> <span>Records</span>
        </a>
      </li>
      <li class="mt-auto p-3 nav-item">
        <a class="nav-link" href="{{ route('logout') }}">
          <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        </a>
      </li>
    </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 min-vh-100 bg-light">
      <!-- Topbar -->
      <header class="d-flex justify-content-between align-items-center px-4 py-2 border-bottom bg-white">
        <div>
          <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-list"></i>
          </button>
          <span class="h6 mb-0">@yield('page-title','Dashboard')</span>
        </div>
        <div class="d-flex align-items-center">
          <div class="me-3">
            <small class="text-muted">Admin</small>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-4">
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    // Sidebar toggle
    document.getElementById('toggleSidebar')?.addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
  </script>
</body>
</html>
