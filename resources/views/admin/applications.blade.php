@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title (MODIFIED) --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900">Applications</h3>

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

    {{-- ✅ NEW: Wrapper for all Active content --}}
    <div id="active-view-content">
        {{-- Summary Cards (Unchanged) --}}
        <div class="row text-center mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm booking-card">
                    <div class="card-body">
                        <h6 class="card-title">Total (This Page)</h6>
                        <h3 class="fw-bold">{{ $applications->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm booking-card approved">
                    <div class="card-body">
                        <h6 class="card-title">Approved (This Page)</h6>
                        <h3 class="fw-bold">{{ $applications->where('status', 'Approved')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm booking-card rejected">
                    <div class="card-body">
                        <h6 class="card-title">Rejected (This Page)</h6>
                        <h3 class="fw-bold">{{ $applications->where('status', 'Rejected')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Applications List (Unchanged) --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
                 style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
                <span>APPLICATIONS LIST</span>
                <div class="d-flex" style="flex-basis: 300px;">
                    <input type="text" id="realtimeSearchInput" class="form-control form-control-sm"
                           placeholder="Filter visible applicants...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 text-center booking-table align-middle">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTableBody">
                            {{-- Your existing Blade loop is untouched --}}
                            @forelse($applications as $application)
                            <tr class="application-row"
                                data-searchable-text="{{ strtolower($application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name . ' ' . $application->email . ' ' . ($application->unit->unit_name ?? '')) }}">
                                <td>{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
                                <td>{{ $application->email }}</td>
                                <td>{{ $application->contact_num }}</td>
                                <td>
                                    @if($application->unit)
                                    <div class="d-flex flex-column align-items-center">
                                        <strong>{{ $application->unit->unit_name }}</strong>
                                        <small class="text-muted">{{ $application->unit->title }}</small>
                                    </div>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($application->unit_price)
                                    ₱{{ number_format($application->unit_price, 2) }}
                                    @elseif($application->unit)
                                    ₱{{ number_format($application->unit->unit_price, 2) }}
                                    @else — @endif
                                </td>
                                <td>
                                    @if($application->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($application->status === 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- All your action buttons are untouched --}}
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-info view-btn" data-bs-toggle="modal" data-bs-target="#viewApplicationModal" title="View Details"
                                            data-fullname="{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}"
                                            data-email="{{ $application->email }}"
                                            data-contact_num="{{ $application->contact_num }}"
                                            data-status="{{ $application->status }}"
                                            data-unit_name="{{ $application->unit->unit_name ?? 'N/A' }}"
                                            data-unit_title="{{ $application->unit->title ?? 'N/A' }}"
                                            data-unit_price_raw="{{ $application->unit_price ?? $application->unit->unit_price ?? 0 }}"
                                            data-downpayment="{{ $application->downpayment }}"
                                            data-contract_years="{{ $application->contract_years }}"
                                            data-contract_start="{{ $application->contract_start }}"
                                            data-remarks="{{ $application->remarks }}">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        @if($application->status === 'Pending')
                                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editApplicationModal" title="Edit"
                                            data-id="{{ $application->id }}"
                                            data-first_name="{{ $application->first_name }}"
                                            data-middle_name="{{ $application->middle_name }}"
                                            data-last_name="{{ $application->last_name }}"
                                            data-email="{{ $application->email }}"
                                            data-contact_num="{{ $application->contact_num }}"
                                            data-unit_id="{{ $application->unit_id }}"
                                            data-unit_price="{{$application->unit_price}}"
                                            data-downpayment="{{$application->downpayment}}"
                                            data-payment_due_date="{{ $application->payment_due_date }}"
                                            data-contract_years="{{ $application->contract_years }}"
                                            data-contract_duration="{{ $application->contract_duration }}"
                                            data-contract_start="{{ $application->contract_start }}"
                                            data-remarks="{{ $application->remarks }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="d-inline-block mb-0 form-approve">@csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="d-inline-block mb-0 form-reject">@csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-warning" title="Archive"
                                            onclick="archiveApplication({{ $application->id }})">
                                            <i class="bi bi-archive-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-row"><td colspan="7" class="text-muted py-3">No applications found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Your existing server-side pagination --}}
            @if ($applications->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3">
                {!! $applications->links() !!}
            </div>
            @endif
        </div>
    </div> {{-- End Active View Content --}}


    {{-- ✅ NEW: Archived Applications Card (Hidden by default) --}}
    <div class="card border-0 shadow-sm" id="archived-view-content" style="display: none;">
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center flex-wrap gy-2"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>ARCHIVED APPLICANTS</span>
            <input type="text" id="searchArchivedApplicants" class="form-control form-control-sm"
                   style="flex-basis: 300px;" placeholder="Search archived applicants...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light text-uppercase small text-secondary">
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    {{-- This ID is the same as your old modal, so the script will work --}}
                    <tbody id="archivedApplicationsBody">
                        <tr><td colspan="6" class="py-4 text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- NEW: Pagination container for archived list --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="archived-pagination-container">
            {{-- Archived pagination will be injected here --}}
        </div>
    </div>

</div>

{{--
    ===========================================
    MGA MODAL (Unchanged, except Archived Modal is removed)
    ===========================================
--}}
<div class="modal fade" id="editApplicationModal" tabindex="-1" aria-labelledby="editApplicationLabel" aria-hidden="true">
    {{-- ...all your edit modal content... --}}
    <div class="modal-dialog modal-lg">
     <div class="modal-content">
       <div class="modal-header bg-primary text-white">
         <h5 class="modal-title" id="editApplicationLabel">Edit Application</h5>
         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
       </div>
       <div class="modal-body">
         <form id="editApplicationForm">
           <input type="hidden" id="application_id">
           <div class="row mb-3">
             <div class="col-md-4">
               <label>First Name</label>
               <input type="text" id="first_name" class="form-control" required>
             </div>
             <div class="col-md-4">
               <label>Middle Name</label>
               <input type="text" id="middle_name" class="form-control">
             </div>
             <div class="col-md-4">
               <label>Last Name</label>
               <input type="text" id="last_name" class="form-control" required>
             </div>
           </div>
           <div class="row mb-3">
             <div class="col-md-6">
               <label>Email</label>
               <input type="email" id="email" class="form-control" required>
             </div>
             <div class="col-md-6">
               <label>Contact Number</label>
               <input type="text" id="contact_num" class="form-control" required>
             </div>
           </div>
           <div class="row mb-3">
             <div class="col-md-6">
               <label>Phase</label>
               <select id="phase_select" class="form-select" required>
                 <option value="">-- Select Phase --</option>
               </select>
             </div>
             <div class="col-md-6">
               <label>Unit</label>
               <select id="unit_select" class="form-select" required>
                 <option value="">-- Select Unit --</option>
               </select>
             </div>
           </div>
           <div class="row mb-3">
             <div class="col-md-6">
               <label>Downpayment</label>
               <input type="number" id="downpayment" class="form-control" min="1" step="0.1" required>
             </div>
             <div class="col-md-6">
               <label>Contract Years</label>
               <input type="number" id="contract_years" class="form-control" min="1" required>
              </div>
           </div>
         </form>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
         <button type="button" id="saveChangesBtn" class="btn btn-success">Save Changes</button>
       </div>
     </div>
    </div>
</div>


<div class="modal fade" id="viewApplicationModal" tabindex="-1" aria-labelledby="viewApplicationLabel" aria-hidden="true">
    {{-- ...all your view modal content... --}}
    <div class="modal-dialog modal-lg">
     <div class="modal-content">
       <div class="modal-header bg-info text-white">
         <h5 class="modal-title" id="viewApplicationLabel">View Application Details</h5>
         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
       </div>
       <div class="modal-body">
         <div class="row">
           <div class="col-md-6">
             <h5>Applicant Details</h5>
             <hr class="mt-0">
             <p><strong>Full Name:</strong> <span id="view_full_name"></span></p>
             <p><strong>Email:</strong> <span id="view_email"></span></p>
             <p><strong>Contact #:</strong> <span id="view_contact_num"></span></p>
             <p><strong>Status:</strong> <span id="view_status" class="badge"></span></p>
           </div>
           <div class="col-md-6">
             <h5>Application Details</h5>
             <hr class="mt-0">
             <p><strong>Unit Applied For:</strong> <span id="view_unit_name"></span></p>
             <p><strong>Unit Type:</strong> <span id="view_unit_title"></span></p>
             <p><strong>Total Unit Price:</strong> <span id="view_unit_price"></span></p>
             <p><strong>Downpayment:</strong> <span id="view_downpayment"></span></p>
             <p><strong>Contract Term:</strong> <span id="view_contract_years"></span></p>
             <p><strong>Contract Start:</strong> <span id="view_contract_start"></span></p>
           </div>
         </div>
         <div class="row mt-3">
           <div class="col-12">
             <h5>Remarks</h5>
             <hr class="mt-0">
             <p id="view_remarks" class="text-muted fst-italic"></p>
           </div>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       </div>
     </div>
    </div>
</div>

{{-- Archived Modal HTML is REMOVED --}}
{{-- <div class="modal fade" id="archivedModal" ...> ... </div> --}}

@endsection

{{--
    ===========================================
    MGA SCRIPT
    ===========================================
--}}
@push('scripts')
{{-- This script combines your original logic with the new toggle logic --}}
<script>
    // Global variable para sa all units
    let allUnits = [];

    // --- NEW: Global state for toggle and archived data ---
    let currentView = 'active';
    let allArchivedApplicants = [];
    let currentArchivedPage = 1;
    const ROWS_PER_PAGE = 10; // For archived pagination

    // --- NEW: Helper function for escaping HTML ---
    function escapeHtml(str) {
        return String(str || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // --- NEW: Helper function for pagination UI ---
    function buildPaginationUI(totalPages, currentPage) {
        if (totalPages <= 1) return "";
        let html = `<nav><ul class="pagination pagination-sm mb-0">`;

        // Prev
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">&laquo;</a></li>`;

        // Numbers
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
                html += `<li class="page-item ${p === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            }
        });

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">&raquo;</a></li>`;
        html += `</ul></nav>`;
        return html;
    }

    document.addEventListener('DOMContentLoaded', function() {

        // --- SweetAlert Fallbacks (defined once) ---
        const sa_confirmAction = window.confirmAction || function(title, confirmText, cancelText, callback) {
            if (confirm(title)) callback();
        };
        const sa_showSuccess = window.showSuccess || function(message) {
            alert(message);
        };
        const sa_showError = window.showError || function(message) {
            alert(message);
        };

        // --- CSRF Token (defined once) ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : '';

        // --- NEW: Toggle Button Listeners ---
        const btnActive = document.getElementById('btn-view-active');
        const btnArchived = document.getElementById('btn-view-archived');
        const activeContent = document.getElementById('active-view-content');
        const archivedContent = document.getElementById('archived-view-content');

        btnActive.addEventListener('click', () => {
            if (currentView === 'active') return;
            currentView = 'active';

            btnActive.classList.add('active', 'btn-action');
            btnActive.classList.remove('btn-outline-blue');
            btnArchived.classList.remove('active', 'btn-action');
            btnArchived.classList.add('btn-outline-blue');

            archivedContent.style.display = 'none';
            activeContent.style.display = '';
        });

        btnArchived.addEventListener('click', () => {
            if (currentView === 'archived') return;
            currentView = 'archived';

            btnArchived.classList.add('active', 'btn-action');
            btnArchived.classList.remove('btn-outline-blue');
            btnActive.classList.remove('active', 'btn-action');
            btnActive.classList.add('btn-outline-blue');

            activeContent.style.display = 'none';
            archivedContent.style.display = '';

            // Load data if this is the first click
            if (allArchivedApplicants.length === 0) {
                fetchArchivedApplicants();
            } else {
                // Just re-render if data is already in memory
                renderArchivedDisplay(1);
            }
        });

        // 1. EDIT Modal Listener (Unchanged)
        const editModalEl = document.getElementById('editApplicationModal');
        if(editModalEl) {
            editModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button || !button.classList.contains('edit-btn')) return;
                const applicationData = {
                    id: button.dataset.id,
                    first_name: button.dataset.first_name,
                    middle_name: button.dataset.middle_name,
                    last_name: button.dataset.last_name,
                    email: button.dataset.email,
                    contact_num: button.dataset.contact_num,
                    unit_id: button.dataset.unit_id,
                    unit_price: button.dataset.unit_price,
                    downpayment: button.dataset.downpayment,
                    payment_due_date: button.dataset.payment_due_date,
                    contract_years: button.dataset.contract_years,
                };
                populateEditModal(applicationData);
            });
        }

        // 2. VIEW Modal Listener (Unchanged)
        const viewModalEl = document.getElementById('viewApplicationModal');
        if(viewModalEl) {
            viewModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button || !button.classList.contains('view-btn')) return;
                const data = button.dataset;
                const formatDisplay = (value, prefix = '', suffix = '') => {
                    if (value && value !== '0') { return `${prefix}${value}${suffix}`; }
                    return '—';
                };
                const formatPrice = (value) => {
                    const num = parseFloat(value);
                    if (!isNaN(num) && num > 0) {
                        return '₱' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                    return '—';
                };
                const formatDate = (dateString) => {
                    if (!dateString) return '—';
                    try {
                        const date = new Date(dateString);
                        const utcDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
                        return utcDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    } catch (e) { return dateString; }
                };
                document.getElementById('view_full_name').textContent = data.fullname || '—';
                document.getElementById('view_email').textContent = data.email || '—';
                document.getElementById('view_contact_num').textContent = data.contact_num || '—';
                const statusEl = document.getElementById('view_status');
                statusEl.textContent = data.status || 'Pending';
                statusEl.className = 'badge';
                if (data.status === 'Approved') { statusEl.classList.add('bg-success'); }
                else if (data.status === 'Rejected') { statusEl.classList.add('bg-danger'); }
                else { statusEl.classList.add('bg-warning', 'text-dark'); }
                document.getElementById('view_unit_name').textContent = data.unit_name || '—';
                document.getElementById('view_unit_title').textContent = data.unit_title || '—';
                document.getElementById('view_unit_price').textContent = formatPrice(data.unit_price_raw);
                document.getElementById('view_downpayment').textContent = formatPrice(data.downpayment);
                document.getElementById('view_contract_years').textContent = formatDisplay(data.contract_years, '', ' year(s)');
                document.getElementById('view_contract_start').textContent = formatDate(data.contract_start);
                document.getElementById('view_remarks').textContent = data.remarks || 'No remarks provided.';
            });
        }

        // 3. MAIN PAGE Real-time Search (Unchanged)
        const searchInput = document.getElementById('realtimeSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (event) => {
                const query = event.target.value.toLowerCase();
                const rows = document.querySelectorAll('#applicationsTableBody tr[data-searchable-text]');
                const emptyRow = document.getElementById('empty-row');
                let visibleRows = 0;
                rows.forEach(row => {
                    const text = row.dataset.searchableText;
                    if (text.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                if (emptyRow) {
                    emptyRow.style.display = (visibleRows === 0) ? '' : 'none';
                }
            });
        }

        // 4. ARCHIVE MODAL Real-time Search (MODIFIED to trigger render)
        const searchArchivedInput = document.getElementById('searchArchivedApplicants');
        if (searchArchivedInput) {
            searchArchivedInput.addEventListener('input', () => { // Changed to 'input' for better response
                renderArchivedDisplay(1); // Re-render page 1 with filter
            });
        }

        // 5. Approve Form Handler (✅ MODIFIED with redirect-proof logic)
        document.querySelectorAll('.form-approve').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = this.action;
                sa_confirmAction('Do you want to approve this application?', 'Yes, approve', 'Cancel', async () => {
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });

                        // This handles redirects gracefully
                        if (res.ok) {
                            let successMsg = 'Application approved!';
                            try {
                                const result = await res.json();
                                successMsg = result.success || result.message || successMsg;
                            } catch (e) { /* Ignore JSON parse error on redirect */ }

                            sa_showSuccess(successMsg);
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            // Handle real JSON errors
                            let errorMsg = `Failed (${res.status})`;
                            try {
                                const result = await res.json();
                                errorMsg = result.error || result.message || errorMsg;
                            } catch(e) { /* Use default error msg */ }
                            throw new Error(errorMsg);
                        }
                    } catch (err) {
                        console.error(err);
                        sa_showError(err.message);
                    }
                });
            });
        });

        // 6. Reject Form Handler (✅ MODIFIED with redirect-proof logic)
        document.querySelectorAll('.form-reject').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = this.action;
                sa_confirmAction('Do you want to reject this application?', 'Yes, reject', 'Cancel', async () => {
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });

                        if (res.ok) {
                            let successMsg = 'Application rejected!';
                            try {
                                const result = await res.json();
                                successMsg = result.success || result.message || successMsg;
                            } catch (e) { /* Ignore JSON parse error on redirect */ }

                            sa_showSuccess(successMsg);
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            let errorMsg = `Failed (${res.status})`;
                            try {
                                const result = await res.json();
                                errorMsg = result.error || result.message || errorMsg;
                            } catch(e) { /* Use default error msg */ }
                            throw new Error(errorMsg);
                        }
                    } catch (err) {
                        console.error(err);
                        sa_showError(err.message);
                    }
                });
            });
        });

        // 7. 'Save' button listener (Unchanged)
        document.getElementById('saveChangesBtn').addEventListener('click', async () => {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const contractStartDate = `${year}-${month}-${day}`;
            const id = document.getElementById('application_id').value;
            const data = {
                first_name: document.getElementById('first_name').value,
                middle_name: document.getElementById('middle_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                contact_num: document.getElementById('contact_num').value,
                unit_id: document.getElementById('unit_select').value,
                downpayment: document.getElementById('downpayment').value,
                contract_years: document.getElementById('contract_years').value,
                contract_start: contractStartDate,
            };
            try {
                const response = await axios.put(`/api/applications/editApplications/${id}`, data);
                sa_showSuccess(response.data.message);
                setTimeout(() => { location.reload(); }, 2000);
            } catch (error) {
                console.error(error.response.data);
                let errorMessage = 'Failed to update application.';
                if (error.response.data && error.response.data.errors) {
                    errorMessage += '\n\nErrors:\n';
                    for (const key in error.response.data.errors) {
                        errorMessage += `- ${error.response.data.errors[key][0]}\n`;
                    }
                }
                sa_showError(errorMessage);
            }
        });

        // 8. Archived Modal script (REMOVED - logic is now in fetchArchivedApplicants)
        // The original `[data-bs-target="#archivedModal"]` listener is removed.

        // 9. Session Message Handlers (Unchanged)
        @if (session('success'))
            sa_showSuccess("{{ session('success') }}");
        @endif
        @if (session('message'))
            sa_showSuccess("{{ session('message') }}");
        @endif
        @if (session('error'))
            sa_showError("{{ session('error') }}");
        @endif

    }); // End of DOMContentLoaded


    // --- NEW: Function to fetch archived data (modified from your modal script) ---
    async function fetchArchivedApplicants() {
        const tbody = document.getElementById('archivedApplicationsBody');
        tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-muted">Loading archived applicants...</td></tr>`;
        const sa_showError = window.showError || function(message) { alert(message); };

        try {
            const res = await fetch('/api/applications/archived');
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            const data = await res.json();

            // Store data globally
            allArchivedApplicants = data;

            // Sort it (Pending > Approved > Rejected)
            const statusOrder = {'Pending': 1, 'Approved': 2, 'Rejected': 3};
            allArchivedApplicants.sort((a, b) => {
                const statusA = statusOrder[a.status] || 4;
                const statusB = statusOrder[b.status] || 4;
                return statusA - statusB;
            });

            renderArchivedDisplay(1); // Render the first page

        } catch (err) {
            console.error(err);
            sa_showError('Failed to load archived applications');
            tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-danger">Failed to load data.</td></tr>`;
        }
    }

    // --- NEW: Function to render archived data and pagination ---
    function renderArchivedDisplay(page = 1) {
        currentArchivedPage = page;
        const tbody = document.getElementById('archivedApplicationsBody');
        const paginationContainer = document.getElementById('archived-pagination-container');
        const sa_showError = window.showError || function(message) { alert(message); };

        try {
            // 1. Filter
            const query = document.getElementById('searchArchivedApplicants').value.toLowerCase();
            const filteredData = allArchivedApplicants.filter(app => {
                const fullName = `${app.first_name || ''} ${app.middle_name || ''} ${app.last_name || ''}`;
                const email = app.email || '';
                const unitName = app.unit?.unit_name || '';
                const searchableText = `${fullName} ${email} ${unitName} ${app.status || ''}`.toLowerCase();
                return searchableText.includes(query);
            });

            // 2. Paginate
            const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
            currentArchivedPage = Math.min(Math.max(1, page), totalPages || 1); // Clamp page number
            const start = (currentArchivedPage - 1) * ROWS_PER_PAGE;
            const end = start + ROWS_PER_PAGE;
            const pageData = filteredData.slice(start, end);

            // 3. Render
            tbody.innerHTML = ''; // Clear
            if (pageData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-muted">No archived applicants found.</td></tr>`;
            } else {
                pageData.forEach(app => {
                    const fullName = `${app.first_name || ''} ${app.middle_name || ''} ${app.last_name || ''}`.trim();
                    const email = app.email || 'N/A';
                    const unitName = app.unit?.unit_name || 'N/A';

                    let statusBadge = '';
                    if (app.status === 'Approved') statusBadge = '<span class="badge bg-success">Approved</span>';
                    else if (app.status === 'Rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
                    else statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${escapeHtml(fullName)}</td>
                        <td>${escapeHtml(email)}</td>
                        <td>${escapeHtml(app.contact_num || 'N/A')}</td>
                        <td>${escapeHtml(unitName)}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-success" onclick="restoreApplicant(${app.id})">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            // 4. Render Pagination
            paginationContainer.innerHTML = buildPaginationUI(totalPages, currentArchivedPage);
            paginationContainer.querySelectorAll(".page-link").forEach(link => {
                link.addEventListener("click", e => {
                    e.preventDefault();
                    const newPage = parseInt(e.target.dataset.page, 10);
                    if (newPage) renderArchivedDisplay(newPage);
                });
            });
        } catch (err) {
            console.error("Error during renderArchivedDisplay:", err);
            sa_showError("An error occurred while displaying archived data.");
            tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-danger">Error rendering data.</td></tr>`;
        }
    }
</script>

{{-- This second script block contains your unchanged functions --}}
<script>
    /**
     * Function to populate the Edit modal
     */
    function populateEditModal(application) {
        document.getElementById('application_id').value = application.id;
        document.getElementById('first_name').value = application.first_name;
        document.getElementById('middle_name').value = application.middle_name || '';
        document.getElementById('last_name').value = application.last_name;
        document.getElementById('email').value = application.email;
        document.getElementById('contact_num').value = application.contact_num;
        document.getElementById('downpayment').value = application.downpayment || '';
        document.getElementById('contract_years').value = application.contract_years || '';

        const phaseSelect = document.getElementById('phase_select');
        const unitSelect = document.getElementById('unit_select');

        phaseSelect.innerHTML = '<option value="">-- Loading Phases --</option>';
        unitSelect.innerHTML = '<option value="">-- Select Phase First --</option>';

        axios.get('/api/allUnits')
            .then(response => {
                allUnits = response.data;
                const allPhaseNames = allUnits.map(unit => unit.location);
                const uniquePhases = [...new Set(allPhaseNames)];

                phaseSelect.innerHTML = '<option value="">-- Select Phase --</option>';

                uniquePhases.forEach(phaseName => {
                    const option = document.createElement('option');
                    option.value = phaseName;
                    option.text = phaseName;
                    phaseSelect.appendChild(option);
                });

                const currentUnit = allUnits.find(u => u.id == application.unit_id);

                if (currentUnit) {
                    phaseSelect.value = currentUnit.location;
                    populateUnits(currentUnit.location, application.unit_id);
                } else {
                    populateUnits(null, null);
                }
            }).catch(err => {
                console.error('Failed to load units/phases:', err);
                phaseSelect.innerHTML = '<option value="">-- Error Loading Data --</option>';
            });
    }

    /**
     * Helper function to populate the units dropdown
     */
    function populateUnits(selectedPhaseName, selectedUnitId) {
        const unitSelect = document.getElementById('unit_select');
        const filteredUnits = selectedPhaseName
            ? allUnits.filter(u => u.location == selectedPhaseName)
            : [];
        unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.text = `${unit.title} (${unit.unit_code || 'N/A'})`;
            if (unit.id == selectedUnitId) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });
    }

    // Listener para kapag nagpalit ng Phase
    document.getElementById('phase_select').addEventListener('change', (event) => {
        populateUnits(event.target.value, null);
    });

    /**
     * Archive an application
     */
    async function archiveApplication(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        const sa_confirm = window.confirmAction || function(title, confirmText, cancelText, callback) {
            if (confirm(title)) callback();
        };
        const sa_success = window.showSuccess || function(message) { alert(message); };
        const sa_error = window.showError || function(message) { alert(message); };

        sa_confirm(
            'Do you want to archive this applicant?',
            'Yes, archive it',
            'Cancel',
            async () => {
                try {
                    const res = await fetch(`/admin/applications/${id}/archive`, {
                        method: 'POST',
                        headers: {
                    // Gamitin ang route mula sa form mo dati
                    // (NOTE: Siguraduhin na ito ay POST route sa web.php mo)
                    const res = await fetch(`admin/applications/${id}/archive/`, {
                        method: 'POST', // Ginawa kong POST para tumugma sa <form> mo dati at sa restore
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    // ✅ MODIFIED: Using the same redirect-proof logic
                    if (res.ok) {
                        let successMsg = 'Applicant archived successfully!';
                        try {
                            const result = await res.json();
                            successMsg = result.message || successMsg;
                        } catch(e) { /* Ignore JSON parse error on redirect */ }

                        sa_success(successMsg);
                        setTimeout(() => { location.reload(); }, 2000);
                    } else {
                        let errorMsg = `Failed (${res.status})`;
                        try {
                           const result = await res.json();
                           errorMsg = result.message || errorMsg;
                        } catch(e) { /* Use default error msg */ }
                        throw new Error(errorMsg);
                    }
                } catch (err) {
                    console.error(err);
                    sa_error('Error archiving applicant: ' + err.message);
                }
            }
        );
    }

    /**
     * Restore an application
     */
    async function restoreApplicant(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        const sa_confirmAction = window.confirmAction || function(title, confirmText, cancelText, callback) {
            if (confirm(title)) callback();
        };
        const sa_showSuccess = window.showSuccess || function(message) { alert(message); };
        const sa_showError = window.showError || function(message) { alert(message); };

        sa_confirmAction(
            'Do you want to restore this applicant?',
            'Yes, restore it',
            'Cancel',
            async () => {
                try {
                    const res = await fetch(`/api/applications/restore/${id}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    // ✅ MODIFIED: Using the same redirect-proof logic
                    if (res.ok) {
                         let successMsg = 'Applicant restored successfully!';
                         try {
                            const result = await res.json();
                            successMsg = result.message || successMsg;
                         } catch (e) { /* Ignore JSON parse error on redirect */ }

                        sa_showSuccess(successMsg);

                        // Hide the archived card and show the active one
                        document.getElementById('archived-view-content').style.display = 'none';
                        document.getElementById('active-view-content').style.display = '';
                        // Click the "Active" button to fix the toggle state
                        document.getElementById('btn-view-active').click();

                        // Reloading is still the best way to refresh the server-rendered active list
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        let errorMsg = `Failed (${res.status})`;
                        try {
                           const result = await res.json();
                           errorMsg = result.message || errorMsg;
                        } catch(e) { /* Use default error msg */ }
                        throw new Error(errorMsg);
                    }
                } catch (err) {
                    console.error(err);
                    sa_showError('Error restoring applicant: ' + err.message);
                }
            }
        );
    }
</script>
@endpush