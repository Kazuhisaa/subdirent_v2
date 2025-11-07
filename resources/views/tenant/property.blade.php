@extends('tenant.dashboard')

@section('title', 'Tenant Property')
@section('page-title', 'My Property')

@section('content')
<div class="container-fluid tenant-dashboard">

  @if($tenant->tenant && $tenant->tenant->unit)

    @php
        // 1. Set the default image path
        $propertyImage = asset('uploads/default.jpg');

        // 2. Check if the unit has files and the array is not empty
        //    ($tenant->tenant->unit->files is now a PHP array because of your model cast)
        if (!empty($tenant->tenant->unit->files) && is_array($tenant->tenant->unit->files)) {
            
            // 3. Get the first file and set it as the image
            //    We use asset() to create the correct public URL
            $propertyImage = asset($tenant->tenant->unit->files[0]);
        }
    @endphp

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <div class="me-3">
            <img src="{{ $propertyImage }}" 
                 alt="{{ $tenant->tenant->unit->title }}" 
                 class="rounded" 
                 width="160" 
                 height="160" 
                 style="object-fit: cover;">
          </div>
          <div>
            <h5 class="fw-bold mb-1 text-primary">{{ $tenant->tenant->unit->title }}</h5>
            <p class="text-muted mb-0">{{ $tenant->tenant->unit->location }}</p>
            <small class="text-muted">Unit Code: {{ $tenant->tenant->unit->unit_code }}</small>
          </div>
        </div>
      </div>
    </div>

    

    <div class="row">

      {{-- RIGHT SIDE --}}
      <div class="col-lg-4 d-flex flex-column">
        <div class="card border-0 shadow-sm mb-4 flex-grow-0">
          <div class="card-body text-center">
            <img src="{{ $tenant->profile_photo_url }}"  class="rounded-circle mb-3" width="90" height="90" alt="Tenant">
            <h6 class="fw-bold text-primary mb-1">{{ $tenant->tenant->first_name }} {{ $tenant->tenant->last_name }}</h6>
            <small class="text-muted d-block mb-2">{{ $tenant->tenant->email }}</small>
            <p class="text-muted mb-1"><i class="bi bi-telephone text-danger me-1"></i>{{ $tenant->tenant->contact_num }}</p>
            <span class="badge bg-success">Active Tenant</span>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <h6 class="fw-bold text-secondary mb-3">Documents</h6>

    <div class="d-flex flex-wrap gap-3">

      @if ($contract->contract_pdf)
        <a href="{{ asset('storage/' . $contract->contract_pdf) }}" 
           target="_blank" 
           class="btn btn-outline-tenant btn-sm d-flex align-items-center">
          <i class="bi bi-file-earmark-text me-1"></i> View Contract
        </a>
      @else
        <button class="btn btn-outline-danger btn-sm d-flex align-items-center" disabled>
          <i class="bi bi-x-circle me-1"></i> No Contract
        </button>
      @endif

      <a href="#" class="btn btn-outline-tenant btn-sm d-flex align-items-center">
        <i class="bi bi-receipt me-1"></i> Receipts
      </a>
      
    </div>
  </div>
</div>

      </div>

      {{-- LEFT SIDE --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3 text-secondary">Property Overview</h6>
            <div class="row mb-3 text-center">
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->tenant->unit->floor_area ?? 'N/A' }} m²</h4>
                <small class="text-muted">Floor Area</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->tenant->unit->bedroom ?? 'N/A' }}</h4>
                <small class="text-muted">Bedrooms</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">{{ $tenant->tenant->unit->bathroom ?? 'N/A' }}</h4>
                <small class="text-muted">Bathrooms</small>
              </div>
              <div class="col-md-3">
                <h4 class="text-primary">₱{{ $tenant->tenant->unit->unit_price }}</h4>
                <small class="text-muted">Unit Price</small>
              </div>
            </div>

            <h6 class="fw-bold text-secondary mb-2">Description</h6>
            <p class="text-muted">{{ $tenant->tenant->unit->description }}</p>

            <h6 class="fw-bold text-secondary mt-4 mb-2">Contract Details</h6>
            <ul class="list-unstyled text-muted mb-4">
              <li><i class="bi bi-calendar-check me-2 text-primary"></i> Contract Duration: {{ $tenant->tenant->unit->contract_years }} year(s)</li>
              <li><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Location: {{ $tenant->tenant->unit->location }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    {{-- COMBINED AMENITIES SECTION BELOW BOTH COLUMNS --}}
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-body">
        <h6 class="fw-bold text-secondary mb-3">Amenities</h6>
        <div class="row text-center">
          <div class="col-md-2 mb-3">
            <i class="bi bi-water text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">Swimming Pool</p>
          </div>
          <div class="col-md-2 mb-3">
            <i class="bi bi-tools text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">Free Maintenance</p>
          </div>
          <div class="col-md-2 mb-3">
            <i class="bi bi-bicycle text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">Gym Access</p>
          </div>
          <div class="col-md-2 mb-3">
            <i class="bi bi-car-front text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">Parking Space</p>
          </div>
          <div class="col-md-2 mb-3">
            <i class="bi bi-shield-lock text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">24/7 Security</p>
          </div>
          <div class="col-md-2 mb-3">
            <i class="bi bi-building text-primary fs-3"></i>
            <p class="mt-1 text-muted mb-0">Function Hall</p>
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

{{-- UNIT PRICE PREDICTION --}}
<div class="card border-0 shadow-sm mt-4">
  <div class="card-body">
    <h6 class="fw-bold text-secondary mb-3">Unit Price Prediction</h6>

    <canvas id="unitPriceChart" width="200" height="50"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    const predictions = @json($predictions ?? []);

    if (predictions.length > 0) {
        const labels = predictions.map(p => p.year);
        const data = predictions.map(p => p.predicted_price);

        const ctx = document.getElementById('unitPriceChart').getContext('2d');
        const unitPriceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Predicted Unit Price (₱)',
                    data: data,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    } else {
        document.getElementById('unitPriceChart').insertAdjacentHTML('afterend', '<p class="text-muted mt-2">No prediction data available.</p>');
    }
</script>

@endsection
