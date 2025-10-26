{{-- resources/views/admin/tenants.blade.php --}}
@extends('admin.dashboard')

@section('content')

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>
    </div>

    {{-- ✅ CHANGED: Wrapped the table in the same card style as the add-room form --}}
    <div class="card border-0 shadow-sm">

        {{-- ✅ ADDED: A gradient card-header to match the add-room form --}}
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>TENANT LIST</span>

            {{-- ✅ MOVED: The "Add Tenant" button is now here and styled to match --}}
            <button class="btn btn-sm text-white fw-semibold" 
                    style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border:none; border-radius: 6px;">
                + Add Tenant
            </button>
        </div>

        {{-- Table --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- This table will use the .tenant-table styles from your CSS --}}
                <table class="table align-middle mb-0 tenant-table text-center">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
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