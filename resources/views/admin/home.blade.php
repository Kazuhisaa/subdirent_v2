@extends('admin.dashboard')

@section('title','Admin Dashboard')
@section('page-title','Dashboard')

@section('content')

<div class="row g-3">
    {{-- ✅ Registered Tenants --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Registered Tenants</h6>
                <h2 id="registeredTenantsCount" class="mb-0 fw-bold text-blue-800">0</h2>
            </div>
        </div>
    </div>

    {{-- ✅ Available Units --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Available Units</h6>
                <h2 id="availableUnitsCount" class="mb-0 fw-bold text-blue-800">0</h2>
            </div>
        </div>
    </div>

{{-- ✅ Under Maintenance --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Under Maintenance</h6>
                {{-- This now uses your new variable from the controller --}}
                <h2 class="mb-0 fw-bold text-blue-800">{{ $inProgressMaintenanceCount ?? 0 }}</h2>
            </div>
        </div>
    </div>

{{-- Monthly Income --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Monthly Income</h6>
                {{-- ✅ Add this ID to the h2 tag --}}
                <h2 id="monthlyIncomeValue" class="mb-0 fw-bold text-blue-800">₱0.00</h2>
            </div>
        </div>
    </div>
</div>

{{-- ==== CHART AND TABLE BELOW STAYS SAME ==== --}}
<div class="row mt-4 g-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                MONTHLY INCOME OVERVIEW
            </div>
            <div class="card-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                QUICK ACTIONS
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.reports') }}" class="btn btn-action w-100 py-2 fw-bold">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Latest Bookings Table --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                LATEST BOOKINGS
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 booking-table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Unit Title</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="latestBookingsTableBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Loading bookings...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(session('admin_api_token'))
<script>
    sessionStorage.setItem('admin_api_token', "{{ session('admin_api_token') }}");
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", async function() {

    const token = sessionStorage.getItem('admin_api_token');

    // === ✅ FETCH LATEST MONTHLY INCOME ===
    async function fetchMonthlyIncome() {
        const incomeElement = document.getElementById('monthlyIncomeValue');
        try {
            const res = await fetch('http://127.0.0.1:8000/api/revenue/latestRevenue', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error(`Status: ${res.status}`);
            const data = await res.json();

            // Check if data is an array and not empty
            if (Array.isArray(data) && data.length > 0) {
                // Get the very last item from the revenue data array
                const latestRecord = data[data.length - 1];
                const income = parseFloat(latestRecord.monthly_revenue);

                // Format and display the income
                incomeElement.textContent = `₱${income.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`;

            } else {
                incomeElement.textContent = '₱0.00'; // Set default if no data
            }

        } catch (error) {
            console.error('Error fetching monthly income:', error);
            incomeElement.textContent = '—'; // Show dash on error
        }
    }



    // === ✅ FETCH REGISTERED TENANTS ===
    async function fetchTenantsCount() {
        try {
            const response = await fetch('/api/admin/api/tenants', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`Status: ${response.status}`);
            const data = await response.json();

            // Assume API returns an array of tenants
            const tenantsCount = Array.isArray(data) ? data.length : (data.total ?? 0);
            document.getElementById('registeredTenantsCount').textContent = tenantsCount.toLocaleString();
        } catch (error) {
            console.error('Error fetching tenants:', error);
            document.getElementById('registeredTenantsCount').textContent = '—';
        }
    }

    // === ✅ FETCH AVAILABLE UNITS ===
    async function fetchAvailableUnits() {
        try {
            const response = await fetch('/api/allUnits', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`Status: ${response.status}`);
            const units = await response.json();

            // Filter only units that are not rented
            const available = units.filter(u => !u.is_rented && u.status !== 'rented');
            document.getElementById('availableUnitsCount').textContent = available.length.toLocaleString();
        } catch (error) {
            console.error('Error fetching available units:', error);
            document.getElementById('availableUnitsCount').textContent = '—';
        }
    }

    // === ✅ FETCH REVENUE DATA ===
    async function fetchRevenueData() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        let revenueChart;

        try {
            const res = await fetch('http://127.0.0.1:8000/api/revenue/latestRevenue', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
            const data = await res.json();

            const labels = data.map(item => {
                const date = new Date(item.year_month);
                return date.toLocaleString('default', { month: 'short', year: 'numeric' });
            });
            const revenues = data.map(item => parseFloat(item.monthly_revenue));

            const themeBlue600 = getComputedStyle(document.documentElement).getPropertyValue('--blue-600').trim() || '#1E81CE';
            const themeBlue100 = getComputedStyle(document.documentElement).getPropertyValue('--blue-100').trim() || '#EAF8FF';

            if (revenueChart) revenueChart.destroy();
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: revenues,
                        borderColor: themeBlue600,
                        backgroundColor: themeBlue100 + 'B3',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: themeBlue600,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: ctx => `₱${ctx.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: val => '₱' + val.toLocaleString('en-PH')
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error fetching revenue data:', error);
        }
    }

    // === ✅ FETCH LATEST BOOKINGS ===
    async function fetchLatestBookings() {
        const tableBody = document.getElementById('latestBookingsTableBody');
        try {
            const res = await fetch('/api/applications', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`Status: ${res.status}`);
            const bookings = await res.json();
            tableBody.innerHTML = '';

            if (!bookings.length) {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-muted py-4">No recent bookings found.</td></tr>`;
                return;
            }

bookings
    .sort((a, b) => {
        const order = { 'Pending': 1, 'Confirmed': 2, 'Rejected': 3 };
        return (order[a.status] || 4) - (order[b.status] || 4);
    })
    .slice(0, 5)
    .forEach(b => {
                const name = `${b.first_name || ''} ${b.middle_name ? b.middle_name + ' ' : ''}${b.last_name || ''}`.trim();
                let badge = 'bg-secondary';
                if (b.status === 'Confirmed') badge = 'bg-success';
                else if (b.status === 'Rejected') badge = 'bg-danger';

                tableBody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${name || 'N/A'}</td>
                        <td>${b.unit?.title ?? b.unit_id ?? 'N/A'}</td>
                        <td><span class="badge ${badge}">${b.status ?? 'Pending'}</span></td>
                        <td><a href="/admin/bookings" class="btn btn-sm btn-outline-blue">View</a></td>
                    </tr>
                `);
            });
        } catch (error) {
            console.error('Error fetching bookings:', error);
        }
    }

    // Run all
    fetchTenantsCount();
    fetchAvailableUnits();
    fetchRevenueData();
    fetchLatestBookings();
    fetchMonthlyIncome();
});
</script>
@endsection
