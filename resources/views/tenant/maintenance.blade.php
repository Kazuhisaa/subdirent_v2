@extends('tenant.dashboard')

@section('title', 'Maintenance Request')
@section('page-title', 'Maintenance Request')

@section('content')
<div class="container-fluid tenant-dashboard">

  {{-- Header --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
      <div class="d-flex align-items-center mb-2 mb-md-0">
        <div class="me-3">
          <img src="{{ asset('images/maintenance-icon.png') }}" alt="Maintenance" class="rounded" width="70" height="70">
        </div>
        <div>
          <h5 class="fw-bold mb-1 text-primary">Maintenance Request Center</h5>
          <p class="text-muted mb-0">Submit and track your property maintenance issues easily.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">

    {{-- LEFT SIDE: Request Form --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h6 class="fw-bold text-secondary mb-3">Submit a Request</h6>

          <form action="{{ route('tenant.maintenance.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- 1. Urgency (New Position) --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Urgency</label>
                <div class="d-flex gap-3 flex-wrap">
                    @foreach (['Low', 'Medium', 'High', 'Others'] as $level) 
                    <div class="form-check">
                        {{-- Added 'urgency-radio' class for JS targeting --}}
                        <input class="form-check-input urgency-radio" type="radio" name="urgency" value="{{ $level }}" id="{{ strtolower($level) }}" {{ $loop->first ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ strtolower($level) }}">{{ $level }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- 2. Category (Now dependent and has an ID for JS targeting) --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Category</label>
              <select class="form-select border-primary-subtle" name="category" id="category-select" required>
                {{-- Options populated by JavaScript --}}
                <option disabled selected value="">Select issue category</option>
              </select>
            </div>

            {{-- 3. Description (Now Optional, but required for 'Others') --}}
            <div class="mb-3">
              <label class="form-label fw-semibold" id="description-label">Describe the Issue (Optional)</label>
              <textarea class="form-control border-primary-subtle" name="description" id="description-textarea" rows="5" placeholder="Please describe the issue in detail..."></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Attach Photo (Optional)</label>
              <input class="form-control border-primary-subtle" type="file" name="photo" accept="image/*">
              <small class="text-muted">Attach a clear image of the problem if available.</small>
            </div>

            <button type="submit" class="btn btn-tenant px-4">
              <i class="bi bi-send-fill me-1"></i> Submit Request
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- RIGHT SIDE: Request History --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h6 class="fw-bold text-secondary mb-3">Recent Requests</h6>

          @forelse ($recentRequests as $req)
            <div class="border-start border-3 ps-3 mb-3 
                @if($req->status == 'Pending') border-warning 
                @elseif($req->status == 'In Progress') border-danger 
                @else border-success @endif">

              <h6 class="fw-semibold text-capitalize mb-1 text-{{ $req->status == 'Completed' ? 'success' : ($req->status == 'Pending' ? 'primary' : 'danger') }}">
                {{ $req->description }}
              </h6>
              <small class="text-muted d-block mb-1">Category: {{ $req->category }}</small>
              <small class="badge 
                {{ $req->status == 'Completed' ? 'bg-success' : ($req->status == 'Pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                {{ $req->status }}
              </small>
              <p class="small text-muted mb-0">Reported: {{ $req->created_at->format('M d, Y') }}</p>
            </div>
          @empty
            <p class="text-muted">No maintenance requests yet.</p>
          @endforelse
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-tools text-primary fs-1 mb-3"></i>
          <h6 class="fw-bold text-secondary mb-1">Need Immediate Assistance?</h6>
          <p class="text-muted small mb-3">For urgent repairs, please contact our maintenance hotline below.</p>
          <p class="fw-semibold text-primary mb-0"><i class="bi bi-telephone me-2"></i>(02) 888-5555</p>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urgencyRadios = document.querySelectorAll('.urgency-radio');
        const categorySelect = document.getElementById('category-select');
        const descriptionTextarea = document.getElementById('description-textarea');
        const descriptionLabel = document.getElementById('description-label');

        // Define categories based on urgency
        const categories = {
            'Low': ['Appliance is Destroyed', 'General Wear and Tear', 'Minor Paint Issue', 'Non-essential Plumbing', 'Other Minor Repair'],
            'Medium': ['Water Heater Malfunction', 'Minor Electrical Issues (e.g., specific outlet failure)', 'Pest Control (Non-emergency)', 'Broken Window Pane', 'Leaky Faucet/Toilet'],
            'High': ['Major Water Leakage (Flooding)', 'Total Loss of Power/HVAC', 'Gas Leak/Fumes', 'Structural Damage Threat', 'Security Issue (e.g., broken main door lock)'],
            'Others': [], // Empty for 'Others'
        };

        function updateFormFields(urgency) {
            // 1. Reset/Clear Category Dropdown
            categorySelect.innerHTML = '';
            
            if (urgency === 'Others') {
                // Scenario: Urgency is 'Others'
                categorySelect.innerHTML = '<option selected value="">N/A - See Description Below</option>';
                categorySelect.disabled = true; // Disable dropdown
                categorySelect.removeAttribute('required');
                
                // Description is mandatory
                descriptionTextarea.required = true; 
                descriptionLabel.textContent = 'Describe the Issue (Required)';
                descriptionTextarea.placeholder = 'Please describe the "Others" issue in detail...';
            } else {
                // Scenario: Urgency is Low, Medium, or High
                const options = categories[urgency] || [];
                categorySelect.innerHTML = '<option disabled selected value="">Select issue category</option>';
                options.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat;
                    option.textContent = cat;
                    categorySelect.appendChild(option);
                });
                categorySelect.disabled = false; // Enable dropdown
                categorySelect.required = true; // Category is mandatory
                
                // Description is optional
                descriptionTextarea.required = false; 
                descriptionLabel.textContent = 'Describe the Issue (Optional)';
                descriptionTextarea.placeholder = 'Please describe the issue in detail...';
            }
        }

        // Attach event listener to all radio buttons
        urgencyRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateFormFields(this.value);
            });
        });

        // Initialize form fields on page load based on the checked radio button (default is 'Low')
        const initialChecked = document.querySelector('.urgency-radio:checked');
        if (initialChecked) {
            updateFormFields(initialChecked.value);
        }
    });
</script>
@endsection