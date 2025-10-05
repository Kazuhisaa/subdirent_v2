
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@extends('admin.dashboard')

@section('page-title', 'Add New Room')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0" style="background:#FFF3C2;">
        <div class="card-header fw-bold" style="background:#FFEFA0; border-bottom:2px solid #E0C97F;">
            ROOM REGISTRATION FORM
        </div>
        <div class="card-body">

            <form action="{{ route('admin.units.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- LOCATION INFORMATION --}}
                <h6 class="fw-bold text-dark mb-3">📍 LOCATION INFORMATION</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Province</label>
                        <input type="text" name="location" class="form-control" placeholder="Enter Province" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City / Municipality</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter City / Municipality" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit Code</label>
                        <input type="text" name="unit_code" class="form-control" placeholder="Unique Unit Code" required>
                    </div>
                </div>

                {{-- PROPERTY DETAILS --}}
                <h6 class="fw-bold text-dark mb-3">🏠 PROPERTY DETAILS</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Floor Area (sqm)</label>
                        <input type="number" name="floor_area" class="form-control" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Monthly Rent</label>
                        <input type="number" step="0.01" name="monthly_rent" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit Price (For Sale)</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Vacant" selected>Vacant</option>
                            <option value="Occupied">Occupied</option>
                        </select>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Unit Description"></textarea>
                </div>

                {{-- FILE UPLOAD --}}
                <div class="mb-4">
                    <label class="form-label">Upload Files (Images)</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="text-center">
                    <button type="submit" class="btn px-5 py-2"
                        style="background:#795548; color:#FFF3C2; font-weight:bold;">
                        ➕ Register Room
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
