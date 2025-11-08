@extends('admin.dashboard')

@section('page-title', 'Payments')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Page Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900">Payment Records</h3>
            <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedPaymentsModal">
                <i class="bi bi-archive-fill me-1"></i> Archived Payments
            </button>
        </div>
    </div>

    {{-- ✅ Summary Cards (These are still from the initial page load) --}}
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

    {{-- ✅ Payments Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">
            <span>PAYMENT RECORDS LIST</span>
            {{-- ✅ ADDED: Search Input --}}
            <input type="text" id="searchPayments" class="form-control form-control-sm" 
                   style="flex-basis: 300px;" placeholder="Search payments...">
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead class="table-light text-uppercase small text-secondary">
                        <tr>
                            <th>Tenant ID</th>
                            <th>Tenant Name</th>
                            <th>Payment Status</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    {{-- ✅ MODIFIED: We keep your Blade loop but add data-* attributes --}}
                    <tbody id="payments-table-body">
                        @forelse($payments as $payment)
                        {{-- This TR will be read by JS, then replaced --}}
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
                            
                            <td>{{ $payment->tenant_id }}</td>
                            <td>
                                @if($payment->tenant)
                                    {{ $payment->tenant->first_name ?? '' }} {{ $payment->tenant->last_name ?? '' }}
                                @else
                                    <span class="text-muted">Unknown Tenant</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($payment->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td>{{ ucfirst($payment->payment_method ?? '—') }}</td>
                            <td>{{ $payment->remarks ?? 'No remarks' }}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="{{ route('admin.payments.download', $payment->id) }}" class="btn btn-sm btn-outline-primary download-btn">
                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                    </a>
                                    <form action="{{ route('admin.admin.payments.archive', $payment->id) }}" method="POST" class="mb-0 archive-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Archive">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No payment records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ✅ ADDED: Pagination Container --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="payments-pagination-container">
            </div>
    </div>
</div>

{{-- ✅ Archived Payments Modal --}}
<div class="modal fade" id="archivedPaymentsModal" tabindex="-1" aria-labelledby="archivedPaymentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540);">
                <h5 class="modal-title fw-bold" id="archivedPaymentsLabel">Archived Payments</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-0">
                {{-- ✅ ADDED: Search for modal --}}
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <input type="text" id="searchArchivedPayments" class="form-control form-control-sm w-50"
                           placeholder="Search archived payments...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-light text-uppercase small text-secondary">
                            <tr>
                                <th>Tenant ID</th>
                                <th>Tenant Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- ✅ MODIFIED: We keep your Blade loop but add data-* attributes --}}
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
                                
                                <td>{{ $archived->tenant_id }}</td>
                                <td>
                                    @if($archived->tenant)
                                        {{ $archived->tenant->first_name ?? '' }} {{ $archived->tenant->last_name ?? '' }}
                                    @else
                                        <span class="text-muted">Unknown Tenant</span>
                                    @endif
                                </td>
                                <td>
                                    @if($archived->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($archived->payment_status === 'partial')
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($archived->payment_date)->format('M d, Y') }}</td>
                                <td>{{ ucfirst($archived->payment_method ?? '—') }}</td>
                                <td>{{ $archived->remarks ?? 'No remarks' }}</td>
                                <td>
                                    <form action="{{ route('admin.admin.payments.restore', $archived->id) }}" 
                                          method="POST" 
                                          class="restoreForm mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No archived payments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ✅ MODIFIED: Footer for pagination --}}
            <div class="modal-footer bg-white border-0 d-flex justify-content-between align-items-center">
                <div id="archived-pagination-container">
                    </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODIFIED: This script now reads from the DOM and uses your original action logic --}}
@push('scripts')
<script>
// --- Global State ---
let allPayments = [];
let allArchivedPayments = [];
const ROWS_PER_PAGE = 10;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// --- SweetAlert Helpers (from your original script) ---
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
function showSuccess(message) {
    Swal.fire({ title: 'Success!', text: message, icon: 'success', confirmButtonColor: '#198754' });
}
function showError(message) {
    Swal.fire({ title: 'Error!', text: message, icon: 'error', confirmButtonColor: '#dc3545' });
}

// --- NEW: Data Hydration ---
/**
 * Reads data from the server-rendered Blade table into JS arrays.
 */
function hydrateAllData() {
    // Hydrate active payments
    allPayments = [];
    document.querySelectorAll('#payments-table-body tr.payment-row').forEach(row => {
        allPayments.push(row.dataset);
    });
    // Sort latest first (by ID)
    allPayments.sort((a, b) => b.id - a.id);

    // Hydrate archived payments
    allArchivedPayments = [];
    document.querySelectorAll('#archived-table-body tr.archived-row').forEach(row => {
        allArchivedPayments.push(row.dataset);
    });
    // Sort latest first (by ID)
    allArchivedPayments.sort((a, b) => b.id - a.id);

    console.log(`Hydrated ${allPayments.length} payments and ${allArchivedPayments.length} archived payments.`);
}

// --- NEW: Render Functions ---
/**
 * Renders the active payments table and its pagination UI.
 */
