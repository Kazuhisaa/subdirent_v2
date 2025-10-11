{{-- resources/views/admin/rooms.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success theme-alert shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">🏠 Room Listings</h2>
        <a href="{{ route('admin.addroom') }}" class="btn btn-action fw-bold px-4">
            + Add Room
        </a>
    </div>

    {{-- ✅ Rooms Grid --}}
    <div class="row g-4">
        @forelse($units as $unit)
            @php
                $badgeClass = match(strtolower($unit->status)) {
                    'available' => 'bg-blue-500 text-white',
                    'archived'  => 'bg-blue-300 text-blue-900',
                    'rented'    => 'bg-blue-200 text-blue-900',
                    default     => 'bg-blue-200 text-blue-800'
                };

                // Decode JSON if necessary
                $files = is_array($unit->files)
                    ? $unit->files
                    : (json_decode($unit->files, true) ?? []);

                // Determine the image path
                $imagePath = !empty($files) && isset($files[0])
                    ? asset($files[0])
                    : asset('images/no-image.png');
            @endphp

            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100 room-card">

                    {{-- ✅ Image Section --}}
                    <div class="position-relative" style="height: 180px; background: #f7faff;">
                        <img src="{{ $imagePath }}"
                             alt="{{ $unit->title }}"
                             class="w-100 h-100 object-fit-cover {{ empty($files) ? 'opacity-75' : '' }}">

                        <span class="badge position-absolute top-2 end-2 {{ $badgeClass }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>

                    {{-- ✅ Content Section --}}
                    <div class="card-body">
                        <h5 class="fw-bold text-blue-900 mb-1">{{ $unit->title }}</h5>
                        <p class="text-blue-700 small mb-2">
                            <i class="bi bi-geo-alt"></i> {{ $unit->location }}
                        </p>

                        <div class="text-blue-800 small lh-sm mb-2">
                            <p><strong>Code:</strong> {{ $unit->unit_code }}</p>
                            <p><strong>Floor Area:</strong> {{ $unit->floor_area ?? 'N/A' }} sqm</p>
                            <p><strong>Bedroom:</strong> {{ $unit->bedroom ?? 'N/A' }}</p>
                            <p><strong>Bathroom:</strong> {{ $unit->bathroom ?? 'N/A' }}</p>
                        </div>

                        <div class="border-top pt-2">
                            <p class="fw-bold text-blue-900 mb-1">
                                Rent: ₱{{ number_format((float) $unit->monthly_rent, 2) }}
                            </p>
                            <p class="fw-semibold text-blue-800 mb-0">
                                Price: ₱{{ number_format((float) $unit->unit_price, 2) }}
                            </p>
                        </div>

                        {{-- ✅ Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('admin.units.edit', $unit->id) }}" 
                               class="btn btn-sm btn-action rounded-pill px-3">
                                Edit
                            </a>

                            @if(strtolower($unit->status) !== 'archived')
                                <form method="POST" 
                                      action="{{ route('admin.units.archive', $unit->id) }}" 
                                      onsubmit="return confirm('Archive this unit?');">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-blue rounded-pill px-3">
                                        Archive
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- ✅ No Data State --}}
            <div class="col-12 text-center py-5">
                <img src="{{ asset('images/empty-state.svg') }}" alt="No data" width="120" class="mb-3">
                <h6 class="text-blue-800">No rooms found.</h6>
            </div>
        @endforelse
    </div>
</div>
@endsection
