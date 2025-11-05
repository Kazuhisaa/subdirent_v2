@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

{{-- PAGE HEADER --}}
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>

        <button class="btn btn-action rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#archivedTenantModal">
            <i class="bi bi-archive-fill me-1"></i> Archived Tenants
        </button>
    </div>
</div>


{{-- ACTIVE TENANTS --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
        style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
        <span>ACTIVE TENANTS</span>
        <input type="text" id="searchTenants" class="form-control form-control-sm w-25" placeholder="Search tenants...">
        <tbody id="tenant-table-body"></tbody>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle text-center mb-0" id="tenant-table">
                <thead class="table-light small text-uppercase text-secondary">
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Unit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tenant-table-body">
                    <tr><td colspan="5" class="py-4 text-muted">Loading tenants...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



{{-- ✅ ARCHIVED TENANTS MODAL --}}
<div class="modal fade" id="archivedTenantModal" tabindex="-1" aria-labelledby="archivedTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      {{-- Header --}}
      <div class="modal-header text-white border-0"
           style="background: linear-gradient(90deg, #007BFF, #0A2540);">
        <h5 class="modal-title fw-bold" id="archivedTenantModalLabel">
          <i class="bi bi-archive me-2"></i> Archived Tenants
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body bg-light p-0">
        <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
          <input type="text" id="searchArchived" class="form-control form-control-sm w-50"
                 placeholder="Search archived tenants...">
                 <tbody id="archived-table-body"></tbody>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center mb-0">
            <thead class="table-light text-uppercase small text-secondary">
              <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Unit</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="archived-table-body">
              <tr><td colspan="5" class="py-4 text-muted">Loading archived tenants...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

{{-- ✅ VIEW SINGLE ARCHIVED TENANT MODAL --}}
<div class="modal fade" id="viewArchivedModal" tabindex="-1" aria-labelledby="viewArchivedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <div class="modal-header text-white border-0"
           style="background: linear-gradient(90deg, #007BFF, #0A2540);">
        <h5 class="modal-title fw-bold" id="viewArchivedModalLabel">
          <i class="bi bi-person-lines-fill me-2"></i> Archived Tenant Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body bg-light">
        <div class="row g-3">
          <div class="col-md-12">
            <label class="fw-semibold">Full Name</label>
            <input type="text" id="archivedFullName" class="form-control" readonly>
          </div>
          <div class="col-md-12">
            <label class="fw-semibold">Email</label>
            <input type="text" id="archivedEmail" class="form-control" readonly>
          </div>
          <div class="col-md-12">
            <label class="fw-semibold">Contact</label>
            <input type="text" id="archivedContact" class="form-control" readonly>
          </div>
          <div class="col-md-12">
            <label class="fw-semibold">Unit</label>
            <input type="text" id="archivedUnit" class="form-control" readonly>
          </div>
        </div>
      </div>

      <div class="modal-footer bg-white border-0">
        <button type="button" id="restoreTenantBtn" class="btn btn-success">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Tenant
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


{{-- ✅ EDIT TENANT MODAL (NEW) --}}
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      {{-- Header --}}
      <div class="modal-header text-white border-0" style="background: linear-gradient(90deg, #1E81CE, #0A2540);">
        <h5 class="modal-title fw-bold" id="editTenantModalLabel">
          <i class="bi bi-pencil-square me-2"></i> Edit Tenant Information
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Body with Form --}}
      <div class="modal-body bg-light p-4">
        <form id="editTenantForm">
          {{-- Hidden ID field to identify the tenant --}}
          <input type="hidden" id="tenantId" name="id">
          <input type="hidden" id="editUnitId" name="unit_id">

          <div class="row g-3">
            <div class="col-md-12">
              <label for="editFirstName" class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control" id="editFirstName" name="first_name" required>
            </div>
            <div class="col-md-12">
              <label for="editMiddleName" class="form-label fw-semibold">Middle Name</label>
              <input type="text" class="form-control" id="editMiddleName" name="middle_name">
            </div>
            <div class="col-md-12">
              <label for="editLastName" class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control" id="editLastName" name="last_name" required>
            </div>
            <div class="col-md-12">
              <label for="editEmail" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <div class="col-md-12">
              <label for="editContact" class="form-label fw-semibold">Contact</label>
              <input type="text" class="form-control" id="editContact" name="contact_num">
            </div>
          </div>
        </form>
      </div>

      {{-- Footer --}}
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-outline-blue" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="editTenantForm" class="btn btn-action" id="editTenantSubmitButton">
            <i class="bi bi-check-circle-fill me-1"></i> Save Changes
        </button>
      </div>

    </div>
  </div>
</div>

@vite(['resources/css/admin.css','resources/css/admin_tenant.css', 'resources/js/admin_tenant.js'])
@endsection
