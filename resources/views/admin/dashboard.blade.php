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
    <nav id="sidebar" class="vh-100">
      <div class="sidebar-brand p-3">
        <h5 class="mb-0">SubdiRent</h5>
        <small>ADMIN</small>
      </div>

      <ul class="nav flex-column px-2">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
            Dashboard
          </a>
        </li>
         <li class="nav-divider mt-3 mb-1 text-uppercase small px-2">Management</li>
          <li class="nav-item ">
          <a class="nav-link" href="{{ route('admin.rooms') }}">Room Management</a>
        </li>
        <li class="nav-item ">
          <a class="nav-link" href="{{ route ('admin.addroom')}}">Add Room</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.tenants') }}">Tenants</a>
        </li>
        <li class="nav-item ">
          <a class="nav-link" href="#">Applications</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.bookings','index') }}">Bookings</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.payments') }}">Payments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.maintenance') }}">Maintenance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.contracts') }}">Contracts</a>
        </li>
         <li class="nav-divider mt-3 mb-1 text-uppercase small px-2">Reports</li>
        <li class="mt-4 px-2">
          <a class="nav-link" href="{{ route('admin.analytics') }}">Analytics</a>
          <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
          <a class="nav-link" href="{{ route('admin.records') }}">Records</a>
        </li>

        <li class="mt-auto p-3">
          <a class="nav-link" href="{{ route('logout') }}">Logout</a>
        </li>
      </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 min-vh-100 bg-light">
      <!-- Topbar -->
      <header class="d-flex justify-content-between align-items-center px-4 py-2 border-bottom bg-white">
        <div>
          <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-2">☰</button>
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
