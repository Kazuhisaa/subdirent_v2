@extends('admin.dashboard')

@section('page-title', 'Units')

@section('content')
<div class="container-fluid py-4">
    {{-- ✅ Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900 mb-0">🏠 Room Listings</h2>

        {{-- ✅ Search + Add Button --}}
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="unitSearch" class="form-control me-2" placeholder="Search Units..." style="width: 250px;">
            <a href="{{ route('admin.addroom') }}" 
               class="btn btn-action fw-bold px-4 rounded-pill">
                + Add Room
            </a>
        </div>
    </div>

    <div id="searchResults" class="row g-3 mb-4"></div>

    {{-- ✅ Rooms Grid --}}
    <div id="unitGrid" class="row g-4">
        @forelse($units as $unit)
            @php
                $isArchived = strtolower($unit->status) === 'archived';
                $badgeClass = match(strtolower($unit->status)) {
                    'available' => 'bg-blue-500 text-white',
                    'archived'  => 'bg-secondary text-white',
                    'rented'    => 'bg-blue-200 text-blue-900',
                    default     => 'bg-blue-200 text-blue-800',
                };
                $files = is_array($unit->files)
                    ? $unit->files
                    : (json_decode($unit->files, true) ?? []);
                $imagePath = (!empty($files) && isset($files[0]))
                    ? asset('uploads/units/' . basename($files[0]))
                    : asset('images/no-image.png');
            @endphp

            <div class="col-12 col-sm-6 col-lg-3 room-card-wrapper">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 room-card {{ $isArchived ? 'archived-card' : '' }}">
                    {{-- ✅ Image --}}
                    <div class="position-relative" style="height: 200px; background: #f5f9ff; cursor: pointer;">
                        <img src="{{ $imagePath }}"
                             alt="{{ $unit->title }}"
                             class="w-100 h-100 object-fit-cover {{ empty($files) ? 'opacity-75' : '' }}"
                             data-bs-toggle="modal"
                             data-bs-target="#imageModal{{ $unit->id }}">
                        <span class="badge position-absolute top-2 end-2 px-3 py-2 {{ $badgeClass }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>

                    {{-- ✅ Details --}}
                    <div class="card-body">
                        <h5 class="fw-bold text-blue-900 mb-1">{{ $unit->title }}</h5>
                        <p class="text-blue-700 small mb-2">
                            <i class="bi bi-geo-alt-fill"></i> {{ $unit->location }}
                        </p>

                        <div class="small text-blue-800 lh-sm mb-3">
                            <p class="mb-1"><strong>Code:</strong> {{ $unit->unit_code }}</p>
                            <p class="mb-1"><strong>Floor Area:</strong> {{ $unit->floor_area ?? 'N/A' }} sqm</p>
                            <p class="mb-1"><strong>Bedroom:</strong> {{ $unit->bedroom ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Bathroom:</strong> {{ $unit->bathroom ?? 'N/A' }}</p>
                        </div>

                        <div class="border-top pt-2">
                            <p class="fw-bold text-blue-900 mb-1">
                                Rent: ₱{{ number_format((float) str_replace(',', '', $unit->monthly_rent), 2, '.', ',') }}
                            </p>
                            <p class="fw-semibold text-blue-800 mb-0">
                                Price: ₱{{ number_format((float) str_replace(',', '', $unit->unit_price), 2, '.', ',') }}
                            </p>
                        </div>

                        {{-- ✅ Actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('admin.units.edit', $unit->id) }}" 
                               class="btn btn-sm btn-action rounded-pill px-3">
                                Edit
                            </a>

                            <form method="POST" 
                                  action="{{ $isArchived ? route('admin.units.unarchive', $unit->id) : route('admin.units.archive', $unit->id) }}" 
                                  onsubmit="return confirm('{{ $isArchived ? 'Unarchive this unit?' : 'Archive this unit?' }}');">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-sm {{ $isArchived ? 'btn-outline-success' : 'btn-outline-blue' }} rounded-pill px-3">
                                    {{ $isArchived ? 'Unarchive' : 'Archive' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <img src="{{ asset('images/empty-state.svg') }}" alt="No data" width="120" class="mb-3">
                <h6 class="text-blue-800">No rooms found.</h6>
            </div>
        @endforelse
    </div>
</div>


<style>
    .archived-card {
        filter: grayscale(100%) brightness(0.8);
        opacity: 0.8;
    }
</style>

{{-- ✅ AJAX Search Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#unitSearch').on('keyup', function() {
        let query = $(this).val().trim();

        if (query.length === 0) {
            $('#searchResults').html('');
            $('#unitGrid').show();
            return;
        }

        $.ajax({
            url: "{{ route('admin.units.search') }}",
            type: 'GET',
            data: { query: query },
            success: function(units) {
                $('#searchResults').html('');
                $('#unitGrid').hide();

                if (units.length === 0) {
                    $('#searchResults').html('<p class="text-muted text-center mt-3">No units found.</p>');
                    return;
                }

                let html = '';
                units.forEach(unit => {
                    let image = unit.image_path ? `/storage/${unit.image_path}` : '/images/no-image.png';

                    html += `
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0">
                                <img src="${image}" class="card-img-top" alt="${unit.title}" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">${unit.title}</h5>
                                    <p class="text-muted mb-1"><i class="bi bi-geo-alt-fill"></i> ${unit.location}</p>
                                    <p class="mb-0"><strong>Phase:</strong> ${unit.phase || 'N/A'}</p>
                                    <p class="mb-0"><strong>Floor Area:</strong> ${unit.floor_area || 'N/A'} sqm</p>
                                    <p class="mb-0"><strong>Rent:</strong> ₱${Number(unit.rent).toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#searchResults').html(html);
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    });
});
</script>
@endsection
