{{-- resources/views/admin/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96x96.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">
    <link rel="manifest" href="public/images/site.webmanifest">  
    <meta charset="UTF-8" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin-api-token" content="{{ session('admin_api_token') }}">
    @if(session('admin_api_token'))
    <meta name="api-token" content="{{ session('admin_api_token') }}">
    @endif

    <title>@yield('title', 'SubdiRent Admin')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite([
        'resources/bootstrapjs/js/bootstrap.bundle.js',
        'resources/bootstrap/css/bootstrap.css',
        'resources/css/admin.css',
        'resources/js/app.js'
    ])
</head>

<body>
    <div class="d-flex admin-root">

        <nav id="sidebar" class="vh-100 bg-white border-end position-fixed">
            <div class="sidebar-brand p-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-building me-2"></i>
                    <span>SubdiRent</span>
                </h5>
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
                    <a class="nav-link {{ request()->routeIs('admin.rooms') ? 'active' : '' }}" href="{{ route('admin.rooms') }}">
                        <i class="bi bi-house-door"></i> <span>Room Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.addroom') ? 'active' : '' }}" href="{{ route('admin.addroom') }}">
                        <i class="bi bi-plus-square"></i> <span>Add Room</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tenants') ? 'active' : '' }}" href="{{ route('admin.tenants') }}">
                        <i class="bi bi-people"></i> <span>Tenants</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.applications') ? 'active' : '' }}" href="{{ route('admin.applications') }}">
                        <i class="bi bi-file-earmark-text"></i> <span>Applications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings','index') }}">
                        <i class="bi bi-calendar-check"></i> <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}">
                        <i class="bi bi-credit-card"></i> <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.maintenance') ? 'active' : '' }}" href="{{ route('admin.maintenance') }}">
                        <i class="bi bi-tools"></i> <span>Maintenance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.contracts') ? 'active' : '' }}" href="{{ route('admin.contracts') }}">
                        <i class="bi bi-file-earmark"></i> <span>Contracts</span>
                    </a>
                </li>

                <li class="nav-divider mt-3 mb-1 text-uppercase small px-2"><span>Reports</span></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                        <i class="bi bi-graph-up"></i> <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                        <i class="bi bi-bar-chart"></i> <span>Reports</span>
                    </a>
                </li>

                <li class="mt-auto p-3 nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <div class="flex-grow-1 min-vh-100 bg-light" id="main-content">
            <header class="d-flex justify-content-between align-items-center px-4 py-2 border-bottom bg-white">
                <div>
                    <button id="toggleSidebar" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="h6 mb-0">@yield('page-title', 'Dashboard')</span>
                </div>
                <div class="d-flex align-items-center">
                    <small class="text-muted">Admin</small>
                </div>
            </header>

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

        // Optional: close sidebar when clicking outside (mobile)
        document.addEventListener("click", function(e) {
            if (window.innerWidth <= 768 && sidebar.classList.contains("show")) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove("show");
                }
            }
        });
    });
    </script>

            <!-- SweetAlert2 CDN -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Your custom alerts file -->
        @vite('resources/js/alerts.js')


     @stack('scripts')
</body>
</html>