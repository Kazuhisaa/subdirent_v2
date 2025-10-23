@extends('admin.dashboard')

@section('page-title', 'Edit Room')

@section('content')
<div class="container-fluid py-4">

    {{-- Alert Messages --}}
    <div id="alert-container"></div>

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            EDIT ROOM DETAILS
        </div>

        <div class="card-body">
            <form id="updateUnitForm" data-id="{{ $unit->id }}" enctype="multipart/form-data">
                @csrf

                {{-- Title, Location, Unit Code --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-dark">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Unit Title" value="{{ $unit->title }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Phase / Location</label>
                        <select name="location" class="form-select" required>
                            <option value="">Select Phase</option>
                            @foreach (['Phase 1','Phase 2','Phase 3','Phase 4','Phase 5'] as $phase)
                                <option value="{{ $phase }}" {{ $unit->location === $phase ? 'selected' : '' }}>
                                    {{ $phase }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Unit Code</label>
                        <input type="text" name="unit_code" class="form-control" placeholder="Unique Code" value="{{ $unit->unit_code }}" required>
                    </div>
                </div>

                {{-- Floor, Rent, Price, Status --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Floor Area</label>
                        <input type="number" name="floor_area" class="form-control" placeholder="m²" value="{{ $unit->floor_area }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Monthly Rent</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control" value="{{ $unit->monthly_rent }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" value="{{ $unit->unit_price }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Bedrooms</label>
                        <input type="number" name="bedroom" class="form-control" min="0" value="{{ $unit->bedroom ?? 0 }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Bathrooms</label>
                        <input type="number" name="bathroom" class="form-control" min="0" value="{{ $unit->bathroom ?? 0 }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select">
                            <option value="available" {{ $unit->status === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ $unit->status === 'rented' ? 'selected' : '' }}>Rented</option>
                        </select>
                    </div>
                </div>

                {{-- Contract Years --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Contract Years</label>
                    <input type="number" name="contract_years" class="form-control" min="1" value="{{ $unit->contract_years ?? 1 }}">
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ $unit->description }}</textarea>
                </div>

                {{-- Upload Images --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Upload Images</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                </div>

                {{-- Buttons --}}
                <div class="text-center">
                    <button type="submit" class="btn text-white fw-semibold px-5 py-2"
                            style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border-radius: 6px;">
                        Save Changes
                    </button>
                    <a href="/admin/rooms" class="btn btn-secondary fw-semibold px-4 py-2 ms-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('updateUnitForm');
    const alertContainer = document.getElementById('alert-container');
    const token = sessionStorage.getItem('admin_api_token');
    const unitId = form.dataset.id;

    // Handle update form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        alertContainer.innerHTML = '';

        const formData = new FormData(form);

        // Remove blank fields for rent and price
        if (!form.monthly_rent.value.trim()) formData.delete('monthly_rent');
        if (!form.unit_price.value.trim()) formData.delete('unit_price');

        try {
            const res = await fetch(`/api/editUnits/${unitId}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            let dataText = await res.text();
            let data;
            try { data = JSON.parse(dataText); }
            catch { throw new Error('Invalid server response'); }

            if (!res.ok) throw new Error(data.message || 'Update failed');

            alertContainer.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ ${data.message || 'Room updated successfully!'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            setTimeout(() => window.location.href = '/admin/rooms', 1500);

        } catch (err) {
            console.error(err);
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ⚠ Failed to update: ${err.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    });
});
</script>
@endsection
