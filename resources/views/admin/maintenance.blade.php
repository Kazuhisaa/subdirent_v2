{{-- resources/views/admin/maintenance.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Maintenance</h2>
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
                                <form action="{{ route('admin.maintenance.archive', $request) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to archive this request?');">
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
});
</script>
@endsection