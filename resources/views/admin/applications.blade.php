@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold text-blue-900">Applications</h3>

            <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedModal">
                <i class="bi bi-archive-fill me-1"></i> Archived Applicants
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
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

    {{-- Applications List --}}
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

                        {{-- ✅ FIX 1: Sort by status (Pending -> Approved -> Rejected) --}}
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

                            {{-- Action Buttons --}}
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <button
                                        class="btn btn-sm btn-outline-info view-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewApplicationModal"
                                        title="View Details"
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
                                        data-remarks="{{ $application->remarks }}"
                                    >
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                    @if($application->status === 'Pending')
                                        <button
                                            class="btn btn-sm btn-outline-primary edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editApplicationModal"
                                            title="Edit"
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

                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Archive"
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

       @if ($applications->hasPages())
       <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3">
         {!! $applications->links() !!}
       </div>
       @endif

   </div>
</div>

{{--
    ===========================================
    MGA MODAL
    ===========================================
--}}
<div class="modal fade" id="editApplicationModal" tabindex="-1" aria-labelledby="editApplicationLabel" aria-hidden="true">
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

<div class="modal fade" id="archivedModal" tabindex="-1" aria-labelledby="archivedLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
     <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
       <div class="modal-header text-white border-0"
         style="background: linear-gradient(90deg, #007BFF, #0A2540);">
         <h5 class="modal-title fw-bold" id="archivedLabel">
             <i class="bi bi-archive me-2"></i> Archived Applicants
         </h5>
         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
       </div>
       <div class="modal-body bg-light p-0">

         <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
           <input type="text" id="searchArchivedApplicants" class="form-control form-control-sm w-50"
             placeholder="Search archived applicants...">
         </div>

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
           <tbody id="archivedApplicationsBody">
              <tr>
                 <td colspan="6" class="py-4 text-muted">Loading archived applicants...</td>
              </tr>
           </tbody>
           </table>
            </div>
       </div>
     </div>
   </div>
</div>


{{--
    ===========================================
    MGA SCRIPT
    ===========================================
--}}
<script>
   // Global variable para sa all units
   let allUnits = [];

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

      // 1. EDIT Modal Listener
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

      // 2. VIEW Modal Listener
      const viewModalEl = document.getElementById('viewApplicationModal');
      if(viewModalEl) {
       viewModalEl.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          if (!button || !button.classList.contains('view-btn')) return;

          const data = button.dataset;

          const formatDisplay = (value, prefix = '', suffix = '') => {
             if (value && value !== '0') {
                return `${prefix}${value}${suffix}`;
             }
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
             } catch (e) {
                return dateString; // Fallback
             }
          };

          document.getElementById('view_full_name').textContent = data.fullname || '—';
          document.getElementById('view_email').textContent = data.email || '—';
          document.getElementById('view_contact_num').textContent = data.contact_num || '—';

          const statusEl = document.getElementById('view_status');
          statusEl.textContent = data.status || 'Pending';
          statusEl.className = 'badge'; // Reset classes
          if (data.status === 'Approved') {
             statusEl.classList.add('bg-success');
          } else if (data.status === 'Rejected') {
             statusEl.classList.add('bg-danger');
          } else {
             statusEl.classList.add('bg-warning', 'text-dark');
             }

          document.getElementById('view_unit_name').textContent = data.unit_name || '—';
          document.getElementById('view_unit_title').textContent = data.unit_title || '—';
          document.getElementById('view_unit_price').textContent = formatPrice(data.unit_price_raw);
          document.getElementById('view_downpayment').textContent = formatPrice(data.downpayment);
          document.getElementById('view_contract_years').textContent = formatDisplay(data.contract_years, '', ' year(s)');
          document.getElementById('view_contract_start').textContent = formatDate(data.contract_start);
          document.getElementById('view_remarks').textContent = data.remarks || 'No remarks provided.';
       });
     }

      // 3. MAIN PAGE Real-time Search
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

      // 4. ARCHIVE MODAL Real-time Search
      const searchArchivedInput = document.getElementById('searchArchivedApplicants');
      if (searchArchivedInput) {
       searchArchivedInput.addEventListener('keyup', (event) => {
          const query = event.target.value.toLowerCase();
          const rows = document.querySelectorAll('#archivedApplicationsBody tr[data-searchable-text]');

          rows.forEach(row => {
             const text = row.dataset.searchableText;
             if (text.includes(query)) {
                row.style.display = '';
             } else {
                row.style.display = 'none';
             }
          });
       });
     }

      // ✅ FIX 2: Intercept Approve/Reject forms to use AJAX and handle JSON errors

      // --- Approve Form Handler ---
