@extends('admin.dashboard')

@section('title', 'Edit Unit')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Unit</h2>

    <form action="{{ route('admin.units.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Unit Title</label>
            <input type="text" class="form-control" id="title" name="title"
                   value="{{ old('title', $unit->title) }}" required>
        </div>

        {{-- Location --}}
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location"
                   value="{{ old('location', $unit->location) }}" required>
        </div>

        {{-- Unit Code --}}
        <div class="mb-3">
            <label for="unit_code" class="form-label">Unit Code</label>
            <input type="text" class="form-control" id="unit_code" name="unit_code"
                   value="{{ old('unit_code', $unit->unit_code) }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $unit->description) }}</textarea>
        </div>

        {{-- Floor Area --}}
        <div class="mb-3">
            <label for="floor_area" class="form-label">Floor Area (sqm)</label>
            <input type="number" class="form-control" id="floor_area" name="floor_area"
                   value="{{ old('floor_area', $unit->floor_area) }}">
        </div>

        {{-- Bathroom --}}
        <div class="mb-3">
            <label for="bathroom" class="form-label">Bathrooms</label>
            <input type="number" class="form-control" id="bathroom" name="bathroom"
                   value="{{ old('bathroom', $unit->bathroom) }}">
        </div>

        {{-- Bedroom --}}
        <div class="mb-3">
            <label for="bedroom" class="form-label">Bedrooms</label>
            <input type="number" class="form-control" id="bedroom" name="bedroom"
                   value="{{ old('bedroom', $unit->bedroom) }}">
        </div>

        {{-- Monthly Rent --}}
        <div class="mb-3">
            <label for="monthly_rent" class="form-label">Monthly Rent</label>
            <input type="number" step="0.01" class="form-control" id="monthly_rent" name="monthly_rent"
                   value="{{ old('monthly_rent', $unit->monthly_rent) }}">
        </div>

        {{-- Unit Price --}}
        <div class="mb-3">
            <label for="unit_price" class="form-label">Unit Price</label>
            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price"
                   value="{{ old('unit_price', $unit->unit_price) }}">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="Available" {{ $unit->status == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Occupied" {{ $unit->status == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="Archived" {{ $unit->status == 'Archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        
        <div class="mb-3">
            <label for="files" class="form-label">Upload Files (Images)</label>
            <input type="file" class="form-control" id="files" name="files[]" multiple>
            @if($unit->files)
                <div class="mt-3">
                    <p><strong>Current Files:</strong></p>
                    @foreach(json_decode($unit->files, true) as $file)
                        <div class="mb-2">
                            @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $file))
                                <img src="{{ asset('uploads/units/' . $file) }}" alt="File" width="120" class="rounded shadow">
                            @else
                                <a href="{{ asset('uploads/units/' . $file) }}" target="_blank">{{ $file }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Update Unit</button>
        <a href="{{ route('admin.rooms') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
