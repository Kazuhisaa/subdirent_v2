@extends('tenant.dashboard')

@section('title', 'Maintenance Request')
@section('page-title', 'Maintenance Request')

@section('content')
<div class="container-fluid tenant-dashboard">

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

          <form>
            {{-- Category Selection --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Category</label>
              <select class="form-select border-primary-subtle">
                <option selected disabled>Select issue category</option>
                <option>Plumbing</option>
                <option>Electrical</option>
                <option>Appliance Repair</option>
                <option>Structural Damage</option>
                <option>Water Leakage</option>
                <option>Others</option>
              </select>
            </div>

            {{-- Urgency Level --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Urgency</label>
              <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="urgency" id="low" checked>
                  <label class="form-check-label" for="low">Low</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="urgency" id="medium">
                  <label class="form-check-label" for="medium">Medium</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="urgency" id="high">
                  <label class="form-check-label" for="high">High</label>
                </div>
              </div>
            </div>

            {{-- Description / Message --}}
            <div class="mb-3">
              <label class="form-label fw-semibold">Describe the Issue</label>
              <textarea class="form-control border-primary-subtle" rows="5" placeholder="Please describe the issue in detail..."></textarea>
            </div>

            {{-- Upload Photo (Optional) --}}
            <div class="mb-4">
              <label class="form-label fw-semibold">Attach Photo (Optional)</label>
              <input class="form-control border-primary-subtle" type="file" accept="image/*">
              <small class="text-muted">Attach a clear image of the problem if available.</small>
            </div>

            {{-- Submit Button --}}
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

          {{-- Example of Hardcoded Requests --}}
          <div class="border-start border-3 border-primary ps-3 mb-3">
            <h6 class="fw-semibold text-primary mb-1">Leaking Faucet</h6>
            <small class="text-muted d-block mb-1">Category: Plumbing</small>
            <small class="badge bg-warning text-dark mb-1">Pending</small>
            <p class="small text-muted mb-0">Reported: Oct 15, 2025</p>
          </div>

          <div class="border-start border-3 border-success ps-3 mb-3">
            <h6 class="fw-semibold text-success mb-1">Light Bulb Replacement</h6>
            <small class="text-muted d-block mb-1">Category: Electrical</small>
            <small class="badge bg-success mb-1">Completed</small>
            <p class="small text-muted mb-0">Reported: Sep 28, 2025</p>
          </div>

          <div class="border-start border-3 border-danger ps-3 mb-0">
            <h6 class="fw-semibold text-danger mb-1">Aircon Malfunction</h6>
            <small class="text-muted d-block mb-1">Category: Appliance Repair</small>
            <small class="badge bg-danger mb-1">In Progress</small>
            <p class="small text-muted mb-0">Reported: Oct 10, 2025</p>
          </div>
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
@endsection
