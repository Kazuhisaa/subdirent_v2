@extends('admin.dashboard')

@section('title', 'Edit Unit')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Unit</h2>

    {{-- Success / Error messages --}}
    <div id="alert-container"></div>

    <form id="updateUnitForm" action="{{ route('admin.units.update', $unit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Unit Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $unit->title) }}" required>
        </div>

        {{-- Location --}}
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $unit->location) }}" required>
        </div>

        {{-- Unit Code --}}
        <div class="mb-3">
            <label for="unit_code" class="form-label">Unit Code</label>
            <input type="text" class="form-control" id="unit_code" name="unit_code" value="{{ old('unit_code', $unit->unit_code) }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $unit->description) }}</textarea>
        </div>

        {{-- Floor Area --}}
        <div class="mb-3">
            <label for="floor_area" class="form-label">Floor Area (sqm)</label>
            <input type="number" class="form-control" id="floor_area" name="floor_area" value="{{ old('floor_area', $unit->floor_area) }}">
        </div>

        {{-- Bathroom --}}
        <div class="mb-3">
            <label for="bathroom" class="form-label">Bathrooms</label>
            <input type="number" class="form-control" id="bathroom" name="bathroom" value="{{ old('bathroom', $unit->bathroom) }}">
        </div>

        {{-- Bedroom --}}
        <div class="mb-3">
            <label for="bedroom" class="form-label">Bedrooms</label>
            <input type="number" class="form-control" id="bedroom" name="bedroom" value="{{ old('bedroom', $unit->bedroom) }}">
        </div>

        {{-- Monthly Rent --}}
        <div class="mb-3">
            <label for="monthly_rent" class="form-label">Monthly Rent</label>
            <input type="number" step="0.01" class="form-control" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent', $unit->monthly_rent) }}">
        </div>

        {{-- Unit Price --}}
        <div class="mb-3">
            <label for="unit_price" class="form-label">Unit Price</label>
            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price', $unit->unit_price) }}">
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

        {{-- Upload Files --}}
        <div class="mb-3">
            <label for="files" class="form-label">Upload Files (Images)</label>
            <input type="file" class="form-control" id="files" name="files[]" multiple>

            @php
                $files = is_array($unit->files) ? $unit->files : json_decode($unit->files, true) ?? [];
            @endphp

            @if(count($files) > 0)
                <div class="mt-3" id="current-files">
                    <p><strong>Current Files:</strong></p>
                    @foreach($files as $file)
                        <div class="mb-2 file-item">
                            @if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $file))
                                <img src="{{ asset($file) }}" alt="File" width="120" class="rounded shadow">
                            @else
                                <a href="{{ asset($file) }}" target="_blank">{{ basename($file) }}</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger remove-file" data-file="{{ $file }}">Remove</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Update Unit</button>
        <a href="{{ route('admin.rooms') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateUnitForm');
    const alertContainer = document.getElementById('alert-container');
    const currentFilesContainer = document.getElementById('current-files');

    // Track removed files
    let removeFiles = [];

    // Remove file button
    document.querySelectorAll('.remove-file').forEach(btn => {
        btn.addEventListener('click', function() {
            const filePath = this.dataset.file;
            removeFiles.push(filePath);
            this.parentElement.remove();
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(form);
        removeFiles.forEach(f => formData.append('remove_files[]', f));

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(res => res.json())
        .then(data => {
            alertContainer.innerHTML = '';
            if(data.message) {
                alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('admin.rooms') }}";
                }, 2000);
            }
            if(data.errors) {
                let errorsHtml = '<div class="alert alert-danger"><ul>';
                for(let key in data.errors) {
                    errorsHtml += `<li>${data.errors[key][0]}</li>`;
                }
                errorsHtml += '</ul></div>';
                alertContainer.innerHTML = errorsHtml;
            }
        })
        .catch(err => console.error('Error:', err));
    });
});
</script>
@endsection
