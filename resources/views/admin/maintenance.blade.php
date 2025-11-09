@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Maintenance</h2>
        <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedMaintenanceModal">
            <i class="bi bi-archive-fill me-1"></i> Archived Requests
        </button>
    </div>
    
    {{-- Session Error Message (For Modal Validation) --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        There were some errors with your submission. Please check the update modal.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    {{-- Note: The 'success' message banner has been removed, as it's now handled by SweetAlert in the script --}}

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
                    {{-- This ID is required for search and pagination --}}
                    <tbody id="maintenance-table-body">
                        @forelse ($requests as $request)
                        {{-- These data-* attributes are all required for the script to work --}}
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
                            data-urgency="{{ $request->urgency }}" 
                            data-photo-url="{{ $request->photo ? asset($request->photo) : '' }}"
                        >
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
                                {{-- This button opens the "Update" modal --}}
                                <button type="button" class="btn btn-sm btn-outline-blue" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateMaintenanceModal"
                                    data-request-id="{{ $request->id }}"
                                    data-tenant-name="{{ $request->tenant->first_name ?? 'N/A' }} {{ $request->tenant->last_name ?? '' }}"
                                    data-request-desc="{{ $request->description }}"
                                    data-current-status="{{ $request->status }}"
                                    data-scheduled-date="{{ $request->scheduled_date ? \Carbon\Carbon::parse($request->scheduled_date)->format('Y-m-d') : '' }}"
                                    data-notes="{{ $request->notes }}"
                                    data-update-url="{{ route('admin.maintenance.update', $request) }}"
                                    data-request-urgency="{{ $request->urgency }}"
                                    data-photo-url="{{ $request->photo ? asset($request->photo) : '' }}"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                {{-- This form is for archiving --}}
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

        {{-- ✅ CRITICAL: This ID is required for pagination --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="maintenance-pagination-container">
            {{-- Main pagination is injected here by JS --}}
        </div>
    </div>
</div>

{{-- 
======================================================================
MODALS
======================================================================
--}}

{{-- Update Maintenance Modal --}}
<div class="modal fade" id="updateMaintenanceModal" tabindex="-1" aria-labelledby="updateMaintenanceModalLabel" aria-hidden="true">
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

                    <div class="mb-3" id="modalPhotoContainer" style="display: none;">
                        <label class="form-label small">Submitted Photo</label>
                        <a id="modalPhotoLink" href="" target="_blank">
                            <img id="modalPhotoImage" src="" alt="Maintenance Photo" class="img-fluid rounded border" style="max-height: 300px; width: 100%; object-fit: cover;">
                        </a>
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

{{-- Archived Maintenance Modal --}}
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
                    {{-- This ID is required for modal search --}}
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
                        {{-- This ID is required for modal search and pagination --}}
                        <tbody id="archived-table-body">
                            {{-- Check if variable exists to prevent errors --}}
                            @isset($archivedRequests)
                                @forelse($archivedRequests as $archived)
                                {{-- These data-* attributes are required for the script --}}
                                 <tr class="archived-row"
                                    data-id="{{ $archived->id }}"
                                    data-tenant-name="{{ $archived->tenant->first_name ?? 'N/A' }} {{ $archived->tenant->last_name ?? '' }}"
                                    data-unit="{{ $archived->tenant->unit->title ?? 'N/A' }}"
                                    data-category="{{ $archived->category }}"
                                    data-date-submitted="{{ $archived->created_at->format('M d, Y') }}"
                                    data-status="{{ $archived->status }}"
                                >
                                    <td class="ps-4">{{ $archived->id }}</td>
                                    <td>{{ $archived->tenant->first_name ?? 'N/A' }} {{ $archived->tenant->last_name ?? '' }}</td>
                                    <td>{{ $archived->tenant->unit->title ?? 'N/A' }}</td>
                                    <td>{{ $archived->category }}</td>
                                    <td>{{ $archived->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            if ($archived->status == 'Completed') $badgeClass = 'bg-success';
                                            if ($archived->status == 'In Progress') $badgeClass = 'bg-primary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $archived->status }}</span>
                                    </td>
                                    <td class="pe-4">
                                        {{-- This form is for restoring --}}
                                        <form action="{{ route('admin.maintenance.restore', $archived) }}" method="POST" class="d-inline restore-form">
                                            @csrf
                                            @method('PUT') 
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
            
            {{-- ✅ CRITICAL: This ID is required for modal pagination --}}
            <div class="modal-footer bg-white border-0 d-flex justify-content-center pt-3" id="archived-pagination-container">
                 {{-- Archived pagination is injected here by JS --}}
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
======================================================================
PAGE SCRIPT (FIXED PAGINATION)
======================================================================
--}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // --- Modal Elements ---
    const updateModal = document.getElementById('updateMaintenanceModal');
    const modalForm = document.getElementById('updateMaintenanceForm');
    const modalTenantName = document.getElementById('modalTenantName');
    const modalRequestDesc = document.getElementById('modalRequestDesc');
    const modalStatus = document.getElementById('modalStatus');
    const modalScheduledDate = document.getElementById('modalScheduledDate');
    const modalNotes = document.getElementById('modalNotes');
    const scheduledDateContainer = document.getElementById('scheduledDateContainer');
    const modalPhotoContainer = document.getElementById('modalPhotoContainer');
    const modalPhotoLink = document.getElementById('modalPhotoLink');
    const modalPhotoImage = document.getElementById('modalPhotoImage');

    // --- Note: ---
    // This script assumes your 'alerts.js' file provides 
    // global functions: showSuccess(), showError(), and confirmAction()

    // --- Toggle date visibility ---
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
    if (modalStatus) {
        modalStatus.addEventListener('change', toggleScheduledDate);
    }

    // --- When modal is shown ---
    if (updateModal) {
        updateModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const updateUrl = button.dataset.updateUrl;
            const tenantName = button.dataset.tenantName || 'N/A';
            const requestDesc = button.dataset.requestDesc || '';
            const currentStatus = button.dataset.currentStatus || 'Pending';
            const scheduledDate = button.dataset.scheduledDate || '';
            const notes = button.dataset.notes || '';
            const urgency = button.dataset.requestUrgency || 'Low';
            const photoUrl = button.dataset.photoUrl || '';

            modalForm.action = updateUrl;
            modalTenantName.value = tenantName;
            modalRequestDesc.value = requestDesc;
            modalStatus.value = currentStatus;
            modalScheduledDate.value = scheduledDate;
            modalNotes.value = notes;

            if (photoUrl && photoUrl.trim() !== '') {
                modalPhotoImage.src = photoUrl;
                modalPhotoLink.href = photoUrl;
                modalPhotoContainer.style.display = 'block';
            } else {
                modalPhotoContainer.style.display = 'none';
                modalPhotoImage.src = '';
                modalPhotoLink.href = '';
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const minDateStr = today.toISOString().split('T')[0];
            modalScheduledDate.setAttribute('min', minDateStr);

            let maxDate = new Date(today);
            if (urgency === 'High') maxDate.setDate(today.getDate() + 2);
            else if (urgency === 'Medium') maxDate.setDate(today.getDate() + 4);
            else maxDate.setDate(today.getDate() + 7);

            const maxDateStr = maxDate.toISOString().split('T')[0];
            modalScheduledDate.setAttribute('max', maxDateStr);

            toggleScheduledDate();
        });
    }

    // --- Handle form submission ---
    if (modalForm) {
        modalForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(modalForm);
            try {
                const response = await fetch(modalForm.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                });

                const result = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showError(result.message || "Failed to update request.");
                    return;
                }

                showSuccess("Maintenance request updated successfully!");
                setTimeout(() => window.location.reload(), 1500);

            } catch (err) {
                console.error(err);
                showError("An error occurred while updating.");
            }
        });
    }

    // --- Archive confirmation (using global confirmAction) ---
    document.querySelectorAll('.archive-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                'Are you sure you want to archive this request?',
                'Yes, archive it',
                'Cancel',
                () => { e.target.submit(); }
            );
        });
    });

    // --- Restore confirmation (using global confirmAction) ---
    document.querySelectorAll('.restore-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            confirmAction(
                'Are you sure you want to restore this request?',
                'Yes, restore it',
                'Cancel',
                () => { e.target.submit(); }
            );
        });
    });

    // --- Auto-open modal if there are validation errors ---
    @if ($errors->any())
        const modalToOpen = new bootstrap.Modal(document.getElementById('updateMaintenanceModal'));
        modalToOpen.show();
    @endif

    // --- Helper function to build Bootstrap pagination ---
    function buildPaginationUI(totalPages, currentPage) {
        if (totalPages <= 1) return "";

        let html = `<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">`;

        // Previous Button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>`;

        // Page Numbers
        const pagesToShow = [];
        const maxPagesToShow = 5; // Max number of page links (e.g., 1 ... 3 4 5 ... 7)
        
        if (totalPages <= maxPagesToShow) {
            // Show all pages if total is small
            for (let i = 1; i <= totalPages; i++) {
                pagesToShow.push(i);
            }
        } else {
            // Logic for ellipsis
            pagesToShow.push(1); // Always show first page

            let start = Math.max(2, currentPage - 1);
            let end = Math.min(totalPages - 1, currentPage + 1);
            
            if (currentPage > 3) {
                pagesToShow.push('...'); // Ellipsis after page 1
            }

            if (currentPage === 3) start = 2;
            if (currentPage === totalPages - 2) end = totalPages - 1;

            for (let i = start; i <= end; i++) {
                if (!pagesToShow.includes(i)) {
                    pagesToShow.push(i);
                }
            }

            if (currentPage < totalPages - 2) {
                 if (!pagesToShow.includes('...')) pagesToShow.push('...'); // Ellipsis before last page
            }
            
            if (!pagesToShow.includes(totalPages)) {
                 pagesToShow.push(totalPages); // Always show last page
            }
        }

        pagesToShow.forEach(p => {
            if (p === '...') {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            } else {
                html += `
                    <li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                    </li>`;
            }
        });

        // Next Button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>`;

        html += `</ul></nav>`;
        return html;
    }


    // --- Main Table Search & Pagination ---
    const searchInput = document.getElementById('searchMaintenance');
    const tableBody = document.getElementById('maintenance-table-body');
    const paginationContainer = document.getElementById('maintenance-pagination-container');
    const rowsPerPage = 10;
    let currentPage = 1;

    let allRows = [];
    if (tableBody) {
        allRows = Array.from(tableBody.querySelectorAll('tr.maintenance-row'));
        allRows.sort((a, b) => {
            const dateA = new Date(a.dataset.dateSubmitted);
            const dateB = new Date(b.dataset.dateSubmitted);
            return dateB - dateA; // Sort latest first
        });
    }
    let filteredRows = [...allRows]; 

    function renderTable() {
        if (!tableBody) return;
        tableBody.innerHTML = ''; // Clear table
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        
        if (filteredRows.length === 0) {
            const emptyHtml = '<tr><td colspan="8" class="text-center py-4">No maintenance requests match your search.</td></tr>';
            tableBody.innerHTML = @json($requests->isEmpty()) ? '<tr><td colspan="8" class="text-center py-4">No maintenance requests found.</td></tr>' : emptyHtml;
        } else {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            filteredRows.slice(start, end).forEach(row => tableBody.appendChild(row));
        }
        
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationContainer) return;
        
        // Use the new Bootstrap pagination builder
        paginationContainer.innerHTML = buildPaginationUI(totalPages, currentPage);
        
        // Add listeners to the new .page-link <a> tags
        paginationContainer.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", e => {
                e.preventDefault();
                const newPage = parseInt(e.target.dataset.page, 10);
                if (newPage && newPage !== currentPage) {
                    currentPage = newPage;
                    renderTable();
                }
            });
        });
    }

    function filterMaintenanceRows() {
        const searchTerm = searchInput.value.toLowerCase();
        
        filteredRows = allRows.filter(row => {
            const tenant = row.dataset.tenantName.toLowerCase();
            const unit = row.dataset.unit.toLowerCase();
            const category = row.dataset.category.toLowerCase();
            const description = row.dataset.description.toLowerCase();
            const status = row.dataset.status.toLowerCase();

            return tenant.includes(searchTerm) ||
                   unit.includes(searchTerm) ||
                   category.includes(searchTerm) ||
                   description.includes(searchTerm) ||
                   status.includes(searchTerm);
        });
        
        currentPage = 1; // Reset to first page
        renderTable();
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', filterMaintenanceRows);
    }
    renderTable(); // Initial render


    // --- Archived Modal Search & Pagination ---
    const archivedModal = document.getElementById('archivedMaintenanceModal');
    const archivedSearchInput = document.getElementById('searchArchivedMaintenance');
    const archivedTableBody = document.getElementById('archived-table-body');
    const archivedPaginationContainer = document.getElementById('archived-pagination-container');
    const archivedRowsPerPage = 10;
    let currentArchivedPage = 1;

    let allArchivedRows = [];
    let filteredArchivedRows = [];

    function renderArchivedTable() {
        if (!archivedTableBody) return;
        archivedTableBody.innerHTML = '';
        const totalPages = Math.ceil(filteredArchivedRows.length / archivedRowsPerPage);
        
        if (filteredArchivedRows.length === 0) {
            const emptyHtml = '<tr><td colspan="7" class="text-center py-4">No archived requests match your search.</td></tr>';
            archivedTableBody.innerHTML = allArchivedRows.length === 0 ? '<tr><td colspan="7" class="text-center py-4">No archived requests found.</td></tr>' : emptyHtml;
        } else {
            const start = (currentArchivedPage - 1) * archivedRowsPerPage;
            const end = start + archivedRowsPerPage;
            filteredArchivedRows.slice(start, end).forEach(row => archivedTableBody.appendChild(row));
        }
        
        renderArchivedPagination(totalPages);
    }

    function renderArchivedPagination(totalPages) {
        if (!archivedPaginationContainer) return;

        // Use the new Bootstrap pagination builder
        archivedPaginationContainer.innerHTML = buildPaginationUI(totalPages, currentArchivedPage);

        // Add listeners to the new .page-link <a> tags
        archivedPaginationContainer.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", e => {
                e.preventDefault();
                const newPage = parseInt(e.target.dataset.page, 10);
                if (newPage && newPage !== currentArchivedPage) {
                    currentArchivedPage = newPage;
                    renderArchivedTable();
                }
            });
        });
    }

    function filterArchivedRows() {
        const searchTerm = archivedSearchInput.value.toLowerCase();
        
        filteredArchivedRows = allArchivedRows.filter(row => {
            const tenant = row.dataset.tenantName.toLowerCase();
            const unit = row.dataset.unit.toLowerCase();
            const category = row.dataset.category.toLowerCase();
            const status = row.dataset.status.toLowerCase();
            
            return tenant.includes(searchTerm) ||
                   unit.includes(searchTerm) ||
                   category.includes(searchTerm) ||
                   status.includes(searchTerm);
        });
        
        currentArchivedPage = 1;
        renderArchivedTable();
    }
    
    if (archivedSearchInput) {
        archivedSearchInput.addEventListener('keyup', filterArchivedRows);
    }
    
    if (archivedModal) {
        archivedModal.addEventListener('show.bs.modal', function() {
            if (!archivedTableBody) return;
            allArchivedRows = Array.from(archivedTableBody.querySelectorAll('tr.archived-row'));
            allArchivedRows.sort((a, b) => {
                const dateA = new Date(a.dataset.dateSubmitted);
                const dateB = new Date(b.dataset.dateSubmitted);
                return dateB - dateA; // Sort latest first
            });
            filteredArchivedRows = [...allArchivedRows];
            
            archivedSearchInput.value = '';
            currentArchivedPage = 1; // Reset to page 1
            renderArchivedTable();
        });
    }

    // --- Show SweetAlert for Session Success Message ---
    // This runs on page load after a redirect (e.g., from archive or restore)
    @if (session('success'))
        showSuccess("{{ session('success') }}");
    @endif

});
</script>
@endpush