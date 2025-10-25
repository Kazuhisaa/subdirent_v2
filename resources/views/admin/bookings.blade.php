{{-- resources/views/admin/booking.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900 mb-0">Bookings</h3>
            {{-- "Add Booking" button moved to card header --}}
        </div>
    </div>

    {{-- Booking Summary Cards (These use .booking-card styles) --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Total Bookings</h6>
                    <h3 id="totalBookings" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Confirmed Bookings</h6>
                    <h3 id="confirmedBookings" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Rejected Bookings</h6>
                    <h3 id="rejectedBookings" class="fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings List Card --}}
    <div class="card border-0 shadow-sm">

        {{-- Card Header with Gradient --}}
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>BOOKINGS LIST</span>

             {{-- Add Booking Button --}}
            <a href="#" class="btn btn-sm text-white fw-semibold"
                    style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border:none; border-radius: 6px;">
                + Add Booking
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript Fetch Logic --}}
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const tableBody = document.getElementById('bookingsTableBody');
    const totalEl = document.getElementById('totalBookings');
    const confirmedEl = document.getElementById('confirmedBookings');
    const rejectedEl = document.getElementById('rejectedBookings');
    const token = sessionStorage.getItem('admin_api_token'); 

    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>`;

    // Check for token first
    if (!token) {
        console.error("Authorization token not found in sessionStorage.");
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">⚠ Error: Missing authorization token. Please login again.</td></tr>`;
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
            throw new Error(`HTTP error! status: ${response.status} ${response.statusText}`);
        }

        const bookings = await response.json();

        // Update summary counts
        totalEl.textContent = bookings.length;
        const confirmedCount = bookings.filter(b => b.status === 'Confirmed').length;
        const rejectedCount = bookings.filter(b => b.status === 'Rejected').length;
        confirmedEl.textContent = confirmedCount;
        rejectedEl.textContent = rejectedCount;

        // Render table rows
        tableBody.innerHTML = ''; // Clear loading state

        if (bookings.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No bookings found.</td></tr>`;
            return;
        }

        bookings.forEach(b => {
            const fullName = `${b.first_name} ${b.middle_name ? b.middle_name + ' ' : ''}${b.last_name}`;

            let statusBadgeClass = 'bg-secondary'; 
            let statusText = b.status ?? 'Pending';
            if (b.status === 'Confirmed') {
                statusBadgeClass = 'bg-success';
                 statusText = 'Confirmed';
            } else if (b.status === 'Rejected') {
                statusBadgeClass = 'bg-danger';
                 statusText = 'Rejected';
            }

            const actionsHtml = `
                <div class="d-flex justify-content-center align-items-center gap-2">
                    ${statusText === 'Pending' ? `
                        <form action="/api/bookings/confirm/${b.id}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Confirm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                        <form action="/api/bookings/reject/${b.id}" method="POST" class="mb-0"> {{-- Assumes /api/bookings/reject/{id} route exists --}}
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    ` : ''}

                    <form action="/api/bookings/${b.id}" method="POST" class="mb-0"> {{-- Assumes DELETE /api/bookings/{id} route exists --}}
                         @csrf
                         @method('DELETE')
                         <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                            <i class="bi bi-archive-fill"></i>
                        </button>
                    </form>
                </div>
            `;

            const row = `
                <tr>
                    <td>${b.id}</td>
                    <td>${fullName}</td>
                    <td>${b.email}</td>
                    <td>${b.contact_num}</td>
                    <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
                    <td>${actionsHtml}</td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

    } catch (error) {
        console.error('Error fetching bookings:', error);
        tableBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center py-4">⚠ Failed to load bookings: ${error.message}. Check console and API logs.</td></tr>`;
    }
});
</script>
@endsection