@extends('admin.dashboard')

@section('page-title', 'Bookings Management')

@section('content')
<div class="container-fluid py-4">

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-blue-900">Bookings Management</h2>

        <button class="btn btn-action rounded-pill fw-bold px-4" onclick="fetchArchivedBookings()">
            <i class="bi bi-archive-fill me-1"></i> Archived Bookings
        </button>
    </div>
</div>

    {{-- ✅ ACTIVE BOOKINGS --}}
    <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>ACTIVE BOOKINGS</span>
            <input type="text" id="searchInput" class="form-control form-control-sm w-25" placeholder="Search bookings...">
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle text-center mb-0">
                    <thead class="table-light text-uppercase small text-secondary">
                        <tr>
                            <th>#</th>
                            <th>Tenant Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTable">
                        <tr>
                            <td colspan="8" class="py-4 text-muted">Loading bookings...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ✅ ARCHIVED BOOKINGS MODAL --}}
    <div class="modal fade" id="archivedModal" tabindex="-1" aria-labelledby="archivedLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header text-white border-0"
                     style="background: linear-gradient(90deg, #007BFF, #0A2540);">
                    <h5 class="modal-title fw-bold" id="archivedLabel">
                        <i class="bi bi-archive me-2"></i> Archived Bookings
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light p-0">
                    <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <input type="text" id="searchArchivedBookings" class="form-control form-control-sm w-50"
                               placeholder="Search archived bookings...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-light text-uppercase small text-secondary">
                                <tr>
                                    <th>#</th>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archivedTable">
                                <tr>
                                    <td colspan="8" class="py-4 text-muted">Loading archived bookings...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', fetchBookings);

async function fetchBookings() {
    const token = sessionStorage.getItem('admin_api_token');
    const tableBody = document.getElementById('bookingsTable');
    tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-muted">Loading bookings...</td></tr>';

    try {
        const response = await fetch('/api/bookings', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await response.json();

        if (!Array.isArray(data)) throw new Error('Invalid response');

        tableBody.innerHTML = data.length ? data.map((b, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${b.first_name} ${b.middle_name ?? ''} ${b.last_name}</td>
                <td>${b.email}</td>
                <td>${b.contact_num}</td>
                <td>${b.date}</td>
                <td>${b.booking_time}</td>
                <td>
                    <span class="badge bg-${b.status === 'Confirmed' ? 'success' : 'secondary'}">
                        ${b.status ?? 'Pending'}
                    </span>
                </td>
                <td>
                    ${b.status === 'Pending' ? `
                        <button class="btn btn-success btn-sm me-1" onclick="updateBookingStatus(${b.id}, 'confirm')" title="Confirm">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    ` : ''}
                    <button class="btn btn-outline-warning btn-sm" onclick="archiveBooking(${b.id})" title="Archive">
                        <i class="bi bi-archive-fill"></i>
                    </button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="8" class="py-4 text-muted">No bookings found.</td></tr>';
    } catch (err) {
        console.error(err);
        showError('Failed to load bookings. Please try again later.');
        tableBody.innerHTML = `<tr><td colspan="8" class="py-4 text-danger text-center">Error loading bookings.</td></tr>`;
    }
}

async function updateBookingStatus(id, action) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    confirmAction(
        'Do you want to confirm this booking?',
        'Yes, confirm it',
        'Cancel',
        async () => {
            try {
                const response = await fetch(`/api/bookings/${action}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Failed to update booking.');

                showSuccess(result.message || 'Booking confirmed!');
                fetchBookings();
            } catch (err) {
                console.error(err);
                showError('Error updating booking: ' + err.message);
            }
        }
    );
}

async function archiveBooking(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    confirmAction(
        'Are you sure you want to archive this booking?',
        'Yes, archive it',
        'Cancel',
        async () => {
            try {
                const response = await fetch(`/api/bookings/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Failed to archive booking.');

                showSuccess('Booking archived successfully!');
                fetchBookings();
            } catch (err) {
                console.error(err);
                showError('Error archiving booking: ' + err.message);
            }
        }
    );
}

async function fetchArchivedBookings() {
    const token = sessionStorage.getItem('admin_api_token');
    const modalEl = document.getElementById('archivedModal');
    const modal = new bootstrap.Modal(modalEl);
    const tableBody = document.getElementById('archivedTable');

    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Loading archived bookings...</td></tr>';

    if (!token) {
        showError('Missing authorization token.');
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Missing authorization token.</td></tr>';
        modal.show();
        return;
    }

    try {
        const res = await fetch('/api/bookings/archived', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });

        const data = await res.json();

        if (!Array.isArray(data) || data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No archived bookings.</td></tr>';
            modal.show();
            return;
        }

        tableBody.innerHTML = data.map((b, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${b.first_name} ${b.middle_name ?? ''} ${b.last_name}</td>
                <td>${b.email}</td>
                <td>${b.contact_num}</td>
                <td>${b.date}</td>
                <td>${b.booking_time}</td>
                <td><span class="badge bg-${b.status === 'Confirmed' ? 'success' : 'secondary'}">${b.status ?? 'Pending'}</span></td>
                <td>
                    <button class="btn btn-outline-success btn-sm" onclick="restoreBooking(${b.id})">
                        <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                </td>
            </tr>
        `).join('');

        modal.show();
    } catch (err) {
        console.error(err);
        showError('Error loading archived bookings: ' + err.message);
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error loading archived data.</td></tr>`;
        modal.show();
    }
}

async function restoreBooking(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    confirmAction(
        'Do you want to restore this booking?',
        'Yes, restore it',
        'Cancel',
        async () => {
            try {
                const res = await fetch(`/api/bookings/restore/${id}`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });

                const result = await res.json();
                if (!res.ok) throw new Error(result.message || `Failed (${res.status})`);

                showSuccess('Booking restored successfully!');
                bootstrap.Modal.getInstance(document.getElementById('archivedModal')).hide();
                fetchBookings();
            } catch (err) {
                console.error(err);
                showError('Error restoring booking: ' + err.message);
            }
        }
    );
}

// 🔍 Search functionality
document.getElementById('searchInput').addEventListener('keyup', () => {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookingsTable tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
});
</script>
@endsection
