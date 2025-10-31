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
                    <h6 class="card-title">Total Applications</h6>
                    <h3 class="fw-bold">{{ $applications->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card approved">
                <div class="card-body">
                    <h6 class="card-title">Approved Applications</h6>
                    <h3 class="fw-bold">{{ $applications->where('status', 'Approved')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm booking-card rejected">
                <div class="card-body">
                    <h6 class="card-title">Rejected Applications</h6>
                    <h3 class="fw-bold">{{ $applications->where('status', 'Rejected')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Applications List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            APPLICATIONS LIST
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
                    <tbody>
                        @forelse($applications as $application)
                        <tr>
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
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>

                            {{-- Action Buttons --}}
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- Hide edit button if approved/rejected --}}
                                    @if($application->status === 'Pending')
                                        <button 
                                            class="btn btn-sm btn-primary edit-btn"
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
                                            data-unit_price="{{ $application->unit_price }}"
                                            data-downpayment="{{ $application->downpayment }}"
                                            data-payment_due_date="{{ $application->payment_due_date }}"
                                            data-contract_years="{{ $application->contract_years }}"
                                            data-contract_duration="{{ $application->contract_duration }}"
                                            data-contract_start="{{ $application->contract_start }}"
                                            data-remarks="{{ $application->remarks }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Approve --}}
                                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" class="mb-0">@csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        {{-- Reject --}}
                                        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" class="mb-0">@csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Archive (Always visible) --}}
                                    <form action="{{ route('admin.applications.archive', $application->id) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                                            <i class="bi bi-archive-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-muted py-3">No applications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Edit Application Modal -->
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
              <input type="number" id="downpayment" class="form-control" min="0" step="0.01" required>
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


<script>
// Fetch all phases and units initially
let allUnits = [];

// Open Edit Modal
function openEditModal(application) {
    document.getElementById('application_id').value = application.id;
    document.getElementById('first_name').value = application.first_name;
    document.getElementById('middle_name').value = application.middle_name || '';
    document.getElementById('last_name').value = application.last_name;
    document.getElementById('email').value = application.email;
    document.getElementById('contact_num').value = application.contact_num;
    document.getElementById('downpayment').value = application.downpayment || '';
    document.getElementById('contract_years').value = application.contract_years || '';

    // Load phases (once)
    axios.get('/api/phases').then(response => {
        const phaseSelect = document.getElementById('phase_select');
        phaseSelect.innerHTML = '<option value="">-- Select Phase --</option>';
        response.data.forEach(phase => {
            const option = document.createElement('option');
            option.value = phase.id;
            option.text = phase.name;
            phaseSelect.appendChild(option);
        });
    });

    // Load units (for selected phase)
    axios.get('/api/units').then(response => {
        allUnits = response.data;
        populateUnits(application.unit_id);
    });

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editApplicationModal'));
    modal.show();
}

// Populate unit dropdown based on phase
function populateUnits(selectedUnitId) {
    const unitSelect = document.getElementById('unit_select');
    const phaseId = document.getElementById('phase_select').value;

    const filteredUnits = allUnits.filter(u => u.phase_id == phaseId);
    unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';

    filteredUnits.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit.id;
        option.text = `${unit.title} (${unit.location})`;
        if (unit.id == selectedUnitId) option.selected = true;
        unitSelect.appendChild(option);
    });
}

// When phase changes, refresh unit options
document.getElementById('phase_select').addEventListener('change', () => {
    populateUnits();
});

// Save changes
document.getElementById('saveChangesBtn').addEventListener('click', async () => {
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
    };

    try {
        const response = await axios.put(`/api/applications/editApplications/${id}`, data);
        alert(response.data.message);
        location.reload();
    } catch (error) {
        console.error(error.response.data);
        alert('Failed to update application.');
    }
});

</script>
@endsection
