{{-- resources/views/admin/home.blade.php --}}
@extends('admin.dashboard')

@section('title','Admin Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3">
    <!-- Top Cards -->
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Registered Users</h6>
                <h2 class="mb-0">{{ $registeredUsers ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Rooms for Rent</h6>
                <h2 class="mb-0">{{ $roomsForRent ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Unpaid Rent</h6>
                <h2 class="mb-0">{{ $unpaidRent ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Monthly Income</h6>
                <h2 class="mb-0">₱{{ number_format($monthlyIncome ?? 0,2) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Chart + Quick Actions -->
<div class="row mt-4">
    <!-- Chart -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title">Monthly Income Overview</h6>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

        <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-center mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.rooms') }}" class="btn btn-action w-100">Generate Reports</a>
                    <a href="{{ route('admin.tenants') }}" class="btn btn-outline-blue w-100">Send Reminder Emails</a>
                    <a href="{{ route('admin.bookings','create') }}" class="btn btn-outline-blue w-100">Upload CSV</a>
                </div>
            </div>
        </div>
    </div>
    
<!-- Latest Bookings -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Latest Bookings</h6>
                <table class="table table-sm booking-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestBookings ?? [] as $b)
                            <tr>
                                <td>{{ $b->id }}</td>
                                <td>{{ $b->full_name }}</td>
                                <td>{{ $b->room->title ?? '—' }}</td>
                                <td>{{ $b->status }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-blue">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No bookings yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [20000, 25000, 18000, 30000, 22000, 27000],
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--blue-600').trim(),
                backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--blue-200').trim(),
                fill: true,
                tension: 0.4,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
            },
            scales: {
                x: { grid: { color: 'rgba(10,37,64,0.05)' } },
                y: { beginAtZero: true, grid: { color: 'rgba(10,37,64,0.05)' } }
            }
        }
    });
});
</script>
@endsection
