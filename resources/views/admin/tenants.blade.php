{{-- resources/views/admin/tenants.blade.php --}}
@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900">Tenant Management</h2>
        <button class="btn btn-action fw-bold">+ Add Tenant</button>
    </div>

    {{-- Tenant Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle tenant-table mb-0">
                    <thead class="text-blue-900" style="background-color: var(--blue-200);">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td class="fw-semibold">John Doe</td>
                            <td>johndoe@example.com</td>
                            <td>john_doe</td>
                            <td>09123456789</td>
                            <td>
                                <span class="badge bg-blue-500 text-blue-900">Active</span>
                            </td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-sm btn-action">Edit</button>
                                <button class="btn btn-sm btn-outline-blue fw-semibold">Archive</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td class="fw-semibold">Jane Smith</td>
                            <td>janesmith@example.com</td>
                            <td>jane_smith</td>
                            <td>09987654321</td>
                            <td>
                                <span class="badge bg-blue-300 text-blue-900">Archived</span>
                            </td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-sm btn-action">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Optional CSS for hover and styling --}}
<style>
    .tenant-table tbody tr:hover {
        background-color: var(--blue-100);
        transition: background 0.2s ease;
    }

    .tenant-table th, .tenant-table td {
        vertical-align: middle;
    }

    .btn-outline-blue {
        border-color: var(--blue-400);
        color: var(--blue-700);
        transition: all 0.2s ease;
    }
    .btn-outline-blue:hover {
        background-color: var(--blue-500);
        color: #fff;
        border-color: var(--blue-500);
    }
</style>
@endsection
