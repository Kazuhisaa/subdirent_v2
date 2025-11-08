@extends('admin.dashboard')

@section('page-title', 'Bookings Management')

@section('content')
<div class="container-fluid py-4">

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gy-3">
        <h2 class="fw-bold text-blue-900 mb-0">Bookings Management</h2>

        <button class="btn btn-action rounded-pill fw-bold px-4" onclick="fetchArchivedBookings()">
            <i class="bi bi-archive-fill me-1"></i> Archived Bookings
        </button>
    </div>
</div>

    {{-- ✅ ACTIVE BOOKINGS --}}
    <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
        
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">
            
            <span>ACTIVE BOOKINGS</span>
            
            <input type="text" id="searchInput" class="form-control form-control-sm" 
                   style="flex-basis: 300px;" placeholder="Search bookings...">
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

        {{-- ✅ 1. THIS IS THE PAGINATION CONTAINER FOR ACTIVE BOOKINGS --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="pagination-container">
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
                        {{-- Make sure this ID is 'searchArchivedInput' as used in the new script --}}
                        <input type="text" id="searchArchivedInput" class="form-control form-control-sm w-50"
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

                {{-- ✅ 2. THIS IS THE MODIFIED FOOTER FOR ARCHIVED PAGINATION --}}
                <div class="modal-footer bg-white border-0 d-flex justify-content-between align-items-center">
                    <div id="archived-pagination-container">
                        </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
@push('scripts') {{-- Or @push('scripts') if you use @stack --}}
<script>
// --- Global State for Data and Pagination ---
let allBookings = [];
let allArchivedBookings = [];
const ROWS_PER_PAGE = 10;

document.addEventListener('DOMContentLoaded', fetchBookings);

// --- Render Functions ---

/**
 * Renders the active bookings table and its pagination UI.
 * @param {number} page The page number to display.
 */
function renderBookingsDisplay(page = 1) {
    const tableBody = document.getElementById('bookingsTable');
    const paginationContainer = document.getElementById('pagination-container');
    if (!tableBody || !paginationContainer) return;
    
    // 1. Filter data based on search input
    const query = document.getElementById('searchInput').value.toLowerCase();
    const filteredData = allBookings.filter(b => {
        const fullName = `${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`;
        const searchableText = [fullName, b.email, b.contact_num, b.date, b.status].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    // 2. Paginate the filtered data
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const start = (page - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageData = filteredData.slice(start, end);

    // 3. Render table rows
    if (pageData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-muted">No bookings found.</td></tr>';
    } else {
        tableBody.innerHTML = pageData.map((b, index) => {
            const fullName = `${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`.trim();
            const email = b.email || 'N/A';
            const contact = b.contact_num || 'N/A';
            const date = b.date || 'N/A';
            const time = b.booking_time || 'N/A';
            const status = b.status || 'Pending';
            
            return `
            <tr>
                <td>${start + index + 1}</td>
                <td>${fullName}</td>
                <td>${email}</td>
                <td>${contact}</td>
                <td>${date}</td>
                <td>${time}</td>
                <td>
                    <span class="badge bg-${status === 'Confirmed' ? 'success' : 'secondary'}">
                        ${status}
                    </span>
                </td>
                <td>
                    <div class="d-flex justify-content-center" style="gap: .25rem;">
                        ${status === 'Pending' ? `
                            <button class="btn btn-success btn-sm" onclick="updateBookingStatus(${b.id}, 'confirm')" title="Confirm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-outline-warning btn-sm" onclick="archiveBooking(${b.id})" title="Archive">
                            <i class="bi bi-archive-fill"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `}).join('');
    }

    // 4. Render pagination UI
    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderBookingsDisplay');
}


/**
 * Renders the archived bookings table and its pagination UI.
 * @param {number} page The page number to display.
 */
function renderArchivedDisplay(page = 1) {
    const tableBody = document.getElementById('archivedTable');
    const paginationContainer = document.getElementById('archived-pagination-container');
    if (!tableBody || !paginationContainer) return;
    
    const query = document.getElementById('searchArchivedInput').value.toLowerCase();
    const filteredData = allArchivedBookings.filter(b => {
        const fullName = `${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`;
        const searchableText = [fullName, b.email, b.contact_num, b.date, b.status].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const start = (page - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageData = filteredData.slice(start, end);

    if (pageData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No archived bookings.</td></tr>';
    } else {
        tableBody.innerHTML = pageData.map((b, index) => {
            const fullName = `${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`.trim();
            const email = b.email || 'N/A';
            const contact = b.contact_num || 'N/A';
            const date = b.date || 'N/A';
            const time = b.booking_time || 'N/A';
            const status = b.status || 'Pending';

            return `
            <tr>
                <td>${start + index + 1}</td>
                <td>${fullName}</td>
                <td>${email}</td>
                <td>${contact}</td>
                <td>${date}</td>
                <td>${time}</td>
                <td><span class="badge bg-${status === 'Confirmed' ? 'success' : 'secondary'}">${status}</span></td>
                <td>
                    <button class="btn btn-outline-success btn-sm" onclick="restoreBooking(${b.id})">
                        <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                </td>
            </tr>
        `}).join('');
    }
    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderArchivedDisplay');
}

/**
 * Builds the Bootstrap pagination HTML string.
 * @param {number} totalPages Total number of pages.
 * @param {number} currentPage The current active page.
 * @param {string} renderFunctionName The name of the render function to call on page click.
 * @returns {string} The HTML for the pagination component.
 */
function buildPaginationUI(totalPages, currentPage, renderFunctionName) {
    if (totalPages <= 1) return "";
    let html = `<nav><ul class="pagination pagination-sm mb-0">`;
    
    // Previous button
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${currentPage - 1})">&laquo;</a>
             </li>`;

    // Page numbers (simple version)
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${i})">${i}</a>
                 </li>`;
    }

    // Next button
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunctionName}(${currentPage + 1})">&raquo;</a>
             </li>`;
    
    html += `</ul></nav>`;
    return html;
}

// --- Data Fetching and Actions ---

async function fetchBookings() {
    const token = sessionStorage.getItem('admin_api_token');
    const tableBody = document.getElementById('bookingsTable');
    tableBody.innerHTML = '<tr><td colspan="8" class="py-4 text-muted">Loading bookings...</td></tr>';
    
    try {
        const response = await fetch('/api/bookings', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        if (!Array.isArray(data)) throw new Error('Invalid response format');
        
        // Sort by ID descending (latest first) and store
        allBookings = data.sort((a, b) => b.id - a.id);
        
        // Render the first page
        renderBookingsDisplay(1);
    } catch (err) {
        console.error('fetchBookings error:', err);
        showError('Failed to load bookings. Please try again later.');
        tableBody.innerHTML = `<tr><td colspan="8" class="py-4 text-danger text-center">Error loading bookings.</td></tr>`;
    }
}

async function fetchArchivedBookings() {
    const token = sessionStorage.getItem('admin_api_token');
    const modalEl = document.getElementById('archivedModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const tableBody = document.getElementById('archivedTable');

    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Loading...</td></tr>';
    modal.show();

    if (!token) {
        showError('Missing authorization token.');
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Authorization error.</td></tr>';
        return;
    }

    try {
        const res = await fetch('/api/bookings/archived', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        const data = await res.json();
        if (!Array.isArray(data)) throw new Error('Invalid response format');
        
        // Sort by ID descending (latest first) and store
        allArchivedBookings = data.sort((a, b) => b.id - a.id);

        // Render the first page of the modal
        renderArchivedDisplay(1);
    } catch (err) {
        console.error('fetchArchivedBookings error:', err);
        showError('Error loading archived bookings: ' + err.message);
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error loading data.</td></tr>`;
    }
}

async function updateBookingStatus(id, action) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    // Assuming confirmAction exists globally
    confirmAction('Do you want to confirm this booking?', 'Yes, confirm it', 'Cancel', async () => {
        try {
            const response = await fetch(`/api/bookings/${action}/${id}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to update booking.');
            showSuccess(result.message || 'Booking confirmed!');
            fetchBookings(); // Re-fetch and re-render
        } catch (err) {
            console.error('updateBookingStatus error:', err);
            showError('Error updating booking: ' + err.message);
        }
    });
}

async function archiveBooking(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    // Assuming confirmAction exists globally
    confirmAction('Are you sure you want to archive this booking?', 'Yes, archive it', 'Cancel', async () => {
        try {
            const response = await fetch(`/api/bookings/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to archive booking.');
            showSuccess('Booking archived successfully!');
            fetchBookings(); // Re-fetch and re-render
        } catch (err) {
            console.error('archiveBooking error:', err);
            showError('Error archiving booking: ' + err.message);
        }
    });
}

async function restoreBooking(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    // Assuming confirmAction exists globally
    confirmAction('Do you want to restore this booking?', 'Yes, restore it', 'Cancel', async () => {
        try {
            const res = await fetch(`/api/bookings/restore/${id}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (!res.ok) throw new Error(result.message || `Failed (${res.status})`);
            showSuccess('Booking restored successfully!');
            
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('archivedModal'));
            if(modalInstance) modalInstance.hide();
            
            fetchBookings(); // Re-fetch active bookings
            // We fetch archived as well so the data is fresh next time the modal is opened
            fetchArchivedBookings(); 
        } catch (err) {
            console.error('restoreBooking error:', err);
            showError('Error restoring booking: ' + err.message);
        }
    });
}

// --- Search Event Listeners ---

// Main page search
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', () => {
        renderBookingsDisplay(1); // On search, filter and go back to page 1
    });
}

// Archived modal search
const searchArchivedInput = document.getElementById('searchArchivedInput');
if (searchArchivedInput) {
    searchArchivedInput.addEventListener('keyup', () => {
        renderArchivedDisplay(1); // On search, filter and go back to page 1
    });
}

/**
 * NOTE: This script assumes you have global functions for SweetAlert like:
 * - showSuccess(message)
 * - showError(message)
 * - confirmAction(title, confirmText, cancelText, callback)
 * Make sure they are defined in your main admin layout.
 */
</script>
@endpush