document.querySelectorAll('.form-approve').forEach(form => {
       form.addEventListener('submit', function(e) {
          e.preventDefault();
          const currentForm = this;
          const url = currentForm.action;

          sa_confirmAction('Do you want to approve this application?', 'Yes, approve', 'Cancel', async () => {
             try {
                const res = await fetch(url, {
                   method: 'POST',
                   headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': csrfToken
                   }
                });

                // ✅ START OF FIX
                // Check if the response is actually JSON before trying to parse it
                const contentType = res.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    // It's HTML, probably a redirect or error page
                    throw new Error("Session expired or server error. Please refresh the page.");
                }

                // Now it's safe to parse as JSON
                const result = await res.json();
                // ✅ END OF FIX

                if (!res.ok) {
                   // This will catch the {"error": "..."}
                   throw new Error(result.error || result.message || `Failed (${res.status})`);
              _message }

                // Success
                sa_showSuccess(result.success || result.message || 'Application approved!');
                setTimeout(() => location.reload(), 2000);

             } catch (err) {
                // Show error from the catch block
                console.error(err);
                sa_showError(err.message);
             }
          });
       });
     });
      // --- Reject Form Handler ---
      document.querySelectorAll('.form-reject').forEach(form => {
       form.addEventListener('submit', function(e) {
          e.preventDefault();
          const currentForm = this;

          const sa_confirmAction = window.confirmAction || function(title, confirmText, cancelText, callback) {
             if (confirm(title)) callback();
          };

          sa_confirmAction('Do you want to reject this application?', 'Yes, reject', 'Cancel', () => {
             currentForm.submit();
          });
       });
     });
      // 5. 'Save' button listener (for Edit Modal)
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

         setTimeout(() => {
            location.reload();
         }, 2000);

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

      // 6. Archived Modal script
      document.querySelector('[data-bs-target="#archivedModal"]').addEventListener('click', async () => {
      const loadingRow = `<tr><td colspan="6" class="py-4 text-muted">Loading archived applicants...</td></tr>`;
      const tbody = document.getElementById('archivedApplicationsBody');
      tbody.innerHTML = loadingRow;

      try {
         const res = await fetch('/api/applications/archived');
         const data = await res.json();

         tbody.innerHTML = ''; // Clear loading

         if (data.length === 0) {
             tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-muted">No archived applicants found.</td></tr>`;
             return;
         }

         data.forEach(app => {
            const tr = document.createElement('tr');

            const fullName = `${app.first_name || ''} ${app.middle_name || ''} ${app.last_name || ''}`;
            const email = app.email || '';
            const unitName = app.unit?.unit_name || '';
            tr.dataset.searchableText = `${fullName} ${email} ${unitName}`.toLowerCase();

            tr.innerHTML = `
               <td>${fullName.trim()}</td>
               <td>${email}</td>
               <td>${app.contact_num || 'N/A'}</td>
               <td>${unitName}</td>
               <td>${app.status || 'N/A'}</td>
               <td>
                  <button class="btn btn-sm btn-outline-success" onclick="restoreApplicant(${app.id})">
                     <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                  </button>
               </td>
            `;
            tbody.appendChild(tr);
         });

      } catch (err) {
         console.error(err);
         sa_showError('Failed to load archived applications');
         tbody.innerHTML = `<tr><td colspan="6" class="py-4 text-danger">Failed to load data.</td></tr>`;
      }
   });

      // 7. Session Message Handlers
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
</script>

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

   // Get CSRF token
   const csrfToken = document.querySelector('meta[name="csrf-token"]')
              ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              : '';

   // SweetAlert fallbacks
   const sa_confirm = window.confirmAction || function(title, confirmText, cancelText, callback) {
     if (confirm(title)) {
        callback();
     }
   };
   const sa_success = window.showSuccess || function(message) {
     alert(message);
   };
   const sa_error = window.showError || function(message) {
     alert(message);
   };

   // Confirmation
   sa_confirm(
     'Do you want to archive this applicant?', // Title
     'Yes, archive it', // Confirm text
     'Cancel', // Cancel text
     async () => { // Callback
        try {
           const res = await fetch(`/admin/applications/${id}/archive`, {
              method: 'POST',
              headers: {
                 'Accept': 'application/json',
                 'X-CSRF-TOKEN': csrfToken
              }
           });

           const result = await res.json();
           if (!res.ok) {
              throw new Error(result.message || `Failed (${res.status})`);
           }

           sa_success('Applicant archived successfully!');

           setTimeout(() => {
              location.reload();
           }, 2000);

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

   // Get CSRF token from meta tag
   const csrfToken = document.querySelector('meta[name="csrf-token"]')
              ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              : '';

   // SweetAlert fallbacks
   const sa_confirmAction = window.confirmAction || function(title, confirmText, cancelText, callback) {
     if (confirm(title)) {
        callback();
     }
   };
   const sa_showSuccess = window.showSuccess || function(message) {
     alert(message);
   };
   const sa_showError = window.showError || function(message) {
        alert(message);
   };

   // Use the SweetAlert-style confirmation
   sa_confirmAction(
     'Do you want to restore this applicant?', // Title
     'Yes, restore it', // Confirm text
     'Cancel', // Cancel text
     async () => { // This is the callback function
        try {
           const res = await fetch(`/api/applications/restore/${id}`, {
              method: 'POST',
              headers: {
                 'Accept': 'application/json',
                     'X-CSRF-TOKEN': csrfToken
              }
           });

           const result = await res.json();
           if (!res.ok) {
              throw new Error(result.message || `Failed (${res.status})`);
           }

           sa_showSuccess('Applicant restored successfully!');

           // Hide the modal
           const modalEl = document.getElementById('archivedModal');
           if (modalEl) {
              const modalInstance = bootstrap.Modal.getInstance(modalEl);
              if (modalInstance) {
                  modalInstance.hide();
              }
           }

           setTimeout(() => {
              location.reload();
           }, 2000);

        } catch (err) {
           console.error(err);
           sa_showError('Error restoring applicant: ' + err.message);
        }
     }
   );
 }
</script>
@endsection