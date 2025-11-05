@extends('admin.dashboard')

@section('page-title', 'Add New Room')

@section('content')
<div class="container-fluid py-4">

    {{-- Alert Messages --}}
    <div id="alert-container"></div>

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            ROOM REGISTRATION FORM
        </div>

        <div class="card-body">
            <form id="addUnitForm" enctype="multipart/form-data">
                @csrf

                {{-- Title, Location, Unit Code --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-dark">Unit Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Unit Title" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Phase</label>
                        <select name="location" class="form-select" required>
                            <option value="">Select Phase</option>
                            <option value="Phase 1">Phase 1</option>
                            <option value="Phase 2">Phase 2</option>
                            <option value="Phase 3">Phase 3</option>
                            <option value="Phase 4">Phase 4</option>
                            <option value="Phase 5">Phase 5</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Unit Code</label>
                        <input type="text" name="unit_code" class="form-control" placeholder="Unit Code" required>
                    </div>
                </div>

                {{-- Floor, Price, Bedrooms, Bathrooms --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Floor Area</label>
                        <input type="number" name="floor_area" class="form-control" placeholder="m²">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="₱" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Bedrooms</label>
                        <input type="number" name="bedroom" class="form-control" min="0" placeholder="e.g. 2" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Bathrooms</label>
                        <input type="number" name="bathroom" class="form-control" min="0" placeholder="e.g. 1" required>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Enter description..." required></textarea>
                </div>

                {{-- Upload Images --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Upload Images</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                </div>

                {{-- Submit Button --}}
                <div class="text-center">
                    <button type="submit" class="btn text-white fw-semibold px-5 py-2"
                            style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border-radius: 6px;">
                        Register Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addUnitForm');
    const alertContainer = document.getElementById('alert-container');
    const token = sessionStorage.getItem('admin_api_token');

    if (!form || !token) {
        console.error("⚠ Missing form or API token");
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        alertContainer.innerHTML = '';

        const formData = new FormData(form);

        try {
            const res = await fetch('/api/addUnits', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const dataText = await res.text();
            let data;
            try {
                data = JSON.parse(dataText);
            } catch {
                console.error('Raw server response (not JSON):', dataText);
                throw new Error('Invalid server response — check backend logs');
            }

            if (!res.ok) throw new Error(data.message || 'Server error');

            // ✅ Success message
            alertContainer.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ ${data.message || 'Room added successfully!'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            form.reset();

        } catch (err) {
            console.error('Add Unit Error:', err);
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ⚠ Failed to add room: ${err.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    });
});
</script>

@endsection
