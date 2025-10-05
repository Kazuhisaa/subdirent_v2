@if(session('success'))
    <div class="alert alert-success text-center fw-bold" 
         style="background:#EAF8FF; color:#0A2540; border:2px solid #2A9DF4;">
        {{ session('success') }}
    </div>
@endif

@extends('admin.dashboard')

@section('page-title', 'Add New Room')

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow-sm border-0">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540);">
            ROOM REGISTRATION FORM
        </div>

        <div class="card-body">

            <form action="{{ route('admin.units.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- LOCATION INFORMATION --}}
                <h6 class="fw-bold mb-3" style="color:#0A2540;">📍 LOCATION INFORMATION</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Province</label>
                        <input type="text" name="location" class="form-control border-primary" 
                               placeholder="Enter Province" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">City / Municipality</label>
                        <input type="text" name="title" class="form-control border-primary" 
                               placeholder="Enter City / Municipality" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Unit Code</label>
                        <input type="text" name="unit_code" class="form-control border-primary" 
                               placeholder="Unique Unit Code" required>
                    </div>
                </div>

                {{-- PROPERTY DETAILS --}}
                <h6 class="fw-bold mb-3" style="color:#0A2540;">🏠 PROPERTY DETAILS</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Floor Area (sqm)</label>
                        <input type="number" name="floor_area" class="form-control border-primary" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Monthly Rent</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control border-primary" 
                               placeholder="0.00" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Unit Price (For Sale)</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control border-primary" 
                               placeholder="0.00" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select border-primary">
                            <option value="Vacant" selected>Vacant</option>
                            <option value="Occupied">Occupied</option>
                        </select>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Description</label>
                    <textarea name="description" class="form-control border-primary" rows="3" 
                              placeholder="Unit Description"></textarea>
                </div>

                {{-- FILE UPLOAD --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Upload Files (Images)</label>
                    <input type="file" name="files[]" class="form-control border-primary" multiple>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="text-center">
                    <button type="submit" class="btn text-white px-5 py-2"
                            style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border: none; border-radius: 6px;">
                        ➕ Register Room
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