function renderPaymentsDisplay(page = 1) {
    const body = document.getElementById('payments-table-body');
    const paginationContainer = document.getElementById('payments-pagination-container');
    
    const query = document.getElementById('searchPayments').value.toLowerCase();
    const filteredData = allPayments.filter(p => {
        const searchableText = [
            p.tenantId, p.name, p.status, p.date, p.method, p.remarks
        ].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const pageData = filteredData.slice((page - 1) * ROWS_PER_PAGE, page * ROWS_PER_PAGE);

    if (pageData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="py-3 text-muted">No payments found.</td></tr>`;
    } else {
        body.innerHTML = pageData.map(p => `
            <tr>
                <td>${p.tenantId}</td>
                <td>${p.name}</td>
                <td>${renderStatusBadge(p.status)}</td>
                <td>${formatDate(p.date)}</td>
                <td>${ucfirst(p.method)}</td>
                <td>${p.remarks}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="${p.downloadUrl}" class="btn btn-sm btn-outline-primary download-btn">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </a>
                        <form action="${p.archiveUrl}" method="POST" class="mb-0 archive-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Archive">
                                <i class="bi bi-archive"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderPaymentsDisplay');
    attachActionListeners('#payments-table-body');
}

/**
 * Renders the archived payments table and its pagination UI.
 */
function renderArchivedDisplay(page = 1) {
    const body = document.getElementById('archived-table-body');
    const paginationContainer = document.getElementById('archived-pagination-container');
    
    const query = document.getElementById('searchArchivedPayments').value.toLowerCase();
    const filteredData = allArchivedPayments.filter(p => {
        const searchableText = [
            p.tenantId, p.name, p.status, p.date, p.method, p.remarks
        ].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const pageData = filteredData.slice((page - 1) * ROWS_PER_PAGE, page * ROWS_PER_PAGE);

    if (pageData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="py-3 text-muted">No archived payments found.</td></tr>`;
    } else {
        body.innerHTML = pageData.map(p => `
            <tr>
                <td>${p.tenantId}</td>
                <td>${p.name}</td>
                <td>${renderStatusBadge(p.status)}</td>
                <td>${formatDate(p.date)}</td>
                <td>${ucfirst(p.method)}</td>
                <td>${p.remarks}</td>
                <td>
                    <form action="${p.restoreUrl}" method="POST" class="restoreForm mb-0">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderArchivedDisplay');
    attachActionListeners('#archived-table-body');
}

// --- NEW: Re-using your original action listener logic ---
/**
 * Attaches event listeners to the dynamically rendered buttons.
 * This logic is from your original script, adapted for dynamic content.
 */
function attachActionListeners(tbodySelector) {
    const tableBody = document.querySelector(tbodySelector);
    if (!tableBody) return;

    // 🗃️ ARCHIVE BUTTONS
    tableBody.querySelectorAll('form.archive-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                "Are you sure you want to archive this payment record?",
                "Yes, archive it", "Cancel",
                async () => {
                    // This is your original, correct logic
                    try {
                        const response = await fetch(form.action, {
                            method: "POST",
                            body: new FormData(form),
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        if (!response.ok) throw new Error("Network error");
                        const result = await response.json();
                        
                        await showSuccess(result.message || "Payment archived successfully!");
                        window.location.reload(); // Reload to get fresh data from server
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
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            confirmAction(
                "Do you want to restore this archived payment?",
                "Yes, restore it", "Cancel",
                async () => {
                    // This is your original, correct logic
                    try {
                        const response = await fetch(form.action, {
                            method: "POST",
                            body: new FormData(form),
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                        });
                        if (!response.ok) throw new Error("Network error");
                        const result = await response.json();

                        await showSuccess(result.message || "Payment restored successfully!");
                        window.location.reload(); // Reload to get fresh data
                    } catch (error) {
                        console.error(error);
                        showError("An error occurred while restoring.");
                    }
                }
            );
        });
    });

    // 📄 DOWNLOAD BUTTONS
    tableBody.querySelectorAll('a.download-btn').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            confirmAction(
                "Do you want to download this payment invoice?",
                "Yes, download it", "Cancel",
                () => {
                    window.location.href = link.href; // Just follow the link
                }
            );
        });
    });
}

// --- UI Helpers ---
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
    // Re-format the date string consistently
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: '2-digit', year: 'numeric'
    });
}

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function buildPaginationUI(totalPages, currentPage, renderFunction) {
    if (totalPages <= 1) return "";
    let html = `<nav><ul class="pagination pagination-sm mb-0">`;
    
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${currentPage - 1})">&laquo;</a>
    </li>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${i})">${i}</a>
        </li>`;
    }

    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${renderFunction}(${currentPage + 1})">&raquo;</a>
    </li>`;
    
    html += `</ul></nav>`;
    return html;
}

// --- Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    // 1. Read the data from the HTML
    hydrateAllData();
    
    // 2. Render the first page using the JS data
    renderPaymentsDisplay(1);
    renderArchivedDisplay(1); // Pre-render the modal table

    // 3. Attach search listeners
    document.getElementById('searchPayments')?.addEventListener('keyup', () => renderPaymentsDisplay(1));
    document.getElementById('searchArchivedPayments')?.addEventListener('keyup', () => renderArchivedDisplay(1));
});

// Make render functions globally accessible for pagination links
window.renderPaymentsDisplay = renderPaymentsDisplay;
window.renderArchivedDisplay = renderArchivedDisplay;
</script>
@endpush
@endsection