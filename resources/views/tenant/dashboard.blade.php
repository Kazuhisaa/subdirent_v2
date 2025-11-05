<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'SubdiRent Tenant')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


  @vite([
      'resources/bootstrap/css/bootstrap.css',
      'resources/css/tenant.css',
      'resources/bootstrapjs/js/bootstrap.bundle.js',
      'resources/js/app.js'
  ])
  {{-- This stack is for any styles pushed from child pages --}}
  @stack('styles')
</head>

<body>
  <div class="d-flex tenant-root">

    <!-- SIDEBAR -->
    <nav id="sidebar" class="vh-100 bg-white border-end position-fixed">
      <div class="sidebar-brand p-3 border-bottom">
        <h5 class="mb-0 d-flex align-items-center">
          <i class="bi bi-house-heart me-2 text-primary"></i>
          <span class="fw-bold text-primary">SubdiRent</span>
        </h5>
        <small class="text-muted">TENANT PORTAL</small>
      </div>

      <ul class="nav flex-column px-2">
        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.home') ? 'active' : '' }}" href="{{ route('tenant.home') }}">
            <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
          </a>
        </li>

        <li class="nav-divider mt-3 mb-1 text-uppercase small px-2 text-muted fw-bold">
          <span>My Account</span>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.property') ? 'active' : '' }}" href="{{ route('tenant.property') }}">
            <i class="bi bi-building-check me-2"></i> <span>My Property</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.payments') ? 'active' : '' }}" href="{{ route('tenant.payments') }}">
            <i class="bi bi-wallet2 me-2"></i> <span>My Payments</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.maintenance') ? 'active' : '' }}" href="{{ route('tenant.maintenance') }}">
            <i class="bi bi-tools me-2"></i> <span>Maintenance Requests</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tenant.account') ? 'active' : '' }}" href="{{ route('tenant.account') }}">
            <i class="bi bi-person-circle me-2"></i> <span>My Account</span>
          </a>
        </li>

        <!-- Logout -->
        <li class="mt-auto p-3 nav-item border-top">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
              <i class="bi bi-box-arrow-right me-2"></i> <span>Logout</span>
            </button>
          </form>
        </li>
      </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 min-vh-100 bg-light" id="main-content">
      <!-- Topbar -->
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

      <!-- Page Content -->
      <main class="p-4">
        @yield('content')
      </main>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function() {
      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.getElementById('toggleSidebar');

      toggleBtn.addEventListener('click', () => {
        if (window.innerWidth > 768) {
          sidebar.classList.toggle('collapsed');
        } else {
          sidebar.classList.toggle('show');
        }
      });

      // Close sidebar when clicking outside (mobile)
      document.addEventListener("click", function(e) {
          if (window.innerWidth <= 768 && sidebar.classList.contains("show")) {
              if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                  sidebar.classList.remove("show");
              }
          }
      });
  });
  </script>

<script>
  window.apiToken = "{{ auth()->user()->createToken('tenant-token')->plainTextToken ?? '' }}";
</script>
</body>
</html>
