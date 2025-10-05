@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">ANALYTICS</h3>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card room-card text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-blue-700">TOTAL REVENUE</h6>
                    <h3 class="fw-bold text-blue-900">₱120,000</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card room-card text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-blue-700">TOTAL ROOMS</h6>
                    <h3 class="fw-bold text-blue-600">35</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card room-card text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-blue-700">TOTAL TENANTS</h6>
                    <h3 class="fw-bold text-blue-800">92</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Revenue Chart --}}
    <div class="card shadow-sm border-0" style="background-color: var(--blue-100);">
        <div class="card-header" style="background: var(--blue-500); color: #fff;">
            <span class="fw-bold">Monthly Revenue</span>
        </div>
        <div class="card-body text-center">
            <canvas id="revenueChart" style="max-height: 200px; max-width: 500px;"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(42, 157, 244, 0.4)');  
    gradient.addColorStop(1, 'rgba(14, 69, 160, 0.1)');   

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [20000, 25000, 18000, 30000, 22000, 27000], // dummy data
                borderColor: 'var(--blue-700)',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'var(--blue-700)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'var(--blue-700)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top', labels: { color: 'var(--blue-900)' } },
                title: { display: false }
            },
            scales: {
                x: { ticks: { color: 'var(--blue-800)' }, grid: { color: 'rgba(42,157,244,0.1)' } },
                y: { beginAtZero: true, ticks: { color: 'var(--blue-800)' }, grid: { color: 'rgba(42,157,244,0.1)' } }
            }
        }
    });
});
</script>
@endsection
