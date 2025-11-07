{{-- resources/views/admin/maintenance.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Maintenance</h2>
        <button class="btn btn-action rounded-pill fw-bold px-4" onclick="fetchArchivedMaintenance()">
      <i class="bi bi-archive-fill me-1"></i> Archived Maintenance
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
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>MAINTENANCE REQUESTS</span>
            {{-- We can re-add the "Add Request" button if admins can create them --}}
            {{-- <a href="#" class="btn btn-sm text-white fw-semibold" ...>+ Add Request</a> --}}
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
                    <tbody>
                        @forelse ($requests as $request)
                        <tr>
                            <td class="ps-4">{{ $request->id }}</td>
                            <td class_="fw-bold">
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
                                {{-- Update Status Button --}}
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
                                
                                {{-- Archive Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="archiveMaintenance({{ $request->id }})" 
                                        title="Archive">
                                    <i class="bi bi-archive-fill"></i>
                                </button>
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
    </div>
</div>

<!-- =============================================== -->
<!-- Update Maintenance Modal -->
<!-- =============================================== -->
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
                    
                    <hr>

                    <div class="mb-3">
                        <label for="modalStatus" class="form-label small fw-semibold">Status</label>
                        <select id="modalStatus" name="status" class="form-select rounded-pill">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    {{-- Conditional Field: Scheduled Date --}}
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

<div class="modal fade" id="archivedMaintenanceModal" tabindex="-1" aria-labelledby="archivedMaintenanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            {{-- Header --}}
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540);">
                <h5 class="modal-title fw-bold" id="archivedMaintenanceLabel">
                    <i class="bi bi-archive me-2"></i> Archived Maintenance Requests
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body bg-light p-0">
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-start mb-0 mx-auto" style="width: auto !important;">
                        <thead class="table-light small text-uppercase text-secondary">
                            <tr>
                                <th class="fw-semibold">ID</th>
                                <th class="fw-semibold">Tenant</th>
                                <th class="fw-semibold">Unit</th>
                                <th class="fw-semibold">Issue Type</th>
                                <th class="fw-semibold">Description</th>
                                <th class="fw-semibold">Date Submitted</th>
                                <th class="fw-semibold">Status</th>
                                <th class="fw-semibold pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="archivedMaintenanceTable">
                            <tr>
                                <td colspan="8" class="py-4 text-muted text-center">Loading archived requests...</td>
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
</div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateModal = document.getElementById('updateMaintenanceModal');
    if (updateModal) {
        const modalForm = document.getElementById('updateMaintenanceForm');
        const modalTenantName = document.getElementById('modalTenantName');
        const modalRequestDesc = document.getElementById('modalRequestDesc');
        const modalStatus = document.getElementById('modalStatus');
        const modalScheduledDate = document.getElementById('modalScheduledDate');
        const modalNotes = document.getElementById('modalNotes');
        const scheduledDateContainer = document.getElementById('scheduledDateContainer');

        // Function to toggle scheduled date visibility
        function toggleScheduledDate() {
            if (modalStatus.value === 'In Progress') {
                scheduledDateContainer.style.display = 'block';
                modalScheduledDate.setAttribute('required', 'required');
            } else {
                scheduledDateContainer.style.display = 'none';
                modalScheduledDate.removeAttribute('required');
            }
        }

        // Add event listener to status dropdown
        modalStatus.addEventListener('change', toggleScheduledDate);

        // Handle modal opening
        updateModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal

            // Extract data from data-bs-* attributes
            const updateUrl = button.getAttribute('data-update-url');
            const tenantName = button.getAttribute('data-tenant-name');
            const requestDesc = button.getAttribute('data-request-desc');
            const currentStatus = button.getAttribute('data-current-status');
            const scheduledDate = button.getAttribute('data-scheduled-date');
            const notes = button.getAttribute('data-notes');

            // Populate the modal
            modalForm.setAttribute('action', updateUrl);
            modalTenantName.value = tenantName;
            modalRequestDesc.value = requestDesc;
            modalStatus.value = currentStatus;
            modalScheduledDate.value = scheduledDate;
            modalNotes.value = notes;

            // Trigger the visibility check for the scheduled date
            toggleScheduledDate();
        });
    }

    // If there are validation errors, find the modal and show it
    @if ($errors->any())
        const modalToOpen = new bootstrap.Modal(document.getElementById('updateMaintenanceModal'));
        modalToOpen.show();
    @endif

    // ⬇️ MOVED THIS LISTENER INSIDE DOMContentLoaded ⬇️
    // 🔍 Search functionality for archived maintenance
    const searchInput = document.getElementById('searchArchivedMaintenance');
    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            const input = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#archivedMaintenanceTable tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });
    }

}); // ⬅️ ADDED THE CLOSING BRACE AND PARENTHESIS HERE

