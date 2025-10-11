{{-- resources/views/admin/tenants.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-blue-900">Tenant Management</h2>
            <p class="text-muted small mb-0">View and manage all registered tenants in your system.</p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-semibold shadow-sm rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Add Tenant
        </button>
    </div>

    {{-- Tenant Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 tenant-table">
                    <thead style="background: linear-gradient(90deg, #f8fafc, #eef2ff);" class="small text-uppercase text-secondary">
                        <tr>
                            <th class="fw-semibold py-3 ps-4">Full Name</th>
                            <th class="fw-semibold">Email</th>
                            <th class="fw-semibold">Room</th>
                            <th class="fw-semibold text-center">Status</th>
                            <th class="text-end fw-semibold pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Example Row 1 --}}
                        <tr>
                            <td class="fw-semibold ps-4">John Doe</td>
                            <td>johndoe@example.com</td>
                            <td>Room A1</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Active</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border-0 text-primary me-2" title="Edit">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                                <button class="btn btn-sm btn-light border-0 text-danger" title="Archive">
                                    <i class="bi bi-archive fs-5"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Example Row 2 --}}
                        <tr class="archived-row">
                            <td class="fw-semibold ps-4">Jane Smith</td>
                            <td>janesmith@example.com</td>
                            <td>Room B2</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Archived</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border-0 text-primary" title="Edit">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Example Row 3 --}}
                        <tr>
                            <td class="fw-semibold ps-4">Mark Anderson</td>
                            <td>markanderson@example.com</td>
                            <td>Room C1</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Active</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border-0 text-primary me-2" title="Edit">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                                <button class="btn btn-sm btn-light border-0 text-danger" title="Archive">
                                    <i class="bi bi-archive fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .tenant-table th {
        letter-spacing: 0.04rem;
        font-weight: 600;
        color: #64748b;
    }

    .tenant-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease-in-out;
    }

    .tenant-table tbody tr:hover {
        background-color: #f9fafb;
        transform: scale(1.002);
    }

    .tenant-table td {
        vertical-align: middle;
        color: #1e293b;
    }

    .archived-row td {
        opacity: 0.6;
    }

    .btn-light {
        border-radius: 10px;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-light:hover {
        background-color: #e8f0fe;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .card {
        background: #ffffff;
    }

    .btn-primary {
        background: linear-gradient(90deg, #3b82f6, #2563eb);
        border: none;
        color: white;
        transition: 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #2563eb, #1d4ed8);
        transform: scale(1.03);
    }
</style>
@endsection
