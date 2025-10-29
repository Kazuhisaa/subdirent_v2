@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>
    </div>

    {{-- ACTIVE TENANTS --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header fw-bold text-white"
            style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            ACTIVE TENANTS
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle text-center mb-0" id="tenant-table">
                    <thead class="table-light text-uppercase small text-secondary">
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

    {{-- ARCHIVED TENANTS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-bold text-white"
            style="background: linear-gradient(90deg, #6c757d, #343a40); border-radius: .5rem;">
            ARCHIVED TENANTS
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle text-center mb-0" id="archived-table">
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
    </div>
</div>

{{-- ✅ EDIT TENANT MODAL --}}
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editTenantModalLabel">Edit Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTenantForm">
                <div class="modal-body">
                    <input type="hidden" id="tenantId">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" id="editFirstName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Middle Name</label>
                        <input type="text" id="editMiddleName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" id="editLastName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact</label>
                        <input type="text" id="editContact" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@vite(['resources/css/admin.css','resources/css/admin_tenant.css', 'resources/js/admin_tenant.js'])
@endsection
