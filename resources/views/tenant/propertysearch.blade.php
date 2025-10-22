@extends('tenant.dashboard')

@section('title', 'Property Search')
@section('page-title', 'Available Properties')

@section('content')
<div class="container-fluid tenant-dashboard">

  <!-- Header -->
  <div class="tenant-header d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-1 text-dark">Good Morning, {{ auth()->user()->name }}</h4>
      <p class="text-muted small mb-0">
        <i class="bi bi-geo-alt"></i>
        @if(auth()->user()->tenant && auth()->user()->tenant->unit)
          {{ auth()->user()->tenant->unit->location ?? 'Philippines' }}
        @else
          Philippines
        @endif
      </p>
    </div>

    <div class="tenant-searchbar d-flex align-items-center gap-2">
      <input 
        type="text" 
        id="unitSearch" 
        class="form-control tenant-search-input" 
        placeholder="Search properties..."
      >
      <img 
        src="{{ auth()->user()->profile_photo_url ?? '/images/avatar-default.png' }}" 
        alt="Avatar" 
        class="tenant-avatar"
      >
    </div>
  </div>

  <!-- Available Units -->
  <div class="row g-4" id="unitsGrid">
    <!-- showUnit.js dynamically populates property cards here -->
  </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold" id="applyModalLabel">Apply for this Property</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <img id="applyUnitImage" src="/images/placeholder.jpg" alt="Unit Image" class="img-fluid rounded-4 shadow-sm">
          </div>
          <div class="col-md-6">
            <p id="applyUnitInfo" class="fw-semibold text-dark mb-3"></p>
            <form id="applyForm">
              <div class="mb-3">
                <label for="tenantMessage" class="form-label fw-semibold">Message to Landlord</label>
                <textarea id="tenantMessage" class="form-control rounded-3 shadow-sm" rows="3" required></textarea>
              </div>
              <input type="hidden" id="applyUnitId">
              <button type="submit" class="btn btn-primary w-100 rounded-pill">Submit Application</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<!-- Scripts -->
@vite(['public/fetch_js/showUnit.js'])
<script>
  window.apiToken = "{{ auth()->user()->createToken('tenant-token')->plainTextToken ?? '' }}";
  window.userRole = "{{ auth()->user()->role ?? 'tenant' }}";
</script>
