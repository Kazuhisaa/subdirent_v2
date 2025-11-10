@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header (MODIFIED) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-blue-900">Maintenance</h2>
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

    {{-- Session Error Message (For Modal Validation) --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        There were some errors with your submission. Please check the update modal.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ✅ NEW: UNIFIED Maintenance Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">

            {{-- Title changes dynamically --}}
            <span id="table-title">MAINTENANCE REQUESTS</span>

            {{-- Search bar is now generic --}}
            <input type="text" id="table-search" class="form-control form-control-sm"
                   style="flex-basis: 300px;" placeholder="Search requests...">
        </div>

        {{-- Maintenance Requests Table --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Table is now generic --}}
                <table class="table align-middle mb-0 tenant-table text-center">
                    {{-- Head changes dynamically --}}
                    <thead class="table-light small text-uppercase text-secondary" id="main-table-head">
                        {{-- Content injected by JS --}}
                    </thead>
                    {{-- Body changes dynamically --}}
                    <tbody id="main-table-body">
                        <tr><td colspan="8" class="text-center py-4">Loading requests...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination is now generic --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="main-pagination-container">
            {{-- Main pagination is injected here by JS --}}
        </div>
    </div>
</div>

{{--
======================================================================
MODALS (Update Modal is kept, Archived Modal is removed)
======================================================================
--}}

{{-- Update Maintenance Modal (Unchanged) --}}
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

{{-- Note: The '#archivedMaintenanceModal' has been removed. --}}


{{--
======================================================================
✅ NEW: DATA HYDRATION SOURCE
This hidden div contains your original Blade loops for the script to read.
======================================================================
--}}
<div id="data-hydration-source" style="display: none;">

    {{-- Original Active Requests Table --}}
    <table>
        <tbody id="maintenance-table-original">
            @forelse ($requests as $request)
            <tr class="maintenance-row"
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
                {{-- Data attributes are all that matter --}}
            </tr>
            @empty
            @endforelse
        </tbody>
    </table>

    {{-- Original Archived Requests Table --}}
    <table>
        <tbody id="archived-table-original">
            @isset($archivedRequests)
                @forelse($archivedRequests as $archived)
                 <tr class="archived-row"
                    data-tenant-name="{{ $archived->tenant->first_name ?? 'N/A' }} {{ $archived->tenant->last_name ?? '' }}"
                    data-unit="{{ $archived->tenant->unit->title ?? 'N/A' }}"
                    data-category="{{ $archived->category }}"
                    data-description="{{ $archived->description }}" {{-- Added description for search --}}
                    data-date-submitted="{{ $archived->created_at->format('M d, Y') }}"
                    data-status="{{ $archived->status }}"
                    data-restore-url="{{ route('admin.maintenance.restore', $archived) }}" {{-- Fixed route --}}
                >
                    {{-- Data attributes are all that matter --}}
                </tr>
                @empty
                @endforelse
            @endisset
        </tbody>
    </table>

</div> {{-- End of data-hydration-source --}}

@endsection

{{--
======================================================================
PAGE SCRIPT (REFACTORED)
======================================================================
--}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // --- Global State ---
    let allRequests = [];
    let allArchivedRequests = [];
    let currentView = 'active'; // 'active' or 'archived'
    const ROWS_PER_PAGE = 10;

    // --- Modal Elements (Unchanged) ---
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
    // This script assumes 'alerts.js' provides:
    // showSuccess(), showError(), and confirmAction()

    // --- Update Modal Logic (Unchanged) ---
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

    // --- Update Form Submission (Unchanged) ---
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

    // --- NEW: Data Hydration ---
    /**
     * Reads data from the server-rendered hidden tables into JS arrays.
     */
    function hydrateAllData() {
        // Hydrate active requests
        allRequests = [];
        document.querySelectorAll('#maintenance-table-original tr.maintenance-row').forEach(row => {
            allRequests.push(row.dataset);
        });

        // ✅ MODIFIED: New multi-level sorting
        allRequests.sort((a, b) => {
            const statusOrder = {
                'Pending': 1,
                'In Progress': 2,
                'Completed': 3,
            };
            const statusA = statusOrder[a.status] || 4; // Get order for A
            const statusB = statusOrder[b.status] || 4; // Get order for B

            // 1. Compare by status
            if (statusA !== statusB) {
                return statusA - statusB; // Sorts 1 (Pending), 2 (In Progress), 3 (Completed)
            }

            // 2. If status is the same, sort by latest date submitted
            return new Date(b.dateSubmitted) - new Date(a.dateSubmitted);
        });

        // Hydrate archived requests
        allArchivedRequests = [];
        document.querySelectorAll('#archived-table-original tr.archived-row').forEach(row => {
            allArchivedRequests.push(row.dataset);
        });
        // Sort latest first (by date)
        allArchivedRequests.sort((a, b) => new Date(b.dateSubmitted) - new Date(a.dateSubmitted));

        console.log(`Hydrated ${allRequests.length} requests and ${allArchivedRequests.length} archived requests.`);
    }

    // --- NEW: UI Helpers ---
    function escapeHtml(str) {
        return String(str || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * ✅ MODIFIED: Pending status now uses bg-warning
     */
    function getStatusBadge(status) {
        if (status === 'Completed') return '<span class="badge bg-success">Completed</span>';
        if (status === 'In Progress') return '<span class="badge bg-primary">In Progress</span>';
        return '<span class="badge bg-warning text-dark">Pending</span>'; // Default is Pending
    }

    // --- NEW: Unified Render Function (Unchanged) ---
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

        const activeTableHead = `
            <tr>
                <th class="fw-semibold">Tenant</th>
                <th class="fw-semibold">Unit</th>
                <th class="fw-semibold">Issue Type</th>
                <th class="fw-semibold">Description</th>
                <th class="fw-semibold">Date Submitted</th>
                <th class="fw-semibold">Status</th>
                <th class="fw-semibold pe-4">Actions</th>
            </tr>`;

        const archivedTableHead = `
            <tr>
                <th class="fw-semibold">Tenant</th>
                <th class="fw-semibold">Unit</th>
                <th class="fw-semibold">Issue Type</th>
                <th class="fw-semibold">Date Submitted</th>
                <th class="fw-semibold">Status</th>
                <th class="fw-semibold pe-4">Action</th>
            </tr>`;

        if (currentView === 'active') {
            titleEl.textContent = 'MAINTENANCE REQUESTS';
            searchEl.placeholder = 'Search active requests...';
            sourceData = allRequests;
            emptyText = 'No active maintenance requests found.';
            tableHeadHTML = activeTableHead;

            rowBuilderFn = (r) => `
                <tr>
                    <td class="fw-bold">${escapeHtml(r.tenantName)}</td>
                    <td>${escapeHtml(r.unit)}</td>
                    <td>${escapeHtml(r.category)}</td>
                    <td class="text-start" style="max-width: 300px;">
                        <small class="d-block text-truncate">${escapeHtml(r.description)}</small>
                    </td>
                    <td>${escapeHtml(r.dateSubmitted)}</td>
                    <td>${getStatusBadge(r.status)}</td>
                    <td class="pe-4">
                        <button type="button" class="btn btn-sm btn-outline-blue"
                            data-bs-toggle="modal"
                            data-bs-target="#updateMaintenanceModal"
                            data-request-id="${escapeHtml(r.id)}"
                            data-tenant-name="${escapeHtml(r.tenantName)}"
                            data-request-desc="${escapeHtml(r.description)}"
                            data-current-status="${escapeHtml(r.status)}"
                            data-scheduled-date="${escapeHtml(r.scheduledDate)}"
                            data-notes="${escapeHtml(r.notes)}"
                            data-update-url="${escapeHtml(r.updateUrl)}"
                            data-request-urgency="${escapeHtml(r.urgency)}"
                            data-photo-url="${escapeHtml(r.photoUrl)}"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="${escapeHtml(r.archiveUrl)}" method="POST" class="d-inline archive-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-archive-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>`;

        } else { // 'archived'
            titleEl.textContent = 'ARCHIVED REQUESTS';
            searchEl.placeholder = 'Search archived requests...';
            sourceData = allArchivedRequests;
            emptyText = 'No archived maintenance requests found.';
            tableHeadHTML = archivedTableHead;

            rowBuilderFn = (r) => `
                <tr>
                    <td>${escapeHtml(r.tenantName)}</td>
                    <td>${escapeHtml(r.unit)}</td>
                    <td>${escapeHtml(r.category)}</td>
                    <td>${escapeHtml(r.dateSubmitted)}</td>
                    <td>${getStatusBadge(r.status)}</td>
                    <td class="pe-4">
                        <form action="${escapeHtml(r.restoreUrl)}" method="POST" class="d-inline restore-form">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                            </button>
                        </form>
                    </td>
                </tr>`;
        }

        const query = searchEl.value.trim().toLowerCase();
        const filteredData = sourceData.filter(r => {
            const searchableText = [
                r.id, r.tenantName, r.unit, r.category, r.description, r.status
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
            bodyEl.innerHTML = `<tr><td colspan="8" class="text-muted py-3">${emptyText}</td></tr>`;
        } else {
            pageData.forEach((r) => {
                bodyEl.insertAdjacentHTML("beforeend", rowBuilderFn(r));
            });
        }

        paginationEl.innerHTML = buildPaginationUI(totalPages, page);
        attachActionListeners('#main-table-body');

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

    // --- NEW: Centralized Action Listeners (Unchanged) ---
    function attachActionListeners(tbodySelector) {
        const tableBody = document.querySelector(tbodySelector);
        if (!tableBody) return;

        // Archive confirmation
        tableBody.querySelectorAll('.archive-form').forEach(form => {
            if (form.listenerAttached) return;
            form.listenerAttached = true;
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

        // Restore confirmation
        tableBody.querySelectorAll('.restore-form').forEach(form => {
            if (form.listenerAttached) return;
            form.listenerAttached = true;
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
    }

    // --- Pagination UI Builder (Unchanged) ---
    function buildPaginationUI(totalPages, currentPage) {
        if (totalPages <= 1) return "";
        let html = `<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">`;
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>`;

        const pagesToShow = [];
        const maxPagesToShow = 5;
        if (totalPages <= maxPagesToShow) {
            for (let i = 1; i <= totalPages; i++) pagesToShow.push(i);
        } else {
            pagesToShow.push(1);
            let start = Math.max(2, currentPage - 1);
            let end = Math.min(totalPages - 1, currentPage + 1);
            if (currentPage > 3) pagesToShow.push('...');
            if (currentPage === 3) start = 2;
            if (currentPage === totalPages - 2) end = totalPages - 1;
            for (let i = start; i <= end; i++) {
                if (!pagesToShow.includes(i)) pagesToShow.push(i);
            }
            if (currentPage < totalPages - 2) {
                if (!pagesToShow.includes('...')) pagesToShow.push('...');
            }
            if (!pagesToShow.includes(totalPages)) pagesToShow.push(totalPages);
        }
        pagesToShow.forEach(p => {
            if (p === '...') {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            } else {
                html += `<li class="page-item ${p === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            }
        });
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>`;
        html += `</ul></nav>`;
        return html;
    }

    // --- Auto-open modal (Unchanged) ---
    @if ($errors->any())
        const modalToOpen = new bootstrap.Modal(document.getElementById('updateMaintenanceModal'));
        modalToOpen.show();
    @endif

    // --- Session Success Alert (Unchanged) ---
    @if (session('success'))
        showSuccess("{{ session('success') }}");
    @endif

    // --- NEW: Initialization (Unchanged) ---
    hydrateAllData();

    const btnActive = document.querySelector("#btn-view-active");
    const btnArchived = document.querySelector("#btn-view-archived");

    if (btnActive && btnArchived) {
        btnActive.addEventListener("click", () => {
            if (currentView === 'active') return;
            currentView = 'active';
            btnActive.classList.add("active", "btn-action");
            btnActive.classList.remove("btn-outl<ctrl61>ine-blue");
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