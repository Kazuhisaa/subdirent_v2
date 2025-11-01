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

                                    <button 
                                        class="btn btn-sm btn-info view-btn"
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


<script>
    // Global variable para sa all units
    let allUnits = [];

    // 1. Makikinig tayo sa 'show.bs.modal' event para sa EDIT
    document.addEventListener('DOMContentLoaded', function() {
        const editModalEl = document.getElementById('editApplicationModal');
        if(editModalEl) {
            editModalEl.addEventListener('show.bs.modal', function(event) {
                // Kunin ang button na pinindot
                const button = event.relatedTarget;
                if (!button || !button.classList.contains('edit-btn')) return; 

                // 2. Kunin lahat ng data mula sa data-* attributes ng button
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

                // 3. Ipasa ang data sa function na magpupuno ng form
                populateEditModal(applicationData);
            });
        }


        // ===========================================
        // 3. ITO YUNG BAGONG SCRIPT PARA SA VIEW MODAL
        // ===========================================
        const viewModalEl = document.getElementById('viewApplicationModal');
        if(viewModalEl) {
            viewModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button || !button.classList.contains('view-btn')) return;

                // Kunin ang data mula sa view button
                const data = button.dataset;

                // Helper function para sa formatting (para hindi laging '—')
                const formatDisplay = (value, prefix = '', suffix = '') => {
                    if (value && value !== '0') {
                        return `${prefix}${value}${suffix}`;
                    }
                    return '—';
                };
                
                // Format price
                const formatPrice = (value) => {
                    const num = parseFloat(value);
                    if (!isNaN(num) && num > 0) {
                        return '₱' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                    return '—';
                };

                // Format Date
                const formatDate = (dateString) => {
                    if (!dateString) return '—';
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    } catch (e) {
                        return '—';
                    }
                };

                // Populate applicant details
                document.getElementById('view_full_name').textContent = data.fullname || '—';
                document.getElementById('view_email').textContent = data.email || '—';
                document.getElementById('view_contact_num').textContent = data.contact_num || '—';
                
                // Populate status badge
                const statusEl = document.getElementById('view_status');
                statusEl.textContent = data.status || 'Pending';
                statusEl.className = 'badge'; // Reset classes
                if (data.status === 'Approved') {
                    statusEl.classList.add('bg-success');
                } else if (data.status === 'Rejected') {
                    statusEl.classList.add('bg-danger');
                } else {
                    statusEl.classList.add('bg-secondary');
                }

                // Populate application details
                document.getElementById('view_unit_name').textContent = data.unit_name || '—';
                document.getElementById('view_unit_title').textContent = data.unit_title || '—';
                document.getElementById('view_unit_price').textContent = formatPrice(data.unit_price_raw);
                document.getElementById('view_downpayment').textContent = formatPrice(data.downpayment);
                document.getElementById('view_contract_years').textContent = formatDisplay(data.contract_years, '', ' year(s)');
                document.getElementById('view_contract_start').textContent = formatDate(data.contract_start);
                document.getElementById('view_remarks').textContent = data.remarks || 'No remarks provided.';
            });
        }
    });

    /**
     * Ito 'yung function na magpupuno ng modal
     */
    function populateEditModal(application) {
        // Ilagay ang basic info
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

        // Reset muna ang dropdowns
        phaseSelect.innerHTML = '<option value="">-- Loading Phases --</option>';
        unitSelect.innerHTML = '<option value="">-- Select Phase First --</option>';

        // 4. FIX: Kunin ang data mula sa /api/allUnits
        axios.get('/api/allUnits')
            .then(response => {
                allUnits = response.data; // I-save sa global variable

                // 5. FIX: Gumawa ng listahan ng "Phases" mula sa "location"
                const allPhaseNames = allUnits.map(unit => unit.location); // Kunin lahat ng 'location'
                const uniquePhases = [...new Set(allPhaseNames)]; // Kunin lang 'yung unique

                phaseSelect.innerHTML = '<option value="">-- Select Phase --</option>';
                
                // I-populate ang phase dropdown
                uniquePhases.forEach(phaseName => {
                    const option = document.createElement('option');
                    option.value = phaseName; // Ang value ay 'yung mismong pangalan (e.g., "Phase 1")
                    option.text = phaseName;
                    phaseSelect.appendChild(option);
                });

                // Ngayong may data na, hanapin ang current unit
                const currentUnit = allUnits.find(u => u.id == application.unit_id);
                
                if (currentUnit) {
                    // 6. FIX: I-set ang Phase dropdown sa tamang "location"
                    phaseSelect.value = currentUnit.location;
                    
                    // 7. FIX: I-populate ang Units dropdown batay sa "location" (phase name)
                    populateUnits(currentUnit.location, application.unit_id);
                } else {
                    // Walang unit na naka-assign
                    populateUnits(null, null);
                }

            }).catch(err => {
                console.error('Failed to load units/phases:', err);
                phaseSelect.innerHTML = '<option value="">-- Error Loading Data --</option>';
            });
    }

    /**
     * Helper function para i-populate ang units dropdown
     * @param {string} selectedPhaseName - Ang pangalan ng phase (e.g., "Phase 1")
     * @param {string|int} selectedUnitId - Ang ID ng unit na naka-select
     */
    function populateUnits(selectedPhaseName, selectedUnitId) {
        const unitSelect = document.getElementById('unit_select');
        
        // 8. FIX: Salain ang units base sa "location"
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
        // I-populate ulit ang units gamit ang bagong phase NAME
        populateUnits(event.target.value, null);
    });


    // 9. 'Save' button listener
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
            // FIX: Ito 'yung tamang URL base sa api.php mo
            const response = await axios.put(`/api/applications/editApplications/${id}`, data);
            
            alert(response.data.message);
            location.reload();
        } catch (error) {
            console.error(error.response.data);
            
            let errorMessage = 'Failed to update application.';
            if (error.response.data && error.response.data.errors) {
                errorMessage += '\n\nErrors:\n';
                for (const key in error.response.data.errors) {
                    errorMessage += `- ${error.response.data.errors[key][0]}\n`;
                }
            }
            alert(errorMessage);
        }
    });

</script>
@endsection