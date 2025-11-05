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

            <div class="mb-3">
              <label class="form-label fw-semibold">Category</label>
              <select class="form-select border-primary-subtle" name="category" required>
                <option disabled selected>Select issue category</option>
                <option>Plumbing</option>
                <option>Electrical</option>
                <option>Appliance Repair</option>
                <option>Structural Damage</option>
                <option>Water Leakage</option>
                <option>Others</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Urgency</label>
              <div class="d-flex gap-3 flex-wrap">
                @foreach (['Low', 'Medium', 'High'] as $level)
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="urgency" value="{{ $level }}" id="{{ strtolower($level) }}" {{ $loop->first ? 'checked' : '' }}>
                  <label class="form-check-label" for="{{ strtolower($level) }}">{{ $level }}</label>
                </div>
                @endforeach
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Describe the Issue</label>
              <textarea class="form-control border-primary-subtle" name="description" rows="5" required placeholder="Please describe the issue in detail..."></textarea>
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
@endsection
