@extends('tenant.dashboard')

@section('title', 'My Account')
@section('page-title', 'Account Settings')

@section('content')
<div class="tenant-account-page container-fluid">
  
  {{-- === TOP ROW === --}}
  <div class="row g-4">

    <div class="col-lg-4 d-flex flex-column">
      <div class="card shadow-sm rounded-4 p-4 text-center mb-4">
        
        <img 
          src="{{ $tenant->profile_photo_url }}" 
          alt="Profile Photo" 
          class="account-avatar mb-3 mx-auto d-block"
          id="avatarPreview"
        >
        
        <h5 class="fw-bold text-primary mb-1">{{ $tenant->tenant->first_name ?? $tenant->name }}</h5>
        <p class="text-muted small mb-0">Tenant</p>
      </div>

      @if($tenant->tenant && $tenant->tenant->unit)
      {{-- 1. ADDED h-100 and d-flex TO THE CARD to allow inner stretching --}}
      <div class="card shadow-sm rounded-4 p-4 flex-fill h-100 d-flex flex-column"> 
        
        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-house-fill me-2"></i>Current Unit</h6>
        
        {{-- 2. ADDED mt-auto TO THIS DIV to push content to the bottom --}}

          <p class="mb-1"><strong>Unit:</strong> {{ $tenant->tenant->unit->title ?? 'N/A' }}</p>
          <p class="mb-1"><strong>Location:</strong> {{ $tenant->tenant->unit->location ?? 'N/A' }}</p>
          <p class="mb-0"><strong>Unit Price:</strong> ₱{{ number_format($tenant->tenant->unit->unit_price, 2) ?? 'N/A'}}</p>
      </div>
      @endif
    </div>

    <div class="col-lg-8">
      <div class="card shadow-sm rounded-4 p-4 h-100"> 
        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-fill me-2"></i>Personal Information</h6>
        
        {{-- This form handles personal info AND the avatar upload --}}
        <form method="POST" action="{{ route('tenant.update') }}" class="account-form" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label fw-semibold" for="avatarUpload">Update Profile Photo</label>
              <input type="file" class="form-control rounded-pill" name="avatar" id="avatarUpload" accept="image/*">
              <small class="text-muted">Max file size: 2MB. Allowed types: jpg, png, webp.</small>
            </div>
          </div>

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
                     value="{{ old('phone', $tenant->tenant->contact_num ?? '') }}">
            </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Discard</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div> {{-- === END OF TOP ROW === --}}


  {{-- === FULL-WIDTH ROW FOR SECURITY === --}}
  <div class="row g-4 mt-4"> {{-- g-4 for alignment, mt-4 for top margin --}}
    <div class="col-12">
      <div class="card shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-shield-lock-fill me-2"></i>Security Settings</h6>
          <a class="btn btn-outline-primary btn-sm rounded-pill px-3" 
             id="toggleSecurityButton" 
             href="#" 
             role="button">
            Change
          </a>
        </div>
        
        {{-- MODIFIED: Added 'show' class if there are password or email errors --}}
        <div class="collapse {{ ($errors->has('password') || $errors->has('email')) ? 'show' : '' }}" id="securityCollapse">
          <form method="POST" action="{{ route('tenant.credentials.update') }}" class="text-start border-top pt-3">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" 
                     name="email" value="{{ old('email', $tenant->email) }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- 🔑 NEW PASSWORD FIELD WITH TOGGLE --}}
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <div class="input-group">
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       id="newPassword" 
                       placeholder="Enter new password (min 8 characters)">
                <button class="btn btn-outline-secondary" type="button" data-password-toggle="newPassword">
                    <i class="bi bi-eye-slash" id="newPassword-icon"></i>
                </button>
                
                {{-- REMOVED rounded-pill from input to fit input-group, added to group --}}
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            
            {{-- 🔑 CONFIRM PASSWORD FIELD WITH TOGGLE --}}
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <div class="input-group">
                <input type="password" class="form-control" 
                       name="password_confirmation" 
                       id="confirmPassword" 
                       placeholder="Confirm new password">
                <button class="btn btn-outline-secondary" type="button" data-password-toggle="confirmPassword">
                    <i class="bi bi-eye-slash" id="confirmPassword-icon"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary rounded-pill px-4">Update Login Info</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- This script previews the new avatar AND handles the security form toggle AND password toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // --- Avatar Preview Script ---
  const avatarUpload = document.getElementById('avatarUpload');
  const avatarPreview = document.getElementById('avatarPreview');

  if (avatarUpload) {
    avatarUpload.addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        avatarPreview.src = URL.createObjectURL(file);
        avatarPreview.style.objectFit = 'cover'; // Ensure preview is not stretched
      }
    });
  }

  // --- CUSTOM TOGGLE SCRIPT (Security Collapse) ---
  const toggleBtn = document.getElementById('toggleSecurityButton');
  const securityForm = document.getElementById('securityCollapse');

  if (toggleBtn && securityForm) {
    toggleBtn.addEventListener('click', function(event) {
      // Prevent the <a> tag's default behavior
      event.preventDefault(); 
      // Manually add or remove the 'show' class to toggle visibility
      securityForm.classList.toggle('show');
      
      // Update button text/icon (optional visual feedback)
      if (securityForm.classList.contains('show')) {
          toggleBtn.innerHTML = 'Hide';
      } else {
          toggleBtn.innerHTML = 'Change';
      }
    });
  }
  
  // --- 🔑 NEW PASSWORD TOGGLE SCRIPT ---
  document.querySelectorAll('[data-password-toggle]').forEach(button => {
    button.addEventListener('click', function() {
      const targetId = this.getAttribute('data-password-toggle');
      const targetInput = document.getElementById(targetId);
      const targetIcon = document.getElementById(targetId + '-icon');
      
      if (targetInput.type === 'password') {
        targetInput.type = 'text';
        targetIcon.classList.remove('bi-eye-slash');
        targetIcon.classList.add('bi-eye');
      } else {
        targetInput.type = 'password';
        targetIcon.classList.remove('bi-eye');
        targetIcon.classList.add('bi-eye-slash');
      }
    });
  });

});
</script>
@endsection