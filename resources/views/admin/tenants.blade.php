{{-- resources/views/admin/tenants.blade.php --}}
@extends('admin.dashboard')

@section('content')

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>
        <button class="btn btn-primary fw-semibold px-4 py-2 shadow-sm rounded-pill">
            + Add Tenant
        </button>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 tenant-table text-center">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
                            <th class="fw-semibold py-3 ps-4">#</th>
                            <th class="fw-semibold">Full Name</th>
                            <th class="fw-semibold">Email</th>
                            <th class="fw-semibold">Contact No.</th>
                            <th class="fw-semibold">Unit</th>
                            <th class="fw-semibold pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tenant-table-body">
                        <tr id="loading-row">
                            <td colspan="9" class="text-center py-4 text-muted">Loading tenants...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- External files --}}
@vite(['resources/css/admin.css','resources/css/admin_tenant.css', 'resources/js/admin_tenant.js'])
@endsection
