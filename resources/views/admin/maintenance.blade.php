@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Maintenance</h2>
        {{-- ✅ ADDED: Archive Button --}}
        <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedMaintenanceModal">
            <i class="bi bi-archive-fill me-1"></i> Archived Requests
        </button>
    </div>

    {{-- Session Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    {{-- Session Error Message --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        There were some errors with your submission. Please check the modal.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">
            <span>MAINTENANCE REQUESTS</span>
            <input type="text" id="searchMaintenance" class="form-control form-control-sm" 
                   style="flex-basis: 300px;" placeholder="Search requests...">
        </div>

        {{-- Maintenance Requests Table --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 tenant-table text-center">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
                            <th class="fw-semibold py-3 ps-4">ID</th>
                            <th class="fw-semibold">Tenant</th>
                            <th class="fw-semibold">Unit</th>
                            <th class="fw-semibold">Issue Type</th>
                            <th class="fw-semibold">Description</th>
                            <th class="fw-semibold">Date Submitted</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="maintenance-table-body">
                        @forelse ($requests as $request)
                        <tr class="maintenance-row"
                            data-id="{{ $request->id }}"
                            data-tenant-name="{{ $request->tenant->first_name ?? 'N/A' }} {{ $request->tenant->last_name ?? '' }}"
                            data-unit="{{ $request->tenant->unit->title ?? 'N/A' }}"
                            data-category="{{ $request->category }}"
                            data-description="{{ $request->description }}"
                            data-date-submitted="{{ $request->created_at->format('M d, Y') }}"
                            data-status="{{ $request->status }}"
                            data-scheduled-date="{{ $request->scheduled_date ? \Carbon\Carbon::parse($request->scheduled_date)->format('Y-m-d') : '' }}"
                            data-notes="{{ $request->notes }}"
                            data-update-url="{{ route('admin.maintenance.update', $request) }}"
                            data-archive-url="{{ route('admin.maintenance.archive', $request) }}"
                        >
                            {{-- This content is for initial load and will be replaced by JS --}}
                            <td class="ps-4">{{ $request->id }}</td>
                            <td class="fw-bold">
                                {{ $request->tenant->first_name ?? 'N/A' }} {{ $request->tenant->last_name ?? '' }}
                            </td>
                            <td>{{ $request->tenant->unit->title ?? 'N/A' }}</td>
                            <td>{{ $request->category }}</td>
                            <td class="text-start" style="max-width: 300px;">
                                <small class="d-block text-truncate">{{ $request->description }}</small>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = 'bg-secondary'; // Default
                                    if ($request->status == 'Completed') $badgeClass = 'bg-success';
                                    if ($request->status == 'In Progress') $badgeClass = 'bg-primary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $request->status }}</span>
                            </td>
                            <td class="pe-4">
                                <button type="button" class="btn btn-sm btn-outline-blue" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateMaintenanceModal"
                                    data-request-id="{{ $request->id }}"
                                    data-tenant-name="{{ $request->tenant->first_name ?? 'N/A' }} {{ $request->tenant->last_name ?? '' }}"
                                    data-request-desc="{{ $request->description }}"
                                    data-current-status="{{ $request->status }}"
                                    data-scheduled-date="{{ $request->scheduled_date ? \Carbon\Carbon::parse($request->scheduled_date)->format('Y-m-d') : '' }}"
                                    data-notes="{{ $request->notes }}"
                                    data-update-url="{{ route('admin.maintenance.update', $request) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.maintenance.archive', $request) }}" method="POST" class="d-inline archive-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No maintenance requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="maintenance-pagination-container">
            </div>
    </div>
</div>

<div class="modal fade" id="updateMaintenanceModal" tabindex="-1" aria-labelledby="updateMaintenanceModalLabel" aria-hidden="true">
    {{-- ... (Your entire update modal remains unchanged) ... --}}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <form id="updateMaintenanceForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold text-blue-900" id="updateMaintenanceModalLabel">Update Maintenance Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-0">
                    <div class="mb-3">
                        <label class="form-label small">Tenant</label>
                        <input type="text" id="modalTenantName" class="form-control rounded-pill" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Request</label>
                        <textarea id="modalRequestDesc" class="form-control rounded-3" rows="3" disabled></textarea>
                    </div>
                    
                    <hr>

                    <div class="mb-3">
                        <label for="modalStatus" class="form-label small fw-semibold">Status</label>
                        <select id="modalStatus" name="status" class="form-select rounded-pill">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="mb-3" id="scheduledDateContainer" style="display: none;">
                        <label for="modalScheduledDate" class="form-label small fw-semibold">Scheduled Date for Service</label>
                        <input type="date" id="modalScheduledDate" name="scheduled_date" class="form-control rounded-pill">
                        @error('scheduled_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="modalNotes" class="form-label small fw-semibold">Admin Notes</label>
                        <textarea id="modalNotes" name="notes" class="form-control rounded-3" rows="3" placeholder="Add notes for this request..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="archivedMaintenanceModal" tabindex="-1" aria-labelledby="archivedMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(90deg, #007BFF, #0A2540);">
                <h5 class="modal-title fw-bold" id="archivedMaintenanceModalLabel">
                    <i class="bi bi-archive-fill me-2"></i> Archived Maintenance Requests
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <input type="text" id="searchArchivedMaintenance" class="form-control form-control-sm w-50"
                           placeholder="Search archived requests...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-light text-uppercase small text-secondary">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Tenant</th>
                                <th>Unit</th>
                                <th>Issue Type</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th class="pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody id="archived-table-body">
                            {{-- This assumes $archivedRequests is passed from your controller --}}
                            @isset($archivedRequests)
                                @forelse($archivedRequests as $archived)
                                <tr class="archived-row"
                                    data-id="{{ $archived->id }}"
                                    data-tenant-name="{{ $archived->tenant->first_name ?? 'N/A' }} {{ $archived->tenant->last_name ?? '' }}"
                                    data-unit="{{ $archived->tenant->unit->title ?? 'N/A' }}"
                                    data-category="{{ $archived->category }}"
                                    data-description="{{ $archived->description }}"
                                    data-date-submitted="{{ $archived->created_at->format('M d, Y') }}"
                                    data-status="{{ $archived->status }}"
                                    data-restore-url="{{ route('admin.maintenance.restore', $archived) }}" 
                                    {{-- Assuming you have a 'admin.maintenance.restore' route --}}
                                >
                                    <td class="ps-4">{{ $archived->id }}</td>
                                    <td>{{ $archived->tenant->first_name ?? 'N/A' }} {{ $archived->tenant->last_name ?? '' }}</td>
                                    <td>{{ $archived->tenant->unit->title ?? 'N/A' }}</td>
                                    <td>{{ $archived->category }}</td>
                                    <td>{{ $archived->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary'; // Default
                                            if ($archived->status == 'Completed') $badgeClass = 'bg-success';
                                            if ($archived->status == 'In Progress') $badgeClass = 'bg-primary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $archived->status }}</span>
                                    </td>
                                    <td class="pe-4">
                                        {{-- This assumes you have a 'admin.maintenance.restore' route --}}
                                        <form action="{{ route('admin.maintenance.restore', $archived) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            @method('PUT') 
                                            {{-- Or POST, match your route definition --}}
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No archived requests found.</td>
                                </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-danger">Archived data not loaded. Check controller.</td>
                                </tr>
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 d-flex justify-content-between align-items-center">
                <div id="archived-pagination-container">
                    </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// --- Global State ---
let allRequests = [];
let allArchivedRequests = []; // ✅ ADDED
const ROWS_PER_PAGE = 10;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// --- SweetAlert Helpers ---
function confirmAction(title, confirmText, cancelText, onConfirm) {
    if (typeof Swal === 'undefined') {
        if (confirm(title)) onConfirm();
        return;
    }
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
    if (typeof Swal === 'undefined') { alert(message); return; }
    Swal.fire({ title: 'Success!', text: message, icon: 'success', confirmButtonColor: '#198754' });
}
function showError(message) {
    if (typeof Swal === 'undefined') { alert(message); return; }
    Swal.fire({ title: 'Error!', text: message, icon: 'error', confirmButtonColor: '#dc3545' });
}

// --- Data Hydration ---
function hydrateAllData() {
    // Hydrate active requests
    allRequests = [];
    document.querySelectorAll('#maintenance-table-body tr.maintenance-row').forEach(row => {
        allRequests.push(row.dataset);
    });
    allRequests.sort((a, b) => b.id - a.id); // Sort latest first

    // ✅ ADDED: Hydrate archived requests
    allArchivedRequests = [];
    document.querySelectorAll('#archived-table-body tr.archived-row').forEach(row => {
        allArchivedRequests.push(row.dataset);
    });
    allArchivedRequests.sort((a, b) => b.id - a.id); // Sort latest first
}

// --- Render Functions ---
function renderMaintenanceDisplay(page = 1) {
    const body = document.getElementById('maintenance-table-body');
    const paginationContainer = document.getElementById('maintenance-pagination-container');
    
    const query = document.getElementById('searchMaintenance').value.toLowerCase();
    const filteredData = allRequests.filter(req => {
        const searchableText = [
            req.id, req.tenantName, req.unit, req.category, req.description, req.status
        ].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const pageData = filteredData.slice((page - 1) * ROWS_PER_PAGE, page * ROWS_PER_PAGE);

    if (pageData.length === 0) {
        body.innerHTML = `<tr><td colspan="8" class="py-4 text-muted">No maintenance requests found.</td></tr>`;
    } else {
        body.innerHTML = pageData.map(req => {
            const descTruncated = req.description.length > 50 ? req.description.substring(0, 50) + '...' : req.description;
            
            return `
            <tr>
                <td class="ps-4">${req.id}</td>
                <td class="fw-bold">${req.tenantName}</td>
                <td>${req.unit}</td>
                <td>${req.category}</td>
                <td class="text-start" style="max-width: 300px;">
                    <small class="d-block text-truncate" title="${req.description}">${descTruncated}</small>
                </td>
                <td>${req.dateSubmitted}</td>
                <td>${renderStatusBadge(req.status)}</td>
                <td class="pe-4">
                    <button type="button" class="btn btn-sm btn-outline-blue" 
                        data-bs-toggle="modal" 
                        data-bs-target="#updateMaintenanceModal"
                        data-request-id="${req.id}"
                        data-tenant-name="${req.tenantName}"
                        data-request-desc="${req.description}"
                        data-current-status="${req.status}"
                        data-scheduled-date="${req.scheduledDate}"
                        data-notes="${req.notes}"
                        data-update-url="${req.updateUrl}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <form action="${req.archiveUrl}" method="POST" class="d-inline archive-form">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                            <i class="bi bi-archive-fill"></i>
                        </button>
                    </form>
                </td>
            </tr>
            `;
        }).join('');
    }

    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderMaintenanceDisplay');
    attachActionListeners('#maintenance-table-body');
}

// ✅ ADDED: Render function for archived modal
function renderArchivedDisplay(page = 1) {
    const body = document.getElementById('archived-table-body');
    const paginationContainer = document.getElementById('archived-pagination-container');
    
    const query = document.getElementById('searchArchivedMaintenance').value.toLowerCase();
    const filteredData = allArchivedRequests.filter(req => {
        const searchableText = [
            req.id, req.tenantName, req.unit, req.category, req.status
        ].join(' ').toLowerCase();
        return searchableText.includes(query);
    });

    const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
    const pageData = filteredData.slice((page - 1) * ROWS_PER_PAGE, page * ROWS_PER_PAGE);

    if (pageData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" class="py-4 text-muted">No archived requests found.</td></tr>`;
    } else {
        body.innerHTML = pageData.map(req => `
            <tr>
                <td class="ps-4">${req.id}</td>
                <td>${req.tenantName}</td>
                <td>${req.unit}</td>
                <td>${req.category}</td>
                <td>${req.dateSubmitted}</td>
                <td>${renderStatusBadge(req.status)}</td>
                <td class="pe-4">
                    <form action="${req.restoreUrl}" method="POST" class="d-inline restore-form">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PUT"> 
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    paginationContainer.innerHTML = buildPaginationUI(totalPages, page, 'renderArchivedDisplay');
    attachActionListeners('#archived-table-body'); // Attach listeners to new modal buttons
}

// ✅ MODIFIED: attachActionListeners now handles restore forms
function attachActionListeners(tbodySelector) {
    const tableBody = document.querySelector(tbodySelector);
    if (!tableBody) return;

    // Archive Forms
    tableBody.querySelectorAll('form.archive-form').forEach(form => {
        if (form.listenerAttached) return;
        form.listenerAttached = true;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                "Are you sure you want to archive this request?",
                "Yes, archive it", "Cancel",
                () => handleFormSubmit(form, "Request archived successfully!")
            );
        });
    });

    // Restore Forms
    tableBody.querySelectorAll('form.restore-form').forEach(form => {
        if (form.listenerAttached) return;
        form.listenerAttached = true;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                "Are you sure you want to restore this request?",
                "Yes, restore it", "Cancel",
                () => handleFormSubmit(form, "Request restored successfully!")
            );
        });
    });
}

// ✅ ADDED: Generic form submission handler for web routes
async function handleFormSubmit(form, successMessage) {
    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken,
            },
        });

        if (response.redirected) {
            await showSuccess(successMessage);
            window.location.href = response.url; // Follow redirect
            return;
        }

        if (!response.ok) {
            let errorMsg = "An error occurred.";
            try {
                const result = await response.json();
                errorMsg = result.message || errorMsg;
            } catch (e) {}
            throw new Error(errorMsg);
        }

        const result = await response.json();
        await showSuccess(result.message || successMessage);
        window.location.reload(); // Reload page
    } catch (error) {
        console.error(error);
        showError(error.message);
    }
}

// --- UI Helpers ---
function renderStatusBadge(status) {
    let badgeClass = 'bg-secondary'; // Default
    if (status == 'Completed') badgeClass = 'bg-success';
    if (status == 'In Progress') badgeClass = 'bg-primary';
    return `<span class="badge ${badgeClass}">${status}</span>`;
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
document.addEventListener('DOMContentLoaded', function () {
    // 1. HYDRATE DATA FROM BLADE
    hydrateAllData();

    // 2. RENDER INITIAL PAGES
    renderMaintenanceDisplay(1);
    renderArchivedDisplay(1); // Pre-render the modal table

    // 3. ATTACH SEARCH LISTENERS
    document.getElementById('searchMaintenance')?.addEventListener('keyup', () => renderMaintenanceDisplay(1));
    document.getElementById('searchArchivedMaintenance')?.addEventListener('keyup', () => renderArchivedDisplay(1)); // ✅ ADDED

    // 4. PRESERVE ORIGINAL MODAL LOGIC
    const updateModal = document.getElementById('updateMaintenanceModal');
    if (updateModal) {
        // ... (your existing modal logic remains unchanged) ...
        const modalForm = document.getElementById('updateMaintenanceForm');
        const modalTenantName = document.getElementById('modalTenantName');
        const modalRequestDesc = document.getElementById('modalRequestDesc');
        const modalStatus = document.getElementById('modalStatus');
        const modalScheduledDate = document.getElementById('modalScheduledDate');
        const modalNotes = document.getElementById('modalNotes');
        const scheduledDateContainer = document.getElementById('scheduledDateContainer');

        function toggleScheduledDate() {
            if (modalStatus.value === 'In Progress') {
                scheduledDateContainer.style.display = 'block';
                modalScheduledDate.setAttribute('required', 'required');
            } else {
                scheduledDateContainer.style.display = 'none';
                modalScheduledDate.removeAttribute('required');
                modalScheduledDate.value = '';
            }
        }
        modalStatus.addEventListener('change', toggleScheduledDate);

        updateModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const updateUrl = button.getAttribute('data-update-url');
            const tenantName = button.getAttribute('data-tenant-name');
            const requestDesc = button.getAttribute('data-request-desc');
            const currentStatus = button.getAttribute('data-current-status');
            const scheduledDate = button.getAttribute('data-scheduled-date');
            const notes = button.getAttribute('data-notes');

            modalForm.setAttribute('action', updateUrl);
            modalTenantName.value = tenantName;
            modalRequestDesc.value = requestDesc;
            modalStatus.value = currentStatus;
            modalScheduledDate.value = scheduledDate;
            modalNotes.value = notes;
            toggleScheduledDate();
        });
    }

    // 5. PRESERVE ORIGINAL ERROR HANDLING
    @if ($errors->any())
        console.warn("Validation errors detected. Opening modal.");
        const modalToOpen = new bootstrap.Modal(document.getElementById('updateMaintenanceModal'));
        modalToOpen.show();
    @endif
});

// Make render functions global
window.renderMaintenanceDisplay = renderMaintenanceDisplay;
window.renderArchivedDisplay = renderArchivedDisplay; // ✅ ADDED
</script>
@endpush
@endsection