{{-- resources/views/admin/booking.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-blue-900">Bookings</h3>
        </div>
    </div>

    {{-- Booking Summary Cards --}}
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
                    <h6 class="card-title">Approved Bookings</h6>
                    <h3 id="approvedBookings" class="fw-bold">0</h3>
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

    {{-- Bookings List --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center booking-header">
            <span class="fw-bold text-blue-900">Bookings List</span>
            <a href="#" class="btn btn-sm btn-action">+ Add Booking</a>
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
                            <td colspan="6" class="text-center text-muted">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ✅ JS Fetch Logic --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    fetch('bookings')
        .then(response => response.json())
        .then(bookings => {
            // Update summary counts
            document.getElementById('totalBookings').textContent = bookings.length;

            const approved = bookings.filter(b => b.status === 'Confirmed').length;
            const rejected = bookings.filter(b => b.status === 'Rejected').length;

            document.getElementById('approvedBookings').textContent = approved;
            document.getElementById('rejectedBookings').textContent = rejected;

            // Render table
            const tableBody = document.getElementById('bookingsTableBody');
            tableBody.innerHTML = '';

            if (bookings.length === 0) {
                tableBody.innerHTML = `<tr>
                    <td colspan="6" class="text-center text-muted">No bookings found.</td>
                </tr>`;
                return;
            }

            bookings.forEach(b => {
                const fullName = `${b.first_name} ${b.middle_name ? b.middle_name + ' ' : ''}${b.last_name}`;
                const row = `
                    <tr>
                        <td>${b.id}</td>
                        <td>${fullName}</td>
                        <td>${b.email}</td>
                        <td>${b.contact_num}</td>
                        <td>
                            <span class="badge bg-${b.status === 'Confirmed' ? 'success' : (b.status === 'Rejected' ? 'danger' : 'secondary')}">
                                ${b.status ?? 'Pending'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error fetching bookings:', error);
            document.getElementById('bookingsTableBody').innerHTML = `
                <tr><td colspan="6" class="text-danger">Failed to load bookings.</td></tr>
            `;
        });
});
</script>
@endsection
