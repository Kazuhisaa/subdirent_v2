@extends('admin.dashboard')

@section('page-title', 'Bookings Management')

@section('content')
<div class="container-fluid py-4">

{{-- PAGE HEADER --}}
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold text-blue-900">Bookings Management</h2>
    </div>
    <div class="col-md-6 d-flex justify-content-end align-items-center">
        {{-- NEW: View Toggle Button Group --}}
        <div class="btn-group" role="group" aria-label="View Toggle">
            <button type="button" class="btn btn-action rounded-pill-start fw-bold px-4 active" id="btn-view-active">
                <i class="bi bi-person-check-fill me-1"></i> Active
            </button>
            <button type="button" class="btn btn-outline-blue rounded-pill-end fw-bold px-4" id="btn-view-archived">
                <i class="bi bi-archive-fill me-1"></i> Archived
            </button>
        </div>
    </div>
</div>

{{-- ✅ UNIFIED BOOKINGS TABLE --}}
<div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">

    <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
         style="background: linear-gradient(90deg, #007BFF, #0A2540);">

        {{-- Title changes dynamically --}}
        <span id="table-title">ACTIVE BOOKINGS</span>

        {{-- Search bar is now generic --}}
        <input type="text" id="table-search" class="form-control form-control-sm"
               style="flex-basis: 300px;" placeholder="Search bookings...">
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            {{-- Table is now generic --}}
            <table class="table align-middle text-center mb-0">
                {{-- Head changes dynamically --}}
                <thead class="table-light text-uppercase small text-secondary" id="main-table-head">
                    {{-- Content injected by JS --}}
                </thead>
                {{-- Body changes dynamically --}}
                <tbody id="main-table-body">
                    <tr>
                        <td colspan="7" class="py-4 text-muted">Loading bookings...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ PAGINATION CONTAINER IS NOW GENERIC --}}
    <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="main-pagination-container">
        {{-- Content injected by JS --}}
    </div>
</div>

{{-- Note: The '#archivedModal' has been removed. --}}

</div>
@endsection

@push('scripts')
<script>
// --- Global State for Data, Pagination, and View ---
let allBookings = [];
let allArchivedBookings = [];
let currentView = 'active'; // 'active' or 'archived'
const ROWS_PER_PAGE = 10;
let currentToken = ""; // Store token

document.addEventListener('DOMContentLoaded', async () => {
    // We get the token once and store it
    currentToken = sessionStorage.getItem('admin_api_token');
    if (!currentToken) {
        showError('Admin token not found. App cannot function.');
        return;
    }

    // --- Attach View Toggle Listeners ---
    const btnActive = document.querySelector("#btn-view-active");
    const btnArchived = document.querySelector("#btn-view-archived");

    if (btnActive && btnArchived) {
        btnActive.addEventListener("click", () => {
            if (currentView === 'active') return;
            currentView = 'active';
            btnActive.classList.add("active", "btn-action");
            btnActive.classList.remove("btn-outline-blue");
            btnArchived.classList.remove("active", "btn-action");
            btnArchived.classList.add("btn-outline-blue");
            renderDisplay(1); // Re-render with new view
        });
        btnArchived.addEventListener("click", () => {
            if (currentView === 'archived') return;
            currentView = 'archived';
            btnArchived.classList.add("active", "btn-action");
            btnArchived.classList.remove("btn-outline-blue");
            btnActive.classList.remove("active", "btn-action");
            btnActive.classList.add("btn-outline-blue");
            renderDisplay(1); // Re-render with new view
        });
    }

    // --- Attach Generic Search Listener ---
    const searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('input', () => { // 'input' is more responsive than 'keyup'
            renderDisplay(1); // On search, filter and go back to page 1
        });
    }

    // --- Load All Data and Initial Render ---
    await loadAllDataAndRender();
});

/**
 * Main function to load all data from the server and trigger the first render.
 */
