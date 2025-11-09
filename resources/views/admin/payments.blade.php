@extends('admin.dashboard')

@section('page-title', 'Payments')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Page Header (MODIFIED) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-blue-900">Payment Records</h3>
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

    {{-- ✅ Summary Cards (Unchanged - these remain static from page load) --}}
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card">
                <div class="card-body">
                    <h6 class="card-title">Total Payments</h6>
                    <h3 class="fw-bold">{{ $payments->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Paid</h6>
                    <h3 class="fw-bold">{{ $payments->where('payment_status', 'paid')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Unpaid</h6>
                    <h3 class="fw-bold">{{ $payments->where('payment_status', 'unpaid')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ NEW: UNIFIED Payments Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">

            {{-- Title changes dynamically --}}
            <span id="table-title">PAYMENT RECORDS LIST</span>

            {{-- Search bar is now generic --}}
            <input type="text" id="table-search" class="form-control form-control-sm"
                   style="flex-basis: 300px;" placeholder="Search payments...">
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Table is now generic --}}
                <table class="table mb-0 text-center booking-table align-middle">
                    {{-- Head changes dynamically --}}
                    <thead class="table-light text-uppercase small text-secondary" id="main-table-head">
                        {{-- Content injected by JS --}}
                    </thead>
                    {{-- Body changes dynamically --}}
                    <tbody id="main-table-body">
                        <tr><td colspan="7" class="text-center text-muted py-3">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Pagination is now generic --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="main-pagination-container">
            {{-- Content injected by JS --}}
        </div>
    </div>
</div>


{{--
✅ NEW: DATA HYDRATION SOURCE
This hidden div contains your original Blade loops.
The 'hydrateAllData()' function will read from here,
populating the JS arrays. The unified table above
will then be rendered using that data.
--}}
<div id="data-hydration-source" style="display: none;">

    {{-- Original Active Payments Table Body --}}
    <table id="payments-table-original">
        <tbody id="payments-table-body">
            @forelse($payments as $payment)
            <tr class="payment-row"
                data-id="{{ $payment->id }}"
                data-tenant-id="{{ $payment->tenant_id }}"
                data-name="{{ $payment->tenant ? ($payment->tenant->first_name . ' ' . $payment->tenant->last_name) : 'Unknown Tenant' }}"
                data-status="{{ $payment->payment_status }}"
                data-date="{{ $payment->payment_date }}"
                data-method="{{ $payment->payment_method ?? '—' }}"
                data-remarks="{{ $payment->remarks ?? 'No remarks' }}"
                data-download-url="{{ route('admin.payments.download', $payment->id) }}"
                data-archive-url="{{ route('admin.admin.payments.archive', $payment->id) }}">
                {{-- Data is all that matters, content is irrelevant --}}
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>

    {{-- Original Archived Payments Table Body (from modal) --}}
    <table id="archived-table-original">
        <tbody id="archived-table-body">
            @forelse($archivedPayments as $archived)
            <tr class="archived-row"
                data-id="{{ $archived->id }}"
                data-tenant-id="{{ $archived->tenant_id }}"
                data-name="{{ $archived->tenant ? ($archived->tenant->first_name . ' ' . $archived->tenant->last_name) : 'Unknown Tenant' }}"
                data-status="{{ $archived->payment_status }}"
                data-date="{{ $archived->payment_date }}"
                data-method="{{ $archived->payment_method ?? '—' }}"
                data-remarks="{{ $archived->remarks ?? 'No remarks' }}"
                data-restore-url="{{ route('admin.admin.payments.restore', $archived->id) }}">
                {{-- Data is all that matters, content is irrelevant --}}
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>

</div> {{-- End of data-hydration-source --}}


{{-- Note: The '#archivedPaymentsModal' has been removed. --}}

@push('scripts')
<script>
// --- Global State ---
let allPayments = [];
let allArchivedPayments = [];
let currentView = 'active'; // 'active' or 'archived'
const ROWS_PER_PAGE = 10;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// --- SweetAlert Helpers ---
function confirmAction(title, confirmText, cancelText, onConfirm) {
    Swal.fire({
        title: title,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
    }).then(result => {
        if (result.isConfirmed && typeof onConfirm === 'function') {
            onConfirm();
        }
    });
}

/**
 * ✅ MODIFIED: Shows a standard alert with an "OK" button.
 * The 'await' in the action handlers will now pause until the user
 * clicks "OK".
 */
function showSuccess(message) {
    return Swal.fire({ // 'return' is still needed for the await
        title: 'Success!',
        text: message,
        icon: 'success',
        showConfirmButton: true, // <-- OK button is back
        confirmButtonColor: '#198754'
        // No timer
    });
}

function showError(message) {
    Swal.fire({ title: 'Error!', text: message, icon: 'error', confirmButtonColor: '#dc3545' });
}

/**
 * ✅ NEW: A simple helper function to create a delay.
 * We will 'await' this for 3 seconds (3000ms).
 */
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// --- Data Hydration (Unchanged) ---
function hydrateAllData() {
    allPayments = [];
    document.querySelectorAll('#payments-table-body tr.payment-row').forEach(row => {
        allPayments.push(row.dataset);
    });
    allPayments.sort((a, b) => b.id - a.id);

    allArchivedPayments = [];
    document.querySelectorAll('#archived-table-body tr.archived-row').forEach(row => {
        allArchivedPayments.push(row.dataset);
    });
    allArchivedPayments.sort((a, b) => b.id - a.id);

    console.log(`Hydrated ${allPayments.length} payments and ${allArchivedPayments.length} archived payments.`);
}

// --- UNIFIED RENDER FUNCTION (Unchanged) ---
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

    let sourceData, tableHeadHTML, rowBuilderFn, emptyText;
    const sharedTableHead = `
        <tr>
            <th>Tenant ID</th>
            <th>Tenant Name</th>
            <th>Payment Status</th>
            <th>Payment Date</th>
            <th>Payment Method</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>`;
    const listenerFn = attachActionListeners;

    if (currentView === 'active') {
        titleEl.textContent = 'PAYMENT RECORDS LIST';
        searchEl.placeholder = 'Search active payments...';
        sourceData = allPayments;
        emptyText = 'No active payments found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (p) => `
            <tr>
                <td>${escapeHtml(p.tenantId)}</td>
                <td>${escapeHtml(p.name)}</td>
                <td>${renderStatusBadge(p.status)}</td>
                <td>${formatDate(p.date)}</td>
                <td>${ucfirst(p.method)}</td>
                <td>${escapeHtml(p.remarks)}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="${p.downloadUrl}" class="btn btn-sm btn-outline-primary download-btn">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </a>
                        <form action="${p.archiveUrl}" method="POST" class="mb-0 archive-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                                <i class="bi bi-archive"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>`;

    } else { // 'archived'
        titleEl.textContent = 'ARCHIVED PAYMENTS';
        searchEl.placeholder = 'Search archived payments...';
        sourceData = allArchivedPayments;
        emptyText = 'No archived payments found.';
        tableHeadHTML = sharedTableHead;

        rowBuilderFn = (p) => `
            <tr>
                <td>${escapeHtml(p.tenantId)}</td>
                <td>${escapeHtml(p.name)}</td>
                <td>${renderStatusBadge(p.status)}</td>
                <td>${formatDate(p.date)}</td>
                <td>${ucfirst(p.method)}</td>
                <td>${escapeHtml(p.remarks)}</td>
                <td>
                    <form action="${p.restoreUrl}" method="POST" class="restoreForm mb-0">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                        </button>
                    </form>
                </td>
            </tr>`;
    }

    const query = searchEl.value.trim().toLowerCase();
    const filteredData = sourceData.filter(p => {
        const searchableText = [
            p.tenantId, p.name, p.status, p.date, p.method, p.remarks
        ].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const start = (page - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const pageData = filteredData.slice(start, end);

    headEl.innerHTML = tableHeadHTML;
    bodyEl.innerHTML = "";
    if (pageData.length === 0) {
        bodyEl.innerHTML = `<tr><td colspan="7" class="text-muted py-3">${emptyText}</td></tr>`;
    } else {
        pageData.forEach((p) => {
            bodyEl.insertAdjacentHTML("beforeend", rowBuilderFn(p));
        });
    }

    paginationEl.innerHTML = buildPaginationUI(totalPages, page);
    listenerFn('#main-table-body');

    paginationEl.querySelectorAll(".page-link").forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const newPage = parseInt(e.target.dataset.page, 10);
            if (newPage) {
                renderDisplay(newPage);
            }
        });
    });
}


// --- Action Listeners (MODIFIED) ---
function attachActionListeners(tbodySelector) {
    const tableBody = document.querySelector(tbodySelector);
    if (!tableBody) return;

    // 🗃️ ARCHIVE BUTTONS
    tableBody.querySelectorAll('form.archive-form').forEach(form => {
        if (form.listenerAttached) return;
        form.listenerAttached = true;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                "Are you sure you want to archive this payment record?",
                "Yes, archive it", "Cancel",
                async () => {
                    try {
                        const response = await fetch(form.action, {
                            method: "POST", body: new FormData(form),
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        if (!response.ok) throw new Error("Network error");
                        const result = await response.json();

                        // ✅ MODIFIED: 3-step process
                        await showSuccess(result.message || "Payment archived successfully!"); // 1. Wait for user to click OK
                        await delay(3000); // 2. Wait 3 seconds
                        window.location.reload(); // 3. Reload
                    } catch (error) {
                        console.error(error);
                        showError("An error occurred while archiving.");
                    }
                }
            );
        });
    });

    // 🔁 RESTORE BUTTONS
    tableBody.querySelectorAll("form.restoreForm").forEach(form => {
        if (form.listenerAttached) return;
        form.listenerAttached = true;

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            confirmAction(
                "Do you want to restore this archived payment?",
                "Yes, restore it", "Cancel",
                async () => {
                    try {
                        const response = await fetch(form.action, {
                            method: "POST", body: new FormData(form),
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        if (!response.ok) throw new Error("Network error");
                        const result = await response.json();

                        // ✅ MODIFIED: 3-step process
                        await showSuccess(result.message || "Payment restored successfully!"); // 1. Wait for user to click OK
                        await delay(3000); // 2. Wait 3 seconds
                        window.location.reload(); // 3. Reload
                    } catch (error) {
                        console.error(error);
                        showError("An error occurred while restoring.");
                    }
                }
            );
        });
    });

    // 📄 DOWNLOAD BUTTONS (Unchanged)
    tableBody.querySelectorAll('a.download-btn').forEach(link => {
        if (link.listenerAttached) return;
        link.listenerAttached = true;

        link.addEventListener('click', function (e) {
            e.preventDefault();
            confirmAction(
                "Do you want to download this payment invoice?",
                "Yes, download it", "Cancel",
                () => {
                    window.location.href = link.href;
                }
            );
        });
    });
}

// --- UI Helpers (Unchanged) ---
function renderStatusBadge(status) {
    if (status === 'paid') {
        return '<span class="badge bg-success">Paid</span>';
    } else if (status === 'partial') {
        return '<span class="badge bg-warning text-dark">Partial</span>';
    } else {
        return '<span class="badge bg-danger">Unpaid</span>';
    }
}
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: '2-digit', year: 'numeric'
    });
}
function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}
function escapeHtml(str) {
    return String(str || "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
function buildPaginationUI(totalPages, currentPage) {
    if (totalPages <= 1) return "";
    let html = `<nav><ul class="pagination pagination-sm mb-0">`;

    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">&laquo;</a>
             </li>`;

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

    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">&raquo;</a>
             </li>`;
    html += `</ul></nav>`;
    return html;
}

// --- Initialization (Unchanged) ---
document.addEventListener('DOMContentLoaded', () => {
    hydrateAllData();

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
            renderDisplay(1);
        });
        btnArchived.addEventListener("click", () => {
            if (currentView === 'archived') return;
            currentView = 'archived';
            btnArchived.classList.add("active", "btn-action");
            btnArchived.classList.remove("btn-outline-blue");
            btnActive.classList.remove("active", "btn-action");
            btnActive.classList.add("btn-outline-blue");
            renderDisplay(1);
        });
    }

    const searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            renderDisplay(1);
        });
    }

    renderDisplay(1);
});
</script>
@endpush
@endsection