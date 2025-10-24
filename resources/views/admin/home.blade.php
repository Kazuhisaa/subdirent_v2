{{-- resources/views/admin/home.blade.php --}}
@extends('admin.dashboard')

@section('title','Admin Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3"> {{-- Added border-0 rounded-3 --}}
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Registered Users</h6> {{-- Adjusted text --}}
                <h2 class="mb-0 fw-bold text-blue-800">{{ $registeredUsers ?? 0 }}</h2> {{-- Added theme text color --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3"> {{-- Added border-0 rounded-3 --}}
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Available Rooms</h6> {{-- Adjusted text --}}
                <h2 class="mb-0 fw-bold text-blue-800">{{ $roomsForRent ?? 0 }}</h2> {{-- Added theme text color --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3"> {{-- Added border-0 rounded-3 --}}
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Unpaid Rent</h6> {{-- Adjusted text --}}
                <h2 class="mb-0 fw-bold text-blue-800">{{ $unpaidRent ?? 0 }}</h2> {{-- Added theme text color --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3"> {{-- Added border-0 rounded-3 --}}
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase small">Monthly Income</h6> {{-- Adjusted text --}}
                <h2 class="mb-0 fw-bold text-blue-800">₱{{ number_format($monthlyIncome ?? 0,2) }}</h2> {{-- Added theme text color --}}
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-4"> {{-- Added g-4 for consistent spacing --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3 h-100"> {{-- Added border-0 rounded-3 --}}
            {{-- ✅ ADDED: Gradient Header --}}
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                MONTHLY INCOME OVERVIEW
            </div>
            <div class="card-body">
                {{-- <h6 class="card-title">Monthly Income Overview</h6> --}} {{-- Removed duplicate title --}}
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

        <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100"> {{-- Added border-0 rounded-3 and h-100 --}}
             {{-- ✅ ADDED: Gradient Header --}}
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                 QUICK ACTIONS
            </div>
            <div class="card-body d-flex flex-column justify-content-center"> {{-- Added flex for vertical centering --}}
                {{-- <h6 class="card-title text-center mb-3">Quick Actions</h6> --}} {{-- Removed duplicate title --}}
                <div class="d-grid gap-3"> {{-- Increased gap --}}
                    <a href="{{ route('admin.reports') }}" class="btn btn-action w-100 py-2 fw-bold">Generate Reports</a> {{-- Point to reports --}}
                    <a href="#" class="btn btn-outline-blue w-100 py-2 fw-bold">Send Reminder Emails</a> {{-- Placeholder --}}
                    <a href="#" class="btn btn-outline-blue w-100 py-2 fw-bold">Upload CSV</a> {{-- Placeholder --}}
                </div>
            </div>
        </div>
    </div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3"> {{-- Added border-0 rounded-3 --}}
             {{-- ✅ ADDED: Gradient Header --}}
            <div class="card-header fw-bold text-white"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem .5rem 0 0;">
                LATEST BOOKINGS
            </div>
            <div class="card-body p-0"> {{-- Removed padding --}}
                <div class="table-responsive"> {{-- Added responsive wrapper --}}
                    <table class="table table-sm mb-0 booking-table align-middle text-center"> {{-- Added align-middle, text-center, removed default table class --}}
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Room</th> {{-- Assuming Unit Title is meant --}}
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        {{-- ✅✅✅ ADDED ID HERE ✅✅✅ --}}
                        <tbody id="latestBookingsTableBody">
                            {{-- Initial loading state --}}
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Existing Revenue Chart ---
    const ctx = document.getElementById('revenueChart').getContext('2d');
    // Dummy Data - Replace with your actual data fetching if needed
    const revenueLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; // Example labels
    const revenueData = [15000, 25000, 18000, 30000, 22000, 27000, 28000, 31000, 29000, 33000, 35000, 40000]; // Example data
    const themeBlue600 = getComputedStyle(document.documentElement).getPropertyValue('--blue-600').trim() || '#1E81CE';
    const themeBlue100 = getComputedStyle(document.documentElement).getPropertyValue('--blue-100').trim() || '#EAF8FF';


    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueLabels, // Use dynamic labels
            datasets: [{
                label: 'Revenue (₱)',
                data: revenueData, // Use dynamic data
                borderColor: themeBlue600,
                backgroundColor: themeBlue100 + 'B3', // Added alpha transparency
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointBackgroundColor: themeBlue600, // Make points visible
                pointRadius: 3 // Adjust point size
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allow chart to fill container height
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                     callbacks: { // Format tooltip
                         label: function(context) {
                             let label = context.dataset.label || '';
                             if (label) { label += ': '; }
                             if (context.parsed.y !== null) {
                                 label += '₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2});
                             }
                             return label;
                         }
                     }
                 }
            },
            scales: {
                x: { grid: { color: 'rgba(10,37,64,0.05)' } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(10,37,64,0.05)' },
                    ticks: { // Format Y-axis labels
                         callback: function(value, index, values) {
                             return '₱' + value.toLocaleString('en-PH');
                         }
                     }
                 }
            }
        }
    });

    // --- ✅✅✅ NEW: Fetch Latest Bookings ✅✅✅ ---
    const bookingsTableBody = document.getElementById('latestBookingsTableBody');
    const token = sessionStorage.getItem('admin_api_token'); // Get auth token

    async function fetchLatestBookings() {
        if (!bookingsTableBody) return; // Exit if table body not found
        if (!token) {
            console.error("Admin API token not found for fetching bookings.");
            bookingsTableBody.innerHTML = `<tr><td colspan="5" class="text-danger text-center py-4">Error: Missing auth token.</td></tr>`;
            return;
        }

        try {
            const response = await fetch('/api/bookings', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const bookings = await response.json();

            bookingsTableBody.innerHTML = ''; // Clear loading/error message

            if (!bookings || bookings.length === 0) {
                bookingsTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No recent bookings found.</td></tr>`;
                return;
            }

            // Get the latest 5 bookings (assuming the API returns them in some order, sort if needed)
            // If API returns oldest first, reverse it. If newest first, slice directly.
            // Let's assume newest first for this example:
            const latestFive = bookings.slice(0, 5);

            latestFive.forEach(b => {
                const fullName = `${b.first_name || ''} ${b.middle_name ? b.middle_name + ' ' : ''}${b.last_name || ''}`.trim();

                // Determine status badge
                let statusBadgeClass = 'bg-secondary'; // Default Pending
                let statusText = b.status ?? 'Pending';
                if (b.status === 'Confirmed') { statusBadgeClass = 'bg-success'; statusText = 'Confirmed'; }
                else if (b.status === 'Rejected') { statusBadgeClass = 'bg-danger'; statusText = 'Rejected'; }

                const row = `
                    <tr>
                        <td>${b.id}</td>
                        <td>${fullName || 'N/A'}</td>
                        <td>${b.unit ? b.unit.title : (b.unit_id || 'N/A')}</td> {{-- Adjust if unit relationship isn't loaded --}}
                        <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
                        <td>
                            {{-- Link to the specific booking view/edit page if available --}}
                            <a href="/admin/bookings/${b.id}" class="btn btn-sm btn-outline-blue">View</a>
                        </td>
                    </tr>
                `;
                bookingsTableBody.insertAdjacentHTML('beforeend', row);
            });

        } catch (error) {
            console.error('Error fetching latest bookings:', error);
            bookingsTableBody.innerHTML = `<tr><td colspan="5" class="text-danger text-center py-4">Failed to load bookings.</td></tr>`;
        }
    }

    // Call the function to fetch bookings
    fetchLatestBookings();

});
</script>
@endsection