async function loadAllDataAndRender() {
    const tableBody = document.getElementById('main-table-body');
    tableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-muted">Loading data...</td></tr>';

    try {
        // Fetch active and archived bookings in parallel
        await Promise.all([
            fetchBookings(),
            fetchArchivedBookings()
        ]);

        // Once all data is loaded, render the display
        renderDisplay(1);

    } catch (err) {
        console.error("Error loading initial data:", err);
        tableBody.innerHTML = '<tr><td colspan="7" class="py-4 text-danger">Failed to load critical data.</td></tr>';
        showError('Failed to load data. Please refresh the page.');
    }
}

// --- BAGONG HELPER FUNCTIONS (Unchanged) ---
function formatBookingDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        const localDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
        return localDate.toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

function formatBookingTime(timeString) {
    if (!timeString) return 'N/A';
    try {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(hours);
        date.setMinutes(minutes);
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric', minute: '2-digit', hour12: true
        });
    } catch (e) {
        return timeString;
    }
}

function escapeHtml(str) {
    return String(str || "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// --- NEW UNIFIED RENDER FUNCTION ---

/**
 * Filters, paginates, and renders the appropriate booking list (active or archived)
 * based on the global 'currentView' state.
 */
function renderDisplay(page = 1) {
    const titleEl = document.querySelector("#table-title");
    const searchEl = document.querySelector("#table-search");
    const headEl = document.querySelector("#main-table-head");
    const bodyEl = document.querySelector("#main-table-body");
    const paginationEl = document.querySelector("#main-pagination-container");

    if (!titleEl || !searchEl || !headEl || !bodyEl || !paginationEl) {
        console.error("One or more critical table elements are missing.");
        return;
    }

    // 1. Define sources based on currentView
    let sourceData, tableHeadHTML, rowBuilderFn, listenerFn, emptyText;

    const sharedTableHead = `
        <tr>
            <th>Tenant Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>`;

    if (currentView === 'active') {
        titleEl.textContent = 'ACTIVE BOOKINGS';
        searchEl.placeholder = 'Search active bookings...';
        sourceData = allBookings;
        emptyText = 'No active bookings found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (b) => {
            const fullName = escapeHtml(`${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`.trim());
            const email = escapeHtml(b.email || 'N/A');
            const contact = escapeHtml(b.contact_num || 'N/A');
            const date = escapeHtml(formatBookingDate(b.date));
            const time = escapeHtml(formatBookingTime(b.booking_time));
            const status = escapeHtml(b.status || 'Pending');

            return `
                <tr>
                    <td>${fullName}</td>
                    <td>${email}</td>
                    <td>${contact}</td>
                    <td>${date}</td>
                    <td>${time}</td>
                    <td>
                        <span class="badge bg-${status === 'Confirmed' ? 'success' : 'warning text-dark'}">
                            ${status}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            ${status === 'Pending' ? `
                                <button class="btn btn-outline-success btn-sm confirm-btn" data-id="${b.id}" title="Confirm">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            ` : ''}
                            <button class="btn btn-outline-warning btn-sm archive-btn" data-id="${b.id}" title="Archive">
                                <i class="bi bi-archive-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
        };

        listenerFn = attachActiveListeners;

    } else { // 'archived'
        titleEl.textContent = 'ARCHIVED BOOKINGS';
        searchEl.placeholder = 'Search archived bookings...';
        sourceData = allArchivedBookings;
        emptyText = 'No archived bookings found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (b) => {
            const fullName = escapeHtml(`${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`.trim());
            const email = escapeHtml(b.email || 'N/A');
            const contact = escapeHtml(b.contact_num || 'N/A');
            const date = escapeHtml(formatBookingDate(b.date));
            const time = escapeHtml(formatBookingTime(b.booking_time));
            const status = escapeHtml(b.status || 'Pending');

            return `
                <tr>
                    <td>${fullName}</td>
                    <td>${email}</td>
                    <td>${contact}</td>
                    <td>${date}</td>
                    <td>${time}</td>
                    <td>
                        <span class="badge bg-${status === 'Confirmed' ? 'success' : 'warning text-dark'}">
                            ${status}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-outline-success btn-sm restore-btn" data-id="${b.id}">
                            <i class="bi bi-arrow-clockwise"></i> Restore
                        </button>
                    </td>
                </tr>`;
        };

        listenerFn = attachRestoreListeners;
    }

    // 2. Filter Data
    const query = searchEl.value.trim().toLowerCase();
    const filteredData = sourceData.filter(b => {
        const fullName = `${b.first_name} ${b.middle_name ?? ''} ${b.last_name}`;
        const searchableText = [fullName, b.email, b.contact_num, b.date, b.status].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    // 3. Paginate Data
    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const start = (page - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageData = filteredData.slice(start, end);

    // 4. Render Table Head
    headEl.innerHTML = tableHeadHTML;

    // 5. Render Table Rows
    bodyEl.innerHTML = ""; // Clear previous rows
    if (pageData.length === 0) {
        bodyEl.innerHTML = `<tr><td colspan="7" class="text-muted py-3">${emptyText}</td></tr>`;
    } else {
        pageData.forEach((b) => {
            bodyEl.insertAdjacentHTML("beforeend", rowBuilderFn(b));
        });
    }

    // 6. Render Pagination (Using the better pattern)
    paginationEl.innerHTML = buildPaginationUI(totalPages, page);

    // 7. Re-attach listeners for new buttons
    listenerFn();

    // 8. Attach listeners for pagination links
    paginationEl.querySelectorAll(".page-link").forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const newPage = parseInt(e.target.dataset.page, 10);
            if (newPage) {
                renderDisplay(newPage); // Call the main render function
            }
        });
    });
}

/**
 * Builds Bootstrap pagination HTML string.
 * (This is the cleaner version from your tenant file)
 */
function buildPaginationUI(totalPages, currentPage) {
    if (totalPages <= 1) return "";
    let html = `<nav><ul class="pagination pagination-sm mb-0">`;

    // Previous button
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">&laquo;</a>
             </li>`;

    // Page numbers logic (same as tenant file)
    const pagesToShow = [];
    pagesToShow.push(1);
    let start = Math.max(2, currentPage - 2);
    let end = Math.min(totalPages - 1, currentPage + 2);
    if (currentPage - 2 > 2) pagesToShow.push('...');
    for (let i = start; i <= end; i++) {
        if (!pagesToShow.includes(i)) pagesToShow.push(i);
    }
    if (currentPage + 2 < totalPages - 1) pagesToShow.push('...');
    if (!pagesToShow.includes(totalPages)) pagesToShow.push(totalPages);

    pagesToShow.forEach(p => {
        if (p === '...') {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        } else {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                     </li>`;
        }
    });

    // Next button
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">&raquo;</a>
             </li>`;
    html += `</ul></nav>`;
    return html;
}

// --- Data Fetching Functions (Refactored) ---

/**
 * Fetches ACTIVE bookings and stores them in 'allBookings'.
 * Does not render, just fetches data.
 */
async function fetchBookings() {
    try {
        const response = await fetch('/api/bookings', {
            headers: { 'Authorization': `Bearer ${currentToken}` }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        if (!Array.isArray(data)) throw new Error('Invalid response format');

        allBookings = data.sort((a, b) => {
            const aIsPending = (a.status || 'Pending') === 'Pending';
            const bIsPending = (b.status || 'Pending') === 'Pending';
            if (aIsPending && !bIsPending) return -1;
            if (!aIsPending && bIsPending) return 1;
            return b.id - a.id;
        });
    } catch (err) {
        console.error('fetchBookings error:', err);
        showError('Failed to load active bookings.');
        allBookings = []; // Ensure it's an empty array on failure
        throw err; // Re-throw to be caught by Promise.all
    }
}

/**
 * Fetches ARCHIVED bookings and stores them in 'allArchivedBookings'.
 * Does not render or show a modal, just fetches data.
 */
async function fetchArchivedBookings() {
    try {
        const res = await fetch('/api/bookings/archived', {
            headers: { 'Authorization': `Bearer ${currentToken}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        const data = await res.json();
        if (!Array.isArray(data)) throw new Error('Invalid response format');

        allArchivedBookings = data.sort((a, b) => {
            const aIsPending = (a.status || 'Pending') === 'Pending';
            const bIsPending = (b.status || 'Pending') === 'Pending';
            if (aIsPending && !bIsPending) return -1;
            if (!aIsPending && bIsPending) return 1;
            return b.id - a.id;
        });
    } catch (err) {
        console.error('fetchArchivedBookings error:', err);
        showError('Failed to load archived bookings.');
        allArchivedBookings = []; // Ensure it's an empty array on failure
        throw err; // Re-throw to be caught by Promise.all
    }
}

// --- NEW ACTION LISTENER ATTACHMENT ---

/**
 * Attaches event listeners for 'Confirm' and 'Archive' buttons.
 */
function attachActiveListeners() {
    // Confirm buttons
    document.querySelectorAll("#main-table-body .confirm-btn").forEach(btn => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;
        btn.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id;
            updateBookingStatus(id, 'confirm');
        });
    });

    // Archive buttons
    document.querySelectorAll("#main-table-body .archive-btn").forEach(btn => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;
        btn.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id;
            archiveBooking(id);
        });
    });
}

/**
 * Attaches event listeners for 'Restore' buttons.
 */
function attachRestoreListeners() {
    document.querySelectorAll("#main-table-body .restore-btn").forEach(btn => {
        if (btn.listenerAttached) return;
        btn.listenerAttached = true;
        btn.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id;
            restoreBooking(id);
        });
    });
}


// --- Action Functions (Refactored for new render flow) ---

async function updateBookingStatus(id, action) {
    if (!currentToken) return showError('Missing authorization token.');

    confirmAction('Do you want to confirm this booking?', 'Yes, confirm it', 'Cancel', async () => {
        try {
            const response = await fetch(`/api/bookings/${action}/${id}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${currentToken}`, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to update booking.');

            showSuccess(result.message || 'Booking confirmed!');

            // Just re-fetch active bookings and re-render
            await fetchBookings();
            renderDisplay(); // Re-render current page
        } catch (err) {
            console.error('updateBookingStatus error:', err);
            showError('Error updating booking: ' + err.message);
        }
    });
}

async function archiveBooking(id) {
    if (!currentToken) return showError('Missing authorization token.');

    confirmAction('Are you sure you want to archive this booking?', 'Yes, archive it', 'Cancel', async () => {
        try {
            const response = await fetch(`/api/bookings/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${currentToken}` }
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to archive booking.');

            showSuccess('Booking archived successfully!');

            // Re-fetch BOTH and re-render, staying on page 1 of 'active' view
            await Promise.all([fetchBookings(), fetchArchivedBookings()]);
            renderDisplay(1);
        } catch (err) {
            console.error('archiveBooking error:', err);
            showError('Error archiving booking: ' + err.message);
        }
    });
}

async function restoreBooking(id) {
    if (!currentToken) return showError('Missing authorization token.');

    confirmAction('Do you want to restore this booking?', 'Yes, restore it', 'Cancel', async () => {
        try {
            const res = await fetch(`/api/bookings/restore/${id}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${currentToken}`, 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (!res.ok) throw new Error(result.message || `Failed (${res.status})`);

            showSuccess('Booking restored successfully!');

            // Re-fetch BOTH and re-render, staying on page 1 of 'archived' view
            await Promise.all([fetchBookings(), fetchArchivedBookings()]);
            renderDisplay(1);
        } catch (err) {
            console.error('restoreBooking error:', err);
            showError('Error restoring booking: ' + err.message);
        }
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