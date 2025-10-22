@extends('tenant.dashboard')

@section('title', 'My Account')
@section('page-title', 'Account Settings')

@section('content')
<div class="tenant-account-page container-fluid">
  <div class="account-card shadow-sm bg-white rounded-4 p-4">
    <div class="row g-4 align-items-start">
      <!-- Profile Section -->
      <div class="col-md-3 text-center">
        <img 
          src="{{ $tenant->profile_photo_url ?? asset('images/avatar-default.png') }}" 
          alt="Profile Photo" 
          class="account-avatar mb-3"
        >
        <h5 class="fw-semibold mb-1">{{ $tenant->tenant->first_name ?? $tenant->name }}</h5>
        <p class="text-muted small mb-3">Tenant</p>

        <!-- EMAIL & PASSWORD UPDATE -->
        <form method="POST" action="{{ route('tenant.credentials.update') }}" class="text-start">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control rounded-pill" 
                   name="email" value="{{ old('email', $tenant->email) }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control rounded-pill" name="password" placeholder="Enter new password">
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control rounded-pill" name="password_confirmation" placeholder="Confirm new password">
          </div>

          <button type="submit" class="btn btn-outline-primary w-100 rounded-pill">Update Login Info</button>
        </form>
      </div>

      <!-- Info Section -->
      <div class="col-md-9">
        <form method="POST" action="{{ route('tenant.update') }}" class="account-form">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control rounded-pill" name="first_name" 
                     value="{{ old('first_name', $tenant->tenant->first_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control rounded-pill" name="last_name" 
                     value="{{ old('last_name', $tenant->tenant->last_name ?? '') }}" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="text" class="form-control rounded-pill" name="phone"
                     value="{{ old('phone', $tenant->tenant->phone ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date of Birth</label>
              <input type="date" class="form-control rounded-pill" name="birth_date"
                     value="{{ old('birth_date', $tenant->tenant->birth_date ?? '') }}">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label">Address</label>
              <input type="text" class="form-control rounded-pill" name="address"
                     value="{{ old('address', $tenant->tenant->address ?? '') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Postal Code</label>
              <input type="text" class="form-control rounded-pill" name="postal_code"
                     value="{{ old('postal_code', $tenant->tenant->postal_code ?? '') }}">
            </div>
          </div>

          <!-- Linked Unit Info -->
          @if($tenant->tenant && $tenant->tenant->unit)
          <div class="linked-unit mt-5 p-3 border rounded-4">
            <h6 class="fw-semibold mb-2">🏠 Current Unit</h6>
            <p class="mb-1"><strong>Unit:</strong> {{ $tenant->tenant->unit->title ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Location:</strong> {{ $tenant->tenant->unit->location ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Unit Price:</strong> ₱{{ $tenant->tenant->unit->unit_price ?? 'N/A'}}</p>
          </div>
          @endif

          <div class="row mt-4">
            <div class="col-md-6">
              <button type="reset" class="btn btn-outline-secondary w-100 rounded-pill">Discard Changes</button>
            </div>
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100 rounded-pill">Save Changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
