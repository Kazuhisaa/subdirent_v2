@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold">ANALYTICS</h3>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="fw-bold">TOTAL REVENUE</h6>
                    <h3 class="fw-bold text-dark">₱120,000</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="fw-bold">TOTAL ROOMS</h6>
                    <h3 class="fw-bold text-success">35</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="fw-bold">TOTAL TENANTS</h6>
                    <h3 class="fw-bold text-danger">92</h3> {{-- dummy data --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Revenue Chart --}}
    <div class="card shadow-sm border-0" style="background-color: #FFF3C2;">
        <div class="card-header" style="background-color: #FFD95A;">
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

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [20000, 25000, 18000, 30000, 22000, 27000], // dummy data
                borderColor: '#c9302c',
                backgroundColor: '#F5A62355',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endsection
