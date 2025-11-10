@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

{{-- PAGE HEADER --}}
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>
    </div>
    <div class="col-md-6 d-flex justify-content-end align-items-center">
        {{-- NEW: View Toggle Button Group --}}
        <div class="btn-group" role="group" aria-label="View Toggle">
            <button type="button" class="btn btn-action rounded-pill-start fw-bold px-4 active" id="btn-view-active">
                <i class="bi bi-person-check-fill me-1"></i> Active
            </button>
            <button type="button" class="btn btn-outline-blue rounded-pill-end fw-bold px-4" id="btn-view-archived">
                <i class="bi bi-archive-fill me-1"></i> Archived
            </button>
        </div>
    </div>
</div>


{{-- UNIFIED TENANTS TABLE --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">

        {{-- Title changes dynamically --}}
        <span id="table-title">ACTIVE TENANTS</span>

        {{-- Search bar is now generic --}}
        <input type="text" id="table-search" class="form-control form-control-sm w-25" placeholder="Search tenants...">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            {{-- Table is now generic --}}
            <table class="table align-middle text-center mb-0" id="main-table">
                {{-- Head changes dynamically --}}
                <thead class="table-light small text-uppercase text-secondary" id="main-table-head">
                    {{-- Content injected by JS --}}
                </thead>
                {{-- Body changes dynamically --}}
                <tbody id="main-table-body">
                    <tr><td colspan="5" class="py-4 text-muted">Loading tenants...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    {{-- Pagination is now generic --}}
    <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3" id="main-pagination-container">
        {{-- Content injected by JS --}}
    </div>
</div>


{{-- Note: I have removed the '#archivedTenantModal' and '#viewArchivedModal'
     as they are no longer used with this new toggle-based layout. --}}


{{-- ✅ EDIT TENANT MODAL (Unchanged) --}}
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white border-0" style="background: linear-gradient(90deg, #1E81CE, #0A2540);">
                <h5 class="modal-title fw-bold" id="editTenantModalLabel">
                    <i class="bi bi-pencil-square me-2"></i> Edit Tenant Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <form id="editTenantForm">
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