// ⬇️ THESE FUNCTIONS ARE NOW GLOBAL (OUTSIDE DOMContentLoaded) SO ONCLICK WORKS ⬇️

/**
 * Archives a maintenance request.
 */
async function archiveMaintenance(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.'); // Assumes showError helper exists

    // Assumes confirmAction helper exists
    confirmAction(
        'Are you sure you want to archive this request?',
        'Yes, archive it',
        'Cancel',
        async () => {
            try {
                const response = await fetch(`/api/maintenance/${id}`, { 
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Failed to archive request.');

                showSuccess('Request archived successfully!'); // Assumes showSuccess helper exists
                location.reload(); // Reload page to update the server-rendered list
            } catch (err) {
                console.error(err);
                showError('Error archiving request: ' + err.message);
            }
        }
    );
}

/**
 * Fetches and displays archived maintenance requests in the modal.
 * Assumes you have an API route: GET /api/maintenance/archived
 */
async function fetchArchivedMaintenance() {
    const token = sessionStorage.getItem('admin_api_token');
    const modalEl = document.getElementById('archivedMaintenanceModal');
    const modal = new bootstrap.Modal(modalEl);
    const tableBody = document.getElementById('archivedMaintenanceTable');

    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Loading archived requests...</td></tr>';

    if (!token) {
        showError('Missing authorization token.');
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Missing authorization token.</td></tr>';
        modal.show();
        return;
    }

    try {
        const res = await fetch('/api/maintenance/archived', { 
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });

       const data = await res.json();

        if (!Array.isArray(data) || data.length === 0) {
         tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No archived requests.</td></tr>';
            modal.show();
            return;
        }

        tableBody.innerHTML = data.map((r, i) => {
            // JS logic for badge
            let badgeClass = 'bg-secondary';
            if (r.status === 'Completed') badgeClass = 'bg-success';
           if (r.status === 'In Progress') badgeClass = 'bg-primary';

            // JS logic for date
            const formattedDate = new Date(r.created_at).toLocaleDateString('en-US', {
             year: 'numeric', month: 'short', day: 'numeric'
           });

            // Handle potential nulls (assuming API returns nested tenant/unit data)
            const tenantName = `${r.tenant?.first_name ?? 'N/A'} ${r.tenant?.last_name ?? ''}`;
            const unitTitle = r.tenant?.unit?.title ?? 'N/A';

            return `
                <tr>
                    <td>${r.id}</td>
                    <td>${tenantName}</td>
                    <td>${unitTitle}</td>
             <td>${r.category ?? 'N/A'}</td>
                    <td class="text-start" style="max-width: 300px;">
                        <small class="d-block text-truncate">${r.description ?? ''}</small>
                    </td>
                    <td>${formattedDate}</td>
                    <td><span class="badge ${badgeClass}">${r.status}</span></td>
                    <td class="pe-4">
                        <button class="btn btn-outline-success btn-sm" onclick="restoreMaintenance(${r.id})">
                            <i class="bi bi-arrow-clockwise"></i> Restore
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        modal.show();
    } catch (err) {
     console.error(err);
        showError('Error loading archived requests: ' + err.message);
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error loading archived data.</td></tr>`;
         modal.show();
    }
}

/**
 * Restores an archived maintenance request.
 * Assumes you have an API route: POST /api/maintenance/restore/{id}
 */
async function restoreMaintenance(id) {
    const token = sessionStorage.getItem('admin_api_token');
    if (!token) return showError('Missing authorization token.');

    confirmAction(
        'Do you want to restore this request?',
        'Yes, restore it',
        'Cancel',
        async () => {
            try {
                const res = await fetch(`/api/maintenance/restore/${id}`, { 
                 method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });

                const result = await res.json();
                if (!res.ok) throw new Error(result.message || `Failed (${res.status})`);

                showSuccess('Request restored successfully!');
                bootstrap.Modal.getInstance(document.getElementById('archivedMaintenanceModal')).hide();
             location.reload(); // Reload page to update the main list
            } catch (err) {
                console.error(err);
              showError('Error restoring request: ' + err.message);
            }
        }
    );
}

// ⬅️ REMOVED THE MISPLACED '}' FROM HERE

</script>
@endsection