@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#333;">Room Listings</h2>
        <a href="{{ route('admin.addroom') }}" class="btn btn-success">+ Add Room</a>
    </div>

    <div class="row">
        @forelse($units as $unit)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0" style="background:#FFF3C2;">
                    <div class="card-body">
                        <h6 class="fw-bold text-uppercase">
                            {{ $unit->location ?? 'PHASE' }} 
                        </h6>
                        <h5 class="mb-2">{{ $unit->title }}</h5>

                        {{-- Status Badge --}}
                        <span class="badge 
                            @if($unit->status === 'Available') bg-success 
                            @elseif($unit->status === 'Archived') bg-secondary 
                            @else bg-warning @endif">
                            {{ $unit->status }}
                        </span>

                        {{-- Property Details --}}
                        <div class="mt-3">
                            <p><strong>Property:</strong> {{ $unit->unit_code }}</p>
                            <p><strong>Floor Area:</strong> {{ $unit->floor_area }} sqm</p>
                            <p><strong>Bedroom:</strong> {{ $unit->bedroom }}</p>
                            <p><strong>Bathroom:</strong> {{ $unit->bathroom }}</p>
                            <p><strong>Rent:</strong> ₱{{ number_format((float) $unit->monthly_rent, 2) }}</p>
                            <p><strong>Price:</strong> ₱{{ number_format((float) $unit->unit_price, 2) }}</p>
                        </div>

                        {{-- Unit Images --}}
                        @if($unit->files && is_array($unit->files))
                            <div class="mt-3">
                                @foreach($unit->files as $file)
                                    <img src="{{ asset($file) }}" alt="unit image" 
                                        class="img-fluid rounded mb-2" style="max-height:150px;">
                                @endforeach
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('admin.units.edit', $unit->id) }}" 
                               class="btn btn-sm btn-primary">Edit</a>

                            {{-- Archive Button --}}
                            @if($unit->status !== 'Archived')
                                <form method="POST" 
                                    action="{{ route('admin.units.archive', $unit->id) }}"
                                    onsubmit="return confirm('Archive this unit?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-warning">Archive</button>
                                </form>
                            @endif
                        </div>

                        {{-- Status Display --}}
                        <div class="mt-3">
                            @if($unit->status === 'Available')
                                <span class="badge bg-success">Available for Rent</span>
                            @elseif($unit->status === 'Archived')
                                <span class="badge bg-secondary">Archived</span>
                            @else
                                <span class="badge bg-warning">{{ $unit->status }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No rooms found.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
