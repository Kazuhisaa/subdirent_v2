@extends('admin.dashboard')

@section('page-title', 'Add New Room')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success text-center fw-bold shadow-sm rounded-3 mb-4"
             style="background:#EAF8FF; color:#0A2540; border:2px solid #2A9DF4;">
            {{ session('success') }}
        </div>
    @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ✅ Form Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
            🏘️ ROOM REGISTRATION FORM
        </div>

        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                {{-- 📍 LOCATION INFORMATION --}}
                <h6 class="fw-bold text-primary mb-3">📍 LOCATION INFORMATION</h6>
                <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">Title</label>
                            <input type="text" name="title" class="form-control border-primary shadow-sm"
                                placeholder="Enter Room Title (e.g. Unit A - Phase 1)" required>
                        </div>
                 

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Phase</label>
                        <input type="text" name="location" class="form-control border-primary shadow-sm" 
                               placeholder="Enter Phase (e.g. Phase 1)" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Unit Code</label>
                        <input type="text" name="unit_code" class="form-control border-primary shadow-sm" 
                               placeholder="Unique Unit Code" required>
                    </div>
                </div>

                {{-- 🏠 PROPERTY DETAILS --}}
                <h6 class="fw-bold text-primary mb-3">🏠 PROPERTY DETAILS</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Floor Area (sqm)</label>
                        <input type="number" name="floor_area" class="form-control border-primary shadow-sm" min="0" placeholder="e.g. 45">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Monthly Rent (₱)</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control border-primary shadow-sm" 
                               placeholder="e.g. 8000.00">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Unit Price (₱)</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control border-primary shadow-sm" 
                               placeholder="e.g. 450000.00">
                    </div>

                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select border-primary shadow-sm">
                            <option value="available" selected>Available</option>
                            <option value="rented">Rented</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Description</label>
                        <textarea name="description" class="form-control border-primary shadow-sm" rows="4" 
                                  placeholder="Enter detailed description of the unit..."></textarea>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Upload Files (Images)</label>
                    <input type="file" name="files[]" class="form-control border-primary shadow-sm" multiple>
                    <small class="text-muted">Allowed: JPG, JPEG | Max: 2MB per file</small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn text-white fw-semibold px-5 py-2 shadow-sm"
                            style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border-radius: 6px;">
                        ➕ Register Room
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
