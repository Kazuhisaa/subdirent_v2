@extends('tenant.dashboard')

@section('title', 'Tenant Property')
@section('page-title', 'My Property')

@section('content')
<div class="container-fluid tenant-dashboard">

  @if($tenant->tenant && $tenant->tenant->unit)

    @php
        // --- Logic to handle multiple image files (JSON array from DB) ---
        $unitFiles = $tenant->tenant->unit->files ?? [];

        // Check if files is a string (JSON) or an array
        if (is_string($unitFiles)) {
             $unitImages = array_filter(json_decode($unitFiles, true) ?? [], 'is_string');
        } else {
             $unitImages = is_array($unitFiles) ? array_filter($unitFiles, 'is_string') : [];
        }

        // Use a default image if no files are found
        if (empty($unitImages)) {
            $unitImages = ['uploads/default.jpg'];
        }
    @endphp

    {{-- START: Property Header Card with Image Carousel --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        
        <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
          
          {{-- Image Carousel Container (Fixed Width: 380px) --}}
          <div class="me-3 flex-shrink-0" style="width: 380px; margin-right: -1rem;"> 
            <div id="unitCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
              <div class="carousel-inner rounded-3 shadow-lg"> {{-- Rounded corners and shadow for professional look --}}
                @forelse($unitImages as $index => $file)
                  <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img src="{{ asset($file) }}" 
                         class="d-block w-100" 
                         alt="{{ $tenant->tenant->unit->title }} Photo {{ $index + 1 }}"
                         style="height: 280px; object-fit: cover;"> {{-- Increased height --}}
                  </div>
                @empty
                  <div class="carousel-item active">
                    <img src="{{ asset('uploads/default.jpg') }}" 
                         class="d-block w-100" 
                         alt="No Image Available"
                         style="height: 280px; object-fit: cover;">
                  </div>
                @endforelse
              </div>

              @if(count($unitImages) > 1) {{-- Controls only visible with multiple images --}}
                <button class="carousel-control-prev" type="button" data-bs-target="#unitCarousel" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#unitCarousel" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              @endif
            </div>
          </div>
          
          {{-- Text Details (Left Aligned and Vertically Centered Next to Image) --}}
          <div class="flex-grow-1 d-flex flex-column justify-content-center py-4 text-start" style="margin-left: 3rem"> 
            <h3 class="fw-bold mb-1 text-primary">{{ $tenant->tenant->unit->title }}</h3>
            <p class="text-muted mb-0 fw-semibold">{{ $tenant->tenant->unit->location }}</p>
            <small class="text-muted mt-1">Unit Code: <span class="fw-bold">{{ $tenant->tenant->unit->unit_code }}</span></small>
            @if(count($unitImages) > 1)
                <small class="text-muted mt-2 mb-0">Cycle through {{ count($unitImages) }} photos.</small>
            @endif
          </div>

        </div>
      </div>
    </div>
    {{-- END: Property Header Card with Image Carousel --}}

    <div class="row">

      {{-- RIGHT COLUMN (Tenant Info & Documents - CONSOLIDATED) --}}
      <div class="col-lg-4 d-flex flex-column">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-secondary mb-3">Your Tenant Profile</h6>
                <div class="text-center mb-3">
                    <img src="{{ $tenant->profile_photo_url }}" class="rounded-circle mb-2" width="70" height="70" alt="Tenant">
                    <h5 class="fw-bold text-primary mb-0">{{ $tenant->tenant->first_name }} {{ $tenant->tenant->last_name }}</h5>
                    <small class="text-muted d-block">{{ $tenant->tenant->email }}</small>
                </div>
                <ul class="list-unstyled small mb-0 text-start ps-3">
                    <li><i class="bi bi-telephone text-primary me-2"></i> {{ $tenant->tenant->contact_num }}</li>
                    <li><i class="bi bi-person-check-fill text-success me-2"></i> Status: Active Tenant</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-secondary mb-3">Contract & Receipts</h6>
                <div class="d-flex flex-wrap gap-3">
                    @if ($contract->contract_pdf)
                        <a href="{{ asset('storage/' . $contract->contract_pdf) }}" target="_blank" class="btn btn-tenant btn-sm d-flex align-items-center">
                            <i class="bi bi-file-earmark-text me-1"></i> View Contract
                        </a>
                    @else
                        <button class="btn btn-outline-danger btn-sm d-flex align-items-center" disabled>
                            <i class="bi bi-x-circle me-1"></i> No Contract File
                        </button>
                    @endif
                    <a href="#" class="btn btn-outline-tenant btn-sm d-flex align-items-center">
                        <i class="bi bi-receipt me-1"></i> View Receipts
                    </a>
                </div>
            </div>
        </div>
      </div>

      {{-- LEFT COLUMN (Property Stats, Description, Contract) --}}
      <div class="col-lg-8">
        
        {{-- Property Stats Card --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3 text-secondary">Unit Specifications</h6>
            <div class="row mb-4 text-center">
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
                  <h4 class="text-primary">₱{{ number_format($tenant->tenant->unit->unit_price, 2) }}</h4>
                  <small class="text-muted">Unit Price</small>
              </div>
            </div>

            <h6 class="fw-bold text-secondary mb-2">Description</h6>
            <p class="text-muted border-start border-3 ps-3">{{ $tenant->tenant->unit->description }}</p>

          </div>
        </div>
        
        {{-- Contract and Location Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-secondary mb-3">Duration & Location</h6>
                <ul class="list-unstyled text-muted mb-0">
                  <li><i class="bi bi-calendar-check me-2 text-primary"></i> Contract Duration: <span class="fw-semibold text-dark">{{ $tenant->tenant->unit->contract_years }} year(s)</span></li>
                  <li class="mt-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Location: <span class="fw-semibold text-dark">{{ $tenant->tenant->unit->location }}</span></li>
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

    {{-- UNIT PRICE PREDICTION --}}
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-body">
        <h6 class="fw-bold text-secondary mb-3">Unit Price Prediction</h6>

        <canvas id="unitPriceChart" width="200" height="50"></canvas>
      </div>
    </div>

  @else
    <div class="alert alert-warning">
      <i class="bi bi-info-circle me-2"></i> You have no assigned property yet.
    </div>
  @endif

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
        const chartElement = document.getElementById('unitPriceChart');
        if (chartElement) {
            chartElement.insertAdjacentHTML('afterend', '<p class="text-muted mt-2">No prediction data available.</p>');
            chartElement.style.display = 'none'; 
        }
    }
</script>

@endsection