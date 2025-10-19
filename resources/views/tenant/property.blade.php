@extends('tenant.dashboard')

@section('title', 'Tenant Property')
@section('page-title', 'My Property')

@section('content')
<div class="container-fluid tenant-dashboard">

  @if($tenant && $tenant->unit)
    <!-- Property Header -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <div class="me-3">
            <img src="{{ asset('images/property-default.jpg') }}" alt="Property" class="rounded" width="80" height="80">
          </div>
          <div>
            <h5 class="fw-bold mb-1 text-primary">{{ $tenant->unit->title }}</h5>
            <p class="text-muted mb-0">{{ $tenant->unit->location }}</p>
            <small class="text-muted">Unit Code: {{ $tenant->unit->unit_code }}</small>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- LEFT COLUMN: Property Overview -->
      <div class="col-lg-8">
        <!-- Property Overview -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3 text-secondary">Property Overview</h6>
            <div class="row mb-3 text-center">
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->unit->floor_area ?? 'N/A' }} m²</h4>
                <small class="text-muted">Floor Area</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->unit->bedroom ?? 'N/A' }}</h4>
                <small class="text-muted">Bedrooms</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->unit->bathroom ?? 'N/A' }}</h4>
                <small class="text-muted">Bathrooms</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">₱{{ $tenant->unit->monthly_rent }}</h4>
                <small class="text-muted">Monthly Rent</small>
              </div>
            </div>

            <h6 class="fw-bold text-secondary mb-2">Description</h6>
            <p class="text-muted">{{ $tenant->unit->description }}</p>

            <h6 class="fw-bold text-secondary mt-4 mb-2">Contract Details</h6>
            <ul class="list-unstyled text-muted mb-0">
              <li><i class="bi bi-calendar-check me-2 text-primary"></i> Contract Duration: {{ $tenant->unit->contract_years }} year(s)</li>
              <li><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Location: {{ $tenant->unit->location }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: Tenant Info + Documents -->
      <div class="col-lg-4 d-flex flex-column">
        <!-- Tenant Info -->
        <div class="card border-0 shadow-sm mb-4 flex-grow-0">
          <div class="card-body text-center">
            <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle mb-3" width="90" height="90" alt="Tenant">
            <h6 class="fw-bold text-primary mb-1">{{ $tenant->first_name }} {{ $tenant->last_name }}</h6>
            <small class="text-muted d-block mb-2">{{ $tenant->email }}</small>
            <p class="text-muted mb-1"><i class="bi bi-telephone text-danger me-1"></i>{{ $tenant->contact_num }}</p>
            <span class="badge bg-success">Active Tenant</span>
          </div>
        </div>

        <!-- Documents (Now Below Tenant Info) -->
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-bold text-secondary mb-3">Documents</h6>
            <div class="d-flex flex-wrap gap-3">
              <a href="#" class="btn btn-outline-tenant btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Contract PDF
              </a>
              <a href="#" class="btn btn-outline-tenant btn-sm">
                <i class="bi bi-receipt me-1"></i> Receipts
              </a>
              <a href="#" class="btn btn-outline-tenant btn-sm">
                <i class="bi bi-clock-history me-1"></i> Transaction History
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

  @else
    <div class="alert alert-warning">
      <i class="bi bi-info-circle me-2"></i> You have no assigned property yet.
    </div>
  @endif

</div>
@endsection
